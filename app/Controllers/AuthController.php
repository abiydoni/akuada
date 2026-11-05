<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CompanyModel;

class AuthController extends BaseController
{
    /**
     * Role Switcher / Bypass (Dinonaktifkan demi keamanan dan kepatuhan autentikasi)
     */
    public function switchRole(string $role)
    {
        session()->setFlashdata('error', 'Akses bypass login dinonaktifkan. Silakan masuk menggunakan akun resmi.');
        return redirect()->to('/auth/login');
    }

    /**
     * Login Page
     */
    public function login()
    {
        // Jika sudah login resmi, arahkan ke dashboard / marketplace yang sesuai
        if (session('is_logged_in') && session('user_id') && session('user_role')) {
            $role = session('user_role');
            if ($role === 'admin') return redirect()->to('/admin/dashboard');
            if ($role === 'vendor') return redirect()->to('/vendor/dashboard');
            return redirect()->to('/'); // Khusus buyer kembali ke halaman marketplace
        }

        $captchaNonce = bin2hex(random_bytes(16));
        $captchaTime  = time();
        $captchaSecret = config('App')->appTimezone ?? 'akuada_captcha_salt_2026';
        $captchaHash  = hash_hmac('sha256', $captchaNonce . '|' . $captchaTime, $captchaSecret);

        session()->set('login_captcha_challenge', [
            'nonce' => $captchaNonce,
            'time'  => $captchaTime,
            'hash'  => $captchaHash,
        ]);

        return view('auth/login', [
            'title'       => 'Masuk Akun Perusahaan - AkuAda B2B',
            'captchaHash' => $captchaHash,
        ]);
    }

