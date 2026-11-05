<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>
<?php
$typeLabels = [
    'GENERAL'    => ['label' => 'Jurnal Umum (Memorial)', 'cls' => 'bg-slate-100 text-slate-700 border-slate-200'],
    'SALES_PO'   => ['label' => 'Otomatis PO & Tagihan',  'cls' => 'bg-purple-50 text-purple-700 border-purple-200'],
    'RECEIPT_AR' => ['label' => 'Pelunasan Piutang (AR)', 'cls' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
    'PAYMENT_AP' => ['label' => 'Settlement Vendor (AP)', 'cls' => 'bg-blue-50 text-blue-700 border-blue-200'],
    'CASH_IN'    => ['label' => 'Bukti Kas/Bank Masuk',   'cls' => 'bg-teal-50 text-teal-700 border-teal-200'],
    'CASH_OUT'   => ['label' => 'Bukti Kas/Bank Keluar',  'cls' => 'bg-rose-50 text-rose-700 border-rose-200'],
    'ADJUSTMENT' => ['label' => 'Jurnal Penyesuaian',     'cls' => 'bg-amber-50 text-amber-700 border-amber-200'],
];

$grandDebit  = array_sum(array_column($journals, 'total_debit'));
$grandCredit = array_sum(array_column($journals, 'total_credit'));
?>

<div class="space-y-6">
    <?= $this->include('admin/accounting/_nav') ?>

    <!-- Header & Quick Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-extrabold uppercase tracking-wider">Double-Entry Bookkeeping</span>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Total Debit = Kredit Seimbang</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Jurnal Umum (General Journal)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pencatatan kronologis seluruh transaksi keuangan otomatis dari PO/Invoice maupun jurnal memorial/penyesuaian manual.</p>
        </div>
        <button type="button" onclick="openAddJournalModal()" class="btn-akuada-primary px-5 py-2.5 text-xs flex-shrink-0">
            <span class="material-symbols-outlined text-[18px]">post_add</span>
            <span>Catat Jurnal Umum Baru</span>
        </button>
    </div>

    <!-- Sub-Tabs per Jenis Jurnal (Jurnal Umum di Tab Terakhir) -->
    <?php
    $journalTabs = [
        ''           => 'Semua Transaksi',
        'SALES_PO'   => 'Otomatis PO & Tagihan',
        'RECEIPT_AR' => 'Pelunasan Piutang (AR)',
        'PAYMENT_AP' => 'Settlement Vendor (AP)',
        'CASH_IN'    => 'Kas/Bank Masuk',
        'CASH_OUT'   => 'Kas/Bank Keluar',
        'ADJUSTMENT' => 'Jurnal Penyesuaian',
        'GENERAL'    => 'Jurnal Umum (Memorial)',
    ];
    ?>
    <div class="bg-white rounded-2xl p-2 border border-slate-200/80 shadow-sm flex flex-wrap items-center gap-1.5 text-xs">
        <?php foreach ($journalTabs as $tKey => $tTitle):
            $isTabActive = ($typeFilter === $tKey);
            $tabUrl = base_url('admin/accounting/journals') . ($tKey ? '?type=' . urlencode($tKey) : '');
        ?>
            <a href="<?= $tabUrl ?>"
               class="px-3.5 py-2 rounded-xl font-bold transition <?= $isTabActive ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' ?>">
                <?= esc($tTitle) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Filter Bar & Summary -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col lg:flex-row lg:items-end justify-between gap-4 text-xs">
        <form action="<?= base_url('admin/accounting/journals') ?>" method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block font-bold text-slate-600 mb-1 text-[11px]">Dari Tanggal:</label>
                <input type="date" name="start_date" value="<?= esc($startDate) ?>" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1 text-[11px]">Sampai Tanggal:</label>
                <input type="date" name="end_date" value="<?= esc($endDate) ?>" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>
            <div>
                <label class="block font-bold text-slate-600 mb-1 text-[11px]">Jenis Jurnal:</label>
                <select name="type" class="px-3 py-2 rounded-xl border border-slate-300 bg-white font-semibold">
                    <option value="">Semua Jenis Jurnal</option>
                    <?php foreach ($typeLabels as $tk => $tm): ?>
                        <option value="<?= $tk ?>" <?= $typeFilter === $tk ? 'selected' : '' ?>><?= $tm['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition flex items-center gap-1">
                <span class="material-symbols-outlined text-[16px]">filter_alt</span> Filter
            </button>
            <?php if ($startDate || $endDate || $typeFilter): ?>
                <a href="<?= base_url('admin/accounting/journals') ?>" class="px-3 py-2 rounded-xl bg-slate-100 text-slate-600 font-bold hover:bg-slate-200">Reset</a>
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-4 bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200">
            <div>
                <span class="text-[10px] text-slate-500 block">Total Mutasi Debit:</span>
                <strong class="font-mono text-sm text-indigo-700"><?= format_rupiah($grandDebit) ?></strong>
            </div>
            <div class="h-6 w-px bg-slate-200"></div>
            <div>
                <span class="text-[10px] text-slate-500 block">Total Mutasi Kredit:</span>
                <strong class="font-mono text-sm text-pink-700"><?= format_rupiah($grandCredit) ?></strong>
            </div>
        </div>
    </div>

    <!-- Journal Vouchers List -->
    <div class="space-y-4">
        <?php foreach ($journals as $jrn):
            $tMeta = $typeLabels[$jrn['journal_type']] ?? $typeLabels['GENERAL'];
        ?>
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <!-- Voucher Header -->
                <div class="bg-slate-50/90 px-5 py-3 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2 text-xs">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="font-mono font-black text-purple-800 text-sm"><?= esc($jrn['journal_number']) ?></span>
                        <span class="px-2.5 py-0.5 rounded-full border text-[10px] font-bold <?= $tMeta['cls'] ?>"><?= $tMeta['label'] ?></span>
                        <span class="text-slate-500 font-mono flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">calendar_today</span>
                            <?= date('d M Y', strtotime($jrn['journal_date'])) ?>
                        </span>
                        <?php if (!empty($jrn['reference_number'])): ?>
                            <span class="px-2 py-0.5 rounded bg-white border border-slate-200 font-mono text-[11px] text-slate-700">
                                Ref: <strong><?= esc($jrn['reference_number']) ?></strong>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-[11px] text-slate-400">Oleh: <strong><?= esc($jrn['created_by']) ?></strong></span>
                        <?php if ($jrn['reference_type'] === 'manual' || $jrn['reference_type'] === 'cash_bank_manual'): ?>
                            <a href="<?= base_url('admin/accounting/journals/delete/' . $jrn['id']) ?>"
                               data-confirm="Hapus jurnal manual <?= esc($jrn['journal_number']) ?> ini?"
                               class="text-rose-600 hover:underline font-bold flex items-center gap-0.5 text-[11px]">
                                <span class="material-symbols-outlined text-[15px]">delete</span> Hapus
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="px-5 py-2.5 bg-purple-50/20 border-b border-slate-100 text-xs text-slate-700 font-medium">
                    <?= esc($jrn['description']) ?>
                </div>

                <!-- Voucher Lines -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="text-[10px] uppercase tracking-wider text-slate-400 border-b border-slate-100 bg-white">
                                <th class="py-2 px-5 w-32">Kode COA</th>
                                <th class="py-2 px-4">Nama Akun Perkiraan</th>
                                <th class="py-2 px-4">Uraian Baris Jurnal</th>
                                <th class="py-2 px-5 text-right w-44">Debit (IDR)</th>
                                <th class="py-2 px-5 text-right w-44">Kredit (IDR)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($jrn['items'] as $item):
                                $isCred = ((float)$item['credit'] > 0);
                            ?>
                                <tr class="hover:bg-slate-50/80">
                                    <td class="py-2.5 px-5 font-mono font-bold text-purple-800">
                                        <a href="<?= base_url('admin/accounting/ledger?coa_id=' . $item['coa_id']) ?>" class="hover:underline">
                                            <?= esc($item['account_code']) ?>
                                        </a>
                                    </td>
                                    <td class="py-2.5 px-4 font-bold text-slate-800 <?= $isCred ? 'pl-10 text-slate-700' : '' ?>">
                                        <?= esc($item['account_name']) ?>
                                    </td>
                                    <td class="py-2.5 px-4 text-slate-500">
                                        <?= esc($item['line_description']) ?>
                                    </td>
                                    <td class="py-2.5 px-5 text-right font-mono font-bold text-indigo-700">
                                        <?= (float)$item['debit'] > 0 ? format_rupiah($item['debit']) : '-' ?>
                                    </td>
                                    <td class="py-2.5 px-5 text-right font-mono font-bold text-pink-700">
                                        <?= (float)$item['credit'] > 0 ? format_rupiah($item['credit']) : '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-200 font-bold text-xs">
                            <tr>
                                <td colspan="3" class="py-2 px-5 text-right text-slate-600 uppercase text-[10px]">Total Voucher Jurnal:</td>
                                <td class="py-2 px-5 text-right font-mono text-indigo-900"><?= format_rupiah($jrn['total_debit']) ?></td>
                                <td class="py-2 px-5 text-right font-mono text-pink-900"><?= format_rupiah($jrn['total_credit']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal Catat Jurnal Umum Manual (Multi-Baris) -->
<div id="modalAddJournal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-2xl max-w-4xl w-full p-6 shadow-2xl border border-slate-200 text-xs space-y-4 my-8">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">post_add</span>
                Entri Jurnal Umum / Memorial Baru (Double-Entry)
            </h3>
            <button type="button" onclick="document.getElementById('modalAddJournal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/accounting/journals/store') ?>" method="POST" id="manualJournalForm" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Jurnal *</label>
                    <input type="date" name="journal_date" required value="<?= date('Y-m-d') ?>"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-mono font-bold">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jenis Jurnal *</label>
                    <select name="journal_type" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white font-bold">
                        <option value="GENERAL">Jurnal Umum (Memorial)</option>
                        <option value="ADJUSTMENT">Jurnal Penyesuaian (Adjustment)</option>
                        <option value="CASH_OUT">Pengeluaran Kas / Biaya</option>
                        <option value="CASH_IN">Penerimaan Kas / Modal</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Bukti / Referensi *</label>
                    <input type="text" name="reference_number" required value="MEMO/<?= date('Ym') ?>/<?= rand(100, 999) ?>"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Keterangan / Narasi Transaksi *</label>
                <input type="text" name="description" required placeholder="Contoh: Pencatatan beban sewa kantor / penyesuaian penyusutan aset tetap..."
                       class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-semibold">
            </div>

            <!-- Journal Lines Table -->
            <div class="border border-slate-200 rounded-xl overflow-visible">
                <table class="w-full text-left border-collapse text-xs" id="journalLinesTable">
                    <thead class="bg-slate-900 text-white text-[10px] uppercase">
                        <tr>
                            <th class="py-2.5 px-3 w-5/12">Akun COA (Ketik Pencarian / Pilih)</th>
                            <th class="py-2.5 px-3">Uraian Baris</th>
                            <th class="py-2.5 px-3 w-36 text-right">Debit (Rp)</th>
                            <th class="py-2.5 px-3 w-36 text-right">Kredit (Rp)</th>
                            <th class="py-2.5 px-2 w-10 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="journalLinesBody" class="divide-y divide-slate-100">
                        <?php for ($i = 0; $i < 2; $i++): ?>
                        <tr class="jrn-line-row">
                            <td class="p-2 relative coa-col">
                                <div class="relative coa-searchable-select">
                                    <input type="hidden" name="coa_id[]" class="coa-id-val" value="">
                                    <div class="relative flex items-center">
                                        <span class="material-symbols-outlined text-[16px] text-slate-400 absolute left-2.5 pointer-events-none">search</span>
                                        <input type="text"
                                               class="coa-search-input w-full pl-8 pr-7 py-2 rounded-lg border border-slate-300 bg-white font-semibold text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-200 focus:border-purple-600 transition"
                                               placeholder="Ketik kode / nama COA..."
                                               autocomplete="off"
                                               required>
                                        <button type="button" class="coa-dropdown-btn absolute right-1 text-slate-400 hover:text-slate-700 p-1 rounded transition" title="Buka Daftar Akun">
                                            <span class="material-symbols-outlined text-[18px]">arrow_drop_down</span>
                                        </button>
                                    </div>
                                    <!-- Floating Filtered List -->
                                    <div class="coa-options-menu hidden absolute left-0 top-full mt-1 w-full sm:min-w-[340px] bg-white rounded-xl shadow-2xl border border-slate-200 max-h-56 overflow-y-auto z-50 divide-y divide-slate-100 text-xs">
                                        <!-- Populated dynamically via JS -->
                                    </div>
                                </div>
                            </td>
                            <td class="p-2">
                                <input type="text" name="line_description[]" placeholder="Catatan baris..." class="w-full px-2.5 py-2 rounded-lg border border-slate-300">
                            </td>
                            <td class="p-2">
                                <input type="number" step="1" min="0" name="debit[]" value="0"
                                       onfocus="if(this.value=='0') this.select();"
                                       onblur="if(!this.value.trim()) this.value='0'; recalcJournalModal();"
                                       oninput="recalcJournalModal()"
                                       class="jrn-debit w-full px-2.5 py-2 rounded-lg border border-slate-300 text-right font-mono font-bold text-indigo-700">
                            </td>
                            <td class="p-2">
                                <input type="number" step="1" min="0" name="credit[]" value="0"
                                       onfocus="if(this.value=='0') this.select();"
                                       onblur="if(!this.value.trim()) this.value='0'; recalcJournalModal();"
                                       oninput="recalcJournalModal()"
                                       class="jrn-credit w-full px-2.5 py-2 rounded-lg border border-slate-300 text-right font-mono font-bold text-pink-700">
                            </td>
                            <td class="p-2 text-center">
                                <button type="button" onclick="removeJournalRow(this)" class="text-slate-400 hover:text-rose-600"><span class="material-symbols-outlined text-[18px]">close</span></button>
                            </td>
                        </tr>
                        <?php endfor; ?>
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200 font-bold">
                        <tr>
                            <td colspan="2" class="p-3">
                                <button type="button" onclick="addJournalRow()" class="px-3 py-1.5 rounded-lg bg-purple-100 text-purple-800 hover:bg-purple-200 font-bold flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">add</span> Tambah Baris Akun
                                </button>
                            </td>
                            <td class="p-3 text-right font-mono text-indigo-900" id="modalTotalDebit">Rp 0</td>
                            <td class="p-3 text-right font-mono text-pink-900" id="modalTotalCredit">Rp 0</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex items-center justify-between pt-2">
                <div id="balanceStatusBadge" class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs">
                    Nominal: Rp 0 (Belum Ada Nilai Transaksi)
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('modalAddJournal').classList.add('hidden')" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold">Batal</button>
                    <button type="submit" id="btnSubmitJournal" disabled class="btn-akuada-primary px-6 py-2.5 text-xs opacity-50 cursor-not-allowed">Posting Jurnal ke Buku Besar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
const coaMasterList = <?= json_encode(array_values(array_map(function($c) {
    return [
        'id'   => (int)$c['id'],
        'code' => (string)$c['account_code'],
        'name' => (string)$c['account_name'],
        'type' => (string)($c['account_type'] ?? 'COA'),
    ];
}, $coaList))) ?>;

function formatIdr(n) {
    return 'Rp ' + Math.round(n).toLocaleString('id-ID');
}

function openAddJournalModal() {
    document.getElementById('modalAddJournal').classList.remove('hidden');
    recalcJournalModal();
}

function initCoaSearchable(wrapper) {
    if (!wrapper || wrapper.dataset.coaInit === 'true') return;
    wrapper.dataset.coaInit = 'true';

    const input = wrapper.querySelector('.coa-search-input');
    const hidden = wrapper.querySelector('.coa-id-val');
    const menu = wrapper.querySelector('.coa-options-menu');
    const btn = wrapper.querySelector('.coa-dropdown-btn');

    function renderOptions(query = '') {
        const q = query.toLowerCase().trim();
        const filtered = q
            ? coaMasterList.filter(c => c.code.toLowerCase().includes(q) || c.name.toLowerCase().includes(q) || c.type.toLowerCase().includes(q))
            : coaMasterList;

        if (filtered.length === 0) {
            menu.innerHTML = `<div class="p-3 text-center text-slate-400 italic">Tidak ditemukan akun COA untuk "${query}"</div>`;
        } else {
            menu.innerHTML = filtered.map(c => `
                <div class="coa-opt-item px-3 py-2 hover:bg-purple-50 cursor-pointer flex items-center justify-between gap-2 transition"
                     data-id="${c.id}" data-label="[${c.code}] ${c.name}">
                    <div class="flex items-center gap-1.5 overflow-hidden">
                        <span class="font-mono font-bold text-purple-700 flex-shrink-0">[${c.code}]</span>
                        <span class="font-semibold text-slate-800 truncate">${c.name}</span>
                    </div>
                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 uppercase flex-shrink-0">${c.type}</span>
                </div>
            `).join('');

            menu.querySelectorAll('.coa-opt-item').forEach(item => {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    selectCoa(item.dataset.id, item.dataset.label);
                });
            });
        }
    }

    function selectCoa(id, label) {
        hidden.value = id;
        input.value = label;
        input.classList.remove('border-rose-500', 'bg-rose-50');
        menu.classList.add('hidden');
    }

    function showMenu() {
        document.querySelectorAll('.coa-options-menu').forEach(m => {
            if (m !== menu) m.classList.add('hidden');
        });
        renderOptions(input.value);
        menu.classList.remove('hidden');
    }

    input.addEventListener('focus', function() {
        showMenu();
        this.select();
    });

    input.addEventListener('input', function() {
        showMenu();
        if (!this.value.trim()) {
            hidden.value = '';
        }
    });

    if (btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (menu.classList.contains('hidden')) {
                showMenu();
                input.focus();
            } else {
                menu.classList.add('hidden');
            }
        });
    }

    input.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            menu.classList.add('hidden');
        } else if (e.key === 'Enter') {
            const first = menu.querySelector('.coa-opt-item');
            if (first && !menu.classList.contains('hidden')) {
                e.preventDefault();
                selectCoa(first.dataset.id, first.dataset.label);
            }
        }
    });
}

