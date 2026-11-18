<?= $this->extend('layouts/main') ?>

<?= $this->section('subcontent') ?>

<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
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

<div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: RFQ Items (8 Cols) -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden space-y-6">
            <div class="p-6 pb-0">
                <h3 class="text-sm font-bold text-slate-800 pb-3 border-b border-slate-100">Daftar Spesifikasi & Penawaran Harga Barang</h3>
            </div>

            <div class="w-full p-6 pt-0">
                <table class="w-full text-xs text-left border-collapse">
                    <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                        <tr>
                            <th class="px-4 py-3.5">Nama Barang / Spesifikasi</th>
                            <th class="px-4 py-3.5 text-center w-28">Kuantitas</th>
                            <th class="px-4 py-3.5 text-right w-36">Target Buyer</th>
                            <th class="px-4 py-3.5 text-right w-44">Penawaran Vendor</th>
                            <th class="px-4 py-3.5 w-44">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php foreach ($rfq['items'] as $item): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3.5 align-top font-bold text-slate-900 leading-snug break-words"><?= esc($item['item_name']) ?></td>
                                <td class="px-4 py-3.5 align-top text-center whitespace-nowrap font-bold text-slate-800"><?= number_format($item['qty']) ?> <?= esc($item['uom']) ?></td>
                                <td class="px-4 py-3.5 align-top text-right whitespace-nowrap font-mono text-slate-600"><?= $item['target_unit_price'] ? format_rupiah($item['target_unit_price']) : '-' ?></td>
                                <td class="px-4 py-3.5 align-top text-right whitespace-nowrap font-mono font-bold text-purple-700">
                                    <?= $item['vendor_offered_price'] ? format_rupiah($item['vendor_offered_price']) : '<span class="text-amber-600 font-normal italic">Menunggu Quota</span>' ?>
                                </td>
                                <td class="px-4 py-3.5 align-top text-slate-500 break-words"><?= esc($item['notes'] ?? '-') ?></td>
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

            <?php if (!empty($rfq['design_specs']) || !empty($rfq['design_file_buyer'])): ?>
                <div class="p-6 pt-0">
                    <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200 space-y-2 text-xs">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <strong class="font-bold text-purple-950 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-purple-600 text-[18px]">palette</span>
                                Spesifikasi Desain & Lampiran Custom
                            </strong>
                            <?php if (($rfq['design_status'] ?? '') === 'forwarded_to_vendor'): ?>
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">✓ Telah Diteruskan AkuAda ke Vendor</span>
                            <?php else: ?>
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold">⏳ Sedang Diverifikasi Tim AkuAda</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($rfq['design_specs'])): ?>
                            <p class="text-slate-700 bg-white p-3 rounded-xl border border-purple-100 leading-relaxed"><?= nl2br(esc($rfq['design_specs'])) ?></p>
                        <?php endif; ?>
                        <?php
                            $currentRole = session('user_role') ?: (session('role') ?: 'customer');
                            $canSeeBuyerFile = in_array($currentRole, ['admin', 'customer']);
                            $fileToShow = $canSeeBuyerFile ? ($rfq['design_file_buyer'] ?? null) : (($rfq['design_status'] ?? '') === 'forwarded_to_vendor' ? ($rfq['design_file_vendor'] ?: $rfq['design_file_buyer']) : null);
                        ?>
                        <?php if (!empty($fileToShow)): ?>
                            <div class="pt-1">
                                <a href="<?= base_url($fileToShow) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-[11px] transition shadow-xs">
                                    <span class="material-symbols-outlined text-[15px]">download</span>
                                    <span>Lihat / Unduh File Desain</span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
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
