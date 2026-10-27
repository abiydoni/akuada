<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Manajemen Transaksi & Purchase Order B2B</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau status pemrosesan PO, dokumen BAST, dan faktur pajak e-Faktur.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="p-4">No. PO & Order</th>
                        <th class="p-4">Corporate Buyer</th>
                        <th class="p-4">Vendor</th>
                        <th class="p-4">Subtotal DPP & PPN</th>
                        <th class="p-4">Fee Platform & Hak Vendor</th>
                        <th class="p-4">Total Tagihan</th>
                        <th class="p-4">Metode Bayar</th>
                        <th class="p-4">Faktur & Invoice</th>
                        <th class="p-4">Status Pengadaan</th>
                        <th class="p-4 text-center">Ubah Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($orders as $ord): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4">
                                <span class="font-mono font-bold text-purple-700 block"><?= esc($ord['po_number'] ?? $ord['order_number']) ?></span>
                                <span class="text-[10px] text-slate-400 block"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></span>
                            </td>
                            <td class="p-4">
                                <strong class="text-slate-900 block"><?= esc($ord['buyer_name']) ?></strong>
                                <span class="text-[10px] text-slate-400">NPWP: <?= esc($ord['buyer_npwp'] ?? '-') ?></span>
                            </td>
                            <td class="p-4 text-slate-700 font-medium"><?= esc($ord['vendor_name']) ?></td>
                            <td class="p-4">
                                <span class="text-slate-700 font-bold block"><?= format_rupiah($ord['subtotal']) ?></span>
                                <span class="text-[10px] text-blue-600 font-semibold block">+ PPN 11%: <?= format_rupiah($ord['ppn_amount']) ?></span>
                            </td>
                            <td class="p-4">
                                <div class="space-y-0.5">
                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-800 font-bold text-[10px] border border-emerald-200">
                                        Fee: <?= format_rupiah($ord['total_vendor_fee'] ?? 0) ?>
                                    </span>
                                    <span class="text-[10px] text-slate-500 block">Hak Vendor: <strong class="text-slate-800"><?= format_rupiah($ord['vendor_net_payout'] ?? ($ord['subtotal'] - ($ord['total_vendor_fee'] ?? 0))) ?></strong></span>
                                </div>
                            </td>
                            <td class="p-4 font-extrabold text-slate-900"><?= format_rupiah($ord['total_amount']) ?></td>
                            <td class="p-4"><?= badge_payment_method($ord['payment_method']) ?></td>
                            <td class="p-4">
                                <div class="flex flex-col gap-1">
                                    <a href="<?= base_url('admin/orders/invoice-customer/' . $ord['id']) ?>" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-800 font-bold text-[10px] border border-purple-200 transition" title="Faktur Penjualan AkuAda ke Hotel/Perusahaan">
                                        <span class="material-symbols-outlined text-[13px] text-purple-600">receipt_long</span>
                                        <span>INV Pelanggan (AkuAda)</span>
                                    </a>
                                    <a href="<?= base_url('admin/orders/invoice-vendor/' . $ord['id']) ?>" target="_blank" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 font-bold text-[10px] border border-blue-200 transition" title="Faktur Penagihan Vendor ke AkuAda (Net Payout setelah Fee)">
                                        <span class="material-symbols-outlined text-[13px] text-blue-600">account_balance_wallet</span>
                                        <span>INV Vendor (Net Fee)</span>
                                    </a>
                                </div>
                            </td>
                            <td class="p-4"><?= badge_order_status($ord['order_status']) ?></td>
                            <td class="p-4 text-center">
                                <form action="<?= base_url('admin/transactions/update') ?>" method="POST" class="inline-flex items-center gap-1.5">
                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                    <select name="order_status" class="text-[11px] py-1 px-2 rounded-lg border border-slate-300 bg-white font-semibold" onchange="this.form.submit()">
                                        <option value="po_issued" <?= in_array($ord['order_status'], ['po_issued', 'pending_po_approval']) ? 'selected' : '' ?>>1. Penerbitan PO</option>
                                        <option value="po_approved" <?= $ord['order_status'] === 'po_approved' ? 'selected' : '' ?>>2. PO Dikonfirmasi</option>
                                        <option value="in_production" <?= in_array($ord['order_status'], ['in_production', 'processing']) ? 'selected' : '' ?>>3. Produksi / Penyiapan</option>
                                        <option value="quality_control" <?= $ord['order_status'] === 'quality_control' ? 'selected' : '' ?>>4. Pemeriksaan Mutu (QC)</option>
                                        <option value="packaging" <?= $ord['order_status'] === 'packaging' ? 'selected' : '' ?>>5. Pengemasan & Pallet</option>
                                        <option value="shipped" <?= $ord['order_status'] === 'shipped' ? 'selected' : '' ?>>6. Dalam Pengiriman</option>
                                        <option value="delivered" <?= $ord['order_status'] === 'delivered' ? 'selected' : '' ?>>7. Tiba di Lokasi</option>
                                        <option value="bast_signed" <?= $ord['order_status'] === 'bast_signed' ? 'selected' : '' ?>>8. BAST Disahkan</option>
                                        <option value="completed" <?= $ord['order_status'] === 'completed' ? 'selected' : '' ?>>9. Selesai & Lunas</option>
                                        <option value="cancelled" <?= $ord['order_status'] === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
