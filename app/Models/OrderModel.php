<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_number', 'po_number', 'customer_company_id', 'vendor_company_id',
        'subtotal', 'ppn_amount', 'total_vendor_fee', 'vendor_net_payout', 'shipping_amount', 'total_amount',
        'cashback_percent', 'cashback_amount', 'is_cashback_awarded',
        'payment_method', 'payment_status', 'order_status',
        'tax_invoice_required', 'customer_notes', 'shipping_address',
        'bast_number', 'invoice_number', 'vendor_invoice_number', 'tax_invoice_number', 'due_date',
        'buyer_paid_at', 'buyer_payment_ref', 'buyer_payment_bank',
        'buyer_sender_bank', 'buyer_sender_account_name', 'buyer_sender_account_number',
        'buyer_payment_proof', 'buyer_payment_amount', 'buyer_payment_notes', 'payment_rejection_reason',
        'vendor_payout_status', 'vendor_payout_due_date', 'vendor_paid_at', 'vendor_payout_ref', 'vendor_payout_bank',
        'delivered_at', 'delivered_by_name', 'delivery_notes', 'has_review',
        'design_file_buyer', 'design_file_vendor', 'design_specs', 'design_notes_vendor',
        'design_status', 'mockup_file_vendor', 'mockup_file_buyer', 'mockup_notes',
        'delivery_method', 'delivery_token', 'driver_name', 'driver_phone', 'vehicle_plate',
        'delivery_status_detail', 'delivery_proof_photo', 'recipient_name', 'recipient_phone',
        'delivery_latitude', 'delivery_longitude',
        'courier_name', 'tracking_number', 'expedition_service', 'shipped_at'
    ];
    protected $useTimestamps = true;

    public function __construct()
    {
        parent::__construct();
        $this->ensureDebtColumns();
    }

    /**
     * Automatically add debt, settlement & delivery tracking columns to `orders` table if not yet present
     */
    public function ensureDebtColumns(): void
    {
        static $checked = false;
        if ($checked) {
            return;
        }
        $checked = true;

        try {
            if (!$this->db->fieldExists('vendor_payout_status', 'orders')) {
                $this->db->query("ALTER TABLE `orders`
                    ADD COLUMN `buyer_paid_at` DATETIME NULL DEFAULT NULL,
                    ADD COLUMN `buyer_payment_ref` VARCHAR(150) NULL DEFAULT NULL,
                    ADD COLUMN `buyer_payment_bank` VARCHAR(100) NULL DEFAULT NULL,
                    ADD COLUMN `vendor_payout_status` VARCHAR(30) NOT NULL DEFAULT 'unpaid',
                    ADD COLUMN `vendor_payout_due_date` DATE NULL DEFAULT NULL,
                    ADD COLUMN `vendor_paid_at` DATETIME NULL DEFAULT NULL,
                    ADD COLUMN `vendor_payout_ref` VARCHAR(150) NULL DEFAULT NULL,
                    ADD COLUMN `vendor_payout_bank` VARCHAR(100) NULL DEFAULT NULL
                ");
            }
            if (!$this->db->fieldExists('buyer_sender_bank', 'orders')) {
                $this->db->query("ALTER TABLE `orders`
                    ADD COLUMN `buyer_sender_bank` VARCHAR(100) NULL DEFAULT NULL,
                    ADD COLUMN `buyer_sender_account_name` VARCHAR(150) NULL DEFAULT NULL,
                    ADD COLUMN `buyer_sender_account_number` VARCHAR(100) NULL DEFAULT NULL,
                    ADD COLUMN `buyer_payment_proof` VARCHAR(255) NULL DEFAULT NULL,
                    ADD COLUMN `buyer_payment_amount` DECIMAL(15,2) NULL DEFAULT NULL,
                    ADD COLUMN `buyer_payment_notes` TEXT NULL DEFAULT NULL,
                    ADD COLUMN `payment_rejection_reason` TEXT NULL DEFAULT NULL
                ");
            }
            if (!$this->db->fieldExists('delivered_at', 'orders')) {
                $this->db->query("ALTER TABLE `orders`
                    ADD COLUMN `delivered_at` DATETIME NULL DEFAULT NULL,
                    ADD COLUMN `delivered_by_name` VARCHAR(150) NULL DEFAULT NULL,
                    ADD COLUMN `delivery_notes` TEXT NULL DEFAULT NULL,
                    ADD COLUMN `has_review` TINYINT(1) NOT NULL DEFAULT 0
                ");
            }
            if (!$this->db->fieldExists('design_status', 'orders')) {
                $this->db->query("ALTER TABLE `orders`
                    ADD COLUMN `design_file_buyer` VARCHAR(255) NULL DEFAULT NULL,
                    ADD COLUMN `design_file_vendor` VARCHAR(255) NULL DEFAULT NULL,
                    ADD COLUMN `design_specs` TEXT NULL DEFAULT NULL,
                    ADD COLUMN `design_notes_vendor` TEXT NULL DEFAULT NULL,
                    ADD COLUMN `design_status` VARCHAR(40) NOT NULL DEFAULT 'none',
                    ADD COLUMN `mockup_file_vendor` VARCHAR(255) NULL DEFAULT NULL,
                    ADD COLUMN `mockup_file_buyer` VARCHAR(255) NULL DEFAULT NULL,
                    ADD COLUMN `mockup_notes` TEXT NULL DEFAULT NULL
                ");
            }
            if (!$this->db->fieldExists('delivery_token', 'orders')) {
                $this->db->query("ALTER TABLE `orders`
                    ADD COLUMN `delivery_method` VARCHAR(50) NOT NULL DEFAULT 'vendor_fleet',
                    ADD COLUMN `delivery_token` VARCHAR(64) NULL DEFAULT NULL,
                    ADD COLUMN `driver_name` VARCHAR(150) NULL DEFAULT NULL,
                    ADD COLUMN `driver_phone` VARCHAR(50) NULL DEFAULT NULL,
                    ADD COLUMN `vehicle_plate` VARCHAR(30) NULL DEFAULT NULL,
                    ADD COLUMN `delivery_status_detail` VARCHAR(50) NULL DEFAULT 'on_the_way',
                    ADD COLUMN `delivery_proof_photo` VARCHAR(255) NULL DEFAULT NULL,
                    ADD COLUMN `recipient_name` VARCHAR(150) NULL DEFAULT NULL,
                    ADD COLUMN `recipient_phone` VARCHAR(50) NULL DEFAULT NULL,
                    ADD COLUMN `delivery_latitude` VARCHAR(50) NULL DEFAULT NULL,
                    ADD COLUMN `delivery_longitude` VARCHAR(50) NULL DEFAULT NULL,
                    ADD INDEX `idx_delivery_token` (`delivery_token`)
                ");
                $this->db->query("UPDATE `orders` SET `delivery_token` = MD5(CONCAT('akuada_deliv_', id, '_', UNIX_TIMESTAMP(IFNULL(created_at, NOW())))) WHERE `delivery_token` IS NULL OR `delivery_token` = ''");
            }
            if (!$this->db->fieldExists('tracking_number', 'orders')) {
                $this->db->query("ALTER TABLE `orders`
                    ADD COLUMN `courier_name` VARCHAR(100) NULL DEFAULT NULL,
                    ADD COLUMN `tracking_number` VARCHAR(100) NULL DEFAULT NULL,
                    ADD COLUMN `expedition_service` VARCHAR(100) NULL DEFAULT NULL,
                    ADD COLUMN `shipped_at` DATETIME NULL DEFAULT NULL,
                    ADD INDEX `idx_tracking_number` (`tracking_number`)
                ");
            }
            if (function_exists('sync_orders_with_system_settings')) {
                sync_orders_with_system_settings();
            }
        } catch (\Throwable $e) {
            // Ignore if columns already exist
        }
    }

    public function getDetailedOrders($companyId = null, $role = null)
    {
        $builder = $this->select('orders.*, buyer.company_name as buyer_name, buyer.npwp as buyer_npwp, buyer.credit_limit_top as buyer_credit_limit, buyer.credit_used_top as buyer_credit_used, vendor.company_name as vendor_name, vendor.commission_rate as vendor_commission_rate, vendor.bank_name as vendor_bank_name, vendor.bank_account_number as vendor_bank_account_number, vendor.bank_account_holder as vendor_bank_account_holder')
                        ->join('companies as buyer', 'buyer.id = orders.customer_company_id', 'left')
                        ->join('companies as vendor', 'vendor.id = orders.vendor_company_id', 'left');

        if ($role === 'vendor' && $companyId) {
            $builder->where('orders.vendor_company_id', $companyId);
        } elseif ($role === 'customer' && $companyId) {
            $builder->where('orders.customer_company_id', $companyId);
        }

        $orders = $builder->orderBy('orders.id', 'DESC')->findAll();
        foreach ($orders as &$ord) {
            $ord = recalculate_order_from_settings($ord);
        }
        unset($ord);
        return $orders;
    }

    public function getOrderWithItems($orderId)
    {
        $order = $this->select('orders.*, 
            buyer.company_name as buyer_name, buyer.legal_type as buyer_legal_type, buyer.npwp as buyer_npwp, buyer.nib as buyer_nib, buyer.address as buyer_address, buyer.city as buyer_city, buyer.province as buyer_province, buyer.phone as buyer_phone, buyer.email as buyer_email, buyer.pic_name as buyer_pic, buyer.pic_phone as buyer_phone, buyer.pic_email as buyer_pic_email,
            vendor.company_name as vendor_name, vendor.legal_type as vendor_legal_type, vendor.npwp as vendor_npwp, vendor.nib as vendor_nib, vendor.address as vendor_address, vendor.city as vendor_city, vendor.province as vendor_province, vendor.phone as vendor_phone, vendor.email as vendor_email, vendor.pic_name as vendor_pic, vendor.pic_phone as vendor_pic_phone, vendor.pic_email as vendor_pic_email, vendor.commission_rate as vendor_commission_rate, vendor.bank_name as vendor_bank_name, vendor.bank_account_number as vendor_bank_account_number, vendor.bank_account_holder as vendor_bank_account_holder')
                      ->join('companies as buyer', 'buyer.id = orders.customer_company_id', 'left')
                      ->join('companies as vendor', 'vendor.id = orders.vendor_company_id', 'left')
                      ->where('orders.id', $orderId)
                      ->first();

        if ($order) {
            $itemModel = new OrderItemModel();
            $order['items'] = $itemModel->where('order_id', $order['id'])->findAll();

            $logModel = new OrderStatusLogModel();
            $order['status_logs'] = $logModel->getLogsByOrder($order['id']);

            $order = recalculate_order_from_settings($order);
        }

        return $order;
    }

    public function getOrderByDeliveryToken(string $token)
    {
        $order = $this->select('orders.*, 
            buyer.company_name as buyer_name, buyer.legal_type as buyer_legal_type, buyer.npwp as buyer_npwp, buyer.nib as buyer_nib, buyer.address as buyer_address, buyer.city as buyer_city, buyer.province as buyer_province, buyer.phone as buyer_phone, buyer.email as buyer_email, buyer.pic_name as buyer_pic, buyer.pic_phone as buyer_pic_phone, buyer.pic_email as buyer_pic_email,
            vendor.company_name as vendor_name, vendor.legal_type as vendor_legal_type, vendor.npwp as vendor_npwp, vendor.nib as vendor_nib, vendor.address as vendor_address, vendor.city as vendor_city, vendor.province as vendor_province, vendor.phone as vendor_phone, vendor.email as vendor_email, vendor.pic_name as vendor_pic, vendor.pic_phone as vendor_pic_phone, vendor.pic_email as vendor_pic_email, vendor.commission_rate as vendor_commission_rate')
                      ->join('companies as buyer', 'buyer.id = orders.customer_company_id', 'left')
                      ->join('companies as vendor', 'vendor.id = orders.vendor_company_id', 'left')
                      ->where('orders.delivery_token', $token)
                      ->first();

        if ($order) {
            $itemModel = new OrderItemModel();
            $order['items'] = $itemModel->where('order_id', $order['id'])->findAll();

            $logModel = new OrderStatusLogModel();
            $order['status_logs'] = $logModel->getLogsByOrder($order['id']);

            $order = recalculate_order_from_settings($order);
        }

        return $order;
    }

    public function ensureOrderDeliveryToken(int $orderId): string
    {
        $order = $this->find($orderId);
        if (!$order) {
            return '';
        }
        if (!empty($order['delivery_token'])) {
            return $order['delivery_token'];
        }
        $token = bin2hex(random_bytes(16));
        $this->update($orderId, ['delivery_token' => $token]);
        return $token;
    }
}
