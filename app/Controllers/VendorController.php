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

        $companyModel = new CompanyModel();
        $company = $companyModel->find($order['vendor_company_id']);

        return view('vendor/order_detail', [
            'title'        => 'Detail Pesanan PO #' . ($order['po_number'] ?? $order['order_number']) . ' - Vendor AkuAda',
            'order'        => $order,
            'company'      => $company,
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

    public function downloadTemplateCsv()
    {
        $headers = [
            'kategori',
            'nama_barang',
            'sku',
            'barcode',
            'merk',
            'satuan_uom',
            'harga_b2b',
            'stok',
            'moq',
            'berat_gram',
            'lead_time_hari',
            'tkdn_persen',
            'kena_ppn',
            'deskripsi',
            'spesifikasi',
            'gambar_url'
        ];

        $sampleRows = [
            [
                'Alat Tulis Kantor & Kertas',
                'Kertas HVS PaperOne A4 80gr (Box isi 5 Rim)',
                'SKU-PPO-A480',
                '8992345678901',
                'PaperOne',
                'Box',
                '245000',
                '150',
                '2',
                '12500',
                '2',
                '85.5',
                '1',
                'Kertas HVS putih bersih ukuran A4 dengan gramatur 80 gsm premium untuk cetak dokumen resmi kantor.',
                'Ukuran: 210 x 297 mm; Ketebalan: 80 gsm; Kemasan: 1 Box isi 5 Rim @ 500 lembar (Total 2.500 lembar); Sertifikasi PEFC & ISO 9001',
                'https://images.unsplash.com/photo-1589330694653-dad6d3240e2b?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'Komputer & IT Infrastruktur',
                'Keyboard Wireless Logitech MK270 Combo Mouse Silent',
                'SKU-LOG-MK270',
                '8999876543210',
                'Logitech',
                'Unit',
                '360000',
                '50',
                '1',
                '800',
                '3',
                '40.0',
                '1',
                'Paket keyboard dan mouse wireless 2.4 GHz dengan baterai tahan lama dan koneksi stabil hingga 10 meter.',
                'Koneksi: USB Receiver 2.4GHz; Jangkauan: 10 meter; Baterai: 2x AAA (Keyboard), 1x AA (Mouse); Kompatibel: Windows 10/11 & macOS; Garansi resmi 1 tahun',
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=600&q=80'
            ],
            [
                'Perlengkapan K3, Safety & APD',
                'Helm Safety Proyek SNI MSA V-Gard Fastrac Putih',
                'SKU-MSA-VGARD-W',
                '8991122334455',
                'MSA',
                'Pcs',
                '125000',
                '80',
                '5',
                '450',
                '2',
                '78.0',
                '1',
                'Helm keselamatan kerja standar SNI dan ANSI dengan suspensi putar Fastrac untuk perlindungan benturan di area industri dan konstruksi.',
                'Material: Polietilen kepadatan tinggi (HDPE); Standar: SNI ISO 3873 & ANSI/ISEA Z89.1; Suspensi: 4 titik Fastrac putar; Warna: Putih',
                'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=600&q=80'
            ]
        ];

        $filename = 'template_import_produk_vendor_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        // UTF-8 BOM for Excel compatibility
        fputs($out, "\xEF\xBB\xBF");

        fputcsv($out, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }

    public function importCsv()
    {
        $vendorId = $this->getVendorCompanyId();
        $companyModel = new CompanyModel();
        $company = $companyModel->find($vendorId);
        $contractFee = (float)($company['commission_rate'] ?? get_default_vendor_fee_rate());

        $file = $this->request->getFile('csv_file');

        if (!$file || !$file->isValid()) {
            session()->setFlashdata('error', 'File CSV tidak ditemukan atau gagal diunggah.');
            return redirect()->to('/vendor/products');
        }

        $extension = strtolower($file->getClientExtension());
        if ($extension !== 'csv' && $extension !== 'txt') {
            session()->setFlashdata('error', 'Format file harus berekstensi .csv');
            return redirect()->to('/vendor/products');
        }

        $tempPath = $file->getTempName();
        $handle = fopen($tempPath, 'r');
        if (!$handle) {
            session()->setFlashdata('error', 'Gagal membaca file CSV yang diunggah.');
            return redirect()->to('/vendor/products');
        }

        // Auto-detect delimiter (, or ;)
        $firstLine = fgets($handle);
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
        rewind($handle);

        // Strip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle, 0, $delimiter);
        if (!$header) {
            fclose($handle);
            session()->setFlashdata('error', 'File CSV kosong atau tidak memiliki format header yang valid.');
            return redirect()->to('/vendor/products');
        }

        // Normalize header keys (lowercase, trimmed, strip non-alphanumeric except underscore)
        $headerMap = [];
        foreach ($header as $colIdx => $colName) {
            $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '-'], '_', $colName))));
            $headerMap[$cleanName] = $colIdx;
        }

        // Load existing categories for matching
        $categoryModel = new CategoryModel();
        $allCategories = $categoryModel->findAll();
        $defaultCategory = !empty($allCategories) ? $allCategories[0] : null;

        $productModel = new ProductModel();
        $now = date('Y-m-d H:i:s');
        $importedCount = 0;
        $skippedCount = 0;

        $db = \Config\Database::connect();
        $db->transStart();

        $rowNum = 1;
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNum++;

            // Check if empty row
            if (empty(array_filter($row, fn($v) => trim((string)$v) !== ''))) {
                continue;
            }

            $getVal = function($key, $default = '') use ($headerMap, $row) {
                if (isset($headerMap[$key]) && isset($row[$headerMap[$key]])) {
                    return trim((string)$row[$headerMap[$key]]);
                }
                return $default;
            };

            $name = $getVal('nama_barang') ?: $getVal('name') ?: $getVal('nama');
            if (empty($name)) {
                $skippedCount++;
                continue;
            }

            // Category Matching
            $categoryName = $getVal('kategori') ?: $getVal('category');
            $matchedCategoryId = null;

            if (!empty($categoryName)) {
                foreach ($allCategories as $cat) {
                    if (strcasecmp($cat['name'], $categoryName) === 0 || stripos($cat['name'], $categoryName) !== false || stripos($categoryName, $cat['name']) !== false) {
                        $matchedCategoryId = $cat['id'];
                        break;
                    }
                }
            }

            if (!$matchedCategoryId && $defaultCategory) {
                $matchedCategoryId = $defaultCategory['id'];
            }

            // Price sanitization (remove "Rp", dots, spaces)
            $rawPrice = $getVal('harga_b2b') ?: $getVal('price_regular') ?: $getVal('harga') ?: '0';
            $cleanPrice = (float)str_replace(['Rp', 'rp', '.', ' '], '', str_replace(',', '.', $rawPrice));

            if ($cleanPrice <= 0) {
                $skippedCount++;
                continue;
            }

            $sku = $getVal('sku') ?: 'SKU-' . strtoupper(substr(md5(uniqid($name, true)), 0, 8));
            $barcode = $getVal('barcode');
            $brand = $getVal('merk') ?: $getVal('brand');
            $uom = $getVal('satuan_uom') ?: $getVal('uom') ?: 'Pcs';
            $stock = max(0, (int)str_replace(['.', ','], '', $getVal('stok') ?: $getVal('stock') ?: '0'));
            $moq = max(1, (int)str_replace(['.', ','], '', $getVal('moq') ?: '1'));
            $weight = max(0, (int)str_replace(['.', ','], '', $getVal('berat_gram') ?: $getVal('weight') ?: '500'));
            $leadTime = max(1, (int)str_replace(['.', ','], '', $getVal('lead_time_hari') ?: $getVal('lead_time') ?: '2'));
            $tkdn = (float)str_replace(',', '.', $getVal('tkdn_persen') ?: $getVal('tkdn') ?: '0');
            $isTaxable = in_array(strtolower($getVal('kena_ppn') ?: $getVal('is_taxable') ?: '1'), ['1', 'ya', 'yes', 'true']) ? 1 : 0;
            $desc = $getVal('deskripsi') ?: $getVal('description') ?: ('Pengadaan ' . $name . ' berkualitas untuk kebutuhan korporat.');
            $spec = $getVal('spesifikasi') ?: $getVal('spec_details') ?: '';
            $imageUrl = $getVal('gambar_url') ?: $getVal('image_url') ?: 'https://images.unsplash.com/photo-1589330694653-dad6d3240e2b?auto=format&fit=crop&w=600&q=80';

            $slug = url_title($name, '-', true) . '-' . rand(1000, 9999);

            $productModel->insert([
                'vendor_company_id'   => $vendorId,
                'category_id'         => $matchedCategoryId,
                'name'                => $name,
                'slug'                => $slug,
                'sku'                 => $sku,
                'barcode'             => $barcode,
                'brand'               => $brand,
                'uom'                 => $uom,
                'description'         => $desc,
                'spec_details'        => $spec,
                'price_regular'       => $cleanPrice,
                'moq'                 => $moq,
                'stock'               => $stock,
                'weight_gram'         => $weight,
                'lead_time_days'      => $leadTime,
                'is_taxable'          => $isTaxable,
                'ppn_percent'         => get_ppn_rate(),
                'vendor_fee_percent'  => $contractFee,
                'tkdn_percent'        => $tkdn,
                'is_sample_available' => 0,
                'image_url'           => $imageUrl,
                'status'              => 'active',
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);

            $importedCount++;
        }

        fclose($handle);
        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Terjadi kesalahan pada database saat memproses import barang.');
        } else {
            $msg = "Berhasil mengimpor $importedCount master barang ke katalog B2B Anda!";
            if ($skippedCount > 0) {
                $msg .= " ($skippedCount baris dilewati karena format tidak sesuai/harga nol)";
            }
            session()->setFlashdata('success', $msg);
        }

        return redirect()->to('/vendor/products');
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
        // Sembunyikan semua buyer: Vendor hanya berkomunikasi terpusat dengan tim AkuAda
        $orders = (new \App\Models\OrderModel())->where('vendor_company_id', $vendorId)->orderBy('id', 'DESC')->findAll(30);

        return view('vendor/chat', [
            'title'   => 'Pusat Pesan & Chat - Mitra Vendor AkuAda',
            'threads' => $threads,
            'buyers'  => [],
            'orders'  => $orders,
        ]);
    }

    public function requestPayout()
    {
        $vendorId = $this->getVendorCompanyId();
        $orderId = (int)$this->request->getPost('order_id');

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if (!$order || (int)$order['vendor_company_id'] !== $vendorId) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan atau Anda tidak memiliki hak akses.');
            return redirect()->back();
        }

        if (($order['vendor_payout_status'] ?? 'unpaid') === 'paid') {
            session()->setFlashdata('info', 'Dana untuk pesanan ini telah dicairkan oleh AkuAda.');
            return redirect()->back();
        }

        $now = date('Y-m-d H:i:s');
        $fee = (float)($order['total_vendor_fee'] ?? 0);
        $net = (float)($order['vendor_net_payout'] ?? ($order['subtotal'] - $fee)) + (float)($order['ppn_amount'] ?? 0);

        $orderModel->update($orderId, [
            'vendor_payout_status' => 'pending_payout',
            'updated_at'           => $now,
        ]);

        $logModel = new \App\Models\OrderStatusLogModel();
        $logModel->logStatus(
            $orderId,
            $order['order_status'],
            'Pengajuan Pencairan Dana Settlement Vendor',
            'Vendor mengajukan permohonan pencairan dana penjualan senilai ' . format_rupiah($net) . ' ke rekening perusahaan. Menunggu transfer oleh Tim Finance AkuAda.',
            session('user_name') ?? 'Mitra Vendor'
        );

        session()->setFlashdata('success', 'Pengajuan pencairan dana sebesar ' . format_rupiah($net) . ' berhasil dikirimkan ke Tim Finance AkuAda!');
        return redirect()->back();
    }
}
