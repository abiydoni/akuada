<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('admin/vendors') ?>" class="hover:text-purple-600">Daftar Mitra Vendor</a>
                <span>/</span>
                <span class="text-purple-700 font-bold">Edit Vendor #<?= esc($vendor['id']) ?></span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-extrabold text-slate-900">Edit Data Mitra Vendor</h1>
                <span class="px-2.5 py-0.5 rounded-lg bg-purple-100 text-purple-800 font-mono font-bold text-xs">ID Vendor: <?= esc($vendor['id']) ?></span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui legalitas, profil perusahaan, kontak PIC, rekening bank settlement, dan persentase komisi kontrak PKS.</p>
        </div>
        <a href="<?= base_url('admin/vendors') ?>" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1.5 transition">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Kembali</span>
        </a>
    </div>

    <form action="<?= base_url('admin/vendors/update/' . $vendor['id']) ?>" method="POST" class="space-y-6 text-xs">
        <?= csrf_field() ?>
        
        <!-- 1. Legalitas Perusahaan Vendor -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-purple-600">store</span>
                <h2 class="text-sm font-bold text-slate-800">1. Data Legalitas & Profil Badan Usaha Vendor</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Nama Perusahaan Vendor: <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" value="<?= esc($vendor['company_name']) ?>" required placeholder="Contoh: PT Duta Sarana Mandiri" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold text-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Bentuk Badan Hukum: <span class="text-rose-500">*</span></label>
                    <select name="legal_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-bold">
                        <?php 
                            $legalTypes = ['PT', 'PT Tbk', 'CV', 'Firma', 'Koperasi', 'UD'];
                            foreach ($legalTypes as $lt): 
                        ?>
                            <option value="<?= $lt ?>" <?= ($vendor['legal_type'] === $lt) ? 'selected' : '' ?>><?= $lt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">NPWP Badan Usaha (16 Digit): <span class="text-rose-500">*</span></label>
                    <input type="text" name="npwp" value="<?= esc($vendor['npwp'] ?? '') ?>" required placeholder="01.234.567.8-901.000" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Induk Berusaha (NIB): <span class="text-rose-500">*</span></label>
                    <input type="text" name="nib" value="<?= esc($vendor['nib'] ?? '') ?>" required placeholder="9120001234567" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kota / Domisili Gudang: <span class="text-rose-500">*</span></label>
                    <input type="text" name="city" value="<?= esc($vendor['city'] ?? '') ?>" required placeholder="Jakarta Barat / Surabaya / Bandung" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Provinsi:</label>
                    <input type="text" name="province" value="<?= esc($vendor['province'] ?? 'DKI Jakarta') ?>" placeholder="DKI Jakarta" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Telepon Kantor:</label>
                    <input type="text" name="phone" value="<?= esc($vendor['phone'] ?? '') ?>" placeholder="(021) 5678-9012" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Website Perusahaan:</label>
                    <input type="text" name="website" value="<?= esc($vendor['website'] ?? '') ?>" placeholder="https://perusahaan.co.id" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div class="md:col-span-3">
                    <label class="block font-bold text-slate-700 mb-1">Alamat Lengkap Kantor & Gudang Utama:</label>
                    <textarea name="address" rows="2" placeholder="Jl. Pergudangan Daan Mogot Km. 14, Jakarta Barat"
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-purple-600"><?= esc($vendor['address'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- 2. Rekening Bank Vendor (Pencairan / Settlement) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-purple-600">account_balance</span>
                <h2 class="text-sm font-bold text-slate-800">2. Rekening Bank Penerima (Pencairan Hasil Penjualan)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Bank:</label>
                    <input type="text" name="bank_name" value="<?= esc($vendor['bank_name'] ?? '') ?>" placeholder="Bank Central Asia (BCA) / Mandiri" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Rekening:</label>
                    <input type="text" name="bank_account_number" value="<?= esc($vendor['bank_account_number'] ?? '') ?>" placeholder="135020611062" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Atas Nama Rekening:</label>
                    <input type="text" name="bank_account_holder" value="<?= esc($vendor['bank_account_holder'] ?? '') ?>" placeholder="PT Duta Sarana Mandiri" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>
            </div>
        </div>

        <!-- 3. Kesepakatan Komisi Platform Khusus Vendor Ini -->
        <div class="bg-gradient-to-br from-purple-50/60 via-blue-50/50 to-pink-50/40 rounded-2xl p-6 border-2 border-purple-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-purple-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-700">handshake</span>
                    <h2 class="text-sm font-bold text-purple-950">3. Tarif Komisi Platform Kesepakatan PKS</h2>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-purple-200 text-purple-900 font-extrabold text-[10px]">Negosiasi Kontrak</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="p-4 bg-white rounded-xl border border-purple-300 shadow-sm space-y-2">
                    <label class="block font-extrabold text-slate-900">Tarif Fee Komisi Kesepakatan (%): <span class="text-rose-500">*</span></label>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.1" min="0" max="100" name="commission_rate" value="<?= esc($vendor['commission_rate'] ?? 2.5) ?>" required
                               class="w-full px-4 py-2.5 rounded-xl border-2 border-purple-400 focus:border-purple-600 font-black text-xl text-purple-900 text-center">
                        <span class="text-lg font-black text-purple-800">%</span>
                    </div>
                    <p class="text-[11px] text-slate-500">
                        Tarif komisi platform yang dipotong saat pencairan invoice atas transaksi produk vendor ini.
                    </p>
                </div>

                <div class="space-y-2 text-slate-600 leading-relaxed text-[11px]">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-[18px] flex-shrink-0">check_circle</span>
                        <span><strong>Status Aktif:</strong> Mengubah tarif di sini akan memperbarui profil PKS vendor dan transaksi baru selanjutnya.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-blue-600 text-[18px] flex-shrink-0">verified</span>
                        <span>Perubahan tarif ini langsung tersimpan dan terekam dalam audit log sistem.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. PIC Sales & Akun Akses Login Vendor -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-blue-600">manage_accounts</span>
                <h2 class="text-sm font-bold text-slate-800">4. Kontak PIC & Kredensial Akun Login Vendor</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap PIC Vendor: <span class="text-rose-500">*</span></label>
                    <input type="text" name="pic_name" value="<?= esc($vendor['pic_name'] ?? '') ?>" required placeholder="Contoh: Budi Santoso" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. WhatsApp / HP PIC: <span class="text-rose-500">*</span></label>
                    <input type="text" name="pic_phone" value="<?= esc($vendor['pic_phone'] ?? '') ?>" required placeholder="0812-3456-7890" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Resmi (Untuk Login Vendor): <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="<?= esc($vendor['email']) ?>" required placeholder="budi@dutasarana.co.id" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 font-mono font-bold text-blue-900">
                    <span class="text-[10px] text-slate-400">Email untuk login ke portal vendor.</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Ganti Kata Sandi (Opsional):</label>
                    <input type="text" name="password" placeholder="Kosongkan jika kata sandi tidak ingin diubah" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 font-mono text-slate-900">
                    <span class="text-[10px] text-slate-400">Isi hanya jika ingin mereset password akun vendor ini.</span>
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 p-3 rounded-xl border <?= $vendor['is_verified'] ? 'border-emerald-200 bg-emerald-50/50' : 'border-amber-200 bg-amber-50/50' ?> cursor-pointer">
                    <input type="checkbox" name="is_verified" value="1" <?= $vendor['is_verified'] ? 'checked' : '' ?> class="w-4 h-4 text-emerald-600 rounded">
                    <div>
                        <strong class="<?= $vendor['is_verified'] ? 'text-emerald-950' : 'text-amber-950' ?> font-bold block text-xs">Status Akun Terverifikasi Resmi</strong>
                        <span class="text-[11px] <?= $vendor['is_verified'] ? 'text-emerald-700' : 'text-amber-700' ?>">Centang untuk memberikan hak aktif mengunggah produk dan menerima PO pesanan.</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="<?= base_url('admin/vendors/delete/' . $vendor['id']) ?>" 
               data-confirm="Apakah Anda yakin ingin MENGHAPUS PERMANEN mitra vendor <?= esc($vendor['company_name']) ?> beserta seluruh produk katalognya? Tindakan ini tidak dapat dibatalkan!"
               class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[17px]">delete_forever</span>
                <span>Hapus Vendor Ini</span>
            </a>

            <div class="flex items-center gap-3">
                <a href="<?= base_url('admin/vendors') ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="btn-akuada-primary px-8 py-3 text-xs shadow-lg flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan Perubahan Data Vendor</span>
                </button>
            </div>
        </div>

    </form>
</div>

<?= $this->endSection() ?>
