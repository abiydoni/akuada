<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Daftar Purchase Order (PO) & Dokumen BAST</h1>
            <p class="text-xs text-slate-500 mt-0.5">Seluruh arsip surat pesanan resmi, termin pembayaran TOP, dan status pengiriman.</p>
        </div>
        <a href="<?= base_url('products') ?>" class="btn-akuada-primary text-xs px-4 py-2">
            <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span>
            <span>Buat PO Baru</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="p-4">No. PO & Tanggal</th>
                        <th class="p-4">Vendor Rekanan</th>
                        <th class="p-4">Termin Pembayaran</th>
                        <th class="p-4">Total Nilai Tagihan</th>
                        <th class="p-4">No. BAST</th>
                        <th class="p-4">No. e-Faktur Pajak</th>
                        <th class="p-4">Status Pengadaan</th>
                        <th class="p-4 text-center">Aksi Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($orders as $o): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4">
                                <span class="font-mono font-bold text-purple-700 block"><?= esc($o['po_number'] ?? $o['order_number']) ?></span>
                                <span class="text-[10px] text-slate-400"><?= date('d/m/Y', strtotime($o['created_at'])) ?></span>
                            </td>
                            <td class="p-4 font-bold text-slate-800"><?= esc($o['vendor_name']) ?></td>
                            <td class="p-4"><?= badge_payment_method($o['payment_method']) ?></td>
                            <td class="p-4 font-extrabold text-slate-900"><?= format_rupiah($o['total_amount']) ?></td>
                            <td class="p-4 font-mono text-slate-600"><?= esc($o['bast_number'] ?? '-') ?></td>
                            <td class="p-4 font-mono text-blue-700"><?= esc($o['tax_invoice_number'] ?? '-') ?></td>
                            <td class="p-4"><?= badge_order_status($o['order_status']) ?></td>
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="<?= base_url('customer/orders/' . $o['id']) ?>" class="px-2.5 py-1.5 rounded-lg bg-pink-50 hover:bg-pink-100 text-pink-700 font-bold transition flex items-center justify-center gap-1" title="Lihat Status PO & BAST">
                                        <span class="material-symbols-outlined text-[15px]">visibility</span>
                                        <span>PO</span>
                                    </a>
                                    <a href="<?= base_url('customer/orders/invoice/' . $o['id']) ?>" target="_blank" class="px-2.5 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold transition flex items-center justify-center gap-1 border border-purple-200" title="Cetak Faktur Penjualan AkuAda">
                                        <span class="material-symbols-outlined text-[15px]">receipt_long</span>
                                        <span>Invoice</span>
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
