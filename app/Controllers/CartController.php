<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ProductPriceTierModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\CompanyModel;

class CartController extends BaseController
{
    public function index()
    {
        $cart = session('cart') ?? [];
        $subtotal = 0;
        $ppnTotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['subtotal'];
            $ppnTotal += round($item['subtotal'] * (get_ppn_rate() / 100), 2);
        }

        $shippingAmount = (float)get_system_setting('default_shipping_amount', 0);
        $grandTotal = $subtotal + $ppnTotal + $shippingAmount;

        return view('marketplace/cart', [
            'title'      => 'Keranjang Pengadaan PO - AkuAda B2B',
            'cart'       => $cart,
            'subtotal'   => $subtotal,
            'ppnTotal'       => $ppnTotal,
            'shippingAmount' => $shippingAmount,
            'grandTotal' => $grandTotal
        ]);
    }

    public function add()
    {
        $productId = (int)$this->request->getPost('product_id');
        $qty = max(1, (int)$this->request->getPost('qty'));

        $productModel = new ProductModel();
        $product = $productModel->find($productId);

        if (!$product) {
            session()->setFlashdata('error', 'Produk tidak ditemukan');
            return redirect()->back();
        }

        // MOQ check
        if ($qty < $product['moq']) {
            session()->setFlashdata('error', 'Jumlah pesanan minimal (MOQ) adalah ' . $product['moq'] . ' ' . $product['uom']);
            return redirect()->back();
        }

        // Calculate Tier Pricing
        $tierModel = new ProductPriceTierModel();
        $unitPrice = $tierModel->getPriceForQuantity($productId, $qty, $product['price_regular']);

        $ppnRate = get_ppn_rate();
        $ppnAmount = $product['is_taxable'] ? round($unitPrice * $qty * ($ppnRate / 100), 2) : 0;
        $subtotal = $unitPrice * $qty;

        // Get vendor company agreed commission rate
        $companyModel = new CompanyModel();
        $vendorComp = $companyModel->find($product['vendor_company_id']);
        $agreedRate = (float)($vendorComp['commission_rate'] ?? get_default_vendor_fee_rate());
        $effectiveFee = ($product['vendor_fee_percent'] !== null && $product['vendor_fee_percent'] !== '') 
            ? (float)$product['vendor_fee_percent'] 
            : $agreedRate;

        $cart = session('cart') ?? [];
        $cart[$productId] = [
            'product_id'         => $productId,
            'name'               => $product['name'],
            'sku'                => $product['sku'],
            'uom'                => $product['uom'],
            'moq'                => $product['moq'],
            'vendor_company_id'  => $product['vendor_company_id'],
            'image_url'          => $product['image_url'],
            'qty'                => $qty,
            'unit_price'         => $unitPrice,
            'ppn_percent'        => $ppnRate,
            'ppn_amount'         => $ppnAmount,
            'subtotal'           => $subtotal,
            'vendor_fee_percent' => $effectiveFee,
        ];

        session()->set('cart', $cart);
        session()->setFlashdata('toast', 'Barang ditambahkan ke Keranjang Pengadaan PO!');
        return redirect()->to('/cart');
    }

    public function update()
    {
        $productId = (int)$this->request->getPost('product_id');
        $qty = max(1, (int)$this->request->getPost('qty'));

        $cart = session('cart') ?? [];
        if (!isset($cart[$productId])) {
            return redirect()->to('/cart');
        }

        $productModel = new ProductModel();
        $product = $productModel->find($productId);

        if ($qty < $product['moq']) {
            session()->setFlashdata('error', 'Minimal order (MOQ) untuk barang ini adalah ' . $product['moq'] . ' ' . $product['uom']);
            return redirect()->to('/cart');
        }

        $tierModel = new ProductPriceTierModel();
        $unitPrice = $tierModel->getPriceForQuantity($productId, $qty, $product['price_regular']);

        $ppnRate = get_ppn_rate();
        $ppnAmount = $product['is_taxable'] ? round($unitPrice * $qty * ($ppnRate / 100), 2) : 0;
        $subtotal = $unitPrice * $qty;

        $cart[$productId]['qty'] = $qty;
        $cart[$productId]['unit_price'] = $unitPrice;
        $cart[$productId]['ppn_amount'] = $ppnAmount;
        $cart[$productId]['subtotal'] = $subtotal;

        session()->set('cart', $cart);
        session()->setFlashdata('toast', 'Jumlah pesanan berhasil diperbarui!');
        return redirect()->to('/cart');
    }

    public function remove($productId)
    {
        $cart = session('cart') ?? [];
        unset($cart[$productId]);
        session()->set('cart', $cart);
        session()->setFlashdata('toast', 'Barang dihapus dari keranjang.');
        return redirect()->to('/cart');
    }

    public function checkout()
    {
        $cart = session('cart') ?? [];
        if (empty($cart)) {
            session()->setFlashdata('error', 'Keranjang pengadaan Anda masih kosong.');
            return redirect()->to('/');
        }

        $subtotal = 0;
        $ppnTotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['subtotal'];
            $ppnTotal += round($item['subtotal'] * (get_ppn_rate() / 100), 2);
        }
        $shippingAmount = (float)get_system_setting('default_shipping_amount', 0);
        $grandTotal = $subtotal + $ppnTotal + $shippingAmount;

        // Identify buyer company from session
        $companyModel = new CompanyModel();
        $buyerCompanyId = (session('user_role') === 'customer' && session('company_id')) ? session('company_id') : ($companyModel->where('type', 'buyer')->countAllResults() === 1 ? ensure_role_session('customer') : session('company_id'));
        $buyerCompany = null;
        $isRegistered = false;

        if ($buyerCompanyId) {
            $buyerCompany = $companyModel->find($buyerCompanyId);
            if ($buyerCompany && $buyerCompany['type'] === 'buyer' && $buyerCompany['is_verified'] == 1 && (float)$buyerCompany['credit_limit_top'] > 0) {
                $isRegistered = true;
            }
        }

        return view('marketplace/checkout', [
            'title'        => 'Checkout Pengadaan B2B - AkuAda',
            'cart'         => $cart,
            'subtotal'     => $subtotal,
            'ppnTotal'       => $ppnTotal,
            'shippingAmount' => $shippingAmount,
            'grandTotal'   => $grandTotal,
            'buyerCompany' => $buyerCompany,
            'isRegistered' => $isRegistered,
        ]);
    }

    public function processCheckout()
    {
        $cart = session('cart') ?? [];
        if (empty($cart)) {
            return redirect()->to('/');
        }

        $poNumber = $this->request->getPost('po_number') ?: 'PO/' . date('Ym') . '/' . rand(100, 999);
        $paymentMethod = $this->request->getPost('payment_method') ?: 'top_30';
        $taxInvoiceRequired = $this->request->getPost('tax_invoice_required') ? 1 : 0;
        $customerNotes = $this->request->getPost('customer_notes');
        $shippingAddress = $this->request->getPost('shipping_address');

        $buyerCompanyId = (session('user_role') === 'customer' && session('company_id')) ? session('company_id') : ($companyModel->where('type', 'buyer')->countAllResults() === 1 ? ensure_role_session('customer') : session('company_id'));
        $companyModel = new CompanyModel();
        $buyerCompany = null;
        $isRegistered = false;

        if ($buyerCompanyId) {
            $buyerCompany = $companyModel->find($buyerCompanyId);
            if ($buyerCompany && $buyerCompany['type'] === 'buyer' && $buyerCompany['is_verified'] == 1 && (float)$buyerCompany['credit_limit_top'] > 0) {
                $isRegistered = true;
            }
        }

        // Group cart by vendor if multiple, or take vendor of first item
        $firstItem = reset($cart);
        $vendorCompanyId = $firstItem['vendor_company_id'] ?? 1;

        $subtotal = 0;
        $ppnTotal = 0;
        $totalVendorFee = 0;
        $vendorNetPayout = 0;

        // Precalculate items and fee breakdowns
        $processedItems = [];
        foreach ($cart as $item) {
            $itemSubtotal = (float)$item['subtotal'];
            $itemPpn = round($itemSubtotal * (get_ppn_rate() / 100), 2);
            $item['ppn_amount'] = $itemPpn;
            $subtotal += $itemSubtotal;
            $ppnTotal += $itemPpn;

            // Fee calculation per product item - fallback to vendor company agreed rate
            if (!isset($item['vendor_fee_percent']) || $item['vendor_fee_percent'] === null) {
                $compModel = new CompanyModel();
                $vComp = $compModel->find($item['vendor_company_id'] ?? 1);
                $customFeeRate = (float)($vComp['commission_rate'] ?? get_default_vendor_fee_rate());
            } else {
                $customFeeRate = (float)$item['vendor_fee_percent'];
            }
            $feeCalc = calculate_vendor_fee($itemSubtotal, $customFeeRate, $itemPpn);

            $totalVendorFee += $feeCalc['fee_amount'];
            $vendorNetPayout += $feeCalc['net_amount'];

            $processedItems[] = array_merge($item, [
                'fee_percent' => $feeCalc['percent'],
                'fee_amount'  => $feeCalc['fee_amount'],
                'net_amount'  => $feeCalc['net_amount'],
            ]);
        }
        $shippingAmount = (float)get_system_setting('default_shipping_amount', 0);
        $grandTotal = $subtotal + $ppnTotal + $shippingAmount;

        // Aturan Keras: Jika buyer belum terdaftar di AkuAda, tidak bisa membayar PO dengan kredit (TOP / PO On-Account)
        $isOverlimitRequest = ($paymentMethod === 'overlimit_top');
        if ($isOverlimitRequest) {
            if (!$isRegistered) {
                session()->setFlashdata('error', 'Perusahaan Anda belum terdaftar resmi di AkuAda untuk fasilitas kredit.');
                return redirect()->to('/checkout');
            }
            $overlimitDays = max(1, (int)($this->request->getPost('overlimit_top_days') ?: 30));
            $paymentMethod = 'top_' . $overlimitDays;
            $creditLimit = (float)($buyerCompany['credit_limit_top'] ?? 0);
            $creditUsed = (float)($buyerCompany['credit_used_top'] ?? 0);
            $shortfall = max(0, $grandTotal - max(0, $creditLimit - $creditUsed));
            $reason = trim((string)($this->request->getPost('overlimit_reason') ?: 'Pengajuan ekstra limit TOP'));
            $customerNotes = trim('[OVER-LIMIT REQUEST: Kurang ' . format_rupiah($shortfall) . ' | Alasan: ' . $reason . '] ' . $customerNotes);
        } elseif (str_starts_with($paymentMethod, 'top_') || $paymentMethod === 'corporate_po') {
            if (!$isRegistered) {
                session()->setFlashdata('error', 'Perusahaan Anda belum terdaftar resmi di AkuAda untuk fasilitas kredit. Pembayaran PO tidak dapat menggunakan kredit tempo (TOP 14-90 Hari / Corporate PO). Silakan pilih metode pembayaran Bank Virtual Account atau hubungi Superadmin AkuAda untuk pendaftaran akun fasilitas kredit.');
                return redirect()->to('/checkout');
            }

            $creditLimit = (float)($buyerCompany['credit_limit_top'] ?? 0);
            $creditUsed = (float)($buyerCompany['credit_used_top'] ?? 0);
            $availableCredit = max(0, $creditLimit - $creditUsed);

            if ($grandTotal > $availableCredit) {
                session()->setFlashdata('error', 'Sisa fasilitas kredit TOP perusahaan Anda tidak mencukupi untuk pesanan ini. Tersedia: ' . format_rupiah($availableCredit) . ', Total Tagihan PO: ' . format_rupiah($grandTotal) . '. Silakan ajukan kenaikan fasilitas kredit ke Superadmin AkuAda atau gunakan pembayaran tunai/VA.');
                return redirect()->to('/checkout');
            }
        }

        // Jika buyer belum terdaftar resmi dan membayar dengan Bank VA (Non-Kredit)
        if (!$buyerCompanyId) {
            $companyName = trim($this->request->getPost('guest_company_name') ?: '');
            if (empty($companyName)) {
                $companyName = 'Perusahaan Pembeli Baru (Non-Kredit)';
            }
            $legalType = $this->request->getPost('guest_legal_type') ?: 'PT';
            $npwp = $this->request->getPost('guest_npwp') ?: null;
            $email = $this->request->getPost('guest_email') ?: 'purchasing@tamu.co.id';
            $phone = $this->request->getPost('guest_phone') ?: null;
            $picName = $this->request->getPost('guest_pic_name') ?: 'PIC Purchasing';

            $buyerCompanyId = $companyModel->insert([
                'type'             => 'buyer',
                'company_name'     => $companyName,
                'legal_type'       => $legalType,
                'npwp'             => $npwp,
                'email'            => $email,
                'phone'            => $phone,
                'pic_name'         => $picName,
                'pic_phone'        => $phone,
                'pic_email'        => $email,
                'credit_limit_top' => 0.00, // Tidak ada fasilitas kredit
                'credit_used_top'  => 0.00,
                'is_verified'      => 0, // Belum terverifikasi
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
        }

        // Determine due date according to payment method
        $days = str_starts_with($paymentMethod, 'top_') ? max(1, (int)substr($paymentMethod, 4)) : 30;
        if ($paymentMethod === 'top_14') $days = 14;
        if ($paymentMethod === 'top_60') $days = 60;
        if ($paymentMethod === 'top_90') $days = 90;
        $dueDate = date('Y-m-d', strtotime("+$days days"));

        $orderNumber = 'ORD-' . date('Ym') . '-' . sprintf('%04d', rand(100, 9999));
        $now = date('Y-m-d H:i:s');

        $orderModel = new OrderModel();
        $orderId = $orderModel->insert([
            'order_number'         => $orderNumber,
            'po_number'            => $poNumber,
            'customer_company_id'  => $buyerCompanyId,
            'vendor_company_id'    => $vendorCompanyId,
            'subtotal'             => $subtotal,
            'ppn_amount'           => $ppnTotal,
            'total_vendor_fee'     => $totalVendorFee,
            'vendor_net_payout'    => $vendorNetPayout,
            'shipping_amount'      => $shippingAmount,
            'total_amount'         => $grandTotal,
            'payment_method'       => $paymentMethod,
            'payment_status'       => 'pending_approval',
            'order_status'         => 'po_issued',
            'tax_invoice_required' => $taxInvoiceRequired,
            'customer_notes'       => $customerNotes,
            'shipping_address'     => $shippingAddress,
            'bast_number'          => 'BAST/AKUADA/' . date('Ym') . '/' . rand(100, 999),
            'invoice_number'       => 'INV/AKUADA/' . date('Ym') . '/' . rand(100, 999),
            'tax_invoice_number'   => get_system_setting('tax_nsfp_prefix', '010.002-26.') . rand(10000000, 99999999),
            'due_date'               => $dueDate,
            'vendor_invoice_number'  => 'VINV/' . date('Ym') . '/' . rand(100, 999),
            'vendor_payout_status'   => 'unpaid',
            'vendor_payout_due_date' => date('Y-m-d', strtotime('+' . max(1, (int)get_system_setting('vendor_payout_days', 7)) . ' days')),
            'created_at'           => $now,
            'updated_at'           => $now,
        ]);

        $orderItemModel = new OrderItemModel();
        foreach ($processedItems as $item) {
            $orderItemModel->insert([
                'order_id'           => $orderId,
                'product_id'         => $item['product_id'],
                'product_name'       => $item['name'],
                'sku'                => $item['sku'],
                'uom'                => $item['uom'],
                'qty'                => $item['qty'],
                'unit_price'         => $item['unit_price'],
                'vendor_fee_percent' => $item['fee_percent'],
                'vendor_fee_amount'  => $item['fee_amount'],
                'vendor_net_amount'  => $item['net_amount'],
                'ppn_amount'         => $item['ppn_amount'],
                'subtotal'           => $item['subtotal'],
                'created_at'         => $now,
                'updated_at'         => $now,
            ]);
        }

        // Log initial stage to timeline
        $statusLogModel = new \App\Models\OrderStatusLogModel();
        $statusLogModel->logStatus(
            $orderId,
            'po_issued',
            'Penerbitan Purchase Order (PO)',
            'PO resmi #' . $poNumber . ' berhasil diterbitkan oleh pembeli dengan metode pembayaran ' . strtoupper($paymentMethod) . '.',
            session('user_name') ?? 'Procurement Officer'
        );

        // If paying with TOP credit, update credit used on buyer company
        if ($buyerCompany && (str_starts_with($paymentMethod, 'top_') || $paymentMethod === 'corporate_po')) {
            $newUsed = (float)$buyerCompany['credit_used_top'] + $grandTotal;
            $companyModel->update($buyerCompanyId, ['credit_used_top' => $newUsed]);
            session()->set('credit_used_top', $newUsed);
        }

        // Clear cart
        session()->remove('cart');

        session()->setFlashdata('success', $isOverlimitRequest
            ? 'Purchase Order (PO) #' . $poNumber . ' berhasil diterbitkan dengan status Pengajuan Over-Limit TOP! Superadmin AkuAda akan segera menyetujui penambahan plafon kredit Anda.'
            : 'Purchase Order (PO) #' . $poNumber . ' Berhasil Diterbitkan! Vendor sedang memproses pesanan dan dokumen serah terima (BAST).');
        return redirect()->to('/customer/orders/' . $orderId);
    }
}
