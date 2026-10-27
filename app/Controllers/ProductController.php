<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Models\ProductPriceTierModel;

class ProductController extends BaseController
{
    public function index()
    {
        $categoryModel = new CategoryModel();
        $productModel = new ProductModel();

        $catId = $this->request->getGet('cat');
        $keyword = $this->request->getGet('q');

        $categories = $categoryModel->getWithCount();
        $products = $productModel->getDetailedProducts(40, $catId, $keyword);
        
        $activeCategory = null;
        if ($catId) {
            $activeCategory = $categoryModel->find($catId);
        }

        return view('marketplace/catalog', [
            'title'          => 'Katalog Pengadaan Operasional - AkuAda B2B',
            'categories'     => $categories,
            'products'       => $products,
            'activeCategory' => $activeCategory,
            'keyword'        => $keyword,
            'catId'          => $catId
        ]);
    }

    public function detail($slug)
    {
        $productModel = new ProductModel();
        $product = $productModel->getProductBySlug($slug);

        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Barang pengadaan tidak ditemukan');
        }

        $relatedProducts = $productModel->where('category_id', $product['category_id'])
                                       ->where('id !=', $product['id'])
                                       ->findAll(4);

        return view('marketplace/detail', [
            'title'           => $product['name'] . ' - AkuAda B2B',
            'product'         => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }

    public function requestSample($id)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($id);

        if (!$product || !$product['is_sample_available']) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Sample tidak tersedia untuk produk ini.']);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Permintaan sample unit untuk barang "' . $product['name'] . '" berhasil diajukan! Tim Account Manager AkuAda dan Vendor akan mengirimkan sample resmi ke alamat perusahaan Anda.'
        ]);
    }
}
