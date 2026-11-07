<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\RfqModel;
use App\Models\CompanyModel;
use App\Models\OrderReviewModel;
use App\Models\OrderMessageModel;
use App\Models\OrderReturnModel;

class CustomerController extends BaseController
{
    private function getBuyerCompanyId()
    {
        return ensure_role_session('customer');
    }

    public function dashboard()
    {
        $buyerId = $this->getBuyerCompanyId();
        $orderModel = new OrderModel();
        $rfqModel = new RfqModel();
        $companyModel = new CompanyModel();

        $company = $companyModel->find($buyerId);
        $orders = $orderModel->getDetailedOrders($buyerId, 'customer');
        $rfqs = $rfqModel->getDetailedRfqs($buyerId, 'customer');

        $totalSpending = 0;
        $activeOrders = 0;
        foreach ($orders as $o) {
            $totalSpending += (float)$o['total_amount'];
            if (in_array($o['order_status'], ['processing', 'shipped', 'delivered', 'pending_po_approval'])) {
                $activeOrders++;
            }
        }

        return view('customer/dashboard', [
            'title'         => 'Dashboard Pengadaan Korporat - AkuAda B2B',
            'company'       => $company,
            'orders'        => $orders,
            'rfqs'          => $rfqs,
            'totalSpending' => $totalSpending,
            'activeOrders'  => $activeOrders,
            'recentOrders'  => array_slice($orders, 0, 5),
        ]);
    }

    public function orders()
    {
        $buyerId = $this->getBuyerCompanyId();
        $orderModel = new OrderModel();
        $orders = $orderModel->getDetailedOrders($buyerId, 'customer');

        return view('customer/orders', [
            'title'  => 'Daftar Purchase Order (PO) & BAST - AkuAda B2B',
            'orders' => $orders
        ]);
    }

