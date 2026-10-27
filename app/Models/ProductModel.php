<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'vendor_company_id', 'category_id', 'name', 'slug', 'sku', 'barcode',
        'brand', 'uom', 'description', 'spec_details', 'price_regular',
        'moq', 'weight_gram', 'stock', 'lead_time_days', 'is_taxable',
        'ppn_percent', 'vendor_fee_percent', 'tkdn_percent', 'is_sample_available', 'image_url', 'status'
    ];
    protected $useTimestamps = true;

    public function getDetailedProducts($limit = 20, $categoryId = null, $keyword = null)
    {
        $builder = $this->select('products.*, companies.company_name as vendor_name, companies.is_verified as vendor_verified, categories.name as category_name')
                        ->join('companies', 'companies.id = products.vendor_company_id', 'left')
                        ->join('categories', 'categories.id = products.category_id', 'left')
                        ->where('products.status', 'active');

        if ($categoryId) {
            $builder->where('products.category_id', $categoryId);
        }

        if ($keyword) {
            $builder->groupStart()
                    ->like('products.name', $keyword)
                    ->orLike('products.brand', $keyword)
                    ->orLike('products.sku', $keyword)
                    ->orLike('products.description', $keyword)
                    ->groupEnd();
        }

        return $builder->orderBy('products.id', 'DESC')->findAll($limit);
    }

    public function getProductBySlug($slug)
    {
        $product = $this->select('products.*, companies.company_name as vendor_name, companies.legal_type as vendor_legal_type, companies.npwp as vendor_npwp, companies.city as vendor_city, companies.is_verified as vendor_verified, categories.name as category_name, categories.slug as category_slug')
                        ->join('companies', 'companies.id = products.vendor_company_id', 'left')
                        ->join('categories', 'categories.id = products.category_id', 'left')
                        ->where('products.slug', $slug)
                        ->first();

        if ($product) {
            $tierModel = new ProductPriceTierModel();
            $product['price_tiers'] = $tierModel->where('product_id', $product['id'])
                                                ->orderBy('min_qty', 'ASC')
                                                ->findAll();
        }

        return $product;
    }
}
