<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\ProductPriceTierModel;
use App\Models\CategoryModel;
use App\Models\RfqModel;
use App\Models\RfqItemModel;
use App\Models\CompanyModel;
use App\Models\OrderReviewModel;
use App\Models\OrderMessageModel;
use App\Models\OrderReturnModel;

class VendorController extends BaseController
{
    private function getVendorCompanyId()
    {
        return ensure_role_session('vendor');
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

        $reviewModel = new OrderReviewModel();
        $review = $reviewModel->getByOrder((int)$id);

        $returnModel = new OrderReturnModel();
        $returns = $returnModel->getReturnsByOrder((int)$id);
        $latestReturn = !empty($returns) ? $returns[0] : null;

        return view('vendor/order_detail', [
            'title'        => 'Detail Pesanan PO #' . ($order['po_number'] ?? $order['order_number']) . ' - Vendor AkuAda',
            'order'        => $order,
            'review'       => $review,
            'returns'      => $returns,
            'latestReturn' => $latestReturn,
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
            'title'   => 'Faktur Penagihan Vendor ke AkuAda - ' . ($order['vendor_invoice_number'] ?? $order['po_number']),
            'order'   => $order,
            'isAdmin' => false
        ]);
    }

    public function bast($id)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Dokumen BAST tidak ditemukan');
        }

        return view('shared/bast_document', [
            'title' => 'Berita Acara Serah Terima (BAST) & Surat Jalan ' . ($order['bast_number'] ?? $order['po_number']),
            'order' => $order,
        ]);
    }

    public function taxInvoice($id)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Faktur Pajak tidak ditemukan');
        }

        return view('shared/tax_invoice_document', [
            'title' => 'e-Faktur Pajak PPN ' . ($order['tax_invoice_number'] ?? $order['po_number']),
            'order' => $order,
        ]);
    }

    public function updateShipping()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $status = $this->request->getPost('order_status');

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);
        if (!$order) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan.');
            return redirect()->back();
        }

        if (in_array($order['order_status'], ['completed', 'bast_signed'])) {
            session()->setFlashdata('error', 'Pesanan telah dikonfirmasi SELESAI oleh pihak Buyer dan status telah dikunci secara permanen. Pembaruan tidak dapat dilakukan lagi.');
            return redirect()->back();
        }

        if (in_array($status, ['completed', 'bast_signed'])) {
            session()->setFlashdata('error', 'Status "Pesanan Selesai" dan "Pengesahan BAST" hanya dapat dikonfirmasi langsung oleh pihak Corporate Buyer setelah memeriksa barang fisik.');
            return redirect()->back();
        }

        $bastNumber = trim((string)$this->request->getPost('bast_number'));
        $taxInvoiceNumber = trim((string)$this->request->getPost('tax_invoice_number'));
        $vendorInvoiceNumber = trim((string)$this->request->getPost('vendor_invoice_number'));
        $shippingAmount = $this->request->getPost('shipping_amount');

        $seq = str_pad((string)$orderId, 4, '0', STR_PAD_LEFT);
        $ym = !empty($order['created_at']) ? date('Ym', strtotime($order['created_at'])) : date('Ym');

        if (empty($bastNumber)) {
            $bastNumber = !empty($order['bast_number']) ? $order['bast_number'] : ('BAST/AKUADA/' . $ym . '/' . $seq);
        }
        if (empty($taxInvoiceNumber)) {
            $taxInvoiceNumber = !empty($order['tax_invoice_number']) ? $order['tax_invoice_number'] : ('010.002-26.' . sprintf('%08d', abs(crc32('order_' . $orderId))));
        }
        if (empty($vendorInvoiceNumber)) {
            $vendorInvoiceNumber = !empty($order['vendor_invoice_number']) ? $order['vendor_invoice_number'] : ('VINV/' . $ym . '/' . $seq);
        }

        $updateData = [
            'order_status'          => $status,
            'bast_number'           => $bastNumber,
            'tax_invoice_number'    => $taxInvoiceNumber,
            'vendor_invoice_number' => $vendorInvoiceNumber,
            'shipping_amount'       => ($shippingAmount !== null && $shippingAmount !== '') ? max(0, (float)$shippingAmount) : 0,
            'updated_at'            => date('Y-m-d H:i:s')
        ];
        if ($status === 'delivered' && empty($order['delivered_at'])) {
            $updateData['delivered_at'] = date('Y-m-d H:i:s');
        }

        $orderModel = new OrderModel();
        $orderModel->update($orderId, $updateData);
        sync_orders_with_system_settings();

        $statusLabels = [
            'po_issued'       => 'Penerbitan PO',
            'po_approved'     => 'PO Dikonfirmasi Vendor',
            'in_production'   => 'Proses Produksi / Penyiapan Barang',
            'quality_control' => 'Pemeriksaan Mutu & Standar (QC)',
            'packaging'       => 'Pengemasan & Paletisasi Master Box',
            'shipped'         => 'Dalam Pengiriman (Surat Jalan Diterbitkan)',
            'delivered'       => 'Barang Sampai di Lokasi / Tujuan',
            'bast_signed'     => 'Pengesahan BAST',
            'completed'       => 'Selesai & Lunas',
            'cancelled'       => 'Pesanan Dibatalkan',
        ];
        $statusTitle = $statusLabels[$status] ?? ucfirst($status);
        $statusNotes = $this->request->getPost('status_notes') ?: ('Status pengiriman diperbarui oleh ' . (session('company_name') ?? 'Vendor') . '.');

        $logModel = new \App\Models\OrderStatusLogModel();
        $logModel->logStatus($orderId, $status, $statusTitle, $statusNotes, session('user_name') ?? 'Vendor Mitra');

        if ($status === 'delivered') {
            $msgModel = new \App\Models\OrderMessageModel();
            $msgModel->sendMessage(
                $orderId,
                (int)session('user_id'),
                session('user_name') ?? 'Vendor Logistik',
                'vendor',
                "📦 Barang pesanan telah sampai di lokasi tujuan pemesan. Silakan tim Buyer memeriksa fisik barang dan menekan tombol 'Konfirmasi Selesai & Sahkan BAST'."
            );
        }

        session()->setFlashdata('success', 'Status pengadaan berhasil diperbarui menjadi: ' . $statusTitle . '!');
        return redirect()->back();
    }

    public function replyReview()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $reply = trim((string)$this->request->getPost('vendor_reply'));

        if (empty($reply)) {
            session()->setFlashdata('error', 'Tanggapan balasan ulasan tidak boleh kosong.');
            return redirect()->back();
        }

        $reviewModel = new OrderReviewModel();
        $review = $reviewModel->getByOrder($orderId);
        if (!$review) {
            session()->setFlashdata('error', 'Data ulasan tidak ditemukan.');
            return redirect()->back();
        }

        $vendorId = $this->getVendorCompanyId();
        if ((int)$review['vendor_company_id'] !== (int)$vendorId && session('role') !== 'admin') {
            session()->setFlashdata('error', 'Akses balasan ulasan tidak diizinkan.');
            return redirect()->back();
        }

        $reviewModel->update($review['id'], [
            'vendor_reply'      => $reply,
            'vendor_replied_at' => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Tanggapan ulasan Anda berhasil disimpan dan dikirimkan kepada pembeli!');
        return redirect()->back();
    }

    public function respondReturn()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $returnId = (int)$this->request->getPost('return_id');
        $action = $this->request->getPost('action'); // 'approve_replacement', 'approve_refund', 'reject'
        $responseNotes = trim((string)$this->request->getPost('vendor_response'));
        $trackingNumber = trim((string)$this->request->getPost('replacement_tracking'));

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);
        if (!$order) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan.');
            return redirect()->back();
        }

        $vendorId = $this->getVendorCompanyId();
        if ((int)$order['vendor_company_id'] !== (int)$vendorId && session('role') !== 'admin') {
            session()->setFlashdata('error', 'Akses respon retur tidak diizinkan.');
            return redirect()->back();
        }

        $returnModel = new OrderReturnModel();
        $ret = $returnModel->find($returnId);
        if (!$ret || (int)$ret['order_id'] !== $orderId) {
            session()->setFlashdata('error', 'Data pengajuan retur tidak ditemukan.');
            return redirect()->back();
        }

        $now = date('Y-m-d H:i:s');
        $vendorName = session('company_name') ?: 'Mitra Vendor';

        if ($action === 'approve_replacement') {
            $returnModel->update($returnId, [
                'status'               => 'approved',
                'vendor_response'      => $responseNotes ?: 'Permohonan retur disetujui. Unit barang pengganti baru segera dikirimkan ke alamat Buyer.',
                'vendor_responded_at'  => $now,
                'replacement_tracking' => $trackingNumber,
                'updated_at'           => $now,
            ]);

            $orderModel->update($orderId, [
                'order_status' => 'replacement_shipped',
                'updated_at'   => $now,
            ]);

            $logModel = new \App\Models\OrderStatusLogModel();
            $logModel->logStatus(
                $orderId,
                'replacement_shipped',
                'Retur Disetujui - Barang Pengganti Dikirim Vendor',
                "Vendor menyetujui klaim retur No: {$ret['return_number']}. Barang pengganti dikirimkan dengan No. Resi/Surat Jalan: " . ($trackingNumber ?: 'Menyusul') . ". Catatan: {$responseNotes}",
                session('user_name') ?? 'Mitra Vendor'
            );

            $messageModel = new OrderMessageModel();
            $messageModel->addMessage(
                $orderId,
                session('user_id'),
                session('user_name') ?: 'Mitra Vendor',
                'vendor',
                $vendorName,
                "✅ [KLAIM RETUR DISETUJUI - BARANG PENGGANTI DIKIRIM]\nNo. Retur: {$ret['return_number']}\nSurat Jalan/Resi Pengganti: " . ($trackingNumber ?: '-') . "\nCatatan Vendor: {$responseNotes}\nBarang pengganti telah kami kirimkan menuju alamat gudang Anda."
            );

            session()->setFlashdata('success', 'Klaim retur disetujui dan pengiriman barang pengganti berhasil dicatat!');
        } elseif ($action === 'approve_refund') {
            $returnModel->update($returnId, [
                'status'              => 'completed',
                'vendor_response'     => $responseNotes ?: 'Permohonan retur disetujui untuk pemotongan tagihan TOP / Credit Note.',
                'vendor_responded_at' => $now,
                'updated_at'          => $now,
            ]);

            $orderModel->update($orderId, [
                'order_status' => 'return_completed',
                'updated_at'   => $now,
            ]);

            $logModel = new \App\Models\OrderStatusLogModel();
            $logModel->logStatus(
                $orderId,
                'return_completed',
                'Retur Disetujui - Penyelesaian Potong Tagihan TOP',
                "Vendor menyetujui klaim retur No: {$ret['return_number']} dengan resolusi potong tagihan / Credit Note. Catatan: {$responseNotes}",
                session('user_name') ?? 'Mitra Vendor'
            );

            $messageModel = new OrderMessageModel();
            $messageModel->addMessage(
                $orderId,
                session('user_id'),
                session('user_name') ?: 'Mitra Vendor',
                'vendor',
                $vendorName,
                "✅ [KLAIM RETUR DISETUJUI - RESOLUSI KREDIT / REFUND]\nNo. Retur: {$ret['return_number']}\nVendor menyetujui pemotongan tagihan PO ini. Catatan: {$responseNotes}"
            );

            session()->setFlashdata('success', 'Klaim retur berhasil disetujui dengan resolusi penyesuaian tagihan!');
        } else {
            // Reject
            if (empty($responseNotes)) {
                session()->setFlashdata('error', 'Wajib mencantumkan alasan penolakan retur secara jelas.');
                return redirect()->back();
            }

            $returnModel->update($returnId, [
                'status'              => 'rejected',
                'vendor_response'     => $responseNotes,
                'vendor_responded_at' => $now,
                'updated_at'          => $now,
            ]);

            $orderModel->update($orderId, [
                'order_status' => 'return_rejected',
                'updated_at'   => $now,
            ]);

            $logModel = new \App\Models\OrderStatusLogModel();
            $logModel->logStatus(
                $orderId,
                'return_rejected',
                'Klaim Retur Ditolak oleh Vendor',
                "Vendor menolak klaim retur No: {$ret['return_number']} dengan alasan: {$responseNotes}",
                session('user_name') ?? 'Mitra Vendor'
            );

            $messageModel = new OrderMessageModel();
            $messageModel->addMessage(
                $orderId,
                session('user_id'),
                session('user_name') ?: 'Mitra Vendor',
                'vendor',
                $vendorName,
                "❌ [KLAIM RETUR DITOLAK OLEH VENDOR]\nNo. Retur: {$ret['return_number']}\nAlasan Penolakan: {$responseNotes}\nJika terdapat perbedaan persepsi, silakan gunakan ruang chat ini atau hubungi Tim CS Superadmin AkuAda."
            );

            session()->setFlashdata('error', 'Permohonan retur telah ditolak. Alasan penolakan telah diteruskan ke pihak Buyer.');
        }

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
            'ppn_percent'         => get_ppn_rate(),
            'vendor_fee_percent'  => $feePercent,
            'tkdn_percent'        => $tkdn,
            'is_sample_available' => $isSample,
            'image_url'           => $imageUrl,
            'status'              => 'active',
            'created_at'          => $now,
            'updated_at'          => $now,
        ]);

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

    public function updateCompanyProfile()
    {
        $vendorId = $this->getVendorCompanyId();
        $companyModel = new CompanyModel();

        $companyModel->update($vendorId, [
            'company_name'        => trim((string)$this->request->getPost('company_name')),
            'legal_type'          => trim((string)($this->request->getPost('legal_type') ?: 'PT')),
            'npwp'                => trim((string)$this->request->getPost('npwp')),
            'nib'                 => trim((string)$this->request->getPost('nib')),
            'address'             => trim((string)$this->request->getPost('address')),
            'city'                => trim((string)$this->request->getPost('city')),
            'province'            => trim((string)$this->request->getPost('province')),
            'pic_name'            => trim((string)$this->request->getPost('pic_name')),
            'pic_phone'           => trim((string)$this->request->getPost('pic_phone')),
            'phone'               => trim((string)$this->request->getPost('pic_phone')),
            'email'               => trim((string)$this->request->getPost('email')),
            'bank_name'           => trim((string)$this->request->getPost('bank_name')),
            'bank_account_number' => trim((string)$this->request->getPost('bank_account_number')),
            'bank_account_holder' => trim((string)$this->request->getPost('bank_account_holder')),
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Data profil legalitas, NPWP, dan rekening bank vendor berhasil diperbarui!');
        return redirect()->to('/vendor/company-profile');
    }

    public function settlements()
    {
        $vendorId = $this->getVendorCompanyId();
        $orderModel = new OrderModel();
        $companyModel = new CompanyModel();

        $company = $companyModel->find($vendorId);
        $orders = $orderModel->getDetailedOrders($vendorId, 'vendor');

        $totalUnpaidNet = 0;
        $totalOverdueNet = 0;
        $totalPaidNet = 0;
        $today = date('Y-m-d');

        foreach ($orders as $o) {
            $fee = (float)($o['total_vendor_fee'] ?? 0);
            $net = (float)($o['vendor_net_payout'] ?? ($o['subtotal'] - $fee)) + (float)($o['ppn_amount'] ?? 0);
            $payoutDue = $o['vendor_payout_due_date'] ?: date('Y-m-d', strtotime($o['created_at'] . ' +7 days'));
            if (($o['vendor_payout_status'] ?? 'unpaid') === 'paid') {
                $totalPaidNet += $net;
            } else {
                $totalUnpaidNet += $net;
                if ($payoutDue < $today) {
                    $totalOverdueNet += $net;
                }
            }
        }

        return view('vendor/settlements', [
            'title'           => 'Piutang Settlement & Pencairan Dana - Vendor AkuAda',
            'company'         => $company,
            'orders'          => $orders,
            'totalUnpaidNet'  => $totalUnpaidNet,
            'totalOverdueNet' => $totalOverdueNet,
            'totalPaidNet'    => $totalPaidNet,
        ]);
    }

    public function updateBankAccount()
    {
        $vendorId = $this->getVendorCompanyId();
        $companyModel = new CompanyModel();
        $companyModel->update($vendorId, [
            'bank_name'           => $this->request->getPost('bank_name'),
            'bank_account_number' => $this->request->getPost('bank_account_number'),
            'bank_account_holder' => $this->request->getPost('bank_account_holder'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Rekening bank pencairan dana settlement berhasil diperbarui!');
        return redirect()->to('/vendor/settlements');
    }

    public function chatCenter()
    {
        $vendorId = $this->getVendorCompanyId();
        $messageModel = new OrderMessageModel();
        $threads = $messageModel->getConversationsList('vendor', $vendorId);
        $buyers = (new \App\Models\CompanyModel())->where('type', 'buyer')->orderBy('company_name', 'ASC')->findAll();
        $orders = (new \App\Models\OrderModel())->where('vendor_company_id', $vendorId)->orderBy('id', 'DESC')->findAll(30);

        return view('vendor/chat', [
            'title'   => 'Pusat Pesan & Chat Pengadaan - Mitra Vendor AkuAda',
            'threads' => $threads,
            'buyers'  => $buyers,
            'orders'  => $orders,
        ]);
    }
}
