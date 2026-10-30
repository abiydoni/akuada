<?= $this->extend('layouts/main') ?>

<?= $this->section('subcontent') ?>

<!-- Breadcrumbs -->
<div class="bg-slate-100 py-3.5 border-b border-slate-200 text-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center gap-2 text-slate-500">
        <a href="<?= base_url('/') ?>" class="hover:text-purple-600">Beranda</a>
        <span>/</span>
        <a href="<?= base_url('products?cat=' . $product['category_id']) ?>" class="hover:text-purple-600"><?= esc($product['category_name']) ?></a>
        <span>/</span>
        <span class="text-slate-800 font-semibold truncate max-w-sm"><?= esc($product['name']) ?></span>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Product Image & Vendor Card (5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm overflow-hidden">
                <div class="aspect-square bg-slate-50 rounded-xl overflow-hidden relative group">
                    <img id="mainProductImg" src="<?= esc($product['image_url']) ?>" alt="<?= esc($product['name']) ?>" class="w-full h-full object-cover">
                    
                    <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                        <span class="badge-moq shadow">MOQ: <?= $product['moq'] ?> <?= esc($product['uom']) ?></span>
                        <?php if ($product['tkdn_percent'] > 0): ?>
                            <span class="badge-tkdn shadow">Sertifikat TKDN <?= $product['tkdn_percent'] ?>%</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- B2B Action Quick Links -->
                <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-slate-100 text-xs">
                    <?php if ($product['is_sample_available']): ?>
                        <button type="button" onclick="requestSample(<?= $product['id'] ?>)" class="w-full py-2 px-3 rounded-lg border border-purple-300 text-purple-700 hover:bg-purple-50 font-semibold flex items-center justify-center gap-1.5 transition">
                            <span class="material-symbols-outlined text-[16px]">science</span>
                            <span>Minta Sample Unit</span>
                        </button>
                    <?php else: ?>
                        <span class="w-full py-2 px-3 rounded-lg bg-slate-100 text-slate-400 font-medium text-center">Sample Tidak Tersedia</span>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('rfq/create?product_id=' . $product['id']) ?>" class="w-full py-2 px-3 rounded-lg border border-pink-300 text-pink-700 hover:bg-pink-50 font-semibold flex items-center justify-center gap-1.5 transition text-center">
                        <span class="material-symbols-outlined text-[16px]">request_quote</span>
                        <span>Minta Nego / RFQ</span>
                    </a>
                </div>
            </div>

            <!-- Vendor Verification Box -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm space-y-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Pemasok Resmi Terverifikasi</span>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-purple-700 to-blue-600 text-white flex items-center justify-center font-extrabold text-lg shadow-sm">
                        <?= substr($product['vendor_name'], 3, 2) ?>
                    </div>
                    <div>
                        <div class="flex items-center gap-1.5">
                            <h4 class="font-bold text-slate-900 text-sm"><?= esc($product['vendor_name']) ?></h4>
                            <span class="material-symbols-outlined text-blue-600 text-[18px]">verified</span>
                        </div>
                        <p class="text-[11px] text-slate-500">Legalitas: <strong><?= esc($product['vendor_legal_type'] ?? 'PT') ?></strong> | NPWP: <?= esc($product['vendor_npwp'] ?? 'Aktif') ?></p>
                        <p class="text-[11px] text-slate-500">Lokasi: <?= esc($product['vendor_city'] ?? 'Jakarta Timur') ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Column: Product Details & Specs (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-purple-100 text-purple-800">
                        Brand: <?= esc($product['brand']) ?>
                    </span>
                    <span class="text-xs text-slate-400">|</span>
                    <span class="text-xs text-slate-500 font-mono">SKU: <?= esc($product['sku']) ?></span>
                </div>

                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 leading-snug">
                    <?= esc($product['name']) ?>
                </h1>

                <!-- Price and Tax Info -->
                <div class="bg-gradient-to-br from-purple-50 via-indigo-50/40 to-white p-4 rounded-xl border border-purple-100 space-y-1">
                    <span class="text-xs text-slate-500">Harga Satuan Reguler:</span>
                    <div class="text-2xl font-extrabold text-purple-900" id="displayUnitPrice">
                        <?= format_rupiah($product['price_regular']) ?>
                        <span class="text-xs font-normal text-slate-600">/ <?= esc($product['uom']) ?></span>
                    </div>
                    <div class="flex items-center gap-2 pt-1 text-xs text-slate-600">
                        <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span>
                        <span>Faktur Pajak PPN 11% Otomatis Diterbitkan DJP</span>
                    </div>
                </div>
            </div>

            <!-- Volume Tier Pricing Table (Crucial for B2B Wholesale) -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="bg-slate-50/80 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-purple-600 text-[18px]">price_change</span>
                        Tabel Harga Grosir Korporat (Tiering)
                    </span>
                    <span class="text-[10px] bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full font-bold">Diskon Volume B2B</span>
                </div>
                <table class="w-full text-xs text-left border-collapse">
                    <thead class="text-[10px] text-slate-500 bg-slate-50/50 uppercase font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-2.5">Jumlah Pesanan</th>
                            <th class="px-4 py-2.5 text-right font-mono">Harga Satuan (DPP)</th>
                            <th class="px-4 py-2.5 text-right">Potongan Hemat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <!-- Regular Base Tier -->
                        <tr class="tier-row hover:bg-purple-50/40 transition-colors" data-min="<?= $product['moq'] ?>" data-max="9" data-price="<?= $product['price_regular'] ?>">
                            <td class="px-4 py-2.5 font-medium text-slate-700"><?= $product['moq'] ?> - <?= !empty($product['price_tiers']) ? ($product['price_tiers'][0]['min_qty'] - 1) : '10' ?> <?= esc($product['uom']) ?></td>
                            <td class="px-4 py-2.5 text-right font-mono font-bold text-slate-900"><?= format_rupiah($product['price_regular']) ?></td>
                            <td class="px-4 py-2.5 text-right text-slate-400 font-medium">Harga Dasar</td>
                        </tr>
                        <?php if (!empty($product['price_tiers'])): ?>
                            <?php foreach ($product['price_tiers'] as $tier): ?>
                                <tr class="tier-row hover:bg-purple-50/40 transition-colors" data-min="<?= $tier['min_qty'] ?>" data-max="<?= $tier['max_qty'] ?? 999999 ?>" data-price="<?= $tier['unit_price'] ?>">
                                    <td class="px-4 py-2.5 font-medium text-slate-700">
                                        <?= $tier['min_qty'] ?><?= $tier['max_qty'] ? ' - ' . $tier['max_qty'] : '+' ?> <?= esc($product['uom']) ?>
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono font-extrabold text-purple-700">
                                        <?= format_rupiah($tier['unit_price']) ?>
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-bold text-emerald-600">
                                        Hemat <?= round((($product['price_regular'] - $tier['unit_price']) / $product['price_regular']) * 100) ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Specifications & Description -->
            <div class="space-y-4 pt-2">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 mb-2">Deskripsi Kebutuhan Operasional</h3>
                    <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line"><?= esc($product['description']) ?></p>
                </div>

                <div class="border-t border-slate-100 pt-4">
                    <h3 class="text-sm font-bold text-slate-900 mb-2">Spesifikasi Teknis B2B</h3>
                    <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 text-xs text-slate-700 font-mono space-y-1 whitespace-pre-line">
                        <?= esc($product['spec_details']) ?>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2 text-xs">
                    <div class="p-3 rounded-lg border border-slate-200 bg-white">
                        <span class="text-slate-400 text-[11px] block">Waktu Proses / Lead Time</span>
                        <strong class="text-slate-800 text-sm"><?= $product['lead_time_days'] ?> Hari Kerja</strong>
                    </div>
                    <div class="p-3 rounded-lg border border-slate-200 bg-white">
                        <span class="text-slate-400 text-[11px] block">Stok Tersedia</span>
                        <strong class="text-slate-800 text-sm"><?= number_format($product['stock']) ?> <?= esc($product['uom']) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Interactive Purchase Card & Tax Calculator (3 Cols) -->
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl p-5 border border-purple-200 shadow-xl shadow-purple-900/5 sticky top-24 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700">Kalkulator Pengadaan PO</h3>
                    <span class="badge-top text-[10px]">TOP Support</span>
                </div>

                <form action="<?= base_url('cart/add') ?>" method="POST" id="purchaseForm" class="space-y-4">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                    <!-- Quantity Input with MOQ Enforced -->
                    <div>
                        <div class="flex items-center justify-between text-xs mb-1.5">
                            <label for="qtyInput" class="font-bold text-slate-700">Jumlah Pesanan:</label>
                            <span class="text-[11px] text-pink-600 font-semibold">Min. <?= $product['moq'] ?> <?= esc($product['uom']) ?></span>
                        </div>
                        <div class="flex items-center border border-slate-300 rounded-xl overflow-hidden focus-within:border-purple-600 focus-within:ring-2 focus-within:ring-purple-100">
                            <button type="button" onclick="adjustQty(-1)" class="px-3.5 py-2.5 bg-slate-100 text-slate-600 hover:bg-slate-200 transition font-bold text-sm">-</button>
                            <input type="number" id="qtyInput" name="qty" value="<?= $product['moq'] ?>" min="<?= $product['moq'] ?>" max="<?= $product['stock'] ?>" 
                                   class="w-full text-center text-sm font-bold text-slate-900 border-none focus:ring-0 py-2" oninput="calculatePrice()">
                            <button type="button" onclick="adjustQty(1)" class="px-3.5 py-2.5 bg-slate-100 text-slate-600 hover:bg-slate-200 transition font-bold text-sm">+</button>
                        </div>
                    </div>

                    <!-- Live Calculation Breakdown -->
                    <div class="bg-slate-50 p-3.5 rounded-xl space-y-2 text-xs border border-slate-200/80">
                        <div class="flex justify-between text-slate-500">
                            <span>Harga Satuan Terpilih:</span>
                            <span class="font-bold text-slate-800" id="calcUnitRate">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal Barang:</span>
                            <span class="font-bold text-slate-800" id="calcSubtotal">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>PPN 11% (e-Faktur):</span>
                            <span class="font-bold text-blue-700" id="calcPpn">Rp 0</span>
                        </div>
                        <div class="border-t border-slate-200 pt-2 flex justify-between items-baseline font-bold">
                            <span class="text-slate-800">Total Nilai PO:</span>
                            <span class="text-base text-purple-900 font-extrabold" id="calcGrandTotal">Rp 0</span>
                        </div>
                    </div>

                    <!-- Payment Term Options Preview -->
                    <div class="text-[11px] text-slate-500 space-y-1">
                        <p class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-purple-600">verified</span>
                            <span>Bisa bayar tempo <strong>TOP 14/30/60/90 hari</strong></span>
                        </p>
                        <p class="flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px] text-blue-600">local_shipping</span>
                            <span>Gratis pengiriman korporat Jabodetabek</span>
                        </p>
                    </div>

                    <!-- Add to PO Cart Button -->
                    <button type="submit" class="w-full btn-akuada-primary py-3 text-xs">
                        <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
                        <span>Tambah ke Keranjang PO</span>
                    </button>
                </form>

                <div class="border-t border-slate-100 pt-3 text-center">
                    <span class="text-[11px] text-slate-400">Plafon kredit korporat Anda aman terproteksi.</span>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JavaScript for Dynamic Tier Pricing and Sample Request -->
<script>
    const basePrice = <?= (float)$product['price_regular'] ?>;
    const moq = <?= (int)$product['moq'] ?>;
    const isTaxable = <?= $product['is_taxable'] ? 'true' : 'false' ?>;
    const ppnRate = <?= (float)$product['ppn_percent'] ?> / 100;

    const tiers = <?= json_encode($product['price_tiers'] ?? []) ?>;

    function formatRupiahJs(number) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
    }

    function adjustQty(amount) {
        const input = document.getElementById('qtyInput');
        let current = parseInt(input.value) || moq;
        let next = current + amount;
        if (next < moq) next = moq;
        input.value = next;
        calculatePrice();
    }

    function calculatePrice() {
        const input = document.getElementById('qtyInput');
        let qty = parseInt(input.value) || moq;
        if (qty < moq) qty = moq;

        // Find tier price
        let appliedPrice = basePrice;
        if (tiers && tiers.length > 0) {
            for (let i = 0; i < tiers.length; i++) {
                let min = parseInt(tiers[i].min_qty);
                let max = tiers[i].max_qty ? parseInt(tiers[i].max_qty) : Infinity;
                if (qty >= min && qty <= max) {
                    appliedPrice = parseFloat(tiers[i].unit_price);
                    break;
                }
            }
        }

        // Highlight active table row
        document.querySelectorAll('.tier-row').forEach(row => {
            let rMin = parseInt(row.getAttribute('data-min'));
            let rMax = parseInt(row.getAttribute('data-max'));
            if (qty >= rMin && qty <= rMax) {
                row.classList.add('tier-row-active');
            } else {
                row.classList.remove('tier-row-active');
            }
        });

        const subtotal = qty * appliedPrice;
        const ppn = isTaxable ? (subtotal * ppnRate) : 0;
        const grandTotal = subtotal + ppn;

        document.getElementById('displayUnitPrice').innerHTML = formatRupiahJs(appliedPrice) + ' <span class="text-xs font-normal text-slate-600">/ <?= esc($product['uom']) ?></span>';
        document.getElementById('calcUnitRate').textContent = formatRupiahJs(appliedPrice);
        document.getElementById('calcSubtotal').textContent = formatRupiahJs(subtotal);
        document.getElementById('calcPpn').textContent = formatRupiahJs(ppn);
        document.getElementById('calcGrandTotal').textContent = formatRupiahJs(grandTotal);
    }

    function requestSample(productId) {
        AkuAdaAlert.confirm(
            'Ajukan Permintaan Sample Unit?',
            'Tim pengadaan vendor akan memverifikasi alamat kantor Anda dan mengirimkan unit sample untuk keperluan evaluasi tender internal.',
            function() {
                fetch('<?= base_url('product/sample/') ?>/' + productId, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.json())
                .then(data => {
                    AkuAdaAlert.success('Permintaan Dikirim!', data.message);
                })
                .catch(err => {
                    AkuAdaAlert.success('Permintaan Dikirim!', 'Sample unit sedang disiapkan untuk pengiriman ke kantor Anda.');
                });
            }
        );
    }

    document.addEventListener('DOMContentLoaded', calculatePrice);
</script>

<?= $this->endSection() ?>
