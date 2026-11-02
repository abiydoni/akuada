<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Profil Legalitas & Rekening Settlement Vendor</h1>
            <p class="text-xs text-slate-500 mt-0.5">Identitas resmi perusahaan mitra pemasok yang tercantum pada kontrak PKS, PO, dan Faktur Pajak.</p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6 text-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-purple-600 text-white flex items-center justify-center font-extrabold text-2xl shadow-md">
                    <?= esc(strtoupper(substr($company['company_name'] ?? 'VN', 0, 2))) ?>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-bold text-slate-900"><?= esc($company['company_name'] ?? 'Mitra Vendor') ?></h2>
                        <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">verified</span> Verified Supplier
                        </span>
                    </div>
                    <p class="text-slate-500">Badan Usaha: <strong><?= esc($company['legal_type'] ?? 'PT') ?></strong> | Komisi PKS Platform: <strong><?= esc($company['commission_rate'] ?? get_default_vendor_fee_rate()) ?>%</strong></p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('editVendorProfileBox').classList.toggle('hidden')" class="btn-akuada-primary px-4 py-2 text-xs flex items-center gap-1.5 self-start sm:self-auto">
                <span class="material-symbols-outlined text-[16px]">edit</span>
                <span>Ubah Legalitas & Rekening Bank</span>
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <span class="font-bold uppercase tracking-wider text-[11px] block text-blue-900">Data Pajak & Rekening Settlement</span>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">NPWP Badan:</span>
                        <strong class="font-mono text-slate-800"><?= esc($company['npwp'] ?? '-') ?></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nomor Induk Berusaha (NIB):</span>
                        <strong class="font-mono text-slate-800"><?= esc($company['nib'] ?? '-') ?></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Rekening Settlement:</span>
                        <strong class="font-mono text-emerald-800 font-bold"><?= esc($company['bank_name'] ?? 'BCA') ?> - <?= esc($company['bank_account_number'] ?? '-') ?></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Atas Nama Rekening:</span>
                        <span class="text-slate-800"><?= esc($company['bank_account_holder'] ?? $company['company_name']) ?></span>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <span class="font-bold uppercase tracking-wider text-[11px] block text-blue-900">Kontak & Alamat Operasional Gudang</span>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Alamat Gudang / Kantor:</span>
                        <strong class="text-slate-800 text-right max-w-xs"><?= esc($company['address'] ?? '-') ?>, <?= esc($company['city'] ?? '') ?></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">PIC Utama Vendor:</span>
                        <strong class="text-slate-800"><?= esc($company['pic_name'] ?? '-') ?> (<?= esc($company['pic_phone'] ?? '-') ?>)</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Email Resmi:</span>
                        <strong class="text-slate-800"><?= esc($company['email'] ?? '-') ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form (Hidden by default) -->
        <div id="editVendorProfileBox" class="hidden pt-6 border-t border-slate-200 space-y-4 animate-in fade-in duration-150">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">edit_document</span>
                <span>Form Pembaruan Profil & Rekening Vendor</span>
            </h3>

            <form action="<?= base_url('vendor/company-profile/update') ?>" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Perusahaan Vendor *</label>
                        <input type="text" name="company_name" required value="<?= esc($company['company_name'] ?? '') ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Bentuk Badan Usaha *</label>
                        <select name="legal_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white font-semibold">
                            <option value="PT" <?= ($company['legal_type'] ?? '') === 'PT' ? 'selected' : '' ?>>PT (Perseroan Terbatas)</option>
                            <option value="CV" <?= ($company['legal_type'] ?? '') === 'CV' ? 'selected' : '' ?>>CV (Commanditaire Vennootschap)</option>
                            <option value="UD" <?= ($company['legal_type'] ?? '') === 'UD' ? 'selected' : '' ?>>UD / Perorangan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">NPWP Vendor *</label>
                        <input type="text" name="npwp" required value="<?= esc($company['npwp'] ?? '') ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor Induk Berusaha (NIB) *</label>
                        <input type="text" name="nib" required value="<?= esc($company['nib'] ?? '') ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Alamat Kantor / Gudang Pengambilan *</label>
                        <textarea name="address" rows="2" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300"><?= esc($company['address'] ?? '') ?></textarea>
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kota *</label>
                        <input type="text" name="city" required value="<?= esc($company['city'] ?? '') ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Provinsi *</label>
                        <input type="text" name="province" required value="<?= esc($company['province'] ?? '') ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                    </div>

                    <!-- Rekening Bank Settlement -->
                    <div class="md:col-span-2 p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-3">
                        <span class="font-bold text-emerald-950 block">Rekening Bank Pencairan Dana Settlement Hak Bersih:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Nama Bank *</label>
                                <input type="text" name="bank_name" required value="<?= esc($company['bank_name'] ?? 'Bank Central Asia (BCA)') ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Nomor Rekening *</label>
                                <input type="text" name="bank_account_number" required value="<?= esc($company['bank_account_number'] ?? '') ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono font-bold">
                            </div>
                            <div>
                                <label class="block font-semibold text-slate-700 mb-1">Atas Nama Rekening *</label>
                                <input type="text" name="bank_account_holder" required value="<?= esc($company['bank_account_holder'] ?? ($company['company_name'] ?? '')) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama PIC Vendor *</label>
                        <input type="text" name="pic_name" required value="<?= esc($company['pic_name'] ?? '') ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">No. HP / WhatsApp PIC *</label>
                        <input type="text" name="pic_phone" required value="<?= esc($company['pic_phone'] ?? '') ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Email Resmi *</label>
                        <input type="email" name="email" required value="<?= esc($company['email'] ?? '') ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('editVendorProfileBox').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-bold">Batal</button>
                    <button type="submit" class="btn-akuada-primary px-6 py-2 text-xs">Simpan Perubahan Vendor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
