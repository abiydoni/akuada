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
        $isLoggedInCustomer = !empty(session('user_id')) && (session('user_role') === 'customer' || session('role') === 'customer');
        $buyerCompanyId = $isLoggedInCustomer ? (int)session('company_id') : null;
        $buyerCompany = null;
        $hasCompany = false;
        $isVerified = false;

        $maxUnpaidLimit = get_max_unpaid_notes_limit();
        $unpaidNotesCount = 0;
        $remainingNotes = $maxUnpaidLimit;
        $isSufficientCredit = true;

        if ($buyerCompanyId) {
            $buyerCompany = $companyModel->find($buyerCompanyId);
            if ($buyerCompany && $buyerCompany['type'] === 'buyer') {
                $hasCompany = true;
                $isVerified = ((int)$buyerCompany['is_verified'] === 1);
                if ($isVerified) {
                    $unpaidNotesCount = get_buyer_unpaid_notes_count($buyerCompanyId);
                    $remainingNotes = max(0, $maxUnpaidLimit - $unpaidNotesCount);
                    $isSufficientCredit = ($unpaidNotesCount < $maxUnpaidLimit);
                }
            }
        }

        $isRegistered = $isVerified;

        return view('marketplace/checkout', [
            'title'              => 'Checkout Pengadaan B2B - AkuAda',
            'cart'               => $cart,
            'subtotal'           => $subtotal,
            'ppnTotal'           => $ppnTotal,
            'shippingAmount'     => $shippingAmount,
            'grandTotal'         => $grandTotal,
            'buyerCompany'       => $buyerCompany,
            'hasCompany'         => $hasCompany,
            'isVerified'         => $isVerified,
            'isRegistered'       => $isRegistered,
            'maxUnpaidLimit'     => $maxUnpaidLimit,
            'unpaidNotesCount'   => $unpaidNotesCount,
            'remainingNotes'     => $remainingNotes,
            'isSufficientCredit' => $isSufficientCredit,
        ]);
    }

    public function processCheckout()
    {
        $cart = session('cart') ?? [];
        if (empty($cart)) {
            return redirect()->to('/');
        }

        $poNumber = $this->request->getPost('po_number') ?: generate_unique_number('PO');
        $paymentMethod = $this->request->getPost('payment_method') ?: 'top_30';
        $taxInvoiceRequired = $this->request->getPost('tax_invoice_required') ? 1 : 0;
        $customerNotes = $this->request->getPost('customer_notes');
        $shippingAddress = $this->request->getPost('shipping_address');

        $isLoggedInCustomer = !empty(session('user_id')) && (session('user_role') === 'customer' || session('role') === 'customer');
        $buyerCompanyId = $isLoggedInCustomer ? (int)session('company_id') : null;
        $companyModel = new CompanyModel();
        $buyerCompany = null;
        $hasCompany = false;
        $isVerified = false;

        $maxUnpaidLimit = get_max_unpaid_notes_limit();
        $unpaidNotesCount = 0;
        $isSufficientCredit = true;

        if ($buyerCompanyId) {
            $buyerCompany = $companyModel->find($buyerCompanyId);
            if ($buyerCompany && $buyerCompany['type'] === 'buyer') {
                $hasCompany = true;
                $isVerified = ((int)$buyerCompany['is_verified'] === 1);
                if ($isVerified) {
                    $unpaidNotesCount = get_buyer_unpaid_notes_count($buyerCompanyId);
                    $isSufficientCredit = ($unpaidNotesCount < $maxUnpaidLimit);
                }
            }
        }

        $isRegistered = $isVerified;

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

        // Aturan Keras: Jika buyer belum diverifikasi legalitasnya oleh Superadmin, tidak bisa membayar PO dengan fasilitas kredit (TOP / Corporate PO / Over-limit)
        $isOverlimitRequest = ($paymentMethod === 'overlimit_top');
        if ($isOverlimitRequest) {
            if (!$isVerified) {
                session()->setFlashdata('error', 'Dokumen legalitas perusahaan Anda belum diverifikasi oleh Superadmin AkuAda. Pengajuan Over-Limit TOP hanya dapat dilakukan oleh akun resmi yang telah terverifikasi.');
                return redirect()->to('/checkout');
            }
            $overlimitDays = max(1, (int)($this->request->getPost('overlimit_top_days') ?: 30));
            $paymentMethod = 'top_' . $overlimitDays;
            $reason = trim((string)($this->request->getPost('overlimit_reason') ?: 'Pengajuan ekstra nota kredit'));
            $customerNotes = trim("[OVER-LIMIT REQUEST: Batas {$maxUnpaidLimit} Nota Tercapai ({$unpaidNotesCount} Nota Belum Lunas) | Alasan: {$reason}] " . $customerNotes);
        } elseif (str_starts_with($paymentMethod, 'top_') || $paymentMethod === 'corporate_po') {
            if (!$isVerified) {
                session()->setFlashdata('error', 'Dokumen legalitas perusahaan Anda belum disetujui oleh Superadmin AkuAda. Pembayaran PO tidak dapat menggunakan kredit tempo (TOP 14-90 Hari / Corporate PO). Silakan pilih metode pembayaran Bank Virtual Account (Non-Kredit) untuk melanjutkan transaksi pengadaan.');
                return redirect()->to('/checkout');
            }

            if (!$isSufficientCredit) {
                session()->setFlashdata('error', "Batas kredit tempo tercapai. Perusahaan Anda saat ini memiliki {$unpaidNotesCount} dari batas maksimal {$maxUnpaidLimit} nota yang belum terbayarkan dalam bulan ini. Silakan lakukan pelunasan tagihan sebelumnya atau gunakan pembayaran Bank Virtual Account.");
                return redirect()->to('/checkout');
            }
        }

        // Jika buyer belum terdaftar resmi dan membayar dengan Bank VA (Non-Kredit)
        $isGuestCheckout = false;
        if (!$buyerCompanyId) {
            $isGuestCheckout = true;
            $companyName = trim($this->request->getPost('guest_company_name') ?: '');
            if (empty($companyName)) {
                $companyName = 'Perusahaan Pembeli Baru (Non-Kredit)';
            }
            $legalType = $this->request->getPost('guest_legal_type') ?: 'PT';
            $npwp = $this->request->getPost('guest_npwp') ?: null;
            $email = trim($this->request->getPost('guest_email') ?: 'purchasing@tamu.co.id');
            $phone = trim($this->request->getPost('guest_phone') ?: '');
            $picName = trim($this->request->getPost('guest_pic_name') ?: 'PIC Purchasing');

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
                'credit_limit_top' => 0.00, // Tidak ada fasilitas kredit awal
                'credit_used_top'  => 0.00,
                'is_verified'      => 0, // Belum terverifikasi fasilitas kredit
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);

            // Buatkan akun login customer otomatis untuk buyer umum agar langsung bisa melihat PO, Faktur & VA
            $userModel = new \App\Models\UserModel();
            $existingUser = $userModel->where('email', $email)->first();
            if (!$existingUser) {
                $tempPassword = bin2hex(random_bytes(4));
                $authUserId = $userModel->insert([
                    'name'       => $picName,
                    'email'      => $email,
                    'password'   => password_hash($tempPassword, PASSWORD_DEFAULT),
                    'role'       => 'customer',
                    'phone'      => $phone,
                    'company_id' => $buyerCompanyId,
                    'position'   => 'PIC Procurement',
                    'status'     => 'active',
                ]);
            } else {
                $authUserId = $existingUser['id'];
                if (empty($existingUser['company_id'])) {
                    $userModel->update($authUserId, ['company_id' => $buyerCompanyId]);
                }
            }

            // Inisialisasi session login sebagai customer
            session()->set([
                'is_logged_in'     => true,
                'role'             => 'customer',
                'user_id'          => $authUserId,
                'user_name'        => $picName,
                'user_email'       => $email,
                'user_role'        => 'customer',
                'company_id'       => $buyerCompanyId,
                'company_name'     => $companyName,
                'company_legal'    => $legalType,
                'company_npwp'     => $npwp,
                'commission_rate'  => 0,
                'credit_limit_top' => 0.00,
                'credit_used_top'  => 0.00,
            ]);
        }

        // Determine due date according to payment method
        if ($paymentMethod === 'bank_va') {
            $dueDate = date('Y-m-d', strtotime('+3 days'));
        } else {
            $days = str_starts_with($paymentMethod, 'top_') ? max(1, (int)substr($paymentMethod, 4)) : 30;
            if ($paymentMethod === 'top_14') $days = 14;
            if ($paymentMethod === 'top_60') $days = 60;
            if ($paymentMethod === 'top_90') $days = 90;
            $dueDate = date('Y-m-d', strtotime("+$days days"));
        }

        $orderNumber = generate_unique_number('ORD');
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
            'payment_status'       => $isOverlimitRequest ? 'pending_approval' : 'unpaid',
            'order_status'         => 'po_issued',
            'tax_invoice_required' => $taxInvoiceRequired,
            'customer_notes'       => $customerNotes,
            'shipping_address'     => $shippingAddress,
            'bast_number'          => generate_unique_number('BAST'),
            'invoice_number'       => generate_unique_number('INV'),
            'tax_invoice_number'   => get_system_setting('tax_nsfp_prefix', '010.002-26.') . sprintf('%08d', abs(crc32(uniqid('', true)))),
            'due_date'               => $dueDate,
            'vendor_invoice_number'  => generate_unique_number('VINV'),
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

        // If paying with TOP credit, update credit used on buyer company (only for verified buyer)
        if ($isVerified && $buyerCompany && (str_starts_with($paymentMethod, 'top_') || $paymentMethod === 'corporate_po')) {
            $newUsed = (float)$buyerCompany['credit_used_top'] + $grandTotal;
            $companyModel->update($buyerCompanyId, ['credit_used_top' => $newUsed]);
            session()->set('credit_used_top', $newUsed);
        }

        // Clear cart
        session()->remove('cart');

        if ($isOverlimitRequest) {
            $successNotice = 'Purchase Order (PO) #' . $poNumber . ' berhasil diterbitkan dengan status Pengajuan Over-Limit TOP! Superadmin AkuAda akan segera menyetujui penambahan plafon kredit Anda.';
        } elseif ($isGuestCheckout) {
            $successNotice = 'Purchase Order (PO) #' . $poNumber . ' berhasil diterbitkan! Akun portal pengadaan korporat Anda telah aktif. Silakan lakukan pembayaran melalui Virtual Account untuk proses serah terima barang.';
        } elseif (!$isVerified) {
            $successNotice = 'Purchase Order (PO) #' . $poNumber . ' berhasil diterbitkan! Karena legalitas perusahaan Anda masih dalam proses verifikasi, pembayaran dilakukan melalui Bank Virtual Account (Non-Kredit).';
        } else {
            $successNotice = 'Purchase Order (PO) #' . $poNumber . ' Berhasil Diterbitkan! Vendor sedang memproses pesanan dan dokumen serah terima (BAST).';
        }

        session()->setFlashdata('success', $successNotice);
        return redirect()->to('/customer/orders/' . $orderId);
    }
}

