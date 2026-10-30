<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-wider">Manajemen Pesanan Vendor</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Pesanan Purchase Order (PO) & Surat Jalan BAST</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola pemenuhan pesanan korporat, terbitkan nomor BAST & e-Faktur PPN, serta cetak faktur penagihan ke AkuAda.</p>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <h3 class="font-bold text-slate-800 text-sm">Daftar Purchase Order Masuk</h3>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px]"><?= count($orders) ?> Pesanan</span>
            </div>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-48">No. PO & Dokumen</th>
                        <th class="px-4 py-3.5">Perusahaan Pembeli (Buyer)</th>
                        <th class="px-4 py-3.5 text-right w-48">Nilai DPP & Hak Vendor</th>
                        <th class="px-4 py-3.5 text-center w-36">Status Pengadaan</th>
                        <th class="px-4 py-3.5 text-center w-44">Aksi & Faktur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">local_shipping</span>
                                    <p class="font-semibold text-slate-600">Belum ada Purchase Order yang masuk</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Pesanan dari corporate buyer akan tampil secara otomatis di halaman ini.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($orders as $o): ?>
                        <?php
                            $fee = (float)($o['total_vendor_fee'] ?? 0);
                            $net = (float)($o['vendor_net_payout'] ?? ($o['subtotal'] - $fee));
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 align-top">
                                <a href="<?= base_url('vendor/orders/' . $o['id']) ?>" class="font-mono font-bold text-blue-700 hover:text-blue-900 hover:underline flex items-center gap-1 text-xs">
                                    <span><?= esc($o['po_number'] ?? $o['order_number']) ?></span>
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono block mt-0.5"><?= date('d/m/Y', strtotime($o['created_at'])) ?></span>
                                <div class="mt-2 space-y-0.5 text-[10px] font-mono text-slate-500">
                                    <div>BAST: <?= !empty($o['bast_number']) ? '<strong class="text-slate-700">' . esc($o['bast_number']) . '</strong>' : '<span class="text-slate-400 italic">Belum Terbit</span>' ?></div>
                                    <div>e-Faktur: <?= !empty($o['tax_invoice_number']) ? '<strong class="text-blue-700">' . esc($o['tax_invoice_number']) . '</strong>' : '<span class="text-slate-400">-</span>' ?></div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-900 leading-snug break-words"><?= esc($o['buyer_name']) ?></div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">NPWP: <?= esc($o['buyer_npwp'] ?? '-') ?></div>
                                <div class="mt-1"><?= badge_payment_method($o['payment_method']) ?></div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-emerald-700 text-sm"><?= format_rupiah($net) ?></div>
                                <div class="text-[10px] text-slate-500 mt-0.5">DPP: <?= format_rupiah($o['subtotal']) ?></div>
                                <div class="text-[10px] text-purple-700">Fee Platform: -<?= format_rupiah($fee) ?></div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-center">
                                <?= badge_order_status($o['order_status']) ?>
                            </td>
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="flex flex-col gap-1.5">
                                    <a href="<?= base_url('vendor/orders/' . $o['id']) ?>" class="w-full px-2.5 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs transition inline-flex items-center justify-center gap-1 border border-blue-200 shadow-sm">
                                        <span class="material-symbols-outlined text-[14px]">edit_document</span>
                                        <span>Update & BAST</span>
                                    </a>
                                    <a href="<?= base_url('vendor/orders/vendor-invoice/' . $o['id']) ?>" target="_blank" class="w-full px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs transition inline-flex items-center justify-center gap-1 border border-emerald-200">
                                        <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                                        <span>Tagihan ke AkuAda</span>
                                    </a>
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