    public function orderDetail($id)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Purchase Order tidak ditemukan');
        }

        $reviewModel = new OrderReviewModel();
        $review = $reviewModel->getByOrder((int)$id);

        $returnModel = new OrderReturnModel();
        $returns = $returnModel->getReturnsByOrder((int)$id);
        $latestReturn = !empty($returns) ? $returns[0] : null;

        return view('customer/order_detail', [
            'title'        => 'Detail PO ' . ($order['po_number'] ?? $order['order_number']) . ' - AkuAda B2B',
            'order'        => $order,
            'review'       => $review,
            'returns'      => $returns,
            'latestReturn' => $latestReturn,
        ]);
    }

    public function invoice($id)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice tidak ditemukan');
        }

        $companyId = session('company_id');
        if (session('user_role') === 'customer' && $companyId && $order['customer_company_id'] != $companyId) {
            session()->setFlashdata('error', 'Anda tidak memiliki hak akses melihat invoice ini.');
            return redirect()->to(base_url('customer/orders'));
        }

        return view('customer/invoice', [
            'title' => 'Invoice ' . ($order['invoice_number'] ?? $order['po_number']) . ' - AkuAda',
            'order' => $order,
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
            'title' => 'Berita Acara Serah Terima (BAST) ' . ($order['bast_number'] ?? $order['po_number']) . ' - AkuAda',
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
            'title' => 'e-Faktur Pajak PPN ' . ($order['tax_invoice_number'] ?? $order['po_number']) . ' - AkuAda',
            'order' => $order,
        ]);
    }

    public function completeOrder()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $signerName = trim((string)$this->request->getPost('signer_name')) ?: (session('user_name') ?? 'PIC Pengadaan Buyer');
        $notes = trim((string)$this->request->getPost('completion_notes')) ?: 'Barang telah dicek dan diterima lengkap dalam kondisi baik. Pesanan resmi dinyatakan selesai.';

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);
        if (!$order) {
            session()->setFlashdata('error', 'Pesanan PO tidak ditemukan.');
            return redirect()->back();
        }

        $buyerId = $this->getBuyerCompanyId();
        if ((int)$order['customer_company_id'] !== (int)$buyerId && session('role') !== 'admin') {
            session()->setFlashdata('error', 'Akses penyelesaian pesanan tidak diizinkan.');
            return redirect()->back();
        }

        if (in_array($order['order_status'], ['completed', 'bast_signed'])) {
            session()->setFlashdata('error', 'Pesanan telah diselesaikan sebelumnya dan status telah terkunci.');
            return redirect()->back();
        }

        $updateData = [
            'order_status'      => 'completed',
            'delivered_by_name' => $signerName,
            'delivery_notes'    => $notes,
            'updated_at'        => date('Y-m-d H:i:s')
        ];
        if (empty($order['delivered_at'])) {
            $updateData['delivered_at'] = date('Y-m-d H:i:s');
        }

        $orderModel->update($orderId, $updateData);

        $logModel = new \App\Models\OrderStatusLogModel();
        $logModel->logStatus(
            $orderId, 
            'completed', 
            'Pesanan Dinyatakan Selesai oleh Buyer', 
            "Seluruh barang telah diperiksa dan pesanan resmi dikonfirmasi SELESAI oleh {$signerName}. Catatan: {$notes}", 
            $signerName
        );

        // Notifikasi ke PO Live Chat
        $msgModel = new \App\Models\OrderMessageModel();
        $msgModel->sendMessage(
            $orderId,
            (int)session('user_id'),
            $signerName,
            'customer',
            "✅ Pesanan PO resmi dikonfirmasi SELESAI oleh {$signerName}. Seluruh barang telah diterima dalam kondisi baik. Dokumen BAST sah.",
            session('company_name') ?? 'Corporate Buyer'
        );

        session()->setFlashdata('success', 'Pesanan berhasil diselesaikan oleh ' . $signerName . '! Terima kasih telah bertransaksi. Silakan berikan penilaian bintang untuk mitra vendor.');
        return redirect()->back();
    }

    public function signBast()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $signerName = $this->request->getPost('signer_name') ?: session('user_name');

        $orderModel = new OrderModel();
        $orderModel->update($orderId, [
            'order_status' => 'completed',
            'updated_at'   => date('Y-m-d H:i:s')
        ]);

        $logModel = new \App\Models\OrderStatusLogModel();
        $logModel->logStatus(
            $orderId, 
            'completed', 
            'Pengesahan BAST & Pesanan Selesai', 
            'Barang telah diterima lengkap di gudang dan dokumen BAST disahkan secara digital oleh ' . $signerName . ' (Senior Procurement Manager). Pesanan selesai.', 
            $signerName
        );

        session()->setFlashdata('success', 'Berita Acara Serah Terima (BAST) berhasil disahkan oleh ' . $signerName . ' dan pesanan resmi SELESAI! Periode jatuh tempo Term of Payment berjalan.');
        return redirect()->back();
    }

    public function confirmDelivery()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $receiverName = trim((string)$this->request->getPost('receiver_name')) ?: (session('user_name') ?? 'Penerima Logistik');
        $deliveryNotes = trim((string)$this->request->getPost('delivery_notes')) ?: 'Barang fisik telah sampai di gudang/kantor pemesan dalam kondisi baik dan lengkap sesuai nomor PO.';

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);
        if (!$order) {
            session()->setFlashdata('error', 'Pesanan PO tidak ditemukan.');
            return redirect()->back();
        }

        $buyerId = $this->getBuyerCompanyId();
        if ((int)$order['customer_company_id'] !== (int)$buyerId && session('role') !== 'admin') {
            session()->setFlashdata('error', 'Akses konfirmasi penerimaan barang tidak diizinkan.');
            return redirect()->back();
        }

        if (in_array($order['order_status'], ['delivered', 'bast_signed', 'completed'])) {
            session()->setFlashdata('error', 'Barang pesanan telah dikonfirmasi diterima sebelumnya dan status telah terkunci.');
            return redirect()->back();
        }

        $orderModel->update($orderId, [
            'order_status'      => 'delivered',
            'delivered_at'      => date('Y-m-d H:i:s'),
            'delivered_by_name' => $receiverName,
            'delivery_notes'    => $deliveryNotes,
            'updated_at'        => date('Y-m-d H:i:s')
        ]);

        $logModel = new \App\Models\OrderStatusLogModel();
        $logModel->logStatus(
            $orderId, 
            'delivered', 
            'Barang Tiba & Diterima Pembeli (Buyer)', 
            "Barang telah sampai dan dikonfirmasi diterima oleh {$receiverName}. Catatan penerimaan: {$deliveryNotes}", 
            $receiverName
        );

        session()->setFlashdata('success', 'Konfirmasi penerimaan barang berhasil disimpan! Barang telah diterima oleh ' . $receiverName . '. Silakan sahkan dokumen BAST dan berikan Penilaian Bintang untuk Vendor.');
        return redirect()->back();
    }

    public function submitReview()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $rating = max(1, min(5, (int)$this->request->getPost('rating')));
        $ratingQuality = max(1, min(5, (int)($this->request->getPost('rating_quality') ?: 5)));
        $ratingDelivery = max(1, min(5, (int)($this->request->getPost('rating_delivery') ?: 5)));
        $ratingService = max(1, min(5, (int)($this->request->getPost('rating_service') ?: 5)));
        $reviewTitle = trim((string)$this->request->getPost('review_title'));
        $reviewText = trim((string)$this->request->getPost('review_text'));

        if (empty($reviewText)) {
            session()->setFlashdata('error', 'Mohon tuliskan ulasan pengalaman Anda terhadap produk dan pelayanan vendor.');
            return redirect()->back();
        }

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);
        if (!$order) {
            session()->setFlashdata('error', 'Pesanan PO tidak ditemukan.');
            return redirect()->back();
        }

        $buyerId = $this->getBuyerCompanyId();
        if ((int)$order['customer_company_id'] !== (int)$buyerId && session('role') !== 'admin') {
            session()->setFlashdata('error', 'Akses pengisian ulasan tidak diizinkan.');
            return redirect()->back();
        }

        $reviewModel = new OrderReviewModel();
        $existing = $reviewModel->getByOrder($orderId);
        $buyerName = session('user_name') ?: 'Procurement Team';

        if ($existing) {
            $reviewModel->update($existing['id'], [
                'rating'          => $rating,
                'rating_quality'  => $ratingQuality,
                'rating_delivery' => $ratingDelivery,
                'rating_service'  => $ratingService,
                'review_title'    => $reviewTitle ?: ('Ulasan PO #' . ($order['po_number'] ?? $order['order_number'])),
                'review_text'     => $reviewText,
                'buyer_name'      => $buyerName,
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
        } else {
            $reviewModel->insert([
                'order_id'            => $orderId,
                'customer_company_id' => (int)$order['customer_company_id'],
                'vendor_company_id'   => (int)$order['vendor_company_id'],
                'user_id'             => session('user_id'),
                'buyer_name'          => $buyerName,
                'rating'              => $rating,
                'rating_quality'      => $ratingQuality,
                'rating_delivery'     => $ratingDelivery,
                'rating_service'      => $ratingService,
                'review_title'        => $reviewTitle ?: ('Ulasan PO #' . ($order['po_number'] ?? $order['order_number'])),
                'review_text'         => $reviewText,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s'),
            ]);
            $orderModel->update($orderId, ['has_review' => 1]);
        }

        session()->setFlashdata('success', 'Terima kasih atas penilaian ' . str_repeat('⭐', $rating) . ' dan ulasan Anda untuk mitra vendor!');
        return redirect()->back();
    }

    public function requestReturn()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $reasonCategory = $this->request->getPost('reason_category') ?: 'damaged';
        $resolutionRequested = $this->request->getPost('resolution_requested') ?: 'replacement';
        $itemDesc = trim((string)$this->request->getPost('item_description'));
        $damageDesc = trim((string)$this->request->getPost('damage_description'));

        if (empty($damageDesc)) {
            session()->setFlashdata('error', 'Mohon isi kronologi atau rincian kerusakan fisik barang.');
            return redirect()->back();
        }

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);
        if (!$order) {
            session()->setFlashdata('error', 'Pesanan PO tidak ditemukan.');
            return redirect()->back();
        }

        $buyerId = $this->getBuyerCompanyId();
        if ((int)$order['customer_company_id'] !== (int)$buyerId && session('role') !== 'admin') {
            session()->setFlashdata('error', 'Akses klaim retur tidak diizinkan.');
            return redirect()->back();
        }

        // Handle evidence photo upload
        $photoUrl = null;
        $file = $this->request->getFile('evidence_photo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = 'return_' . $orderId . '_' . time() . '.' . $file->getExtension();
            $uploadPath = FCPATH . 'uploads/returns';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $file->move($uploadPath, $newName);
            $photoUrl = base_url('uploads/returns/' . $newName);
        } else {
            $photoUrl = $this->request->getPost('evidence_photo_url') ?: null;
        }

        $returnModel = new OrderReturnModel();
        $returnNumber = $returnModel->generateReturnNumber($orderId);

        $returnModel->insert([
            'order_id'             => $orderId,
            'return_number'        => $returnNumber,
            'customer_company_id'  => (int)$order['customer_company_id'],
            'vendor_company_id'    => (int)$order['vendor_company_id'],
            'user_id'              => session('user_id'),
            'requester_name'       => session('user_name') ?: 'Procurement Team',
            'reason_category'      => $reasonCategory,
            'resolution_requested' => $resolutionRequested,
            'item_description'     => $itemDesc,
            'damage_description'   => $damageDesc,
            'evidence_photo'       => $photoUrl,
            'status'               => 'pending',
            'created_at'           => date('Y-m-d H:i:s'),
            'updated_at'           => date('Y-m-d H:i:s'),
        ]);

        $reasonLabels = [
            'damaged'    => 'Barang Rusak / Pecah saat Pengiriman',
            'defective'  => 'Cacat Produksi / Tidak Berfungsi',
            'wrong_item' => 'Barang / Spesifikasi Tidak Sesuai PO',
            'incomplete' => 'Kuantitas Kurang / Komponen Hilang',
            'expired'    => 'Melewati Masa Kadaluarsa (Expired)',
            'other'      => 'Alasan Khusus Lainnya',
        ];
        $reasonLabel = $reasonLabels[$reasonCategory] ?? $reasonCategory;

        $resLabels = [
            'replacement'   => 'Kirim Barang Pengganti Baru',
            'refund_credit' => 'Potong Tagihan TOP / Pengembalian Dana (Credit Note)',
        ];
        $resLabel = $resLabels[$resolutionRequested] ?? $resolutionRequested;

        $orderModel->update($orderId, [
            'order_status' => 'return_requested',
            'updated_at'   => date('Y-m-d H:i:s')
        ]);

        $logModel = new \App\Models\OrderStatusLogModel();
        $logModel->logStatus(
            $orderId,
            'return_requested',
            'Pengajuan Klaim Retur & Komplain Barang Rusak',
            "Buyer mengajukan komplain retur No: {$returnNumber}. Alasan: {$reasonLabel}. Solusi diminta: {$resLabel}. Rincian: {$damageDesc}",
            session('user_name') ?? 'Corporate Buyer'
        );

        // Auto post message into Live Chat PO
        $messageModel = new OrderMessageModel();
        $messageModel->addMessage(
            $orderId,
            session('user_id'),
            session('user_name') ?: 'Corporate Buyer',
            'customer',
            session('company_name') ?: 'Corporate Buyer',
            "⚠️ [KLAIM RETUR / BARANG RUSAK DILAPORKAN]\nNo. Pengajuan: {$returnNumber}\nKategori Masalah: {$reasonLabel}\nSolusi yang Diminta: {$resLabel}\nPenjelasan: {$damageDesc}\nMohon pihak vendor segera meninjau permohonan ini."
        );

        session()->setFlashdata('success', 'Permohonan klaim retur (' . $returnNumber . ') berhasil diajukan! Notifikasi resmi telah dikirimkan ke pihak Vendor dan Admin Platform AkuAda.');
        return redirect()->back();
    }

    public function rfqs()
    {
        $buyerId = $this->getBuyerCompanyId();
        $rfqModel = new RfqModel();
        $rfqs = $rfqModel->getDetailedRfqs($buyerId, 'customer');

        return view('customer/rfqs', [
            'title' => 'Permintaan Penawaran (RFQ) Saya - AkuAda B2B',
            'rfqs'  => $rfqs
        ]);
    }

    public function acceptQuote($rfqId)
    {
        $rfqModel = new RfqModel();
        $rfq = $rfqModel->getRfqWithItems($rfqId);

        if (!$rfq) {
            session()->setFlashdata('error', 'Dokumen RFQ tidak ditemukan.');
            return redirect()->back();
        }

        // Check if PO was already generated for this RFQ
        if (preg_match('/\[PO-ID:(\d+)\]/', (string)($rfq['notes'] ?? ''), $m)) {
            session()->setFlashdata('success', 'Penawaran RFQ ini sudah diterbitkan menjadi Purchase Order.');
            return redirect()->to('/customer/orders/' . (int)$m[1]);
        }

        $buyerId = (int)($rfq['customer_company_id'] ?: $this->getBuyerCompanyId());
        $companyModel = new CompanyModel();
        $buyer = $companyModel->find($buyerId);

        $vendorId = (int)($rfq['vendor_company_id'] ?: 0);
        if ($vendorId <= 0) {
            $firstVendor = $companyModel->where('type', 'vendor')->first();
            $vendorId = (int)($firstVendor['id'] ?? 1);
        }
        $vendor = $companyModel->find($vendorId);
        $vendorCommissionRate = (float)($vendor['commission_rate'] ?? get_default_vendor_fee_rate());
        $ppnRate = get_ppn_rate();

        $subtotal = 0.0;
        $totalVendorFee = 0.0;
        $totalVendorNet = 0.0;
        $totalPpn = 0.0;
        $orderItemsPayload = [];

        foreach (($rfq['items'] ?? []) as $it) {
            $qty = max(1, (int)$it['qty']);
            $unitPrice = (float)($it['vendor_offered_price'] ?: $it['target_unit_price'] ?: 0);
            $lineSubtotal = round($unitPrice * $qty, 2);
            $lineFee = round($lineSubtotal * ($vendorCommissionRate / 100), 2);
            $lineNet = round($lineSubtotal - $lineFee, 2);
            $linePpn = round($lineSubtotal * ($ppnRate / 100), 2);

            $subtotal += $lineSubtotal;
            $totalVendorFee += $lineFee;
            $totalVendorNet += $lineNet;
            $totalPpn += $linePpn;

            $orderItemsPayload[] = [
                'product_id'         => $it['product_id'] ?: null,
                'product_name'       => $it['item_name'],
                'sku'                => 'RFQ-' . $rfqId . '-' . rand(100, 999),
                'uom'                => $it['uom'] ?: 'Unit',
                'qty'                => $qty,
                'unit_price'         => $unitPrice,
                'vendor_fee_percent' => $vendorCommissionRate,
                'vendor_fee_amount'  => $lineFee,
                'vendor_net_amount'  => $lineNet,
                'ppn_amount'         => $linePpn,
                'subtotal'           => $lineSubtotal,
            ];
        }

        $totalAmount = round($subtotal + $totalPpn, 2);
        $now = date('Y-m-d H:i:s');
        $seq = str_pad((string)rand(100, 999), 3, '0', STR_PAD_LEFT);
        $poNumber = 'PO/RFQ/' . date('Ym') . '/' . $seq;
        $orderNumber = 'ORD-' . date('Ymd') . '-' . $seq;
        $invNumber = 'INV/AKUADA/' . date('Ym') . '/' . $seq;
        $vInvNumber = 'VINV/VENDOR/' . date('Ym') . '/' . $seq;
        $bastNumber = 'BAST/AKUADA/' . date('Ym') . '/' . $seq;
        $taxInvNumber = '010.002-' . date('y') . '.' . rand(10000000, 99999999);

        // Check legal verification status
        $isVerifiedBuyer = ((int)($buyer['is_verified'] ?? 0) === 1);

        // Credit evaluation (Hanya berlaku untuk buyer yang sudah diverifikasi legalitasnya)
        $limit = (float)($buyer['credit_limit_top'] ?? 0);
        $used = (float)($buyer['credit_used_top'] ?? 0);
        $available = max(0, $limit - $used);
        $isOverlimit = $isVerifiedBuyer ? ($totalAmount > $available) : false;
        $shortfall = max(0, $totalAmount - $available);

        if ($isVerifiedBuyer) {
            $paymentMethod = 'top_30';
            $paymentStatus = $isOverlimit ? 'pending_approval' : 'unpaid';
            $dueDate = date('Y-m-d', strtotime('+30 days'));
            $poNotes = 'Diterbitkan otomatis dari persetujuan Tender RFQ ' . $rfq['rfq_number'] . ' (' . $rfq['title'] . ').';
            if ($isOverlimit) {
                $poNotes = '[OVER-LIMIT REQUEST: Kekurangan Plafon ' . format_rupiah($shortfall) . ' | Usulan Limit Baru: ' . format_rupiah($used + $totalAmount) . '] ' . $poNotes;
            }
        } else {
            // Buyer belum diverifikasi legalitasnya: Tetap bisa transaksi, tapi WAJIB Bank Virtual Account (Non-Kredit)
            $paymentMethod = 'bank_va';
            $paymentStatus = 'unpaid';
            $dueDate = date('Y-m-d', strtotime('+3 days'));
            $poNotes = 'Diterbitkan otomatis dari persetujuan Tender RFQ ' . $rfq['rfq_number'] . ' (' . $rfq['title'] . ') dengan metode pembayaran Bank Virtual Account (Legalitas Perusahaan Menunggu Verifikasi Admin / Non-Kredit).';
        }

        $orderModel = new OrderModel();
        $orderId = (int)$orderModel->insert([
            'order_number'           => $orderNumber,
            'po_number'              => $poNumber,
            'customer_company_id'    => $buyerId,
            'vendor_company_id'      => $vendorId,
            'subtotal'               => $subtotal,
            'ppn_amount'             => $totalPpn,
            'total_vendor_fee'       => $totalVendorFee,
            'vendor_net_payout'      => $totalVendorNet,
            'shipping_amount'        => 0,
            'total_amount'           => $totalAmount,
            'payment_method'         => $paymentMethod,
            'payment_status'         => $paymentStatus,
            'order_status'           => 'po_issued',
            'tax_invoice_required'   => 1,
            'customer_notes'         => $poNotes,
            'shipping_address'       => $buyer['address'] ?? 'Gudang Penerimaan Buyer',
            'bast_number'            => $bastNumber,
            'invoice_number'         => $invNumber,
            'vendor_invoice_number'  => $vInvNumber,
            'tax_invoice_number'     => $taxInvNumber,
            'due_date'               => $dueDate,
            'vendor_payout_status'   => 'unpaid',
            'vendor_payout_due_date' => date('Y-m-d', strtotime('+7 days')),
            'created_at'             => $now,
            'updated_at'             => $now,
        ]);

        $orderItemModel = new \App\Models\OrderItemModel();
        foreach ($orderItemsPayload as $itemRow) {
            $itemRow['order_id'] = $orderId;
            $itemRow['created_at'] = $now;
            $itemRow['updated_at'] = $now;
            $orderItemModel->insert($itemRow);
        }

        // Update buyer credit used ONLY if verified and within credit limit
        if ($isVerifiedBuyer && !$isOverlimit && $buyer) {
            $newUsed = $used + $totalAmount;
            $companyModel->update($buyerId, ['credit_used_top' => $newUsed]);
            if (session('company_id') == $buyerId) {
                session()->set('credit_used_top', $newUsed);
            }
        }

        $logMethodText = $isVerifiedBuyer ? 'Fasilitas Kredit TOP 30 Hari' : 'Bank Virtual Account (Non-Kredit / Menunggu Verifikasi Legalitas)';
        $logModel = new \App\Models\OrderStatusLogModel();
        $logModel->logStatus(
            $orderId,
            'po_issued',
            'Purchase Order Diterbitkan dari Konversi RFQ ' . $rfq['rfq_number'],
            'Penawaran harga vendor pada RFQ ' . $rfq['rfq_number'] . ' telah disetujui oleh Buyer dan dikonversi otomatis menjadi Purchase Order resmi ' . $poNumber . ' senilai ' . format_rupiah($totalAmount) . ' dengan metode ' . $logMethodText . '.',
            session('user_name') ?? ($buyer['pic_name'] ?? 'Corporate Buyer')
        );

        $updatedNotes = trim((string)($rfq['notes'] ?? '') . ' [PO-ID:' . $orderId . '|PO-NO:' . $poNumber . ']');
        $rfqModel->update($rfqId, [
            'status'         => 'approved',
            'total_estimate' => $totalAmount,
            'notes'          => $updatedNotes,
            'updated_at'     => $now,
        ]);

        \App\Models\CoaModel::syncOrdersToJournals();

        $successMsg = $isVerifiedBuyer
            ? 'Penawaran RFQ ' . $rfq['rfq_number'] . ' berhasil disetujui & Purchase Order resmi ' . $poNumber . ' telah diterbitkan dengan fasilitas kredit TOP 30!'
            : 'Penawaran RFQ ' . $rfq['rfq_number'] . ' berhasil disetujui & Purchase Order resmi ' . $poNumber . ' telah diterbitkan! Karena legalitas perusahaan masih dalam proses verifikasi, pembayaran menggunakan Corporate Virtual Account (Non-Kredit).';
        session()->setFlashdata('success', $successMsg);
        return redirect()->to('/customer/orders/' . $orderId);
    }

    public function taxInvoices()
    {
        $buyerId = $this->getBuyerCompanyId();
        $orderModel = new OrderModel();
        $orders = $orderModel->where('customer_company_id', $buyerId)
                             ->where('tax_invoice_required', 1)
                             ->orderBy('id', 'DESC')
                             ->findAll();

        return view('customer/tax_invoices', [
            'title'  => 'e-Faktur Pajak PPN ' . format_tax_rate() . ' Resmi - AkuAda B2B',
            'orders' => array_map('recalculate_order_from_settings', $orders)
        ]);
    }

    public function cashback()
    {
        $buyerId = $this->getBuyerCompanyId();
        $companyModel = new CompanyModel();
        $company = $companyModel->find($buyerId);

        $withdrawalModel = new \App\Models\BuyerWithdrawalModel();
        $withdrawals = $withdrawalModel->getWithdrawalsWithCompany($buyerId);

        $mutationModel = new \App\Models\BuyerCashbackMutationModel();
        $mutations = $mutationModel->getCompanyMutations($buyerId);

        $cashbackBalance = (float)($company['cashback_balance'] ?? 0);
        $totalEarned = 0;
        $totalWithdrawn = 0;
        $pendingWithdrawal = 0;

        foreach ($mutations as $m) {
            if ($m['type'] === 'credit') {
                $totalEarned += (float)$m['amount'];
            }
        }
        foreach ($withdrawals as $w) {
            if ($w['status'] === 'completed') {
                $totalWithdrawn += (float)$w['amount'];
            } elseif ($w['status'] === 'pending') {
                $pendingWithdrawal += (float)$w['amount'];
            }
        }

        $cashbackRate = get_buyer_cashback_rate($buyerCompanyId);

        return view('customer/cashback', [
            'title'             => 'Saldo Cashback & Penarikan Dana - AkuAda B2B',
            'company'           => $company,
            'cashbackBalance'   => $cashbackBalance,
            'totalEarned'       => $totalEarned,
            'totalWithdrawn'    => $totalWithdrawn,
            'pendingWithdrawal' => $pendingWithdrawal,
            'withdrawals'       => $withdrawals,
            'mutations'         => $mutations,
            'cashbackRate'      => $cashbackRate,
        ]);
    }

    public function requestWithdrawal()
    {
        $buyerId = $this->getBuyerCompanyId();
        $companyModel = new CompanyModel();
        $company = $companyModel->find($buyerId);

        // Aturan Keras: Kecuali buyer yang tidak terdaftar, mereka tidak akan menerima apa-apa
        if (!$company || (int)($company['is_verified'] ?? 0) !== 1) {
            session()->setFlashdata('error', 'Fasilitas saldo cashback dan penarikan dana hanya tersedia untuk akun perusahaan pembeli resmi yang telah terdaftar dan diverifikasi oleh AkuAda.');
            return redirect()->to('/customer/cashback');
        }

        $amount = (float)$this->request->getPost('amount');
        $currentBalance = (float)($company['cashback_balance'] ?? 0);

        if ($amount <= 0) {
            session()->setFlashdata('error', 'Nominal penarikan harus lebih besar dari Rp 0.');
            return redirect()->to('/customer/cashback');
        }

        if ($amount > $currentBalance) {
            session()->setFlashdata('error', 'Nominal penarikan (' . format_rupiah($amount) . ') melebihi saldo cashback Anda saat ini (' . format_rupiah($currentBalance) . ').');
            return redirect()->to('/customer/cashback');
        }

        $bankName = trim((string)$this->request->getPost('bank_name'));
        $accountNumber = trim((string)$this->request->getPost('bank_account_number'));
        $accountHolder = trim((string)$this->request->getPost('bank_account_holder'));
        $notes = trim((string)$this->request->getPost('buyer_notes'));

        if (empty($bankName) || empty($accountNumber) || empty($accountHolder)) {
            session()->setFlashdata('error', 'Harap lengkapi informasi rekening bank tujuan pencairan.');
            return redirect()->to('/customer/cashback');
        }

        // Simpan sebagai default bank company jika sebelumnya belum ada
        if (empty($company['bank_name']) || empty($company['bank_account_number'])) {
            $companyModel->update($buyerId, [
                'bank_name'           => $bankName,
                'bank_account_number' => $accountNumber,
                'bank_account_holder' => $accountHolder,
            ]);
        }

        $withdrawalNumber = 'WD-' . date('Ym') . '-' . sprintf('%04d', rand(100, 9999));
        $withdrawalModel = new \App\Models\BuyerWithdrawalModel();
        $withdrawalModel->insert([
            'withdrawal_number'   => $withdrawalNumber,
            'company_id'          => $buyerId,
            'user_id'             => session('user_id'),
            'amount'              => $amount,
            'bank_name'           => $bankName,
            'bank_account_number' => $accountNumber,
            'bank_account_holder' => $accountHolder,
            'status'              => 'pending',
            'buyer_notes'         => $notes,
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Permintaan penarikan saldo cashback #' . $withdrawalNumber . ' sebesar ' . format_rupiah($amount) . ' berhasil diajukan! Finance AkuAda akan segera memproses transfer ke rekening ' . $bankName . ' (' . $accountNumber . ' a/n ' . $accountHolder . ').');
        return redirect()->to('/customer/cashback');
    }

    public function creditLimit()
    {
        return redirect()->to('/customer/cashback');
    }

    public function requestLimitIncrease()
    {
        $buyerId = $this->getBuyerCompanyId();
        $requestedLimit = (float)$this->request->getPost('requested_limit');
        $preferredTop = (int)($this->request->getPost('preferred_top') ?: 30);
        $notes = trim((string)$this->request->getPost('notes'));

        $payload = [
            'company_id'      => $buyerId,
            'requested_limit' => $requestedLimit,
            'preferred_top'   => $preferredTop,
            'notes'           => $notes ?: 'Pengajuan peningkatan kapasitas pengadaan korporat B2B.',
            'requested_by'    => session('user_name') ?? 'PIC Procurement Buyer',
            'requested_at'    => date('Y-m-d H:i:s'),
            'status'          => 'pending',
        ];

        $settingModel = new \App\Models\SystemSettingModel();
        $settingModel->setVal('credit_req_company_' . $buyerId, json_encode($payload), 'credit');
        clear_system_settings_cache();

        session()->setFlashdata('success', 'Pengajuan kenaikan plafon kredit TOP sebesar ' . format_rupiah($requestedLimit) . ' telah dikirim ke Superadmin & Tim Credit Risk Analyst AkuAda!');
        return redirect()->to('/customer/credit-limit');
    }

    public function companyProfile()
    {
        $buyerId = $this->getBuyerCompanyId();
        $companyModel = new CompanyModel();
        $company = $companyModel->find($buyerId);

        return view('customer/profile', [
            'title'   => 'Profil Legalitas Perusahaan - AkuAda B2B',
            'company' => $company
        ]);
    }

    public function updateCompanyProfile()
    {
        $buyerId = $this->getBuyerCompanyId();
        $companyModel = new CompanyModel();

        $companyModel->update($buyerId, [
            'company_name' => trim((string)$this->request->getPost('company_name')),
            'legal_type'   => trim((string)($this->request->getPost('legal_type') ?: 'PT')),
            'npwp'         => trim((string)$this->request->getPost('npwp')),
            'nib'          => trim((string)$this->request->getPost('nib')),
            'address'      => trim((string)$this->request->getPost('address')),
            'city'         => trim((string)$this->request->getPost('city')),
            'province'     => trim((string)$this->request->getPost('province')),
            'pic_name'     => trim((string)$this->request->getPost('pic_name')),
            'pic_phone'    => trim((string)$this->request->getPost('pic_phone')),
            'phone'        => trim((string)$this->request->getPost('pic_phone')),
            'email'        => trim((string)$this->request->getPost('email')),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Data profil legalitas, NPWP, dan kontak perusahaan berhasil diperbarui!');
        return redirect()->to('/customer/company-profile');
    }

    public function payables()
    {
        $buyerId = $this->getBuyerCompanyId();
        $orderModel = new OrderModel();
        $companyModel = new CompanyModel();

        $company = $companyModel->find($buyerId);
        $orders = $orderModel->getDetailedOrders($buyerId, 'customer');

        $totalUnpaid = 0;
        $totalOverdue = 0;
        $totalPaid = 0;
        $today = date('Y-m-d');

        foreach ($orders as $o) {
            $amt = (float)$o['total_amount'];
            $dueDate = $o['due_date'] ?: date('Y-m-d', strtotime($o['created_at'] . ' +30 days'));
            if ($o['payment_status'] === 'paid') {
                $totalPaid += $amt;
            } else {
                $totalUnpaid += $amt;
                if ($dueDate < $today || $o['payment_status'] === 'overdue') {
                    $totalOverdue += $amt;
                }
            }
        }

        return view('customer/payables', [
            'title'        => 'Daftar Hutang Tagihan TOP & Pembayaran - AkuAda B2B',
            'company'      => $company,
            'orders'       => $orders,
            'totalUnpaid'  => $totalUnpaid,
            'totalOverdue' => $totalOverdue,
            'totalPaid'    => $totalPaid,
        ]);
    }

    public function payDebt()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $paymentBank = $this->request->getPost('payment_bank') ?: 'Bank Mandiri (Escrow) AkuAda';
        $paymentRef = trim((string)$this->request->getPost('payment_ref')) ?: ('TRF-BUYER-' . date('YmdHis'));
        $senderBank = trim((string)$this->request->getPost('buyer_sender_bank')) ?: 'Bank Mandiri';
        $senderAccName = trim((string)$this->request->getPost('buyer_sender_account_name')) ?: (session('company_name') ?? 'Corporate Buyer');
        $senderAccNo = trim((string)$this->request->getPost('buyer_sender_account_number')) ?: '123-456-7890';
        $transferDate = $this->request->getPost('transfer_date') ?: date('Y-m-d');
        $paymentNotes = trim((string)$this->request->getPost('buyer_payment_notes'));

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if (!$order) {
            session()->setFlashdata('error', 'Tagihan PO tidak ditemukan.');
            return redirect()->back();
        }

        // Handle Payment Proof (File Upload or Fallback URL)
        $proofUrl = null;
        $file = $this->request->getFile('payment_proof_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = $file->getExtension();
            $fileName = 'proof_' . $orderId . '_' . date('YmdHis') . '.' . $ext;
            $uploadPath = FCPATH . 'uploads/payments';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $file->move($uploadPath, $fileName);
            $proofUrl = base_url('uploads/payments/' . $fileName);
        } else {
            $proofUrl = $this->request->getPost('payment_proof_url') ?: 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80';
        }

        $now = date('Y-m-d H:i:s');
        $paidAt = $transferDate ? ($transferDate . ' ' . date('H:i:s')) : $now;

        $orderModel->update($orderId, [
            'payment_status'              => 'pending_approval',
            'buyer_paid_at'               => $paidAt,
            'buyer_payment_bank'          => $paymentBank,
            'buyer_payment_ref'           => $paymentRef,
            'buyer_sender_bank'           => $senderBank,
            'buyer_sender_account_name'   => $senderAccName,
            'buyer_sender_account_number' => $senderAccNo,
            'buyer_payment_proof'         => $proofUrl,
            'buyer_payment_amount'        => (float)$order['total_amount'],
            'buyer_payment_notes'         => $paymentNotes,
            'payment_rejection_reason'    => null,
            'updated_at'                  => $now,
        ]);

        $logModel = new \App\Models\OrderStatusLogModel();
        $logModel->logStatus(
            $orderId,
            $order['order_status'],
            'Pengajuan Konfirmasi Pembayaran Tagihan PO',
            'Buyer mengonfirmasi pelunasan tagihan senilai ' . format_rupiah($order['total_amount']) . ' dari ' . $senderBank . ' (' . $senderAccNo . ' a/n ' . $senderAccName . ') ke ' . $paymentBank . ' dengan No. Ref: ' . $paymentRef . '. Menunggu verifikasi mutasi rekening oleh Admin Finance AkuAda.',
            session('user_name') ?? 'Corporate Buyer'
        );

        session()->setFlashdata('success', 'Konfirmasi pelunasan tagihan PO ' . ($order['po_number'] ?? $order['order_number']) . ' berhasil dikirimkan! Tim Finance AkuAda akan memverifikasi mutasi rekening koran untuk pengesahan status LUNAS & pemulihan plafon TOP.');
        return redirect()->to('/customer/payables');
    }

    public function payOrderDirect()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $paymentBank = $this->request->getPost('payment_bank') ?: 'Bank Mandiri (Escrow) AkuAda';
        $paymentRef = trim((string)$this->request->getPost('payment_ref')) ?: ('TRF-' . date('YmdHis'));
        $senderBank = trim((string)$this->request->getPost('buyer_sender_bank')) ?: 'Transfer Bank / Virtual Account';
        $senderAccName = trim((string)$this->request->getPost('buyer_sender_account_name')) ?: (session('company_name') ?? 'Corporate Buyer');
        $senderAccNo = trim((string)$this->request->getPost('buyer_sender_account_number')) ?: '-';
        $paymentNotes = trim((string)$this->request->getPost('buyer_payment_notes'));

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if (!$order) {
            session()->setFlashdata('error', 'Tagihan PO tidak ditemukan.');
            return redirect()->back();
        }

        $proofUrl = null;
        $file = $this->request->getFile('payment_proof_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $ext = $file->getExtension();
            $fileName = 'proof_' . $orderId . '_' . date('YmdHis') . '.' . $ext;
            $uploadPath = FCPATH . 'uploads/payments';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $file->move($uploadPath, $fileName);
            $proofUrl = base_url('uploads/payments/' . $fileName);
        } else {
            $proofUrl = $this->request->getPost('payment_proof_url') ?: 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80';
        }

        $now = date('Y-m-d H:i:s');
        $orderModel->update($orderId, [
            'payment_status'              => 'pending_approval',
            'buyer_paid_at'               => $now,
            'buyer_payment_bank'          => $paymentBank,
            'buyer_payment_ref'           => $paymentRef,
            'buyer_sender_bank'           => $senderBank,
            'buyer_sender_account_name'   => $senderAccName,
            'buyer_sender_account_number' => $senderAccNo,
            'buyer_payment_proof'         => $proofUrl,
            'buyer_payment_amount'        => (float)$order['total_amount'],
            'buyer_payment_notes'         => $paymentNotes,
            'payment_rejection_reason'    => null,
            'updated_at'                  => $now,
        ]);

        $logModel = new \App\Models\OrderStatusLogModel();
        $logModel->logStatus(
            $orderId,
            $order['order_status'],
            'Konfirmasi Pembayaran Dikirimkan Buyer',
            'Pembeli mengonfirmasi pelunasan tagihan sebesar ' . format_rupiah($order['total_amount']) . ' ke ' . $paymentBank . ' dengan No. Ref: ' . $paymentRef . '. Menunggu verifikasi mutasi oleh Finance AkuAda.',
            session('user_name') ?? 'Corporate Buyer'
        );

        session()->setFlashdata('success', 'Konfirmasi pembayaran PO #' . ($order['po_number'] ?? $order['order_number']) . ' berhasil dikirimkan! Tim Finance AkuAda akan segera memverifikasi mutasi rekening koran.');
        return redirect()->to('/customer/orders/' . $orderId);
    }

    public function simulatePayOrder()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if (!$order) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan.');
            return redirect()->back();
        }

        $now = date('Y-m-d H:i:s');
        $paymentBank = 'Bank Virtual Account (Instant Payment)';
        $paymentRef = 'VA-AUTOPAID-' . date('YmdHis');

        $orderModel->update($orderId, [
            'payment_status'              => 'paid',
            'buyer_paid_at'               => $now,
            'buyer_payment_bank'          => $paymentBank,
            'buyer_payment_ref'           => $paymentRef,
            'buyer_payment_amount'        => (float)$order['total_amount'],
            'payment_rejection_reason'    => null,
            'updated_at'                  => $now,
        ]);

        $companyModel = new CompanyModel();
        $company = $companyModel->find($order['customer_company_id']);
        if ($company && (float)$company['credit_used_top'] > 0) {
            $newUsed = max(0, (float)$company['credit_used_top'] - (float)$order['total_amount']);
            $companyModel->update($company['id'], ['credit_used_top' => $newUsed]);
        }

        \App\Models\CoaModel::syncOrdersToJournals();

        $logModel = new \App\Models\OrderStatusLogModel();
        $logModel->logStatus(
            $orderId,
            $order['order_status'],
            'Pembayaran Tagihan Lunas (Virtual Account)',
            'Tagihan PO sebesar ' . format_rupiah($order['total_amount']) . ' telah dibayar lunas otomatis via ' . $paymentBank . ' (Ref: ' . $paymentRef . ').',
            session('user_name') ?? 'Corporate Buyer'
        );

        $cbAmount = process_buyer_order_cashback($orderId);
        $cbMsg = ($cbAmount && $cbAmount > 0) ? ' & Cashback ' . format_rupiah($cbAmount) . ' berhasil dikreditkan ke saldo akun Anda!' : '';

        session()->setFlashdata('success', 'Pembayaran tagihan PO sebesar ' . format_rupiah($order['total_amount']) . ' berhasil diselesaikan & LUNAS!' . $cbMsg);
        return redirect()->to('/customer/orders/' . $orderId);
    }

    public function chatCenter()
    {
        $buyerId = $this->getBuyerCompanyId();
        $messageModel = new OrderMessageModel();
        $threads = $messageModel->getConversationsList('customer', $buyerId);
        $orders = (new \App\Models\OrderModel())->where('customer_company_id', $buyerId)->orderBy('id', 'DESC')->findAll(30);

        return view('customer/chat', [
            'title'   => 'Pusat Chat & Bantuan CS AkuAda - AkuAda B2B',
            'threads' => $threads,
            'vendors' => [], // Buyer hanya berkomunikasi dengan AkuAda, vendor disembunyikan
            'orders'  => $orders,
        ]);
    }
}
