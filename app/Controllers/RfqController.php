<?php

namespace App\Controllers;

use App\Models\RfqModel;
use App\Models\RfqItemModel;
use App\Models\CompanyModel;
use App\Models\ProductModel;

class RfqController extends BaseController
{
    public function index()
    {
        $rfqModel = new RfqModel();
        $companyId = session('company_id');
        $role = session('user_role') ?? 'customer';

        $rfqs = $rfqModel->getDetailedRfqs($companyId, $role);

        return view('rfq/index', [
            'title' => 'Daftar Permintaan Penawaran Resmi (RFQ) - AkuAda B2B',
            'rfqs'  => $rfqs,
            'role'  => $role
        ]);
    }

    public function create()
    {
        if (!session('is_logged_in') || session('user_role') !== 'customer') {
            session()->setFlashdata('error', 'Silakan masuk dengan akun Corporate Buyer untuk membuat dan mengajukan RFQ.');
            return redirect()->to('/auth/login');
        }

        $companyModel = new CompanyModel();
        $productModel = new ProductModel();

        $vendors = $companyModel->getVendors();
        $products = $productModel->where('status', 'active')->findAll();

        return view('rfq/create', [
            'title'    => 'Buat Permintaan Penawaran Resmi (RFQ) - AkuAda B2B',
            'vendors'  => $vendors,
            'products' => $products
        ]);
    }

