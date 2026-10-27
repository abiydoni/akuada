<?php

namespace App\Models;

use CodeIgniter\Model;

class RfqItemModel extends Model
{
    protected $table = 'rfq_items';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'rfq_id', 'product_id', 'item_name', 'qty', 'uom',
        'target_unit_price', 'vendor_offered_price', 'notes'
    ];
    protected $useTimestamps = true;
}