    /**
     * Process Login
     */
    public function processLogin()
    {
        $email = trim((string)$this->request->getPost('email'));
        $password = (string)$this->request->getPost('password');
        $captchaToken = (string)$this->request->getPost('captcha_token');
        $honeypot = (string)$this->request->getPost('auth_security_guard_hp');

        // 1. Anti-bot honeypot check
        if (!empty($honeypot)) {
            session()->setFlashdata('error', 'Aktivitas otomatis (bot) terdeteksi. Permintaan login ditolak.');
            return redirect()->back();
        }

        // 2. 1-Click Captcha validation
        $challenge = session('login_captcha_challenge');
        if (empty($captchaToken) || empty($challenge) || !hash_equals($challenge['hash'], $captchaToken)) {
            session()->setFlashdata('error', 'Silakan klik centang verifikasi keamanan "Saya bukan robot" terlebih dahulu.');
            return redirect()->back()->withInput();
        }

        // 3. Expire after 15 minutes
        if (time() - $challenge['time'] > 900) {
            session()->setFlashdata('error', 'Sesi verifikasi keamanan telah kedaluwarsa. Silakan ulangi centang "Saya bukan robot".');
            return redirect()->back()->withInput();
        }

        if (empty($email) || empty($password)) {
            session()->setFlashdata('error', 'Silakan masukkan email dan kata sandi Anda.');
            return redirect()->back()->withInput();
        }

        $userModel = new UserModel();
        $user = $userModel->select('users.*, companies.company_name, companies.legal_type, companies.npwp, companies.nib, companies.commission_rate, companies.credit_limit_top, companies.credit_used_top')
                          ->join('companies', 'companies.id = users.company_id', 'left')
                          ->where('users.email', $email)
                          ->first();

        if ($user && password_verify($password, $user['password'])) {
            if (($user['status'] ?? 'active') !== 'active') {
                session()->setFlashdata('error', 'Akun Anda sedang ditangguhkan atau nonaktif. Silakan hubungi Administrator.');
                return redirect()->back();
            }

            // Regenerasi ID sesi untuk mencegah session fixation
            session()->regenerate();

            session()->set([
                'is_logged_in'     => true,
                'role'             => $user['role'],
                'user_id'          => (int)$user['id'],
                'user_name'        => $user['name'],
                'user_email'       => $user['email'],
                'user_role'        => $user['role'],
                'user_avatar'      => $user['avatar'] ?? null,
                'company_id'       => (int)($user['company_id'] ?? 0),
                'company_name'     => $user['company_name'] ?? null,
                'company_legal'    => $user['legal_type'] ?? 'PT',
                'company_npwp'     => $user['npwp'] ?? null,
                'commission_rate'  => (float)($user['commission_rate'] ?? 2.50),
                'credit_limit_top' => (float)($user['credit_limit_top'] ?? 0),
                'credit_used_top'  => (float)($user['credit_used_top'] ?? 0),
            ]);

            session()->setFlashdata('success', 'Selamat datang kembali di AkuAda, ' . $user['name'] . '!');

            if ($user['role'] === 'admin') return redirect()->to('/admin/dashboard');
            if ($user['role'] === 'vendor') return redirect()->to('/vendor/dashboard');
            
            // Khusus buyer (customer), kembali ke halaman marketplace (bukan ke dashboard)
            return redirect()->to('/');
        }

        session()->setFlashdata('error', 'Email atau kata sandi tidak cocok. Silakan periksa kembali.');
        return redirect()->back()->withInput();
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
     * Process Registration for Corporate Buyer
     */
    public function processRegister()
    {
        $companyName     = trim((string)$this->request->getPost('company_name'));
        $legalType       = $this->request->getPost('legal_type') ?: 'PT';
        $npwp            = trim((string)$this->request->getPost('npwp'));
        $nib             = trim((string)$this->request->getPost('nib'));
        $businessField   = trim((string)$this->request->getPost('business_field'));
        $phone           = trim((string)$this->request->getPost('phone'));
        $website         = trim((string)$this->request->getPost('website'));
        $address         = trim((string)$this->request->getPost('address'));
        $city            = trim((string)$this->request->getPost('city'));
        $province        = trim((string)$this->request->getPost('province'));

        $picName         = trim((string)$this->request->getPost('pic_name'));
        $position        = trim((string)$this->request->getPost('position')) ?: 'Procurement Manager';
        $picPhone        = trim((string)$this->request->getPost('pic_phone'));
        $email           = strtolower(trim((string)$this->request->getPost('email')));
        $password        = (string)$this->request->getPost('password');
        $passwordConfirm = (string)$this->request->getPost('password_confirm');

        // 1. Basic validation
        if (empty($companyName) || empty($npwp) || empty($nib) || empty($address) || empty($city) || empty($province) || empty($picName) || empty($email) || empty($picPhone) || empty($password)) {
            session()->setFlashdata('error', 'Mohon lengkapi seluruh kolom formulir bertanda bintang (*).');
            return redirect()->back()->withInput();
        }

        if (strlen($password) < 6) {
            session()->setFlashdata('error', 'Kata sandi minimal 6 karakter demi keamanan akun perusahaan.');
            return redirect()->back()->withInput();
        }

        if ($password !== $passwordConfirm) {
            session()->setFlashdata('error', 'Konfirmasi kata sandi tidak cocok. Mohon ulangi pengetikan sandi.');
            return redirect()->back()->withInput();
        }

        $userModel = new UserModel();
        $existingUser = $userModel->where('email', $email)->first();
        if ($existingUser) {
            session()->setFlashdata('error', 'Email ' . $email . ' sudah terdaftar dalam sistem. Silakan gunakan email lain atau masuk ke akun Anda.');
            return redirect()->back()->withInput();
        }

        // 2. Validate mandatory uploaded files (NPWP, NIB, Deed, ID Card)
        $npwpFile   = $this->request->getFile('npwp_file');
        $nibFile    = $this->request->getFile('nib_file');
        $deedFile   = $this->request->getFile('deed_file');
        $idCardFile = $this->request->getFile('id_card_file');
        $sppkpFile  = $this->request->getFile('sppkp_file');

        if (!$npwpFile || !$npwpFile->isValid()) {
            session()->setFlashdata('error', 'Berkas NPWP Perusahaan / SKT Pajak wajib diunggah.');
            return redirect()->back()->withInput();
        }

        if (!$nibFile || !$nibFile->isValid()) {
            session()->setFlashdata('error', 'Berkas NIB (Nomor Induk Berusaha) OSS wajib diunggah.');
            return redirect()->back()->withInput();
        }

        if (!$deedFile || !$deedFile->isValid()) {
            session()->setFlashdata('error', 'Berkas Akta Pendirian / SK Kemenkumham wajib diunggah.');
            return redirect()->back()->withInput();
        }

        if (!$idCardFile || !$idCardFile->isValid()) {
            session()->setFlashdata('error', 'Foto/Scan KTP PIC Penanggung Jawab wajib diunggah.');
            return redirect()->back()->withInput();
        }

        // 3. Process Uploads
        $slug = url_title($companyName, '-', true);
        $npwpPath   = $this->saveBuyerDocument($npwpFile, 'npwp', $slug);
        $nibPath    = $this->saveBuyerDocument($nibFile, 'nib', $slug);
        $deedPath   = $this->saveBuyerDocument($deedFile, 'akta', $slug);
        $idCardPath = $this->saveBuyerDocument($idCardFile, 'ktp', $slug);
        $sppkpPath  = ($sppkpFile && $sppkpFile->isValid()) ? $this->saveBuyerDocument($sppkpFile, 'sppkp', $slug) : null;

        if (!$npwpPath || !$nibPath || !$deedPath || !$idCardPath) {
            session()->setFlashdata('error', 'Format berkas dokumen harus berformat PDF, JPG, JPEG, atau PNG dengan ukuran maksimal 5MB.');
            return redirect()->back()->withInput();
        }

        $now = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();
        $db->transStart();

        // 4. Insert Company
        $companyModel = new CompanyModel();
        $companyId = $companyModel->insert([
            'type'             => 'buyer',
            'company_name'     => $companyName,
            'legal_type'       => $legalType,
            'npwp'             => $npwp,
            'nib'              => $nib,
            'business_field'   => $businessField ?: 'Perdagangan Umum & Jasa',
            'address'          => $address,
            'city'             => $city,
            'province'         => $province,
            'phone'            => $phone ?: $picPhone,
            'website'          => $website,
            'email'            => $email,
            'pic_name'         => $picName,
            'pic_phone'        => $picPhone,
            'pic_email'        => $email,
            'credit_limit_top' => 0.00,        // Kredit TOP 0 hingga diverifikasi admin
            'credit_used_top'  => 0.00,
            'cashback_balance' => 0.00,
            'cashback_rate'    => null,        // Default sistem
            'commission_rate'  => 0.00,
            'is_verified'      => 0,           // Status Belum Terverifikasi (Pending Review Legalitas)
            'npwp_file'        => $npwpPath,
            'nib_file'         => $nibPath,
            'deed_file'        => $deedPath,
            'id_card_file'     => $idCardPath,
            'sppkp_file'       => $sppkpPath,
            'created_at'       => $now,
            'updated_at'       => $now,
        ]);

        // 5. Insert User
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

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat menyimpan pendaftaran. Silakan coba kembali.');
            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Pendaftaran Berhasil! Dokumen legalitas (NPWP, NIB, Akta & KTP) perusahaan ' . $companyName . ' telah berhasil diunggah. Akun Anda sedang dalam antrean verifikasi tim Compliance AkuAda. Silakan masuk ke portal.');
        return redirect()->to('/auth/login');
    }

    /**
     * Helper to safely save uploaded buyer documents
     */
    private function saveBuyerDocument($file, string $prefix, string $companySlug): ?string
    {
        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return null;
        }

        $allowedExts = ['pdf', 'jpg', 'jpeg', 'png'];
        $ext = strtolower($file->getClientExtension());
        if (!in_array($ext, $allowedExts, true)) {
            return null;
        }

        // 5MB limit
        if ($file->getSizeByUnit('mb') > 5) {
            return null;
        }

        $targetDir = FCPATH . 'uploads/buyer_documents/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $safeSlug = preg_replace('/[^a-z0-9_-]/i', '_', substr($companySlug, 0, 25));
        $filename = $prefix . '_' . $safeSlug . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
        $file->move($targetDir, $filename);

        return 'uploads/buyer_documents/' . $filename;
    }

    /**
     * Logout
     */
    public function logout()
    {
        session()->destroy();
        session()->start();
        session()->setFlashdata('success', 'Anda telah berhasil keluar dari akun.');
        return redirect()->to('/auth/login');
    }
}

