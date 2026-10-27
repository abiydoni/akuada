<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'slug', 'icon', 'description', 'sort_order'];
    protected $useTimestamps = true;

    public function getWithCount()
    {
        return $this->select('categories.*, COUNT(products.id) as product_count')
                    ->join('products', 'products.category_id = categories.id AND products.status = "active"', 'left')
                    ->groupBy('categories.id')
                    ->orderBy('categories.sort_order', 'ASC')
                    ->findAll();
    }
}
