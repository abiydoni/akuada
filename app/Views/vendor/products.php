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
        <div class="flex flex-wrap items-center gap-2 self-start sm:self-auto">
            <a href="<?= base_url('vendor/products/template-csv') ?>" class="px-3.5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-300 shadow-xs transition flex items-center gap-1.5" title="Unduh template format CSV">
                <span class="material-symbols-outlined text-[18px] text-emerald-600">file_download</span>
                <span>Unduh Template CSV</span>
            </a>
            <button type="button" onclick="openImportModal()" class="px-3.5 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs border border-emerald-300 shadow-xs transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">upload_file</span>
                <span>Import CSV</span>
            </button>
            <a href="<?= base_url('vendor/products/new') ?>" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add_box</span>
                <span>+ Tambah Barang Baru</span>
            </a>
        </div>
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
                                        <a href="<?= base_url('vendor/products/delete/' . $p['id']) ?>" data-confirm="Nonaktifkan barang ini dari katalog?" class="p-1.5 rounded-lg hover:bg-rose-50 text-rose-600 transition border border-rose-200" title="Nonaktifkan">
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

<!-- Modal Import CSV Barang -->
<div id="importModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden text-xs animate-in fade-in zoom-in-95 duration-150">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-emerald-50 to-blue-50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-2xl">upload_file</span>
                <div>
                    <h3 class="font-extrabold text-slate-900 text-sm">Import Master Barang via CSV</h3>
                    <p class="text-[11px] text-slate-500">Unggah banyak barang sekaligus ke katalog pengadaan B2B Anda.</p>
                </div>
            </div>
            <button type="button" onclick="closeImportModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form action="<?= base_url('vendor/products/import-csv') ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <?= csrf_field() ?>

            <div class="p-3.5 rounded-2xl bg-emerald-50/80 border border-emerald-200 text-emerald-950 space-y-2">
                <div class="font-bold flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-emerald-600 text-[18px]">description</span>
                        Petunjuk Pengisian CSV:
                    </span>
                    <a href="<?= base_url('vendor/products/template-csv') ?>" class="text-[11px] text-emerald-700 hover:text-emerald-900 font-extrabold underline flex items-center gap-0.5">
                        <span class="material-symbols-outlined text-[14px]">download</span> Unduh Contoh Template
                    </a>
                </div>
                <ul class="text-[11px] text-slate-600 space-y-1 list-disc list-inside">
                    <li>Gunakan file berekstensi <strong>.csv</strong> (Mendukung pemisah koma <code>,</code> maupun titik-koma <code>;</code> dari Excel).</li>
                    <li>Kolom wajib: <strong>nama_barang</strong> dan <strong>harga_b2b</strong>.</li>
                    <li>Jika kolom <strong>kategori</strong> tidak cocok persis, sistem otomatis memetakan ke kategori yang paling mendekati.</li>
                    <li>Kolom <strong>sku</strong> akan dibuatkan otomatis bila dikosongkan.</li>
                </ul>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1.5">Pilih File CSV Produk: <span class="text-rose-500">*</span></label>
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-4 text-center hover:border-emerald-500 transition-colors bg-slate-50/50">
                    <input type="file" name="csv_file" accept=".csv,text/csv,text/plain" required id="csvInput"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                    <p class="text-[11px] text-slate-400 mt-2">Maksimal ukuran file: 5 MB.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-50 font-semibold text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">cloud_upload</span>
                    <span>Proses Import Barang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}
</script>

<?= $this->endSection() ?>
