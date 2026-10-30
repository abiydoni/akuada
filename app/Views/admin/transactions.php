<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold tracking-wide uppercase">Audit Pengadaan B2B</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Manajemen Transaksi & Purchase Order</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau status siklus PO, dokumen serah terima BAST, dan e-Faktur Pajak PPN 11%.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-purple-50 text-purple-700 text-xs font-bold border border-purple-200">
                Total: <?= count($orders) ?> Pesanan
            </span>
        </div>
    </div>

    <!-- Main Table Card - Fits 100% width with NO horizontal scroll -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-44">No. PO & Dokumen</th>
                        <th class="px-4 py-3.5">Corporate Buyer</th>
                        <th class="px-4 py-3.5">Mitra Vendor</th>
                        <th class="px-4 py-3.5 text-right w-48">Rincian Nilai & Fee</th>
                        <th class="px-4 py-3.5 text-center w-36">Status & Bayar</th>
                        <th class="px-4 py-3.5 text-center w-48">Aksi & Update</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">receipt_long</span>
                                    <p class="font-semibold text-slate-600">Belum ada transaksi purchase order</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Pesanan dari corporate buyer akan otomatis tercatat di sini.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($orders as $ord): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- No. PO & Invoices -->
                            <td class="px-4 py-3.5 align-top">
                                <a href="<?= base_url('admin/orders/' . $ord['id']) ?>" 
                                   class="font-mono font-bold text-purple-700 hover:text-purple-900 hover:underline flex items-center gap-1 group text-xs" 
                                   title="Buka Lembar Kerja Detail PO">
                                    <span><?= esc($ord['po_number'] ?? $ord['order_number']) ?></span>
                                    <span class="material-symbols-outlined text-[13px] opacity-0 group-hover:opacity-100 transition">arrow_forward</span>
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono block mt-0.5">
                                    <?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?>
                                </span>

                                <div class="flex flex-wrap gap-1 mt-2">
                                    <a href="<?= base_url('admin/orders/invoice-customer/' . $ord['id']) ?>" target="_blank" 
                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-purple-50 hover:bg-purple-100 text-purple-800 font-semibold text-[10px] border border-purple-200 transition" 
                                       title="Faktur Penjualan AkuAda ke Buyer">
                                        <span class="material-symbols-outlined text-[12px] text-purple-600">receipt_long</span>
                                        <span>INV Buyer</span>
                                    </a>
                                    <a href="<?= base_url('admin/orders/invoice-vendor/' . $ord['id']) ?>" target="_blank" 
                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-blue-50 hover:bg-blue-100 text-blue-800 font-semibold text-[10px] border border-blue-200 transition" 
                                       title="Faktur Penagihan Vendor ke AkuAda">
                                        <span class="material-symbols-outlined text-[12px] text-blue-600">account_balance_wallet</span>
                                        <span>INV Vendor</span>
                                    </a>
                                </div>
                            </td>

                            <!-- Buyer (Full name, no cut-off) -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-900 leading-snug break-words"><?= esc($ord['buyer_name']) ?></div>
                                <div class="text-[11px] text-slate-400 font-mono mt-0.5">NPWP: <?= esc($ord['buyer_npwp'] ?? '-') ?></div>
                            </td>

                            <!-- Vendor (Full name, no cut-off) -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-semibold text-slate-800 leading-snug break-words"><?= esc($ord['vendor_name']) ?></div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Mitra Pemasok</div>
                            </td>

                            <!-- Financial Details (DPP, PPN, Fee, Total) -->
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-slate-900 text-sm"><?= format_rupiah($ord['total_amount']) ?></div>
                                <div class="text-[11px] text-slate-600 mt-0.5">DPP: <?= format_rupiah($ord['subtotal']) ?></div>
                                <div class="text-[10px] text-blue-600 font-semibold">+ PPN 11%: <?= format_rupiah($ord['ppn_amount']) ?></div>
                                <div class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 font-bold text-[10px] border border-emerald-200 mt-1">
                                    Fee AkuAda: <?= format_rupiah($ord['total_vendor_fee'] ?? 0) ?>
                                </div>
                            </td>

                            <!-- Status & Payment Method -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="space-y-1.5">
                                    <?= badge_order_status($ord['order_status']) ?>
                                    <div class="block"><?= badge_payment_method($ord['payment_method']) ?></div>
                                </div>
                            </td>

                            <!-- Action & Status Update -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="space-y-2">
                                    <a href="<?= base_url('admin/orders/' . $ord['id']) ?>" 
                                       class="w-full px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-purple-100 text-slate-700 hover:text-purple-800 font-bold text-xs transition inline-flex items-center justify-center gap-1 border border-slate-200" 
                                       title="Buka Lembar Kerja Detail PO">
                                        <span class="material-symbols-outlined text-[15px]">visibility</span>
                                        <span>Rincian PO</span>
                                    </a>

                                    <form action="<?= base_url('admin/transactions/update') ?>" method="POST" class="w-full">
                                        <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                        <select name="order_status" 
                                                class="w-full text-[11px] py-1 px-1.5 rounded-lg border border-slate-300 bg-white font-medium text-slate-700 focus:border-purple-600 focus:ring-1 focus:ring-purple-600 cursor-pointer" 
                                                onchange="this.form.submit()" 
                                                title="Ganti status langsung">
                                            <option value="po_issued" <?= in_array($ord['order_status'], ['po_issued', 'pending_po_approval']) ? 'selected' : '' ?>>1. Penerbitan PO</option>
                                            <option value="po_approved" <?= $ord['order_status'] === 'po_approved' ? 'selected' : '' ?>>2. PO Dikonfirmasi</option>
                                            <option value="in_production" <?= in_array($ord['order_status'], ['in_production', 'processing']) ? 'selected' : '' ?>>3. Produksi / Siap</option>
                                            <option value="quality_control" <?= $ord['order_status'] === 'quality_control' ? 'selected' : '' ?>>4. Pemeriksaan QC</option>
                                            <option value="packaging" <?= $ord['order_status'] === 'packaging' ? 'selected' : '' ?>>5. Pengemasan</option>
                                            <option value="shipped" <?= $ord['order_status'] === 'shipped' ? 'selected' : '' ?>>6. Pengiriman</option>
                                            <option value="delivered" <?= $ord['order_status'] === 'delivered' ? 'selected' : '' ?>>7. Tiba di Lokasi</option>
                                            <option value="bast_signed" <?= $ord['order_status'] === 'bast_signed' ? 'selected' : '' ?>>8. BAST Disahkan</option>
                                            <option value="completed" <?= $ord['order_status'] === 'completed' ? 'selected' : '' ?>>9. Selesai & Lunas</option>
                                            <option value="cancelled" <?= $ord['order_status'] === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                                        </select>
                                    </form>
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
