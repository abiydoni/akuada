<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('vendor/products') ?>" class="hover:text-blue-600">Daftar Produk Saya</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Edit Produk</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900">Edit Barang: <?= esc($product['name']) ?></h1>
        </div>
    </div>

    <form action="<?= base_url('vendor/products/update/' . $product['id']) ?>" method="POST" class="space-y-6 text-xs">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
            <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">1. Informasi Utama & Spesifikasi Barang</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Barang Pengadaan *</label>
                    <input type="text" name="name" value="<?= esc($product['name']) ?>" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori Pengadaan *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $product['category_id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Merek / Brand Resmi</label>
                    <input type="text" name="brand" value="<?= esc($product['brand'] ?? '') ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode SKU Internal</label>
                    <input type="text" name="sku" value="<?= esc($product['sku']) ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Satuan Jual (UOM) *</label>
                    <input type="text" name="uom" value="<?= esc($product['uom']) ?>" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Tayang</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white">
                        <option value="active" <?= ($product['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Aktif Ditayangkan</option>
                        <option value="inactive" <?= ($product['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Nonaktif / Arsip</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">URL Gambar Produk</label>
                    <input type="url" name="image_url" value="<?= esc($product['image_url'] ?? '') ?>" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Produk B2B</label>
                    <textarea name="description" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300"><?= esc($product['description'] ?? '') ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Spesifikasi Teknis Detail</label>
                    <textarea name="spec_details" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300"><?= esc($product['spec_details'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
            <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">2. Harga Satuan (DPP), Kuantitas MOQ & Pajak</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Harga Satuan Reguler (DPP) *</label>
                    <input type="number" name="price_regular" value="<?= (float)$product['price_regular'] ?>" required min="100" step="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Minimum Order (MOQ) *</label>
                    <input type="number" name="moq" value="<?= (int)$product['moq'] ?>" required min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Stok Tersedia *</label>
                    <input type="number" name="stock" value="<?= (int)$product['stock'] ?>" required min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Lead Time Pengiriman (Hari)</label>
                    <input type="number" name="lead_time_days" value="<?= (int)$product['lead_time_days'] ?>" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kandungan TKDN (%)</label>
                    <input type="number" name="tkdn_percent" value="<?= (float)$product['tkdn_percent'] ?>" min="0" max="100" step="0.1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Komisi Platform Fee (%)</label>
                    <input type="number" name="vendor_fee_percent" value="<?= esc($product['vendor_fee_percent'] ?? $vendorContractFee) ?>" step="0.1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 font-mono text-slate-600">
                </div>
            </div>

            <div class="flex flex-wrap gap-6 pt-2">
                <label class="inline-flex items-center gap-2 font-semibold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_taxable" value="1" <?= !empty($product['is_taxable']) ? 'checked' : '' ?> class="rounded text-blue-600">
                    <span>Barang Kena Pajak (PPN 11%)</span>
                </label>
                <label class="inline-flex items-center gap-2 font-semibold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_sample_available" value="1" <?= !empty($product['is_sample_available']) ? 'checked' : '' ?> class="rounded text-blue-600">
                    <span>Sediakan Permintaan Sampel Produk B2B</span>
                </label>
            </div>
        </div>

        <!-- Tier Pricing -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-bold text-slate-900">3. Harga Grosir Bertingkat (B2B Tier Pricing)</h3>
            <div class="space-y-3">
                <?php for ($i = 0; $i < max(2, count($tiers)); $i++): ?>
                    <?php $t = $tiers[$i] ?? null; ?>
                    <div class="grid grid-cols-3 gap-3">
                        <input type="number" name="tier_min[]" value="<?= esc($t['min_qty'] ?? '') ?>" placeholder="Min. Qty" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
                        <input type="number" name="tier_max[]" value="<?= esc($t['max_qty'] ?? '') ?>" placeholder="Maks. Qty" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
                        <input type="number" name="tier_price[]" value="<?= isset($t['unit_price']) ? (float)$t['unit_price'] : '' ?>" placeholder="Harga Satuan Grosir (Rp)" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="<?= base_url('vendor/products') ?>" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
