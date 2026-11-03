<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderReviewModel extends Model
{
    protected $table = 'order_reviews';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'customer_company_id', 'vendor_company_id', 'user_id',
        'buyer_name', 'rating', 'rating_quality', 'rating_delivery', 'rating_service',
        'review_title', 'review_text', 'vendor_reply', 'vendor_replied_at',
        'created_at', 'updated_at'
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
            if (!$this->db->tableExists('order_reviews')) {
                $this->db->query("CREATE TABLE IF NOT EXISTS `order_reviews` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `order_id` INT NOT NULL,
                    `customer_company_id` INT NOT NULL,
                    `vendor_company_id` INT NOT NULL,
                    `user_id` INT NULL DEFAULT NULL,
                    `buyer_name` VARCHAR(150) NOT NULL,
                    `rating` TINYINT NOT NULL DEFAULT 5,
                    `rating_quality` TINYINT NOT NULL DEFAULT 5,
                    `rating_delivery` TINYINT NOT NULL DEFAULT 5,
                    `rating_service` TINYINT NOT NULL DEFAULT 5,
                    `review_title` VARCHAR(255) NULL DEFAULT NULL,
                    `review_text` TEXT NOT NULL,
                    `vendor_reply` TEXT NULL DEFAULT NULL,
                    `vendor_replied_at` DATETIME NULL DEFAULT NULL,
                    `created_at` DATETIME NULL DEFAULT NULL,
                    `updated_at` DATETIME NULL DEFAULT NULL,
                    INDEX `idx_order_id` (`order_id`),
                    INDEX `idx_vendor_company` (`vendor_company_id`),
                    INDEX `idx_customer_company` (`customer_company_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
            }
        } catch (\Throwable $e) {
            // Ignore error if exists
        }
    }

    public function getByOrder(int $orderId): ?array
    {
        return $this->where('order_id', $orderId)->first();
    }

    public function getVendorReviews(int $vendorCompanyId, int $limit = 20): array
    {
        return $this->select('order_reviews.*, c.company_name as customer_name, o.order_number, o.po_number')
                    ->join('companies c', 'c.id = order_reviews.customer_company_id', 'left')
                    ->join('orders o', 'o.id = order_reviews.order_id', 'left')
                    ->where('order_reviews.vendor_company_id', $vendorCompanyId)
                    ->orderBy('order_reviews.created_at', 'DESC')
                    ->findAll($limit);
    }

    public function getVendorRatingSummary(int $vendorCompanyId): array
    {
        $reviews = $this->where('vendor_company_id', $vendorCompanyId)->findAll();
        $total = count($reviews);
        if ($total === 0) {
            return [
                'average'          => 5.0,
                'total'            => 0,
                'average_quality'  => 5.0,
                'average_delivery' => 5.0,
                'average_service'  => 5.0,
                'breakdown'        => [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0],
            ];
        }

        $sumRating = 0;
        $sumQuality = 0;
        $sumDelivery = 0;
        $sumService = 0;
        $breakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];

        foreach ($reviews as $r) {
            $val = (int)$r['rating'];
            if ($val < 1) $val = 1;
            if ($val > 5) $val = 5;
            $sumRating += $val;
            $breakdown[$val] = ($breakdown[$val] ?? 0) + 1;

            $sumQuality  += (int)($r['rating_quality'] ?? 5);
            $sumDelivery += (int)($r['rating_delivery'] ?? 5);
            $sumService  += (int)($r['rating_service'] ?? 5);
        }

        return [
            'average'          => round($sumRating / $total, 1),
            'total'            => $total,
            'average_quality'  => round($sumQuality / $total, 1),
            'average_delivery' => round($sumDelivery / $total, 1),
            'average_service'  => round($sumService / $total, 1),
            'breakdown'        => $breakdown,
        ];
    }
}

