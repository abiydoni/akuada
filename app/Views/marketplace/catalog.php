<?= $this->extend('layouts/main') ?>

<?= $this->section('subcontent') ?>

<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
            <a href="<?= base_url('/') ?>" class="hover:text-purple-600">Beranda</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold">Katalog Pengadaan B2B</span>
            <?php if ($activeCategory): ?>
                <span>/</span>
                <span class="text-purple-700 font-semibold"><?= esc($activeCategory['name']) ?></span>
            <?php endif; ?>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900">
            <?= $activeCategory ? esc($activeCategory['name']) : 'Semua Kebutuhan Operasional Perusahaan' ?>
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Menampilkan <?= count($products) ?> produk pengadaan resmi dengan harga grosir bertingkat dan PPN 11%.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Filter Sidebar -->
        <aside class="w-full lg:w-64 flex-shrink-0">
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-6 sticky top-24">
                <div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Kategori B2B</h3>
                    <ul class="space-y-1 text-xs">
                        <li>
                            <a href="<?= base_url('products') ?>" class="flex items-center justify-between p-2 rounded-lg <?= empty($catId) ? 'bg-purple-50 text-purple-700 font-bold' : 'text-slate-600 hover:bg-slate-50' ?>">
                                <span>Semua Kategori</span>
                                <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="<?= base_url('products?cat=' . $cat['id']) ?>" class="flex items-center justify-between p-2 rounded-lg <?= ($catId == $cat['id']) ? 'bg-purple-50 text-purple-700 font-bold' : 'text-slate-600 hover:bg-slate-50' ?>">
                                    <span class="truncate"><?= esc($cat['name']) ?></span>
                                    <span class="text-[10px] text-slate-400"><?= $cat['product_count'] ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="border-t border-slate-100 pt-4 space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Ketentuan Pembayaran</h3>
                    <div class="space-y-2 text-xs text-slate-600">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" checked class="rounded text-purple-600 focus:ring-purple-500">
                            <span>Mendukung TOP (Kredit Tempo)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" checked class="rounded text-purple-600 focus:ring-purple-500">
                            <span>Ber-Faktur Pajak PPN 11%</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="rounded text-purple-600 focus:ring-purple-500">
                            <span>Memiliki Sertifikat TKDN</span>
                        </label>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <div class="p-3 rounded-xl bg-pink-50 border border-pink-200 text-xs space-y-2">
                        <strong class="font-bold text-pink-900 block">Kebutuhan Kustom?</strong>
                        <p class="text-pink-700 text-[11px] leading-relaxed">Ajukan permintaan spesifikasi khusus atau negosiasi volume besar.</p>
                        <a href="<?= base_url('rfq/create') ?>" class="block text-center py-1.5 bg-pink-600 hover:bg-pink-700 text-white font-bold rounded-lg transition text-[11px]">
                            Minta RFQ Resmi
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Product Grid -->
        <main class="flex-1 min-w-0">
            <?php if (empty($products)): ?>
                <div class="bg-white rounded-2xl p-12 text-center border border-slate-200">
                    <span class="material-symbols-outlined text-5xl text-slate-300">search_off</span>
                    <h3 class="text-base font-bold text-slate-800 mt-3">Tidak Ada Barang yang Cocok</h3>
                    <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci pencarian lain atau pilih kategori yang berbeda.</p>
                    <a href="<?= base_url('products') ?>" class="mt-4 inline-block btn-akuada-primary text-xs px-4 py-2">
                        Reset Pencarian
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($products as $product): ?>
                        <div class="akuada-card akuada-card-hover flex flex-col justify-between overflow-hidden group">
                            <div>
                                <div class="relative bg-slate-100 h-48 overflow-hidden">
                                    <img src="<?= esc($product['image_url']) ?>" alt="<?= esc($product['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    
                                    <div class="absolute top-2.5 left-2.5 flex flex-col gap-1">
                                        <span class="badge-moq shadow-sm">
                                            MOQ: <?= $product['moq'] ?> <?= esc($product['uom']) ?>
                                        </span>
                                        <?php if ($product['tkdn_percent'] > 0): ?>
                                            <span class="badge-tkdn shadow-sm">
                                                TKDN <?= $product['tkdn_percent'] ?>%
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="absolute bottom-2.5 right-2.5">
                                        <span class="badge-ppn shadow-sm">
                                            PPN 11%
                                        </span>
                                    </div>
                                </div>

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

                                    <div class="pt-2">
                                        <span class="text-[11px] text-slate-500 block">Harga Satuan B2B:</span>
                                        <div class="text-lg font-extrabold text-slate-900">
                                            <?= format_rupiah($product['price_regular']) ?>
                                            <span class="text-xs text-slate-400 font-normal">/ <?= esc($product['uom']) ?></span>
                                        </div>
                                    </div>

                                    <div class="text-[11px] text-slate-500 pt-2 border-t border-slate-100 flex items-center justify-between">
                                        <span class="truncate max-w-[140px] font-medium text-slate-700"><?= esc($product['vendor_name']) ?></span>
                                        <span class="text-pink-600 font-semibold text-[10px]">Tersedia Grosir</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 pt-0">
                                <a href="<?= base_url('product/' . $product['slug']) ?>" class="w-full btn-akuada-primary text-xs py-2">
                                    <span class="material-symbols-outlined text-[16px]">visibility</span>
                                    <span>Lihat Detail & Kalkulator Tier</span>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>

    </div>
</div>

<?= $this->endSection() ?>
