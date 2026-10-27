<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Permintaan Penawaran Resmi (RFQ) Saya</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau status tanggapan penawaran harga dari vendor dan setujui penawaran untuk menerbitkan PO.</p>
        </div>
        <a href="<?= base_url('rfq/create') ?>" class="btn-akuada-primary text-xs px-4 py-2">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>Buat Permintaan RFQ Baru</span>
        </a>
    </div>

    <div class="space-y-4">
        <?php foreach ($rfqs as $rfq): ?>
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4 text-xs">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-pink-700"><?= esc($rfq['rfq_number']) ?></span>
                            <span class="text-slate-300">•</span>
                            <strong class="text-slate-900 text-sm"><?= esc($rfq['title']) ?></strong>
                        </div>
                        <p class="text-slate-500 mt-0.5">Vendor Dituju: <strong><?= esc($rfq['vendor_name'] ?? 'Terbuka Untuk Semua Mitra') ?></strong> | Target Serah Terima: <?= date('d M Y', strtotime($rfq['target_delivery_date'])) ?></p>
                    </div>
                    <div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-800">
                            Status: <?= esc($rfq['status']) ?>
                        </span>
                    </div>
                </div>

                <!-- Items & Quotes Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-3">Nama Barang</th>
                                <th class="p-3">Kuantitas</th>
                                <th class="p-3">Target Harga Buyer</th>
                                <th class="p-3">Penawaran Resmi Vendor</th>
                                <th class="p-3">Catatan Spek</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php 
                                $itemModel = new \App\Models\RfqItemModel();
                                $items = $itemModel->where('rfq_id', $rfq['id'])->findAll();
                                foreach ($items as $it): 
                            ?>
                                <tr>
                                    <td class="p-3 font-semibold text-slate-800"><?= esc($it['item_name']) ?></td>
                                    <td class="p-3 font-bold"><?= $it['qty'] ?> <?= esc($it['uom']) ?></td>
                                    <td class="p-3 text-slate-500"><?= $it['target_unit_price'] ? format_rupiah($it['target_unit_price']) : '-' ?></td>
                                    <td class="p-3 font-extrabold text-pink-700">
                                        <?= $it['vendor_offered_price'] ? format_rupiah($it['vendor_offered_price']) : '<span class="text-amber-600 italic">Sedang Dihitung Vendor</span>' ?>
                                    </td>
                                    <td class="p-3 text-slate-500"><?= esc($it['notes'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($rfq['status'] === 'quoted'): ?>
                    <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div>
                            <strong class="text-emerald-900 block font-bold">Vendor Telah Mengirimkan Penawaran Resmi!</strong>
                            <span class="text-[11px] text-emerald-700">Harga penawaran telah diverifikasi memenuhi spesifikasi Anda. Anda dapat menyetujui penawaran ini.</span>
                        </div>
                        <a href="<?= base_url('customer/rfqs/accept/' . $rfq['id']) ?>" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition shadow-sm whitespace-nowrap">
                            Setujui & Terbitkan PO &rarr;
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?= $this->endSection() ?>
