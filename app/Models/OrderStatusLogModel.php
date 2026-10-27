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
