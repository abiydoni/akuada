<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<div class="space-y-6">
    <div>
        <h1 class="text-xl font-extrabold text-slate-900">Profil Legalitas & Rekening Settlement Vendor</h1>
        <p class="text-xs text-slate-500 mt-0.5">Identitas resmi perusahaan mitra pemasok yang tercantum pada kontrak PKS, PO, dan Faktur Pajak.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6 text-xs">
        <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
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
                <p class="text-slate-500">Badan Usaha: <strong><?= esc($company['legal_type'] ?? 'PT') ?></strong> | Komisi PKS Platform: <strong><?= esc($company['commission_rate'] ?? 2.5) ?>%</strong></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <span class="font-bold uppercase tracking-wider text-[11px] block text-blue-900">Data Pajak & Perizinan Usaha</span>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">NPWP Badan:</span>
                        <strong class="font-mono text-slate-800"><?= esc($company['npwp'] ?? '-') ?></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nomor Induk Berusaha (NIB):</span>
                        <strong class="font-mono text-slate-800"><?= esc($company['nib'] ?? '-') ?></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status PKP:</span>
                        <strong class="text-emerald-600 font-bold">PKP Aktif (Wajib e-Faktur PPN 11%)</strong>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <span class="font-bold uppercase tracking-wider text-[11px] block text-blue-900">Kontak & Alamat Operasional</span>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
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
    </div>
</div>

<?= $this->endSection() ?>