// Global click outside to close menus
document.addEventListener('click', function(e) {
    if (!e.target.closest('.coa-searchable-select')) {
        document.querySelectorAll('.coa-options-menu').forEach(m => m.classList.add('hidden'));
    }
});

function addJournalRow() {
    const tbody = document.getElementById('journalLinesBody');
    const firstRow = tbody.querySelector('.jrn-line-row');
    const clone = firstRow.cloneNode(true);

    const wrapper = clone.querySelector('.coa-searchable-select');
    if (wrapper) {
        delete wrapper.dataset.coaInit;
    }

    const hidden = clone.querySelector('.coa-id-val');
    const searchInp = clone.querySelector('.coa-search-input');
    const descInp = clone.querySelector('input[name="line_description[]"]');
    const debitInp = clone.querySelector('.jrn-debit');
    const creditInp = clone.querySelector('.jrn-credit');
    const menu = clone.querySelector('.coa-options-menu');

    if (hidden) hidden.value = '';
    if (searchInp) {
        searchInp.value = '';
        searchInp.classList.remove('border-rose-500', 'bg-rose-50');
    }
    if (descInp) descInp.value = '';
    if (debitInp) debitInp.value = '0';
    if (creditInp) creditInp.value = '0';
    if (menu) {
        menu.classList.add('hidden');
        menu.innerHTML = '';
    }

    tbody.appendChild(clone);
    initCoaSearchable(wrapper);
    recalcJournalModal();
}

