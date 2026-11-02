<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<?php
$appName = get_system_setting('app_name', 'AkuAda');
?>
<div class="min-h-screen flex flex-col bg-slate-100">

    <!-- Admin Top Navbar -->
    <header class="bg-slate-900 text-white border-b border-purple-900/60 sticky top-0 z-30 shadow-md print:hidden">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-purple-600 via-indigo-600 to-pink-500 flex items-center justify-center text-white shadow-md">
                        <span class="material-symbols-outlined text-xl">admin_panel_settings</span>
                    </div>
                    <div>
                        <span class="text-lg font-extrabold text-white"><?= esc($appName) ?> <span class="text-purple-400 font-semibold text-xs tracking-wider uppercase px-2 py-0.5 rounded bg-purple-900/80 border border-purple-700/60 ml-1">Admin Platform</span></span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <a href="<?= base_url('/') ?>" class="text-xs text-slate-300 hover:text-white flex items-center gap-1 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                    <span class="material-symbols-outlined text-[15px]">open_in_new</span> Lihat Marketplace
                </a>
                
                <div class="flex items-center gap-2 border-l border-slate-700 pl-4">
                    <img src="<?= esc(session('user_avatar') ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=128&q=80') ?>" class="w-8 h-8 rounded-full border border-purple-400 object-cover">
                    <div class="text-left hidden sm:block">
                        <span class="block text-xs font-bold text-white"><?= esc(session('user_name') ?? 'Superadmin') ?></span>
                        <span class="block text-[10px] text-purple-300">Administrator Utama</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow flex flex-col md:flex-row gap-6">
        
        <!-- Admin Sidebar Navigation -->
        <aside class="w-full md:w-64 flex-shrink-0 print:hidden">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 space-y-5 sticky top-24">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 px-3">Ringkasan & Analitik</span>
                    <nav class="mt-1.5 space-y-1">
                        <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= current_url() == base_url('admin/dashboard') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">dashboard</span> Dashboard Utama
                        </a>
                        <a href="<?= base_url('admin/transactions') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/transactions') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">receipt_long</span> Transaksi & PO Masuk
                        </a>
                        <a href="<?= base_url('admin/debts') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/debts') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span> Hutang Piutang & Aging
                        </a>
                    </nav>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between px-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700">General Ledger & Keuangan</span>
                        <span class="px-1.5 py-0.5 rounded bg-purple-100 text-purple-800 text-[9px] font-black">GL</span>
                    </div>
                    <nav class="mt-1.5 space-y-1">
                        <a href="<?= base_url('admin/accounting/coa') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/accounting/coa') || current_url() == base_url('admin/accounting') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">account_tree</span> Master COA
                        </a>
                        <a href="<?= base_url('admin/accounting/journals') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/accounting/journals') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">menu_book</span> Jurnal Umum
                        </a>
                        <a href="<?= base_url('admin/accounting/ledger') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/accounting/ledger') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">library_books</span> Buku Besar (GL)
                        </a>
                        <a href="<?= base_url('admin/accounting/cash-bank') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/accounting/cash-bank') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">account_balance</span> Buku Kas & Bank
                        </a>
                        <a href="<?= base_url('admin/accounting/trial-balance') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/accounting/trial-balance') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">balance</span> Neraca Saldo
                        </a>
                        <a href="<?= base_url('admin/accounting/income-statement') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/accounting/income-statement') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">monitoring</span> Laporan Laba Rugi (R/L)
                        </a>
                        <a href="<?= base_url('admin/accounting/balance-sheet') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/accounting/balance-sheet') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">analytics</span> Laporan Neraca
                        </a>
                        <a href="<?= base_url('admin/accounting/cash-flow') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/accounting/cash-flow') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">currency_exchange</span> Laporan Arus Kas
                        </a>
                    </nav>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 px-3">Kelola Ekosistem B2B</span>
                    <nav class="mt-1.5 space-y-1">
                        <a href="<?= base_url('admin/vendors') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/vendors') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">store</span> Verifikasi Mitra Vendor
                        </a>
                        <a href="<?= base_url('admin/buyers') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/buyers') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">corporate_fare</span> Buyer & Limit TOP
                        </a>
                        <a href="<?= base_url('admin/products') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/products') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">inventory_2</span> Katalog & Master Barang
                        </a>
                        <a href="<?= base_url('admin/categories') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/categories') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">category</span> Master Kategori
                        </a>
                        <a href="<?= base_url('admin/rfqs') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/rfqs') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">request_quote</span> Pengawasan RFQ
                        </a>
                    </nav>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 px-3">Kelola Akun & Audit</span>
                    <nav class="mt-1.5 space-y-1">
                        <a href="<?= base_url('admin/users') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/users') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">manage_accounts</span> Manajemen Pengguna
                        </a>
                        <a href="<?= base_url('admin/status-logs') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/status-logs') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">history</span> Log Status Pesanan
                        </a>
                    </nav>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 px-3">Konfigurasi Terpusat</span>
                    <nav class="mt-1.5 space-y-1">
                        <a href="<?= base_url('admin/settings') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'admin/settings') ? 'bg-purple-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">tune</span> Pengaturan Sistem & Pajak
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Admin Content Area -->
        <section class="flex-1 min-w-0">
            <?= $this->renderSection('admin_content') ?>
        </section>

    </div>
</div>
<?= $this->endSection() ?>
