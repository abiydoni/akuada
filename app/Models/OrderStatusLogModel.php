<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderStatusLogModel extends Model
{
    protected $table = 'order_status_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'status', 'title', 'description', 'updated_by', 'created_at'
    ];
    protected $useTimestamps = false;

    public function getLogsByOrder($orderId)
    {
        return $this->where('order_id', $orderId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Filter log aktivitas untuk tampilan Corporate Buyer.
     * Buyer HANYA boleh melihat jejak proses pengadaan, kurasi desain, konfirmasi vendor,
     * status pengiriman/logistik, BAST, dan konfirmasi pembayaran tagihan invoice buyer.
     * Log internal pencairan settlement hutang platform ke vendor, fee/komisi, dan nomor rekening vendor
     * secara ketat disaring dan DILARANG tampil ke Buyer.
     */
    public function filterLogsForBuyer(array $logs): array
    {
        return array_values(array_filter($logs, function ($log) {
            $title = strtolower((string)($log['title'] ?? ''));
            $desc  = strtolower((string)($log['description'] ?? ''));

            // Saring dan sembunyikan log pencairan/settlement/payout internal vendor
            if (
                str_contains($title, 'settlement') ||
                str_contains($title, 'payout') ||
                str_contains($title, 'pencairan dana') ||
                str_contains($title, 'pencairan hak') ||
                str_contains($title, 'hak bersih') ||
                str_contains($title, 'bagi hasil') ||
                str_contains($title, 'komisi platform') ||
                str_contains($title, 'fee vendor') ||
                str_contains($desc, 'hak bersih vendor') ||
                str_contains($desc, 'rekening vendor') ||
                str_contains($desc, 'settle-') ||
                str_contains($desc, 'payout-') ||
                str_contains($desc, 'pencairan dana penjualan') ||
                str_contains($desc, 'pencairan dana hak bersih')
            ) {
                return false;
            }

            return true;
        }));
    }

    /**
     * Filter log aktivitas untuk Mitra Vendor.
     * Vendor boleh melihat informasi PO, produksi, pengiriman, dan pencairan hak mereka,
     * namun informasi internal kredit/plafon over-limit buyer disaring.
     */
    public function filterLogsForVendor(array $logs): array
    {
        return array_values(array_filter($logs, function ($log) {
            $title = strtolower((string)($log['title'] ?? ''));
            $desc  = strtolower((string)($log['description'] ?? ''));

            if (
                str_contains($title, 'over-limit') ||
                str_contains($title, 'plafon kredit') ||
                str_contains($desc, 'plafon kredit buyer')
            ) {
                return false;
            }

            return true;
        }));
    }

    /**
     * Filter log aktivitas untuk Driver/Kurir Ekspedisi.
     * Driver hanya boleh melihat status fisik pengiriman/logistik.
     */
    public function filterLogsForDriver(array $logs): array
    {
        return array_values(array_filter($logs, function ($log) {
            $title = strtolower((string)($log['title'] ?? ''));
            $desc  = strtolower((string)($log['description'] ?? ''));

            if (
                str_contains($title, 'settlement') ||
                str_contains($title, 'payout') ||
                str_contains($title, 'pencairan') ||
                str_contains($title, 'piutang') ||
                str_contains($title, 'pelunasan') ||
                str_contains($title, 'pembayaran') ||
                str_contains($title, 'tagihan') ||
                str_contains($title, 'plafon') ||
                str_contains($title, 'over-limit') ||
                str_contains($desc, 'settle-') ||
                str_contains($desc, 'payout-') ||
                str_contains($desc, 'rp ')
            ) {
                return false;
            }

            return true;
        }));
    }

    public function logStatus($orderId, $status, $title, $description = null, $updatedBy = null)
    {
        return $this->insert([
            'order_id'    => $orderId,
            'status'      => $status,
            'title'       => $title,
            'description' => $description,
            'updated_by'  => $updatedBy ?: (session('user_name') ?? 'Sistem'),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }
}