function removeJournalRow(btn) {
    const tbody = document.getElementById('journalLinesBody');
    if (tbody.querySelectorAll('.jrn-line-row').length > 2) {
        btn.closest('tr').remove();
        recalcJournalModal();
    }
}

function recalcJournalModal() {
    let d = 0, c = 0;
    document.querySelectorAll('.jrn-debit').forEach(el => d += parseFloat(el.value || 0));
    document.querySelectorAll('.jrn-credit').forEach(el => c += parseFloat(el.value || 0));
    
    const debitEl = document.getElementById('modalTotalDebit');
    const creditEl = document.getElementById('modalTotalCredit');
    if (debitEl) debitEl.textContent = formatIdr(d);
    if (creditEl) creditEl.textContent = formatIdr(c);

    const badge = document.getElementById('balanceStatusBadge');
    const btn = document.getElementById('btnSubmitJournal');
    if (!badge || !btn) return;

    if (d === 0 && c === 0) {
        badge.className = 'px-3 py-1.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs';
        badge.textContent = 'Nominal: Rp 0 (Belum Ada Nilai Transaksi)';
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    } else if (d > 0 && Math.abs(d - c) < 0.01) {
        badge.className = 'px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs';
        badge.textContent = '✓ SEIMBANG (BALANCE): ' + formatIdr(d);
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        const diff = Math.abs(d - c);
        badge.className = 'px-3 py-1.5 rounded-xl bg-rose-100 text-rose-800 font-bold text-xs';
        badge.textContent = '⚠ SELISIH (TIDAK SEIMBANG): ' + formatIdr(diff);
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

// Form validation before submit
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.coa-searchable-select').forEach(initCoaSearchable);
    recalcJournalModal();

    const form = document.getElementById('manualJournalForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            let hasEmptyCoa = false;
            document.querySelectorAll('.coa-id-val').forEach(inp => {
                if (!inp.value || inp.value === '0') {
                    hasEmptyCoa = true;
                    const searchInput = inp.closest('.coa-searchable-select').querySelector('.coa-search-input');
                    searchInput.classList.add('border-rose-500', 'bg-rose-50');
                }
            });

            if (hasEmptyCoa) {
                e.preventDefault();
                if (window.AkuAdaAlert) {
                    AkuAdaAlert.error('Akun COA Belum Lengkap', 'Mohon pilih akun COA yang valid untuk setiap baris jurnal.');
                } else {
                    alert('Mohon pilih akun COA yang valid untuk setiap baris jurnal.');
                }
                return false;
            }

            let d = 0, c = 0;
            document.querySelectorAll('.jrn-debit').forEach(el => d += parseFloat(el.value || 0));
            document.querySelectorAll('.jrn-credit').forEach(el => c += parseFloat(el.value || 0));
            if (d <= 0 || Math.abs(d - c) > 0.01) {
                e.preventDefault();
                if (window.AkuAdaAlert) {
                    AkuAdaAlert.error('Jurnal Tidak Seimbang', 'Total debit dan total kredit harus lebih dari Rp 0 dan memiliki jumlah yang seimbang.');
                } else {
                    alert('Total debit dan kredit harus seimbang.');
                }
                return false;
            }
        });
    }
});
</script>

<?= $this->endSection() ?>

