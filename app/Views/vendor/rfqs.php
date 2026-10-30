<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<div class="space-y-6">
    <div>
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-wider">Tender & Quotation</span>
        </div>
        <h1 class="text-xl font-extrabold text-slate-900 mt-1">Permintaan Penawaran Harga (RFQ) Masuk</h1>
        <p class="text-xs text-slate-500 mt-0.5">Ajukan harga penawaran terbaik (Quotation) kepada perusahaan pembeli untuk memenangkan tender pengadaan.</p>
    </div>

    <div class="space-y-4">
        <?php if (empty($rfqs)): ?>
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-12 text-center">
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">price_change</span>
                    <p class="font-semibold text-slate-600">Belum ada permintaan RFQ yang masuk</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Permintaan penawaran harga dari corporate buyer akan tampil di sini.</p>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($rfqs as $rfq): ?>
            <form action="<?= base_url('vendor/rfqs/quote') ?>" method="POST" class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4 text-xs">
                <input type="hidden" name="rfq_id" value="<?= $rfq['id'] ?>">

                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="space-y-0.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono font-bold text-blue-700"><?= esc($rfq['rfq_number']) ?></span>
                            <span class="text-slate-300">•</span>
                            <strong class="text-slate-900 text-sm"><?= esc($rfq['title']) ?></strong>
                        </div>
                        <p class="text-slate-500">
                            Perusahaan Peminta: <strong><?= esc($rfq['buyer_name']) ?></strong> |
                            Target Kirim: <strong><?= date('d M Y', strtotime($rfq['target_delivery_date'])) ?></strong>
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-blue-100 text-blue-800 border border-blue-200">
                        Status: <?= esc($rfq['status']) ?>
                    </span>
                </div>

                <div class="w-full rounded-xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px]">
                            <tr>
                                <th class="px-4 py-3">Nama Barang & Spesifikasi</th>
                                <th class="px-4 py-3 text-center w-28">Qty</th>
                                <th class="px-4 py-3 text-right w-44">Target Harga Buyer</th>
                                <th class="px-4 py-3 text-right w-56">Input Harga Penawaran Vendor (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php
                                $itemModel = new \App\Models\RfqItemModel();
                                $items = $itemModel->where('rfq_id', $rfq['id'])->findAll();
                                foreach ($items as $it):
                            ?>
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900"><?= esc($it['item_name']) ?></div>
                                        <?php if (!empty($it['notes'])): ?>
                                            <div class="text-[11px] text-slate-500"><?= esc($it['notes']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold"><?= number_format($it['qty']) ?> <?= esc($it['uom']) ?></td>
                                    <td class="px-4 py-3 text-right font-mono text-slate-600">
                                        <?= $it['target_unit_price'] ? format_rupiah($it['target_unit_price']) : '-' ?>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <input type="hidden" name="item_id[]" value="<?= $it['id'] ?>">
                                        <input type="number" name="offered_price[]" value="<?= $it['vendor_offered_price'] ? (float)$it['vendor_offered_price'] : '' ?>" placeholder="Masukkan harga..." required class="w-full px-3 py-1.5 rounded-lg border border-blue-300 font-mono text-right">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition flex items-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">send</span>
                        <span>Kirim Penawaran Resmi (Quotation)</span>
                    </button>
                </div>
            </form>
        <?php endforeach; ?>
    </div>
</div>

<?= $this->endSection() ?>
