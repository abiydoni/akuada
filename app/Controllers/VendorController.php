<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\ProductPriceTierModel;
use App\Models\CategoryModel;
use App\Models\RfqModel;
use App\Models\RfqItemModel;
use App\Models\CompanyModel;

class VendorController extends BaseController
{
    private function getVendorCompanyId()
    {
        return session('company_id') ?? 1; // Default to PT Mitra Office Solusindo
    }

    public function dashboard()
    {
        $vendorId = $this->getVendorCompanyId();
        $orderModel = new OrderModel();
        $productModel = new ProductModel();
        $rfqModel = new RfqModel();
        $companyModel = new CompanyModel();

        $company = $companyModel->find($vendorId);
        $orders = $orderModel->getDetailedOrders($vendorId, 'vendor');
        $products = $productModel->where('vendor_company_id', $vendorId)->findAll();
        $rfqs = $rfqModel->getDetailedRfqs($vendorId, 'vendor');

        $totalRevenue = 0;
        $totalGrossSales = 0;
        $totalPlatformFee = 0;
        $totalNetPayout = 0;

        foreach ($orders as $o) {
            $totalRevenue += (float)$o['total_amount'];
            $totalGrossSales += (float)$o['subtotal'];
            $totalPlatformFee += (float)($o['total_vendor_fee'] ?? 0);
            $totalNetPayout += (float)($o['vendor_net_payout'] ?? ($o['subtotal'] - ($o['total_vendor_fee'] ?? 0)));
        }

        return view('vendor/dashboard', [
            'title'             => 'Dashboard Supplier - Portal Vendor AkuAda',
            'company'           => $company,
            'totalRevenue'      => $totalRevenue,
            'totalGrossSales'   => $totalGrossSales,
            'totalPlatformFee'  => $totalPlatformFee,
            'totalNetPayout'    => $totalNetPayout,
            'totalOrders'       => count($orders),
            'totalProducts'     => count($products),
            'totalRfqs'         => count($rfqs),
            'recentOrders'      => array_slice($orders, 0, 5),
            'products'          => array_slice($products, 0, 5),
        ]);
    }

    public function orders()
    {
        $vendorId = $this->getVendorCompanyId();
        $orderModel = new OrderModel();
        $orders = $orderModel->getDetailedOrders($vendorId, 'vendor');

        return view('vendor/orders', [
            'title'  => 'Pesanan Masuk & Surat Jalan BAST - Vendor AkuAda',
            'orders' => $orders
        ]);
    }

