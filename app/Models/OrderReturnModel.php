<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderReturnModel extends Model
{
    protected $table = 'order_returns';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'return_number', 'customer_company_id', 'vendor_company_id',
        'user_id', 'requester_name', 'reason_category', 'resolution_requested',
        'item_description', 'damage_description', 'evidence_photo',
        'status', 'vendor_response', 'vendor_responded_at', 'admin_notes',
        'replacement_tracking', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = true;

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
            if (!$this->db->tableExists('order_returns')) {
                $this->db->query("CREATE TABLE IF NOT EXISTS `order_returns` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `order_id` INT NOT NULL,
                    `return_number` VARCHAR(50) NOT NULL,
                    `customer_company_id` INT NOT NULL,
                    `vendor_company_id` INT NOT NULL,
                    `user_id` INT NULL DEFAULT NULL,
                    `requester_name` VARCHAR(150) NOT NULL,
                    `reason_category` VARCHAR(50) NOT NULL,
                    `resolution_requested` VARCHAR(50) NOT NULL DEFAULT 'replacement',
                    `item_description` TEXT NULL DEFAULT NULL,
                    `damage_description` TEXT NOT NULL,
                    `evidence_photo` VARCHAR(255) NULL DEFAULT NULL,
                    `status` VARCHAR(30) NOT NULL DEFAULT 'pending',
                    `vendor_response` TEXT NULL DEFAULT NULL,
                    `vendor_responded_at` DATETIME NULL DEFAULT NULL,
                    `admin_notes` TEXT NULL DEFAULT NULL,
                    `replacement_tracking` VARCHAR(150) NULL DEFAULT NULL,
                    `created_at` DATETIME NULL DEFAULT NULL,
                    `updated_at` DATETIME NULL DEFAULT NULL,
                    INDEX `idx_order_id` (`order_id`),
                    INDEX `idx_customer` (`customer_company_id`),
                    INDEX `idx_vendor` (`vendor_company_id`),
                    INDEX `idx_status` (`status`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            }
        } catch (\Throwable $e) {
            // Ignore if exists
        }
    }

    public function getReturnsByOrder(int $orderId): array
    {
        return $this->where('order_id', $orderId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getLatestReturn(int $orderId): ?array
    {
        return $this->where('order_id', $orderId)
                    ->orderBy('id', 'DESC')
                    ->first();
    }

    public function generateReturnNumber(int $orderId): string
    {
        return 'RTR/' . date('Ym') . '/' . str_pad($orderId, 4, '0', STR_PAD_LEFT) . '-' . substr(strtoupper(uniqid()), -4);
    }
}

