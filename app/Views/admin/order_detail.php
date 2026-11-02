<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('admin/transactions') ?>" class="hover:text-purple-600">Manajemen PO & Transaksi</a>
                <span>/</span>
                <span class="font-mono text-purple-700 font-bold"><?= esc($order['po_number'] ?? $order['order_number']) ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900">Audit PO & Detail Eksekusi Transaksi B2B</h1>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="<?= base_url('admin/orders/invoice-customer/' . $order['id']) ?>" target="_blank" class="px-3.5 py-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-800 font-bold text-xs transition flex items-center gap-1.5 border border-purple-200">
                <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                <span>Faktur Pelanggan (AkuAda)</span>
            </a>
            <a href="<?= base_url('admin/orders/invoice-vendor/' . $order['id']) ?>" target="_blank" class="px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold text-xs transition flex items-center gap-1.5 border border-blue-200">
                <span class="material-symbols-outlined text-[16px]">account_balance_wallet</span>
                <span>Faktur Penagihan Vendor</span>
            </a>
            <div>
                <?= badge_order_status($order['order_status']) ?>
            </div>
        </div>
    </div>

    <!-- Stepper Pipeline View -->
    <?php if (str_contains((string)($order['customer_notes'] ?? ''), '[OVER-LIMIT REQUEST:')): ?>
        <div class="bg-amber-50 border-2 border-amber-400 rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-start gap-3 text-xs">
                <span class="material-symbols-outlined text-amber-600 text-2xl mt-0.5">warning</span>
                <div class="space-y-1">
                    <span class="px-2 py-0.5 rounded bg-amber-500 text-white font-extrabold text-[10px] uppercase">Menunggu Persetujuan Over-Limit Plafon TOP</span>
                    <h3 class="text-sm font-black text-amber-950">Buyer Menerbitkan PO Melebihi Sisa Plafon Kredit Reguler</h3>
                    <p class="text-amber-900"><?= esc($order['customer_notes']) ?></p>
                </div>
            </div>
            <form action="<?= base_url('admin/orders/approve-overlimit') ?>" method="POST" class="flex-shrink-0">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow flex items-center gap-1.5 transition">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Setujui Over-Limit & Naikkan Plafon Buyer</span>
                </button>
            </form>
        </div>
    <?php endif; ?>
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">route</span>
                <h3 class="text-sm font-bold text-slate-900">Progres 8 Tahap Pengadaan B2B (Live Stepper)</h3>
            </div>
            <span class="text-xs text-purple-700 font-bold">Status: <?= strtoupper($order['order_status']) ?></span>
        </div>

        <?= render_order_progress_stepper($order['order_status']) ?>
    </div>

    <!-- 2 Column Overview: Info Perusahaan & Finansial -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
        
        <!-- Buyer Profile -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <span class="text-[10px] font-extrabold text-purple-700 uppercase tracking-widest block">PERUSAHAAN PEMBELI (BUYER):</span>
            <strong class="text-base text-slate-900 block"><?= esc($order['buyer_name']) ?></strong>
            <p class="text-slate-500 font-mono">NPWP: <?= esc($order['buyer_npwp'] ?? '-') ?></p>
            <p class="text-slate-600">Alamat: <?= esc($order['shipping_address'] ?? $order['buyer_address']) ?></p>
            <p class="text-slate-500">PIC: <?= esc($order['buyer_pic'] ?? '-') ?> (<?= esc($order['buyer_phone'] ?? '-') ?>)</p>
        </div>

        <!-- Vendor Profile -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <span class="text-[10px] font-extrabold text-blue-700 uppercase tracking-widest block">MITRA PENYEDIA (VENDOR):</span>
            <strong class="text-base text-slate-900 block"><?= esc($order['vendor_name']) ?></strong>
            <p class="text-slate-500 font-mono">NPWP: <?= esc($order['vendor_npwp'] ?? '-') ?></p>
            <p class="text-slate-600">Kota: <?= esc($order['vendor_city'] ?? '-') ?></p>
            <p class="text-slate-500">PIC: <?= esc($order['vendor_pic'] ?? '-') ?> (<?= esc($order['vendor_phone'] ?? '-') ?>)</p>
            <p class="text-emerald-700 font-semibold font-mono">Rek: <?= esc($order['vendor_bank_name'] ?? 'BCA') ?> - <?= esc($order['vendor_bank_account_number'] ?? '-') ?></p>
        </div>

        <!-- Financial Summary -->
        <div class="bg-gradient-to-br from-purple-50/60 to-blue-50/60 p-5 rounded-2xl border border-purple-200 shadow-sm space-y-2">
            <span class="text-[10px] font-extrabold text-purple-900 uppercase tracking-widest block">RINGKASAN FINANSIAL & FEE:</span>
            <div class="space-y-1.5 pt-1">
                <div class="flex justify-between">
                    <span class="text-slate-600">Subtotal DPP:</span>
                    <strong class="text-slate-900"><?= format_rupiah($order['subtotal']) ?></strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600">PPN <?= esc(format_tax_rate()) ?>:</span>
                    <strong class="text-blue-700">+ <?= format_rupiah($order['ppn_amount']) ?></strong>
                </div>
                <div class="flex justify-between pt-1 border-t border-purple-200">
                    <span class="font-bold text-slate-800">Total Tagihan Buyer:</span>
                    <strong class="text-base font-extrabold text-purple-900"><?= format_rupiah($order['total_amount']) ?></strong>
                </div>
                <div class="flex justify-between text-emerald-800 pt-1">
                    <span>Komisi Fee Platform AkuAda:</span>
                    <strong class="font-bold"><?= format_rupiah($order['total_vendor_fee']) ?></strong>
                </div>
                <div class="flex justify-between text-slate-700">
                    <span>Hak Bersih Vendor (Net Payout):</span>
                    <strong class="font-extrabold"><?= format_rupiah($order['vendor_net_payout']) ?></strong>
                </div>
                <div class="pt-2 text-[11px] text-slate-500">
                    Metode: <?= badge_payment_method($order['payment_method']) ?> | Jatuh Tempo: <strong><?= $order['due_date'] ? date('d M Y', strtotime($order['due_date'])) : '-' ?></strong>
                </div>
            </div>
        </div>

    </div>

    <!-- Table of Order Items (order_items table) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden space-y-4 p-6">
        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <span class="material-symbols-outlined text-purple-600">inventory</span>
            Daftar Barang Dipesan (Tabel order_items)
        </h3>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Nama Barang & SKU</th>
                        <th class="px-4 py-3.5 text-center w-28">Kuantitas</th>
                        <th class="px-4 py-3.5 text-right w-36">Harga Satuan (Web)</th>
                        <th class="px-4 py-3.5 text-right w-48">Fee Platform & Hak Vendor</th>
                        <th class="px-4 py-3.5 text-right w-44">PPN <?= esc(format_tax_rate()) ?> & Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($order['items'])): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400">Tidak ada item dalam pesanan ini.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($order['items'] as $item): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-900 leading-snug break-words"><?= esc($item['product_name']) ?></div>
                                <div class="text-[10px] font-mono font-bold text-purple-700 mt-0.5">SKU: <?= esc($item['sku'] ?? '-') ?></div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-center font-bold text-slate-800">
                                <?= number_format($item['qty']) ?> <?= esc($item['uom']) ?>
                            </td>
                            <td class="px-4 py-3.5 align-top text-right font-mono font-medium text-slate-800">
                                <?= format_rupiah($item['unit_price']) ?>
                            </td>
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-purple-50 text-purple-800 font-bold text-[10px] border border-purple-200">
                                    Fee <?= (float)$item['vendor_fee_percent'] ?>%: <?= format_rupiah($item['vendor_fee_amount']) ?>
                                </div>
                                <div class="text-emerald-700 font-bold text-xs mt-1">
                                    Hak: <?= format_rupiah($item['vendor_net_amount']) ?>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-slate-900 text-sm"><?= format_rupiah($item['subtotal']) ?></div>
                                <div class="text-[10px] text-blue-600 font-medium mt-0.5">+ PPN: <?= format_rupiah($item['ppn_amount']) ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Timeline History (order_status_logs table) & Update Action -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 text-xs">
        
        <!-- Timeline Log View (8 Cols) -->
        <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-600">history</span>
                    Audit Trail & Log Status Transaksi (Tabel order_status_logs)
                </h3>
                <span class="text-slate-400"><?= count($order['status_logs'] ?? []) ?> Catatan Log</span>
            </div>

            <div class="relative pl-6 space-y-3 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-purple-300">
                <?php if (empty($order['status_logs'])): ?>
                    <p class="text-slate-400 italic">Belum ada catatan log aktivitas.</p>
                <?php else: ?>
                    <?php foreach ($order['status_logs'] as $log): ?>
                        <div class="relative flex items-start gap-3">
                            <div class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-white border-2 border-purple-600 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-purple-500"></div>
                            </div>
                            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 w-full space-y-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <strong class="font-bold text-slate-900"><?= esc($log['title']) ?></strong>
                                    <span class="text-[10px] text-slate-400 font-mono"><?= date('d M Y, H:i WIB', strtotime($log['created_at'])) ?></span>
                                </div>
                                <?php if (!empty($log['description'])): ?>
                                    <p class="text-slate-600 text-[11px]"><?= esc($log['description']) ?></p>
                                <?php endif; ?>
                                <span class="text-[10px] text-purple-700 font-semibold block pt-0.5">Dicatat oleh: <?= esc($log['updated_by']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Superadmin Status Action Form (4 Cols) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-purple-200 shadow-sm space-y-4 self-start">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-purple-600">manage_accounts</span>
                Intervensi Status Superadmin
            </h3>

            <form action="<?= base_url('admin/transactions/update') ?>" method="POST" class="space-y-4">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Pengadaan PO:</label>
                    <select name="order_status" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-semibold">
                        <option value="po_issued" <?= $order['order_status'] === 'po_issued' ? 'selected' : '' ?>>1. Penerbitan PO</option>
                        <option value="po_approved" <?= $order['order_status'] === 'po_approved' ? 'selected' : '' ?>>2. PO Dikonfirmasi Vendor</option>
                        <option value="in_production" <?= in_array($order['order_status'], ['in_production', 'processing']) ? 'selected' : '' ?>>3. Proses Produksi / Penyiapan</option>
                        <option value="quality_control" <?= $order['order_status'] === 'quality_control' ? 'selected' : '' ?>>4. Pemeriksaan Mutu (QC)</option>
                        <option value="packaging" <?= $order['order_status'] === 'packaging' ? 'selected' : '' ?>>5. Pengemasan & Paletisasi</option>
                        <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>6. Dalam Pengiriman (Surat Jalan)</option>
                        <option value="delivered" <?= $order['order_status'] === 'delivered' ? 'selected' : '' ?>>7. Tiba di Lokasi Buyer</option>
                        <option value="bast_signed" <?= $order['order_status'] === 'bast_signed' ? 'selected' : '' ?>>8. Pengesahan BAST Digital</option>
                        <option value="completed" <?= $order['order_status'] === 'completed' ? 'selected' : '' ?>>9. Selesai & Lunas</option>
                        <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Status Pembayaran Tempo:</label>
                    <select name="payment_status" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-semibold">
                        <option value="pending_approval" <?= $order['payment_status'] === 'pending_approval' ? 'selected' : '' ?>>Menunggu Verifikasi Pembayaran</option>
                        <option value="unpaid" <?= $order['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Belum Lunas (Tempo Berjalan)</option>
                        <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Lunas Terverifikasi</option>
                        <option value="overdue" <?= $order['payment_status'] === 'overdue' ? 'selected' : '' ?>>Jatuh Tempo (Overdue)</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor BAST Sah:</label>
                    <input type="text" name="bast_number" value="<?= esc($order['bast_number'] ?? '') ?>" placeholder="BAST/AKUADA/..." 
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. e-Faktur Pajak:</label>
                    <input type="text" name="tax_invoice_number" value="<?= esc($order['tax_invoice_number'] ?? '') ?>" placeholder="010.002-26..." 
                           class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-emerald-800 mb-1">Biaya Logistik & Pengiriman (Rp):</label>
                    <input type="number" name="shipping_amount" value="<?= (float)($order['shipping_amount'] ?? 0) ?>" min="0" step="1000"
                           class="w-full px-3 py-2 rounded-xl border border-emerald-300 bg-emerald-50/40 focus:border-emerald-600 font-mono font-bold text-emerald-900">
                </div>

                <button type="submit" class="w-full py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span>Simpan & Catat Audit Log</span>
                </button>
            </form>
        </div>

    </div>

</div>

<?= $this->endSection() ?>

