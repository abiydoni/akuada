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
        'payment_method', 'payment_status', 'order_status',
        'tax_invoice_required', 'customer_notes', 'shipping_address',
        'bast_number', 'invoice_number', 'vendor_invoice_number', 'tax_invoice_number', 'due_date',
        'buyer_paid_at', 'buyer_payment_ref', 'buyer_payment_bank',
        'vendor_payout_status', 'vendor_payout_due_date', 'vendor_paid_at', 'vendor_payout_ref', 'vendor_payout_bank'
    ];
    protected $useTimestamps = true;

    public function __construct()
    {
        parent::__construct();
        $this->ensureDebtColumns();
    }

    /**
     * Automatically add debt & settlement tracking columns to `orders` table if not yet present
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

        return $builder->orderBy('orders.id', 'DESC')->findAll();
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
        }

        return $order;
    }
}
