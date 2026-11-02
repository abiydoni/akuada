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

$diff         = round($totalAssets - $totalPasiva, 2);
$isBsBalanced = abs($diff) < 1.0;
?>

<div class="space-y-6">
    <?= $this->include('admin/accounting/_nav') ?>

    <!-- Filter & Print Controls -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-wrap items-end justify-between gap-4 text-xs print:hidden">
        <form action="<?= base_url('admin/accounting/balance-sheet') ?>" method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Posisi Neraca Per Tanggal:</label>
                <input type="date" name="as_of_date" value="<?= esc($asOfDate) ?>" class="px-3.5 py-2 rounded-xl border border-slate-300 font-mono font-bold">
            </div>
            <button type="submit" class="btn-akuada-primary px-5 py-2.5 text-xs">Tampilkan Posisi Neraca</button>
        </form>

        <div class="flex items-center gap-3">
            <?php if ($isBsBalanced): ?>
                <span class="px-3.5 py-2 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold">
                    ✓ NERACA SEIMBANG (AKTIVA = PASIVA)
                </span>
            <?php else: ?>
                <span class="px-3.5 py-2 rounded-xl bg-amber-100 text-amber-800 font-extrabold">
                    Selisih Saldo Awal: <?= format_rupiah(abs($diff)) ?>
                </span>
            <?php endif; ?>
            <button type="button" onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak Laporan Neraca</span>
            </button>
        </div>
    </div>

    <!-- Official Balance Sheet Document -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 sm:p-10 space-y-8">
        <!-- Kop Surat -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b-2 border-slate-900">
            <div>
                <h2 class="text-2xl font-black tracking-tight text-slate-900"><?= esc($issuerName) ?></h2>
                <p class="text-xs font-bold text-purple-700"><?= esc($issuerTagline) ?></p>
                <p class="text-[11px] text-slate-500 mt-1"><?= esc($issuerAddress) ?> • NPWP: <?= esc($issuerNpwp) ?></p>
            </div>
            <div class="sm:text-right">
                <span class="px-3 py-1 rounded-lg bg-slate-900 text-white font-black text-xs uppercase tracking-wider">LAPORAN POSISI KEUANGAN (NERACA / BALANCE SHEET)</span>
                <p class="text-xs font-semibold text-slate-600 mt-2">Per Tanggal: <?= date('d F Y', strtotime($asOfDate)) ?></p>
                <p class="text-[11px] text-slate-400">Mata Uang: Rupiah (IDR)</p>
            </div>
        </div>

        <!-- Two-Column Side-by-Side Balance Sheet: AKTIVA (Left) vs PASIVA (Right) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 text-xs items-stretch">

            <!-- LEFT COLUMN: ASET (AKTIVA) -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between h-full">
                <div>
                    <div class="bg-blue-950 text-white px-5 py-3 font-black uppercase tracking-wider text-xs flex justify-between">
                        <span>ASET (AKTIVA)</span>
                        <span>NILAI SALDO (IDR)</span>
                    </div>

                    <!-- Aset Lancar -->
                    <div class="p-4 space-y-2">
                        <h4 class="font-extrabold text-blue-900 uppercase text-[11px] bg-blue-50 px-3 py-1.5 rounded-lg">A. Aset Lancar (Current Assets)</h4>
                        <table class="w-full divide-y divide-slate-100">
                            <?php foreach ($currentAssets as $acc): ?>
                                <tr>
                                    <td class="py-2 px-2 font-mono font-bold text-purple-700 w-24"><?= esc($acc['account_code']) ?></td>
                                    <td class="py-2 px-2 text-slate-800"><?= esc($acc['account_name']) ?></td>
                                    <td class="py-2 px-2 text-right font-mono font-semibold <?= $acc['report_amount'] < 0 ? 'text-rose-600' : 'text-slate-900' ?>">
                                        <?= $acc['report_amount'] < 0 ? '(' . format_rupiah(abs($acc['report_amount'])) . ')' : format_rupiah($acc['report_amount']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="bg-slate-50 font-bold text-slate-900">
                                <td colspan="2" class="py-2.5 px-2 text-right">Total Aset Lancar:</td>
                                <td class="py-2.5 px-2 text-right font-mono"><?= format_rupiah($totalCurrentAssets) ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Aset Tetap -->
                    <div class="p-4 pt-0 space-y-2">
                        <h4 class="font-extrabold text-blue-900 uppercase text-[11px] bg-blue-50 px-3 py-1.5 rounded-lg">B. Aset Tidak Lancar / Aset Tetap (Fixed Assets)</h4>
                        <table class="w-full divide-y divide-slate-100">
                            <?php foreach ($fixedAssets as $acc): ?>
                                <tr>
                                    <td class="py-2 px-2 font-mono font-bold text-purple-700 w-24"><?= esc($acc['account_code']) ?></td>
                                    <td class="py-2 px-2 text-slate-800"><?= esc($acc['account_name']) ?></td>
                                    <td class="py-2 px-2 text-right font-mono font-semibold <?= $acc['report_amount'] < 0 ? 'text-rose-600' : 'text-slate-900' ?>">
                                        <?= $acc['report_amount'] < 0 ? '(' . format_rupiah(abs($acc['report_amount'])) . ')' : format_rupiah($acc['report_amount']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="bg-slate-50 font-bold text-slate-900">
                                <td colspan="2" class="py-2.5 px-2 text-right">Total Aset Tetap (Bersih):</td>
                                <td class="py-2.5 px-2 text-right font-mono"><?= format_rupiah($totalFixedAssets) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="bg-slate-900 text-white px-5 py-4 flex items-center justify-between font-black">
                    <span class="uppercase text-xs">TOTAL ASET (AKTIVA)</span>
                    <span class="font-mono text-base text-emerald-300"><?= format_rupiah($totalAssets) ?></span>
                </div>
            </div>

            <!-- RIGHT COLUMN: KEWAJIBAN & EKUITAS (PASIVA) -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden flex flex-col justify-between h-full">
                <div>
                    <div class="bg-purple-950 text-white px-5 py-3 font-black uppercase tracking-wider text-xs flex justify-between">
                        <span>KEWAJIBAN & EKUITAS (PASIVA)</span>
                        <span>NILAI SALDO (IDR)</span>
                    </div>

                    <!-- Kewajiban Jangka Pendek -->
                    <div class="p-4 space-y-2">
                        <h4 class="font-extrabold text-amber-900 uppercase text-[11px] bg-amber-50 px-3 py-1.5 rounded-lg">A. Kewajiban Jangka Pendek (Current Liabilities)</h4>
                        <table class="w-full divide-y divide-slate-100">
                            <?php foreach ($shortLiabilities as $acc): ?>
                                <tr>
                                    <td class="py-2 px-2 font-mono font-bold text-purple-700 w-24"><?= esc($acc['account_code']) ?></td>
                                    <td class="py-2 px-2 text-slate-800"><?= esc($acc['account_name']) ?></td>
                                    <td class="py-2 px-2 text-right font-mono font-semibold text-slate-900"><?= format_rupiah($acc['report_amount']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="bg-slate-50 font-bold text-slate-900">
                                <td colspan="2" class="py-2.5 px-2 text-right">Total Kewajiban Jangka Pendek:</td>
                                <td class="py-2.5 px-2 text-right font-mono"><?= format_rupiah($totalShortLiabilities) ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Kewajiban Jangka Panjang -->
                    <div class="p-4 pt-0 space-y-2">
                        <h4 class="font-extrabold text-amber-900 uppercase text-[11px] bg-amber-50 px-3 py-1.5 rounded-lg">B. Kewajiban Jangka Panjang</h4>
                        <table class="w-full divide-y divide-slate-100">
                            <?php foreach ($longLiabilities as $acc): ?>
                                <tr>
                                    <td class="py-2 px-2 font-mono font-bold text-purple-700 w-24"><?= esc($acc['account_code']) ?></td>
                                    <td class="py-2 px-2 text-slate-800"><?= esc($acc['account_name']) ?></td>
                                    <td class="py-2 px-2 text-right font-mono font-semibold text-slate-900"><?= format_rupiah($acc['report_amount']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="bg-slate-50 font-bold text-slate-900">
                                <td colspan="2" class="py-2.5 px-2 text-right">Total Kewajiban (Liabilitas):</td>
                                <td class="py-2.5 px-2 text-right font-mono"><?= format_rupiah($totalLiabilities) ?></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Ekuitas -->
                    <div class="p-4 pt-0 space-y-2">
                        <h4 class="font-extrabold text-purple-900 uppercase text-[11px] bg-purple-50 px-3 py-1.5 rounded-lg">C. Ekuitas Pemegang Saham (Shareholders' Equity)</h4>
                        <table class="w-full divide-y divide-slate-100">
                            <?php foreach ($equities as $acc): ?>
                                <tr>
                                    <td class="py-2 px-2 font-mono font-bold text-purple-700 w-24"><?= esc($acc['account_code']) ?></td>
                                    <td class="py-2 px-2 text-slate-800"><?= esc($acc['account_name']) ?></td>
                                    <td class="py-2 px-2 text-right font-mono font-semibold text-slate-900"><?= format_rupiah($acc['report_amount']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="bg-emerald-50/60">
                                <td class="py-2 px-2 font-mono font-bold text-emerald-700 w-24">399-999</td>
                                <td class="py-2 px-2 font-bold text-emerald-900">Laba / (Rugi) Bersih Tahun Berjalan (Dari Laporan R/L)</td>
                                <td class="py-2 px-2 text-right font-mono font-bold text-emerald-800"><?= format_rupiah($netProfitCurrentYear) ?></td>
                            </tr>
                            <tr class="bg-slate-50 font-bold text-slate-900">
                                <td colspan="2" class="py-2.5 px-2 text-right">Total Ekuitas:</td>
                                <td class="py-2.5 px-2 text-right font-mono"><?= format_rupiah($totalEquity) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="bg-slate-900 text-white px-5 py-4 flex items-center justify-between font-black">
                    <span class="uppercase text-xs">TOTAL KEWAJIBAN & EKUITAS (PASIVA)</span>
                    <span class="font-mono text-base text-emerald-300"><?= format_rupiah($totalPasiva) ?></span>
                </div>
            </div>

        </div>

        <!-- Signature Block -->
        <div class="pt-6 border-t border-slate-200 flex justify-end">
            <div class="w-72 text-center space-y-2 text-xs">
                <p class="text-[11px] font-bold text-slate-500 uppercase">Jakarta, <?= date('d F Y', strtotime($asOfDate)) ?></p>
                <p class="font-black text-slate-900"><?= esc($issuerName) ?></p>
                <p class="text-[11px] text-slate-500"><?= esc($signDivision) ?></p>
                <div class="py-3 flex justify-center">
                    <div class="w-20 h-20 rounded-full border-2 border-dashed border-purple-300 flex flex-col items-center justify-center text-purple-700 bg-purple-50/40 -rotate-6">
                        <span class="text-[9px] font-black uppercase"><?= esc($sealText) ?></span>
                        <span class="text-[8px] font-mono"><?= esc($sealSubtext) ?></span>
                    </div>
                </div>
                <p class="font-bold text-slate-900 underline"><?= esc($signName) ?></p>
                <p class="text-[11px] text-slate-500"><?= esc($signTitle) ?></p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

