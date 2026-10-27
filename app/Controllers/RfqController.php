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
        $title = $this->request->getPost('title');
        $vendorId = $this->request->getPost('vendor_company_id') ?: null;
        $notes = $this->request->getPost('notes');
        $targetDate = $this->request->getPost('target_delivery_date') ?: date('Y-m-d', strtotime('+14 days'));
        
        $itemNames = $this->request->getPost('item_name') ?? [];
        $qtys = $this->request->getPost('qty') ?? [];
        $uoms = $this->request->getPost('uom') ?? [];
        $targetPrices = $this->request->getPost('target_unit_price') ?? [];
        $itemNotes = $this->request->getPost('item_notes') ?? [];

        $buyerCompanyId = session('company_id') ?? 4;
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
}
