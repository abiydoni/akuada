<?= $this->extend('layouts/main') ?>

<?= $this->section('subcontent') ?>

<!-- Hero Section (Tokopedia B2B Theme: Dominant Purple, Blue, Pink) -->
<section class="relative bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 text-white overflow-hidden py-12 md:py-16">
    <!-- Ambient Glow / Blur Orbs -->
    <div class="absolute -top-24 -left-24 w-96 h-96 bg-purple-600/30 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/2 right-0 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-1/3 w-80 h-80 bg-blue-600/25 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 backdrop-blur border border-white/20 text-pink-300 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-pink-400 animate-ping"></span>
                    <span>Platform Pengadaan B2B Terlengkap di Indonesia</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight">
                    Aku Ada, Berarti <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-300 via-blue-300 to-pink-400">
                        Kebutuhan Operasional Perusahaan Anda
                    </span> <br>
                    Pasti Tersedia.
                </h1>

                <p class="text-slate-300 text-sm sm:text-base max-w-xl leading-relaxed">
                    Pengadaan barang kantor, IT hardware, perlengkapan K3 APD, hygiene, hingga pantry korporat. Lengkap dengan <strong>Faktur Pajak PPN 11%</strong>, harga grosir bertingkat, dan opsi pembayaran tempo <strong>TOP hingga 90 hari</strong>.
                </p>

                <!-- Quick Action Buttons -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="<?= base_url('products') ?>" class="btn-akuada-primary px-6 py-3 text-sm">
                        <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                        <span>Jelajahi Katalog B2B</span>
                    </a>
                    <a href="<?= base_url('rfq/create') ?>" class="px-5 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-sm border border-white/30 backdrop-blur transition flex items-center gap-2">
                        <span class="material-symbols-outlined text-[20px] text-pink-400">request_quote</span>
                        <span>Minta Penawaran (RFQ)</span>
                    </a>
                </div>

                <!-- B2B Value Badges -->
                <div class="grid grid-cols-3 gap-3 pt-6 border-t border-white/10 max-w-lg text-xs">
                    <div>
                        <div class="font-extrabold text-lg text-purple-300">100%</div>
                        <div class="text-slate-400 text-[11px]">Resmi Ber-Faktur Pajak</div>
                    </div>
                    <div>
                        <div class="font-extrabold text-lg text-blue-300">90 Hari</div>
                        <div class="text-slate-400 text-[11px]">Kredit Tempo (TOP)</div>
                    </div>
                    <div>
                        <div class="font-extrabold text-lg text-pink-300">Rp 500 Juta</div>
                        <div class="text-slate-400 text-[11px]">Plafon Kredit Pengadaan</div>
                    </div>
                </div>
            </div>

            <!-- Hero Feature Banner Card (Right side) -->
            <div class="lg:col-span-5">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 shadow-2xl relative">
                    <div class="flex items-center justify-between pb-4 border-b border-white/10">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-purple-400">tune</span>
                            <span class="font-bold text-sm text-white">Simulasi Pengadaan Cepat</span>
                        </div>
                        <span class="px-2 py-0.5 rounded bg-pink-500/30 text-pink-300 text-[10px] font-bold uppercase tracking-wider">B2B Account</span>
                    </div>

                    <div class="py-4 space-y-3 text-xs">
                        <div class="bg-slate-900/60 p-3 rounded-xl border border-white/5 space-y-1">
                            <span class="text-slate-400 text-[11px]">Kebutuhan Rutin Terpopuler:</span>
                            <div class="flex flex-wrap gap-1.5 pt-1">
                                <a href="<?= base_url('products?cat=1') ?>" class="px-2 py-1 rounded-md bg-purple-500/20 text-purple-200 border border-purple-500/30 hover:bg-purple-500/30 transition">Kertas Fotocopy A4 (100 Box)</a>
                                <a href="<?= base_url('products?cat=2') ?>" class="px-2 py-1 rounded-md bg-blue-500/20 text-blue-200 border border-blue-500/30 hover:bg-blue-500/30 transition">Laptop ThinkPad (10 Unit)</a>
                                <a href="<?= base_url('products?cat=3') ?>" class="px-2 py-1 rounded-md bg-pink-500/20 text-pink-200 border border-pink-500/30 hover:bg-pink-500/30 transition">Helm Proyek Krisbow SNI</a>
                            </div>
                        </div>

                        <div class="bg-slate-900/60 p-3 rounded-xl border border-white/5 flex items-center justify-between">
                            <div>
                                <span class="block text-slate-400 text-[11px]">Estimasi Hemat Volume:</span>
                                <span class="text-base font-extrabold text-emerald-400">Hingga 18% Diskon</span>
                            </div>
                            <div class="text-right">
                                <span class="block text-slate-400 text-[11px]">Fasilitas Pembayaran:</span>
                                <span class="text-xs font-bold text-white">TOP 30-90 Hari</span>
                            </div>
                        </div>
                    </div>

                    <a href="<?= base_url('rfq/create') ?>" class="block w-full text-center py-2.5 rounded-xl bg-gradient-to-r from-pink-500 to-purple-600 hover:from-pink-600 hover:to-purple-700 text-white font-bold text-xs shadow-lg shadow-pink-500/25 transition">
                        Ajukan Kebutuhan PO Perusahaan Anda &rarr;
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Category Icons Carousel / Grid (Tokopedia Style) -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 -mt-6 relative z-20">
    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Kategori Kebutuhan Operasional</h2>
                <p class="text-xs text-slate-500">Pilih kategori spesifik pengadaan operasional kantor dan industri</p>
            </div>
            <a href="<?= base_url('products') ?>" class="text-xs font-bold text-purple-700 hover:text-purple-800 flex items-center gap-0.5">
                Lihat Semua <span class="material-symbols-outlined text-[16px]">chevron_right</span>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-7 gap-3">
            <?php foreach ($categories as $cat): ?>
                <a href="<?= base_url('products?cat=' . $cat['id']) ?>" class="flex flex-col items-center p-3 rounded-xl hover:bg-purple-50/60 border border-slate-100 hover:border-purple-200 transition text-center group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-purple-100 via-indigo-50 to-pink-100 flex items-center justify-center text-purple-700 group-hover:scale-110 transition-transform shadow-sm">
                        <span class="material-symbols-outlined text-2xl"><?= esc($cat['icon']) ?></span>
                    </div>
                    <span class="mt-2.5 text-xs font-semibold text-slate-700 group-hover:text-purple-800 line-clamp-2 leading-tight">
                        <?= esc($cat['name']) ?>
                    </span>
                    <span class="text-[10px] text-slate-400 mt-1">
                        <?= $cat['product_count'] ?? 0 ?> Produk
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Special B2B Features / Official Suppliers -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800 border border-purple-200">Mitra Terverifikasi</span>
                <h2 class="text-xl font-bold text-slate-900">Official Vendor & Principle Store</h2>
            </div>
            <p class="text-xs text-slate-500 mt-1">Pemasok resmi bersertifikasi legalitas NIB, NPWP aktif, dan garansi resmi prinsipal.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php foreach ($officialVendors as $vendor): ?>
            <div class="akuada-card p-5 hover:border-blue-300 transition relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-purple-500/10 to-transparent rounded-bl-full pointer-events-none"></div>
                <div class="flex items-start gap-3.5">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-600 to-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md flex-shrink-0">
                        <?= substr($vendor['company_name'], 3, 2) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5">
                            <h3 class="font-bold text-slate-900 text-sm truncate"><?= esc($vendor['company_name']) ?></h3>
                            <?php if ($vendor['is_verified']): ?>
                                <span class="material-symbols-outlined text-[16px] text-blue-600" title="Terverifikasi Resmi">verified</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-0.5">NPWP: <?= esc($vendor['npwp'] ?? 'Terdaftar') ?></p>
                        <p class="text-[11px] text-slate-500">📍 <?= esc($vendor['city'] ?? 'Jakarta') ?></p>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Legalitas: <strong class="text-purple-700"><?= esc($vendor['legal_type']) ?> Aktif</strong></span>
                    <a href="<?= base_url('products?vendor=' . $vendor['id']) ?>" class="font-bold text-purple-700 hover:text-purple-900 group-hover:translate-x-0.5 transition-transform flex items-center gap-0.5">
                        Lihat Katalog &rarr;
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Featured B2B Products (Detail & Lengkap: MOQ, Tier Pricing, Satuan, PPN) -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Kebutuhan Pengadaan Operasional Unggulan</h2>
            <p class="text-xs text-slate-500 mt-1">Daftar barang dengan harga bertingkat grosir, MOQ jelas, dan faktur pajak PPN 11%.</p>
        </div>
        <a href="<?= base_url('products') ?>" class="btn-akuada-outline text-xs px-3.5 py-1.5">
            Lihat Semua Produk
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php foreach ($featuredProducts as $product): ?>
            <div class="akuada-card akuada-card-hover flex flex-col justify-between overflow-hidden group">
                <div>
                    <!-- Product Image & Badges -->
                    <div class="relative bg-slate-100 h-48 overflow-hidden">
                        <img src="<?= esc($product['image_url']) ?>" alt="<?= esc($product['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        
                        <!-- Top Badges -->
                        <div class="absolute top-2.5 left-2.5 flex flex-col gap-1">
                            <span class="badge-moq shadow-sm">
                                Min. Order (MOQ): <?= $product['moq'] ?> <?= esc($product['uom']) ?>
                            </span>
                            <?php if ($product['tkdn_percent'] > 0): ?>
                                <span class="badge-tkdn shadow-sm">
                                    TKDN <?= $product['tkdn_percent'] ?>%
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="absolute bottom-2.5 right-2.5">
                            <span class="badge-ppn shadow-sm">
                                PPN 11% Resmi
                            </span>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="p-4 space-y-2">
                        <div class="flex items-center justify-between text-[11px] text-slate-400">
                            <span class="font-semibold text-purple-700"><?= esc($product['brand'] ?? 'Official') ?></span>
                            <span>SKU: <?= esc($product['sku']) ?></span>
                        </div>

                        <h3 class="font-bold text-slate-900 text-sm line-clamp-2 group-hover:text-purple-700 transition leading-snug">
                            <a href="<?= base_url('product/' . $product['slug']) ?>">
                                <?= esc($product['name']) ?>
                            </a>
                        </h3>

                        <!-- Price Section -->
                        <div class="pt-2">
                            <span class="text-[11px] text-slate-500 block">Harga Satuan B2B (per <?= esc($product['uom']) ?>):</span>
                            <div class="text-lg font-extrabold text-slate-900">
                                <?= format_rupiah($product['price_regular']) ?>
                            </div>
                            <span class="text-[10px] text-pink-600 font-semibold block">
                                *Tersedia harga tiering volume diskon
                            </span>
                        </div>

                        <!-- Vendor info -->
                        <div class="text-[11px] text-slate-500 pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="truncate max-w-[150px]"><?= esc($product['vendor_name']) ?></span>
                            <span class="text-[10px] bg-slate-100 px-1.5 py-0.5 rounded text-slate-600 font-medium">Ready Stock</span>
                        </div>
                    </div>
                </div>

                <!-- Footer Card Action -->
                <div class="p-4 pt-0">
                    <a href="<?= base_url('product/' . $product['slug']) ?>" class="w-full btn-akuada-primary text-xs py-2">
                        <span class="material-symbols-outlined text-[16px]">visibility</span>
                        <span>Lihat Detail & Tier Harga</span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- RFQ Direct Banner (Tokopedia Style Request Banner) -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="bg-gradient-to-r from-purple-800 via-indigo-700 to-pink-600 rounded-3xl p-8 sm:p-12 text-white shadow-2xl relative overflow-hidden">
        <div class="relative z-10 max-w-2xl space-y-4">
            <span class="px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider backdrop-blur">
                Layanan Pengadaan Khusus & Tender
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Punya Daftar Kebutuhan Barang dalam Jumlah Besar?
            </h2>
            <p class="text-purple-100 text-sm leading-relaxed">
                Kirimkan daftar barang Anda melalui fitur <strong>Request for Quotation (RFQ)</strong>. Tim vendor terverifikasi AkuAda akan memberikan penawaran harga khusus korporat, negosiasi termin TOP, dan pengiriman multi-cabang.
            </p>
            <div class="pt-2">
                <a href="<?= base_url('rfq/create') ?>" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-purple-900 font-bold text-sm shadow-xl hover:bg-purple-50 transition transform hover:-translate-y-0.5">
                    <span class="material-symbols-outlined text-[20px] text-pink-600">post_add</span>
                    <span>Buat Permintaan Penawaran (RFQ) Sekarang</span>
                </a>
            </div>
        </div>

        <div class="absolute right-0 bottom-0 top-0 w-1/3 hidden lg:flex items-center justify-center opacity-20 pointer-events-none">
            <span class="material-symbols-outlined text-[240px]">contract</span>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
