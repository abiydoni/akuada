<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-900 via-purple-950 to-slate-900 flex items-center justify-center relative overflow-hidden">
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-purple-600/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-pink-500/25 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-2xl w-full bg-white/95 backdrop-blur-xl p-8 sm:p-10 rounded-3xl border border-white/20 shadow-2xl relative z-10 space-y-6">
        <div class="text-center space-y-1">
            <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-700 via-blue-600 to-pink-500 flex items-center justify-center text-white shadow-md">
                    <span class="material-symbols-outlined text-2xl">domain</span>
                </div>
            </a>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                Pendaftaran Akun Perusahaan (Corporate Buyer)
            </h2>
            <p class="text-xs text-slate-500">
                Dapatkan akses langsung ke harga grosir tiering, faktur pajak resmi, dan pengajuan limit kredit TOP.
            </p>
        </div>

        <form action="<?= base_url('auth/register/process') ?>" method="POST" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Perusahaan: <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" required placeholder="PT Sinergi Mandiri Abadi" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Bentuk Legalitas: <span class="text-rose-500">*</span></label>
                    <select name="legal_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 bg-white">
                        <option value="PT">PT (Perseroan Terbatas)</option>
                        <option value="PT Tbk">PT Tbk (Terbuka)</option>
                        <option value="CV">CV (Commanditaire Vennootschap)</option>
                        <option value="BUMN">BUMN / BUMD</option>
                        <option value="Yayasan">Yayasan / Lembaga</option>
                        <option value="UD">UD / Perorangan Usaha</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">NPWP Perusahaan (16 Digit): <span class="text-rose-500">*</span></label>
                    <input type="text" name="npwp" required placeholder="01.234.567.8-901.000" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Induk Berusaha (NIB):</label>
                    <input type="text" name="nib" placeholder="9120001234567" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 font-mono">
                </div>
            </div>

            <div class="border-t border-slate-100 pt-3">
                <span class="text-[11px] font-bold text-purple-900 uppercase tracking-wider block mb-3">Informasi PIC Procurement (Penanggung Jawab)</span>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Lengkap PIC: <span class="text-rose-500">*</span></label>
                        <input type="text" name="pic_name" required placeholder="Budi Santoso, S.T." 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Resmi Kantor: <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" required placeholder="procurement@perusahaan.com" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">No. WhatsApp / HP: <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" required placeholder="08123456789" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Kata Sandi Akun: <span class="text-rose-500">*</span></label>
                <input type="password" name="password" required placeholder="Minimal 8 karakter" value="admin123"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
            </div>

            <div class="p-3 bg-purple-50 rounded-xl border border-purple-200 text-[11px] text-purple-900">
                Plafon awal kredit tempo <strong>TOP Rp 100.000.000</strong> akan otomatis disiapkan untuk akun perusahaan baru Anda setelah pendaftaran.
            </div>

            <button type="submit" class="w-full btn-akuada-primary py-3 text-xs mt-2">
                <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
                <span>Daftarkan Perusahaan Sekarang</span>
            </button>
        </form>

        <div class="space-y-3 pt-2 border-t border-slate-100 text-center text-xs">
            <div class="text-slate-500">
                Sudah memiliki akun? 
                <a href="<?= base_url('auth/login') ?>" class="font-bold text-purple-700 hover:underline">
                    Masuk ke Akun Anda &rarr;
                </a>
            </div>

            <!-- Notice for Vendors -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-[11px] text-slate-600 text-left flex items-start gap-2">
                <span class="material-symbols-outlined text-blue-600 text-[18px] flex-shrink-0 mt-0.5">verified_user</span>
                <div>
                    <strong class="text-slate-900 block font-semibold">Pendaftaran Rekanan Mitra Supplier / Vendor:</strong>
                    <span>Sesuai regulasi kepatuhan B2B AkuAda, akun vendor didaftarkan secara terpusat oleh Admin setelah proses verifikasi legalitas dan penetapan Perjanjian Kerjasama (PKS). Hubungi <a href="mailto:vendor@akuada.co.id" class="text-blue-700 font-bold underline">vendor@akuada.co.id</a> untuk pengajuan kemitraan.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
