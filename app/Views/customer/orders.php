<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">
    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-pink-100 text-pink-800 text-[10px] font-bold uppercase tracking-wider">Pengadaan Korporat</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Daftar Purchase Order (PO) & Dokumen BAST</h1>
            <p class="text-xs text-slate-500 mt-0.5">Seluruh arsip surat pesanan resmi, termin pembayaran TOP tempo, dan pengesahan BAST serah terima.</p>
        </div>
        <a href="<?= base_url('products') ?>" class="btn-akuada-primary text-xs px-4 py-2.5 shadow-md flex items-center gap-2 self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
            <span>+ Buat Pengadaan Baru</span>
        </a>
    </div>

    <!-- Orders Table Card - Fits 100% width with NO horizontal scroll -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-pink-600"></span>
                <h3 class="font-bold text-slate-800 text-sm">Riwayat Purchase Order Saya</h3>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px]"><?= count($orders) ?> Pesanan</span>
            </div>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-48">No. PO & Dokumen</th>
                        <th class="px-4 py-3.5">Vendor Rekanan</th>
                        <th class="px-4 py-3.5 text-right w-44">Total Nilai Tagihan</th>
                        <th class="px-4 py-3.5 text-center w-36">Status Pengadaan</th>
                        <th class="px-4 py-3.5 text-center w-44">Aksi Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">receipt_long</span>
                                    <p class="font-semibold text-slate-600">Belum ada purchase order diterbitkan</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Pilih barang dari katalog marketplace untuk menerbitkan PO resmi.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($orders as $o): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- PO, Date & Documents -->
                            <td class="px-4 py-3.5 align-top">
                                <a href="<?= base_url('customer/orders/' . $o['id']) ?>" 
                                   class="font-mono font-bold text-purple-700 hover:text-purple-900 hover:underline flex items-center gap-1 group text-xs">
                                    <span><?= esc($o['po_number'] ?? $o['order_number']) ?></span>
                                    <span class="material-symbols-outlined text-[13px] opacity-0 group-hover:opacity-100 transition">arrow_forward</span>
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono block mt-0.5"><?= date('d/m/Y', strtotime($o['created_at'])) ?></span>

                                <div class="mt-2 space-y-0.5 text-[10px] font-mono text-slate-500">
                                    <div>BAST: <?= !empty($o['bast_number']) ? '<strong class="text-slate-700">' . esc($o['bast_number']) . '</strong>' : '<span class="text-slate-400 italic">Belum Terbit</span>' ?></div>
                                    <div>e-Faktur: <?= !empty($o['tax_invoice_number']) ? '<strong class="text-blue-700">' . esc($o['tax_invoice_number']) . '</strong>' : '<span class="text-slate-400">-</span>' ?></div>
                                </div>
                            </td>

                            <!-- Vendor (Full text, no cut-off) -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-900 leading-snug break-words"><?= esc($o['vendor_name']) ?></div>
                                <div class="text-[11px] text-slate-400 mt-0.5">Mitra Resmi AkuAda</div>
                            </td>

                            <!-- Total & Payment Method -->
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-slate-900 text-sm"><?= format_rupiah($o['total_amount']) ?></div>
                                <div class="mt-1"><?= badge_payment_method($o['payment_method']) ?></div>
                            </td>

                            <!-- Status -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <?= badge_order_status($o['order_status']) ?>
                            </td>

                            <!-- Actions -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="flex flex-col gap-1.5">
                                    <a href="<?= base_url('customer/orders/' . $o['id']) ?>" 
                                       class="w-full px-2.5 py-1.5 rounded-lg bg-pink-50 hover:bg-pink-100 text-pink-700 font-bold text-xs transition inline-flex items-center justify-center gap-1 border border-pink-200 shadow-sm" 
                                       title="Lihat Rincian Status PO & Dokumen BAST">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                                        <span>PO & BAST</span>
                                    </a>
                                    <a href="<?= base_url('customer/orders/invoice/' . $o['id']) ?>" target="_blank" 
                                       class="w-full px-2.5 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold text-xs transition inline-flex items-center justify-center gap-1 border border-purple-200" 
                                       title="Cetak Faktur Penjualan Resmi AkuAda">
                                        <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                                        <span>Faktur AkuAda</span>
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
