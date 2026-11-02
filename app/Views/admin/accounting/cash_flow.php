<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>
<?php
$netOperating = $operatingIn - $operatingOut;
$netInvesting = $investingIn - $investingOut;
$netFinancing = $financingIn - $financingOut;
$netCashMove  = $netOperating + $netInvesting + $netFinancing;
?>

<div class="space-y-6">
    <?= $this->include('admin/accounting/_nav') ?>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-teal-100 text-teal-800 text-[10px] font-extrabold uppercase tracking-wider">Statement of Cash Flows</span>
                <span class="text-xs text-slate-400 font-mono">Metode Langsung (Direct Method)</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Laporan Arus Kas (Cash Flow Statement)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi arus kas masuk & keluar dari Aktivitas Operasional, Aktivitas Investasi, dan Aktivitas Pendanaan.</p>
        </div>
        <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-1.5 print:hidden">
            <span class="material-symbols-outlined text-[17px]">print</span>
            <span>Cetak Laporan Arus Kas</span>
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 w-full space-y-6 text-xs">
        <div class="pb-4 border-b-2 border-slate-900 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-black text-slate-900"><?= esc(get_system_setting('inv_issuer_name', 'AKUADA')) ?></h2>
                <p class="text-slate-500">Laporan Arus Kas Konsolidasi Rekening Kas & Bank Escrow</p>
            </div>
            <span class="px-3 py-1 rounded-lg bg-slate-900 text-white font-bold">LAPORAN ARUS KAS</span>
        </div>

        <!-- 1. Aktivitas Operasional -->
        <div class="space-y-2">
            <h3 class="font-black uppercase bg-slate-100 px-4 py-2.5 rounded-xl text-slate-900">1. Arus Kas dari Aktivitas Operasional</h3>
            <div class="px-4 space-y-2">
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span>Penerimaan Kas dari Pelanggan (Pelunasan Piutang PO Buyer & Pendapatan)</span>
                    <strong class="font-mono text-emerald-700">+ <?= format_rupiah($operatingIn) ?></strong>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span>Pengeluaran Kas untuk Settlement Mitra Vendor & Beban Operasional (OPEX)</span>
                    <strong class="font-mono text-rose-700">(<?= format_rupiah($operatingOut) ?>)</strong>
                </div>
                <div class="flex justify-between py-2 font-black text-slate-900 bg-slate-50 px-3 rounded-lg">
                    <span>Arus Kas Bersih dari Aktivitas Operasional:</span>
                    <span class="font-mono"><?= format_rupiah($netOperating) ?></span>
                </div>
            </div>
        </div>

        <!-- 2. Aktivitas Investasi -->
        <div class="space-y-2">
            <h3 class="font-black uppercase bg-slate-100 px-4 py-2.5 rounded-xl text-slate-900">2. Arus Kas dari Aktivitas Investasi</h3>
            <div class="px-4 space-y-2">
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span>Penerimaan / Pelepasan Aset Tetap</span>
                    <strong class="font-mono text-emerald-700">+ <?= format_rupiah($investingIn) ?></strong>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span>Perolehan Peralatan Server IT & Inventaris Kantor</span>
                    <strong class="font-mono text-rose-700">(<?= format_rupiah($investingOut) ?>)</strong>
                </div>
                <div class="flex justify-between py-2 font-black text-slate-900 bg-slate-50 px-3 rounded-lg">
                    <span>Arus Kas Bersih dari Aktivitas Investasi:</span>
                    <span class="font-mono"><?= format_rupiah($netInvesting) ?></span>
                </div>
            </div>
        </div>

        <!-- 3. Aktivitas Pendanaan -->
        <div class="space-y-2">
            <h3 class="font-black uppercase bg-slate-100 px-4 py-2.5 rounded-xl text-slate-900">3. Arus Kas dari Aktivitas Pendanaan</h3>
            <div class="px-4 space-y-2">
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span>Setoran Modal / Penerimaan Pendanaan</span>
                    <strong class="font-mono text-emerald-700">+ <?= format_rupiah($financingIn) ?></strong>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100">
                    <span>Pembayaran Dividen / Kewajiban Jangka Panjang</span>
                    <strong class="font-mono text-rose-700">(<?= format_rupiah($financingOut) ?>)</strong>
                </div>
                <div class="flex justify-between py-2 font-black text-slate-900 bg-slate-50 px-3 rounded-lg">
                    <span>Arus Kas Bersih dari Aktivitas Pendanaan:</span>
                    <span class="font-mono"><?= format_rupiah($netFinancing) ?></span>
                </div>
            </div>
        </div>

        <!-- Reconciliation -->
        <div class="p-5 rounded-2xl bg-gradient-to-r from-slate-900 to-purple-950 text-white space-y-2">
            <div class="flex justify-between text-xs text-purple-200">
                <span>Kenaikan / (Penurunan) Bersih Kas & Setara Kas:</span>
                <strong class="font-mono text-white"><?= format_rupiah($netCashMove) ?></strong>
            </div>
            <div class="flex justify-between text-xs text-purple-200">
                <span>Saldo Kas & Setara Kas Awal Periode:</span>
                <strong class="font-mono text-white"><?= format_rupiah($openingCash) ?></strong>
            </div>
            <div class="pt-2 border-t border-white/20 flex justify-between items-baseline">
                <span class="font-black uppercase text-sm">SALDO KAS & SETARA KAS AKHIR PERIODE (TEREKONSILIASI BUKU BANK):</span>
                <span class="font-mono font-black text-xl text-emerald-300"><?= format_rupiah($endingCash) ?></span>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

