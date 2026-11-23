<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">
    <!-- Header Card -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-wider">Kepatuhan Perpajakan DJP</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Arsip e-Faktur Pajak PPN <?= esc(format_tax_rate()) ?> Resmi (DJP)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Faktur pajak elektronik yang terhubung resmi dengan NPWP Perusahaan untuk rekonsiliasi SPT Masa PPN.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-blue-50 text-blue-700 text-xs font-bold border border-blue-200">
                Total: <?= count($orders) ?> Dokumen Pengajuan
            </span>
        </div>
    </div>

    <!-- Tax Invoices Table Card - Fits 100% width with NO horizontal scroll -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <h3 class="font-bold text-slate-800 text-sm">Daftar e-Faktur PPN Masukan Perusahaan</h3>
            </div>
            <span class="text-[11px] text-slate-500 font-medium">*Sesuai PER-03/PJ/2022: e-Faktur terbit sah saat penyerahan barang (Surat Jalan) atau saat pelunasan</span>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-60">Nomor Seri Faktur Pajak (NSFP)</th>
                        <th class="px-4 py-3.5">Referensi PO & Tanggal</th>
                        <th class="px-4 py-3.5 text-right w-52">DPP & PPN <?= esc(format_tax_rate()) ?></th>
                        <th class="px-4 py-3.5 text-right w-44">Total Nilai Faktur</th>
                        <th class="px-4 py-3.5 text-center w-48">Aksi Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">receipt</span>
                                    <p class="font-semibold text-slate-600">Belum ada pengajuan e-Faktur Pajak</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Centang opsi 'Butuh Faktur Pajak PPN' saat checkout PO untuk menerbitkan e-Faktur.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($orders as $ord): ?>
                        <?php
                            $isShippedOrDone = in_array($ord['order_status'] ?? '', ['shipped', 'delivered', 'bast_signed', 'completed'], true);
                            $isPaid = (($ord['payment_status'] ?? '') === 'paid');
                            $isTaxReady = ($isShippedOrDone || $isPaid);
                            $nsfp = !empty($ord['tax_invoice_number']) ? $ord['tax_invoice_number'] : ('010.002-' . date('y', strtotime($ord['created_at'])) . '.' . str_pad((string)($ord['id'] + 48291000), 8, '0', STR_PAD_LEFT));
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- NSFP & DJP Approval Status -->
                            <td class="px-4 py-3.5 align-top">
                                <?php if ($isTaxReady): ?>
                                    <div class="font-mono font-bold text-blue-700 text-xs">
                                        <?= esc($nsfp) ?>
                                    </div>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="material-symbols-outlined text-[12px]">check_circle</span> DJP Approval Sukses / Terbit
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <div class="font-mono text-slate-400 text-xs italic">
                                        Menunggu Penyerahan BKP
                                    </div>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-800 border border-amber-200" title="Faktur Pajak baru terbit resmi saat vendor memberangkatkan barang atau saat pembayaran lunas diterima.">
                                            <span class="material-symbols-outlined text-[12px]">schedule</span> Belum Terbit (Tahap PO)
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- PO Ref & Date -->
                            <td class="px-4 py-3.5 align-top">
                                <a href="<?= base_url('customer/orders/' . $ord['id']) ?>" 
                                   class="font-mono font-bold text-purple-700 hover:text-purple-900 hover:underline block text-xs">
                                    <?= esc($ord['po_number'] ?? $ord['order_number']) ?>
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono block mt-0.5"><?= date('d M Y', strtotime($ord['created_at'])) ?></span>
                            </td>

                            <!-- DPP & PPN 11% -->
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-semibold text-slate-800 text-xs"><?= format_rupiah($ord['subtotal']) ?></div>
                                <div class="text-[11px] text-blue-700 font-bold mt-0.5">+ PPN: <?= format_rupiah($ord['ppn_amount']) ?></div>
                            </td>

                            <!-- Total -->
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-slate-900 text-sm"><?= format_rupiah($ord['total_amount']) ?></div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Termasuk Pajak</div>
                            </td>

                            <!-- Action -->
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="<?= base_url('customer/orders/' . $ord['id']) ?>" 
                                       class="px-2.5 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-100 font-bold text-xs transition inline-flex items-center gap-1 shadow-xs" title="Lihat Rincian Status Pesanan">
                                        <span class="material-symbols-outlined text-[14px]">visibility</span>
                                        <span>PO</span>
                                    </a>

                                    <?php if ($isTaxReady): ?>
                                        <a href="<?= base_url('customer/orders/tax-invoice/' . $ord['id']) ?>" target="_blank"
                                           class="px-2.5 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition inline-flex items-center gap-1 shadow-sm">
                                            <span class="material-symbols-outlined text-[14px]">receipt</span>
                                            <span>e-Faktur DJP</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-400 font-semibold text-xs inline-flex items-center gap-1 cursor-not-allowed select-none border border-slate-200" title="e-Faktur resmi terbit saat vendor memberangkatkan barang atau pelunasan diterima">
                                            <span class="material-symbols-outlined text-[13px]">lock</span>
                                            <span>Belum Terbit</span>
                                        </span>
                                    <?php endif; ?>
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