<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('admin/rfqs') ?>" class="hover:text-purple-600">Pengawasan RFQ</a>
                <span>/</span>
                <span class="font-mono text-purple-700 font-bold"><?= esc($rfq['rfq_number']) ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900"><?= esc($rfq['title']) ?></h1>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-full text-xs font-extrabold uppercase bg-purple-100 text-purple-900 border border-purple-200">
                Status: <?= esc($rfq['status']) ?>
            </span>
        </div>
    </div>

    <!-- RFQ Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs">
        <!-- Buyer -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1.5">
            <span class="text-[10px] font-extrabold text-purple-700 uppercase tracking-widest block">PEMOHON PENGADAAN (BUYER):</span>
            <strong class="text-base text-slate-900 block"><?= esc($rfq['buyer_name']) ?></strong>
            <p class="text-slate-500 font-mono">NPWP: <?= esc($rfq['buyer_npwp'] ?? '-') ?></p>
            <p class="text-slate-500">PIC: <?= esc($rfq['buyer_pic'] ?? '-') ?> (<?= esc($rfq['buyer_phone'] ?? '-') ?>)</p>
        </div>

        <!-- Vendor Target -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1.5">
            <span class="text-[10px] font-extrabold text-blue-700 uppercase tracking-widest block">VENDOR TUJUAN PENAWARAN:</span>
            <strong class="text-base text-slate-900 block"><?= esc($rfq['vendor_name'] ?? 'Terbuka Untuk Semua Mitra Vendor') ?></strong>
            <p class="text-slate-500">Target Tanggal Serah Terima:</p>
            <p class="font-bold text-slate-800 text-sm"><?= date('d F Y', strtotime($rfq['target_delivery_date'])) ?></p>
        </div>

        <!-- Budget & Action -->
        <div class="bg-gradient-to-br from-purple-50 to-blue-50 p-5 rounded-2xl border border-purple-200 shadow-sm space-y-3">
            <div>
                <span class="text-[10px] font-extrabold text-purple-900 uppercase tracking-widest block">TOTAL ESTIMASI ANGGARAN:</span>
                <strong class="text-lg font-black text-purple-900 block"><?= format_rupiah($rfq['total_estimate']) ?></strong>
            </div>

            <!-- Status Form -->
            <form action="<?= base_url('admin/rfqs/update-status') ?>" method="POST" class="space-y-2 pt-2 border-t border-purple-200">
                <input type="hidden" name="rfq_id" value="<?= $rfq['id'] ?>">
                <div class="flex items-center gap-2">
                    <select name="status" class="w-full text-xs py-1.5 px-2.5 rounded-xl border border-purple-300 bg-white font-bold">
                        <option value="submitted" <?= $rfq['status'] === 'submitted' ? 'selected' : '' ?>>Submitted (Diajukan)</option>
                        <option value="in_review" <?= $rfq['status'] === 'in_review' ? 'selected' : '' ?>>In Review (Ditinjau)</option>
                        <option value="quoted" <?= $rfq['status'] === 'quoted' ? 'selected' : '' ?>>Quoted (Vendor Beri Penawaran)</option>
                        <option value="approved" <?= $rfq['status'] === 'approved' ? 'selected' : '' ?>>Approved (Disetujui Buyer)</option>
                        <option value="rejected" <?= $rfq['status'] === 'rejected' ? 'selected' : '' ?>>Rejected (Ditolak)</option>
                        <option value="po_issued" <?= $rfq['status'] === 'po_issued' ? 'selected' : '' ?>>PO Issued (Diterbitkan PO)</option>
                    </select>
                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notes if any -->
    <?php if (!empty($rfq['notes'])): ?>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 text-xs space-y-1">
            <span class="font-bold text-slate-700 block">Catatan & Syarat Khusus Buyer:</span>
            <p class="text-slate-600 italic"><?= nl2br(esc($rfq['notes'])) ?></p>
        </div>
    <?php endif; ?>

    <!-- Table of RFQ Items (rfq_items table) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <span class="material-symbols-outlined text-purple-600">list_alt</span>
            Daftar Barang Permintaan Penawaran (Tabel rfq_items)
        </h3>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Nama Barang & Spesifikasi</th>
                        <th class="px-4 py-3.5 text-center w-28">Qty & Satuan</th>
                        <th class="px-4 py-3.5 text-right w-36">Target Buyer</th>
                        <th class="px-4 py-3.5 text-right w-44">Penawaran Vendor</th>
                        <th class="px-4 py-3.5 text-right w-40">Total Penawaran</th>
                        <th class="px-4 py-3.5 w-44">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($rfq['items'])): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400">Tidak ada item dalam RFQ ini.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($rfq['items'] as $item): ?>
                        <?php 
                            $offered = (float)($item['vendor_offered_price'] ?? 0);
                            $itemTotal = $offered > 0 ? ($offered * $item['qty']) : 0;
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 align-top font-bold text-slate-900 leading-snug break-words"><?= esc($item['item_name']) ?></td>
                            <td class="px-4 py-3.5 align-top text-center whitespace-nowrap font-bold text-slate-800"><?= number_format($item['qty']) ?> <?= esc($item['uom']) ?></td>
                            <td class="px-4 py-3.5 align-top text-right whitespace-nowrap font-mono text-slate-600">
                                <?= $item['target_unit_price'] ? format_rupiah($item['target_unit_price']) : '-' ?>
                            </td>
                            <td class="px-4 py-3.5 align-top text-right whitespace-nowrap font-mono font-bold <?= $offered > 0 ? 'text-emerald-700' : 'text-slate-400 italic' ?>">
                                <?= $offered > 0 ? format_rupiah($offered) : 'Belum Ditawar' ?>
                            </td>
                            <td class="px-4 py-3.5 align-top text-right whitespace-nowrap font-mono font-black text-slate-900 text-[13px]">
                                <?= $itemTotal > 0 ? format_rupiah($itemTotal) : '-' ?>
                            </td>
                            <td class="px-4 py-3.5 align-top text-slate-500 break-words">
                                <?= esc($item['notes'] ?? '-') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

