<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <?= $this->include('admin/accounting/_nav') ?>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800 text-[10px] font-extrabold uppercase tracking-wider">General Ledger (GL)</span>
                <span class="text-xs text-slate-400 font-mono">Rincian Mutasi & Saldo Berjalan per Akun COA</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Buku Besar (General Ledger)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Telusuri histori mutasi Debit, Kredit, dan Saldo Berjalan (*Running Balance*) untuk setiap kode akun <code class="font-mono font-bold text-purple-700">###-###</code>.</p>
        </div>
        <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs flex items-center gap-1.5 print:hidden">
            <span class="material-symbols-outlined text-[17px]">print</span>
            <span>Cetak Buku Besar</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm print:hidden">
        <form action="<?= base_url('admin/accounting/ledger') ?>" method="GET" class="flex flex-wrap items-end gap-3 text-xs">
            <div class="flex-1 min-w-[260px]">
                <label class="block font-bold text-slate-700 mb-1">Pilih Akun Perkiraan (COA ###-###):</label>
                <select name="coa_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white font-bold text-slate-900">
                    <option value="">-- Tampilkan Semua Akun yang Memiliki Saldo / Mutasi --</option>
                    <?php foreach ($coaList as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= (int)$selectedId === (int)$c['id'] ? 'selected' : '' ?>>
                            [<?= esc($c['account_code']) ?>] <?= esc($c['account_name']) ?> (<?= esc($c['normal_balance']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Dari Tanggal:</label>
                <input type="date" name="start_date" value="<?= esc($startDate) ?>" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Sampai Tanggal:</label>
                <input type="date" name="end_date" value="<?= esc($endDate) ?>" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>
            <button type="submit" class="btn-akuada-primary px-5 py-2.5 text-xs">
                <span class="material-symbols-outlined text-[16px]">search</span> Tampilkan Buku Besar
            </button>
            <?php if ($selectedId || $startDate || $endDate): ?>
                <a href="<?= base_url('admin/accounting/ledger') ?>" class="px-3.5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Ledger Cards per Account -->
    <div class="space-y-6">
        <?php foreach ($ledgers as $lg):
            $acc = $lg['account'];
        ?>
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <!-- Account Card Header -->
                <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-purple-950 text-white px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-xl bg-white/15 font-mono font-black text-base text-purple-200 border border-white/20">
                            <?= esc($acc['account_code']) ?>
                        </span>
                        <div>
                            <h2 class="text-sm font-extrabold text-white"><?= esc($acc['account_name']) ?></h2>
                            <p class="text-[11px] text-purple-200">
                                Kelompok: <strong><?= esc($acc['account_group']) ?></strong> •
                                Saldo Normal: <strong><?= esc($acc['normal_balance']) ?></strong> •
                                Pos: <strong><?= esc($acc['report_position']) ?></strong>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] uppercase tracking-wider text-purple-200 block">Saldo Akhir Akun:</span>
                        <span class="font-mono font-black text-lg text-emerald-300"><?= format_rupiah($lg['ending_balance']) ?></span>
                    </div>
                </div>

                <!-- Ledger Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-500">
                                <th class="py-2.5 px-4 w-28">Tanggal</th>
                                <th class="py-2.5 px-4 w-40">No. Jurnal</th>
                                <th class="py-2.5 px-4 w-36">No. Referensi</th>
                                <th class="py-2.5 px-4">Keterangan / Uraian Mutasi</th>
                                <th class="py-2.5 px-4 text-right w-36">Debit (IDR)</th>
                                <th class="py-2.5 px-4 text-right w-36">Kredit (IDR)</th>
                                <th class="py-2.5 px-5 text-right w-44">Saldo Berjalan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <!-- Opening Balance Row -->
                            <tr class="bg-purple-50/40 font-semibold text-slate-700">
                                <td class="py-2.5 px-4 font-mono text-slate-500"><?= $startDate ? esc($startDate) : 'Awal' ?></td>
                                <td class="py-2.5 px-4 font-mono text-purple-700">SALDO-AWAL</td>
                                <td class="py-2.5 px-4">-</td>
                                <td class="py-2.5 px-4 italic">Saldo Awal Periode (Opening Balance)</td>
                                <td class="py-2.5 px-4 text-right font-mono">-</td>
                                <td class="py-2.5 px-4 text-right font-mono">-</td>
                                <td class="py-2.5 px-5 text-right font-mono font-extrabold text-slate-900"><?= format_rupiah($lg['opening_balance']) ?></td>
                            </tr>

                            <?php foreach ($lg['mutations'] as $m): ?>
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-2.5 px-4 font-mono text-slate-600 whitespace-nowrap"><?= date('d/m/Y', strtotime($m['journal_date'])) ?></td>
                                    <td class="py-2.5 px-4 font-mono font-bold text-purple-700 whitespace-nowrap"><?= esc($m['journal_number']) ?></td>
                                    <td class="py-2.5 px-4 font-mono text-slate-600 whitespace-nowrap"><?= esc($m['reference_number'] ?: '-') ?></td>
                                    <td class="py-2.5 px-4 text-slate-800"><?= esc($m['line_description'] ?: $m['journal_description']) ?></td>
                                    <td class="py-2.5 px-4 text-right font-mono font-semibold text-indigo-700 whitespace-nowrap">
                                        <?= (float)$m['debit'] > 0 ? format_rupiah($m['debit']) : '-' ?>
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-semibold text-pink-700 whitespace-nowrap">
                                        <?= (float)$m['credit'] > 0 ? format_rupiah($m['credit']) : '-' ?>
                                    </td>
                                    <td class="py-2.5 px-5 text-right font-mono font-extrabold text-slate-900 whitespace-nowrap">
                                        <?= format_rupiah($m['running_balance']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-slate-100/80 border-t-2 border-slate-300 font-bold text-xs">
                            <tr>
                                <td colspan="4" class="py-3 px-4 text-right text-slate-700 uppercase text-[10px]">Total Mutasi & Saldo Akhir [<?= esc($acc['account_code']) ?>]:</td>
                                <td class="py-3 px-4 text-right font-mono text-indigo-800"><?= format_rupiah($lg['total_debit']) ?></td>
                                <td class="py-3 px-4 text-right font-mono text-pink-800"><?= format_rupiah($lg['total_credit']) ?></td>
                                <td class="py-3 px-5 text-right font-mono text-sm font-black text-purple-950"><?= format_rupiah($lg['ending_balance']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?= $this->endSection() ?>

