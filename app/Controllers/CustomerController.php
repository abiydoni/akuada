<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\RfqModel;
use App\Models\CompanyModel;

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

        return view('customer/order_detail', [
            'title' => 'Detail PO ' . $order['po_number'] . ' - AkuAda B2B',
            'order' => $order
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

    public function signBast()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $signerName = $this->request->getPost('signer_name') ?: session('user_name');

        $orderModel = new OrderModel();
        $orderModel->update($orderId, [
            'order_status' => 'bast_signed',
            'updated_at'   => date('Y-m-d H:i:s')
        ]);

        $logModel = new \App\Models\OrderStatusLogModel();
        $logModel->logStatus(
            $orderId, 
            'bast_signed', 
            'Pengesahan Berita Acara Serah Terima (BAST)', 
            'Barang telah diterima lengkap di gudang dan dokumen BAST disahkan secara digital oleh ' . $signerName . ' (Senior Procurement Manager).', 
            $signerName
        );

        session()->setFlashdata('success', 'Berita Acara Serah Terima (BAST) berhasil disahkan oleh ' . $signerName . '! Periode jatuh tempo Term of Payment resmi berjalan.');
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
        $rfqModel->update($rfqId, [
            'status'     => 'approved',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Penawaran harga dari vendor telah disetujui (Approved)! Anda dapat menerbitkan Purchase Order (PO).');
        return redirect()->back();
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

    public function creditLimit()
    {
        $buyerId = $this->getBuyerCompanyId();
        $companyModel = new CompanyModel();
        $company = $companyModel->find($buyerId);

        return view('customer/credit_limit', [
            'title'   => 'Pengajuan Plafon Kredit TOP - AkuAda B2B',
            'company' => $company
        ]);
    }

    public function requestLimitIncrease()
    {
        $requestedLimit = (float)$this->request->getPost('requested_limit');
        $notes = $this->request->getPost('notes');

        session()->setFlashdata('success', 'Pengajuan kenaikan plafon kredit TOP sebesar ' . format_rupiah($requestedLimit) . ' telah diajukan! Tim Credit Risk Analyst AkuAda akan melakukan verifikasi.');
        return redirect()->to('/customer/dashboard');
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
        $paymentBank = $this->request->getPost('payment_bank') ?: 'Bank Mandiri Escrow AkuAda';
        $paymentRef = $this->request->getPost('payment_ref') ?: ('TRF-BUYER-' . date('YmdHis'));

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if (!$order) {
            session()->setFlashdata('error', 'Tagihan PO tidak ditemukan.');
            return redirect()->back();
        }

        if ($order['payment_status'] !== 'paid') {
            $now = date('Y-m-d H:i:s');
            $orderModel->update($orderId, [
                'payment_status'     => 'paid',
                'buyer_paid_at'      => $now,
                'buyer_payment_bank' => $paymentBank,
                'buyer_payment_ref'  => $paymentRef,
                'updated_at'         => $now,
            ]);

            $companyModel = new CompanyModel();
            $company = $companyModel->find($order['customer_company_id']);
            if ($company) {
                $newUsed = max(0, (float)$company['credit_used_top'] - (float)$order['total_amount']);
                $companyModel->update($company['id'], ['credit_used_top' => $newUsed]);
                if (session('company_id') == $company['id']) {
                    session()->set('credit_used_top', $newUsed);
                }
            }

            $logModel = new \App\Models\OrderStatusLogModel();
            $logModel->logStatus(
                $orderId,
                $order['order_status'],
                'Pelunasan Hutang Tagihan PO oleh Buyer',
                'Pembayaran tagihan sebesar ' . format_rupiah($order['total_amount']) . ' telah dilunasi ke ' . $paymentBank . ' (No. Ref: ' . $paymentRef . '). Plafon kredit TOP telah dipulihkan.',
                session('user_name') ?? 'Corporate Buyer'
            );
        }

        session()->setFlashdata('success', 'Pembayaran hutang untuk PO #' . ($order['po_number'] ?? $order['order_number']) . ' berhasil diproses & plafon kredit TOP Anda telah dipulihkan!');
        return redirect()->to('/customer/payables');
    }
}