    public function orderDetail($id)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Pesanan tidak ditemukan');
        }

        return view('vendor/order_detail', [
            'title' => 'Detail Pesanan PO #' . $order['po_number'] . ' - Vendor AkuAda',
            'order' => $order
        ]);
    }

    public function vendorInvoice($id)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice penagihan tidak ditemukan');
        }

        $companyId = session('company_id');
        if (session('user_role') === 'vendor' && $companyId && $order['vendor_company_id'] != $companyId) {
            session()->setFlashdata('error', 'Anda tidak memiliki hak akses melihat invoice penagihan ini.');
            return redirect()->to(base_url('vendor/orders'));
        }

        return view('vendor/invoice', [
            'title' => 'Faktur Penagihan Vendor ke AkuAda - ' . ($order['vendor_invoice_number'] ?? $order['po_number']),
            'order' => $order,
            'isAdmin' => false
        ]);
    }

    public function updateShipping()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $status = $this->request->getPost('order_status');
        $bastNumber = $this->request->getPost('bast_number');
        $taxInvoiceNumber = $this->request->getPost('tax_invoice_number');

        $orderModel = new OrderModel();
        $orderModel->update($orderId, [
            'order_status'       => $status,
            'bast_number'        => $bastNumber,
            'tax_invoice_number' => $taxInvoiceNumber,
            'updated_at'         => date('Y-m-d H:i:s')
        ]);

        $statusLabels = [
            'po_issued'       => 'Penerbitan PO',
            'po_approved'     => 'PO Dikonfirmasi Vendor',
            'in_production'   => 'Proses Produksi / Penyiapan Barang',
            'quality_control' => 'Pemeriksaan Mutu & Standar (QC)',
            'packaging'       => 'Pengemasan & Paletisasi Master Box',
            'shipped'         => 'Dalam Pengiriman (Surat Jalan Diterbitkan)',
            'delivered'       => 'Barang Tiba di Lokasi Buyer',
            'bast_signed'     => 'Pengesahan BAST',
            'completed'       => 'Selesai & Lunas',
            'cancelled'       => 'Pesanan Dibatalkan',
        ];
        $statusTitle = $statusLabels[$status] ?? ucfirst($status);
        $statusNotes = $this->request->getPost('status_notes') ?: ('Status pemrosesan pesanan diperbarui oleh ' . (session('company_name') ?? 'Vendor') . '.');

        $logModel = new \App\Models\OrderStatusLogModel();
        $logModel->logStatus($orderId, $status, $statusTitle, $statusNotes, session('user_name') ?? 'Vendor Mitra');

        session()->setFlashdata('success', 'Status pengadaan berhasil diperbarui menjadi: ' . $statusTitle . '!');
        return redirect()->back();
    }

    public function products()
    {
        $vendorId = $this->getVendorCompanyId();
        $productModel = new ProductModel();
        $products = $productModel->select('products.*, categories.name as category_name')
                                ->join('categories', 'categories.id = products.category_id', 'left')
                                ->where('vendor_company_id', $vendorId)
                                ->findAll();

        return view('vendor/products', [
            'title'    => 'Katalog Barang B2B Saya - Vendor AkuAda',
            'products' => $products
        ]);
    }

    public function newProduct()
    {
        $vendorId = $this->getVendorCompanyId();
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->orderBy('name', 'ASC')->findAll();

        $companyModel = new CompanyModel();
        $company = $companyModel->find($vendorId);
        $vendorContractFee = (float)($company['commission_rate'] ?? get_default_vendor_fee_rate());

        return view('vendor/product_new', [
            'title'             => 'Tambah Barang Pengadaan Baru - Vendor AkuAda',
            'categories'        => $categories,
            'company'           => $company,
            'vendorContractFee' => $vendorContractFee,
        ]);
    }

    public function storeProduct()
    {
        $vendorId = $this->getVendorCompanyId();
        $companyModel = new CompanyModel();
        $company = $companyModel->find($vendorId);
        $contractFee = (float)($company['commission_rate'] ?? get_default_vendor_fee_rate());

        $name = $this->request->getPost('name');
        $categoryId = (int)$this->request->getPost('category_id');
        $brand = $this->request->getPost('brand');
        $uom = $this->request->getPost('uom') ?? 'Pcs';
        $sku = $this->request->getPost('sku') ?: 'SKU-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $barcode = $this->request->getPost('barcode');
        $price = (float)$this->request->getPost('price_regular');
        $moq = max(1, (int)$this->request->getPost('moq'));
        $stock = (int)$this->request->getPost('stock');
        $leadTime = (int)$this->request->getPost('lead_time_days');
        $tkdn = (float)$this->request->getPost('tkdn_percent');
        $isTaxable = $this->request->getPost('is_taxable') ? 1 : 0;
        $isSample = $this->request->getPost('is_sample_available') ? 1 : 0;
        $desc = $this->request->getPost('description');
        $spec = $this->request->getPost('spec_details');
        $imageUrl = $this->request->getPost('image_url') ?: 'https://images.unsplash.com/photo-1589330694653-dad6d3240e2b?auto=format&fit=crop&w=600&q=80';
        $feeInput = $this->request->getPost('vendor_fee_percent');
        $feePercent = ($feeInput !== null && $feeInput !== '') ? (float)$feeInput : $contractFee;

        $slug = url_title($name, '-', true) . '-' . rand(100, 999);
        $now = date('Y-m-d H:i:s');

        $productModel = new ProductModel();
        $productId = $productModel->insert([
            'vendor_company_id'   => $vendorId,
            'category_id'         => $categoryId,
            'name'                => $name,
            'slug'                => $slug,
            'sku'                 => $sku,
            'barcode'             => $barcode,
            'brand'               => $brand,
            'uom'                 => $uom,
            'description'         => $desc,
            'spec_details'        => $spec,
            'price_regular'       => $price,
            'moq'                 => $moq,
            'stock'               => $stock,
            'lead_time_days'      => $leadTime,
            'is_taxable'          => $isTaxable,
            'ppn_percent'         => 11.00,
            'vendor_fee_percent'  => $feePercent,
            'tkdn_percent'        => $tkdn,
            'is_sample_available' => $isSample,
            'image_url'           => $imageUrl,
            'status'              => 'active',
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);

        // Tier pricing
        $tierMin = $this->request->getPost('tier_min') ?? [];
        $tierMax = $this->request->getPost('tier_max') ?? [];
        $tierPrice = $this->request->getPost('tier_price') ?? [];

        $tierModel = new ProductPriceTierModel();
        foreach ($tierMin as $idx => $min) {
            if (!empty($min) && !empty($tierPrice[$idx])) {
                $tierModel->insert([
                    'product_id' => $productId,
                    'min_qty'    => (int)$min,
                    'max_qty'    => !empty($tierMax[$idx]) ? (int)$tierMax[$idx] : null,
                    'unit_price' => (float)$tierPrice[$idx],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        session()->setFlashdata('success', 'Barang pengadaan baru berhasil ditambahkan ke katalog B2B AkuAda!');
        return redirect()->to('/vendor/products');
    }

    public function editProduct($id)
    {
        $vendorId = $this->getVendorCompanyId();
        $productModel = new ProductModel();
        $product = $productModel->where('id', $id)
                                ->where('vendor_company_id', $vendorId)
                                ->first();

        if (!$product) {
            session()->setFlashdata('error', 'Barang tidak ditemukan atau bukan milik perusahaan Anda.');
            return redirect()->to('/vendor/products');
        }

        $categoryModel = new CategoryModel();
        $categories = $categoryModel->orderBy('name', 'ASC')->findAll();

        $tierModel = new ProductPriceTierModel();
        $tiers = $tierModel->where('product_id', $id)->orderBy('min_qty', 'ASC')->findAll();

        $companyModel = new CompanyModel();
        $company = $companyModel->find($vendorId);
        $vendorContractFee = (float)($company['commission_rate'] ?? get_default_vendor_fee_rate());

        return view('vendor/product_edit', [
            'title'             => 'Edit Barang: ' . $product['name'] . ' - Vendor AkuAda',
            'product'           => $product,
            'categories'        => $categories,
            'tiers'             => $tiers,
            'company'           => $company,
            'vendorContractFee' => $vendorContractFee,
        ]);
    }

    public function updateProduct($id)
    {
        $vendorId = $this->getVendorCompanyId();
        $productModel = new ProductModel();
        $product = $productModel->where('id', $id)
                                ->where('vendor_company_id', $vendorId)
                                ->first();

        if (!$product) {
            session()->setFlashdata('error', 'Barang tidak ditemukan atau bukan milik perusahaan Anda.');
            return redirect()->to('/vendor/products');
        }

        $name = $this->request->getPost('name');
        $categoryId = (int)$this->request->getPost('category_id');
        $brand = $this->request->getPost('brand');
        $uom = $this->request->getPost('uom') ?? 'Pcs';
        $sku = $this->request->getPost('sku') ?: $product['sku'];
        $barcode = $this->request->getPost('barcode');
        $price = (float)$this->request->getPost('price_regular');
        $moq = max(1, (int)$this->request->getPost('moq'));
        $stock = (int)$this->request->getPost('stock');
        $leadTime = (int)$this->request->getPost('lead_time_days');
        $tkdn = (float)$this->request->getPost('tkdn_percent');
        $isTaxable = $this->request->getPost('is_taxable') ? 1 : 0;
        $isSample = $this->request->getPost('is_sample_available') ? 1 : 0;
        $desc = $this->request->getPost('description');
        $spec = $this->request->getPost('spec_details');
        $imageUrl = $this->request->getPost('image_url') ?: $product['image_url'];
        $status = $this->request->getPost('status') ?? 'active';

        $companyModel = new CompanyModel();
        $company = $companyModel->find($vendorId);
        $contractFee = (float)($company['commission_rate'] ?? get_default_vendor_fee_rate());

        $feeInput = $this->request->getPost('vendor_fee_percent');
        $feePercent = ($feeInput !== null && $feeInput !== '') ? (float)$feeInput : $contractFee;

        $now = date('Y-m-d H:i:s');

        $productModel->update($id, [
            'category_id'         => $categoryId,
            'name'                => $name,
            'sku'                 => $sku,
            'barcode'             => $barcode,
            'brand'               => $brand,
            'uom'                 => $uom,
            'description'         => $desc,
            'spec_details'        => $spec,
            'price_regular'       => $price,
            'moq'                 => $moq,
            'stock'               => $stock,
            'lead_time_days'      => $leadTime,
            'is_taxable'          => $isTaxable,
            'vendor_fee_percent'  => $feePercent,
            'tkdn_percent'        => $tkdn,
            'is_sample_available' => $isSample,
            'image_url'           => $imageUrl,
            'status'              => $status,
            'updated_at'          => $now,
        ]);

        // Update Tier pricing: remove existing and re-insert
        $tierMin = $this->request->getPost('tier_min') ?? [];
        $tierMax = $this->request->getPost('tier_max') ?? [];
        $tierPrice = $this->request->getPost('tier_price') ?? [];

        $tierModel = new ProductPriceTierModel();
        $tierModel->where('product_id', $id)->delete();

        foreach ($tierMin as $idx => $min) {
            if (!empty($min) && !empty($tierPrice[$idx])) {
                $tierModel->insert([
                    'product_id' => $id,
                    'min_qty'    => (int)$min,
                    'max_qty'    => !empty($tierMax[$idx]) ? (int)$tierMax[$idx] : null,
                    'unit_price' => (float)$tierPrice[$idx],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        session()->setFlashdata('success', 'Data barang ' . $name . ' berhasil diperbarui!');
        return redirect()->to('/vendor/products');
    }

    public function deleteProduct($id)
    {
        $vendorId = $this->getVendorCompanyId();
        $productModel = new ProductModel();
        $product = $productModel->where('id', $id)
                                ->where('vendor_company_id', $vendorId)
                                ->first();

        if ($product) {
            // Set inactive
            $productModel->update($id, ['status' => 'inactive']);
            session()->setFlashdata('toast', 'Barang ' . $product['name'] . ' dinonaktifkan dari katalog.');
        }

        return redirect()->to('/vendor/products');
    }

    public function rfqs()
    {
        $vendorId = $this->getVendorCompanyId();
        $rfqModel = new RfqModel();
        $rfqs = $rfqModel->getDetailedRfqs($vendorId, 'vendor');

        return view('vendor/rfqs', [
            'title' => 'Permintaan RFQ & Negosiasi Harga - Vendor AkuAda',
            'rfqs'  => $rfqs
        ]);
    }

    public function submitQuote()
    {
        $rfqId = (int)$this->request->getPost('rfq_id');
        $itemIds = $this->request->getPost('item_id') ?? [];
        $offeredPrices = $this->request->getPost('offered_price') ?? [];

        $rfqItemModel = new RfqItemModel();
        foreach ($itemIds as $i => $id) {
            if (isset($offeredPrices[$i]) && $offeredPrices[$i] !== '') {
                $rfqItemModel->update($id, ['vendor_offered_price' => (float)$offeredPrices[$i]]);
            }
        }

        $rfqModel = new RfqModel();
        $rfqModel->update($rfqId, [
            'status'     => 'quoted',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Penawaran harga resmi (Quotation) berhasil dikirimkan ke Buyer Perusahaan!');
        return redirect()->back();
    }

    public function companyProfile()
    {
        $vendorId = $this->getVendorCompanyId();
        $companyModel = new CompanyModel();
        $company = $companyModel->find($vendorId);

        return view('vendor/profile', [
            'title'   => 'Profil Legalitas Vendor - AkuAda B2B',
            'company' => $company
        ]);
    }
}
