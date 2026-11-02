<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>
<?php
$totalCashOpening = array_sum(array_column($bankAccounts, 'effective_opening'));
$totalCashIn      = array_sum(array_column($bankAccounts, 'mutation_debit'));
$totalCashOut     = array_sum(array_column($bankAccounts, 'mutation_credit'));
$totalCashEnding  = array_sum(array_column($bankAccounts, 'ending_balance'));
?>

<div class="space-y-6">
    <?= $this->include('admin/accounting/_nav') ?>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-extrabold uppercase tracking-wider">Treasury & Cash Management</span>
                <span class="text-xs text-slate-400 font-mono">Rekening Escrow & Kas Operasional</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Buku Kas & Bank (Cash & Bank Book)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Monitoring likuiditas rekening bank escrow, penerimaan pembayaran buyer (Inflow), pencairan vendor (Outflow), dan kas operasional.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" onclick="openCashModal('CASH_IN')" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">south_west</span>
                <span>Catat Kas/Bank Masuk</span>
            </button>
            <button type="button" onclick="openCashModal('CASH_OUT')" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">north_east</span>
                <span>Catat Kas/Bank Keluar</span>
            </button>
        </div>
    </div>

    <!-- Liquidity Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase">Saldo Awal Kas & Bank</span>
            <div class="text-lg font-black font-mono text-slate-800 mt-1"><?= format_rupiah($totalCashOpening) ?></div>
            <span class="text-[10px] text-slate-500">Akumulasi <?= count($bankAccounts) ?> Rekening</span>
        </div>
        <div class="bg-emerald-50/70 p-5 rounded-2xl border border-emerald-200 shadow-sm">
            <span class="text-[11px] font-bold text-emerald-800 uppercase">Total Kas/Bank Masuk (Debit)</span>
            <div class="text-lg font-black font-mono text-emerald-700 mt-1">+ <?= format_rupiah($totalCashIn) ?></div>
            <span class="text-[10px] text-emerald-700">Pelunasan Buyer & Penerimaan</span>
        </div>
        <div class="bg-rose-50/70 p-5 rounded-2xl border border-rose-200 shadow-sm">
            <span class="text-[11px] font-bold text-rose-800 uppercase">Total Kas/Bank Keluar (Kredit)</span>
            <div class="text-lg font-black font-mono text-rose-700 mt-1">- <?= format_rupiah($totalCashOut) ?></div>
            <span class="text-[10px] text-rose-700">Settlement Vendor & Biaya OPEX</span>
        </div>
        <div class="bg-gradient-to-br from-purple-900 to-indigo-950 p-5 rounded-2xl text-white shadow-md">
            <span class="text-[11px] font-bold text-purple-200 uppercase">Total Posisi Saldo Akhir</span>
            <div class="text-xl font-black font-mono text-emerald-300 mt-1"><?= format_rupiah($totalCashEnding) ?></div>
            <span class="text-[10px] text-purple-200">Likuiditas Aktif Siap Pakai</span>
        </div>
    </div>

    <!-- Individual Bank Account Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <?php foreach ($bankAccounts as $ba): ?>
            <a href="<?= base_url('admin/accounting/cash-bank?bank_coa_id=' . $ba['id']) ?>"
               class="p-4 rounded-2xl border transition block <?= (int)$selectedBankId === (int)$ba['id'] ? 'bg-purple-50 border-purple-500 ring-2 ring-purple-200' : 'bg-white border-slate-200 hover:border-purple-300' ?>">
                <div class="flex items-center justify-between mb-2">
                    <span class="px-2 py-0.5 rounded bg-slate-900 text-white font-mono text-[10px] font-bold"><?= esc($ba['account_code']) ?></span>
                    <span class="material-symbols-outlined text-blue-600 text-[20px]">account_balance</span>
                </div>
                <h3 class="font-bold text-slate-900 text-xs line-clamp-1"><?= esc($ba['account_name']) ?></h3>
                <p class="font-mono text-[11px] text-slate-500 mt-0.5"><?= esc($ba['bank_account_number'] ?: 'Kas Internal') ?></p>
                <div class="mt-3 pt-2 border-t border-slate-100 flex items-baseline justify-between">
                    <span class="text-[10px] text-slate-400">Saldo:</span>
                    <strong class="font-mono text-sm font-black text-purple-900"><?= format_rupiah($ba['ending_balance']) ?></strong>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Filter & Bank Selector Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm print:hidden">
        <form action="<?= base_url('admin/accounting/cash-bank') ?>" method="GET" class="flex flex-wrap items-end gap-3 text-xs">
            <div class="flex-1 min-w-[260px]">
                <label class="block font-bold text-slate-700 mb-1">Pilih Rekening Kas / Bank:</label>
                <select name="bank_coa_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white font-bold text-slate-900">
                    <?php foreach ($bankAccounts as $ba): ?>
                        <option value="<?= $ba['id'] ?>" <?= (int)$selectedBankId === (int)$ba['id'] ? 'selected' : '' ?>>
                            [<?= esc($ba['account_code']) ?>] <?= esc($ba['account_name']) ?> — Saldo: <?= format_rupiah($ba['ending_balance']) ?>
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
            <button type="submit" class="btn-akuada-primary px-5 py-2.5 text-xs">Tampilkan Buku Bank</button>
        </form>
    </div>

    <!-- Mutation Tables per Bank Account -->
    <div class="space-y-6">
        <?php foreach ($bankLedgers as $bl):
            $bAcc = $bl['account'];
        ?>
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="bg-slate-900 text-white px-6 py-3.5 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="px-2.5 py-0.5 rounded bg-blue-500/30 border border-blue-400/40 font-mono font-bold text-xs text-blue-200"><?= esc($bAcc['account_code']) ?></span>
                        <h3 class="font-bold text-sm"><?= esc($bAcc['account_name']) ?></h3>
                        <?php if (!empty($bAcc['bank_account_number'])): ?>
                            <span class="text-xs font-mono text-slate-300">(No. Rek: <?= esc($bAcc['bank_account_number']) ?>)</span>
                        <?php endif; ?>
                    </div>
                    <div class="font-mono text-sm font-black text-emerald-300">
                        Saldo Akhir: <?= format_rupiah($bl['ending_balance']) ?>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200 text-[10px] uppercase font-bold text-slate-500">
                                <th class="py-2.5 px-4 w-28">Tanggal</th>
                                <th class="py-2.5 px-4 w-40">No. Bukti / Jurnal</th>
                                <th class="py-2.5 px-4 w-36">Referensi Bank</th>
                                <th class="py-2.5 px-4">Keterangan Mutasi Kas/Bank</th>
                                <th class="py-2.5 px-4 text-right w-40">Masuk / Debit (+)</th>
                                <th class="py-2.5 px-4 text-right w-40">Keluar / Kredit (-)</th>
                                <th class="py-2.5 px-5 text-right w-44">Saldo Bank</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr class="bg-blue-50/30 font-semibold">
                                <td class="py-2.5 px-4 font-mono text-slate-500">Awal</td>
                                <td class="py-2.5 px-4 font-mono text-blue-700">SALDO-AWAL</td>
                                <td class="py-2.5 px-4">-</td>
                                <td class="py-2.5 px-4 italic">Saldo Awal Rekening</td>
                                <td class="py-2.5 px-4 text-right">-</td>
                                <td class="py-2.5 px-4 text-right">-</td>
                                <td class="py-2.5 px-5 text-right font-mono font-bold"><?= format_rupiah($bl['opening_balance']) ?></td>
                            </tr>
                            <?php foreach ($bl['mutations'] as $m): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="py-2.5 px-4 font-mono text-slate-600"><?= date('d/m/Y', strtotime($m['journal_date'])) ?></td>
                                    <td class="py-2.5 px-4 font-mono font-bold text-purple-700"><?= esc($m['journal_number']) ?></td>
                                    <td class="py-2.5 px-4 font-mono text-slate-600"><?= esc($m['reference_number'] ?: '-') ?></td>
                                    <td class="py-2.5 px-4 text-slate-800"><?= esc($m['line_description'] ?: $m['journal_description']) ?></td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-emerald-700">
                                        <?= (float)$m['debit'] > 0 ? '+ ' . format_rupiah($m['debit']) : '-' ?>
                                    </td>
                                    <td class="py-2.5 px-4 text-right font-mono font-bold text-rose-700">
                                        <?= (float)$m['credit'] > 0 ? '- ' . format_rupiah($m['credit']) : '-' ?>
                                    </td>
                                    <td class="py-2.5 px-5 text-right font-mono font-extrabold text-slate-900">
                                        <?= format_rupiah($m['running_balance']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Catat Transaksi Kas & Bank Masuk/Keluar -->
<div id="modalCashTx" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 text-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 id="cashModalTitle" class="text-sm font-extrabold text-slate-900">Catat Transaksi Kas & Bank</h3>
            <button type="button" onclick="document.getElementById('modalCashTx').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/accounting/cash-bank/store') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="tx_type" id="cash_tx_type" value="CASH_IN">

            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih Rekening Kas / Bank AkuAda *</label>
                <select name="bank_coa_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white font-bold">
                    <?php foreach ($bankAccounts as $ba): ?>
                        <option value="<?= $ba['id'] ?>">[<?= esc($ba['account_code']) ?>] <?= esc($ba['account_name']) ?> (Saldo: <?= format_rupiah($ba['ending_balance']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1" id="contraAccountLabel">Akun Lawan (Sumber Penerimaan / Beban Tujuan) *</label>
                <select name="contra_coa_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white font-semibold">
                    <?php foreach ($otherCoa as $oc): ?>
                        <option value="<?= $oc['id'] ?>">[<?= esc($oc['account_code']) ?>] <?= esc($oc['account_name']) ?> (<?= esc($oc['account_category']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Transaksi *</label>
                    <input type="date" name="tx_date" required value="<?= date('Y-m-d') ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nominal (Rp) *</label>
                    <input type="number" step="1" min="1" name="amount" required placeholder="5000000" class="w-full px-3.5 py-2.5 rounded-xl border border-purple-300 font-mono font-black text-purple-900">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">No. Referensi Rekening Koran / Cek / Voucher</label>
                <input type="text" name="reference_number" value="BNK/<?= date('Ymd') ?>/<?= rand(100, 999) ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Keterangan Transaksi *</label>
                <input type="text" name="description" required placeholder="Contoh: Pembayaran biaya langganan server / Penerimaan bunga jasa giro bank..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalCashTx').classList.add('hidden')" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold">Batal</button>
                <button type="submit" class="btn-akuada-primary px-6 py-2.5 text-xs">Simpan ke Buku Bank & GL</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCashModal(type) {
    document.getElementById('cash_tx_type').value = type;
    document.getElementById('cashModalTitle').textContent = (type === 'CASH_IN')
        ? 'Catat Bukti Kas / Bank Masuk (Inflow - Debit Bank)'
        : 'Catat Bukti Kas / Bank Keluar (Outflow - Kredit Bank)';
    document.getElementById('contraAccountLabel').textContent = (type === 'CASH_IN')
        ? 'Akun Lawan Sumber Penerimaan (Kredit) *'
        : 'Akun Lawan Beban / Pembayaran (Debit) *';
    document.getElementById('modalCashTx').classList.remove('hidden');
}
</script>

<?= $this->endSection() ?>

