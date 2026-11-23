<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\ProductPriceTierModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\CompanyModel;
use App\Models\UserModel;

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

    /**
     * Generate & Download Surat Penawaran Harga Resmi (Official B2B Quotation)
     * Dibuat otomatis berdasarkan item-item yang dipilih oleh Buyer di keranjang.
     */
    public function quotation()
    {
        $cart = session('cart') ?? [];
        if (empty($cart)) {
            session()->setFlashdata('error', 'Keranjang pengadaan Anda masih kosong. Silakan tambahkan barang terlebih dahulu.');
            return redirect()->to('/cart');
        }

        // Selected items can come from POST (array selected_items) or GET (comma-separated string or array)
        $selectedIds = $this->request->getPost('selected_items') ?? $this->request->getGet('items');
        if (is_string($selectedIds)) {
            $selectedIds = explode(',', $selectedIds);
        }
        $selectedIds = is_array($selectedIds) ? array_map('intval', array_filter($selectedIds)) : [];

        // Filter cart items
        $quotationItems = [];
        $subtotal = 0;
        $ppnTotal = 0;

        $companyModel = new CompanyModel();
        $productModel = new ProductModel();

        $vendorList = [];
        $primaryVendor = null;

        foreach ($cart as $productId => $item) {
            if (empty($selectedIds) || in_array((int)$productId, $selectedIds, true)) {
                $vendorName = 'Mitra Pemasok AkuAda';
                if (!empty($item['vendor_company_id'])) {
                    $vComp = $companyModel->find($item['vendor_company_id']);
                    if ($vComp && !empty($vComp['company_name'])) {
                        $vendorName = (!empty($vComp['legal_type']) ? $vComp['legal_type'] . ' ' : '') . $vComp['company_name'];
                        if (!$primaryVendor) {
                            $primaryVendor = $vComp;
                        }
                        $vendorList[$vComp['id']] = $vendorName;
                    }
                }

                $pData = $productModel->find($productId);
                $brand = $pData['brand'] ?? 'Generic';
                $spec = $pData['spec_details'] ?? '';

                $itemSubtotal = (float)$item['subtotal'];
                $itemPpn = round($itemSubtotal * (get_ppn_rate() / 100), 2);

                $quotationItems[] = [
                    'product_id'   => $productId,
                    'name'         => $item['name'],
                    'sku'          => $item['sku'],
                    'brand'        => $brand,
                    'spec_details' => $spec,
                    'uom'          => $item['uom'],
                    'qty'          => (int)$item['qty'],
                    'unit_price'   => (float)$item['unit_price'],
                    'subtotal'     => $itemSubtotal,
                    'ppn_amount'   => $itemPpn,
                    'vendor_name'  => $vendorName,
                    'image_url'    => $item['image_url'] ?? '',
                ];

                $subtotal += $itemSubtotal;
                $ppnTotal += $itemPpn;
            }
        }

        if (empty($quotationItems)) {
            session()->setFlashdata('error', 'Pilih minimal satu barang di keranjang untuk membuat Surat Penawaran Resmi.');
            return redirect()->to('/cart');
        }

        $shippingAmount = (float)get_system_setting('default_shipping_amount', 0);
        $grandTotal = $subtotal + $ppnTotal + $shippingAmount;

        // Buyer Info dari tabel companies & session
        $buyer = null;
        $buyerCompanyId = (int)(session('company_id') ?: 0);
        if (!$buyerCompanyId && session('user_id')) {
            $userModel = new UserModel();
            $u = $userModel->find(session('user_id'));
            if ($u && !empty($u['company_id'])) {
                $buyerCompanyId = (int)$u['company_id'];
            }
        }
        if ($buyerCompanyId) {
            $buyer = $companyModel->find($buyerCompanyId);
        }

        $userModel = new UserModel();
        $user = session('user_id') ? $userModel->find(session('user_id')) : null;

        $buyerName = $this->request->getPost('buyer_company_name') 
            ?: ($buyer['company_name'] ?? session('company_name') ?? 'Perusahaan Pembeli');

        // Nama & Gelar Penandatangan diambil murni dari tabel users.name (atau companies.pic_name)
        $picName = $this->request->getPost('buyer_pic_name') 
            ?: ($user['name'] ?? ($buyer['pic_name'] ?? session('user_name') ?? 'Bpk/Ibu Pimpinan'));

        // Jabatan Penandatangan diambil murni dari tabel users.position
        $picPosition = $this->request->getPost('buyer_position') 
            ?: ($user['position'] ?? 'PIC Procurement');

        // Email Pemesan diambil murni dari tabel companies atau tabel users
        $buyerEmail = $this->request->getPost('buyer_email') 
            ?: ($buyer['email'] ?? ($user['email'] ?? session('user_email') ?? '-'));

        // Alamat Pengiriman Kebutuhan: ambil data riil dari tabel companies (address, city, province)
        $buyerAddress = '';
        if ($buyer) {
            $addrParts = array_filter([
                $buyer['address'] ?? '',
                $buyer['city'] ?? '',
                $buyer['province'] ?? '',
            ], function($v) { return !empty(trim((string)$v)); });
            
            $buyerAddress = implode(', ', $addrParts);
        }

        // Jika di profil belum ada alamat, gunakan alamat pengiriman terakhir dari tabel orders
        if (empty($buyerAddress) && !empty($buyer['id'])) {
            $orderModel = new OrderModel();
            $lastOrder = $orderModel->where('customer_company_id', $buyer['id'])
                                   ->where('shipping_address IS NOT NULL')
                                   ->where("shipping_address != ''")
                                   ->orderBy('id', 'DESC')
                                   ->first();
            if ($lastOrder && !empty($lastOrder['shipping_address'])) {
                $buyerAddress = $lastOrder['shipping_address'];
            }
        }

        if ($this->request->getPost('buyer_address')) {
            $buyerAddress = trim((string)$this->request->getPost('buyer_address'));
        }

        if (empty($buyerAddress)) {
            $buyerAddress = 'Alamat kantor / gudang belum dikonfigurasi di Profil Perusahaan (Silakan lengkapi di Menu Profil Legalitas)';
        }

        // Sembunyikan nomor HP, arahkan komunikasi resmi ke Live Chat Web
        $buyerPhone = 'Live Chat Web Resmi (Terverifikasi)';

        $quotationNumber = 'QUO/' . date('Ym') . '/' . sprintf('%04d', rand(100, 9999));
        $validUntil = date('Y-m-d', strtotime('+14 days'));

        if (!$primaryVendor) {
            $primaryVendor = $companyModel->where('type', 'vendor')->first();
        }
        $vendorDisplayName = !empty($vendorList) 
            ? implode(', ', array_unique($vendorList))
            : ($primaryVendor ? ((!empty($primaryVendor['legal_type']) ? $primaryVendor['legal_type'] . ' ' : '') . ($primaryVendor['company_name'] ?? 'Mitra Vendor Pemasok AkuAda')) : 'Mitra Vendor Pemasok AkuAda');

        return view('marketplace/quotation', [
            'title'           => 'Surat Penawaran Harga Resmi #' . $quotationNumber . ' - AkuAda B2B',
            'quotationNumber' => $quotationNumber,
            'quotationDate'   => date('Y-m-d'),
            'validUntil'      => $validUntil,
            'items'           => $quotationItems,
            'subtotal'        => $subtotal,
            'ppnTotal'        => $ppnTotal,
            'shippingAmount'  => $shippingAmount,
            'grandTotal'      => $grandTotal,
            'vendor'          => [
                'id'           => $primaryVendor['id'] ?? null,
                'company_name' => $vendorDisplayName,
                'city'         => $primaryVendor['city'] ?? '',
                'province'     => $primaryVendor['province'] ?? '',
                'address'      => $primaryVendor['address'] ?? '',
            ],
            'buyer'           => [
                'company_name' => $buyerName,
                'pic_name'     => $picName,
                'position'     => $picPosition,
                'phone'        => $buyerPhone,
                'email'        => $buyerEmail,
                'address'      => $buyerAddress,
            ],
        ]);
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

        $defaultCreditLimit = (float)get_system_setting('default_credit_limit', 100000000);
        $maxUnpaidLimit = get_max_unpaid_notes_limit();
        $unpaidNotesCount = 0;
        $remainingNotes = $maxUnpaidLimit;
        $creditLimit = $defaultCreditLimit;
        $creditUsed = 0.0;
        $availableCredit = $defaultCreditLimit;
        $isNotesSufficient = true;
        $isAmountSufficient = true;
        $isSufficientCredit = true;

        if ($buyerCompanyId) {
            $buyerCompany = $companyModel->find($buyerCompanyId);
            if ($buyerCompany && $buyerCompany['type'] === 'buyer') {
                $hasCompany = true;
                $isVerified = ((int)$buyerCompany['is_verified'] === 1);
                if ($isVerified) {
                    $unpaidNotesCount = get_buyer_unpaid_notes_count($buyerCompanyId);
                    $remainingNotes = max(0, $maxUnpaidLimit - $unpaidNotesCount);
                    $creditLimit = (float)(!empty($buyerCompany['credit_limit_top']) && $buyerCompany['credit_limit_top'] > 0 
                        ? $buyerCompany['credit_limit_top'] 
                        : $defaultCreditLimit);
                    $creditUsed = (float)($buyerCompany['credit_used_top'] ?? 0);
                    $availableCredit = max(0, $creditLimit - $creditUsed);

                    $isNotesSufficient = ($unpaidNotesCount < $maxUnpaidLimit);
                    $isAmountSufficient = ($grandTotal <= $availableCredit);
                    $isSufficientCredit = $isNotesSufficient && $isAmountSufficient;
                }
            }
        }

        $isRegistered = $isVerified;

        // Alamat pengiriman kantor dibuat default sesuai alamat lengkap buyer
        $defaultShippingAddress = '';
        if ($buyerCompany) {
            $addrParts = array_filter([
                $buyerCompany['address'] ?? '',
                $buyerCompany['city'] ?? '',
                $buyerCompany['province'] ?? '',
            ], function($v) { return !empty(trim((string)$v)); });
            $defaultShippingAddress = implode(', ', $addrParts);
        }

        if (empty($defaultShippingAddress) && !empty($buyerCompanyId)) {
            $orderModel = new OrderModel();
            $lastOrder = $orderModel->where('customer_company_id', $buyerCompanyId)
                                   ->where('shipping_address IS NOT NULL')
                                   ->where("shipping_address != ''")
                                   ->orderBy('id', 'DESC')
                                   ->first();
            if ($lastOrder && !empty($lastOrder['shipping_address'])) {
                $defaultShippingAddress = $lastOrder['shipping_address'];
            }
        }

        return view('marketplace/checkout', [
            'title'                  => 'Checkout Pengadaan B2B - AkuAda',
            'cart'                   => $cart,
            'subtotal'               => $subtotal,
            'ppnTotal'               => $ppnTotal,
            'shippingAmount'         => $shippingAmount,
            'grandTotal'             => $grandTotal,
            'buyerCompany'           => $buyerCompany,
            'hasCompany'             => $hasCompany,
            'isVerified'             => $isVerified,
            'isRegistered'           => $isRegistered,
            'defaultShippingAddress' => $defaultShippingAddress,
            'maxUnpaidLimit'     => $maxUnpaidLimit,
            'unpaidNotesCount'   => $unpaidNotesCount,
            'remainingNotes'     => $remainingNotes,
            'creditLimit'        => $creditLimit,
            'creditUsed'         => $creditUsed,
            'availableCredit'    => $availableCredit,
            'isNotesSufficient'  => $isNotesSufficient,
            'isAmountSufficient' => $isAmountSufficient,
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
        $shippingAddress = trim((string)$this->request->getPost('shipping_address'));
        $designSpecs = trim((string)$this->request->getPost('design_specs'));

        // Fallback default alamat pengiriman kantor jika kosong
        if (empty($shippingAddress) && $buyerCompanyId) {
            $buyerComp = (new CompanyModel())->find($buyerCompanyId);
            if ($buyerComp) {
                $addrParts = array_filter([
                    $buyerComp['address'] ?? '',
                    $buyerComp['city'] ?? '',
                    $buyerComp['province'] ?? '',
                ], function($v) { return !empty(trim((string)$v)); });
                $shippingAddress = implode(', ', $addrParts);
            }
        }

        // Handle optional design file upload from Buyer
        $designFileBuyer = null;
        $fileDesign = $this->request->getFile('design_file');
        if ($fileDesign && $fileDesign->isValid() && !$fileDesign->hasMoved()) {
            $ext = strtolower($fileDesign->getClientExtension());
            $allowedExts = ['pdf', 'png', 'jpg', 'jpeg', 'webp', 'zip', 'rar', 'ai', 'cdr', 'psd', 'svg'];
            if (in_array($ext, $allowedExts)) {
                $uploadDir = FCPATH . 'uploads/designs';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0775, true);
                }
                $newFileName = 'design_po_' . date('YmdHis') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                $fileDesign->move($uploadDir, $newFileName);
                $designFileBuyer = 'uploads/designs/' . $newFileName;
            }
        }
        $designStatus = (!empty($designSpecs) || !empty($designFileBuyer)) ? 'submitted_to_admin' : 'none';

        $isLoggedInCustomer = !empty(session('user_id')) && (session('user_role') === 'customer' || session('role') === 'customer');
        $buyerCompanyId = $isLoggedInCustomer ? (int)session('company_id') : null;
        $companyModel = new CompanyModel();
        $buyerCompany = null;
        $hasCompany = false;
        $isVerified = false;

        $defaultCreditLimit = (float)get_system_setting('default_credit_limit', 100000000);
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
                    $creditLimit = (float)(!empty($buyerCompany['credit_limit_top']) && $buyerCompany['credit_limit_top'] > 0 
                        ? $buyerCompany['credit_limit_top'] 
                        : $defaultCreditLimit);
                    $creditUsed = (float)($buyerCompany['credit_used_top'] ?? 0);
                    $availableCredit = max(0, $creditLimit - $creditUsed);

                    $isNotesSufficient = ($unpaidNotesCount < $maxUnpaidLimit);
                    $isAmountSufficient = ($grandTotal <= $availableCredit);
                    $isSufficientCredit = $isNotesSufficient && $isAmountSufficient;
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
            'design_file_buyer'      => $designFileBuyer,
            'design_specs'           => $designSpecs ?: null,
            'design_status'          => $designStatus,
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
            'PO resmi #' . $poNumber . ' berhasil diterbitkan oleh pembeli dengan metode pembayaran ' . strtoupper($paymentMethod) . '.' . ($designStatus === 'submitted_to_admin' ? ' Lampiran desain & spesifikasi custom telah dikirim ke Tim AkuAda untuk kurasi.' : ''),
            session('user_name') ?? 'Procurement Officer'
        );

        if ($designStatus === 'submitted_to_admin' && $buyerCompanyId) {
            $msgModel = new \App\Models\OrderMessageModel();
            $msgModel->addMessage(
                (int)$orderId,
                session('user_id') ? (int)session('user_id') : null,
                session('user_name') ?? 'PIC Procurement',
                'customer',
                session('company_name') ?? 'Corporate Buyer',
                "🎨 [Permintaan Desain Custom PO #{$poNumber}] Kami telah mengunggah spesifikasi desain barang pesanan untuk dikurasi oleh Tim AkuAda sebelum diteruskan ke vendor.\nSpesifikasi: " . ($designSpecs ?: 'Terlampir di file desain.'),
                $designFileBuyer,
                'admin_comp_' . $buyerCompanyId,
                (int)$buyerCompanyId,
                null,
                'admin',
                'Desain Custom PO #' . $poNumber
            );
        }

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

