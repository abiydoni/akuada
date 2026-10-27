<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Subheader / Utility Bar (Tokopedia B2B style) -->
<header class="bg-white border-b border-slate-200 text-xs text-slate-600">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-1.5 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <span class="flex items-center gap-1 font-medium text-slate-700">
                <span class="material-symbols-outlined text-[15px] text-purple-600">shield</span>
                E-Procurement Resmi Terdaftar & Terintegrasi e-Faktur Pajak PPN 11%
            </span>
            <span class="hidden md:inline text-slate-300">|</span>
            <span class="hidden md:flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px] text-blue-600">verified</span>
                Fasilitas TOP (Term of Payment) Hingga 90 Hari
            </span>
        </div>
        <div class="flex items-center gap-4 font-medium">
            <a href="<?= base_url('page/panduan-pengadaan') ?>" class="hover:text-purple-600 transition flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">help_center</span> Panduan Pengadaan PO
            </a>
            <span class="text-slate-300">|</span>
            <a href="<?= base_url('page/kontak-b2b') ?>" class="hover:text-purple-600 transition flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">call</span> Call Center: (021) 5088-9900
            </a>
        </div>
    </div>
</header>

<!-- Main Header / Tokopedia-style Search & Action Bar -->
<div class="sticky top-0 z-40 bg-white/95 backdrop-blur shadow-sm border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-4">
        
        <!-- Logo AkuAda (Dominant Purple, Blue, Pink) -->
        <a href="<?= base_url('/') ?>" class="flex items-center gap-2.5 flex-shrink-0 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-700 via-blue-600 to-pink-500 flex items-center justify-center text-white shadow-md shadow-purple-500/20 group-hover:scale-105 transition-transform">
                <span class="material-symbols-outlined text-2xl font-bold">domain</span>
            </div>
            <div>
                <span class="text-2xl font-extrabold tracking-tight font-sans">
                    <span class="text-purple-700">Aku</span><span class="text-blue-600">Ada</span><span class="text-pink-500 font-black">.</span>
                </span>
                <span class="block text-[10px] uppercase font-bold tracking-wider text-slate-600 -mt-1">
                    B2B E-Procurement
                </span>
            </div>
        </a>

        <!-- Category Dropdown Button -->
        <div class="relative group hidden lg:block flex-shrink-0">
            <button class="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold text-slate-700 hover:text-purple-700 rounded-lg hover:bg-purple-50 transition border border-transparent hover:border-purple-200">
                <span class="material-symbols-outlined text-[20px] text-purple-600">grid_view</span>
                <span>Kategori B2B</span>
                <span class="material-symbols-outlined text-[16px] text-slate-400 group-hover:rotate-180 transition-transform">expand_more</span>
            </button>
            
            <!-- Category Popover Menu -->
            <div class="absolute left-0 top-full pt-2 w-72 hidden group-hover:block z-50">
                <div class="bg-white rounded-xl shadow-xl border border-slate-100 p-2.5 space-y-1">
                    <a href="<?= base_url('products?cat=1') ?>" class="flex items-center gap-3 p-2 rounded-lg hover:bg-purple-50 text-slate-700 hover:text-purple-700 transition">
                        <span class="material-symbols-outlined text-purple-600">description</span>
                        <div class="text-xs">
                            <strong class="block font-semibold">Alat Tulis & Kertas</strong>
                            <span class="text-slate-400 text-[11px]">PaperOne, Binder, Ordner</span>
                        </div>
                    </a>
                    <a href="<?= base_url('products?cat=2') ?>" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-50 text-slate-700 hover:text-blue-700 transition">
                        <span class="material-symbols-outlined text-blue-600">devices</span>
                        <div class="text-xs">
                            <strong class="block font-semibold">Komputer & Server IT</strong>
                            <span class="text-slate-400 text-[11px]">ThinkPad, Cisco, PC Kantor</span>
                        </div>
                    </a>
                    <a href="<?= base_url('products?cat=3') ?>" class="flex items-center gap-3 p-2 rounded-lg hover:bg-pink-50 text-slate-700 hover:text-pink-700 transition">
                        <span class="material-symbols-outlined text-pink-600">health_and_safety</span>
                        <div class="text-xs">
                            <strong class="block font-semibold">K3 & Safety APD Proyek</strong>
                            <span class="text-slate-400 text-[11px]">Helm Krisbow, Rompi, Sepatu</span>
                        </div>
                    </a>
                    <a href="<?= base_url('products?cat=4') ?>" class="flex items-center gap-3 p-2 rounded-lg hover:bg-purple-50 text-slate-700 hover:text-purple-700 transition">
                        <span class="material-symbols-outlined text-purple-600">cleaning_services</span>
                        <div class="text-xs">
                            <strong class="block font-semibold">Hygiene & Fasilitas</strong>
                            <span class="text-slate-400 text-[11px]">Dettol Pro, Hand Sanitizer 5L</span>
                        </div>
                    </a>
                    <a href="<?= base_url('products?cat=5') ?>" class="flex items-center gap-3 p-2 rounded-lg hover:bg-blue-50 text-slate-700 hover:text-blue-700 transition">
                        <span class="material-symbols-outlined text-blue-600">precision_manufacturing</span>
                        <div class="text-xs">
                            <strong class="block font-semibold">Mesin Kantor & MRO</strong>
                            <span class="text-slate-400 text-[11px]">Printer HP, Shredder, Tools</span>
                        </div>
                    </a>
                    <a href="<?= base_url('products?cat=6') ?>" class="flex items-center gap-3 p-2 rounded-lg hover:bg-pink-50 text-slate-700 hover:text-pink-700 transition">
                        <span class="material-symbols-outlined text-pink-600">coffee</span>
                        <div class="text-xs">
                            <strong class="block font-semibold">Pantry & Konsumsi Korporat</strong>
                            <span class="text-slate-400 text-[11px]">Kopi Biji Arabica, Sachet</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Search Bar with Tokopedia-like Styling -->
        <form action="<?= base_url('products') ?>" method="GET" class="flex-1 max-w-2xl relative">
            <div class="relative flex items-center">
                <input type="text" name="q" value="<?= esc($keyword ?? '') ?>" placeholder="Cari kebutuhan pengadaan kantor (contoh: Kertas A4, ThinkPad, Helm K3, Kursi Kerja)..." 
                       class="w-full pl-11 pr-24 py-2.5 bg-slate-100 hover:bg-slate-50 focus:bg-white text-sm text-slate-900 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 transition shadow-inner placeholder:text-slate-400">
                <span class="material-symbols-outlined absolute left-3.5 text-slate-400 text-[20px]">search</span>
                <button type="submit" class="absolute right-1.5 px-4 py-1.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white text-xs font-semibold rounded-lg shadow transition">
                    Cari
                </button>
            </div>
        </form>

        <!-- Right Action Items: RFQ Quick CTA, Cart, Dashboard Shortcuts -->
        <div class="flex items-center gap-3 flex-shrink-0">
            <!-- RFQ Button -->
            <a href="<?= base_url('rfq/create') ?>" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-lg bg-pink-50 text-pink-700 border border-pink-200 hover:bg-pink-100 hover:border-pink-300 transition shadow-sm">
                <span class="material-symbols-outlined text-[18px]">assignment_add</span>
                <span>Minta RFQ Resmi</span>
            </a>

            <!-- Cart PO Button -->
            <a href="<?= base_url('cart') ?>" class="relative p-2 text-slate-600 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition" title="Keranjang Pengadaan PO">
                <span class="material-symbols-outlined text-[24px]">shopping_cart</span>
                <?php 
                    $cartCount = count(session('cart') ?? []);
                    if ($cartCount > 0): 
                ?>
                    <span class="absolute top-1 right-1 w-5 h-5 bg-pink-500 text-white text-[11px] font-bold rounded-full flex items-center justify-center animate-pulse">
                        <?= $cartCount ?>
                    </span>
                <?php endif; ?>
            </a>

            <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

            <!-- User Auth / Dashboard Access -->
            <?php if (session('user_id')): ?>
                <div class="relative group">
                    <button class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-slate-100 transition">
                        <img src="<?= esc(session('user_avatar') ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=128&q=80') ?>" class="w-8 h-8 rounded-full object-cover border border-purple-300 ring-2 ring-purple-100">
                        <div class="text-left hidden xl:block">
                            <span class="block text-xs font-bold text-slate-800 line-clamp-1"><?= esc(session('user_name')) ?></span>
                            <span class="block text-[10px] text-purple-600 font-semibold uppercase"><?= esc(session('user_role')) ?></span>
                        </div>
                        <span class="material-symbols-outlined text-slate-400 text-[18px]">expand_more</span>
                    </button>
                    <div class="absolute right-0 top-full pt-2 w-56 hidden group-hover:block z-50">
                        <div class="bg-white rounded-xl shadow-xl border border-slate-100 p-2 space-y-1 text-xs">
                            <div class="p-2 border-b border-slate-100 mb-1">
                                <p class="font-bold text-slate-800"><?= esc(session('company_name') ?? 'Akun Personal') ?></p>
                                <p class="text-[11px] text-slate-400"><?= esc(session('user_email')) ?></p>
                            </div>
                            <?php if (session('user_role') === 'admin'): ?>
                                <a href="<?= base_url('admin/dashboard') ?>" class="flex items-center gap-2 p-2 rounded hover:bg-purple-50 text-purple-700 font-semibold">
                                    <span class="material-symbols-outlined text-[16px]">admin_panel_settings</span> Dashboard Admin
                                </a>
                            <?php elseif (session('user_role') === 'vendor'): ?>
                                <a href="<?= base_url('vendor/dashboard') ?>" class="flex items-center gap-2 p-2 rounded hover:bg-blue-50 text-blue-700 font-semibold">
                                    <span class="material-symbols-outlined text-[16px]">store</span> Dashboard Vendor
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('customer/dashboard') ?>" class="flex items-center gap-2 p-2 rounded hover:bg-pink-50 text-pink-700 font-semibold">
                                    <span class="material-symbols-outlined text-[16px]">corporate_fare</span> Dashboard Perusahaan
                                </a>
                            <?php endif; ?>
                            <a href="<?= base_url('customer/orders') ?>" class="flex items-center gap-2 p-2 rounded hover:bg-slate-50 text-slate-700">
                                <span class="material-symbols-outlined text-[16px]">receipt_long</span> Riwayat Purchase Order (PO)
                            </a>
                            <a href="<?= base_url('rfq') ?>" class="flex items-center gap-2 p-2 rounded hover:bg-slate-50 text-slate-700">
                                <span class="material-symbols-outlined text-[16px]">request_quote</span> Permintaan RFQ
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <a href="<?= base_url('auth/logout') ?>" class="flex items-center gap-2 p-2 rounded hover:bg-rose-50 text-rose-600 font-medium">
                                <span class="material-symbols-outlined text-[16px]">logout</span> Keluar Akun
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex items-center gap-2">
                    <a href="<?= base_url('auth/login') ?>" class="px-3 py-1.5 text-xs font-bold text-purple-700 hover:bg-purple-50 rounded-lg transition border border-purple-300">
                        Masuk
                    </a>
                    <a href="<?= base_url('auth/register') ?>" class="px-3.5 py-1.5 text-xs font-bold text-white bg-gradient-to-r from-purple-600 to-pink-500 rounded-lg shadow-sm hover:opacity-95 transition">
                        Daftar Perusahaan
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Main Body Content -->
<main class="flex-grow">
    <?= $this->renderSection('subcontent') ?>
