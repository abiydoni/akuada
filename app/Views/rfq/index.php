<?= $this->extend('layouts/main') ?>

<?= $this->section('subcontent') ?>

<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600 text-3xl">request_quote</span>
                Permintaan Penawaran Resmi (RFQ) B2B
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Layanan negosiasi pengadaan tender, harga bertingkat volume besar, dan penawaran ber-kop surat resmi vendor.
            </p>
        </div>
        <a href="<?= base_url('rfq/create') ?>" class="btn-akuada-primary text-xs px-4 py-2.5">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>Buat Permintaan RFQ Baru</span>
        </a>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between text-xs">
            <span class="font-bold text-slate-700">Daftar Pengajuan Penawaran (<?= count($rfqs) ?> Dokumen)</span>
        </div>

        <?php if (empty($rfqs)): ?>
            <div class="p-12 text-center text-xs text-slate-500">
                Belum ada permintaan penawaran harga yang diajukan.
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-600 font-bold uppercase text-[10px] border-b border-slate-200">
                        <tr>
                            <th class="p-4">No. RFQ</th>
                            <th class="p-4">Perusahaan Pemohon</th>
                            <th class="p-4">Judul Pengadaan</th>
                            <th class="p-4">Vendor Dituju</th>
                            <th class="p-4">Target Kirim</th>
                            <th class="p-4">Estimasi Budget</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($rfqs as $rfq): ?>
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="p-4 font-mono font-bold text-purple-700"><?= esc($rfq['rfq_number']) ?></td>
                                <td class="p-4 font-semibold text-slate-800"><?= esc($rfq['buyer_name']) ?></td>
                                <td class="p-4 font-medium text-slate-900 max-w-xs truncate"><?= esc($rfq['title']) ?></td>
                                <td class="p-4 text-slate-600"><?= esc($rfq['vendor_name'] ?? 'Terbuka (Semua Vendor)') ?></td>
                                <td class="p-4 text-slate-500"><?= date('d M Y', strtotime($rfq['target_delivery_date'])) ?></td>
                                <td class="p-4 font-bold text-slate-800"><?= format_rupiah($rfq['total_estimate']) ?></td>
                                <td class="p-4">
                                    <?php 
                                        $badgeColor = 'bg-amber-100 text-amber-800';
                                        if ($rfq['status'] === 'quoted') $badgeColor = 'bg-blue-100 text-blue-800';
                                        if ($rfq['status'] === 'approved') $badgeColor = 'bg-emerald-100 text-emerald-800';
                                    ?>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase <?= $badgeColor ?>">
                                        <?= esc($rfq['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <a href="<?= base_url('rfq/' . $rfq['id']) ?>" class="px-3 py-1.5 rounded-lg border border-purple-300 text-purple-700 hover:bg-purple-50 font-bold transition">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
