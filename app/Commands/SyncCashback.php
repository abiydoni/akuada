<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\OrderModel;
use App\Models\CompanyModel;
use App\Models\BuyerCashbackMutationModel;

class SyncCashback extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'app:sync-cashback';
    protected $description = 'Sinkronkan nominal cashback untuk pesanan yang telah selesai & BAST sah';

    public function run(array $params)
    {
        helper('akuada');
        CLI::write("Memeriksa pesanan selesai & BAST sah untuk sinkronisasi cashback...", "yellow");

        $orderModel = new OrderModel();
        $orders = $orderModel->whereIn('order_status', ['bast_signed', 'completed'])
                             ->where('is_cashback_awarded', 0)
                             ->findAll();

        if (empty($orders)) {
            CLI::write("Tidak ada pesanan selesai yang tertunda cashback-nya.", "green");
            return;
        }

        foreach ($orders as $o) {
            $orderId = (int)$o['id'];
            $cbAmount = process_buyer_order_cashback($orderId);
            CLI::write("Order #{$orderId} ({$o['po_number']}): Cashback diberikan sebesar " . format_rupiah($cbAmount), "green");
        }

        CLI::write("Sinkronisasi cashback selesai!", "green");
    }
}
