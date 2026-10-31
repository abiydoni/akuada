<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('admin/vendors') ?>" class="hover:text-purple-600">Daftar Mitra Vendor</a>
                <span>/</span>
                <span class="text-purple-700 font-bold">Pendaftaran Vendor Baru</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900">Daftarkan Mitra Vendor Resmi Baru</h1>
            <p class="text-xs text-slate-500 mt-0.5">Hanya Superadmin yang berwenang mendaftarkan akun vendor resmi setelah proses due diligence & penandatanganan PKS.</p>
        </div>
    </div>

    <form action="<?= base_url('admin/vendors/store') ?>" method="POST" class="space-y-6 text-xs">
        
        <!-- 1. Legalitas Perusahaan Vendor -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-purple-600">store</span>
                <h2 class="text-sm font-bold text-slate-800">1. Data Legalitas & Profil Badan Usaha Vendor</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Nama Perusahaan Vendor: <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" required placeholder="Contoh: PT Duta Sarana Mandiri" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold text-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Bentuk Badan Hukum: <span class="text-rose-500">*</span></label>
                    <select name="legal_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-bold">
                        <option value="PT">PT (Perseroan Terbatas)</option>
                        <option value="PT Tbk">PT Tbk (Terbuka)</option>
                        <option value="CV">CV (Commanditaire Vennootschap)</option>
                        <option value="Firma">Firma</option>
                        <option value="Koperasi">Koperasi</option>
                        <option value="UD">UD (Usaha Dagang)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">NPWP Badan Usaha (16 Digit): <span class="text-rose-500">*</span></label>
                    <input type="text" name="npwp" required placeholder="01.234.567.8-901.000" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Induk Berusaha (NIB): <span class="text-rose-500">*</span></label>
                    <input type="text" name="nib" required placeholder="9120001234567" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kota / Domisili Gudang: <span class="text-rose-500">*</span></label>
                    <input type="text" name="city" required placeholder="Jakarta Barat / Surabaya / Bandung" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Alamat Lengkap Kantor & Gudang Utama:</label>
                    <textarea name="address" rows="2" placeholder="Jl. Pergudangan Daan Mogot Km. 14, Jakarta Barat"
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-purple-600"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Telepon Kantor:</label>
                    <input type="text" name="phone" placeholder="(021) 5678-9012" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>
            </div>
        </div>

        <!-- 2. Kesepakatan Komisi Platform Khusus Vendor Ini -->
        <div class="bg-gradient-to-br from-purple-50/60 via-blue-50/50 to-pink-50/40 rounded-2xl p-6 border-2 border-purple-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-purple-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-700">handshake</span>
                    <h2 class="text-sm font-bold text-purple-950">2. Tarif Komisi Platform Kesepakatan PKS (Bisa Berbeda Antar Vendor)</h2>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-purple-200 text-purple-900 font-extrabold text-[10px]">Negosiasi Kontrak</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="p-4 bg-white rounded-xl border border-purple-300 shadow-sm space-y-2">
                    <label class="block font-extrabold text-slate-900">Tarif Fee Komisi Kesepakatan (%): <span class="text-rose-500">*</span></label>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.1" min="0" max="100" name="commission_rate" value="<?= $defaultFee ?? 2.5 ?>" required
                               class="w-full px-4 py-2.5 rounded-xl border-2 border-purple-400 focus:border-purple-600 font-black text-xl text-purple-900 text-center">
                        <span class="text-lg font-black text-purple-800">%</span>
                    </div>
                    <p class="text-[11px] text-slate-500">
                        Tarif ini akan otomatis menjadi acuan pemotongan komisi atas setiap produk yang dijual oleh vendor ini ke corporate buyer.
                    </p>
                </div>

                <div class="space-y-2 text-slate-600 leading-relaxed text-[11px]">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-[18px] flex-shrink-0">check_circle</span>
                        <span><strong>Fleksibilitas B2B:</strong> Vendor kategori IT mungkin disepakati 2.0%, ATK 2.5%, atau K3/Industrial 3.5%.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[18px] flex-shrink-0">verified</span>
                        <span><strong>Otomasi Produk:</strong> Seluruh produk yang nantinya di-upload sendiri oleh vendor ini otomatis mewarisi tarif kesepakatan ini.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-purple-600 text-[18px] flex-shrink-0">sync_alt</span>
                        <span>Superadmin tetap dapat mengubah tarif kesepakatan sewaktu-waktu sesuai perpanjangan kontrak PKS.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. PIC Sales & Akun Akses Login Vendor -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-blue-600">manage_accounts</span>
                <h2 class="text-sm font-bold text-slate-800">3. Kontak PIC & Kredensial Akun Login Vendor</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap PIC Vendor: <span class="text-rose-500">*</span></label>
                    <input type="text" name="pic_name" required placeholder="Contoh: Budi Santoso" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. WhatsApp / HP PIC: <span class="text-rose-500">*</span></label>
                    <input type="text" name="pic_phone" required placeholder="0812-3456-7890" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Resmi (Untuk Login Vendor): <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="budi@dutasarana.co.id" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 font-mono font-bold text-blue-900">
                    <span class="text-[10px] text-slate-400">Email ini akan digunakan vendor untuk masuk ke Portal Supplier.</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kata Sandi Akun Default: <span class="text-rose-500">*</span></label>
                    <input type="text" name="password" value="<?= esc(get_system_setting('default_account_password', 'admin123')) ?>" required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 font-mono font-bold text-slate-900">
                    <span class="text-[10px] text-slate-400">Default: <?= esc(get_system_setting('default_account_password', 'admin123')) ?> (Vendor dapat mengganti password setelah login).</span>
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 cursor-pointer">
                    <input type="checkbox" name="is_verified" value="1" checked class="w-4 h-4 text-emerald-600 rounded">
                    <div>
                        <strong class="text-emerald-950 font-bold block text-xs">Langsung Terverifikasi & Aktifkan Status Resmi</strong>
                        <span class="text-[11px] text-emerald-700">Vendor langsung berhak mengunggah katalog barang dan menerima PO dari corporate buyer.</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="<?= base_url('admin/vendors') ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold transition">
                Batal
            </a>
            <button type="submit" class="btn-akuada-primary px-8 py-3 text-xs shadow-lg">
                <span class="material-symbols-outlined text-[18px]">verified_user</span>
                <span>Terbitkan Akun Mitra Vendor & Simpan Kontrak</span>
            </button>
        </div>

    </form>
</div>

<?= $this->endSection() ?>
