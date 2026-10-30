<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">
    <div>
        <h1 class="text-xl font-extrabold text-slate-900">Profil Legalitas & NPWP Perusahaan Pembeli</h1>
        <p class="text-xs text-slate-500 mt-0.5">Identitas badan usaha yang tercantum pada seluruh dokumen Purchase Order (PO) dan e-Faktur Pajak resmi.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-6 text-xs">
        <div class="flex items-center gap-4 pb-4 border-b border-slate-100">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-pink-600 via-purple-600 to-indigo-600 text-white flex items-center justify-center font-extrabold text-2xl shadow-md">
                <?= esc(strtoupper(substr($company['company_name'] ?? 'PT', 0, 2))) ?>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900"><?= esc($company['company_name']) ?></h2>
                    <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-800 text-[10px] font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[12px]">verified</span> Corporate Verified
                    </span>
                </div>
                <p class="text-slate-500">Legalitas: <strong><?= esc($company['legal_type']) ?></strong> | Terdaftar: <?= date('d M Y', strtotime($company['created_at'])) ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] block text-purple-900">Data Fiskal Perusahaan</span>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">NPWP Badan (16 Digit):</span>
                        <strong class="font-mono text-slate-800"><?= esc($company['npwp'] ?? '01.999.888.7-077.000') ?></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Nomor Induk Berusaha (NIB):</span>
                        <strong class="font-mono text-slate-800"><?= esc($company['nib'] ?? '9120001122334') ?></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Kelayakan Kredit TOP:</span>
                        <strong class="text-emerald-600 font-bold">Grade A (Plafon Aktif)</strong>
                    </div>
                </div>
            </div>

            <div class="space-y-3">
                <span class="font-bold text-slate-700 uppercase tracking-wider text-[11px] block text-purple-900">Kontak Departemen Pengadaan</span>
                <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Alamat Kantor Pusat:</span>
                        <strong class="text-slate-800 text-right max-w-xs"><?= esc($company['address']) ?></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Lead PIC Procurement:</span>
                        <strong class="text-slate-800"><?= esc($company['pic_name']) ?></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Email Kantor:</span>
                        <strong class="text-slate-800"><?= esc($company['email']) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
