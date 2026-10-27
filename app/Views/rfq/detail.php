<?= $this->extend('layouts/main') ?>

<?= $this->section('subcontent') ?>

<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('rfq') ?>" class="hover:text-purple-600">Daftar RFQ</a>
                <span>/</span>
                <span class="font-mono text-purple-700 font-bold"><?= esc($rfq['rfq_number']) ?></span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900"><?= esc($rfq['title']) ?></h1>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-100 text-purple-800">
                Status: <?= esc($rfq['status']) ?>
            </span>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: RFQ Items (8 Cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden space-y-6">
            <div class="p-6 pb-0">
                <h3 class="text-sm font-bold text-slate-800 pb-3 border-b border-slate-100">Daftar Spesifikasi & Penawaran Harga Barang</h3>
            </div>

            <div class="overflow-x-auto p-6 pt-0">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="p-3">Nama Barang</th>
                            <th class="p-3">Qty</th>
                            <th class="p-3">Target Buyer</th>
                            <th class="p-3">Penawaran Vendor</th>
                            <th class="p-3">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($rfq['items'] as $item): ?>
                            <tr>
                                <td class="p-3 font-semibold text-slate-900"><?= esc($item['item_name']) ?></td>
                                <td class="p-3 font-bold"><?= $item['qty'] ?> <?= esc($item['uom']) ?></td>
                                <td class="p-3 text-slate-600"><?= $item['target_unit_price'] ? format_rupiah($item['target_unit_price']) : '-' ?></td>
                                <td class="p-3 font-extrabold text-purple-700">
                                    <?= $item['vendor_offered_price'] ? format_rupiah($item['vendor_offered_price']) : '<span class="text-amber-600 font-normal italic">Menunggu Quota</span>' ?>
                                </td>
                                <td class="p-3 text-slate-500"><?= esc($item['notes'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (!empty($rfq['notes'])): ?>
                <div class="p-6 pt-0">
                    <h4 class="text-xs font-bold text-slate-700 mb-1">Catatan Tambahan Pengadaan:</h4>
                    <p class="text-xs text-slate-600 bg-slate-50 p-3.5 rounded-xl border border-slate-200 leading-relaxed"><?= esc($rfq['notes']) ?></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right: Parties Info (4 Cols) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm text-xs space-y-4">
                <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">Para Pihak Pengadaan</h3>
                
                <div class="space-y-1">
                    <span class="text-slate-400 text-[11px] block">Pemohon (Corporate Buyer):</span>
                    <strong class="text-slate-900 block"><?= esc($rfq['buyer_name']) ?></strong>
                    <span class="text-slate-500 block">PIC: <?= esc($rfq['buyer_pic']) ?> (<?= esc($rfq['buyer_phone']) ?>)</span>
                </div>

                <div class="space-y-1 pt-3 border-t border-slate-100">
                    <span class="text-slate-400 text-[11px] block">Penyedia (Vendor Dituju):</span>
                    <strong class="text-purple-900 block"><?= esc($rfq['vendor_name'] ?? 'Terbuka Untuk Semua Mitra Resmi') ?></strong>
                </div>

                <div class="space-y-1 pt-3 border-t border-slate-100">
                    <span class="text-slate-400 text-[11px] block">Target Serah Terima:</span>
                    <strong class="text-slate-900 block"><?= date('d F Y', strtotime($rfq['target_delivery_date'])) ?></strong>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
