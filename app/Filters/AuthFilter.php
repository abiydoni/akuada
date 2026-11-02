<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter — Middleware autentikasi & otorisasi role-based.
 * Lindungi semua route /admin/*, /vendor/*, /customer/* dari akses tanpa login.
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
        $userRole     = session('user_role');

        // Belum login sama sekali
        if (empty($userRole)) {
            session()->setFlashdata('error', 'Silakan masuk terlebih dahulu untuk mengakses halaman ini.');
            return redirect()->to('/auth/login');
        }

        // Login tapi role tidak sesuai
        if ($userRole !== $requiredRole) {
            session()->setFlashdata('error', 'Anda tidak memiliki hak akses ke area tersebut.');
            // Redirect ke dashboard role yang sesuai
            $dashMap = ['admin' => '/admin/dashboard', 'vendor' => '/vendor/dashboard', 'customer' => '/customer/dashboard'];
            return redirect()->to($dashMap[$userRole] ?? '/');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak ada aksi setelah request
    }
}

