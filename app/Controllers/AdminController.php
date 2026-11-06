<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\OrderStatusLogModel;
use App\Models\CompanyModel;
use App\Models\ProductModel;
use App\Models\ProductPriceTierModel;
use App\Models\CategoryModel;
use App\Models\RfqModel;
use App\Models\RfqItemModel;
use App\Models\SettingModel;
use App\Models\UserModel;
use App\Models\OrderReviewModel;
use App\Models\OrderMessageModel;
use App\Models\OrderReturnModel;
use App\Models\BuyerWithdrawalModel;
use App\Models\BuyerCashbackMutationModel;

class AdminController extends BaseController
{
    public function dashboard()
    {
        $orderModel = new OrderModel();
        $companyModel = new CompanyModel();
        $productModel = new ProductModel();
        $rfqModel = new RfqModel();

        sync_orders_with_system_settings();
        $totalGmv = $orderModel->selectSum('total_amount')->first()['total_amount'] ?? 0;
        $totalPlatformFee = $orderModel->selectSum('total_vendor_fee')->first()['total_vendor_fee'] ?? 0;
        $totalVendorPayout = $orderModel->selectSum('vendor_net_payout')->first()['vendor_net_payout'] ?? 0;
        $totalOrders = $orderModel->countAllResults();
        $totalVendors = $companyModel->where('type', 'vendor')->countAllResults();
        $totalBuyers = $companyModel->where('type', 'buyer')->countAllResults();
        $totalProducts = $productModel->countAllResults();
        $totalRfqs = $rfqModel->countAllResults();

        $recentOrders = $orderModel->getDetailedOrders();
        $vendors = $companyModel->getVendors();
        $buyers = $companyModel->getBuyers();

        return view('admin/dashboard', [
            'title'             => 'Dashboard Utama - Admin AkuAda',
            'totalGmv'          => $totalGmv,
            'totalPlatformFee'  => $totalPlatformFee,
            'totalVendorPayout' => $totalVendorPayout,
            'totalOrders'       => $totalOrders,
            'totalVendors'      => $totalVendors,
            'totalBuyers'       => $totalBuyers,
            'totalProducts'     => $totalProducts,
            'totalRfqs'         => $totalRfqs,
            'recentOrders'      => array_slice($recentOrders, 0, 5),
            'vendors'           => $vendors,
            'buyers'            => $buyers,
        ]);
    }

    public function transactions()
    {
        $orderModel = new OrderModel();
        $orders = $orderModel->getDetailedOrders();

        return view('admin/transactions', [
            'title'  => 'Manajemen Transaksi & Purchase Order B2B - Admin AkuAda',
            'orders' => $orders
        ]);
    }

