<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold uppercase tracking-wider">Audit Trail Hukum</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Log Audit Status Transaksi B2B (Timeline History)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Seluruh riwayat perubahan status pesanan dan dokumen hukum serah terima (Tabel order_status_logs).</p>
        </div>
        <span class="px-3 py-1.5 rounded-xl bg-purple-50 text-purple-700 text-xs font-bold border border-purple-200">
            Total Log: <?= count($logs) ?> Catatan
        </span>
    </div>

    <!-- Status Logs Table Card - Fits 100% width with NO horizontal scroll -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                <h3 class="font-bold text-slate-800 text-sm">Riwayat Peristiwa & Perubahan Status</h3>
            </div>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-44">Waktu & Referensi PO</th>
                        <th class="px-4 py-3.5 w-64">Pihak Terkait (Buyer & Vendor)</th>
                        <th class="px-4 py-3.5 text-center w-44">Tahap & Status</th>
                        <th class="px-4 py-3.5">Keterangan / Aktivitas Lengkap</th>
                        <th class="px-4 py-3.5 text-center w-36">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">history</span>
                                    <p class="font-semibold text-slate-600">Belum ada catatan log aktivitas</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Setiap perubahan status PO dan BAST akan tercatat secara permanen di sini.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Timestamp & PO Ref -->
                            <td class="px-4 py-3.5 align-top">
                                <a href="<?= base_url('admin/orders/' . $log['order_id']) ?>" 
                                   class="font-mono font-bold text-purple-700 hover:text-purple-900 hover:underline flex items-center gap-1 group text-xs">
                                    <span><?= esc($log['po_number'] ?? $log['order_number']) ?></span>
                                    <span class="material-symbols-outlined text-[13px] opacity-0 group-hover:opacity-100 transition">arrow_forward</span>
                                </a>
                                <div class="font-mono text-slate-500 text-[11px] mt-0.5">
                                    <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                                </div>
                            </td>

                            <!-- Buyer & Vendor (Full text, no cut-off) -->
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-900 leading-snug break-words"><?= esc($log['buyer_name']) ?></div>
                                <div class="text-[11px] text-slate-500 mt-0.5 break-words">
                                    Vendor: <strong class="text-slate-700"><?= esc($log['vendor_name']) ?></strong>
                                </div>
                            </td>

                            <!-- Status & Title -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="space-y-1">
                                    <?= badge_order_status($log['status']) ?>
                                    <div class="text-[10px] text-slate-600 font-semibold"><?= esc($log['title']) ?></div>
                                </div>
                            </td>

                            <!-- Description (Full text, NO line-clamp cut-off!) -->
                            <td class="px-4 py-3.5 align-top text-slate-700 leading-relaxed break-words">
                                <?= esc($log['description'] ?? '-') ?>
                            </td>

                            <!-- Operator -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <span class="px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 text-[11px] font-mono font-semibold inline-block">
                                    <?= esc($log['updated_by']) ?>
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
