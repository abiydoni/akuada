<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<?php $today = date('Y-m-d'); ?>

<div class="space-y-6">
    <!-- Header -->
    <div>
        <div class="flex items-center gap-2">
            <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-wider">Accounts Receivable (AR) Vendor</span>
        </div>
        <h1 class="text-xl font-extrabold text-slate-900 mt-1">Daftar Piutang Settlement & Pencairan Dana dari AkuAda</h1>
        <p class="text-xs text-slate-500 mt-0.5">Pantau jadwal jatuh tempo pembayaran hak bersih penjualan Anda dari AkuAda beserta bukti transfer pencairannya.</p>
    </div>

    <!-- 3 Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 rounded-2xl p-5 border-2 border-blue-300 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-blue-950">Piutang Menunggu Pencairan AkuAda</span>
                <span class="material-symbols-outlined text-blue-600">schedule</span>
            </div>
            <div class="text-xl font-black font-mono text-blue-900"><?= format_rupiah($totalUnpaidNet) ?></div>
            <div class="text-[11px] text-slate-500 mt-0.5">Hak bersih + PPN yang belum dicairkan</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-rose-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-rose-700">Melewati Jatuh Tempo Settlement</span>
                <span class="material-symbols-outlined text-rose-600">event_busy</span>
            </div>
            <div class="text-xl font-black font-mono text-rose-700"><?= format_rupiah($totalOverdueNet) ?></div>
            <div class="text-[11px] text-rose-600 mt-0.5">Prioritas pencairan Finance AkuAda</div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold text-emerald-700">Total Dana Telah Cair ke Rekening</span>
                <span class="material-symbols-outlined text-emerald-600">payments</span>
            </div>
            <div class="text-xl font-black font-mono text-emerald-700"><?= format_rupiah($totalPaidNet) ?></div>
            <div class="text-[11px] text-emerald-600 mt-0.5">Lunas ditransfer oleh AkuAda</div>
        </div>
    </div>

    <!-- Tabel Aging Piutang Vendor (Nama Perusahaan, Current, 30, 60, 90, 120, Total) -->
    <?php
        $vendorAging = calculate_aging_summary($orders, 'vendor', [], 'buyer_name');
    ?>
    <?= render_aging_table(
        $vendorAging,
        'Tabel Aging Piutang Settlement per Perusahaan Buyer',
        'Ringkasan kondisi umur piutang penjualan PO berdasarkan kategori Current, 30 Hari, 60 Hari, 90 Hari, dan 120 Hari.',
        'Nama Perusahaan',
        'blue'
    ) ?>

    <!-- Vendor Bank Account Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-5 text-xs">
        <form action="<?= base_url('vendor/settlements/update-bank') ?>" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Bank Pencairan Vendor</label>
                <input type="text" name="bank_name" value="<?= esc($company['bank_name'] ?? 'BCA Corporate') ?>" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nomor Rekening Perusahaan</label>
                <input type="text" name="bank_account_number" value="<?= esc($company['bank_account_number'] ?? '8800-1122-3344') ?>" required class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Atas Nama Rekening Badan</label>
                <input type="text" name="bank_account_holder" value="<?= esc($company['bank_account_holder'] ?? ($company['company_name'] ?? '')) ?>" required class="w-full px-3 py-2 rounded-xl border border-slate-300">
            </div>
            <div>
                <button type="submit" class="w-full py-2 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                    <span class="material-symbols-outlined text-[16px]">save</span>
                    <span>Simpan Rekening Pencairan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Settlements Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                <h3 class="font-bold text-slate-900 text-sm">Daftar Tagihan Settlement Vendor ke AkuAda Beserta Jatuh Tempo</h3>
            </div>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px]">
                    <tr>
                        <th class="px-4 py-3.5">No. Faktur Vendor & PO</th>
                        <th class="px-4 py-3.5">Perusahaan Buyer</th>
                        <th class="px-4 py-3.5 text-center">Jatuh Tempo Pencairan</th>
                        <th class="px-4 py-3.5 text-right">Rincian Hak Bersih Vendor</th>
                        <th class="px-4 py-3.5 text-center">Status Pembayaran AkuAda</th>
                        <th class="px-4 py-3.5 text-center w-36">Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada tagihan piutang settlement.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($orders as $o): ?>
                        <?php
                            $fee = (float)($o['total_vendor_fee'] ?? 0);
                            $netDpp = (float)($o['vendor_net_payout'] ?? ($o['subtotal'] - $fee));
                            $totalReceive = $netDpp + (float)($o['ppn_amount'] ?? 0);
                            $vDue = $o['vendor_payout_due_date'] ?: date('Y-m-d', strtotime($o['created_at'] . ' +7 days'));
                            $vDays = (int)floor((strtotime($vDue) - strtotime($today)) / 86400);
                            $vPaid = (($o['vendor_payout_status'] ?? 'unpaid') === 'paid');
                            $vInv = $o['vendor_invoice_number'] ?? ('VINV/' . date('Ym', strtotime($o['created_at'])) . '/' . str_pad($o['id'], 4, '0', STR_PAD_LEFT));
                        ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-mono font-bold text-slate-900"><?= esc($vInv) ?></div>
                                <a href="<?= base_url('vendor/orders/' . $o['id']) ?>" class="font-mono text-[11px] text-blue-700 hover:underline block">
                                    PO: <?= esc($o['po_number'] ?? $o['order_number']) ?>
                                </a>
                            </td>
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-800"><?= esc($o['buyer_name']) ?></div>
                                <div class="mt-1"><?= badge_order_status($o['order_status']) ?></div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="font-mono font-bold text-slate-900"><?= date('d M Y', strtotime($vDue)) ?></div>
                                <?php if ($vPaid): ?>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                        Cair <?= !empty($o['vendor_paid_at']) ? date('d/m/Y', strtotime($o['vendor_paid_at'])) : '' ?>
                                    </span>
                                <?php elseif ($vDays < 0): ?>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Lewat <?= abs($vDays) ?> Hari</span>
                                <?php else: ?>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700">Sisa <?= $vDays ?> Hari</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-emerald-700 text-sm"><?= format_rupiah($totalReceive) ?></div>
                                <div class="text-[10px] text-slate-500">DPP: <?= format_rupiah($o['subtotal']) ?> | Fee: -<?= format_rupiah($fee) ?></div>
                                <div class="text-[10px] text-blue-700">+PPN: <?= format_rupiah($o['ppn_amount']) ?></div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-center">
                                <?php if ($vPaid): ?>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">LUNAS DICAIRKAN</span>
                                    <?php if (!empty($o['vendor_payout_ref'])): ?>
                                        <div class="text-[10px] font-mono text-slate-500 mt-1">Ref: <?= esc($o['vendor_payout_ref']) ?></div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">MENUNGGU PENCAIRAN</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 align-top text-center">
                                <a href="<?= base_url('vendor/orders/vendor-invoice/' . $o['id']) ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold text-xs inline-flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                                    <span>Faktur Tagihan</span>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

