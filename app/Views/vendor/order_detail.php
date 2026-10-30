<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<div class="space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('vendor/orders') ?>" class="hover:text-blue-600">Pesanan PO & BAST</a>
                <span>/</span>
                <span class="font-mono text-blue-700 font-bold"><?= esc($order['po_number'] ?? $order['order_number']) ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900">Pemenuhan Pesanan PO & Penerbitan Surat Jalan BAST</h1>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="<?= base_url('vendor/orders/vendor-invoice/' . $order['id']) ?>" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-extrabold text-xs transition flex items-center gap-1.5 shadow-sm border border-emerald-200">
                <span class="material-symbols-outlined text-[18px] text-emerald-700">receipt_long</span>
                <span>Cetak Faktur Tagihan ke AkuAda</span>
            </a>
            <div>
                <?= badge_order_status($order['order_status']) ?>
            </div>
        </div>
    </div>

    <!-- Stepper -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">route</span>
                <h3 class="text-sm font-bold text-slate-900">Progres Pemenuhan Pengadaan (8 Tahap Standar B2B)</h3>
            </div>
        </div>
        <?= render_order_progress_stepper($order['order_status']) ?>
    </div>

    <!-- Buyer Info & Payout Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <span class="text-[10px] font-extrabold text-blue-700 uppercase tracking-widest block">TUJUAN PENGIRIMAN (CORPORATE BUYER):</span>
            <strong class="text-base text-slate-900 block"><?= esc($order['buyer_name']) ?></strong>
            <p class="text-slate-600">NPWP Buyer: <strong class="font-mono"><?= esc($order['buyer_npwp'] ?? '-') ?></strong></p>
            <p class="text-slate-600">Alamat Kirim: <strong><?= esc($order['shipping_address'] ?? $order['buyer_address']) ?></strong></p>
            <p class="text-slate-600">PIC Penerima: <?= esc($order['buyer_pic'] ?? '-') ?> (<?= esc($order['buyer_phone'] ?? '-') ?>)</p>
        </div>

        <div class="bg-gradient-to-br from-emerald-50/70 to-blue-50/60 p-5 rounded-2xl border border-emerald-200 shadow-sm space-y-2">
            <span class="text-[10px] font-extrabold text-emerald-900 uppercase tracking-widest block">RINCIAN SETTLEMENT HAK VENDOR:</span>
            <div class="space-y-1.5 pt-1">
                <div class="flex justify-between">
                    <span class="text-slate-600">Total Penjualan Barang (DPP):</span>
                    <strong class="font-mono text-slate-900"><?= format_rupiah($order['subtotal']) ?></strong>
                </div>
                <div class="flex justify-between text-purple-800">
                    <span>Potongan Fee Platform AkuAda:</span>
                    <strong class="font-mono">- <?= format_rupiah($order['total_vendor_fee'] ?? 0) ?></strong>
                </div>
                <div class="flex justify-between pt-2 border-t border-emerald-200">
                    <span class="font-bold text-emerald-950">Hak Bersih Diterima Vendor (Net Payout):</span>
                    <strong class="text-base font-black font-mono text-emerald-800"><?= format_rupiah($order['vendor_net_payout'] ?? ($order['subtotal'] - ($order['total_vendor_fee'] ?? 0))) ?></strong>
                </div>
                <div class="text-[11px] text-slate-500 pt-1">
                    PPN 11% (<?= format_rupiah($order['ppn_amount']) ?>) dikelola sesuai e-Faktur Pajak B2B.
                </div>
            </div>
        </div>
    </div>

    <!-- Order Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-600">inventory_2</span>
            Daftar Item Barang yang Harus Dikirim
        </h3>
        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Nama Barang & SKU</th>
                        <th class="px-4 py-3 text-center w-28">Kuantitas</th>
                        <th class="px-4 py-3 text-right w-36">Harga Satuan</th>
                        <th class="px-4 py-3 text-right w-44">Fee Platform</th>
                        <th class="px-4 py-3 text-right w-44">Hak Bersih Item</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($order['items'] as $it): ?>
                        <tr>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900"><?= esc($it['product_name']) ?></div>
                                <div class="text-[10px] font-mono text-blue-700 font-bold">SKU: <?= esc($it['sku']) ?></div>
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold text-slate-800">
                                <?= number_format($it['qty']) ?> <?= esc($it['uom']) ?>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono text-slate-700">
                                <?= format_rupiah($it['unit_price']) ?>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono text-purple-700">
                                -<?= format_rupiah($it['vendor_fee_amount']) ?> (<?= (float)$it['vendor_fee_percent'] ?>%)
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-700">
                                <?= format_rupiah($it['vendor_net_amount']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Update Shipping & Status Log -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 text-xs">
        <div class="lg:col-span-7 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-blue-600">history</span>
                Riwayat Aktivitas & Pelacakan PO
            </h3>
            <div class="relative pl-6 space-y-3 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-blue-300">
                <?php if (empty($order['status_logs'])): ?>
                    <p class="text-slate-400 italic">Belum ada catatan aktivitas.</p>
                <?php else: ?>
                    <?php foreach ($order['status_logs'] as $log): ?>
                        <div class="relative flex items-start gap-3">
                            <div class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-white border-2 border-blue-600 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 w-full space-y-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <strong class="font-bold text-slate-900"><?= esc($log['title']) ?></strong>
                                    <span class="text-[10px] text-slate-400 font-mono"><?= date('d M Y, H:i WIB', strtotime($log['created_at'])) ?></span>
                                </div>
                                <?php if (!empty($log['description'])): ?>
                                    <p class="text-slate-600 text-[11px]"><?= esc($log['description']) ?></p>
                                <?php endif; ?>
                                <span class="text-[10px] text-blue-700 font-semibold block">Oleh: <?= esc($log['updated_by']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-blue-200 shadow-sm space-y-4 self-start">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-blue-600">local_shipping</span>
                Update Tahapan Pengiriman & Dokumen
            </h3>

            <form action="<?= base_url('vendor/orders/update-shipping') ?>" method="POST" class="space-y-4">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tahapan Pemrosesan PO:</label>
                    <select name="order_status" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-blue-600 bg-white font-semibold">
                        <option value="po_issued" <?= $order['order_status'] === 'po_issued' ? 'selected' : '' ?>>1. Penerbitan PO</option>
                        <option value="po_approved" <?= $order['order_status'] === 'po_approved' ? 'selected' : '' ?>>2. PO Dikonfirmasi Vendor</option>
                        <option value="in_production" <?= in_array($order['order_status'], ['in_production', 'processing']) ? 'selected' : '' ?>>3. Proses Produksi / Penyiapan Barang</option>
                        <option value="quality_control" <?= $order['order_status'] === 'quality_control' ? 'selected' : '' ?>>4. Pemeriksaan Mutu & Standar (QC)</option>
                        <option value="packaging" <?= $order['order_status'] === 'packaging' ? 'selected' : '' ?>>5. Pengemasan & Paletisasi Master Box</option>
                        <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>6. Dalam Pengiriman (Surat Jalan Diterbitkan)</option>
                        <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>7. Barang Tiba di Lokasi Buyer</option>
                        <option value="bast_signed" <?= $order['order_status'] === 'bast_signed' ? 'selected' : '' ?>>8. Pengesahan BAST</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Surat Jalan / BAST:</label>
                    <input type="text" name="bast_number" value="<?= esc($order['bast_number'] ?? ('BAST/' . date('Ym') . '/' . str_pad($order['id'], 4, '0', STR_PAD_LEFT))) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Seri e-Faktur Pajak PPN 11%:</label>
                    <input type="text" name="tax_invoice_number" value="<?= esc($order['tax_invoice_number'] ?? '') ?>" placeholder="010.002-26.xxxxxxxx" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Catatan Pengiriman / Ekspedisi:</label>
                    <textarea name="status_notes" rows="2" placeholder="Contoh: Barang dikirim via armada logistik internal, estimasi tiba pukul 14:00 WIB." class="w-full px-3 py-2 rounded-xl border border-slate-300"></textarea>
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan Pembaruan Status</span>
                </button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
