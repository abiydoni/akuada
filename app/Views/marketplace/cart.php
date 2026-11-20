<?= $this->extend('layouts/main') ?>

<?= $this->section('subcontent') ?>

<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
            <span class="material-symbols-outlined text-purple-600 text-3xl">shopping_cart</span>
            Keranjang Pengadaan Purchase Order (PO)
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Periksa kuantitas kebutuhan operasional, diskon tiering volume, dan kalkulasi PPN <?= esc(format_tax_rate()) ?> sebelum menerbitkan PO.
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <?php if (empty($cart)): ?>
        <div class="bg-white rounded-2xl p-16 text-center border border-slate-200 shadow-sm max-w-xl mx-auto space-y-4">
            <div class="w-20 h-20 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mx-auto">
                <span class="material-symbols-outlined text-4xl">remove_shopping_cart</span>
            </div>
            <h2 class="text-lg font-bold text-slate-800">Keranjang Pengadaan Masih Kosong</h2>
            <p class="text-xs text-slate-500 leading-relaxed">
                Anda belum menambahkan kebutuhan operasional kantor ke keranjang PO. Jelajahi katalog B2B kami untuk menemukan kebutuhan perusahaan Anda.
            </p>
            <a href="<?= base_url('products') ?>" class="btn-akuada-primary text-xs px-6 py-2.5">
                Mulai Belanja Kebutuhan Kantor &rarr;
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Items Table (8 Cols) -->
            <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <input type="checkbox" id="selectAllCart" checked onchange="toggleSelectAllCart(this)"
                               class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500 border-slate-300 cursor-pointer">
                        <label for="selectAllCart" class="text-xs font-bold text-slate-700 cursor-pointer select-none">
                            Pilih Semua Barang (<span id="selectedCountDisplay"><?= count($cart) ?></span>/<?= count($cart) ?>)
                        </label>
                    </div>
                    <span class="text-[11px] text-pink-600 font-semibold">*Harga otomatis menyesuaikan tier volume</span>
                </div>

                <div class="divide-y divide-slate-100">
                    <?php foreach ($cart as $productId => $item): ?>
                        <div class="p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 cart-row" data-subtotal="<?= $item['subtotal'] ?>" data-product-id="<?= $productId ?>">
                            <!-- Checkbox & Product Info -->
                            <div class="flex items-start gap-3.5 flex-1">
                                <input type="checkbox" name="selected_items[]" value="<?= $productId ?>" checked
                                       class="cart-item-checkbox mt-2 w-4 h-4 rounded text-purple-600 focus:ring-purple-500 border-slate-300 cursor-pointer"
                                       onchange="updateCartSelection()">
                                <img src="<?= esc($item['image_url']) ?>" alt="<?= esc($item['name']) ?>" class="w-20 h-20 object-cover rounded-xl border border-slate-200 flex-shrink-0">
                                <div class="space-y-1">
                                    <span class="text-[10px] font-mono text-purple-700 font-bold">SKU: <?= esc($item['sku']) ?></span>
                                    <h3 class="text-sm font-bold text-slate-900 leading-snug">
                                        <?= esc($item['name']) ?>
                                    </h3>
                                    <div class="flex items-center gap-2 text-xs text-slate-500">
                                        <span>Harga Satuan: <strong><?= format_rupiah($item['unit_price']) ?></strong> / <?= esc($item['uom']) ?></span>
                                        <span>•</span>
                                        <span class="badge-moq text-[10px]">Min. <?= $item['moq'] ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Qty Modifier & Subtotal -->
                            <div class="flex sm:flex-col items-end justify-between w-full sm:w-auto gap-3">
                                <form action="<?= base_url('cart/update') ?>" method="POST" class="flex items-center border border-slate-300 rounded-lg overflow-hidden">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= $productId ?>">
                                    <input type="number" name="qty" value="<?= $item['qty'] ?>" min="<?= $item['moq'] ?>" 
                                           class="w-16 text-center text-xs font-bold text-slate-800 border-none focus:ring-0 py-1" onchange="this.form.submit()">
                                    <button type="submit" class="px-2 py-1 bg-slate-100 text-slate-600 hover:bg-slate-200 text-xs font-semibold" title="Perbarui Qty">
                                        <span class="material-symbols-outlined text-[16px]">sync</span>
                                    </button>
                                </form>

                                <div class="text-right">
                                    <span class="text-[10px] text-slate-400 block">Subtotal:</span>
                                    <span class="text-sm font-extrabold text-purple-900"><?= format_rupiah($item['subtotal']) ?></span>
                                </div>

                                <a href="<?= base_url('cart/remove/' . $productId) ?>" class="text-slate-400 hover:text-rose-600 transition" title="Hapus dari Keranjang">
                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
                    <a href="<?= base_url('products') ?>" class="text-purple-700 font-bold hover:underline flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span> Lanjut Tambah Barang
                    </a>
                </div>
            </div>

            <!-- Right Summary Box (4 Cols) -->
            <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-purple-200 shadow-xl shadow-purple-900/5 space-y-4 sticky top-24">
                <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">Ringkasan Pengadaan PO</h3>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Total Nilai Barang (Subtotal):</span>
                        <span class="font-bold text-slate-800"><?= format_rupiah($subtotal) ?></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>PPN <?= esc(format_tax_rate()) ?> Resmi (e-Faktur):</span>
                        <span class="font-bold text-blue-700"><?= format_rupiah($ppnTotal) ?></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Biaya Pengiriman Logistik:</span>
                        <span class="font-bold text-emerald-600"><?= ($shippingAmount ?? 0) > 0 ? format_rupiah($shippingAmount) : 'GRATIS (' . esc(get_system_setting('free_shipping_region', 'Corporate Jabodetabek')) . ')' ?></span>
                    </div>
                    <div class="border-t border-slate-200 pt-3 flex justify-between items-baseline font-bold">
                        <span class="text-sm text-slate-900">Total Pembayaran PO:</span>
                        <span class="text-xl text-purple-900 font-extrabold"><?= format_rupiah($grandTotal) ?></span>
                    </div>
                </div>

                <div class="p-3 bg-purple-50 rounded-xl border border-purple-100 text-[11px] text-purple-900 space-y-1">
                    <p class="font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px] text-purple-600">event_repeat</span>
                        Fasilitas Term of Payment (TOP)
                    </p>
                    <p class="text-purple-700">Dapat dibayar tempo <?= get_min_top_days() ?>-<?= get_max_top_days() ?> hari kalender setelah Berita Acara Serah Terima (BAST) diterbitkan.</p>
                </div>

                <!-- Tombol Penerbitan PO / Checkout -->
                <a href="<?= base_url('checkout') ?>" class="w-full btn-akuada-primary py-3 text-sm flex items-center justify-center gap-2 shadow-md">
                    <span class="material-symbols-outlined text-[20px]">assignment_turned_in</span>
                    <span>Lanjut ke Penerbitan PO & Checkout</span>
                </a>

                <!-- Tombol Unduh Surat Penawaran Resmi (Quotation PDF) -->
                <div class="pt-2 border-t border-slate-100 space-y-1.5">
                    <button type="button" onclick="downloadSelectedQuotation()" 
                            class="w-full py-2.5 px-4 rounded-xl border-2 border-purple-600 bg-white hover:bg-purple-50 text-purple-800 font-bold text-xs flex items-center justify-center gap-2 shadow-xs transition">
                        <span class="material-symbols-outlined text-purple-700 text-[18px]">request_quote</span>
                        <span>📄 Unduh Penawaran Resmi (Quotation)</span>
                    </button>
                    <p class="text-[10px] text-slate-400 text-center leading-relaxed">
                        Cetak surat penawaran harga resmi ber-KOP & bertandatangan untuk approval internal perusahaan Anda.
                    </p>
                </div>

                <!-- Hidden Form for Quotation Generation -->
                <form id="quotationForm" action="<?= base_url('cart/quotation') ?>" method="POST" target="_blank" class="hidden">
                    <?= csrf_field() ?>
                    <div id="quotationHiddenInputs"></div>
                </form>
            </div>

        </div>
    <?php endif; ?>
