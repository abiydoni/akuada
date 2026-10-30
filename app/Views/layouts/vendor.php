<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="min-h-screen flex flex-col bg-slate-100">

    <!-- Vendor Top Navbar -->
    <header class="bg-slate-900 text-white border-b border-blue-900/60 sticky top-0 z-30 shadow-md">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="<?= base_url('vendor/dashboard') ?>" class="flex items-center gap-2 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-purple-600 flex items-center justify-center text-white shadow-md">
                        <span class="material-symbols-outlined text-xl">store</span>
                    </div>
                    <div>
                        <span class="text-lg font-extrabold text-white">AkuAda <span class="text-blue-400 font-semibold text-xs tracking-wider uppercase px-2 py-0.5 rounded bg-blue-950/80 border border-blue-800/60 ml-1">Portal Vendor</span></span>
                    </div>
                </a>
            </div>

            <div class="flex items-center gap-4">
                <a href="<?= base_url('/') ?>" class="text-xs text-slate-300 hover:text-white flex items-center gap-1 bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-700">
                    <span class="material-symbols-outlined text-[15px]">open_in_new</span> Lihat Marketplace
                </a>
                
                <div class="flex items-center gap-2 border-l border-slate-700 pl-4">
                    <img src="<?= esc(session('user_avatar') ?? 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=128&q=80') ?>" class="w-8 h-8 rounded-full border border-blue-400 object-cover">
                    <div class="text-left hidden sm:block">
                        <span class="block text-xs font-bold text-white"><?= esc(session('user_name') ?? 'Mitra Supplier') ?></span>
                        <span class="block text-[10px] text-blue-300"><?= esc(session('company_name') ?? 'PT Mitra Office') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow flex flex-col md:flex-row gap-6">
        
        <!-- Vendor Sidebar Navigation -->
        <aside class="w-full md:w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 space-y-6 sticky top-24">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 px-3">Operasional Vendor</span>
                    <nav class="mt-2 space-y-1">
                        <a href="<?= base_url('vendor/dashboard') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= current_url() == base_url('vendor/dashboard') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">space_dashboard</span> Ringkasan Penjualan
                        </a>
                        <a href="<?= base_url('vendor/orders') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'vendor/orders') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">local_shipping</span> Pesanan PO & BAST
                        </a>
                        <a href="<?= base_url('vendor/settlements') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'vendor/settlements') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">payments</span> Piutang & Pencairan Dana
                        </a>
                        <a href="<?= base_url('vendor/rfqs') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'vendor/rfqs') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">price_change</span> Minta Nego & RFQ
                        </a>
                    </nav>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 px-3">Katalog B2B</span>
                    <nav class="mt-2 space-y-1">
                        <a href="<?= base_url('vendor/products') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= current_url() == base_url('vendor/products') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">inventory</span> Daftar Produk Saya
                        </a>
                        <a href="<?= base_url('vendor/products/new') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= current_url() == base_url('vendor/products/new') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">add_box</span> Tambah Barang Baru
                        </a>
                    </nav>
                </div>

                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600 px-3">Identitas Bisnis</span>
                    <nav class="mt-2 space-y-1">
                        <a href="<?= base_url('vendor/company-profile') ?>" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold <?= str_contains(current_url(), 'vendor/company-profile') ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-700 hover:bg-slate-100' ?>">
                            <span class="material-symbols-outlined text-[18px]">badge</span> Legalitas & NPWP Vendor
                        </a>
                    </nav>
                </div>
            </div>
        </aside>

        <!-- Vendor Content Area -->
        <section class="flex-1 min-w-0">
            <?= $this->renderSection('vendor_content') ?>
        </section>

    </div>
</div>
<?= $this->endSection() ?>
