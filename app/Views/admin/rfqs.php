<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-extrabold text-slate-900">Pengawasan Permintaan Penawaran (RFQ)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau interaksi permintaan penawaran harga antara corporate buyer dan mitra vendor.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="p-4">No. RFQ</th>
                        <th class="p-4">Perusahaan Pembeli</th>
                        <th class="p-4">Judul Pengadaan</th>
                        <th class="p-4">Vendor</th>
                        <th class="p-4">Target Serah Terima</th>
                        <th class="p-4">Estimasi Budget</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($rfqs as $r): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 font-mono font-bold text-purple-700"><?= esc($r['rfq_number']) ?></td>
                            <td class="p-4 font-bold text-slate-900"><?= esc($r['buyer_name']) ?></td>
                            <td class="p-4 font-medium text-slate-800"><?= esc($r['title']) ?></td>
                            <td class="p-4 text-slate-600"><?= esc($r['vendor_name'] ?? 'Semua Vendor Terbuka') ?></td>
                            <td class="p-4 text-slate-500"><?= date('d M Y', strtotime($r['target_delivery_date'])) ?></td>
                            <td class="p-4 font-extrabold text-slate-900"><?= format_rupiah($r['total_estimate']) ?></td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-purple-100 text-purple-800">
                                    <?= esc($r['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