    public function store()
    {
        if (!session('is_logged_in') || session('user_role') !== 'customer') {
            session()->setFlashdata('error', 'Silakan masuk dengan akun Corporate Buyer untuk membuat dan mengajukan RFQ.');
            return redirect()->to('/auth/login');
        }

        $title = $this->request->getPost('title');
        $vendorId = $this->request->getPost('vendor_company_id') ?: null;
        $notes = $this->request->getPost('notes');
        $targetDate = $this->request->getPost('target_delivery_date') ?: date('Y-m-d', strtotime('+14 days'));
        $designSpecs = trim((string)$this->request->getPost('design_specs'));

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
                $newFileName = 'design_rfq_' . date('YmdHis') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                $fileDesign->move($uploadDir, $newFileName);
                $designFileBuyer = 'uploads/designs/' . $newFileName;
            }
        }
        $designStatus = (!empty($designSpecs) || !empty($designFileBuyer)) ? 'submitted_to_admin' : 'none';
        
        $itemNames = $this->request->getPost('item_name') ?? [];
        $qtys = $this->request->getPost('qty') ?? [];
        $uoms = $this->request->getPost('uom') ?? [];
        $targetPrices = $this->request->getPost('target_unit_price') ?? [];
        $itemNotes = $this->request->getPost('item_notes') ?? [];

        $buyerCompanyId = ensure_role_session('customer');
        $rfqNumber = 'RFQ-' . date('Ym') . '-' . sprintf('%04d', rand(100, 9999));
        $now = date('Y-m-d H:i:s');

        $totalEstimate = 0;
        foreach ($qtys as $i => $q) {
            $price = (float)($targetPrices[$i] ?? 0);
            $totalEstimate += ((int)$q * $price);
        }

        $rfqModel = new RfqModel();
        $rfqId = $rfqModel->insert([
            'rfq_number'           => $rfqNumber,
            'customer_company_id'  => $buyerCompanyId,
            'vendor_company_id'    => $vendorId,
            'title'                => $title,
            'notes'                => $notes,
            'target_delivery_date' => $targetDate,
            'status'               => 'submitted',
            'total_estimate'       => $totalEstimate,
            'design_file_buyer'    => $designFileBuyer,
            'design_specs'         => $designSpecs ?: null,
            'design_status'        => $designStatus,
            'created_at'           => $now,
            'updated_at'           => $now,
        ]);

        $rfqItemModel = new RfqItemModel();
        foreach ($itemNames as $i => $name) {
            if (!empty(trim($name))) {
                $rfqItemModel->insert([
                    'rfq_id'            => $rfqId,
                    'product_id'        => null,
                    'item_name'         => $name,
                    'qty'               => max(1, (int)($qtys[$i] ?? 1)),
                    'uom'               => $uoms[$i] ?? 'Pcs',
                    'target_unit_price' => !empty($targetPrices[$i]) ? (float)$targetPrices[$i] : null,
                    'notes'             => $itemNotes[$i] ?? '',
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);
            }
        }

        session()->setFlashdata('success', 'Permintaan Penawaran (RFQ) #' . $rfqNumber . ' berhasil dikirim ke vendor!');
        return redirect()->to('/rfq/' . $rfqId);
    }

    public function detail($id)
    {
        $rfqModel = new RfqModel();
        $rfq = $rfqModel->getRfqWithItems($id);

        if (!$rfq) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('RFQ tidak ditemukan');
        }

        return view('rfq/detail', [
            'title' => 'Detail RFQ #' . $rfq['rfq_number'] . ' - AkuAda B2B',
            'rfq'   => $rfq
        ]);
    }

    /**
     * Download Surat Penawaran Resmi dari RFQ yang diajukan
     */
    public function quotation($id)
    {
        $rfqModel = new RfqModel();
        $rfq = $rfqModel->getRfqWithItems($id);

        if (!$rfq) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('RFQ tidak ditemukan');
        }

        $companyModel = new CompanyModel();
        $buyer = $companyModel->find($rfq['customer_company_id']);
        $vendor = $rfq['vendor_company_id'] ? $companyModel->find($rfq['vendor_company_id']) : null;

        $quotationItems = [];
        $subtotal = 0;
        $ppnTotal = 0;

        foreach ($rfq['items'] as $item) {
            $unitPrice = (float)($item['offered_unit_price'] ?? $item['target_unit_price'] ?? 0);
            $qty = (int)$item['qty'];
            $itemSubtotal = $unitPrice * $qty;
            $itemPpn = round($itemSubtotal * (get_ppn_rate() / 100), 2);

            $quotationItems[] = [
                'product_id'   => $item['product_id'],
                'name'         => $item['item_name'],
                'sku'          => 'SKU-RFQ-' . str_pad((string)$item['id'], 4, '0', STR_PAD_LEFT),
                'brand'        => 'Custom / Standard',
                'spec_details' => $item['notes'] ?? '',
                'uom'          => $item['uom'] ?? 'Pcs',
                'qty'          => $qty,
                'unit_price'   => $unitPrice,
                'subtotal'     => $itemSubtotal,
                'ppn_amount'   => $itemPpn,
                'vendor_name'  => $vendor['company_name'] ?? 'Mitra Vendor AkuAda',
                'image_url'    => '',
            ];

            $subtotal += $itemSubtotal;
            $ppnTotal += $itemPpn;
        }

        $shippingAmount = (float)get_system_setting('default_shipping_amount', 0);
        $grandTotal = $subtotal + $ppnTotal + $shippingAmount;

        $quotationNumber = 'QUO-RFQ/' . date('Ym', strtotime($rfq['created_at'])) . '/' . str_replace('RFQ-', '', $rfq['rfq_number']);
        $validUntil = date('Y-m-d', strtotime($rfq['created_at'] . ' +14 days'));

        $buyerAddress = '';
        if ($buyer) {
            $addrParts = array_filter([
                $buyer['address'] ?? '',
                $buyer['city'] ?? '',
                $buyer['province'] ?? '',
            ], function($v) { return !empty(trim((string)$v)); });
            
            $buyerAddress = implode(', ', $addrParts);
        }
        if (empty($buyerAddress)) {
            $buyerAddress = 'Alamat kantor / gudang belum dikonfigurasi di Profil Perusahaan';
        }

        $userModel = new UserModel();
        $user = null;
        if (!empty($rfq['user_id'])) {
            $user = $userModel->find($rfq['user_id']);
        } elseif (session('user_id')) {
            $user = $userModel->find(session('user_id'));
        }

        return view('marketplace/quotation', [
            'title'           => 'Surat Penawaran Harga RFQ #' . $rfq['rfq_number'] . ' - AkuAda B2B',
            'quotationNumber' => $quotationNumber,
            'quotationDate'   => date('Y-m-d', strtotime($rfq['created_at'])),
            'validUntil'      => $validUntil,
            'rfqRef'          => $rfq['rfq_number'],
            'items'           => $quotationItems,
            'subtotal'        => $subtotal,
            'ppnTotal'        => $ppnTotal,
            'shippingAmount'  => $shippingAmount,
            'grandTotal'      => $grandTotal,
            'buyer'           => [
                'company_name' => $buyer['company_name'] ?? 'Perusahaan Pembeli',
                'pic_name'     => $user['name'] ?? ($buyer['pic_name'] ?? 'Bpk/Ibu Pimpinan'),
                'position'     => $user['position'] ?? 'PIC Procurement',
                'phone'        => 'Live Chat Web Resmi (Terverifikasi)',
                'email'        => $buyer['email'] ?? ($user['email'] ?? '-'),
                'address'      => $buyerAddress,
            ],
        ]);
    }
}