</main>

<!-- Enterprise B2B Footer -->
<footer class="bg-slate-900 text-slate-400 text-xs border-t border-slate-800 mt-16">
    <!-- Value Pillars Banner -->
    <div class="border-b border-slate-800 bg-slate-950/60 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400">
                    <span class="material-symbols-outlined text-2xl">verified_user</span>
                </div>
                <div>
                    <h4 class="font-bold text-white text-sm">Faktur Pajak PPN 11%</h4>
                    <p class="text-[11px] text-slate-400">Integrasi e-Faktur resmi DJP untuk pelaporan SPT Badan.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400">
                    <span class="material-symbols-outlined text-2xl">event_repeat</span>
                </div>
                <div>
                    <h4 class="font-bold text-white text-sm">Kredit Tempo (TOP)</h4>
                    <p class="text-[11px] text-slate-400">Plafon kredit hingga Rp 500 Juta tempo 14-90 hari.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-pink-500/10 border border-pink-500/20 flex items-center justify-center text-pink-400">
                    <span class="material-symbols-outlined text-2xl">contract</span>
                </div>
                <div>
                    <h4 class="font-bold text-white text-sm">Legalitas & Dokumen B2B</h4>
                    <p class="text-[11px] text-slate-400">Otomatisasi SPK, Purchase Order (PO), Surat Jalan, BAST.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400">
                    <span class="material-symbols-outlined text-2xl">handshake</span>
                </div>
                <div>
                    <h4 class="font-bold text-white text-sm">Official Supplier Verified</h4>
                    <p class="text-[11px] text-slate-400">Vendor terverifikasi dengan legalitas NIB & NPWP aktif.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer Links -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 md:grid-cols-5 gap-8">
        <div class="md:col-span-2 space-y-4">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-purple-600 via-blue-600 to-pink-500 flex items-center justify-center text-white">
                    <span class="material-symbols-outlined text-lg">domain</span>
                </div>
                <span class="text-xl font-extrabold text-white">AkuAda<span class="text-pink-500">.</span></span>
            </div>
            <p class="text-slate-400 leading-relaxed text-xs">
                <strong>AkuAda</strong> hadir memastikan setiap kebutuhan operasional perusahaan Anda terpenuhi dengan tepat, cepat, dan transparan. E-Procurement B2B terpercaya di Indonesia dengan fleksibilitas TOP, harga bertingkat grosir korporat, dan kelengkapan faktur pajak resmi.
            </p>
            <div class="text-xs space-y-1 text-slate-400">
                <p>📍 Gedung AkuAda Procurement Tower Lt. 18, Jakarta Selatan</p>
                <p>📞 Phone: (021) 5088-9900 | ✉️ Email: b2b@akuada.co.id</p>
            </div>
        </div>

        <div>
            <h5 class="text-white font-bold mb-3 uppercase tracking-wider text-[11px]">Kategori Utama</h5>
            <ul class="space-y-2">
                <li><a href="<?= base_url('products?cat=1') ?>" class="hover:text-purple-400 transition">Alat Tulis & Kertas</a></li>
                <li><a href="<?= base_url('products?cat=2') ?>" class="hover:text-purple-400 transition">Komputer & Hardware IT</a></li>
                <li><a href="<?= base_url('products?cat=3') ?>" class="hover:text-purple-400 transition">K3 & Safety APD Proyek</a></li>
                <li><a href="<?= base_url('products?cat=4') ?>" class="hover:text-purple-400 transition">Hygiene & Fasilitas Kantor</a></li>
                <li><a href="<?= base_url('products?cat=5') ?>" class="hover:text-purple-400 transition">Mesin Kantor & MRO</a></li>
                <li><a href="<?= base_url('products?cat=7') ?>" class="hover:text-purple-400 transition">Furnitur Ergonomis</a></li>
            </ul>
        </div>

        <div>
            <h5 class="text-white font-bold mb-3 uppercase tracking-wider text-[11px]">Layanan Pengadaan</h5>
            <ul class="space-y-2">
                <li><a href="<?= base_url('rfq/create') ?>" class="hover:text-blue-400 transition">Minta Penawaran (RFQ)</a></li>
                <li><a href="<?= base_url('page/kredit-top') ?>" class="hover:text-blue-400 transition">Pengajuan Kredit TOP</a></li>
                <li><a href="<?= base_url('page/pajak-faktur') ?>" class="hover:text-blue-400 transition">Ketentuan e-Faktur Pajak</a></li>
                <li><a href="<?= base_url('page/syarat-vendor') ?>" class="hover:text-blue-400 transition">Gabung Jadi Mitra Vendor</a></li>
                <li><a href="<?= base_url('page/simulasi-top') ?>" class="hover:text-blue-400 transition">Kalkulator Tempo Pengadaan</a></li>
            </ul>
        </div>

        <div>
            <h5 class="text-white font-bold mb-3 uppercase tracking-wider text-[11px]">Portal Akses</h5>
            <ul class="space-y-2">
                <li><a href="<?= base_url('admin/dashboard') ?>" class="hover:text-pink-400 transition flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">admin_panel_settings</span> Admin Platform</a></li>
                <li><a href="<?= base_url('vendor/dashboard') ?>" class="hover:text-pink-400 transition flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">store</span> Portal Vendor Supplier</a></li>
                <li><a href="<?= base_url('customer/dashboard') ?>" class="hover:text-pink-400 transition flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">corporate_fare</span> Portal Corporate Buyer</a></li>
                <li><a href="<?= base_url('cart') ?>" class="hover:text-pink-400 transition flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">shopping_cart</span> Keranjang PO</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t border-slate-800 py-6 text-center text-[11px] text-slate-500">
        &copy; <?= date('Y') ?> PT Aku Ada Nusantara Bersama. All Rights Reserved. Marketplace Pengadaan Kebutuhan Operasional Perusahaan.
    </div>
</footer>

<?= $this->endSection() ?>
