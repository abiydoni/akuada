<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductPriceTierModel extends Model
{
    protected $table = 'product_price_tiers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['product_id', 'min_qty', 'max_qty', 'unit_price'];
    protected $useTimestamps = true;

    public function getPriceForQuantity($productId, $qty, $defaultPrice)
    {
        $tiers = $this->where('product_id', $productId)
                      ->orderBy('min_qty', 'DESC')
                      ->findAll();

        foreach ($tiers as $tier) {
            if ($qty >= $tier['min_qty']) {
                if ($tier['max_qty'] === null || $qty <= $tier['max_qty']) {
                    return (float)$tier['unit_price'];
                }
            }
        }

        return (float)$defaultPrice;
    }
}
