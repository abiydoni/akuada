<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\CompanyModel;
use App\Models\OrderModel;

class Home extends BaseController
{
    public function index(): string
    {
        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();
        $companyModel = new CompanyModel();
        $orderModel = new OrderModel();

        $categories = $categoryModel->getWithCount();
        $featuredProducts = $productModel->getDetailedProducts(8);
        $officialVendors = $companyModel->getVendors();

        return view('marketplace/home', [
            'title'            => 'AkuAda - Marketplace Pengadaan Operasional Perusahaan (B2B E-Procurement)',
            'categories'       => $categories,
            'featuredProducts' => $featuredProducts,
            'officialVendors'  => $officialVendors,
            'totalVendors'     => count($officialVendors),
            'totalProducts'    => $productModel->where('status', 'active')->countAllResults(),
        ]);
    }
}
