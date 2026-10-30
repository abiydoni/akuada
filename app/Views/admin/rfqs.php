<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold uppercase tracking-wider">Tender & Negosiasi</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Pengawasan Permintaan Penawaran (RFQ)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau interaksi permintaan penawaran harga antara corporate buyer dan mitra vendor (Tabel rfqs & rfq_items).</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-purple-50 text-purple-700 text-xs font-bold border border-purple-200">
                Total: <?= count($rfqs) ?> Permintaan
            </span>
        </div>
    </div>

    <!-- RFQs Table Card - Fits 100% width with NO horizontal scroll -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                <h3 class="font-bold text-slate-800 text-sm">Daftar Pengajuan RFQ B2B</h3>
            </div>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-36">No. RFQ & Tanggal</th>
                        <th class="px-4 py-3.5">Judul Pengadaan & Pihak Terkait</th>
                        <th class="px-4 py-3.5 text-center w-36">Target Serah Terima</th>
                        <th class="px-4 py-3.5 text-right w-40">Estimasi Budget</th>
                        <th class="px-4 py-3.5 text-center w-40">Status & Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($rfqs)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">request_quote</span>
                                    <p class="font-semibold text-slate-600">Belum ada pengajuan RFQ</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Permintaan penawaran dari buyer akan ditampilkan di sini.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($rfqs as $r): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- No. RFQ & Date -->
                            <td class="px-4 py-3.5 align-top">
                                <a href="<?= base_url('admin/rfqs/' . $r['id']) ?>" 
                                   class="font-mono font-bold text-purple-700 hover:text-purple-900 hover:underline flex items-center gap-1 group text-xs">
                                    <span><?= esc($r['rfq_number']) ?></span>
                                    <span class="material-symbols-outlined text-[13px] opacity-0 group-hover:opacity-100 transition">arrow_forward</span>
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono block mt-0.5"><?= date('d/m/Y', strtotime($r['created_at'])) ?></span>
                            </td>

                            <!-- Title, Buyer & Vendor (Full text, no cut-off) -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-900 leading-snug break-words"><?= esc($r['title']) ?></div>
                                <div class="text-[11px] text-slate-600 mt-0.5">
                                    Pemohon: <strong class="text-slate-800"><?= esc($r['buyer_name']) ?></strong>
                                </div>
                                <div class="text-[10px] text-purple-700 mt-0.5 font-medium">
                                    Vendor Dituju: <?= esc($r['vendor_name'] ?? 'Terbuka Untuk Semua Mitra') ?>
                                </div>
                            </td>

                            <!-- Target Delivery Date -->
                            <td class="px-4 py-3.5 align-top text-center font-mono text-slate-600">
                                <div class="font-semibold text-slate-800"><?= date('d M Y', strtotime($r['target_delivery_date'])) ?></div>
                                <div class="text-[10px] text-slate-400">Target Lokasi</div>
                            </td>

                            <!-- Budget -->
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-slate-900 text-sm"><?= format_rupiah($r['total_estimate']) ?></div>
                                <div class="text-[10px] text-slate-400">Estimasi Buyer</div>
                            </td>

                            <!-- Status & Action -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="space-y-1.5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-50 text-purple-800 border border-purple-200 inline-block">
                                        <?= esc($r['status']) ?>
                                    </span>
                                    <div>
                                        <a href="<?= base_url('admin/rfqs/' . $r['id']) ?>" 
                                           class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-purple-100 text-slate-700 hover:text-purple-800 font-bold text-[11px] transition inline-flex items-center gap-1 border border-slate-200"
                                           title="Lihat Rincian Item RFQ & Penawaran">
                                            <span class="material-symbols-outlined text-[14px]">visibility</span>
                                            <span>Rincian RFQ</span>
                                        </a>
                                    </div>
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
