<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">
    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-pink-100 text-pink-800 text-[10px] font-bold uppercase tracking-wider">Tender & Negosiasi</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Permintaan Penawaran Resmi (RFQ) Saya</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau status tanggapan penawaran harga dari vendor dan setujui penawaran untuk menerbitkan PO.</p>
        </div>
        <a href="<?= base_url('rfq/create') ?>" class="btn-akuada-primary text-xs px-4 py-2.5 shadow-md flex items-center gap-2 self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>+ Buat Permintaan RFQ Baru</span>
        </a>
    </div>

    <!-- RFQs List Cards -->
    <div class="space-y-4">
        <?php if (empty($rfqs)): ?>
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-12 text-center">
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">request_quote</span>
                    <p class="font-semibold text-slate-600">Belum ada permintaan penawaran RFQ yang diajukan</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol "+ Buat Permintaan RFQ Baru" untuk meminta penawaran tender pengadaan.</p>
                </div>
            </div>
        <?php endif; ?>

        <?php foreach ($rfqs as $rfq): ?>
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4 text-xs">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                    <div class="space-y-0.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono font-bold text-pink-700"><?= esc($rfq['rfq_number']) ?></span>
                            <span class="text-slate-300">•</span>
                            <strong class="text-slate-900 text-sm leading-snug break-words"><?= esc($rfq['title']) ?></strong>
                        </div>
                        <p class="text-slate-500">
                            Vendor Dituju: <strong><?= esc($rfq['vendor_name'] ?? 'Terbuka Untuk Semua Mitra') ?></strong> | 
                            Target Serah Terima: <strong><?= date('d M Y', strtotime($rfq['target_delivery_date'])) ?></strong>
                        </p>
                    </div>
                    <div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-100 text-purple-800 border border-purple-200">
                            Status: <?= esc($rfq['status']) ?>
                        </span>
                    </div>
                </div>

                <!-- Items & Quotes Table - Fits 100% width with NO horizontal scroll -->
                <div class="w-full rounded-xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Nama Barang & Spesifikasi</th>
                                <th class="px-4 py-3 text-center w-28">Kuantitas</th>
                                <th class="px-4 py-3 text-right w-44">Target Harga Buyer</th>
                                <th class="px-4 py-3 text-right w-52">Penawaran Resmi Vendor</th>
                                <th class="px-4 py-3 w-48">Catatan Spek</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php 
                                $itemModel = new \App\Models\RfqItemModel();
                                $items = $itemModel->where('rfq_id', $rfq['id'])->findAll();
                                foreach ($items as $it): 
                            ?>
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-4 py-3 align-middle font-bold text-slate-900 leading-snug break-words">
                                        <?= esc($it['item_name']) ?>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-center font-bold text-slate-800 whitespace-nowrap">
                                        <?= number_format($it['qty']) ?> <?= esc($it['uom']) ?>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-right font-mono text-slate-600 whitespace-nowrap">
                                        <?= $it['target_unit_price'] ? format_rupiah($it['target_unit_price']) : '-' ?>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-right font-mono font-bold text-pink-700 whitespace-nowrap">
                                        <?= $it['vendor_offered_price'] ? format_rupiah($it['vendor_offered_price']) : '<span class="text-amber-600 italic font-sans font-normal">Sedang Dihitung</span>' ?>
                                    </td>
                                    <td class="px-4 py-3 align-middle text-slate-500 break-words">
                                        <?= esc($it['notes'] ?? '-') ?>
                                    </td>
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
