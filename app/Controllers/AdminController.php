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
        $email = $this->request->getPost('email');
        $picName = $this->request->getPost('pic_name');
        $picPhone = $this->request->getPost('pic_phone');
        $picEmail = $this->request->getPost('pic_email') ?: $email;
        $creditLimit = (float)$this->request->getPost('credit_limit_top');
        $password = $this->request->getPost('password') ?: get_system_setting('default_account_password', 'admin123');
        $position = $this->request->getPost('position') ?: 'Procurement Manager';

        $now = date('Y-m-d H:i:s');

        $companyModel = new CompanyModel();
        $companyId = $companyModel->insert([
            'type'             => 'buyer',
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
            'credit_limit_top' => $creditLimit,
            'credit_used_top'  => 0.00,
            'commission_rate'  => 0.00,
            'is_verified'      => 1,
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

        return view('admin/order_detail', [
            'title' => 'Detail PO & Audit Log Transaksi #' . ($order['po_number'] ?? $order['order_number']) . ' - Superadmin AkuAda',
            'order' => $order
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
        $users = $userModel->getAllUsersWithCompany($roleFilter);
        $companies = $companyModel->orderBy('company_name', 'ASC')->findAll();

        return view('admin/users', [
            'title'      => 'Manajemen Akun Pengguna (Users) - Superadmin AkuAda',
            'users'      => $users,
            'companies'  => $companies,
            'roleFilter' => $roleFilter,
        ]);
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

        $today = date('Y-m-d');

        foreach ($orders as $o) {
            $buyerAmt = (float)$o['total_amount'];
            $buyerDue = $o['due_date'] ?: date('Y-m-d', strtotime($o['created_at'] . ' +30 days'));
            if ($o['payment_status'] === 'paid') {
                $buyerPaidTotal += $buyerAmt;
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

        return view('admin/debts', [
            'title'              => 'Manajemen Hutang Piutang TOP & Aging Schedule - Admin AkuAda',
            'orders'             => $orders,
            'buyerUnpaidTotal'   => $buyerUnpaidTotal,
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
            }

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

    private function _unusedSettingsStub() {
        return redirect()->back();
    }
}
