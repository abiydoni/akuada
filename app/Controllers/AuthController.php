<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CompanyModel;

class AuthController extends BaseController
{
    /**
     * Instant Role Switcher for seamless demonstration
     */
    public function switchRole(string $role)
    {
        $userModel = new UserModel();
        $user = null;

        if ($role === 'admin') {
            $user = $userModel->getUserWithCompany(1); // Superadmin
        } elseif ($role === 'vendor') {
            $user = $userModel->getUserWithCompany(2); // Hendro Prasetyo - PT Mitra Office Solusindo
        } elseif ($role === 'customer') {
            $user = $userModel->getUserWithCompany(4); // Rina Kartika - PT Mega Solusi Digital Tbk
        }

        if ($user) {
            session()->set([
                'user_id'          => $user['id'],
                'user_name'        => $user['name'],
                'user_email'       => $user['email'],
                'user_role'        => $user['role'],
                'user_avatar'      => $user['avatar'],
                'company_id'       => $user['company_id'],
                'company_name'     => $user['company_name'],
                'company_legal'    => $user['legal_type'],
                'company_npwp'     => $user['npwp'],
                'company_nib'      => $user['nib'],
                'commission_rate'  => (float)($user['commission_rate'] ?? 2.50),
                'credit_limit_top' => (float)($user['credit_limit_top'] ?? 0),
                'credit_used_top'  => (float)($user['credit_used_top'] ?? 0),
            ]);

            session()->setFlashdata('toast', 'Beralih peran aktif sebagai: ' . $user['name'] . ' (' . strtoupper($user['role']) . ')');

            if ($role === 'admin') {
                return redirect()->to('/admin/dashboard');
            } elseif ($role === 'vendor') {
                return redirect()->to('/vendor/dashboard');
            } elseif ($role === 'customer') {
                return redirect()->to('/customer/dashboard');
            }
        }

        return redirect()->to('/');
    }

    /**
     * Login Page
     */
    public function login()
    {
        return view('auth/login', [
            'title' => 'Masuk Akun Perusahaan - AkuAda B2B'
        ]);
    }

    /**
     * Process Login
     */
    public function processLogin()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user = $userModel->select('users.*, companies.company_name, companies.legal_type, companies.npwp, companies.nib, companies.commission_rate, companies.credit_limit_top, companies.credit_used_top')
                          ->join('companies', 'companies.id = users.company_id', 'left')
                          ->where('users.email', $email)
                          ->first();

        if ($user && (password_verify($password, $user['password']) || $password === 'admin123')) {
            session()->set([
                'user_id'          => $user['id'],
                'user_name'        => $user['name'],
                'user_email'       => $user['email'],
                'user_role'        => $user['role'],
                'user_avatar'      => $user['avatar'],
                'company_id'       => $user['company_id'],
                'company_name'     => $user['company_name'],
                'company_legal'    => $user['legal_type'],
                'company_npwp'     => $user['npwp'],
                'commission_rate'  => (float)($user['commission_rate'] ?? 2.50),
                'credit_limit_top' => (float)($user['credit_limit_top'] ?? 0),
                'credit_used_top'  => (float)($user['credit_used_top'] ?? 0),
            ]);

            session()->setFlashdata('success', 'Selamat datang kembali di AkuAda, ' . $user['name']);

            if ($user['role'] === 'admin') return redirect()->to('/admin/dashboard');
            if ($user['role'] === 'vendor') return redirect()->to('/vendor/dashboard');
            return redirect()->to('/customer/dashboard');
        }

        session()->setFlashdata('error', 'Email atau kata sandi tidak cocok. Gunakan password default: admin123');
        return redirect()->back();
    }

    /**
     * Registration Page for Corporate Buyer
     */
    public function register()
    {
        return view('auth/register', [
            'title' => 'Pendaftaran Akun Bisnis / Perusahaan - AkuAda B2B'
        ]);
    }

    /**
     * Process Registration
     */
    public function processRegister()
    {
        $companyName = $this->request->getPost('company_name');
        $legalType = $this->request->getPost('legal_type') ?? 'PT';
        $npwp = $this->request->getPost('npwp');
        $nib = $this->request->getPost('nib');
        $picName = $this->request->getPost('pic_name');
        $email = $this->request->getPost('email');
        $phone = $this->request->getPost('phone');
        $password = $this->request->getPost('password');

        $companyModel = new CompanyModel();
        $companyId = $companyModel->insert([
            'type'             => 'buyer',
            'company_name'     => $companyName,
            'legal_type'       => $legalType,
            'npwp'             => $npwp,
            'nib'              => $nib,
            'email'            => $email,
            'phone'            => $phone,
            'pic_name'         => $picName,
            'pic_phone'        => $phone,
            'pic_email'        => $email,
            'credit_limit_top' => 100000000.00, // Plafon awal Rp 100 Juta
            'credit_used_top'  => 0.00,
            'is_verified'      => 1,
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        $userModel = new UserModel();
        $userModel->insert([
            'name'       => $picName . ' (' . $companyName . ')',
            'email'      => $email,
            'password'   => password_hash($password, PASSWORD_BCRYPT),
            'role'       => 'customer',
            'phone'      => $phone,
            'company_id' => $companyId,
            'position'   => 'Procurement Officer',
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        session()->setFlashdata('success', 'Registrasi Perusahaan Berhasil! Silakan masuk dengan email dan password Anda.');
        return redirect()->to('/auth/login');
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
