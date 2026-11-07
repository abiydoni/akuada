<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold uppercase tracking-wider">Katalog Terpusat</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Pengawasan Master Barang Pengadaan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh item barang yang didaftarkan oleh mitra vendor di platform AkuAda.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
            <a href="<?= base_url('admin/products/template-csv') ?>" class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs transition flex items-center gap-1.5" title="Unduh format template Excel resmi (.xlsx) dengan sheet panduan">
                <span class="material-symbols-outlined text-[18px] text-emerald-600">table_view</span>
                <span>Template Excel (.xlsx)</span>
            </a>
            <button type="button" onclick="openImportAdminModal()" class="px-3.5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
                <span>Import Excel / CSV</span>
            </button>
            <span class="px-3 py-2 rounded-xl bg-purple-50 text-purple-700 text-xs font-bold border border-purple-200">
                Total: <?= count($products) ?> SKU
            </span>
        </div>
    </div>

    <!-- Products Table Card - Fits 100% width with NO horizontal scroll -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                <h3 class="font-bold text-slate-800 text-sm">Daftar Katalog Produk Marketplace</h3>
            </div>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Barang & Spesifikasi SKU</th>
                        <th class="px-4 py-3.5">Vendor Pemasok & Kategori</th>
                        <th class="px-4 py-3.5 text-right w-48">Harga & Fee Platform</th>
                        <th class="px-4 py-3.5 text-center w-36">Pajak & Status</th>
                        <th class="px-4 py-3.5 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">inventory_2</span>
                                    <p class="font-semibold text-slate-600">Belum ada data barang di katalog</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Barang yang diunggah oleh mitra vendor akan tampil di sini.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($products as $p): ?>
                        <?php 
                            $feePct = (float)($p['vendor_fee_percent'] ?? get_default_vendor_fee_rate());
                            $feePerUnit = round((float)$p['price_regular'] * ($feePct / 100));
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Product Name & SKU (Full text, no cut-off) -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="flex items-start gap-3">
                                    <img src="<?= esc($p['image_url']) ?>" 
                                         alt="<?= esc($p['name']) ?>" 
                                         class="w-12 h-12 object-cover rounded-xl border border-slate-200 shadow-sm flex-shrink-0 mt-0.5">
                                    <div class="space-y-0.5">
                                        <a href="<?= base_url('product/' . $p['slug']) ?>" target="_blank" 
                                           class="font-bold text-slate-900 hover:text-purple-700 leading-snug break-words transition block" 
                                           title="<?= esc($p['name']) ?>">
                                            <?= esc($p['name']) ?>
                                        </a>
                                        <div class="flex flex-wrap items-center gap-1.5 pt-0.5">
                                            <span class="text-[11px] font-mono font-bold text-purple-700"><?= esc($p['sku']) ?></span>
                                            <?php if (!empty($p['brand'])): ?>
                                                <span class="text-slate-300">•</span>
                                                <span class="text-[11px] text-slate-500 font-medium"><?= esc($p['brand']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Vendor & Category (No cut-off) -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-semibold text-slate-800 leading-snug break-words"><?= esc($p['vendor_name'] ?? 'Vendor') ?></div>
                                <div class="mt-1">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-medium text-[11px] inline-block">
                                        <?= esc($p['category_name'] ?? 'Umum') ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Price & Fee -->
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-slate-900 text-sm"><?= format_rupiah($p['price_regular']) ?></div>
                                <div class="text-[10px] text-slate-500 mt-0.5">MOQ: <?= number_format($p['moq']) ?> <?= esc($p['uom']) ?></div>
                                <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 font-bold text-[10px] border border-emerald-200 mt-1">
                                    Fee <?= $feePct ?>%: <?= format_rupiah($feePerUnit) ?>
                                </div>
                            </td>

                            <!-- Taxes & Status -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-center gap-1">
                                        <span class="badge-ppn text-[10px]">PPN <?= esc(format_tax_rate()) ?></span>
                                        <?php if ($p['tkdn_percent'] > 0): ?>
                                            <span class="badge-tkdn text-[10px]"><?= $p['tkdn_percent'] ?>%</span>
                                        <?php endif; ?>
                                    </div>

                                    <div>
                                        <?php if (($p['status'] ?? 'active') === 'active'): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> Nonaktif
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <!-- Action -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="inline-flex items-center gap-1">
                                    <a href="<?= base_url('admin/products/toggle/' . $p['id']) ?>" 
                                       data-confirm="Ubah status publikasi produk ini?"
                                       class="p-1.5 rounded-lg hover:bg-purple-50 text-slate-600 hover:text-purple-700 transition border border-slate-200" 
                                       title="Toggle Status Aktif">
                                        <span class="material-symbols-outlined text-[17px]"><?= ($p['status'] ?? 'active') === 'active' ? 'visibility_off' : 'visibility' ?></span>
                                    </a>
                                    <a href="<?= base_url('admin/products/delete/' . $p['id']) ?>" 
                                       data-confirm="Hapus barang ini dari marketplace?"
                                       class="p-1.5 rounded-lg hover:bg-rose-50 text-rose-600 transition border border-rose-200" 
                                       title="Hapus Barang">
                                        <span class="material-symbols-outlined text-[17px]">delete</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Import CSV Superadmin -->
<div id="modalImportAdminCsv" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden text-xs animate-in fade-in zoom-in-95 duration-150">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-purple-50 via-indigo-50 to-emerald-50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-xl">upload_file</span>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Import Massal Master Barang (Superadmin)</h3>
                    <p class="text-[11px] text-slate-500">Unggah file .csv sesuai kolom tabel database produk</p>
                </div>
            </div>
            <button type="button" onclick="closeImportAdminModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/products/import-csv') ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <?= csrf_field() ?>

            <div class="p-3.5 rounded-2xl bg-emerald-50/70 border border-emerald-200 text-emerald-950 text-[11px] space-y-2">
                <div class="flex items-center justify-between">
                    <strong class="font-bold flex items-center gap-1 text-emerald-900">
                        <span class="material-symbols-outlined text-[16px]">info</span> Petunjuk Template Excel & Angka:
                    </strong>
                    <a href="<?= base_url('admin/products/template-csv') ?>" class="text-[11px] text-emerald-700 hover:text-emerald-900 font-extrabold underline flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[15px]">download</span> Unduh Template Excel (.xlsx)
                    </a>
                </div>
                <ul class="text-slate-600 space-y-1 list-disc list-inside">
                    <li>Gunakan file format <strong>Excel (.xlsx)</strong> atau <strong>CSV</strong>. Pada file Excel telah tersedia Sheet 2 <em>"Panduan & Kode Angka"</em>.</li>
                    <li><strong>Mudah Diisi:</strong> Kolom Kategori & Satuan UOM cukup diisi angka (misal Kategori: 1, Satuan: 1=Pcs, 2=Box, 3=Unit).</li>
                    <li><strong>Otomatis:</strong> Kolom SKU & Barcode dapat dikosongkan agar digenerate otomatis oleh sistem.</li>
                </ul>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Pilih File Excel / CSV (.xlsx, .csv): <span class="text-rose-500">*</span></label>
                <div class="p-4 rounded-2xl border-2 border-dashed border-slate-300 hover:border-purple-400 transition bg-slate-50 text-center space-y-2">
                    <span class="material-symbols-outlined text-3xl text-slate-400 block mx-auto">cloud_upload</span>
                    <input type="file" name="csv_file" accept=".xlsx,.csv,.txt" required
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                    <p class="text-[10px] text-slate-400">Mendukung file Excel (.xlsx) atau CSV (UTF-8), ukuran maks. 10MB</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" onclick="closeImportAdminModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold">
                    Batal
                </button>
                <button type="submit" class="btn-akuada-primary px-5 py-2 text-xs flex items-center gap-1.5 shadow-md">
                    <span class="material-symbols-outlined text-[17px]">publish</span>
                    <span>Mulai Proses Import</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openImportAdminModal() {
    document.getElementById('modalImportAdminCsv').classList.remove('hidden');
}
function closeImportAdminModal() {
    document.getElementById('modalImportAdminCsv').classList.add('hidden');
}
</script>

<?= $this->endSection() ?>
