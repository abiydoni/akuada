<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderMessageModel;
use CodeIgniter\Controller;

class ChatController extends Controller
{
    protected function getGuestKey(): string
    {
        $sessId = session_id();
        if (empty($sessId)) {
            session(); // start session if not started
            $sessId = session_id();
        }
        return 'guest_' . substr(md5($sessId ?: 'guest_cookie'), 0, 16);
    }

    /**
     * Verify whether the logged-in user or guest can access this conversation
     */
    protected function checkConversationAccess(string $key, ?int &$extractedOrderId = null): bool
    {
        $isLoggedIn = !empty(session('user_id')) || !empty(session('is_logged_in'));
        $role = session('user_role') ?: (session('role') ?: 'guest');
        $companyId = (int)session('company_id');

        // Admin has full oversight on all communications
        if ($role === 'admin') {
            if (str_starts_with($key, 'order_')) {
                $extractedOrderId = (int)str_replace('order_', '', $key);
            }
            return true;
        }

        // Guest access to their own support thread
        if (str_starts_with($key, 'guest_')) {
            return ($key === $this->getGuestKey());
        }

        if (!$isLoggedIn) {
            return false;
        }

        if (str_starts_with($key, 'order_')) {
            $orderId = (int)str_replace('order_', '', $key);
            $extractedOrderId = $orderId;
            $order = (new OrderModel())->find($orderId);
            if (!$order) {
                return false;
            }
            if ($role === 'customer' && (int)$order['customer_company_id'] === $companyId) {
                return true;
            }
            if ($role === 'vendor' && (int)$order['vendor_company_id'] === $companyId) {
                return true;
            }
            return false;
        }

        if (str_starts_with($key, 'direct_')) {
            if ($role === 'customer' || $role === 'vendor') {
                return false; // Buyer dan Vendor hanya boleh berkomunikasi dengan tim AkuAda
            }
            $parts = explode('_', $key);
            $c1 = isset($parts[1]) ? (int)$parts[1] : 0;
            $c2 = isset($parts[2]) ? (int)$parts[2] : 0;
            return ($companyId === $c1 || $companyId === $c2);
        }

        if (str_starts_with($key, 'admin_comp_')) {
            $targetCompId = (int)str_replace('admin_comp_', '', $key);
            return ($companyId === $targetCompId);
        }

        return false;
    }

    /**
     * Get JSON list of conversations
     */
    public function getConversations()
    {
        $isLoggedIn = !empty(session('user_id')) || !empty(session('is_logged_in'));
        $role = session('user_role') ?: (session('role') ?: 'guest');
        $companyId = (int)session('company_id');
        $messageModel = new OrderMessageModel();

        if (!$isLoggedIn) {
            $guestKey = $this->getGuestKey();
            $recentMsgs = $messageModel->getMessagesByConversation($guestKey);
            $lastMsg = !empty($recentMsgs) ? end($recentMsgs) : null;

            return $this->response->setJSON([
                'status'       => 'success',
                'is_logged_in' => false,
                'guest_key'    => $guestKey,
                'threads'      => [
                    [
                        'key'                => $guestKey,
                        'order_id'           => null,
                        'order_number'       => null,
                        'po_number'          => null,
                        'title'              => 'Live Chat CS AkuAda Platform',
                        'counterparty_name'  => 'Official Support AkuAda',
                        'counterparty_role'  => 'admin',
                        'counterparty_badge' => 'Superadmin CS',
                        'last_message'       => $lastMsg ? $lastMsg['message'] : 'Halo! Ada yang bisa tim AkuAda bantu untuk kebutuhan pengadaan perusahaan Anda?',
                        'last_sender'        => $lastMsg ? $lastMsg['sender_name'] : 'AkuAda CS',
                        'last_sender_role'   => 'admin',
                        'last_message_at'    => $lastMsg ? $lastMsg['created_at'] : date('Y-m-d H:i:s'),
                        'time_formatted'     => 'Online',
                        'date_formatted'     => date('d M Y'),
                        'unread_count'       => 0,
                    ]
                ]
            ]);
        }

        $threads = $messageModel->getConversationsList($role, $companyId);

        return $this->response->setJSON([
            'status'       => 'success',
            'is_logged_in' => true,
            'role'         => $role,
            'company_name' => session('company_name'),
            'threads'      => $threads,
        ]);
    }

