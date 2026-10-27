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
            $ppnTotal += $item['ppn_amount'];
        }

        $grandTotal = $subtotal + $ppnTotal;

        return view('marketplace/cart', [
            'title'      => 'Keranjang Pengadaan PO - AkuAda B2B',
            'cart'       => $cart,
            'subtotal'   => $subtotal,
            'ppnTotal'   => $ppnTotal,
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

        $ppnAmount = $product['is_taxable'] ? ($unitPrice * $qty * ($product['ppn_percent'] / 100)) : 0;
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
            'ppn_percent'        => $product['ppn_percent'],
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

        $ppnAmount = $product['is_taxable'] ? ($unitPrice * $qty * ($product['ppn_percent'] / 100)) : 0;
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
            $ppnTotal += $item['ppn_amount'];
        }
        $grandTotal = $subtotal + $ppnTotal;

        // Auto assign buyer company if session exists, or default to PT Mega Solusi
        $companyModel = new CompanyModel();
        $buyerCompanyId = session('company_id') ?? 4;
        $buyerCompany = $companyModel->find($buyerCompanyId);

        return view('marketplace/checkout', [
            'title'        => 'Checkout Pengadaan B2B - AkuAda',
            'cart'         => $cart,
            'subtotal'     => $subtotal,
            'ppnTotal'     => $ppnTotal,
            'grandTotal'   => $grandTotal,
            'buyerCompany' => $buyerCompany
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

        $buyerCompanyId = session('company_id') ?? 4;

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
            $itemPpn = (float)$item['ppn_amount'];
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
            $feeCalc = calculate_vendor_fee($itemSubtotal, $customFeeRate);

            $totalVendorFee += $feeCalc['fee_amount'];
            $vendorNetPayout += $feeCalc['net_amount'];

            $processedItems[] = array_merge($item, [
                'fee_percent' => $feeCalc['percent'],
                'fee_amount'  => $feeCalc['fee_amount'],
                'net_amount'  => $feeCalc['net_amount'],
            ]);
        }
        $grandTotal = $subtotal + $ppnTotal;

        // Determine due date according to payment method
        $days = 30;
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
            'shipping_amount'      => 0.00,
            'total_amount'         => $grandTotal,
            'payment_method'       => $paymentMethod,
            'payment_status'       => 'pending_approval',
            'order_status'         => 'po_issued',
            'tax_invoice_required' => $taxInvoiceRequired,
            'customer_notes'       => $customerNotes,
            'shipping_address'     => $shippingAddress,
            'bast_number'          => 'BAST/AKUADA/' . date('Ym') . '/' . rand(100, 999),
            'invoice_number'       => 'INV/AKUADA/' . date('Ym') . '/' . rand(100, 999),
            'tax_invoice_number'   => '010.002-26.' . rand(10000000, 99999999),
            'due_date'             => $dueDate,
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
        $companyModel = new CompanyModel();
        $company = $companyModel->find($buyerCompanyId);
        if ($company && str_starts_with($paymentMethod, 'top_')) {
            $newUsed = (float)$company['credit_used_top'] + $grandTotal;
            $companyModel->update($buyerCompanyId, ['credit_used_top' => $newUsed]);
            session()->set('credit_used_top', $newUsed);
        }

        // Clear cart
        session()->remove('cart');

        session()->setFlashdata('success', 'Purchase Order (PO) #' . $poNumber . ' Berhasil Diterbitkan! Vendor sedang memproses pesanan dan dokumen serah terima (BAST).');
        return redirect()->to('/customer/orders/' . $orderId);
    }
}
