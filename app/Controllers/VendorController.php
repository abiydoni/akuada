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
use App\Models\OrderStatusLogModel;
use App\Libraries\ProductExcelHelper;

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
        $orderModel->ensureOrderDeliveryToken((int)$id);
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

        // Capture armada vendor / driver details jika diisi di formulir
        $driverName = trim((string)$this->request->getPost('driver_name'));
        $driverPhone = trim((string)$this->request->getPost('driver_phone'));
        $vehiclePlate = trim((string)$this->request->getPost('vehicle_plate'));
        $deliveryMethod = trim((string)$this->request->getPost('delivery_method'));
        $courierName = trim((string)$this->request->getPost('courier_name'));
        $trackingNumber = trim((string)$this->request->getPost('tracking_number'));
        $expeditionService = trim((string)$this->request->getPost('expedition_service'));

        if (!empty($deliveryMethod)) {
            $updateData['delivery_method'] = $deliveryMethod;
        }
        if (!empty($courierName) || $deliveryMethod === 'expedition') {
            $updateData['courier_name'] = $courierName;
        }
        if (!empty($trackingNumber) || $deliveryMethod === 'expedition') {
            $updateData['tracking_number'] = $trackingNumber;
        }
        if (!empty($expeditionService) || $deliveryMethod === 'expedition') {
            $updateData['expedition_service'] = $expeditionService;
        }
        if (!empty($driverName)) {
            $updateData['driver_name'] = $driverName;
        }
        if (!empty($driverPhone)) {
            $updateData['driver_phone'] = $driverPhone;
        }
        if (!empty($vehiclePlate)) {
            $updateData['vehicle_plate'] = $vehiclePlate;
        }

        if ($status === 'shipped') {
            if (empty($order['shipped_at'])) {
                $updateData['shipped_at'] = date('Y-m-d H:i:s');
            }
        }

        if ($status === 'delivered' && empty($order['delivered_at'])) {
            $updateData['delivered_at'] = date('Y-m-d H:i:s');
            if (!empty($driverName)) {
                $updateData['delivered_by_name'] = $driverName;
            } elseif (!empty($courierName)) {
                $updateData['delivered_by_name'] = $courierName;
            }
        }

        $orderModel = new OrderModel();
        $orderModel->ensureOrderDeliveryToken($orderId);
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
        $statusNotes = trim((string)$this->request->getPost('status_notes'));
        if (empty($statusNotes)) {
            if ($status === 'shipped' && ($deliveryMethod === 'expedition' || !empty($trackingNumber))) {
                $statusNotes = 'Pesanan dikirim via Ekspedisi ' . ($courierName ?: 'Ekspedisi') . (!empty($expeditionService) ? ' (' . $expeditionService . ')' : '') . (!empty($trackingNumber) ? ' dengan No. Resi: ' . $trackingNumber : '') . '.';
            } else {
                $statusNotes = 'Status pengiriman diperbarui oleh ' . (session('company_name') ?? 'Vendor') . '.';
            }
        }

        $logModel = new OrderStatusLogModel();
        $logModel->logStatus($orderId, $status, $statusTitle, $statusNotes, session('user_name') ?? 'Vendor Mitra');

        $msgModel = new OrderMessageModel();
        if ($status === 'shipped' && ($deliveryMethod === 'expedition' || !empty($trackingNumber))) {
            $msgModel->sendMessage(
                $orderId,
                (int)session('user_id'),
                session('user_name') ?? 'Vendor Logistik',
                'vendor',
                "🚚 Pesanan Anda telah dikirim via Ekspedisi " . ($courierName ?: 'Ekspedisi') . (!empty($expeditionService) ? " ({$expeditionService})" : "") . (!empty($trackingNumber) ? " dengan No. Resi: {$trackingNumber}" : "") . ". Anda dapat melacak posisi resi pengiriman langsung melalui detail pesanan."
            );
        } elseif ($status === 'delivered') {
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

    /**
     * Halaman Khusus Monitoring Pengiriman Armada & Pembagian Link HP Driver
     */
    public function deliveries()
    {
        $companyId = (int)session('company_id');
        $orderModel = new OrderModel();

        $builder = $orderModel->select('orders.*, 
            buyer.company_name as buyer_name, buyer.legal_type as buyer_legal_type, buyer.address as buyer_address, buyer.city as buyer_city, buyer.province as buyer_province, buyer.pic_name as buyer_pic, buyer.pic_phone as buyer_pic_phone')
                              ->join('companies as buyer', 'buyer.id = orders.customer_company_id', 'left')
                              ->where('orders.vendor_company_id', $companyId)
                              ->whereIn('orders.order_status', ['shipped', 'delivered', 'in_production', 'quality_control', 'packaging', 'po_approved', 'po_issued'])
                              ->orderBy('orders.id', 'DESC');

        $filterStatus = $this->request->getGet('status');
        if ($filterStatus === 'shipped') {
            $builder->where('orders.order_status', 'shipped');
        } elseif ($filterStatus === 'delivered') {
            $builder->where('orders.order_status', 'delivered');
        } elseif ($filterStatus === 'preparing') {
            $builder->whereIn('orders.order_status', ['in_production', 'quality_control', 'packaging', 'po_approved', 'po_issued']);
        }

        $deliveries = $builder->findAll();

        foreach ($deliveries as &$del) {
            $del['delivery_token'] = $orderModel->ensureOrderDeliveryToken((int)$del['id']);
            $del = recalculate_order_from_settings($del);
        }
        unset($del);

        // Hitung statistik pengiriman
        $allOrders = $orderModel->where('vendor_company_id', $companyId)->findAll();
        $totalActive = 0;
        $otwCount = 0;
        $arrivedCount = 0;
        $deliveredCount = 0;

        foreach ($allOrders as $o) {
            if (in_array($o['order_status'], ['shipped', 'in_production', 'quality_control', 'packaging', 'po_approved', 'po_issued'])) {
                $totalActive++;
            }
            if ($o['order_status'] === 'shipped') {
                $otwCount++;
            }
            if (($o['delivery_status_detail'] ?? '') === 'arrived') {
                $arrivedCount++;
            }
            if ($o['order_status'] === 'delivered') {
                $deliveredCount++;
            }
        }

        return view('vendor/deliveries', [
            'title'          => 'Monitoring Pengiriman Kurir Armada & Akses HP - Vendor AkuAda',
            'deliveries'     => $deliveries,
            'totalActive'    => $totalActive,
            'otwCount'       => $otwCount,
            'arrivedCount'   => $arrivedCount,
            'deliveredCount' => $deliveredCount,
            'currentFilter'  => $filterStatus,
        ]);
    }

    /**
     * Penetapan Driver & Kendaraan Armada Pengantaran Sendiri
     */
    public function assignDriver()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $companyId = (int)session('company_id');

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if (!$order || $order['vendor_company_id'] != $companyId) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan atau hak akses ditolak.');
            return redirect()->back();
        }

        $driverName = trim((string)$this->request->getPost('driver_name'));
        $driverPhone = trim((string)$this->request->getPost('driver_phone'));
        $vehiclePlate = trim((string)$this->request->getPost('vehicle_plate'));
        $deliveryMethod = trim((string)$this->request->getPost('delivery_method')) ?: 'vendor_fleet';
        $courierName = trim((string)$this->request->getPost('courier_name'));
        $trackingNumber = trim((string)$this->request->getPost('tracking_number'));
        $expeditionService = trim((string)$this->request->getPost('expedition_service'));

        $token = $orderModel->ensureOrderDeliveryToken($orderId);

        $updateFields = [
            'delivery_method'    => $deliveryMethod,
            'driver_name'        => $driverName,
            'driver_phone'       => $driverPhone,
            'vehicle_plate'      => $vehiclePlate,
            'courier_name'       => $courierName,
            'tracking_number'    => $trackingNumber,
            'expedition_service' => $expeditionService,
            'delivery_token'     => $token,
            'updated_at'         => date('Y-m-d H:i:s'),
        ];
        if (!empty($trackingNumber) && empty($order['shipped_at'])) {
            $updateFields['shipped_at'] = date('Y-m-d H:i:s');
        }

        $orderModel->update($orderId, $updateFields);

        $msg = ($deliveryMethod === 'expedition')
            ? ('Data pengiriman ekspedisi (' . ($courierName ?: 'Ekspedisi') . (!empty($trackingNumber) ? ' - Resi: ' . $trackingNumber : '') . ') untuk PO ' . ($order['po_number'] ?? $order['order_number']) . ' berhasil disimpan!')
            : ('Data driver armada untuk PO ' . ($order['po_number'] ?? $order['order_number']) . ' berhasil disimpan! Link HP kurir siap digunakan.');

        session()->setFlashdata('success', $msg);
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

            $logModel = new OrderStatusLogModel();
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

            $logModel = new OrderStatusLogModel();
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

            $logModel = new OrderStatusLogModel();
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
        $vendorId = $this->getVendorCompanyId();
        $format = strtolower((string)($this->request->getGet('format') ?? 'xlsx'));
        if ($format === 'csv') {
            $csvContent = ProductExcelHelper::generateCsvTemplate(false, $vendorId);
            $filename = 'template_import_produk_vendor_' . date('Ymd_His') . '.csv';

            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($csvContent));
            header('Cache-Control: max-age=0');
            header('Pragma: public');
            header('Expires: 0');

            echo $csvContent;
            exit;
        }

        $xlsxContent = ProductExcelHelper::generateTemplate(false, $vendorId);
        $filename = 'template_import_produk_vendor_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($xlsxContent));
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        header('Expires: 0');

        echo $xlsxContent;
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
            session()->setFlashdata('error', 'File tidak ditemukan atau gagal diunggah.');
            return redirect()->to('/vendor/products');
        }

        $extension = strtolower($file->getClientExtension());
        if (!in_array($extension, ['xlsx', 'csv', 'txt'])) {
            session()->setFlashdata('error', 'Format file harus berekstensi Excel (.xlsx) atau CSV (.csv).');
            return redirect()->to('/vendor/products');
        }

        $tempPath = $file->getTempName();
        $rows = ProductExcelHelper::parseUploadedFile($tempPath, $extension);

        if (empty($rows)) {
            session()->setFlashdata('error', 'File kosong atau tidak memiliki data yang dapat dibaca.');
            return redirect()->to('/vendor/products');
        }

        $headerRow = array_shift($rows);
        $headerMap = [];
        foreach ($headerRow as $colIdx => $colName) {
            $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '-', '(', ')', '/', '='], '_', $colName))));
            $headerMap[$cleanName] = $colIdx;
        }

        // Load existing categories
        $categoryModel = new CategoryModel();
        $allCategories = $categoryModel->findAll();
        $defaultCategory = !empty($allCategories) ? $allCategories[0] : null;

        $productModel = new ProductModel();
        $now = date('Y-m-d H:i:s');
        $importedCount = 0;
        $skippedCount = 0;

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($rows as $row) {
            if (empty(array_filter($row, fn($v) => trim((string)$v) !== ''))) {
                continue;
            }

            $getVal = function(array $aliases, $default = '') use ($headerMap, $row) {
                foreach ($aliases as $alias) {
                    if (isset($headerMap[$alias]) && isset($row[$headerMap[$alias]])) {
                        $val = trim((string)$row[$headerMap[$alias]]);
                        if ($val !== '') {
                            return $val;
                        }
                    }
                }
                return $default;
            };

            $name = $getVal(['nama_barang_wajib', 'nama_barang', 'name', 'nama']);
            if (empty($name)) {
                $skippedCount++;
                continue;
            }

            // Category Matching (mendukung ID Angka atau Teks)
            $rawCat = $getVal(['kategori_id_id_angka_teks', 'kategori_id_angka_teks', 'kategori_id', 'kategori', 'category', 'category_id', 'id_kategori']);
            $matchedCategoryId = ProductExcelHelper::resolveCategoryId($rawCat, $allCategories, $defaultCategory['id'] ?? 1);

            // Sanitasi Harga Regular
            $rawPrice = $getVal(['harga_b2b_regular_rp', 'harga_b2b', 'price_regular', 'harga'], '0');
            $cleanPrice = (float)str_replace(['Rp', 'rp', '.', ' '], '', str_replace(',', '.', $rawPrice));
            if ($cleanPrice <= 0) {
                $skippedCount++;
                continue;
            }

            // SKU & Barcode: Jika kosong otomatis dibuatkan sistem
            $rawSku = $getVal(['sku_kosong_otomatis', 'sku']);
            $sku = !empty($rawSku) ? $rawSku : ('SKU-VND-' . strtoupper(substr(md5(uniqid($name . microtime(), true)), 0, 8)));

            $rawBarcode = $getVal(['barcode_kosong_otomatis', 'barcode']);
            $barcode = !empty($rawBarcode) ? $rawBarcode : ('899' . str_pad((string)rand(1000000000, 9999999999), 10, '0', STR_PAD_LEFT));

            $brand = $getVal(['merk_brand', 'merk', 'brand'], 'Generic');

            // Satuan UOM (mendukung Kode Angka 1-12 atau Teks Pcs/Box dll)
            $rawUom = $getVal(['satuan_uom_kode_angka_teks', 'satuan_uom', 'uom', 'satuan']);
            $uom = ProductExcelHelper::resolveUom($rawUom);

            // Stok, MOQ, Berat, Lead Time dengan nilai default yang aman
            $rawStock = $getVal(['stok_kosong_100', 'stok', 'stock']);
            $stock = ($rawStock !== '' && is_numeric(str_replace(['.', ','], '', $rawStock))) ? max(0, (int)str_replace(['.', ','], '', $rawStock)) : 100;

            $rawMoq = $getVal(['moq_min_order_kosong_1', 'moq']);
            $moq = ($rawMoq !== '' && is_numeric(str_replace(['.', ','], '', $rawMoq))) ? max(1, (int)str_replace(['.', ','], '', $rawMoq)) : 1;

            $rawWeight = $getVal(['berat_gram_kosong_500', 'berat_gram', 'weight']);
            $weight = ($rawWeight !== '' && is_numeric(str_replace(['.', ','], '', $rawWeight))) ? max(1, (int)str_replace(['.', ','], '', $rawWeight)) : 500;

            $rawLeadTime = $getVal(['lead_time_hari_kosong_2', 'lead_time_hari', 'lead_time']);
            $leadTime = ($rawLeadTime !== '' && is_numeric(str_replace(['.', ','], '', $rawLeadTime))) ? max(1, (int)str_replace(['.', ','], '', $rawLeadTime)) : 2;

            $tkdn = (float)str_replace(',', '.', $getVal(['tkdn_persen_kosong_0', 'tkdn_persen', 'tkdn'], '0'));

            // PPN & Sample (1 = Ya, 0 = Tidak)
            $rawPpn = strtolower($getVal(['kena_ppn_1ya_0tidak', 'kena_ppn', 'is_taxable'], '1'));
            $isTaxable = in_array($rawPpn, ['0', 'tidak', 'no', 'false']) ? 0 : 1;

            $rawSample = strtolower($getVal(['bisa_sample_1ya_0tidak', 'bisa_sample', 'is_sample_available'], '1'));
            $isSample = in_array($rawSample, ['0', 'tidak', 'no', 'false']) ? 0 : 1;

            $desc = $getVal(['deskripsi_barang', 'deskripsi', 'description'], 'Pengadaan ' . $name . ' berkualitas untuk kebutuhan korporat.');
            $spec = $getVal(['spesifikasi_teknis', 'spesifikasi', 'spec_details'], '');
            $imageUrl = $getVal(['url_foto_barang', 'gambar_url', 'image_url'], 'https://images.unsplash.com/photo-1589330694653-dad6d3240e2b?auto=format&fit=crop&w=600&q=80');

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
                'is_sample_available' => $isSample,
                'image_url'           => $imageUrl,
                'status'              => 'active',
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);

            $importedCount++;
        }

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
        $orders = (new OrderModel())->where('vendor_company_id', $vendorId)->orderBy('id', 'DESC')->findAll(30);

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

        $logModel = new OrderStatusLogModel();
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

    public function uploadDesignMockup()
    {
        $vendorId = $this->getVendorCompanyId();
        $orderId = (int)$this->request->getPost('order_id');
        $mockupNotes = trim((string)$this->request->getPost('mockup_notes'));

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if (!$order || (int)$order['vendor_company_id'] !== (int)$vendorId) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan atau Anda tidak memiliki hak akses.');
            return redirect()->back();
        }

        $mockupFileVendor = $order['mockup_file_vendor'] ?? null;
        $file = $this->request->getFile('mockup_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = strtolower($file->getClientExtension());
            $allowedExts = ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'zip', 'rar', 'ai', 'cdr', 'psd', 'svg'];
            if (in_array($ext, $allowedExts)) {
                $uploadDir = FCPATH . 'uploads/designs';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
                $newFileName = 'vendor_mockup_' . $orderId . '_' . date('YmdHis') . '.' . $ext;
                $file->move($uploadDir, $newFileName);
                $mockupFileVendor = 'uploads/designs/' . $newFileName;
            }
        }

        if (empty($mockupFileVendor) && empty($mockupNotes)) {
            session()->setFlashdata('error', 'Silakan pilih file gambar/dokumen mockup atau isi catatan preview terlebih dahulu.');
            return redirect()->back();
        }

        $orderModel->update($orderId, [
            'mockup_file_vendor' => $mockupFileVendor,
            'mockup_notes'       => $mockupNotes ?: 'Mockup pra-produksi siap ditinjau oleh Tim AkuAda untuk diteruskan ke Buyer.',
            'design_status'      => 'mockup_uploaded',
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);

        $logModel = new OrderStatusLogModel();
        $logModel->logStatus(
            $orderId,
            $order['order_status'],
            'Upload Mockup Pra-Produksi oleh Vendor ke AkuAda',
            'Vendor telah mengunggah hasil preview/mockup desain ke Tim AkuAda untuk diverifikasi sebelum diteruskan ke pihak Buyer.',
            session('user_name') ?? 'Mitra Vendor'
        );

        // Kirim notifikasi ke ruang chat Vendor <-> AkuAda
        $msgModel = new OrderMessageModel();
        $msgModel->addMessage(
            $orderId,
            (int)session('user_id'),
            session('user_name') ?? 'Mitra Vendor',
            'vendor',
            session('company_name') ?? 'Mitra Vendor',
            "📐 [Mockup Pra-Produksi PO #{$order['po_number']}] Kami telah mengunggah file Mockup/Sample desain untuk diperiksa Tim AkuAda dan diteruskan ke pihak pemesan.\nCatatan: " . ($mockupNotes ?: '-'),
            $mockupFileVendor,
            'admin_comp_' . (int)$vendorId,
            (int)$vendorId,
            null,
            'admin',
            'Mockup PO #' . $order['po_number']
        );

        session()->setFlashdata('success', 'File Mockup Pra-Produksi berhasil dikirim ke Tim AkuAda untuk diteruskan ke Buyer!');
        return redirect()->back();
    }
}