    public function invoiceCustomer($id)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice pelanggan tidak ditemukan');
        }

        return view('customer/invoice', [
            'title'   => '[Admin] Faktur Penjualan AkuAda ke Pelanggan - ' . ($order['invoice_number'] ?? $order['po_number']),
            'order'   => $order,
            'isAdmin' => true,
            'backUrl' => base_url('admin/transactions')
        ]);
    }

    public function invoiceVendor($id)
    {
        $orderModel = new OrderModel();
        $order = $orderModel->getOrderWithItems($id);

        if (!$order) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Invoice vendor tidak ditemukan');
        }

        return view('vendor/invoice', [
            'title'   => '[Admin] Faktur Penagihan Vendor ke AkuAda - ' . ($order['vendor_invoice_number'] ?? $order['po_number']),
            'order'   => $order,
            'isAdmin' => true,
            'backUrl' => base_url('admin/transactions')
        ]);
    }

    public function updateOrderStatus()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $status = $this->request->getPost('order_status');
        $paymentStatus = $this->request->getPost('payment_status');

        $bastNumber       = $this->request->getPost('bast_number');
        $taxInvoiceNumber = $this->request->getPost('tax_invoice_number');

        $orderModel = new OrderModel();
        $data = ['order_status' => $status];
        if ($paymentStatus)    { $data['payment_status']      = $paymentStatus; }
        if ($bastNumber)       { $data['bast_number']         = $bastNumber; }
        if ($taxInvoiceNumber) { $data['tax_invoice_number']  = $taxInvoiceNumber; }
        $shippingAmount = $this->request->getPost('shipping_amount');
        if ($shippingAmount !== null && $shippingAmount !== '') {
            $data['shipping_amount'] = max(0, (float)$shippingAmount);
        }

        $orderModel->update($orderId, $data);
        sync_orders_with_system_settings();

        $statusLabels = [
            'po_issued'       => 'Penerbitan PO',
            'po_approved'     => 'PO Dikonfirmasi Vendor',
            'in_production'   => 'Proses Produksi / Penyiapan Barang',
            'quality_control' => 'Pemeriksaan Mutu & Standar (QC)',
            'packaging'       => 'Pengemasan & Paletisasi Master Box',
            'shipped'         => 'Dalam Pengiriman (Surat Jalan Diterbitkan)',
            'delivered'       => 'Barang Tiba di Lokasi Buyer',
            'bast_signed'     => 'Pengesahan BAST',
            'completed'       => 'Selesai & Lunas',
            'return_requested'    => 'Klaim Retur Diajukan Buyer',
            'return_approved'     => 'Klaim Retur Disetujui Vendor',
            'replacement_shipped' => 'Barang Pengganti Dikirim Vendor',
            'return_completed'    => 'Klaim Retur Selesai',
            'return_rejected'     => 'Klaim Retur Ditolak Vendor',
            'cancelled'       => 'Pesanan Dibatalkan',
        ];
        $statusTitle = $statusLabels[$status] ?? ucfirst($status);

        $logModel = new OrderStatusLogModel();
        $logModel->logStatus($orderId, $status, $statusTitle, 'Status diperbarui oleh Superadmin Platform AkuAda.', session('user_name') ?? 'Superadmin');

        session()->setFlashdata('success', 'Status PO dan Transaksi berhasil diperbarui menjadi: ' . $statusTitle);
        return redirect()->back();
    }

    public function vendors()
    {
        $companyModel = new CompanyModel();
        $vendors = $companyModel->getVendors();

        return view('admin/vendors', [
            'title'   => 'Verifikasi & Manajemen Mitra Vendor - Admin AkuAda',
            'vendors' => $vendors
        ]);
    }

    public function createVendor()
    {
        return view('admin/vendor_create', [
            'title'      => 'Daftarkan Mitra Vendor Baru - Superadmin AkuAda',
            'defaultFee' => get_default_vendor_fee_rate(),
        ]);
    }

    public function storeVendor()
    {
        $companyName = $this->request->getPost('company_name');
        $legalType = $this->request->getPost('legal_type') ?? 'PT';
        $npwp = $this->request->getPost('npwp');
        $nib = $this->request->getPost('nib');
        $address = $this->request->getPost('address');
        $city = $this->request->getPost('city');
        $province = $this->request->getPost('province') ?? 'DKI Jakarta';
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');
        $picName = $this->request->getPost('pic_name');
        $picPhone = $this->request->getPost('pic_phone');
        $picEmail = $this->request->getPost('pic_email') ?: $email;
        $commissionRate = (float)$this->request->getPost('commission_rate');
        $password = $this->request->getPost('password') ?: get_system_setting('default_account_password', 'admin123');
        $isVerified = $this->request->getPost('is_verified') ? 1 : 1;

        $now = date('Y-m-d H:i:s');

        $companyModel = new CompanyModel();
        $companyId = $companyModel->insert([
            'type'             => 'vendor',
            'company_name'     => $companyName,
            'legal_type'       => $legalType,
            'npwp'             => $npwp,
            'nib'              => $nib,
            'address'          => $address,
            'city'             => $city,
            'province'         => $province,
            'phone'            => $phone,
            'email'            => $email,
            'pic_name'         => $picName,
            'pic_phone'        => $picPhone,
            'pic_email'        => $picEmail,
            'credit_limit_top' => 0.00,
            'credit_used_top'  => 0.00,
            'commission_rate'  => $commissionRate,
            'is_verified'      => $isVerified,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        $userModel = new UserModel();
        $userModel->insert([
            'name'       => $picName . ' (' . $companyName . ')',
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_BCRYPT),
            'role'       => 'vendor',
            'phone'      => $picPhone,
            'company_id' => $companyId,
            'position'   => 'Supplier Lead',
            'avatar'     => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=200&q=80',
            'status'     => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        session()->setFlashdata('success', 'Mitra Vendor ' . $companyName . ' berhasil didaftarkan dengan tarif kesepakatan komisi ' . number_format($commissionRate, 1) . '%! Vendor dapat login dengan email: ' . $email);
        return redirect()->to('/admin/vendors');
    }

    public function updateVendorFee()
    {
        $vendorId = (int)$this->request->getPost('vendor_id');
        $commissionRate = (float)$this->request->getPost('commission_rate');
        $syncProducts = $this->request->getPost('sync_products') ? true : false;

        $companyModel = new CompanyModel();
        $vendor = $companyModel->find($vendorId);

        if ($vendor) {
            $companyModel->update($vendorId, [
                'commission_rate' => $commissionRate,
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

            if ($syncProducts) {
                $productModel = new ProductModel();
                $productModel->where('vendor_company_id', $vendorId)
                             ->set(['vendor_fee_percent' => $commissionRate])
                             ->update();
            }

            session()->setFlashdata('success', 'Tarif fee kesepakatan untuk ' . $vendor['company_name'] . ' berhasil diperbarui menjadi ' . number_format($commissionRate, 1) . '%!');
        }

        return redirect()->back();
    }

    public function toggleVendorVerification($id)
    {
        $companyModel = new CompanyModel();
        $vendor = $companyModel->find($id);
        if ($vendor) {
            $newStatus = $vendor['is_verified'] ? 0 : 1;
            $companyModel->update($id, ['is_verified' => $newStatus]);
            session()->setFlashdata('toast', 'Status verifikasi legalitas vendor berhasil diubah.');
        }
        return redirect()->back();
    }

    public function toggleBuyerVerification($id)
    {
        $companyModel = new CompanyModel();
        $buyer = $companyModel->find($id);
        if ($buyer && $buyer['type'] === 'buyer') {
            $newStatus = $buyer['is_verified'] ? 0 : 1;
            $companyModel->update($id, [
                'is_verified' => $newStatus,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            $msg = $newStatus 
                ? 'Status legalitas ' . $buyer['company_name'] . ' berhasil diverifikasi dan disahkan! Buyer kini resmi terverifikasi.' 
                : 'Status verifikasi legalitas ' . $buyer['company_name'] . ' telah dinonaktifkan.';
            session()->setFlashdata('success', $msg);
        }
        return redirect()->back();
    }

    public function buyers()
    {
        $companyModel = new CompanyModel();
        $buyers = $companyModel->getBuyers();

        return view('admin/buyers', [
            'title'  => 'Manajemen Perusahaan Buyer & Plafon Kredit TOP - Admin AkuAda',
            'buyers' => $buyers
        ]);
    }

    public function createBuyer()
    {
        $defaultCredit = (float)get_system_setting('default_credit_limit', 100000000);
        return view('admin/buyer_create', [
            'title'         => 'Daftarkan Perusahaan Buyer Baru & Alokasi Kredit - Superadmin AkuAda',
            'defaultCredit' => $defaultCredit,
        ]);
    }

    public function storeBuyer()
    {
        $companyName = $this->request->getPost('company_name');
        $legalType = $this->request->getPost('legal_type') ?? 'PT';
        $npwp = $this->request->getPost('npwp');
        $nib = $this->request->getPost('nib');
        $address = $this->request->getPost('address');
        $city = $this->request->getPost('city') ?? 'Jakarta Selatan';
        $province = $this->request->getPost('province') ?? 'DKI Jakarta';
        $phone = $this->request->getPost('phone');
        $website = $this->request->getPost('website');
        $businessField = $this->request->getPost('business_field') ?: 'Perdagangan Umum & Jasa';
        $email = $this->request->getPost('email');
        $picName = $this->request->getPost('pic_name');
        $picPhone = $this->request->getPost('pic_phone');
        $picEmail = $this->request->getPost('pic_email') ?: $email;
        $creditLimit = (float)$this->request->getPost('credit_limit_top');
        $cashbackRateInput = $this->request->getPost('cashback_rate');
        $cashbackRate = ($cashbackRateInput !== null && $cashbackRateInput !== '') ? (float)$cashbackRateInput : null;
        $password = $this->request->getPost('password') ?: get_system_setting('default_account_password', 'admin123');
        $position = $this->request->getPost('position') ?: 'Procurement Manager';

        $slug = url_title($companyName, '-', true);
        $npwpPath   = $this->saveUploadedFile('npwp_file', 'npwp', $slug);
        $nibPath    = $this->saveUploadedFile('nib_file', 'nib', $slug);
        $deedPath   = $this->saveUploadedFile('deed_file', 'akta', $slug);
        $idCardPath = $this->saveUploadedFile('id_card_file', 'ktp', $slug);
        $sppkpPath  = $this->saveUploadedFile('sppkp_file', 'sppkp', $slug);

        $now = date('Y-m-d H:i:s');

        $companyModel = new CompanyModel();
        $companyId = $companyModel->insert([
            'type'             => 'buyer',
            'company_name'     => $companyName,
            'legal_type'       => $legalType,
            'npwp'             => $npwp,
            'nib'              => $nib,
            'business_field'   => $businessField,
            'address'          => $address,
            'city'             => $city,
            'province'         => $province,
            'phone'            => $phone,
            'website'          => $website,
            'email'            => $email,
            'pic_name'         => $picName,
            'pic_phone'        => $picPhone,
            'pic_email'        => $picEmail,
            'credit_limit_top' => $creditLimit,
            'credit_used_top'  => 0.00,
            'cashback_rate'    => $cashbackRate,
            'cashback_balance' => 0.00,
            'commission_rate'  => 0.00,
            'is_verified'      => 1,
            'npwp_file'        => $npwpPath,
            'nib_file'         => $nibPath,
            'deed_file'        => $deedPath,
            'id_card_file'     => $idCardPath,
            'sppkp_file'       => $sppkpPath,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        $userModel = new UserModel();
        $userModel->insert([
            'name'       => $picName . ' (' . $companyName . ')',
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_BCRYPT),
            'role'       => 'customer',
            'phone'      => $picPhone,
            'company_id' => $companyId,
            'position'   => $position,
            'avatar'     => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=200&q=80',
            'status'     => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        session()->setFlashdata('success', 'Perusahaan Buyer ' . $companyName . ' berhasil didaftarkan dengan fasilitas kredit TOP sebesar ' . format_rupiah($creditLimit) . '! Tim procurement dapat login dengan email: ' . $email);
        return redirect()->to('/admin/buyers');
    }

    public function updateBuyerLimit()
    {
        $companyId = (int)$this->request->getPost('company_id');
        $newLimit = (float)$this->request->getPost('credit_limit_top');

        $companyModel = new CompanyModel();
        $companyModel->update($companyId, ['credit_limit_top' => $newLimit]);

        session()->setFlashdata('success', 'Plafon Kredit TOP berhasil diperbarui menjadi ' . format_rupiah($newLimit));
        return redirect()->back();
    }

    public function updateBuyerCashbackRate()
    {
        $companyId = (int)$this->request->getPost('company_id');
        $cashbackRateInput = $this->request->getPost('cashback_rate');

        $companyModel = new CompanyModel();
        $buyer = $companyModel->find($companyId);

        if (!$buyer || $buyer['type'] !== 'buyer') {
            session()->setFlashdata('error', 'Perusahaan buyer tidak ditemukan.');
            return redirect()->back();
        }

        $rateValue = ($cashbackRateInput === '' || $cashbackRateInput === null) ? null : max(0.0, (float)$cashbackRateInput);

        $companyModel->update($companyId, [
            'cashback_rate' => $rateValue,
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);

        $displayRate = ($rateValue !== null) ? number_format($rateValue, 2) . '%' : 'Default Sistem (' . get_system_setting('buyer_cashback_rate', 1.0) . '%)';
        session()->setFlashdata('success', 'Persentase cashback untuk ' . $buyer['company_name'] . ' berhasil diperbarui menjadi: ' . $displayRate);

        return redirect()->back();
    }

    public function buyerWithdrawals()
    {
        $withdrawalModel = new BuyerWithdrawalModel();
        $withdrawals = $withdrawalModel->getWithdrawalsWithCompany();

        $companyModel = new CompanyModel();
        $totalCashbackAllBuyers = (float)($companyModel->where('type', 'buyer')->selectSum('cashback_balance')->first()['cashback_balance'] ?? 0);

        $pendingCount = 0;
        $totalPending = 0.0;
        $totalCompleted = 0.0;

        foreach ($withdrawals as $w) {
            if ($w['status'] === 'pending') {
                $pendingCount++;
                $totalPending += (float)$w['amount'];
            } elseif ($w['status'] === 'completed') {
                $totalCompleted += (float)$w['amount'];
            }
        }

        return view('admin/buyer_withdrawals', [
            'title'                  => 'Pencairan Saldo Cashback Buyer - Admin AkuAda',
            'withdrawals'            => $withdrawals,
            'pendingCount'           => $pendingCount,
            'totalPending'           => $totalPending,
            'totalCompleted'         => $totalCompleted,
            'totalCashbackAllBuyers' => $totalCashbackAllBuyers,
        ]);
    }

    public function processBuyerWithdrawal()
    {
        $withdrawalId = (int)$this->request->getPost('withdrawal_id');
        $action = $this->request->getPost('action'); // 'approve' or 'reject'
        $adminNotes = trim((string)$this->request->getPost('admin_notes'));
        $transferProof = trim((string)$this->request->getPost('transfer_proof'));

        $withdrawalModel = new BuyerWithdrawalModel();
        $withdrawal = $withdrawalModel->find($withdrawalId);

        if (!$withdrawal) {
            session()->setFlashdata('error', 'Data pengajuan penarikan dana tidak ditemukan.');
            return redirect()->to('/admin/buyer-withdrawals');
        }

        if ($withdrawal['status'] !== 'pending') {
            session()->setFlashdata('warning', 'Pengajuan penarikan ini sudah diproses sebelumnya.');
            return redirect()->to('/admin/buyer-withdrawals');
        }

        $companyModel = new CompanyModel();
        $company = $companyModel->find($withdrawal['company_id']);

        if (!$company) {
            session()->setFlashdata('error', 'Perusahaan buyer tidak ditemukan.');
            return redirect()->to('/admin/buyer-withdrawals');
        }

        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $currentAdminName = session('user_name') ?: 'Finance Admin';

        if ($action === 'approve') {
            $amount = (float)$withdrawal['amount'];
            $currentBalance = (float)($company['cashback_balance'] ?? 0);

            if ($currentBalance < $amount) {
                session()->setFlashdata('error', 'Gagal memproses penarikan: Saldo cashback buyer saat ini (' . format_rupiah($currentBalance) . ') kurang dari nominal penarikan (' . format_rupiah($amount) . ').');
                return redirect()->to('/admin/buyer-withdrawals');
            }

            $db->transStart();

            $newBalance = $currentBalance - $amount;
            $companyModel->update($withdrawal['company_id'], [
                'cashback_balance' => $newBalance,
                'updated_at'       => $now,
            ]);

            $withdrawalModel->update($withdrawalId, [
                'status'         => 'completed',
                'admin_notes'    => $adminNotes ?: 'Transfer penarikan cashback telah berhasil diproses ke rekening buyer.',
                'transfer_proof' => $transferProof ?: 'TRF-' . date('YmdHis'),
                'processed_by'   => $currentAdminName,
                'processed_at'   => $now,
                'updated_at'     => $now,
            ]);

            $mutationModel = new BuyerCashbackMutationModel();
            $mutationModel->recordMutation(
                (int)$withdrawal['company_id'],
                'debit',
                $amount,
                $newBalance,
                'Penarikan cashback #' . $withdrawal['withdrawal_number'] . ' ke ' . $withdrawal['bank_name'] . ' ' . $withdrawal['bank_account_number'] . ' a/n ' . $withdrawal['bank_account_holder'] . ' (Ref: ' . ($transferProof ?: '-') . ')',
                null,
                $withdrawalId
            );

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Terjadi kesalahan sistem saat memproses transaksi penarikan.');
            } else {
                session()->setFlashdata('success', 'Pencairan cashback #' . $withdrawal['withdrawal_number'] . ' sebesar ' . format_rupiah($amount) . ' untuk ' . $company['company_name'] . ' berhasil disetujui! Saldo buyer telah otomatis terpotong.');
            }
        } elseif ($action === 'reject') {
            $withdrawalModel->update($withdrawalId, [
                'status'         => 'rejected',
                'admin_notes'    => $adminNotes ?: 'Pengajuan penarikan dana ditolak oleh finance.',
                'processed_by'   => $currentAdminName,
                'processed_at'   => $now,
                'updated_at'     => $now,
            ]);

            session()->setFlashdata('success', 'Pengajuan penarikan dana #' . $withdrawal['withdrawal_number'] . ' telah ditolak. Saldo buyer tetap utuh.');
        }

        return redirect()->to('/admin/buyer-withdrawals');
    }

    public function approveOverlimit()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if ($order) {
            $companyModel = new CompanyModel();
            $buyer = $companyModel->find($order['customer_company_id']);
            if ($buyer) {
                $neededLimit = max((float)$buyer['credit_limit_top'], (float)$buyer['credit_used_top']);
                $step = max(1000000, (float)get_system_setting('credit_step_amount', 25000000));
                $newApprovedLimit = ceil($neededLimit / $step) * $step;
                $companyModel->update($buyer['id'], ['credit_limit_top' => $newApprovedLimit]);
            }

            $cleanNotes = preg_replace('/\[OVER-LIMIT REQUEST:[^\]]+\]\s*/', '[OVER-LIMIT DISETUJUI ADMIN] ', (string)($order['customer_notes'] ?? ''));
            $orderModel->update($orderId, [
                'order_status'   => ($order['order_status'] === 'po_issued') ? 'po_approved' : $order['order_status'],
                'customer_notes' => $cleanNotes,
            ]);

            $logModel = new OrderStatusLogModel();
            $logModel->logStatus($orderId, 'po_approved', 'Persetujuan Over-Limit Plafon Kredit TOP', 'Superadmin menyetujui pengajuan Over-Limit PO dan menaikkan plafon kredit buyer.', session('user_name') ?? 'Superadmin');

            session()->setFlashdata('success', 'Pengajuan Over-Limit PO berhasil disetujui dan plafon kredit perusahaan buyer telah dinaikkan secara otomatis!');
        }
        return redirect()->back();
    }

    protected function _unusedPlaceholder()
    {
        return redirect()->back();
    }

    public function products()
    {
        $productModel = new ProductModel();
        $products = $productModel->getAllProductsForAdmin(100);

        return view('admin/products', [
            'title'    => 'Pengawasan Master Barang Pengadaan - Admin AkuAda',
            'products' => $products
        ]);
    }

    public function downloadTemplateCsv()
    {
        $companyModel = new CompanyModel();
        $sampleVendors = $companyModel->where('type', 'vendor')->findAll(3);
        $v1 = !empty($sampleVendors[0]['company_name']) ? $sampleVendors[0]['company_name'] : 'PT Mitra Pemasok Mandiri';
        $v2 = !empty($sampleVendors[1]['company_name']) ? $sampleVendors[1]['company_name'] : 'CV Berkah Teknik Sentosa';
        $v3 = !empty($sampleVendors[2]['company_name']) ? $sampleVendors[2]['company_name'] : $v1;

        $headers = [
            'vendor',
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
            'bisa_sample',
            'deskripsi',
            'spesifikasi',
            'gambar_url'
        ];

        $sampleRows = [
            [
                $v1,
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
                '1',
                'Kertas HVS putih bersih ukuran A4 dengan gramatur 80 gsm premium untuk cetak dokumen resmi kantor.',
                'Ukuran: 210 x 297 mm; Ketebalan: 80 gsm; Kemasan: 1 Box isi 5 Rim @ 500 lembar (Total 2.500 lembar); Sertifikasi PEFC & ISO 9001',
                'https://images.unsplash.com/photo-1589330694653-dad6d3240e2b?auto=format&fit=crop&w=600&q=80'
            ],
            [
                $v2,
                'Komputer & IT Infrastructure',
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
                '0',
                'Paket keyboard dan mouse wireless 2.4 GHz dengan baterai tahan lama dan koneksi stabil hingga 10 meter.',
                'Koneksi: USB Receiver 2.4GHz; Jangkauan: 10 meter; Baterai: 2x AAA (Keyboard), 1x AA (Mouse); Kompatibel: Windows 10/11 & macOS; Garansi resmi 1 tahun',
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=600&q=80'
            ],
            [
                $v3,
                'Perlengkapan K3 & Safety APD',
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
                '1',
                'Helm keselamatan kerja standar SNI dan ANSI dengan suspensi putar Fastrac untuk perlindungan benturan di area industri dan konstruksi.',
                'Material: Polietilen kepadatan tinggi (HDPE); Standar: SNI ISO 3873 & ANSI/ISEA Z89.1; Suspensi: 4 titik Fastrac putar; Warna: Putih',
                'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=600&q=80'
            ]
        ];

        $filename = 'template_import_produk_superadmin_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM
        fputcsv($out, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($out, $row);
        }
        fclose($out);
        exit;
    }

    public function importCsv()
    {
        $file = $this->request->getFile('csv_file');

        if (!$file || !$file->isValid()) {
            session()->setFlashdata('error', 'File CSV tidak ditemukan atau gagal diunggah.');
            return redirect()->to('/admin/products');
        }

        $extension = strtolower($file->getClientExtension());
        if ($extension !== 'csv' && $extension !== 'txt') {
            session()->setFlashdata('error', 'Format file harus berekstensi .csv');
            return redirect()->to('/admin/products');
        }

        $tempPath = $file->getTempName();
        $handle = fopen($tempPath, 'r');
        if (!$handle) {
            session()->setFlashdata('error', 'Gagal membaca file CSV yang diunggah.');
            return redirect()->to('/admin/products');
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
            return redirect()->to('/admin/products');
        }

        $headerMap = [];
        foreach ($header as $colIdx => $colName) {
            $cleanName = strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '', str_replace([' ', '-'], '_', $colName))));
            $headerMap[$cleanName] = $colIdx;
        }

        $companyModel = new CompanyModel();
        $allVendors = $companyModel->where('type', 'vendor')->findAll();
        $defaultVendor = !empty($allVendors) ? $allVendors[0] : null;

        $categoryModel = new CategoryModel();
        $allCategories = $categoryModel->findAll();
        $defaultCategory = !empty($allCategories) ? $allCategories[0] : null;

        $productModel = new ProductModel();
        $now = date('Y-m-d H:i:s');
        $importedCount = 0;
        $skippedCount = 0;

        $db = \Config\Database::connect();
        $db->transStart();

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
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

            // Vendor Matching
            $vendorIdentifier = $getVal('vendor') ?: $getVal('vendor_name') ?: $getVal('nama_vendor') ?: $getVal('vendor_id');
            $matchedVendor = null;
            if (!empty($vendorIdentifier)) {
                if (is_numeric($vendorIdentifier)) {
                    foreach ($allVendors as $v) {
                        if ((int)$v['id'] === (int)$vendorIdentifier) {
                            $matchedVendor = $v;
                            break;
                        }
                    }
                }
                if (!$matchedVendor) {
                    foreach ($allVendors as $v) {
                        if (strcasecmp($v['company_name'], $vendorIdentifier) === 0 || stripos($v['company_name'], $vendorIdentifier) !== false || stripos($vendorIdentifier, $v['company_name']) !== false) {
                            $matchedVendor = $v;
                            break;
                        }
                    }
                }
            }

            if (!$matchedVendor && $defaultVendor) {
                $matchedVendor = $defaultVendor;
            }

            if (!$matchedVendor) {
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
            $isSample = in_array(strtolower($getVal('bisa_sample') ?: $getVal('is_sample_available') ?: '1'), ['1', 'ya', 'yes', 'true']) ? 1 : 0;
            $desc = $getVal('deskripsi') ?: $getVal('description') ?: ('Pengadaan ' . $name . ' berkualitas untuk kebutuhan korporat.');
            $spec = $getVal('spesifikasi') ?: $getVal('spec_details') ?: '';
            $imageUrl = $getVal('gambar_url') ?: $getVal('image_url') ?: 'https://images.unsplash.com/photo-1589330694653-dad6d3240e2b?auto=format&fit=crop&w=600&q=80';

            $slug = url_title($name, '-', true) . '-' . rand(1000, 9999);
            $contractFee = (float)($matchedVendor['commission_rate'] ?? get_default_vendor_fee_rate());

            $productModel->insert([
                'vendor_company_id'   => (int)$matchedVendor['id'],
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

        fclose($handle);
        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Terjadi kesalahan pada database saat memproses import barang.');
        } else {
            $msg = "Superadmin berhasil mengimpor $importedCount master barang ke katalog B2B!";
            if ($skippedCount > 0) {
                $msg .= " ($skippedCount baris dilewati karena format tidak sesuai/vendor tidak ditemukan)";
            }
            session()->setFlashdata('success', $msg);
        }

        return redirect()->to('/admin/products');
    }

    public function toggleProductStatus($id)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($id);
        if ($product) {
            $newStatus = ($product['status'] === 'active') ? 'inactive' : 'active';
            $productModel->update($id, ['status' => $newStatus]);
            session()->setFlashdata('success', 'Status produk "' . $product['name'] . '" diubah menjadi ' . strtoupper($newStatus));
        }
        return redirect()->back();
    }

    public function deleteProduct($id)
    {
        $productModel = new ProductModel();
        $product = $productModel->find($id);
        if ($product) {
            $productModel->update($id, ['status' => 'inactive']);
            session()->setFlashdata('toast', 'Barang "' . $product['name'] . '" dinonaktifkan dari katalog.');
        }
        return redirect()->back();
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
        $vendorCompany = $companyModel->find($order['vendor_company_id']);

        return view('admin/order_detail', [
            'title' => 'Detail PO & Audit Log Transaksi #' . ($order['po_number'] ?? $order['order_number']) . ' - Superadmin AkuAda',
            'order'  => $order,
            'vendorCompany' => $vendorCompany,
            'review'       => $review,
            'returns'      => $returns,
            'latestReturn' => $latestReturn,
        ]);
    }

    public function statusLogs()
    {
        $logModel = new OrderStatusLogModel();
        $logs = $logModel->select('order_status_logs.*, orders.po_number, orders.order_number, buyer.company_name as buyer_name, vendor.company_name as vendor_name')
                         ->join('orders', 'orders.id = order_status_logs.order_id', 'left')
                         ->join('companies as buyer', 'buyer.id = orders.customer_company_id', 'left')
                         ->join('companies as vendor', 'vendor.id = orders.vendor_company_id', 'left')
                         ->orderBy('order_status_logs.id', 'DESC')
                         ->findAll(100);

        return view('admin/status_logs', [
            'title' => 'Audit Log Status Transaksi B2B (Timeline History) - Admin AkuAda',
            'logs'  => $logs
        ]);
    }

    public function categories()
    {
        $catModel = new CategoryModel();
        $categories = $catModel->getWithCount();

        return view('admin/categories', [
            'title'      => 'Manajemen Kategori Pengadaan B2B - Admin AkuAda',
            'categories' => $categories,
        ]);
    }

    public function storeCategory()
    {
        $name = $this->request->getPost('name');
        $slug = $this->request->getPost('slug') ?: url_title($name, '-', true);
        $icon = $this->request->getPost('icon') ?: 'category';
        $desc = $this->request->getPost('description');
        $order = (int)($this->request->getPost('sort_order') ?? 0);

        $catModel = new CategoryModel();
        $catModel->insert([
            'name'        => $name,
            'slug'        => $slug,
            'icon'        => $icon,
            'description' => $desc,
            'sort_order'  => $order,
        ]);

        session()->setFlashdata('success', 'Kategori baru "' . $name . '" berhasil ditambahkan!');
        return redirect()->to('/admin/categories');
    }

    public function updateCategory($id)
    {
        $name = $this->request->getPost('name');
        $slug = $this->request->getPost('slug') ?: url_title($name, '-', true);
        $icon = $this->request->getPost('icon') ?: 'category';
        $desc = $this->request->getPost('description');
        $order = (int)($this->request->getPost('sort_order') ?? 0);

        $catModel = new CategoryModel();
        $catModel->update($id, [
            'name'        => $name,
            'slug'        => $slug,
            'icon'        => $icon,
            'description' => $desc,
            'sort_order'  => $order,
        ]);

        session()->setFlashdata('success', 'Data kategori "' . $name . '" berhasil diperbarui!');
        return redirect()->to('/admin/categories');
    }

    public function deleteCategory($id)
    {
        $prodModel = new ProductModel();
        $count = $prodModel->where('category_id', $id)->countAllResults();

        if ($count > 0) {
            session()->setFlashdata('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $count . ' produk pengadaan.');
            return redirect()->to('/admin/categories');
        }

        $catModel = new CategoryModel();
        $cat = $catModel->find($id);
        $catModel->delete($id);

        session()->setFlashdata('success', 'Kategori "' . ($cat['name'] ?? '') . '" berhasil dihapus.');
        return redirect()->to('/admin/categories');
    }

    public function rfqDetail($id)
    {
        $rfqModel = new RfqModel();
        $rfq = $rfqModel->getRfqWithItems($id);

        if (!$rfq) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('RFQ tidak ditemukan');
        }

        return view('admin/rfq_detail', [
            'title' => 'Detail RFQ Pengadaan #' . $rfq['rfq_number'] . ' - Superadmin AkuAda',
            'rfq'   => $rfq
        ]);
    }

    public function updateRfqStatus()
    {
        $rfqId = (int)$this->request->getPost('rfq_id');
        $status = $this->request->getPost('status');

        $rfqModel = new RfqModel();
        $rfqModel->update($rfqId, [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        session()->setFlashdata('success', 'Status RFQ #' . $rfqId . ' berhasil diperbarui menjadi ' . strtoupper($status));
        return redirect()->back();
    }

    public function users()
    {
        $userModel = new UserModel();
        $companyModel = new CompanyModel();

        $roleFilter = $this->request->getGet('role');
        $searchQuery = trim((string)$this->request->getGet('q'));
        $users = $userModel->getAllUsersWithCompany($roleFilter, $searchQuery);
        $companies = $companyModel->orderBy('company_name', 'ASC')->findAll();

        return view('admin/users', [
            'title'       => 'Manajemen Akun Pengguna (Users) - Superadmin AkuAda',
            'users'       => $users,
            'companies'   => $companies,
            'roleFilter'  => $roleFilter,
            'searchQuery' => $searchQuery,
        ]);
    }

    public function deleteUser($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            session()->setFlashdata('error', 'Akun pengguna tidak ditemukan.');
            return redirect()->to('/admin/users');
        }

        // Jangan izinkan hapus akun Superadmin ID 1 atau user yang sedang login
        if ((int)$id === 1 || (int)$id === (int)session('user_id')) {
            session()->setFlashdata('error', 'Tidak dapat menghapus akun Superadmin utama atau akun yang sedang aktif digunakan.');
            return redirect()->to('/admin/users');
        }

        $userName = $user['name'];
        $userEmail = $user['email'];
        $userModel->delete($id);

        session()->setFlashdata('success', 'Akun pengguna "' . $userName . '" (' . $userEmail . ') berhasil dihapus secara permanen.');
        return redirect()->to('/admin/users');
    }

    public function storeUser()
    {
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $role = $this->request->getPost('role') ?? 'customer';
        $companyId = $this->request->getPost('company_id') ?: null;
        $position = $this->request->getPost('position') ?: 'Staff';
        $password = $this->request->getPost('password') ?: get_system_setting('default_account_password', 'admin123');
        $status = $this->request->getPost('status') ?: 'active';

        $now = date('Y-m-d H:i:s');
        $userModel = new UserModel();
        $userModel->insert([
            'name'       => $name,
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_BCRYPT),
            'role'       => $role,
            'phone'      => $phone,
            'company_id' => $companyId,
            'position'   => $position,
            'status'     => $status,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        session()->setFlashdata('success', 'Pengguna baru ' . $name . ' (' . strtoupper($role) . ') berhasil dibuat!');
        return redirect()->to('/admin/users');
    }

    public function updateUser($id)
    {
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $role = $this->request->getPost('role');
        $companyId = $this->request->getPost('company_id') ?: null;
        $position = $this->request->getPost('position');
        $status = $this->request->getPost('status');

        $data = [
            'name'       => $name,
            'email'      => $email,
            'phone'      => $phone,
            'role'       => $role,
            'company_id' => $companyId,
            'position'   => $position,
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $userModel = new UserModel();
        $userModel->update($id, $data);

        session()->setFlashdata('success', 'Data pengguna ' . $name . ' berhasil diperbarui.');
        return redirect()->to('/admin/users');
    }

    public function toggleUserStatus($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        if ($user) {
            $newStatus = ($user['status'] === 'active') ? 'suspended' : 'active';
            $userModel->update($id, ['status' => $newStatus]);
            session()->setFlashdata('toast', 'Status akun ' . $user['name'] . ' diubah menjadi ' . strtoupper($newStatus));
        }
        return redirect()->back();
    }

    public function resetUserPassword($id)
    {
        $userModel = new UserModel();
        $user = $userModel->find($id);
        $newPassword = $this->request->getPost('new_password') ?: get_system_setting('default_account_password', 'admin123');

        if ($user) {
            $userModel->update($id, [
               'password'   => password_hash($newPassword, PASSWORD_BCRYPT),
               'updated_at' => date('Y-m-d H:i:s'),
            ]);
            session()->setFlashdata('success', 'Kata sandi untuk ' . $user['email'] . ' berhasil direset menjadi: ' . $newPassword);
        }
        return redirect()->back();
    }

    public function rfqs()
    {
        $rfqModel = new RfqModel();
        $rfqs = $rfqModel->getDetailedRfqs();

        return view('admin/rfqs', [
            'title' => 'Pengawasan Permintaan Penawaran (RFQ) - Admin AkuAda',
            'rfqs'  => $rfqs
        ]);
    }

    public function settings()
    {
        $settingModel = new SettingModel();
        $settings = $settingModel->getGrouped();

        return view('admin/settings', [
            'title'    => 'Pengaturan Terpusat Sistem & Pajak - Admin AkuAda',
            'settings' => $settings
        ]);
    }

    public function updateSettings()
    {
        $settingModel = new SettingModel();
        $posts = $this->request->getPost();

        $orderId = (int)($posts['order_id'] ?? 0);
        $orderShipAmt = $posts['order_shipping_amount'] ?? null;
        $activeTab = $posts['active_tab'] ?? '';
        unset($posts['order_id'], $posts['order_shipping_amount'], $posts['active_tab']);
        foreach ($posts as $key => $value) {
            if ($key !== 'csrf_test_name') {
                $group = 'general';
                $type = 'string';

                if (str_starts_with($key, 'vendor_fee_') || $key === 'vendor_payout_days') {
                    $group = 'fee';
                    if (is_numeric($value)) $type = 'number';
                } elseif (in_array($key, ['ppn_rate', 'pph_22_rate', 'tax_nsfp_prefix'])) {
                    $group = 'tax';
                    $type = 'number';
                } elseif (in_array($key, ['allowed_top_terms', 'default_credit_limit', 'max_credit_limit', 'credit_step_amount', 'default_shipping_amount', 'free_shipping_region'])) {
                    $group = 'payment';
                    if (in_array($key, ['default_credit_limit', 'max_credit_limit', 'credit_step_amount', 'default_shipping_amount'])) $type = 'number';
                } elseif (in_array($key, ['primary_color', 'secondary_color', 'accent_color'])) {
                    $group = 'design';
                } elseif (str_starts_with($key, 'inv_')) {
                    $group = 'invoice';
                }

                $settingModel->updateValue($key, $value, $group, $type);
            }
        }

        if ($orderId > 0 && $orderShipAmt !== null && $orderShipAmt !== '') {
            $orderModel = new OrderModel();
            $orderModel->update($orderId, ['shipping_amount' => max(0, (float)$orderShipAmt)]);
        }
        sync_orders_with_system_settings();
        if ($activeTab) {
            session()->setFlashdata('active_tab', $activeTab);
        }
        session()->setFlashdata('success', 'Pengaturan sistem, invoice (pembayaran & penandatangan), tarif fee, dan pajak berhasil disimpan!');
        return redirect()->back();
    }

    public function debts()
    {
        $orderModel = new OrderModel();
        $companyModel = new CompanyModel();
        $orders = $orderModel->getDetailedOrders();
        $buyers = $companyModel->getBuyers();
        $vendors = $companyModel->getVendors();

        $buyerUnpaidTotal = 0;
        $buyerOverdueTotal = 0;
        $buyerPaidTotal = 0;

        $vendorUnpaidTotal = 0;
        $vendorOverdueTotal = 0;
        $vendorPaidTotal = 0;

        $buyerPendingTotal = 0;
        $buyerPendingCount = 0;
        $today = date('Y-m-d');

        foreach ($orders as $o) {
            $buyerAmt = (float)$o['total_amount'];
            $buyerDue = $o['due_date'] ?: date('Y-m-d', strtotime($o['created_at'] . ' +30 days'));
            if ($o['payment_status'] === 'paid') {
                $buyerPaidTotal += $buyerAmt;
            } elseif ($o['payment_status'] === 'pending_approval') {
                $buyerPendingTotal += $buyerAmt;
                $buyerPendingCount++;
                $buyerUnpaidTotal += $buyerAmt;
            } else {
                $buyerUnpaidTotal += $buyerAmt;
                if ($buyerDue < $today || $o['payment_status'] === 'overdue') {
                    $buyerOverdueTotal += $buyerAmt;
                }
            }

            $fee = (float)($o['total_vendor_fee'] ?? 0);
            $vendorNet = (float)($o['vendor_net_payout'] ?? ($o['subtotal'] - $fee)) + (float)($o['ppn_amount'] ?? 0);
            $vendorDue = $o['vendor_payout_due_date'] ?: date('Y-m-d', strtotime($o['created_at'] . ' +7 days'));
            if (($o['vendor_payout_status'] ?? 'unpaid') === 'paid') {
                $vendorPaidTotal += $vendorNet;
            } else {
                $vendorUnpaidTotal += $vendorNet;
                if ($vendorDue < $today) {
                    $vendorOverdueTotal += $vendorNet;
                }
            }
        }

        $buyerAging = calculate_aging_summary($orders, 'buyer', $buyers, 'buyer_name');
        $vendorAging = calculate_aging_summary($orders, 'vendor', $vendors, 'vendor_name');

        $coaModel = new \App\Models\CoaModel();
        $bankAccounts = $coaModel->where('is_cash_bank', 1)->where('is_active', 1)->orderBy('account_code', 'ASC')->findAll();

        return view('admin/debts', [
            'title'              => 'Manajemen Hutang Piutang TOP & Aging Schedule - Admin AkuAda',
            'orders'             => $orders,
            'bankAccounts'       => $bankAccounts,
            'buyerUnpaidTotal'   => $buyerUnpaidTotal,
            'buyerPendingTotal'  => $buyerPendingTotal,
            'buyerPendingCount'  => $buyerPendingCount,
            'buyerOverdueTotal'  => $buyerOverdueTotal,
            'buyerPaidTotal'     => $buyerPaidTotal,
            'vendorUnpaidTotal'  => $vendorUnpaidTotal,
            'vendorOverdueTotal' => $vendorOverdueTotal,
            'vendorPaidTotal'    => $vendorPaidTotal,
            'buyerAging'         => $buyerAging,
            'vendorAging'        => $vendorAging,
        ]);
    }

    public function confirmBuyerPayment()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $paymentBank = $this->request->getPost('payment_bank') ?: 'Bank Mandiri Escrow AkuAda';
        $paymentRef = $this->request->getPost('payment_ref') ?: ('VERIF-AR-' . date('YmdHis'));

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if ($order && $order['payment_status'] !== 'paid') {
            $now = date('Y-m-d H:i:s');
            $orderModel->update($orderId, [
                'payment_status'           => 'paid',
                'buyer_paid_at'            => !empty($order['buyer_paid_at']) ? $order['buyer_paid_at'] : $now,
                'buyer_payment_bank'       => $paymentBank,
                'buyer_payment_ref'        => $paymentRef,
                'payment_rejection_reason' => null,
                'updated_at'               => $now,
            ]);

            $companyModel = new CompanyModel();
            $company = $companyModel->find($order['customer_company_id']);
            if ($company) {
                $newUsed = max(0, (float)$company['credit_used_top'] - (float)$order['total_amount']);
                $companyModel->update($company['id'], ['credit_used_top' => $newUsed]);
            }

            \App\Models\CoaModel::syncOrdersToJournals();

            $logModel = new OrderStatusLogModel();
            $logModel->logStatus(
                $orderId,
                $order['order_status'],
                'Verifikasi Pelunasan Piutang Buyer oleh Admin',
                'Pelunasan tagihan sebesar ' . format_rupiah($order['total_amount']) . ' via ' . $paymentBank . ' (Ref: ' . $paymentRef . ') telah diverifikasi lunas & plafon TOP buyer dipulihkan.',
                session('user_name') ?? 'Superadmin AkuAda'
            );
        }

        session()->setFlashdata('success', 'Pelunasan hutang dari Buyer berhasil dikonfirmasi & plafon kredit TOP buyer telah dipulihkan!');
        return redirect()->to('/admin/debts');
    }

    public function payVendorDebt()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $payoutBank = $this->request->getPost('payout_bank') ?: 'Transfer Bank Corporate';
        $payoutRef = $this->request->getPost('payout_ref') ?: ('SETTLE-AP-' . date('YmdHis'));

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if ($order && ($order['vendor_payout_status'] ?? 'unpaid') !== 'paid') {
            $now = date('Y-m-d H:i:s');
            $fee = (float)($order['total_vendor_fee'] ?? 0);
            $netTotal = (float)($order['vendor_net_payout'] ?? ($order['subtotal'] - $fee)) + (float)($order['ppn_amount'] ?? 0);

            $orderModel->update($orderId, [
                'vendor_payout_status' => 'paid',
                'vendor_paid_at'       => $now,
                'vendor_payout_bank'   => $payoutBank,
                'vendor_payout_ref'    => $payoutRef,
                'updated_at'           => $now,
            ]);

            $logModel = new OrderStatusLogModel();
            $logModel->logStatus(
                $orderId,
                $order['order_status'],
                'Pencairan Settlement Hutang AkuAda ke Vendor',
                'AkuAda telah mencairkan dana hak bersih vendor sebesar ' . format_rupiah($netTotal) . ' ke rekening ' . $payoutBank . ' (No. Ref: ' . $payoutRef . ').',
                session('user_name') ?? 'Superadmin Finance AkuAda'
            );
        }

        session()->setFlashdata('success', 'Pembayaran hutang settlement ke Mitra Vendor berhasil dicairkan!');
        return redirect()->to('/admin/debts?tab=vendor');
    }

    public function rejectBuyerPayment()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $reason = trim((string)$this->request->getPost('rejection_reason')) ?: 'Mutasi dana belum masuk di rekening escrow / bukti transfer tidak valid.';

        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if ($order) {
            $now = date('Y-m-d H:i:s');
            $orderModel->update($orderId, [
                'payment_status'           => 'unpaid',
                'payment_rejection_reason' => $reason,
                'updated_at'               => $now,
            ]);

            $logModel = new OrderStatusLogModel();
            $logModel->logStatus(
                $orderId,
                $order['order_status'],
                'Bukti Pembayaran Ditolak oleh Admin Finance',
                'Konfirmasi pembayaran tagihan ditolak dengan alasan: ' . $reason . '. Buyer diminta mengunggah ulang bukti transfer yang valid.',
                session('user_name') ?? 'Superadmin Finance'
            );

            session()->setFlashdata('error', 'Bukti pembayaran PO ' . ($order['po_number'] ?? $order['order_number']) . ' telah ditolak. Alasan penolakan telah diteruskan ke Buyer.');
        }

        return redirect()->back();
    }

    public function confirmBuyerPaymentDirect()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if (!$order) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan.');
            return redirect()->back();
        }

        $now = date('Y-m-d H:i:s');
        $paymentBank = $this->request->getPost('payment_bank') ?: ($order['buyer_payment_bank'] ?: 'Bank Mandiri (Escrow) AkuAda');
        $paymentRef = $this->request->getPost('payment_ref') ?: ($order['buyer_payment_ref'] ?: ('VERIFIED-' . date('YmdHis')));

        $orderModel->update($orderId, [
            'payment_status'           => 'paid',
            'buyer_paid_at'            => !empty($order['buyer_paid_at']) ? $order['buyer_paid_at'] : $now,
            'buyer_payment_bank'       => $paymentBank,
            'buyer_payment_ref'        => $paymentRef,
            'payment_rejection_reason' => null,
            'updated_at'               => $now,
        ]);

        $companyModel = new CompanyModel();
        $company = $companyModel->find($order['customer_company_id']);
        if ($company && (float)$company['credit_used_top'] > 0) {
            $newUsed = max(0, (float)$company['credit_used_top'] - (float)$order['total_amount']);
            $companyModel->update($company['id'], ['credit_used_top' => $newUsed]);
        }

        \App\Models\CoaModel::syncOrdersToJournals();

        $logModel = new OrderStatusLogModel();
        $logModel->logStatus(
            $orderId,
            $order['order_status'],
            'Verifikasi Pelunasan Pembayaran Buyer Lunas',
            'Pembayaran sebesar ' . format_rupiah($order['total_amount']) . ' via ' . $paymentBank . ' (Ref: ' . $paymentRef . ') telah diverifikasi sah & lunas oleh Tim Finance AkuAda.',
            session('user_name') ?? 'Superadmin Finance AkuAda'
        );

        $cbAmount = process_buyer_order_cashback($orderId);
        $cbMsg = ($cbAmount && $cbAmount > 0) ? ' & Cashback ' . format_rupiah($cbAmount) . ' otomatis dikreditkan ke saldo buyer!' : '';

        session()->setFlashdata('success', 'Pembayaran Buyer sebesar ' . format_rupiah($order['total_amount']) . ' berhasil disahkan LUNAS!' . $cbMsg);
        return redirect()->back();
    }

    public function rejectBuyerPaymentDirect()
    {
        return $this->rejectBuyerPayment();
    }

    public function payoutVendorDirect()
    {
        $orderId = (int)$this->request->getPost('order_id');
        $orderModel = new OrderModel();
        $order = $orderModel->find($orderId);

        if (!$order) {
            session()->setFlashdata('error', 'Pesanan tidak ditemukan.');
            return redirect()->back();
        }

        $companyModel = new CompanyModel();
        $vendor = $companyModel->find($order['vendor_company_id']);

        $vendorBank = $this->request->getPost('payout_bank') ?: ($vendor['bank_name'] ?? 'Bank BCA Corporate');
        $vendorAcc = $this->request->getPost('payout_account') ?: ($vendor['bank_account_number'] ?? '-');
        $vendorHolder = $this->request->getPost('payout_holder') ?: ($vendor['bank_account_holder'] ?? ($vendor['company_name'] ?? 'Mitra Vendor'));
        $payoutRef = $this->request->getPost('payout_ref') ?: ('PAYOUT-' . date('YmdHis'));

        $now = date('Y-m-d H:i:s');
        $fee = (float)($order['total_vendor_fee'] ?? 0);
        $netTotal = (float)($order['vendor_net_payout'] ?? ($order['subtotal'] - $fee)) + (float)($order['ppn_amount'] ?? 0);

        $orderModel->update($orderId, [
            'vendor_payout_status' => 'paid',
            'vendor_paid_at'       => $now,
            'vendor_payout_bank'   => $vendorBank . ' (' . $vendorAcc . ' a/n ' . $vendorHolder . ')',
            'vendor_payout_ref'    => $payoutRef,
            'updated_at'           => $now,
        ]);

        \App\Models\CoaModel::syncOrdersToJournals();

        $logModel = new OrderStatusLogModel();
        $logModel->logStatus(
            $orderId,
            $order['order_status'],
            'Pencairan Dana Settlement ke Rekening Vendor',
            'AkuAda telah mentransfer hak bersih penjualan sebesar ' . format_rupiah($netTotal) . ' ke ' . $vendorBank . ' Rek. ' . $vendorAcc . ' a/n ' . $vendorHolder . ' (No. Ref: ' . $payoutRef . ').',
            session('user_name') ?? 'Superadmin Finance AkuAda'
        );

        session()->setFlashdata('success', 'Dana hak bersih vendor sebesar ' . format_rupiah($netTotal) . ' berhasil dicairkan ke rekening ' . $vendorBank . '!');
        return redirect()->back();
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

    public function chatCenter()
    {
        $messageModel = new OrderMessageModel();
        $threads = $messageModel->getConversationsList('admin', null);
        $companies = (new \App\Models\CompanyModel())->findAll();
        $orders = (new \App\Models\OrderModel())->orderBy('id', 'DESC')->findAll(30);

        return view('admin/chat', [
            'title'     => 'Pusat Pesan & Chat Pengadaan - Superadmin AkuAda',
            'threads'   => $threads,
            'companies' => $companies,
            'orders'    => $orders,
        ]);
    }

    private function saveUploadedFile(string $field, string $prefix, string $slug): ?string
    {
        $file = $this->request->getFile($field);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
            $ext = strtolower($file->getClientExtension());
            if (in_array($ext, $allowedExts, true) && $file->getSizeByUnit('mb') <= 5) {
                $targetDir = FCPATH . 'uploads/buyer_documents/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                $safeSlug = preg_replace('/[^a-z0-9_-]/i', '_', substr($slug, 0, 25));
                $filename = $prefix . '_' . $safeSlug . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
                $file->move($targetDir, $filename);
                return 'uploads/buyer_documents/' . $filename;
            }
        }
        return null;
    }

    private function _unusedSettingsStub() {
        return redirect()->back();
    }
}
