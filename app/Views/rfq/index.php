<?= $this->extend('layouts/main') ?>

<?= $this->section('subcontent') ?>

<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600 text-3xl">request_quote</span>
                Permintaan Penawaran Resmi (RFQ) B2B
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Layanan negosiasi pengadaan tender, harga bertingkat volume besar, dan penawaran ber-kop surat resmi vendor.
            </p>
        </div>
        <a href="<?= base_url('rfq/create') ?>" class="btn-akuada-primary text-xs px-4 py-2.5 shadow-md flex items-center gap-2 self-start sm:self-auto">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>+ Buat Permintaan RFQ Baru</span>
        </a>
    </div>
</div>

<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- RFQs Table Card - Fits 100% width with NO horizontal scroll -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                <h3 class="font-bold text-slate-800 text-sm">Daftar Pengajuan Penawaran</h3>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px]"><?= count($rfqs) ?> Dokumen</span>
            </div>
        </div>

        <?php if (empty($rfqs)): ?>
            <div class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">request_quote</span>
                    <p class="font-semibold text-slate-600">Belum ada permintaan penawaran harga</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Klik tombol "+ Buat Permintaan RFQ Baru" untuk mengajukan tender pengadaan.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="w-full">
                <table class="w-full text-xs text-left border-collapse">
                    <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3.5 w-36">No. RFQ</th>
                            <th class="px-4 py-3.5">Judul Pengadaan & Perusahaan Pemohon</th>
                            <th class="px-4 py-3.5 w-60">Vendor & Target Kirim</th>
                            <th class="px-4 py-3.5 text-right w-44">Estimasi Budget</th>
                            <th class="px-4 py-3.5 text-center w-36">Status & Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php foreach ($rfqs as $rfq): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- No. RFQ -->
                                <td class="px-4 py-3.5 align-top">
                                    <a href="<?= base_url('rfq/' . $rfq['id']) ?>" 
                                       class="font-mono font-bold text-purple-700 hover:text-purple-900 hover:underline block text-xs">
                                        <?= esc($rfq['rfq_number']) ?>
                                    </a>
                                    <span class="text-[10px] text-slate-400 font-mono block mt-0.5"><?= date('d/m/Y', strtotime($rfq['created_at'])) ?></span>
                                </td>

                                <!-- Title & Buyer (No text cut-off) -->
                                <td class="px-4 py-3.5 align-top">
                                    <div class="font-bold text-slate-900 leading-snug break-words"><?= esc($rfq['title']) ?></div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 break-words">
                                        Pemohon: <strong class="text-slate-800"><?= esc($rfq['buyer_name']) ?></strong>
                                    </div>
                                </td>

                                <!-- Vendor & Target Date -->
                                <td class="px-4 py-3.5 align-top">
                                    <div class="font-medium text-slate-700 leading-snug break-words"><?= esc($rfq['vendor_name'] ?? 'Terbuka (Semua Vendor)') ?></div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                        Target: <?= date('d M Y', strtotime($rfq['target_delivery_date'])) ?>
                                    </div>
                                </td>

                                <!-- Budget -->
                                <td class="px-4 py-3.5 align-top text-right font-mono">
                                    <div class="font-black text-slate-900 text-sm"><?= format_rupiah($rfq['total_estimate']) ?></div>
                                </td>

                                <!-- Status & Action -->
                                <td class="px-4 py-3.5 align-top text-center">
                                    <div class="space-y-1.5">
                                        <?php 
                                            $badgeColor = 'bg-amber-50 text-amber-700 border-amber-200';
                                            if ($rfq['status'] === 'quoted') $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                                            if ($rfq['status'] === 'approved') $badgeColor = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                        ?>
                                        <div>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider border <?= $badgeColor ?> inline-block">
                                                <?= esc($rfq['status']) ?>
                                            </span>
                                        </div>
                                        <div>
                                            <a href="<?= base_url('rfq/' . $rfq['id']) ?>" 
                                               class="px-3 py-1 rounded-lg border border-purple-200 text-purple-700 hover:bg-purple-50 font-bold transition inline-flex items-center gap-1 shadow-sm text-xs">
                                                <span class="material-symbols-outlined text-[14px]">visibility</span>
                                                <span>Detail</span>
                                            </a>
                                        </div>
                                    </div>
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
