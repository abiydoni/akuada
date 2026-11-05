<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

/**
 * AuthFilter — Middleware autentikasi & otorisasi role-based ketat.
 * Lindungi semua route /admin, /vendor, /customer dari akses tanpa login resmi.
 */
class AuthFilter implements FilterInterface
{
    /**
     * Mapping prefix URL ke role yang diizinkan.
     */
    private array $roleMap = [
        'admin'    => 'admin',
        'vendor'   => 'vendor',
        'customer' => 'customer',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        $uri     = service('uri');
        $segment = $uri->getSegment(1); // admin | vendor | customer

        if (!isset($this->roleMap[$segment])) {
            return; // Bukan route yang dilindungi
        }

        $requiredRole = $this->roleMap[$segment];
        $isLoggedIn   = (bool)session('is_logged_in');
        $userId       = (int)session('user_id');
        $userRole     = session('user_role');

        // Belum login resmi sama sekali
        if (!$isLoggedIn || empty($userId) || empty($userRole)) {
            session()->remove(['is_logged_in', 'user_id', 'user_role', 'role', 'company_id']);
            session()->setFlashdata('error', 'Silakan masuk dengan akun resmi untuk mengakses area ' . strtoupper($requiredRole) . '.');
            return redirect()->to('/auth/login');
        }

        // Cek keaktifan akun user di database
        $userModel = new UserModel();
        $user = $userModel->find($userId);
        if (!$user || ($user['status'] ?? 'active') !== 'active') {
            session()->destroy();
            session()->setFlashdata('error', 'Sesi login tidak valid atau akun dinonaktifkan. Silakan masuk kembali.');
            return redirect()->to('/auth/login');
        }

        // Login tapi role tidak sesuai dengan area yang diakses
        if ($userRole !== $requiredRole) {
            session()->setFlashdata('error', 'Anda tidak memiliki hak akses ke portal ' . strtoupper($requiredRole) . '.');
            $dashMap = [
                'admin'    => '/admin/dashboard',
                'vendor'   => '/vendor/dashboard',
                'customer' => '/customer/dashboard'
            ];
            return redirect()->to($dashMap[$userRole] ?? '/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah request
    }
}
