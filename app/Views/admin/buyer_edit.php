<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('admin/buyers') ?>" class="hover:text-purple-600">Daftar Perusahaan Buyer</a>
                <span>/</span>
                <span class="text-purple-700 font-bold">Edit Buyer #<?= esc($buyer['id']) ?></span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-extrabold text-slate-900">Edit Data Perusahaan Buyer</h1>
                <span class="px-2.5 py-0.5 rounded-lg bg-pink-100 text-pink-800 font-mono font-bold text-xs">ID Buyer: <?= esc($buyer['id']) ?></span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui profil korporat, kontak PIC procurement, fasilitas plafon kredit TOP, tarif cashback, dan dokumen legalitas.</p>
        </div>
        <a href="<?= base_url('admin/buyers') ?>" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1.5 transition">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Kembali</span>
        </a>
    </div>

    <form action="<?= base_url('admin/buyers/update/' . $buyer['id']) ?>" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
        <?= csrf_field() ?>
        
        <!-- 1. Legalitas Perusahaan Buyer -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-purple-600">domain</span>
                <h2 class="text-sm font-bold text-slate-800">1. Data Legalitas & Profil Badan Usaha Pembeli (Corporate Buyer)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Nama Perusahaan Resmi: <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" value="<?= esc($buyer['company_name']) ?>" required placeholder="Contoh: PT Surya Makmur Sentosa" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold text-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Bentuk Badan Usaha: <span class="text-rose-500">*</span></label>
                    <select name="legal_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-bold">
                        <?php 
                            $legalTypes = ['PT', 'PT Tbk', 'BUMN', 'CV', 'Yayasan', 'Hotel/Resto', 'Koperasi', 'Lainnya'];
                            foreach ($legalTypes as $lt): 
                        ?>
                            <option value="<?= $lt ?>" <?= ($buyer['legal_type'] === $lt) ? 'selected' : '' ?>><?= $lt ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">NPWP Perusahaan (16 Digit): <span class="text-rose-500">*</span></label>
                    <input type="text" name="npwp" value="<?= esc($buyer['npwp'] ?? '') ?>" required placeholder="01.999.888.7-077.000" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Induk Berusaha (NIB): <span class="text-rose-500">*</span></label>
                    <input type="text" name="nib" value="<?= esc($buyer['nib'] ?? '') ?>" required placeholder="9120001122334" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Bidang Usaha / Industri:</label>
                    <input type="text" name="business_field" value="<?= esc($buyer['business_field'] ?? 'Perdagangan Umum & Jasa') ?>" placeholder="Perhotelan / Manufaktur / Kontraktor" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kota Domisili Kantor / Titik Serah: <span class="text-rose-500">*</span></label>
                    <input type="text" name="city" value="<?= esc($buyer['city'] ?? 'Jakarta Selatan') ?>" required placeholder="Jakarta Selatan / Surabaya" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Provinsi:</label>
                    <input type="text" name="province" value="<?= esc($buyer['province'] ?? 'DKI Jakarta') ?>" placeholder="DKI Jakarta" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Telepon Kantor:</label>
                    <input type="text" name="phone" value="<?= esc($buyer['phone'] ?? '') ?>" placeholder="(021) 7890-1234" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Website Perusahaan:</label>
                    <input type="text" name="website" value="<?= esc($buyer['website'] ?? '') ?>" placeholder="https://perusahaan.co.id" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Alamat Kantor Resmi & Pengiriman PO:</label>
                    <textarea name="address" rows="2" placeholder="Gedung Cyber 2 Tower Lt. 15, Jl. HR Rasuna Said, Jakarta Selatan" 
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-purple-600"><?= esc($buyer['address'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- 2. Fasilitas Plafon Kredit TOP & Cashback -->
        <div class="bg-gradient-to-br from-pink-50/60 via-purple-50/50 to-blue-50/40 rounded-2xl p-6 border-2 border-pink-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-pink-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-pink-700">credit_card</span>
                    <h2 class="text-sm font-bold text-pink-950">2. Fasilitas Plafon Kredit TOP (Term of Payment) & Saldo Cashback</h2>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-pink-200 text-pink-900 font-extrabold text-[10px]">Financial Facility</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="p-4 bg-white rounded-xl border border-pink-300 shadow-sm space-y-3">
                    <div>
                        <label class="block font-extrabold text-slate-900 mb-1">Plafon Limit Kredit TOP (Rp): <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center font-bold text-slate-400">Rp</span>
                            <input type="number" step="1000000" min="0" name="credit_limit_top" value="<?= esc($buyer['credit_limit_top'] ?? 100000000) ?>" required
                                   class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 focus:border-pink-600 font-black text-lg text-purple-950 font-mono">
                        </div>
                        <div class="flex justify-between items-center text-[10px] text-slate-500 pt-1">
                            <span>Kredit Terpakai: <strong class="font-mono text-rose-600"><?= format_rupiah($buyer['credit_used_top'] ?? 0) ?></strong></span>
                            <span>Sisa Plafon: <strong class="font-mono text-emerald-600"><?= format_rupiah(max(0, (float)($buyer['credit_limit_top'] ?? 0) - (float)($buyer['credit_used_top'] ?? 0))) ?></strong></span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <label class="block font-bold text-slate-800 mb-1">Persentase Fee Cashback Khusus (%):</label>
                        <div class="relative">
                            <input type="number" step="0.01" min="0" max="100" name="cashback_rate" value="<?= esc($buyer['cashback_rate'] ?? '') ?>" placeholder="Kosongkan untuk ikut tarif default sistem (<?= get_buyer_cashback_rate() ?>%)"
                                   class="w-full pr-10 pl-3.5 py-2 rounded-xl border border-slate-300 focus:border-pink-600 font-mono text-xs">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center font-bold text-slate-400">%</span>
                        </div>
                        <span class="text-[10px] text-slate-400 block pt-0.5">Saldo cashback saat ini: <strong class="font-mono text-purple-900"><?= format_rupiah($buyer['cashback_balance'] ?? 0) ?></strong></span>
                    </div>
                </div>

                <div class="space-y-2 text-slate-600 leading-relaxed text-[11px]">
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-[18px] flex-shrink-0">verified</span>
                        <span><strong>Proteksi B2B:</strong> Buyer dapat berbelanja secara tempo resmi dengan sistem invoice dan BAST.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-purple-600 text-[18px] flex-shrink-0">receipt_long</span>
                        <span><strong>Batas Nota:</strong> Pembelian baru akan otomatis tertahan jika buyer telah mencapai batas 3 nota belum lunas.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="material-symbols-outlined text-pink-600 text-[18px] flex-shrink-0">savings</span>
                        <span>Cashback otomatis diakumulasikan setiap kali buyer melunasi invoice pesanan TOP.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. PIC Procurement & Akun Login Buyer -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-blue-600">manage_accounts</span>
                <h2 class="text-sm font-bold text-slate-800">3. Kontak PIC Procurement & Akun Akses Portal Buyer</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap PIC Procurement: <span class="text-rose-500">*</span></label>
                    <input type="text" name="pic_name" value="<?= esc($buyer['pic_name'] ?? '') ?>" required placeholder="Contoh: Fajar Kurniawan" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jabatan PIC: <span class="text-rose-500">*</span></label>
                    <input type="text" name="position" value="<?= esc($user['position'] ?? 'Procurement Manager') ?>" required placeholder="Procurement Manager / General Affair Lead" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. WhatsApp / HP PIC: <span class="text-rose-500">*</span></label>
                    <input type="text" name="pic_phone" value="<?= esc($buyer['pic_phone'] ?? '') ?>" required placeholder="0812-9876-5432" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Resmi (Untuk Login Portal Buyer): <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="<?= esc($buyer['email']) ?>" required placeholder="procurement@suryamakmur.co.id" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 font-mono font-bold text-purple-900">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Ganti Kata Sandi (Opsional):</label>
                    <input type="text" name="password" placeholder="Kosongkan jika kata sandi tidak ingin diubah" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 font-mono text-slate-900">
                    <span class="text-[10px] text-slate-400">Isi hanya jika ingin mereset password akun buyer ini.</span>
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 p-3 rounded-xl border <?= $buyer['is_verified'] ? 'border-emerald-200 bg-emerald-50/50' : 'border-amber-200 bg-amber-50/50' ?> cursor-pointer">
                    <input type="checkbox" name="is_verified" value="1" <?= $buyer['is_verified'] ? 'checked' : '' ?> class="w-4 h-4 text-emerald-600 rounded">
                    <div>
                        <strong class="<?= $buyer['is_verified'] ? 'text-emerald-950' : 'text-amber-950' ?> font-bold block text-xs">Status Akun Buyer Terverifikasi Resmi</strong>
                        <span class="text-[11px] <?= $buyer['is_verified'] ? 'text-emerald-700' : 'text-amber-700' ?>">Centang untuk menyetujui verifikasi legalitas dan mengizinkan penerbitan Purchase Order (PO).</span>
                    </div>
                </label>
            </div>
        </div>

        <!-- 4. Berkas Legalitas & Kepatuhan KYC -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-indigo-600">folder_open</span>
                <h2 class="text-sm font-bold text-slate-800">4. Kelengkapan Berkas Legalitas Perusahaan (Unggah Ulang jika Diperlukan)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- NPWP File -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-slate-800">Salinan NPWP Perusahaan:</label>
                        <?php if (!empty($buyer['npwp_file'])): ?>
                            <a href="<?= base_url($buyer['npwp_file']) ?>" target="_blank" class="text-purple-600 font-bold hover:underline flex items-center gap-0.5 text-[10px]">
                                <span class="material-symbols-outlined text-[13px]">visibility</span> Lihat File
                            </a>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="npwp_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs">
                </div>

                <!-- NIB File -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-slate-800">Salinan NIB (Nomor Induk Berusaha):</label>
                        <?php if (!empty($buyer['nib_file'])): ?>
                            <a href="<?= base_url($buyer['nib_file']) ?>" target="_blank" class="text-purple-600 font-bold hover:underline flex items-center gap-0.5 text-[10px]">
                                <span class="material-symbols-outlined text-[13px]">visibility</span> Lihat File
                            </a>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="nib_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs">
                </div>

                <!-- Akta Pendirian -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-slate-800">Akta Pendirian & SK Kemenkumham:</label>
                        <?php if (!empty($buyer['deed_file'])): ?>
                            <a href="<?= base_url($buyer['deed_file']) ?>" target="_blank" class="text-purple-600 font-bold hover:underline flex items-center gap-0.5 text-[10px]">
                                <span class="material-symbols-outlined text-[13px]">visibility</span> Lihat File
                            </a>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="deed_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs">
                </div>

                <!-- KTP PIC -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-slate-800">KTP PIC Procurement / Direksi:</label>
                        <?php if (!empty($buyer['id_card_file'])): ?>
                            <a href="<?= base_url($buyer['id_card_file']) ?>" target="_blank" class="text-purple-600 font-bold hover:underline flex items-center gap-0.5 text-[10px]">
                                <span class="material-symbols-outlined text-[13px]">visibility</span> Lihat File
                            </a>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="id_card_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs">
                </div>

                <!-- SPPKP File -->
                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 space-y-1.5 md:col-span-2">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-slate-800">Surat Pengukuhan Pengusaha Kena Pajak (SPPKP - Opsional):</label>
                        <?php if (!empty($buyer['sppkp_file'])): ?>
                            <a href="<?= base_url($buyer['sppkp_file']) ?>" target="_blank" class="text-purple-600 font-bold hover:underline flex items-center gap-0.5 text-[10px]">
                                <span class="material-symbols-outlined text-[13px]">visibility</span> Lihat File
                            </a>
                        <?php endif; ?>
                    </div>
                    <input type="file" name="sppkp_file" accept=".pdf,.jpg,.jpeg,.png" class="w-full text-xs">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between pt-2">
            <a href="<?= base_url('admin/buyers/delete/' . $buyer['id']) ?>" 
               data-confirm="Apakah Anda yakin ingin MENGHAPUS PERMANEN akun perusahaan buyer <?= esc($buyer['company_name']) ?>? Tindakan ini tidak dapat dibatalkan!"
               class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[17px]">delete_forever</span>
                <span>Hapus Buyer Ini</span>
            </a>

            <div class="flex items-center gap-3">
                <a href="<?= base_url('admin/buyers') ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="btn-akuada-primary px-8 py-3 text-xs shadow-lg flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan Perubahan Data Buyer</span>
                </button>
            </div>
        </div>

    </form>
</div>

<?= $this->endSection() ?>
