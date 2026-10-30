<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-wider">Manajemen Katalog</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Katalog Barang Pengadaan B2B Saya</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola harga grosir bertingkat (Tier Pricing), stok gudang, MOQ, dan sertifikasi TKDN barang Anda.</p>
        </div>
        <a href="<?= base_url('vendor/products/new') ?>" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2 self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">add_box</span>
            <span>+ Tambah Barang Pengadaan Baru</span>
        </a>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <h3 class="font-bold text-slate-800 text-sm">Daftar SKU Barang Aktif & Draft</h3>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px]"><?= count($products) ?> SKU</span>
            </div>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Barang & SKU</th>
                        <th class="px-4 py-3.5">Kategori & Stok</th>
                        <th class="px-4 py-3.5 text-right w-48">Harga B2B & Hak Bersih</th>
                        <th class="px-4 py-3.5 text-center w-36">Pajak & Status</th>
                        <th class="px-4 py-3.5 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">inventory_2</span>
                                    <p class="font-semibold text-slate-600">Belum ada barang di katalog Anda</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Klik "+ Tambah Barang Pengadaan Baru" untuk mulai berjualan ke perusahaan B2B.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($products as $p): ?>
                        <?php
                            $feePct = (float)($p['vendor_fee_percent'] ?? get_default_vendor_fee_rate());
                            $calc = calculate_vendor_fee((float)$p['price_regular'], $feePct);
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 align-top">
                                <div class="flex items-start gap-3">
                                    <img src="<?= esc($p['image_url']) ?>" alt="<?= esc($p['name']) ?>" class="w-12 h-12 object-cover rounded-xl border border-slate-200 flex-shrink-0">
                                    <div class="space-y-0.5">
                                        <a href="<?= base_url('product/' . $p['slug']) ?>" target="_blank" class="font-bold text-slate-900 hover:text-blue-700 leading-snug break-words block">
                                            <?= esc($p['name']) ?>
                                        </a>
                                        <div class="flex flex-wrap items-center gap-1.5 text-[11px]">
                                            <span class="font-mono font-bold text-blue-700"><?= esc($p['sku']) ?></span>
                                            <?php if (!empty($p['brand'])): ?>
                                                <span class="text-slate-300">•</span>
                                                <span class="text-slate-500"><?= esc($p['brand']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-top">
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-medium text-[11px] inline-block"><?= esc($p['category_name'] ?? 'Umum') ?></span>
                                <div class="text-[11px] text-slate-500 mt-1">
                                    Stok: <strong class="text-slate-800"><?= number_format($p['stock']) ?> <?= esc($p['uom']) ?></strong> | MOQ: <strong><?= number_format($p['moq']) ?></strong>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-slate-900 text-sm"><?= format_rupiah($p['price_regular']) ?></div>
                                <div class="text-[10px] text-emerald-700 font-bold mt-0.5">Hak Bersih: <?= format_rupiah($calc['net_amount']) ?></div>
                                <div class="text-[10px] text-slate-400">Fee <?= $feePct ?>%: -<?= format_rupiah($calc['fee_amount']) ?></div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="space-y-1">
                                    <div>
                                        <?php if (($p['status'] ?? 'active') === 'active'): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Aktif</span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">Nonaktif</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($p['tkdn_percent'] > 0): ?>
                                        <span class="inline-block px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] font-bold">TKDN <?= (float)$p['tkdn_percent'] ?>%</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="<?= base_url('vendor/products/edit/' . $p['id']) ?>" class="px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold transition border border-blue-200 inline-flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[15px]">edit</span>
                                        <span>Edit</span>
                                    </a>
                                    <?php if (($p['status'] ?? 'active') === 'active'): ?>
                                        <a href="<?= base_url('vendor/products/delete/' . $p['id']) ?>" onclick="return confirm('Nonaktifkan barang ini dari katalog?');" class="p-1.5 rounded-lg hover:bg-rose-50 text-rose-600 transition border border-rose-200" title="Nonaktifkan">
                                            <span class="material-symbols-outlined text-[16px]">visibility_off</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
