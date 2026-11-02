<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('admin/transactions') ?>" class="hover:text-purple-600">Daftar Transaksi & PO</a>
                <span>/</span>
                <span class="font-mono text-purple-700 font-bold"><?= esc($order['po_number'] ?? $order['order_number']) ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900">Audit PO, Log Tahapan & Dokumen Legal</h1>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="<?= base_url('admin/orders/invoice-customer/' . $order['id']) ?>" target="_blank" class="px-3.5 py-2 rounded-xl bg-purple-100 hover:bg-purple-200 text-purple-900 font-extrabold text-xs transition flex items-center gap-1.5 shadow-sm border border-purple-200">
                <span class="material-symbols-outlined text-[18px] text-purple-700">receipt_long</span>
                <span>Faktur Tagihan ke Buyer</span>
            </a>
            <a href="<?= base_url('admin/orders/invoice-vendor/' . $order['id']) ?>" target="_blank" class="px-3.5 py-2 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-extrabold text-xs transition flex items-center gap-1.5 shadow-sm border border-emerald-200">
                <span class="material-symbols-outlined text-[18px] text-emerald-700">receipt</span>
                <span>Faktur Vendor ke AkuAda</span>
            </a>
            <a href="<?= base_url('admin/orders/bast/' . $order['id']) ?>" target="_blank" class="px-3 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-900 font-bold text-xs transition flex items-center gap-1.5 shadow-sm border border-blue-200">
                <span class="material-symbols-outlined text-[18px] text-blue-700">contract</span>
                <span>Dokumen BAST Sah</span>
            </a>
            <a href="<?= base_url('admin/orders/tax-invoice/' . $order['id']) ?>" target="_blank" class="px-3 py-2 rounded-xl bg-teal-50 hover:bg-teal-100 text-teal-900 font-bold text-xs transition flex items-center gap-1.5 shadow-sm border border-teal-200">
                <span class="material-symbols-outlined text-[18px] text-teal-700">receipt</span>
                <span>e-Faktur PPN</span>
            </a>
            <a href="<?= base_url('admin/chat?order_id=' . $order['id']) ?>" class="px-3.5 py-2 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-extrabold text-xs transition flex items-center gap-1.5 shadow-md shadow-purple-600/20">
                <span class="material-symbols-outlined text-[18px]">forum</span>
                <span>Pusat Chat Transaksi</span>
            </a>
        </div>
    </div>

    <!-- 1. Real-Time Order Stepper (8 Stages Timeline) -->
    <div class="bg-white rounded-3xl border border-purple-200 shadow-md p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">route</span>
                <h3 class="text-sm font-bold text-slate-900">Status & Tahapan Pemrosesan Pengadaan (Lifecycle Timeline)</h3>
            </div>
            <div class="flex items-center gap-2">
                <?= badge_order_status($order['order_status']) ?>
                <?= badge_payment_status($order['payment_status']) ?>
            </div>
        </div>

        <?= render_order_progress_stepper($order['order_status']) ?>
    </div>

    <!-- 1b. Dispute / Return Status Banner (If Applicable) -->
    <?php if (!empty($latestReturn)): ?>
        <div class="bg-amber-50 rounded-3xl border-2 border-amber-300 p-6 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-amber-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-[24px]">assignment_return</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-amber-950 text-sm">Tiket Klaim Retur & Pengembalian: <?= esc($latestReturn['return_number']) ?></h3>
                            <?= badge_order_status($latestReturn['status']) ?>
                        </div>
                        <p class="text-xs text-amber-800">Diajukan pada <?= date('d M Y H:i', strtotime($latestReturn['created_at'])) ?> oleh Buyer</p>
                    </div>
                </div>
                <div class="text-xs font-semibold px-3 py-1 rounded-full bg-white border border-amber-300 text-amber-900">
                    Solusi: <?= $latestReturn['resolution_requested'] === 'replacement' ? '🔄 Ganti Barang Baru' : '💰 Kredit Nota / Refund' ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                <div class="bg-white p-3.5 rounded-2xl border border-amber-200 space-y-1">
                    <span class="text-slate-400 font-semibold text-[11px] block">Kategori Kendala:</span>
                    <strong class="text-slate-800 capitalize"><?= str_replace('_', ' ', $latestReturn['reason_category']) ?></strong>
                    <div class="text-slate-600 text-[11px] mt-1"><?= esc($latestReturn['item_description'] ?: 'Semua item pesanan') ?></div>
                </div>

                <div class="bg-white p-3.5 rounded-2xl border border-amber-200 space-y-1">
                    <span class="text-slate-400 font-semibold text-[11px] block">Deskripsi Kerusakan dari Buyer:</span>
                    <p class="text-slate-700 italic">"<?= esc($latestReturn['damage_description']) ?>"</p>
                    <?php if (!empty($latestReturn['evidence_photo'])): ?>
                        <a href="<?= base_url($latestReturn['evidence_photo']) ?>" target="_blank" class="inline-flex items-center gap-1 text-purple-700 font-bold hover:underline mt-1">
                            <span class="material-symbols-outlined text-[14px]">photo</span> Lihat Foto Bukti Fisik
                        </a>
                    <?php endif; ?>
                </div>

                <div class="bg-white p-3.5 rounded-2xl border border-amber-200 space-y-1">
                    <span class="text-slate-400 font-semibold text-[11px] block">Respon Mitra Vendor:</span>
                    <?php if (!empty($latestReturn['vendor_response'])): ?>
                        <p class="text-slate-700">"<?= esc($latestReturn['vendor_response']) ?>"</p>
                    <?php else: ?>
                        <p class="text-slate-400 italic">Menunggu tanggapan/verifikasi vendor.</p>
                    <?php endif; ?>
                    <?php if (!empty($latestReturn['replacement_tracking'])): ?>
                        <div class="mt-1 font-mono font-bold text-purple-800">Resi Pengganti: <?= esc($latestReturn['replacement_tracking']) ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- 2. Overlimit Warning Banner (If Applicable) -->
    <?php if (str_starts_with($order['order_status'] ?? '', 'overlimit_')): ?>
        <div class="p-6 rounded-3xl bg-gradient-to-r from-rose-600 to-amber-600 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 border-2 border-rose-300">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center flex-shrink-0 text-white">
                    <span class="material-symbols-outlined text-[32px]">warning</span>
                </div>
                <div>
                    <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-bold uppercase tracking-wider mb-1 inline-block">
                        Transaksi Melebihi Plafon Kredit
                    </span>
                    <h3 class="text-lg font-black tracking-tight">Membutuhkan Persetujuan Superadmin / Risk Manager</h3>
                    <p class="text-rose-100 text-xs mt-1">
                        Total PO senilai <strong><?= format_rupiah($order['total_amount']) ?></strong> melampaui sisa plafon buyer.
                    </p>
                </div>
            </div>
            <form action="<?= base_url('admin/orders/approve-overlimit') ?>" method="POST" class="flex-shrink-0"><?= csrf_field() ?>
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <button type="submit" class="px-6 py-3 rounded-2xl bg-white text-rose-900 font-extrabold text-xs hover:bg-rose-50 transition shadow-lg flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-emerald-600">verified</span>
                    <span>Setujui Override Plafon & Terbitkan PO</span>
                </button>
            </form>
        </div>
    <?php endif; ?>

    <!-- 3. Review & Rating Card dari Buyer (Jika ada) -->
    <?php if (!empty($review)): ?>
        <div class="bg-white rounded-3xl border border-amber-200/90 shadow-md p-6 sm:p-8 space-y-4 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-amber-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">star</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Ulasan & Rating Bintang Kepuasan Transaksi</h3>
                        <p class="text-[11px] text-slate-500">Evaluasi dari Buyer: <strong><?= esc($order['buyer_name']) ?></strong> kepada Vendor: <strong><?= esc($order['vendor_name']) ?></strong></p>
                    </div>
                </div>
                <span class="text-[11px] text-slate-400 font-mono"><?= date('d F Y, H:i', strtotime($review['created_at'])) . ' WIB' ?></span>
            </div>

            <div class="p-5 rounded-2xl bg-amber-50/40 border border-amber-200/80 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="flex items-center text-amber-400">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="material-symbols-outlined text-[24px] <?= $i <= (int)$review['rating'] ? 'text-amber-400' : 'text-slate-300' ?>">star</span>
                        <?php endfor; ?>
                    </div>
                    <strong class="text-sm font-black text-slate-900"><?= (float)$review['rating'] ?>.0 / 5.0</strong>
                    <span class="text-xs text-slate-500">| Penilai: <strong><?= esc($review['buyer_name']) ?></strong></span>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-[11px]">
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">Kualitas Barang: <strong><?= (int)($review['rating_quality'] ?? 5) ?>★</strong></span>
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">Kecepatan Kirim: <strong><?= (int)($review['rating_delivery'] ?? 5) ?>★</strong></span>
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">Respon Pelayanan: <strong><?= (int)($review['rating_service'] ?? 5) ?>★</strong></span>
                </div>

                <?php if (!empty($review['review_title'])): ?>
                    <h4 class="font-bold text-slate-900 text-xs"><?= esc($review['review_title']) ?></h4>
                <?php endif; ?>
                <p class="text-slate-700 italic bg-white p-3.5 rounded-xl border border-slate-200">
                    "<?= esc($review['review_text']) ?>"
                </p>

                <?php if (!empty($review['vendor_reply'])): ?>
                    <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 space-y-1">
                        <div class="flex items-center justify-between">
                            <strong class="text-blue-900 font-bold">Tanggapan Balasan dari Mitra Vendor:</strong>
                            <span class="text-[10px] text-blue-600 font-mono"><?= date('d M Y', strtotime($review['vendor_replied_at'])) ?></span>
                        </div>
                        <p class="text-slate-700"><?= esc($review['vendor_reply']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- 4. Top Summary: Buyer, Vendor & Settlement Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
        <!-- Corporate Buyer Card -->
        <div class="bg-white p-5 rounded-2xl border border-purple-200 shadow-sm space-y-3">
            <span class="text-[10px] font-extrabold text-purple-700 uppercase tracking-widest block">PEMESAN (CORPORATE BUYER):</span>
            <strong class="text-base text-slate-900 block"><?= esc($order['buyer_name']) ?></strong>
            <div class="space-y-1 text-slate-600">
                <p>NPWP: <strong class="font-mono"><?= esc($order['buyer_npwp'] ?? '-') ?></strong></p>
                <p>PIC: <?= esc($order['buyer_pic'] ?? '-') ?> (<?= esc($order['buyer_phone'] ?? '-') ?>)</p>
                <p>Alamat Kirim: <?= esc($order['shipping_address'] ?? $order['buyer_address']) ?></p>
            </div>
        </div>

        <!-- Vendor Card -->
        <div class="bg-white p-5 rounded-2xl border border-blue-200 shadow-sm space-y-3">
            <span class="text-[10px] font-extrabold text-blue-700 uppercase tracking-widest block">PENYEDIA (MITRA VENDOR):</span>
            <strong class="text-base text-slate-900 block"><?= esc($order['vendor_name']) ?></strong>
            <div class="space-y-1 text-slate-600">
                <p>NPWP: <strong class="font-mono"><?= esc($order['vendor_npwp'] ?? '-') ?></strong></p>
                <p>Rekening: <?= esc($order['vendor_bank_name'] ?? 'BCA') ?> - <span class="font-mono"><?= esc($order['vendor_bank_account_number'] ?? '-') ?></span> a/n <?= esc($order['vendor_bank_account_holder'] ?? '-') ?></p>
                <p>Fee AkuAda: <strong class="text-purple-700"><?= (float)($order['vendor_commission_rate'] ?? 2.5) ?>%</strong></p>
            </div>
        </div>

        <!-- Financial Summary Card -->
        <div class="bg-gradient-to-br from-slate-900 to-purple-950 p-5 rounded-2xl text-white shadow-sm space-y-3">
            <span class="text-[10px] font-extrabold text-purple-300 uppercase tracking-widest block">STRUKTUR REKONSILIASI KEUANGAN:</span>
            <div class="space-y-1.5 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-300">DPP Penjualan:</span>
                    <strong class="font-mono"><?= format_rupiah($order['subtotal']) ?></strong>
                </div>
                <div class="flex justify-between text-blue-300">
                    <span>PPN <?= esc(format_tax_rate()) ?>:</span>
                    <strong class="font-mono"><?= format_rupiah($order['ppn_amount']) ?></strong>
                </div>
                <div class="flex justify-between text-emerald-300">
                    <span>Fee Pendapatan AkuAda:</span>
                    <strong class="font-mono">+ <?= format_rupiah($order['total_vendor_fee']) ?></strong>
                </div>
                <div class="flex justify-between text-amber-300 pt-2 border-t border-purple-800">
                    <span>Hak Bersih Vendor:</span>
                    <strong class="font-mono"><?= format_rupiah($order['vendor_net_payout']) ?></strong>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Order Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <span class="material-symbols-outlined text-purple-600">inventory_2</span>
            Daftar Barang Pesanan
        </h3>
        <div class="w-full overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Barang / Spesifikasi</th>
                        <th class="px-4 py-3 text-center w-28">Kuantitas</th>
                        <th class="px-4 py-3 text-right w-36">Harga Satuan</th>
                        <th class="px-4 py-3 text-right w-44">Komisi Platform</th>
                        <th class="px-4 py-3 text-right w-44">Subtotal DPP</th>
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

    <!-- 6. Intervensi Superadmin & Audit Trail -->
    <div class="space-y-6 text-xs">
        <div class="space-y-6">
            
            <!-- Superadmin Status Action Form -->
            <div class="bg-white p-6 rounded-2xl border border-purple-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="material-symbols-outlined text-purple-600">manage_accounts</span>
                    Intervensi Status & Dokumen Legal Superadmin
                </h3>

                <form action="<?= base_url('admin/transactions/update') ?>" method="POST" class="space-y-4"><?= csrf_field() ?>
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
                            <option value="return_requested" <?= $order['order_status'] === 'return_requested' ? 'selected' : '' ?>>⚠️ Klaim Retur Diajukan Buyer</option>
                            <option value="return_approved" <?= $order['order_status'] === 'return_approved' ? 'selected' : '' ?>>⚠️ Retur Disetujui Vendor</option>
                            <option value="replacement_shipped" <?= $order['order_status'] === 'replacement_shipped' ? 'selected' : '' ?>>📦 Barang Pengganti Dikirim</option>
                            <option value="return_completed" <?= $order['order_status'] === 'return_completed' ? 'selected' : '' ?>>✅ Retur / Penggantian Selesai</option>
                            <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Status Pembayaran Tempo (TOP):</label>
                        <select name="payment_status" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-semibold">
                            <option value="pending_approval" <?= $order['payment_status'] === 'pending_approval' ? 'selected' : '' ?>>Menunggu Verifikasi Pembayaran Buyer</option>
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
                        <label class="block font-bold text-slate-700 mb-1">No. e-Faktur Pajak PPN <?= esc(format_tax_rate()) ?>:</label>
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

            <!-- Audit Trail & Timeline History Log -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
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
                                        <span class="text-[10px] text-slate-400 font-mono"><?= date('d M Y, H:i', strtotime($log['created_at'])) . ' WIB' ?></span>
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

        </div>

        <!-- Banner Monitoring Komunikasi Transaksi -->
        <div class="p-5 rounded-2xl bg-gradient-to-r from-purple-50 via-indigo-50 to-white border border-purple-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-700 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-[22px]">forum</span>
                </div>
                <div>
                    <strong class="text-slate-900 font-bold text-xs block">Monitoring Percakapan Live Chat Pesanan Ini</strong>
                    <p class="text-slate-500 text-[11px] mt-0.5">
                        Pantau diskusi antara <strong><?= esc($order['buyer_name']) ?></strong> dan <strong><?= esc($order['vendor_name']) ?></strong>, atau kirim bantuan mediasi langsung dari Superadmin.
                    </p>
                </div>
            </div>
            <a href="<?= base_url('admin/chat?order_id=' . $order['id']) ?>" class="px-5 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs transition flex items-center gap-2 shadow-md shadow-purple-600/20 flex-shrink-0">
                <span class="material-symbols-outlined text-[18px]">chat</span>
                <span>Buka Percakapan Chat PO ↗</span>
            </a>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
