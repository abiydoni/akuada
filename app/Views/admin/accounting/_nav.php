<?php
$activeTab = $activeTab ?? 'coa';
$navItems = [
    ['id' => 'coa',              'url' => 'admin/accounting/coa',              'icon' => 'account_tree',      'label' => '1. Master COA (###-###)'],
    ['id' => 'journals',         'url' => 'admin/accounting/journals',         'icon' => 'menu_book',         'label' => '2. Jurnal Umum'],
    ['id' => 'ledger',           'url' => 'admin/accounting/ledger',           'icon' => 'library_books',     'label' => '3. Buku Besar (GL)'],
    ['id' => 'cash-bank',        'url' => 'admin/accounting/cash-bank',        'icon' => 'account_balance',   'label' => '4. Buku Kas & Bank'],
    ['id' => 'trial-balance',    'url' => 'admin/accounting/trial-balance',    'icon' => 'balance',           'label' => '5. Neraca Saldo'],
    ['id' => 'income-statement', 'url' => 'admin/accounting/income-statement', 'icon' => 'monitoring',        'label' => '6. Laporan R/L'],
    ['id' => 'balance-sheet',    'url' => 'admin/accounting/balance-sheet',    'icon' => 'analytics',         'label' => '7. Laporan Neraca'],
    ['id' => 'cash-flow',        'url' => 'admin/accounting/cash-flow',        'icon' => 'currency_exchange', 'label' => '8. Arus Kas'],
];
?>
<!-- General Ledger & Financial Reporting Top Tab Bar -->
<div class="bg-white rounded-2xl p-2 border border-slate-200/80 shadow-sm flex flex-wrap items-center justify-between gap-2 print:hidden">
    <div class="flex flex-wrap items-center gap-1">
        <?php foreach ($navItems as $item): 
            $isActive = ($activeTab === $item['id']);
        ?>
            <a href="<?= base_url($item['url']) ?>" 
               class="px-3 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= $isActive ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' ?>">
                <span class="material-symbols-outlined text-[16px]"><?= $item['icon'] ?></span>
                <span><?= $item['label'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    <a href="<?= base_url('admin/accounting/journals/sync') ?>" 
       class="px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold transition flex items-center gap-1.5"
       title="Sinkronkan seluruh transaksi PO, Pelunasan Piutang Buyer & Settlement Vendor ke Jurnal Umum">
        <span class="material-symbols-outlined text-[16px]">sync</span>
        <span>Sync Otomatis PO ke GL</span>
    </a>
</div>

