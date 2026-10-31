<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>
<?php
$issuerName    = get_system_setting('inv_issuer_name', 'AKUADA');
$issuerTagline = get_system_setting('inv_issuer_tagline', 'Official E-Procurement & Treasury Division');
$issuerAddress = get_system_setting('inv_issuer_address', 'Gedung AkuAda Procurement Tower Lt. 18, Jakarta Selatan');
$issuerNpwp    = get_system_setting('inv_issuer_npwp', '01.334.556.7-091.000');
$signName      = get_system_setting('inv_sign_name', 'Dra. Maya Anggraini, Ak., CA.');
$signTitle     = get_system_setting('inv_sign_title', 'Finance & Treasury Director');
$signDivision  = get_system_setting('inv_sign_division', 'Finance & Corporate Accounting Division');
$sealText      = get_system_setting('inv_seal_text', 'AKUADA');
$sealSubtext   = get_system_setting('inv_seal_subtext', 'OFFICIAL SEAL');

$grossMarginPercent = $totalRevenue > 0 ? round(($grossProfit / $totalRevenue) * 100, 2) : 0;
$netMarginPercent   = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;
?>

<div class="space-y-6">
    <?= $this->include('admin/accounting/_nav') ?>

    <!-- Filter & Print Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-wrap items-end justify-between gap-4 text-xs print:hidden">
        <form action="<?= base_url('admin/accounting/income-statement') ?>" method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Dari Tanggal:</label>
                <input type="date" name="start_date" value="<?= esc($startDate) ?>" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Sampai Tanggal:</label>
                <input type="date" name="end_date" value="<?= esc($endDate) ?>" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>
            <button type="submit" class="btn-akuada-primary px-5 py-2.5 text-xs">Tampilkan Periode</button>
            <?php if ($startDate || $endDate): ?>
                <a href="<?= base_url('admin/accounting/income-statement') ?>" class="px-3.5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold">Semua Periode</a>
            <?php endif; ?>
        </form>

        <button type="button" onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold flex items-center gap-2 shadow-sm">
            <span class="material-symbols-outlined text-[18px]">print</span>
            <span>Cetak / Simpan PDF Laporan R/L</span>
        </button>
    </div>

    <!-- Executive KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 print:hidden">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Total Pendapatan Usaha</span>
            <div class="text-lg font-black font-mono text-slate-900 mt-1"><?= format_rupiah($totalRevenue) ?></div>
            <span class="text-[10px] text-emerald-600 font-bold">Penjualan B2B + Fee Komisi Vendor</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Laba Kotor (Gross Profit)</span>
            <div class="text-lg font-black font-mono text-blue-700 mt-1"><?= format_rupiah($grossProfit) ?></div>
            <span class="text-[10px] text-slate-500">Gross Margin: <strong><?= $grossMarginPercent ?>%</strong></span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Total Beban Operasional</span>
            <div class="text-lg font-black font-mono text-rose-700 mt-1"><?= format_rupiah($totalExpense) ?></div>
            <span class="text-[10px] text-slate-500">OPEX Gaji, Server & Umum</span>
        </div>
        <div class="bg-gradient-to-br from-purple-900 to-indigo-950 p-5 rounded-2xl text-white shadow-md">
            <span class="text-[11px] font-bold text-purple-200 uppercase">Laba Bersih (Net Profit)</span>
            <div class="text-xl font-black font-mono <?= $netProfit >= 0 ? 'text-emerald-300' : 'text-rose-300' ?> mt-1"><?= format_rupiah($netProfit) ?></div>
            <span class="text-[10px] text-purple-200">Net Profit Margin: <strong><?= $netMarginPercent ?>%</strong></span>
        </div>
    </div>

    <!-- Official Income Statement Document Sheet -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 sm:p-10 max-w-5xl mx-auto space-y-8">
        <!-- Kop Surat Laporan Keuangan -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b-2 border-slate-900">
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-900"><?= esc($issuerName) ?></h2>
                <p class="text-xs font-bold text-purple-700"><?= esc($issuerTagline) ?></p>
                <p class="text-[11px] text-slate-500 mt-1"><?= esc($issuerAddress) ?> • NPWP: <?= esc($issuerNpwp) ?></p>
            </div>
            <div class="sm:text-right">
                <span class="px-3 py-1 rounded-lg bg-slate-900 text-white font-black text-xs uppercase tracking-wider">LAPORAN LABA RUGI (INCOME STATEMENT)</span>
                <p class="text-xs font-semibold text-slate-600 mt-2">
                    Periode: <?= $startDate ? date('d M Y', strtotime($startDate)) : 'Awal Pembukuan' ?> s/d <?= $endDate ? date('d M Y', strtotime($endDate)) : date('d M Y') ?>
                </p>
                <p class="text-[11px] text-slate-400">Mata Uang: Rupiah (IDR)</p>
            </div>
        </div>

        <!-- Statement Body -->
        <div class="space-y-6 text-xs">

            <!-- 1. PENDAPATAN USAHA -->
            <div>
                <h3 class="font-black text-slate-900 uppercase tracking-wider text-xs bg-slate-100 px-4 py-2.5 rounded-xl border border-slate-200">
                    I. PENDAPATAN USAHA (OPERATING REVENUE)
                </h3>
                <table class="w-full mt-2 divide-y divide-slate-100">
                    <?php foreach ($revenues as $acc): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-4 font-mono font-bold text-purple-800 w-28"><?= esc($acc['account_code']) ?></td>
                            <td class="py-2.5 px-2 font-medium text-slate-800"><?= esc($acc['account_name']) ?></td>
                            <td class="py-2.5 px-4 text-right font-mono font-semibold text-slate-900"><?= format_rupiah($acc['report_amount']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="bg-emerald-50/60 font-extrabold text-emerald-950">
                        <td colspan="2" class="py-3 px-4 text-right uppercase">Total Pendapatan Usaha (A):</td>
                        <td class="py-3 px-4 text-right font-mono text-sm"><?= format_rupiah($totalRevenue) ?></td>
                    </tr>
                </table>
            </div>

            <!-- 2. HARGA POKOK PENGADAAN (COGS) -->
            <div>
                <h3 class="font-black text-slate-900 uppercase tracking-wider text-xs bg-slate-100 px-4 py-2.5 rounded-xl border border-slate-200">
                    II. HARGA POKOK PENGADAAN / BEBAN POKOK VENDOR (COGS)
                </h3>
                <table class="w-full mt-2 divide-y divide-slate-100">
                    <?php foreach ($cogs as $acc): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-4 font-mono font-bold text-purple-800 w-28"><?= esc($acc['account_code']) ?></td>
                            <td class="py-2.5 px-2 font-medium text-slate-800"><?= esc($acc['account_name']) ?></td>
                            <td class="py-2.5 px-4 text-right font-mono font-semibold text-slate-900">(<?= format_rupiah($acc['report_amount']) ?>)</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="bg-rose-50/50 font-extrabold text-rose-950">
                        <td colspan="2" class="py-3 px-4 text-right uppercase">Total Harga Pokok Pengadaan (B):</td>
                        <td class="py-3 px-4 text-right font-mono text-sm">(<?= format_rupiah($totalCogs) ?>)</td>
                    </tr>
                </table>
            </div>

            <!-- LABA KOTOR -->
            <div class="p-4 rounded-2xl bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 flex items-center justify-between">
                <div>
                    <span class="font-black text-blue-950 text-sm uppercase">LABA KOTOR (GROSS PROFIT) [A - B]</span>
                    <span class="block text-[11px] text-blue-700">Margin Laba Kotor Platform: <?= $grossMarginPercent ?>%</span>
                </div>
                <span class="font-mono font-black text-lg text-blue-900"><?= format_rupiah($grossProfit) ?></span>
            </div>

            <!-- 3. BEBAN OPERASIONAL (OPEX) -->
            <div>
                <h3 class="font-black text-slate-900 uppercase tracking-wider text-xs bg-slate-100 px-4 py-2.5 rounded-xl border border-slate-200">
                    III. BEBAN OPERASIONAL & UMUM (OPERATING EXPENSES)
                </h3>
                <table class="w-full mt-2 divide-y divide-slate-100">
                    <?php foreach ($expenses as $acc): ?>
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-4 font-mono font-bold text-purple-800 w-28"><?= esc($acc['account_code']) ?></td>
                            <td class="py-2.5 px-2 font-medium text-slate-800"><?= esc($acc['account_name']) ?></td>
                            <td class="py-2.5 px-4 text-right font-mono font-semibold text-slate-900">(<?= format_rupiah($acc['report_amount']) ?>)</td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="bg-slate-50 font-extrabold text-slate-900">
                        <td colspan="2" class="py-3 px-4 text-right uppercase">Total Beban Operasional (C):</td>
                        <td class="py-3 px-4 text-right font-mono text-sm">(<?= format_rupiah($totalExpense) ?>)</td>
                    </tr>
                </table>
            </div>

            <!-- LABA OPERASIONAL -->
            <div class="p-4 rounded-2xl bg-slate-100 border border-slate-300 flex items-center justify-between font-black text-slate-900">
                <span class="uppercase text-xs">LABA USAHA / OPERASIONAL (OPERATING PROFIT / EBIT)</span>
                <span class="font-mono text-base"><?= format_rupiah($operatingProfit) ?></span>
            </div>

            <!-- 4. PENDAPATAN & BEBAN LAIN-LAIN -->
            <div>
                <h3 class="font-black text-slate-900 uppercase tracking-wider text-xs bg-slate-100 px-4 py-2.5 rounded-xl border border-slate-200">
                    IV. PENDAPATAN & BEBAN NON-OPERASIONAL (LAIN-LAIN)
                </h3>
                <table class="w-full mt-2 divide-y divide-slate-100">
                    <?php foreach ($otherIncomes as $acc): ?>
                        <tr>
                            <td class="py-2.5 px-4 font-mono font-bold text-purple-800 w-28"><?= esc($acc['account_code']) ?></td>
                            <td class="py-2.5 px-2 text-slate-800"><?= esc($acc['account_name']) ?></td>
                            <td class="py-2.5 px-4 text-right font-mono text-emerald-700 font-semibold">+ <?= format_rupiah($acc['report_amount']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php foreach ($otherExpenses as $acc): ?>
                        <tr>
                            <td class="py-2.5 px-4 font-mono font-bold text-purple-800 w-28"><?= esc($acc['account_code']) ?></td>
                            <td class="py-2.5 px-2 text-slate-800"><?= esc($acc['account_name']) ?></td>
                            <td class="py-2.5 px-4 text-right font-mono text-rose-700 font-semibold">(<?= format_rupiah($acc['report_amount']) ?>)</td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <!-- LABA BERSIH (NET PROFIT) -->
            <div class="p-5 rounded-2xl bg-gradient-to-r from-purple-900 via-indigo-950 to-slate-900 text-white flex items-center justify-between shadow-lg">
                <div>
                    <span class="text-sm font-black uppercase tracking-wider block">LABA / (RUGI) BERSIH PERIODE BERJALAN (NET INCOME)</span>
                    <span class="text-[11px] text-purple-200">Ditransfer otomatis ke Ekuitas Laba Tahun Berjalan pada Laporan Neraca</span>
                </div>
                <span class="font-mono font-black text-2xl text-emerald-300"><?= format_rupiah($netProfit) ?></span>
            </div>

            <!-- Signature Block from System Settings -->
            <div class="pt-8 border-t border-slate-200 flex justify-end">
                <div class="w-72 text-center space-y-2">
                    <p class="text-[11px] font-bold text-slate-500 uppercase">Jakarta, <?= date('d F Y') ?></p>
                    <p class="text-xs font-black text-slate-900"><?= esc($issuerName) ?></p>
                    <p class="text-[11px] text-slate-500"><?= esc($signDivision) ?></p>
                    <div class="py-3 flex justify-center">
                        <div class="w-20 h-20 rounded-full border-2 border-dashed border-purple-300 flex flex-col items-center justify-center text-purple-700 bg-purple-50/40 -rotate-6">
                            <span class="text-[9px] font-black uppercase"><?= esc($sealText) ?></span>
                            <span class="text-[8px] font-mono"><?= esc($sealSubtext) ?></span>
                        </div>
                    </div>
                    <p class="font-bold text-slate-900 underline text-xs"><?= esc($signName) ?></p>
                    <p class="text-[11px] text-slate-500"><?= esc($signTitle) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

