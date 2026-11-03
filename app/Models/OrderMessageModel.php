<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderMessageModel extends Model
{
    protected $table = 'order_messages';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'conversation_key', 'subject', 'sender_user_id',
        'sender_company_id', 'receiver_company_id', 'sender_name',
        'sender_role', 'receiver_role', 'sender_company_name',
        'message', 'attachment_path', 'is_read', 'created_at'
    ];
    protected $useTimestamps = false;

    public function __construct()
    {
        parent::__construct();
        $this->ensureSchemaExists();
    }

    public function ensureSchemaExists(): void
    {
        static $checked = false;
        if ($checked) {
            return;
        }
        $checked = true;

        try {
            if (!$this->db->tableExists('order_messages')) {
                $this->db->query("CREATE TABLE IF NOT EXISTS `order_messages` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `order_id` INT NULL DEFAULT NULL,
                    `conversation_key` VARCHAR(100) NULL,
                    `subject` VARCHAR(255) NULL,
                    `sender_user_id` INT NULL DEFAULT NULL,
                    `sender_company_id` INT NULL DEFAULT NULL,
                    `receiver_company_id` INT NULL DEFAULT NULL,
                    `sender_name` VARCHAR(150) NOT NULL,
                    `sender_role` VARCHAR(30) NOT NULL,
                    `receiver_role` VARCHAR(30) NULL,
                    `sender_company_name` VARCHAR(150) NULL DEFAULT NULL,
                    `message` TEXT NOT NULL,
                    `attachment_path` VARCHAR(255) NULL DEFAULT NULL,
                    `is_read` TINYINT NOT NULL DEFAULT 0,
                    `created_at` DATETIME NULL DEFAULT NULL,
                    INDEX `idx_order_id` (`order_id`),
                    INDEX `idx_conv_key` (`conversation_key`),
                    INDEX `idx_created_at` (`created_at`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            }
        } catch (\Throwable $e) {
            // Ignore
        }
    }

    /**
     * Get messages by conversation key or fallback to order_id
     */
    public function getMessagesByConversation(string $conversationKey): array
    {
        return $this->where('conversation_key', $conversationKey)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Backward-compatible getMessagesByOrder
     */
    public function getMessagesByOrder(int $orderId): array
    {
        return $this->groupStart()
                        ->where('order_id', $orderId)
                        ->orWhere('conversation_key', 'order_' . $orderId)
                    ->groupEnd()
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Insert a new message into a conversation
     */
    public function addMessage(
        ?int $orderId,
        ?int $userId,
        string $senderName,
        string $senderRole,
        ?string $companyName,
        string $message,
        ?string $attachment = null,
        ?string $conversationKey = null,
        ?int $senderCompanyId = null,
        ?int $receiverCompanyId = null,
        ?string $receiverRole = null,
        ?string $subject = null
    ): array {
        if (empty($conversationKey)) {
            if (!empty($orderId)) {
                $conversationKey = 'order_' . $orderId;
            } elseif ($receiverRole === 'admin' || $senderRole === 'admin') {
                $targetCompId = $senderRole === 'admin' ? $receiverCompanyId : $senderCompanyId;
                $conversationKey = 'admin_comp_' . ($targetCompId ?: 0);
            } elseif ($senderCompanyId && $receiverCompanyId) {
                $c1 = min($senderCompanyId, $receiverCompanyId);
                $c2 = max($senderCompanyId, $receiverCompanyId);
                $conversationKey = 'direct_' . $c1 . '_' . $c2;
            } else {
                $conversationKey = 'general_' . date('Ymd');
            }
        }

        $data = [
            'order_id'            => $orderId ?: null,
            'conversation_key'    => $conversationKey,
            'subject'             => $subject,
            'sender_user_id'      => $userId,
            'sender_company_id'   => $senderCompanyId,
            'receiver_company_id' => $receiverCompanyId,
            'sender_name'         => $senderName,
            'sender_role'         => $senderRole,
            'receiver_role'       => $receiverRole,
            'sender_company_name' => $companyName,
            'message'             => $message,
            'attachment_path'     => $attachment,
            'is_read'             => 0,
            'created_at'          => date('Y-m-d H:i:s'),
        ];

        $this->insert($data);
        $data['id'] = $this->insertID();
        return $data;
    }

    /**
     * Backward-compatible sendMessage
     */
    public function sendMessage(int $orderId, ?int $userId, string $senderName, string $senderRole, string $message, ?string $companyName = null, ?string $attachment = null): array
    {
        return $this->addMessage($orderId, $userId, $senderName, $senderRole, $companyName, $message, $attachment, 'order_' . $orderId);
    }

    /**
     * Mark messages as read for a conversation key
     */
    public function markAsReadByConversation(string $conversationKey, string $currentRole, ?int $currentUserId = null): void
    {
        $builder = $this->where('conversation_key', $conversationKey)
                        ->where('sender_role !=', $currentRole)
                        ->where('is_read', 0);
        if ($currentUserId) {
            $builder->where('sender_user_id !=', $currentUserId);
        }
        $builder->set(['is_read' => 1])->update();
    }

    /**
     * Backward compatible markAsRead by orderId
     */
    public function markAsRead(int $orderId, string $currentRole): void
    {
        $this->markAsReadByConversation('order_' . $orderId, $currentRole);
    }

    /**
     * Get all active and potential conversations for a user / role / company
     */
    public function getConversationsList(string $role, ?int $companyId = null): array
    {
        $db = $this->db;
        $conversations = [];

        // 1. Fetch personal conversations from existing messages
        $msgRows = $db->table('order_messages')
            ->select('conversation_key, order_id, MAX(id) as last_msg_id, COUNT(id) as total_messages')
            ->groupBy('conversation_key')
            ->orderBy('last_msg_id', 'DESC')
            ->get()->getResultArray();

        $knownKeys = [];

        foreach ($msgRows as $row) {
            $key = $row['conversation_key'] ?? null;
            if (empty($key)) continue;

            // Hapus/abaikan obrolan gabungan vendor & buyer (order_ dan direct_)
            if (str_starts_with($key, 'order_') || str_starts_with($key, 'direct_')) {
                continue;
            }

            $lastMsg = $this->find((int)$row['last_msg_id']);
            if (!$lastMsg) continue;

            $hasAccess = false;
            $counterpartyName = 'AkuAda Support';
            $counterpartyRole = 'admin';
            $counterpartyBadge = 'Superadmin AkuAda';
            $title = $lastMsg['subject'] ?? 'Pusat Bantuan & Layanan B2B';
            $orderNumber = null;
            $poNumber = null;

            if (str_starts_with($key, 'admin_comp_')) {
                $targetCompId = (int)str_replace('admin_comp_', '', $key);
                if ($role === 'admin') {
                    $hasAccess = true;
                    $comp = $db->table('companies')->where('id', $targetCompId)->get()->getRowArray();
                    $counterpartyName = $comp['company_name'] ?? 'Perusahaan';
                    $counterpartyRole = $comp['type'] ?? 'customer';
                    $counterpartyBadge = $counterpartyRole === 'vendor' ? 'Mitra Vendor' : 'Corporate Buyer';
                    $title = $lastMsg['subject'] ?: ('Saluran Personal: ' . $counterpartyName);
                } elseif ($companyId && $companyId === $targetCompId) {
                    $hasAccess = true;
                    $counterpartyName = 'Official Support & CS AkuAda';
                    $counterpartyRole = 'admin';
                    $counterpartyBadge = 'Superadmin Platform';
                    $title = $lastMsg['subject'] ?: 'Bantuan & Layanan Pelanggan AkuAda';
                }
            } elseif (str_starts_with($key, 'guest_')) {
                if ($role === 'admin') {
                    $hasAccess = true;
                    // Ambil identitas nama PIC dan perusahaan yang diisi oleh pembeli umum (guest)
                    $guestMsg = $this->where('conversation_key', $key)
                                     ->where('sender_role', 'guest')
                                     ->orderBy('id', 'ASC')
                                     ->first();

                    $gName = !empty($guestMsg['sender_name']) ? $guestMsg['sender_name'] : ($lastMsg['sender_name'] ?? '');
                    $gComp = !empty($guestMsg['sender_company_name']) ? $guestMsg['sender_company_name'] : ($lastMsg['sender_company_name'] ?? '');

                    $displayName = 'Buyer Umum (Tamu)';
                    if (!empty($gName) && !empty($gComp) && $gComp !== 'Calon Mitra Korporat' && $gComp !== 'Calon Pembeli') {
                        $displayName = $gComp . ' (' . $gName . ')';
                    } elseif (!empty($gComp) && $gComp !== 'Calon Mitra Korporat' && $gComp !== 'Calon Pembeli') {
                        $displayName = $gComp;
                    } elseif (!empty($gName) && $gName !== 'Pengunjung B2B' && $gName !== 'Pengunjung Umum') {
                        $displayName = $gName . ' (Tamu)';
                    }

                    $counterpartyName = $displayName;
                    $counterpartyRole = 'guest';
                    $counterpartyBadge = 'Buyer Umum (Tamu)';
                    $title = (!empty($gComp) && $gComp !== 'Calon Mitra Korporat' && $gComp !== 'Calon Pembeli') ? ('Inquiry Pembeli: ' . $gComp) : 'Inquiry Buyer Belum Terdaftar';
                }
            }

            if ($hasAccess) {
                // Count unread
                $unreadCount = $this->where('conversation_key', $key)
                    ->where('sender_role !=', $role)
                    ->where('is_read', 0)
                    ->countAllResults();

                $conversations[] = [
                    'key'                => $key,
                    'order_id'           => $lastMsg['order_id'] ?? null,
                    'order_number'       => $orderNumber,
                    'po_number'          => $poNumber,
                    'title'              => $title,
                    'counterparty_name'  => $counterpartyName,
                    'counterparty_role'  => $counterpartyRole,
                    'counterparty_badge' => $counterpartyBadge,
                    'last_message'       => $lastMsg['message'],
                    'last_sender'        => $lastMsg['sender_name'],
                    'last_sender_role'   => $lastMsg['sender_role'],
                    'last_message_at'    => $lastMsg['created_at'],
                    'time_formatted'     => date('H:i', strtotime($lastMsg['created_at'])),
                    'date_formatted'     => date('d M Y', strtotime($lastMsg['created_at'])),
                    'unread_count'       => $unreadCount,
                ];
                $knownKeys[$key] = true;
            }
        }

        // 2. Add potential default personal conversations if not yet started
        if ($role === 'customer' && $companyId) {
            $supportKey = 'admin_comp_' . $companyId;
            if (!isset($knownKeys[$supportKey])) {
                $conversations[] = [
                    'key'                => $supportKey,
                    'order_id'           => null,
                    'order_number'       => null,
                    'po_number'          => null,
                    'title'              => 'Pusat Bantuan & Layanan CS AkuAda',
                    'counterparty_name'  => 'Official Support AkuAda',
                    'counterparty_role'  => 'admin',
                    'counterparty_badge' => 'Superadmin Platform',
                    'last_message'       => 'Pusat bantuan pengadaan B2B, penambahan plafon tempo TOP, dan info faktur.',
                    'last_sender'        => 'AkuAda Support',
                    'last_sender_role'   => 'admin',
                    'last_message_at'    => date('Y-m-d H:i:s'),
                    'time_formatted'     => 'Aktif',
                    'date_formatted'     => date('d M Y'),
                    'unread_count'       => 0,
                ];
                $knownKeys[$supportKey] = true;
            }
        } elseif ($role === 'vendor' && $companyId) {
            $supportKey = 'admin_comp_' . $companyId;
            if (!isset($knownKeys[$supportKey])) {
                $conversations[] = [
                    'key'                => $supportKey,
                    'order_id'           => null,
                    'order_number'       => null,
                    'po_number'          => null,
                    'title'              => 'Bantuan Finance & Kemitraan AkuAda',
                    'counterparty_name'  => 'Official Support AkuAda',
                    'counterparty_role'  => 'admin',
                    'counterparty_badge' => 'Superadmin Platform',
                    'last_message'       => 'Pusat bantuan vendor: pencairan dana settlement, faktur komisi, dan integrasi katalog.',
                    'last_sender'        => 'AkuAda Support',
                    'last_sender_role'   => 'admin',
                    'last_message_at'    => date('Y-m-d H:i:s'),
                    'time_formatted'     => 'Aktif',
                    'date_formatted'     => date('d M Y'),
                    'unread_count'       => 0,
                ];
                $knownKeys[$supportKey] = true;
            }
        } elseif ($role === 'admin') {
            // Untuk Admin: daftarkan seluruh perusahaan (Buyer & Vendor) sebagai jalur chat personal 1-on-1
            $allComps = $db->table('companies')->orderBy('type', 'DESC')->orderBy('company_name', 'ASC')->get()->getResultArray();
            foreach ($allComps as $c) {
                $supportKey = 'admin_comp_' . $c['id'];
                if (!isset($knownKeys[$supportKey])) {
                    $conversations[] = [
                        'key'                => $supportKey,
                        'order_id'           => null,
                        'order_number'       => null,
                        'po_number'          => null,
                        'title'              => 'Saluran Personal: ' . $c['company_name'],
                        'counterparty_name'  => $c['company_name'],
                        'counterparty_role'  => $c['type'],
                        'counterparty_badge' => $c['type'] === 'vendor' ? 'Mitra Vendor' : 'Corporate Buyer',
                        'last_message'       => 'Saluran komunikasi personal 1-on-1 Superadmin dengan ' . $c['company_name'] . '.',
                        'last_sender'        => $c['company_name'],
                        'last_sender_role'   => $c['type'],
                        'last_message_at'    => null,
                        'time_formatted'     => 'Siap',
                        'date_formatted'     => '-',
                        'unread_count'       => 0,
                    ];
                    $knownKeys[$supportKey] = true;
                }
            }
        }

        return $conversations;
    }
}