    /**
     * Get messages for a given conversation key or order_id
     */
    public function getMessages($identifier)
    {
        $key = is_numeric($identifier) ? ('order_' . $identifier) : (string)$identifier;
        $orderId = null;

        if (!$this->checkConversationAccess($key, $orderId)) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => 'error',
                'message' => 'Akses pesan tidak diizinkan atau sesi telah berakhir.'
            ]);
        }

        $messageModel = new OrderMessageModel();
        $messages = $messageModel->getMessagesByConversation($key);

        // Fallback if key is order_X and no messages by key yet, check by order_id
        if (empty($messages) && $orderId) {
            $messages = $messageModel->getMessagesByOrder($orderId);
        }

        // Mark incoming messages as read
        $currentRole = session('role') ?? 'customer';
        $currentUserId = (int)session('user_id');
        $messageModel->markAsReadByConversation($key, $currentRole, $currentUserId);

        $formatted = [];
        foreach ($messages as $m) {
            $isMe = false;
            if (session('is_logged_in')) {
                $isMe = ((int)$m['sender_user_id'] === $currentUserId && $m['sender_role'] === $currentRole);
            } else {
                $isMe = ($m['sender_role'] === 'guest' || $m['sender_role'] === 'customer');
            }

            $formatted[] = [
                'id'                  => (int)$m['id'],
                'sender_user_id'      => (int)$m['sender_user_id'],
                'sender_name'         => $m['sender_name'],
                'sender_role'         => $m['sender_role'],
                'sender_company_name' => $m['sender_company_name'] ?? '',
                'message'             => nl2br(esc($m['message'])),
                'raw_message'         => $m['message'],
                'is_me'               => $isMe,
                'created_at'          => $m['created_at'],
                'time_formatted'      => date('H:i', strtotime($m['created_at'])),
                'date_formatted'      => date('d M Y', strtotime($m['created_at'])),
            ];
        }

        return $this->response->setJSON([
            'status'           => 'success',
            'conversation_key' => $key,
            'order_id'         => $orderId,
            'current_user_id'  => $currentUserId,
            'current_role'     => $currentRole,
            'messages'         => $formatted,
        ]);
    }

    /**
     * Send a new message
     */
    public function sendMessage()
    {
        $isLoggedIn = !empty(session('user_id')) || !empty(session('is_logged_in'));
        $rawKey = $this->request->getPost('conversation_key');
        $rawOrderId = $this->request->getPost('order_id');
        $message = trim((string)$this->request->getPost('message'));

        if (!$isLoggedIn) {
            $guestKey = $this->getGuestKey();
            $key = $guestKey;
            $orderId = null;
            $userId = null;
            $userName = trim((string)$this->request->getPost('guest_name')) ?: 'Pengunjung B2B';
            $userRole = 'guest';
            $companyId = null;
            $companyName = trim((string)$this->request->getPost('guest_company')) ?: 'Calon Mitra Korporat';
            $receiverRole = 'admin';
            $receiverCompanyId = null;
            $subject = 'Inquiry Marketplace Publik';
        } else {
            if (!empty($rawKey)) {
                $key = $rawKey;
            } elseif (!empty($rawOrderId)) {
                $key = 'order_' . (int)$rawOrderId;
            } else {
                return $this->response->setStatusCode(400)->setJSON([
                    'status'  => 'error',
                    'message' => 'Tujuan percakapan tidak valid.'
                ]);
            }

            $orderId = null;
            if (!$this->checkConversationAccess($key, $orderId)) {
                return $this->response->setStatusCode(403)->setJSON([
                    'status'  => 'error',
                    'message' => 'Akses pengiriman pesan tidak diizinkan.'
                ]);
            }

            $userId = session('user_id') ? (int)session('user_id') : null;
            $userName = session('user_name') ?: 'Pengguna';
            $userRole = session('user_role') ?: (session('role') ?: 'customer');
            $companyId = session('company_id') ? (int)session('company_id') : null;
            $companyName = session('company_name') ?: ($userRole === 'admin' ? 'Superadmin Platform AkuAda' : 'Perusahaan');
            $receiverCompanyId = $this->request->getPost('receiver_company_id') ? (int)$this->request->getPost('receiver_company_id') : null;
            $receiverRole = $this->request->getPost('receiver_role') ?: null;
            $subject = $this->request->getPost('subject') ?: null;

            if ($userRole === 'customer' || $userRole === 'vendor') {
                // Khusus buyer dan vendor, lawan chat selalu tim AkuAda CS / Superadmin
                $receiverRole = 'admin';
                $receiverCompanyId = null;
            }
        }

        if (empty($message)) {
            return $this->response->setStatusCode(400)->setJSON([
                'status'  => 'error',
                'message' => 'Pesan tidak boleh kosong.'
            ]);
        }

        $messageModel = new OrderMessageModel();
        $newMessage = $messageModel->addMessage(
            $orderId,
            $userId,
            $userName,
            $userRole,
            $companyName,
            $message,
            null,
            $key,
            $companyId,
            $receiverCompanyId,
            $receiverRole,
            $subject
        );

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => [
                'id'                  => (int)$newMessage['id'],
                'sender_user_id'      => $userId,
                'sender_name'         => $userName,
                'sender_role'         => $userRole,
                'sender_company_name' => $companyName,
                'message'             => nl2br(esc($message)),
                'is_me'               => true,
                'time_formatted'      => date('H:i'),
                'date_formatted'      => date('d M Y'),
                'created_at'          => $newMessage['created_at'],
            ]
        ]);
    }

    /**
     * Realtime Polling API for Live Notifications & Sound Alerts
     * Returns: unread chat count, latest unread message, latest order info
     */
    public function realtimeCheck()
    {
        $isLoggedIn = !empty(session('user_id')) || !empty(session('is_logged_in'));
        $role = session('user_role') ?: (session('role') ?: 'guest');
        $companyId = (int)session('company_id');
        $db = \Config\Database::connect();

        $unreadCount = 0;
        $latestMessage = null;
        $latestOrder = null;
        $totalOrders = 0;

        if (!$isLoggedIn) {
            $guestKey = $this->getGuestKey();
            $unreadCount = $db->table('order_messages')
                ->where('conversation_key', $guestKey)
                ->where('sender_role !=', 'guest')
                ->where('is_read', 0)
                ->countAllResults();

            if ($unreadCount > 0) {
                $msgRow = $db->table('order_messages')
                    ->where('conversation_key', $guestKey)
                    ->where('sender_role !=', 'guest')
                    ->where('is_read', 0)
                    ->orderBy('id', 'DESC')
                    ->get(1)->getRowArray();
                if ($msgRow) {
                    $latestMessage = [
                        'id'               => (int)$msgRow['id'],
                        'sender_name'      => $msgRow['sender_name'],
                        'sender_role'      => $msgRow['sender_role'],
                        'message'          => $msgRow['message'],
                        'conversation_key' => $guestKey,
                        'created_at'       => $msgRow['created_at'],
                        'time_formatted'   => date('H:i', strtotime($msgRow['created_at'])),
                    ];
                }
            }
        } else {
            // Logged in: Admin, Vendor, or Customer
            $messageModel = new OrderMessageModel();
            $threads = $messageModel->getConversationsList($role, $companyId);
            
            // Total unread from accessible threads
            foreach ($threads as $t) {
                $unreadCount += (int)($t['unread_count'] ?? 0);
            }

            // Find latest unread message for notification alert
            if ($unreadCount > 0) {
                $builder = $db->table('order_messages');
                $builder->where('sender_role !=', $role)->where('is_read', 0);

                if ($role === 'vendor' && $companyId) {
                    $keys = array_column($threads, 'key');
                    if (!empty($keys)) {
                        $builder->whereIn('conversation_key', $keys);
                    } else {
                        $builder->where('conversation_key', 'admin_comp_' . $companyId);
                    }
                } elseif ($role === 'customer' && $companyId) {
                    $keys = array_column($threads, 'key');
                    if (!empty($keys)) {
                        $builder->whereIn('conversation_key', $keys);
                    } else {
                        $builder->where('conversation_key', 'admin_comp_' . $companyId);
                    }
                }

                $msgRow = $builder->orderBy('id', 'DESC')->get(1)->getRowArray();
                if ($msgRow) {
                    $latestMessage = [
                        'id'                  => (int)$msgRow['id'],
                        'sender_name'         => $msgRow['sender_name'],
                        'sender_role'         => $msgRow['sender_role'],
                        'sender_company_name' => $msgRow['sender_company_name'] ?? '',
                        'message'             => $msgRow['message'],
                        'conversation_key'    => $msgRow['conversation_key'] ?? ('order_' . $msgRow['order_id']),
                        'order_id'            => $msgRow['order_id'],
                        'created_at'          => $msgRow['created_at'],
                        'time_formatted'      => date('H:i', strtotime($msgRow['created_at'])),
                    ];
                }
            }

            // Realtime Order check
            $orderBuilder = $db->table('orders o')
                ->select('o.*, cb.company_name as buyer_name, cv.company_name as vendor_name')
                ->join('companies cb', 'cb.id = o.customer_company_id', 'left')
                ->join('companies cv', 'cv.id = o.vendor_company_id', 'left');

            if ($role === 'vendor' && $companyId) {
                $orderBuilder->where('o.vendor_company_id', $companyId);
            } elseif ($role === 'customer' && $companyId) {
                $orderBuilder->where('o.customer_company_id', $companyId);
            }

            $totalOrders = (clone $orderBuilder)->countAllResults();
            $orderRow = $orderBuilder->orderBy('o.id', 'DESC')->get(1)->getRowArray();

            if ($orderRow) {
                $latestOrder = [
                    'id'           => (int)$orderRow['id'],
                    'order_number' => $orderRow['order_number'],
                    'po_number'    => $orderRow['po_number'] ?? $orderRow['order_number'],
                    'buyer_name'   => $orderRow['buyer_name'] ?? 'Buyer',
                    'vendor_name'  => $orderRow['vendor_name'] ?? 'Vendor',
                    'total_amount' => (float)$orderRow['total_amount'],
                    'order_status' => $orderRow['order_status'],
                    'created_at'   => $orderRow['created_at'],
                    'updated_at'   => $orderRow['updated_at'],
                ];
            }
        }

        return $this->response->setJSON([
            'status'            => 'success',
            'is_logged_in'      => $isLoggedIn,
            'role'              => $role,
            'unread_chat_count' => $unreadCount,
            'latest_message'    => $latestMessage,
            'total_orders'      => $totalOrders,
            'latest_order'      => $latestOrder,
            'server_time'       => date('Y-m-d H:i:s'),
        ]);
    }
}
