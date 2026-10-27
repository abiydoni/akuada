<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'product_id', 'product_name', 'sku', 'uom', 'qty',
        'unit_price', 'vendor_fee_percent', 'vendor_fee_amount', 'vendor_net_amount', 'ppn_amount', 'subtotal'
    ];
    protected $useTimestamps = true;
}
