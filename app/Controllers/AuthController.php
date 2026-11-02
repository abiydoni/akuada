<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CompanyModel;

class AuthController extends BaseController
{
    /**
     * Instant Role Switcher / Bypass for seamless demonstration
     */
    public function switchRole(string $role)
    {
        $userModel = new UserModel();
        $companyModel = new CompanyModel();
        $user = null;

        if (in_array($role, ['admin', 'vendor', 'customer'], true)) {
            $foundUser = $userModel->where('role', $role)->orderBy('id', 'ASC')->first();
            if ($foundUser) {
                $user = $userModel->getUserWithCompany($foundUser['id']);
            }
        }

        // Fallback jika tabel users belum memiliki akun untuk role tersebut namun data company ada
        if (!$user && in_array($role, ['vendor', 'customer'], true)) {
            $companyType = ($role === 'vendor') ? 'vendor' : 'buyer';
            $company = $companyModel->where('type', $companyType)->orderBy('id', 'ASC')->first();
            if ($company) {
                $user = [
                    'id'               => 0,
                    'name'             => ($company['pic_name'] ?: $company['company_name']),
                    'email'            => $company['email'],
                    'role'             => $role,
                    'avatar'           => null,
                    'company_id'       => $company['id'],
                    'company_name'     => $company['company_name'],
                    'legal_type'       => $company['legal_type'],
                    'npwp'             => $company['npwp'],
                    'nib'              => $company['nib'],
                    'commission_rate'  => $company['commission_rate'],
                    'credit_limit_top' => $company['credit_limit_top'],
                    'credit_used_top'  => $company['credit_used_top'],
                ];
            }
        }

        if ($user) {
            session()->set([
                'user_id'          => $user['id'],
                'user_name'        => $user['name'],
                'user_email'       => $user['email'],
                'user_role'        => $user['role'],
                'user_avatar'      => $user['avatar'] ?? null,
                'company_id'       => $user['company_id'],
                'company_name'     => $user['company_name'],
                'company_legal'    => $user['legal_type'] ?? 'PT',
                'company_npwp'     => $user['npwp'] ?? null,
                'company_nib'      => $user['nib'] ?? null,
                'commission_rate'  => (float)($user['commission_rate'] ?? 2.50),
                'credit_limit_top' => (float)($user['credit_limit_top'] ?? 0),
                'credit_used_top'  => (float)($user['credit_used_top'] ?? 0),
            ]);

            session()->setFlashdata('toast', 'Bypass aktif sebagai: ' . $user['name'] . ' (' . ($user['company_name'] ?? strtoupper($user['role'])) . ')');

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

        if ($user && password_verify($password, $user['password'])) {
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

        session()->setFlashdata('error', 'Email atau kata sandi tidak cocok.');
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
            'credit_limit_top' => 0.00,        // Kredit 0 - menunggu verifikasi dan persetujuan admin
            'credit_used_top'  => 0.00,
            'is_verified'      => 0,            // Belum terverifikasi - admin harus approve
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

