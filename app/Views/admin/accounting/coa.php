<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>
<?php
$catBadges = [
    'ASSET'         => ['label' => '1xx • ASET (AKTIVA)',        'cls' => 'bg-blue-50 text-blue-700 border-blue-200'],
    'LIABILITY'     => ['label' => '2xx • KEWAJIBAN (HUTANG)',   'cls' => 'bg-amber-50 text-amber-700 border-amber-200'],
    'EQUITY'        => ['label' => '3xx • EKUITAS (MODAL)',      'cls' => 'bg-purple-50 text-purple-700 border-purple-200'],
    'REVENUE'       => ['label' => '4xx • PENDAPATAN USAHA',     'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
    'COGS'          => ['label' => '5xx • HARGA POKOK (HPP)',    'cls' => 'bg-rose-50 text-rose-700 border-rose-200'],
    'EXPENSE'       => ['label' => '6xx • BEBAN OPERASIONAL',    'cls' => 'bg-pink-50 text-pink-700 border-pink-200'],
    'OTHER_INCOME'  => ['label' => '7xx • PENDAPATAN LAIN',      'cls' => 'bg-teal-50 text-teal-700 border-teal-200'],
    'OTHER_EXPENSE' => ['label' => '8xx • BEBAN LAIN-LAIN',      'cls' => 'bg-slate-100 text-slate-700 border-slate-300'],
];
?>

<div class="space-y-6">
    <?= $this->include('admin/accounting/_nav') ?>

    <!-- Header & Quick Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-extrabold uppercase tracking-wider">Standar Akuntansi Korporat</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Master Chart of Accounts (Daftar Akun Perkiraan COA)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola kode akun perkiraan, saldo awal, posisi laporan (Neraca / Laba Rugi), dan mutasi berjalan.</p>
        </div>
        <button type="button" onclick="document.getElementById('modalAddCoa').classList.remove('hidden')" class="btn-akuada-primary px-5 py-2.5 text-xs flex-shrink-0">
            <span class="material-symbols-outlined text-[18px]">add_circle</span>
            <span>Tambah Kode COA</span>
        </button>
    </div>

    <!-- Filter Category Pills -->
    <div class="flex flex-wrap items-center gap-2 text-xs">
        <a href="<?= base_url('admin/accounting/coa') ?>" class="px-3.5 py-1.5 rounded-xl font-bold border transition <?= empty($categoryFilter) ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
            Semua Akun (<?= count($accounts) ?>)
        </a>
        <?php foreach ($catBadges as $catKey => $meta): ?>
            <a href="<?= base_url('admin/accounting/coa?category=' . $catKey) ?>" class="px-3 py-1.5 rounded-xl font-bold border transition <?= ($categoryFilter === $catKey) ? 'bg-purple-600 text-white border-purple-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>">
                <?= $meta['label'] ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Master COA Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-900 text-white uppercase text-[10px] tracking-wider font-bold">
                        <th class="py-3.5 px-4">Kode COA</th>
                        <th class="py-3.5 px-4">Nama Akun Perkiraan</th>
                        <th class="py-3.5 px-3">Kategori & Kelompok</th>
                        <th class="py-3.5 px-3 text-center">Saldo Normal</th>
                        <th class="py-3.5 px-3 text-center">Pos Laporan</th>
                        <th class="py-3.5 px-4 text-right">Saldo Awal</th>
                        <th class="py-3.5 px-4 text-right">Mutasi Debit</th>
                        <th class="py-3.5 px-4 text-right">Mutasi Kredit</th>
                        <th class="py-3.5 px-4 text-right">Saldo Akhir</th>
                        <th class="py-3.5 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($accounts as $acc):
                        $badge = $catBadges[$acc['account_category']] ?? $catBadges['ASSET'];
                    ?>
                        <tr class="hover:bg-purple-50/30 transition">
                            <td class="py-3 px-4 font-mono font-black text-purple-800 text-sm whitespace-nowrap">
                                <a href="<?= base_url('admin/accounting/ledger?coa_id=' . $acc['id']) ?>" class="hover:underline flex items-center gap-1" title="Lihat Buku Besar Akun Ini">
                                    <span><?= esc($acc['account_code']) ?></span>
                                    <?php if ($acc['is_cash_bank']): ?>
                                        <span class="material-symbols-outlined text-[14px] text-blue-600" title="Akun Kas & Bank">account_balance</span>
                                    <?php endif; ?>
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900"><?= esc($acc['account_name']) ?></div>
                                <?php if (!empty($acc['bank_account_number'])): ?>
                                    <div class="text-[10px] font-mono text-blue-600 font-semibold">No. Rek: <?= esc($acc['bank_account_number']) ?></div>
                                <?php elseif (!empty($acc['description'])): ?>
                                    <div class="text-[10px] text-slate-400 line-clamp-1"><?= esc($acc['description']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-3">
                                <span class="inline-block px-2 py-0.5 rounded border text-[10px] font-bold <?= $badge['cls'] ?>">
                                    <?= $badge['label'] ?>
                                </span>
                                <div class="text-[10px] text-slate-500 mt-0.5 font-medium"><?= esc($acc['account_group']) ?></div>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded font-mono text-[10px] font-bold <?= $acc['normal_balance'] === 'DEBIT' ? 'bg-indigo-50 text-indigo-700' : 'bg-pink-50 text-pink-700' ?>">
                                    <?= esc($acc['normal_balance']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $acc['report_position'] === 'NERACA' ? 'bg-slate-100 text-slate-800' : 'bg-amber-50 text-amber-800' ?>">
                                    <?= $acc['report_position'] === 'NERACA' ? 'NERACA' : 'LABA RUGI' ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-slate-600 whitespace-nowrap">
                                <?= format_rupiah($acc['opening_balance']) ?>
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-indigo-700 whitespace-nowrap">
                                <?= $acc['mutation_debit'] > 0 ? format_rupiah($acc['mutation_debit']) : '-' ?>
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-pink-700 whitespace-nowrap">
                                <?= $acc['mutation_credit'] > 0 ? format_rupiah($acc['mutation_credit']) : '-' ?>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-extrabold text-slate-900 whitespace-nowrap">
                                <?= format_rupiah($acc['ending_balance']) ?>
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <div class="inline-flex items-center gap-1">
                                    <a href="<?= base_url('admin/accounting/ledger?coa_id=' . $acc['id']) ?>" class="p-1.5 rounded-lg bg-slate-100 hover:bg-purple-100 text-slate-700 hover:text-purple-700 transition" title="Buka Buku Besar">
                                        <span class="material-symbols-outlined text-[16px]">menu_book</span>
                                    </a>
                                    <button type="button" onclick='openEditCoaModal(<?= json_encode($acc, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="p-1.5 rounded-lg bg-slate-100 hover:bg-blue-100 text-slate-700 hover:text-blue-700 transition" title="Edit Akun COA">
                                        <span class="material-symbols-outlined text-[16px]">edit</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah COA Baru -->
<div id="modalAddCoa" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 text-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">add_circle</span>
                Tambah Akun Master COA Baru
            </h3>
            <button type="button" onclick="document.getElementById('modalAddCoa').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/accounting/coa/store') ?>" method="POST" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Akun COA *</label>
                    <input type="text" name="account_code" required pattern="\d{3}-\d{3}" placeholder="Contoh: 616-001"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-purple-300 font-mono font-bold text-purple-900 uppercase">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Nama Akun Perkiraan *</label>
                    <input type="text" name="account_name" required placeholder="Contoh: Beban Perjalanan Dinas & Audit"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-900">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori Utama *</label>
                    <select name="account_category" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white font-semibold">
                        <option value="ASSET">1xx - ASET (AKTIVA)</option>
                        <option value="LIABILITY">2xx - KEWAJIBAN (HUTANG)</option>
                        <option value="EQUITY">3xx - EKUITAS (MODAL)</option>
                        <option value="REVENUE">4xx - PENDAPATAN USAHA</option>
                        <option value="COGS">5xx - HARGA POKOK (HPP)</option>
                        <option value="EXPENSE">6xx - BEBAN OPERASIONAL</option>
                        <option value="OTHER_INCOME">7xx - PENDAPATAN LAIN</option>
                        <option value="OTHER_EXPENSE">8xx - BEBAN LAIN-LAIN</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Sub-Kelompok *</label>
                    <input type="text" name="account_group" required value="Beban Operasional & Umum"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Saldo Normal *</label>
                    <select name="normal_balance" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white font-bold">
                        <option value="DEBIT">DEBIT</option>
                        <option value="CREDIT">CREDIT</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Saldo Awal (Opening Balance Rp)</label>
                    <input type="number" step="1" name="opening_balance" value="0"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono font-bold">
                </div>
                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_cash_bank" value="1" class="rounded text-purple-600">
                        <span class="font-bold text-slate-700">Tandai sebagai Rekening Kas / Bank (Buku Bank)</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Keterangan / Deskripsi Akun</label>
                <input type="text" name="description" placeholder="Penjelasan fungsi akun dalam pencatatan pembukuan..."
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalAddCoa').classList.add('hidden')" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold">Batal</button>
                <button type="submit" class="btn-akuada-primary px-6 py-2.5 text-xs">Simpan Akun COA</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit COA -->
<div id="modalEditCoa" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl border border-slate-200 text-xs space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">edit</span>
                Edit Akun Master COA
            </h3>
            <button type="button" onclick="document.getElementById('modalEditCoa').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/accounting/coa/update') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="id" id="edit_coa_id">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode Akun COA *</label>
                    <input type="text" name="account_code" id="edit_account_code" required pattern="\d{3}-\d{3}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-purple-300 font-mono font-bold text-purple-900 uppercase">
                </div>
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Nama Akun Perkiraan *</label>
                    <input type="text" name="account_name" id="edit_account_name" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-bold text-slate-900">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori Utama *</label>
                    <select name="account_category" id="edit_account_category" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white font-semibold">
                        <option value="ASSET">1xx - ASET (AKTIVA)</option>
                        <option value="LIABILITY">2xx - KEWAJIBAN (HUTANG)</option>
                        <option value="EQUITY">3xx - EKUITAS (MODAL)</option>
                        <option value="REVENUE">4xx - PENDAPATAN USAHA</option>
                        <option value="COGS">5xx - HARGA POKOK (HPP)</option>
                        <option value="EXPENSE">6xx - BEBAN OPERASIONAL</option>
                        <option value="OTHER_INCOME">7xx - PENDAPATAN LAIN</option>
                        <option value="OTHER_EXPENSE">8xx - BEBAN LAIN-LAIN</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Sub-Kelompok *</label>
                    <input type="text" name="account_group" id="edit_account_group" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Saldo Normal *</label>
                    <select name="normal_balance" id="edit_normal_balance" class="w-full px-3 py-2.5 rounded-xl border border-slate-300 bg-white font-bold">
                        <option value="DEBIT">DEBIT</option>
                        <option value="CREDIT">CREDIT</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Saldo Awal (Opening Balance Rp)</label>
                    <input type="number" step="1" name="opening_balance" id="edit_opening_balance"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono font-bold">
                </div>
                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_cash_bank" id="edit_is_cash_bank" value="1" class="rounded text-purple-600">
                        <span class="font-bold text-slate-700">Akun Kas / Bank (Buku Bank)</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Keterangan / Deskripsi</label>
                <input type="text" name="description" id="edit_description"
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalEditCoa').classList.add('hidden')" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold">Batal</button>
                <button type="submit" class="btn-akuada-primary px-6 py-2.5 text-xs">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditCoaModal(acc) {
    document.getElementById('edit_coa_id').value = acc.id;
    document.getElementById('edit_account_code').value = acc.account_code;
    document.getElementById('edit_account_name').value = acc.account_name;
    document.getElementById('edit_account_category').value = acc.account_category;
    document.getElementById('edit_account_group').value = acc.account_group;
    document.getElementById('edit_normal_balance').value = acc.normal_balance;
    document.getElementById('edit_opening_balance').value = acc.opening_balance;
    document.getElementById('edit_is_cash_bank').checked = parseInt(acc.is_cash_bank) === 1;
    document.getElementById('edit_description').value = acc.description || '';
    document.getElementById('modalEditCoa').classList.remove('hidden');
}
</script>

<?= $this->endSection() ?>

