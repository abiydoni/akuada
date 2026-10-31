<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>
<?php
$totOpenDebit  = 0.0;
$totOpenCredit = 0.0;
$totMutDebit   = 0.0;
$totMutCredit  = 0.0;
$totEndDebit   = 0.0;
$totEndCredit  = 0.0;

foreach ($accounts as &$acc) {
    $isNormalDebit = ($acc['normal_balance'] === 'DEBIT');
    $openBal       = (float)$acc['effective_opening'];
    $endBal        = (float)$acc['ending_balance'];

    $acc['tb_open_debit']  = ($isNormalDebit && $openBal >= 0) || (!$isNormalDebit && $openBal < 0) ? abs($openBal) : 0.0;
    $acc['tb_open_credit'] = (!$isNormalDebit && $openBal >= 0) || ($isNormalDebit && $openBal < 0) ? abs($openBal) : 0.0;

    $acc['tb_end_debit']   = ($isNormalDebit && $endBal >= 0) || (!$isNormalDebit && $endBal < 0) ? abs($endBal) : 0.0;
    $acc['tb_end_credit']  = (!$isNormalDebit && $endBal >= 0) || ($isNormalDebit && $endBal < 0) ? abs($endBal) : 0.0;

    $totOpenDebit  += $acc['tb_open_debit'];
    $totOpenCredit += $acc['tb_open_credit'];
    $totMutDebit   += (float)$acc['mutation_debit'];
    $totMutCredit  += (float)$acc['mutation_credit'];
    $totEndDebit   += $acc['tb_end_debit'];
    $totEndCredit  += $acc['tb_end_credit'];
}
unset($acc);

$isBalanced = abs($totEndDebit - $totEndCredit) < 1.0;
?>

<div class="space-y-6">
    <?= $this->include('admin/accounting/_nav') ?>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-extrabold uppercase tracking-wider">Trial Balance Worksheet</span>
                <?php if ($isBalanced): ?>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">✓ NERACA SALDO SEIMBANG (BALANCE)</span>
                <?php else: ?>
                    <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold">⚠ SELISIH SALDO AWAL: <?= format_rupiah(abs($totEndDebit - $totEndCredit)) ?></span>
                <?php endif; ?>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Neraca Saldo (Trial Balance)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi saldo awal, pergerakan mutasi Debit/Kredit, dan saldo akhir seluruh kode akun COA <code class="font-mono font-bold text-purple-700">###-###</code>.</p>
        </div>
        <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-1.5 print:hidden">
            <span class="material-symbols-outlined text-[17px]">print</span>
            <span>Cetak Neraca Saldo</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm print:hidden">
        <form action="<?= base_url('admin/accounting/trial-balance') ?>" method="GET" class="flex flex-wrap items-end gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Dari Tanggal:</label>
                <input type="date" name="start_date" value="<?= esc($startDate) ?>" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Sampai Tanggal:</label>
                <input type="date" name="end_date" value="<?= esc($endDate) ?>" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>
            <button type="submit" class="btn-akuada-primary px-5 py-2.5 text-xs">Filter Periode</button>
            <?php if ($startDate || $endDate): ?>
                <a href="<?= base_url('admin/accounting/trial-balance') ?>" class="px-3.5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- 6-Column Trial Balance Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-900 text-white text-[10px] uppercase tracking-wider font-bold">
                        <th rowspan="2" class="py-3 px-4 border-r border-slate-700">Kode COA</th>
                        <th rowspan="2" class="py-3 px-4 border-r border-slate-700">Nama Akun Perkiraan</th>
                        <th colspan="2" class="py-2 px-4 text-center border-b border-r border-slate-700 bg-slate-800">Saldo Awal</th>
                        <th colspan="2" class="py-2 px-4 text-center border-b border-r border-slate-700 bg-indigo-950">Mutasi Pergerakan</th>
                        <th colspan="2" class="py-2 px-4 text-center border-b border-slate-700 bg-purple-950">Saldo Akhir</th>
                    </tr>
                    <tr class="bg-slate-800 text-slate-200 text-[10px] uppercase font-bold">
                        <th class="py-2 px-3 text-right">Debit</th>
                        <th class="py-2 px-3 text-right border-r border-slate-700">Kredit</th>
                        <th class="py-2 px-3 text-right bg-indigo-900/60">Debit</th>
                        <th class="py-2 px-3 text-right border-r border-slate-700 bg-indigo-900/60">Kredit</th>
                        <th class="py-2 px-3 text-right bg-purple-900/60">Debit</th>
                        <th class="py-2 px-3 text-right bg-purple-900/60">Kredit</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($accounts as $acc): ?>
                        <tr class="hover:bg-purple-50/30 transition">
                            <td class="py-2.5 px-4 font-mono font-black text-purple-800 whitespace-nowrap">
                                <a href="<?= base_url('admin/accounting/ledger?coa_id=' . $acc['id']) ?>" class="hover:underline"><?= esc($acc['account_code']) ?></a>
                            </td>
                            <td class="py-2.5 px-4 font-semibold text-slate-800">
                                <?= esc($acc['account_name']) ?>
                                <span class="text-[10px] text-slate-400 ml-1">(<?= esc($acc['report_position']) ?>)</span>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-600"><?= $acc['tb_open_debit'] > 0 ? format_rupiah($acc['tb_open_debit']) : '-' ?></td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-600 border-r border-slate-100"><?= $acc['tb_open_credit'] > 0 ? format_rupiah($acc['tb_open_credit']) : '-' ?></td>
                            <td class="py-2.5 px-3 text-right font-mono text-indigo-700 bg-indigo-50/20"><?= $acc['mutation_debit'] > 0 ? format_rupiah($acc['mutation_debit']) : '-' ?></td>
                            <td class="py-2.5 px-3 text-right font-mono text-pink-700 border-r border-slate-100 bg-indigo-50/20"><?= $acc['mutation_credit'] > 0 ? format_rupiah($acc['mutation_credit']) : '-' ?></td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900 bg-purple-50/20"><?= $acc['tb_end_debit'] > 0 ? format_rupiah($acc['tb_end_debit']) : '-' ?></td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900 bg-purple-50/20"><?= $acc['tb_end_credit'] > 0 ? format_rupiah($acc['tb_end_credit']) : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-slate-900 text-white font-bold text-xs">
                    <tr>
                        <td colspan="2" class="py-3.5 px-4 text-right uppercase tracking-wider text-[11px]">TOTAL NERACA SALDO:</td>
                        <td class="py-3.5 px-3 text-right font-mono text-slate-200"><?= format_rupiah($totOpenDebit) ?></td>
                        <td class="py-3.5 px-3 text-right font-mono text-slate-200 border-r border-slate-700"><?= format_rupiah($totOpenCredit) ?></td>
                        <td class="py-3.5 px-3 text-right font-mono text-indigo-300"><?= format_rupiah($totMutDebit) ?></td>
                        <td class="py-3.5 px-3 text-right font-mono text-pink-300 border-r border-slate-700"><?= format_rupiah($totMutCredit) ?></td>
                        <td class="py-3.5 px-3 text-right font-mono text-emerald-300"><?= format_rupiah($totEndDebit) ?></td>
                        <td class="py-3.5 px-3 text-right font-mono text-emerald-300"><?= format_rupiah($totEndCredit) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