</div>

<script>
function toggleSelectAllCart(masterCb) {
    const itemCbs = document.querySelectorAll('.cart-item-checkbox');
    itemCbs.forEach(cb => {
        cb.checked = masterCb.checked;
    });
    updateCartSelection();
}

function updateCartSelection() {
    const itemCbs = document.querySelectorAll('.cart-item-checkbox');
    const checkedCbs = document.querySelectorAll('.cart-item-checkbox:checked');
    const masterCb = document.getElementById('selectAllCart');
    const countDisplay = document.getElementById('selectedCountDisplay');

    if (countDisplay) {
        countDisplay.textContent = checkedCbs.length;
    }

    if (masterCb) {
        masterCb.checked = (checkedCbs.length === itemCbs.length && itemCbs.length > 0);
        masterCb.indeterminate = (checkedCbs.length > 0 && checkedCbs.length < itemCbs.length);
    }
}

function downloadSelectedQuotation() {
    const checkedCbs = document.querySelectorAll('.cart-item-checkbox:checked');
    const form = document.getElementById('quotationForm');
    const container = document.getElementById('quotationHiddenInputs');

    if (!form || !container) return;
    container.innerHTML = '';

    if (checkedCbs.length === 0) {
        alert('Silakan pilih minimal 1 barang di keranjang untuk membuat Surat Penawaran Resmi.');
        return;
    }

    checkedCbs.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_items[]';
        input.value = cb.value;
        container.appendChild(input);
    });

    form.submit();
}
</script>

<?= $this->endSection() ?>
