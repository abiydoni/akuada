<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('vendor/products') ?>" class="hover:text-blue-600">Daftar Produk Saya</a>
                <span>/</span>
                <span class="text-slate-800 font-semibold">Tambah Barang Baru</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900">Tambah Barang Pengadaan B2B Baru</h1>
        </div>
    </div>

    <form action="<?= base_url('vendor/products/store') ?>" method="POST" class="space-y-6 text-xs">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
            <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">1. Informasi Utama & Spesifikasi Barang</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap Barang Pengadaan *</label>
                    <input type="text" name="name" required placeholder="Contoh: Kertas HVS A4 80gsm PaperOne (1 Box = 5 Rim)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kategori Pengadaan *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white">
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= esc($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Merek / Brand Resmi</label>
                    <input type="text" name="brand" placeholder="Contoh: PaperOne, Epson, 3M" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kode SKU Internal</label>
                    <input type="text" name="sku" placeholder="Otomatis jika dikosongkan" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Satuan Jual (UOM) *</label>
                    <input type="text" name="uom" value="Box" required placeholder="Box / Unit / Pcs / Karton / Lot" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">URL Gambar Produk</label>
                    <input type="url" name="image_url" placeholder="https://images.unsplash.com/..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Produk B2B</label>
                    <textarea name="description" rows="3" placeholder="Jelaskan kegunaan, garansi resmi, dan kelengkapan dokumen pengadaan..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300"></textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Spesifikasi Teknis Detail</label>
                    <textarea name="spec_details" rows="2" placeholder="Dimensi, daya, standar SNI/ISO..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300"></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-5">
            <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">2. Harga Satuan (DPP), Kuantitas MOQ & Pajak</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Harga Satuan Reguler (DPP Sebelum PPN) *</label>
                    <input type="number" name="price_regular" required min="100" step="1" placeholder="250000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Minimum Order (MOQ) *</label>
                    <input type="number" name="moq" value="1" required min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Stok Tersedia *</label>
                    <input type="number" name="stock" value="100" required min="0" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Lead Time Pengiriman (Hari)</label>
                    <input type="number" name="lead_time_days" value="2" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kandungan TKDN (%)</label>
                    <input type="number" name="tkdn_percent" value="0" min="0" max="100" step="0.1" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Komisi Platform Fee (%)</label>
                    <input type="number" name="vendor_fee_percent" value="<?= esc($vendorContractFee) ?>" step="0.1" readonly class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 bg-slate-50 font-mono text-slate-600">
                </div>
            </div>

            <div class="flex flex-wrap gap-6 pt-2">
                <label class="inline-flex items-center gap-2 font-semibold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_taxable" value="1" checked class="rounded text-blue-600">
                    <span>Barang Kena Pajak (Terbitkan e-Faktur PPN 11%)</span>
                </label>
                <label class="inline-flex items-center gap-2 font-semibold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="is_sample_available" value="1" checked class="rounded text-blue-600">
                    <span>Sediakan Permintaan Sampel Produk B2B</span>
                </label>
            </div>
        </div>

        <!-- Tier Pricing -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
            <div>
                <h3 class="text-sm font-bold text-slate-900">3. Harga Grosir Bertingkat (B2B Volume Tier Pricing)</h3>
                <p class="text-[11px] text-slate-500">Opsional: Berikan harga satuan khusus untuk pembelian partai besar oleh perusahaan.</p>
            </div>

            <div class="space-y-3">
                <?php for ($i = 0; $i < 2; $i++): ?>
                    <div class="grid grid-cols-3 gap-3">
                        <input type="number" name="tier_min[]" placeholder="Min. Qty (misal: <?= ($i + 1) * 10 ?>)" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
                        <input type="number" name="tier_max[]" placeholder="Maks. Qty (kosong = tanpa batas)" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
                        <input type="number" name="tier_price[]" placeholder="Harga Satuan Grosir (Rp)" class="px-3 py-2 rounded-xl border border-slate-300 font-mono">
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="<?= base_url('vendor/products') ?>" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold shadow-md flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">publish</span>
                <span>Simpan & Tayangkan di Katalog B2B</span>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
