<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="min-h-screen flex flex-col bg-slate-100">

    <!-- Customer Top Navbar -->
    <header class="bg-slate-900 text-white border-b border-pink-900/60 sticky top-0 z-30 shadow-md">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('customer/dashboard') ?>" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-pink-600 via-purple-600 to-indigo-600 flex items-center justify-center text-white shadow-md">
                        <span class="material-symbols-outlined text-xl">corporate_fare</span>
                    </div>
                    <div>
                        <span class="text-lg font-extrabold text-white">AkuAda <span class="text-pink-400 font-semibold text-xs tracking-wider uppercase px-2 py-0.5 rounded bg-pink-950/80 border border-pink-800/60 ml-1">Portal Corporate Buyer</span></span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <a href="<?= base_url('/') ?>" class="text-xs text-slate-300 hover:text-white flex items-center gap-1 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                    <span class="material-symbols-outlined text-[15px]">storefront</span> Katalog Marketplace
                </a>
                
                <div class="flex items-center gap-2 border-l border-slate-700 pl-4">
                    <img src="<?= esc(session('user_avatar') ?? 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=128&q=80') ?>" class="w-8 h-8 rounded-full border border-pink-400 object-cover">
                    <div class="text-left hidden sm:block">
                        <span class="block text-xs font-bold text-white"><?= esc(session('user_name') ?? 'Rina Kartika') ?></span>
                        <span class="block text-[10px] text-pink-300"><?= esc(session('company_name') ?? 'PT Mega Solusi Digital Tbk') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow flex flex-col md:flex-row gap-6">
        
        <!-- Customer Sidebar Navigation -->
        <aside class="w-full md:w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 space-y-6 sticky top-24">
                
                <!-- TOP Credit Plafon Status Card -->
                <?php
                    $creditLimit = session('credit_limit_top') ?? 250000000.0;
                    $creditUsed = session('credit_used_top') ?? 31250000.0;
                    $creditRemaining = max(0, $creditLimit - $creditUsed);
                    $percentage = $creditLimit > 0 ? round(($creditUsed / $creditLimit) * 100) : 0;
                ?>
                <div class="p-3.5 rounded-xl bg-gradient-to-br from-purple-50 via-pink-50/50 to-white border border-purple-200">
                    <div class="flex items-center justify-between text-xs mb-1">
                        <span class="font-bold text-purple-900">Plafon Kredit TOP</span>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-purple-200/60 text-purple-800">Tempo Aktif</span>
                    </div>
                    <p class="text-sm font-extrabold text-slate-800"><?= format_rupiah($creditRemaining) ?></p>
                    <p class="text-[10px] text-slate-500 mb-2">Sisa dari <?= format_rupiah($creditLimit) ?></p>
                    <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-600 to-pink-500 h-1.5 rounded-full" style="width: <?= $percentage ?>%"></div>
                    </div>
                    <div class="flex justify-between items-center text-[10px] text-slate-500 mt-1 font-medium">
                        <span>Terpakai <?= $percentage ?>%</span>
                        <a href="<?= base_url('customer/credit-limit') ?>" class="text-purple-700 hover:underline">Naikkan Limit</a>
                    </div>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 px-3">Pengadaan Korporat</span>
                    <nav class="mt-2 space-y-1">
                        <a href="<?= base_url('customer/dashboard') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= current_url() == base_url('customer/dashboard') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">space_dashboard</span> Dashboard Pengadaan
                        </a>
                        <a href="<?= base_url('customer/orders') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'customer/orders') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">receipt_long</span> Purchase Order (PO) & BAST
                        </a>
                        <a href="<?= base_url('customer/rfqs') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'customer/rfqs') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">request_quote</span> Permintaan RFQ Saya
                        </a>
                    </nav>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 px-3">Fiskal & Legal Perusahaan</span>
                    <nav class="mt-2 space-y-1">
                        <a href="<?= base_url('customer/tax-invoices') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'customer/tax-invoices') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">receipt</span> e-Faktur Pajak PPN 11%
                        </a>
                        <a href="<?= base_url('customer/company-profile') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'customer/company-profile') ? 'bg-pink-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">badge</span> NPWP & Legalitas PT
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Customer Content Area -->
        <section class="flex-1 min-w-0">
            <?= $this->renderSection('customer_content') ?>
        </section>

    </div>
</div>
<?= $this->endSection() ?>
