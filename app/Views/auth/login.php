<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-900 via-purple-950 to-slate-900 relative overflow-hidden">
    <!-- Ambient glow -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-purple-600/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-pink-500/25 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-md w-full space-y-8 bg-white/95 backdrop-blur-xl p-8 rounded-3xl border border-white/20 shadow-2xl relative z-10">
        <!-- Logo -->
        <div class="text-center">
            <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-2.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-700 via-blue-600 to-pink-500 flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                    <span class="material-symbols-outlined text-3xl">domain</span>
                </div>
            </a>
            <h2 class="mt-4 text-2xl font-extrabold text-slate-900 tracking-tight">
                Masuk ke Portal AkuAda
            </h2>
            <p class="mt-1 text-xs text-slate-500">
                Portal E-Procurement Pengadaan Operasional Perusahaan B2B
            </p>
        </div>

        <!-- Quick 1-Click Role Access Buttons for Evaluation -->
        <div class="bg-purple-50 p-3.5 rounded-2xl border border-purple-200 text-xs space-y-2">
            <span class="font-bold text-purple-900 block text-[11px] uppercase tracking-wider">Akses Cepat 1-Klik Akun Demo:</span>
            <div class="grid grid-cols-3 gap-1.5">
                <a href="<?= base_url('auth/switch-role/admin') ?>" class="px-2 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-semibold text-center text-[10px] transition shadow-sm">
                    Admin
                </a>
                <a href="<?= base_url('auth/switch-role/vendor') ?>" class="px-2 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-semibold text-center text-[10px] transition shadow-sm">
                    Vendor
                </a>
                <a href="<?= base_url('auth/switch-role/customer') ?>" class="px-2 py-1.5 rounded-lg bg-pink-600 hover:bg-pink-700 text-white font-semibold text-center text-[10px] transition shadow-sm">
                    Corporate Buyer
                </a>
            </div>
        </div>

        <!-- Login Form -->
        <form action="<?= base_url('auth/login/process') ?>" method="POST" class="mt-6 space-y-4 text-xs">
            <div>
                <label for="email" class="block font-bold text-slate-700 mb-1">Email Perusahaan / Akun:</label>
                <input type="email" id="email" name="email" required placeholder="admin@akuada.co.id" 
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition text-slate-800">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block font-bold text-slate-700">Kata Sandi:</label>
                    <span class="text-[11px] text-purple-600 hover:underline cursor-pointer">Lupa Sandi?</span>
                </div>
                <input type="password" id="password" name="password" required placeholder="••••••••" value="admin123"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition text-slate-800">
                <span class="text-[10px] text-slate-400 mt-1 block">Default password demo: <code>admin123</code></span>
            </div>

            <button type="submit" class="w-full btn-akuada-primary py-3 text-xs mt-2">
                <span class="material-symbols-outlined text-[18px]">login</span>
                <span>Masuk Sekarang</span>
            </button>
        </form>

        <div class="text-center pt-2 border-t border-slate-100 text-xs text-slate-500">
            Belum memiliki akun perusahaan? 
            <a href="<?= base_url('auth/register') ?>" class="font-bold text-purple-700 hover:underline">
                Daftar Perusahaan Baru &rarr;
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
