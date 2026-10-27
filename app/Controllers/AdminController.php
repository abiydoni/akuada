<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\CompanyModel;
use App\Models\ProductModel;
use App\Models\RfqModel;
use App\Models\SettingModel;
use App\Models\UserModel;

class AdminController extends BaseController
{
    public function dashboard()
    {
        $orderModel = new OrderModel();
        $companyModel = new CompanyModel();
        $productModel = new ProductModel();
        $rfqModel = new RfqModel();

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

        $orderModel = new OrderModel();
        $data = ['order_status' => $status];
        if ($paymentStatus) {
            $data['payment_status'] = $paymentStatus;
        }

        $orderModel->update($orderId, $data);

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
            'cancelled'       => 'Pesanan Dibatalkan',
        ];
        $statusTitle = $statusLabels[$status] ?? ucfirst($status);

        $logModel = new \App\Models\OrderStatusLogModel();
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
        $password = $this->request->getPost('password') ?: 'admin123';
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

    public function buyers()
    {
        $companyModel = new CompanyModel();
        $buyers = $companyModel->getBuyers();

        return view('admin/buyers', [
            'title'  => 'Manajemen Perusahaan Buyer & Plafon Kredit TOP - Admin AkuAda',
            'buyers' => $buyers
        ]);
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

    public function products()
    {
        $productModel = new ProductModel();
        $products = $productModel->getDetailedProducts(100);

        return view('admin/products', [
            'title'    => 'Pengawasan Master Barang Pengadaan - Admin AkuAda',
            'products' => $products
        ]);
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

        foreach ($posts as $key => $value) {
            if ($key !== 'csrf_test_name') {
                $group = 'general';
                $type = 'string';

                if (str_starts_with($key, 'vendor_fee_')) {
                    $group = 'fee';
                    if (is_numeric($value)) $type = 'number';
                } elseif (in_array($key, ['ppn_rate', 'pph_22_rate'])) {
                    $group = 'tax';
                    $type = 'number';
                } elseif (in_array($key, ['allowed_top_terms', 'default_credit_limit'])) {
                    $group = 'payment';
                    if ($key === 'default_credit_limit') $type = 'number';
                } elseif (in_array($key, ['primary_color', 'secondary_color', 'accent_color'])) {
                    $group = 'design';
                }

                $settingModel->updateValue($key, $value, $group, $type);
            }
        }

        session()->setFlashdata('success', 'Pengaturan sistem, tarif fee vendor, pajak, dan design tokens berhasil disimpan!');
        return redirect()->back();
    }
}
