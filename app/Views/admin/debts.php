<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<?php
    $activeTab = service('request')->getGet('tab') === 'vendor' ? 'vendor' : 'buyer';
    $statusFilter = service('request')->getGet('status') ?: '';
    $today = date('Y-m-d');
?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold uppercase tracking-wider">Treasury, AP & AR Control</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Manajemen Hutang Piutang TOP & Settlement Vendor</h1>
            <p class="text-xs text-slate-500 mt-0.5">Verifikasi bukti transfer pelunasan dari Corporate Buyer ke AkuAda serta pencairan hutang settlement ke Mitra Vendor.</p>
        </div>

        <!-- Tab Switcher -->
        <div class="inline-flex p-1 rounded-2xl bg-slate-200/80 border border-slate-300 self-start sm:self-auto">
            <a href="<?= base_url('admin/debts?tab=buyer') ?>"
               class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= $activeTab === 'buyer' ? 'bg-purple-600 text-white shadow' : 'text-slate-700 hover:text-slate-900' ?>">
                <span class="material-symbols-outlined text-[16px]">call_received</span>
                <span>1. Hutang Buyer ke AkuAda</span>
                <?php if (!empty($buyerPendingCount)): ?>
                    <span class="px-1.5 py-0.2 rounded-full bg-amber-400 text-slate-900 text-[10px] font-black"><?= $buyerPendingCount ?></span>
                <?php endif; ?>
            </a>
            <a href="<?= base_url('admin/debts?tab=vendor') ?>"
               class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= $activeTab === 'vendor' ? 'bg-blue-600 text-white shadow' : 'text-slate-700 hover:text-slate-900' ?>">
                <span class="material-symbols-outlined text-[16px]">call_made</span>
                <span>2. Hutang AkuAda ke Vendor</span>
            </a>
        </div>
    </div>

    <?php if ($activeTab === 'buyer'): ?>
        <!-- ================= TAB 1: HUTANG BUYER KE AKUADA (AR) ================= -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-purple-50 via-white to-pink-50 rounded-2xl p-5 border-2 border-purple-300 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-purple-950">Total Piutang Belum Lunas</span>
                    <span class="material-symbols-outlined text-purple-600">account_balance_wallet</span>
                </div>
                <div class="text-xl font-black font-mono text-purple-900"><?= format_rupiah($buyerUnpaidTotal) ?></div>
                <div class="text-[11px] text-slate-500 mt-0.5">Tagihan TOP Buyer berjalan</div>
            </div>

            <div class="bg-gradient-to-br from-amber-50 via-white to-orange-50 rounded-2xl p-5 border-2 border-amber-400 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-amber-950">Menunggu Verifikasi Pembayaran</span>
                    <span class="material-symbols-outlined text-amber-600">pending_actions</span>
                </div>
                <div class="text-xl font-black font-mono text-amber-900"><?= format_rupiah($buyerPendingTotal ?? 0) ?></div>
                <div class="text-[11px] text-amber-700 font-bold mt-0.5"><?= (int)($buyerPendingCount ?? 0) ?> Pembayaran Perlu Diverifikasi</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-rose-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-rose-700">Piutang Lewat Jatuh Tempo</span>
                    <span class="material-symbols-outlined text-rose-600">warning</span>
                </div>
                <div class="text-xl font-black font-mono text-rose-700"><?= format_rupiah($buyerOverdueTotal) ?></div>
                <div class="text-[11px] text-rose-600 mt-0.5">Melewati batas jatuh tempo TOP</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-700">Total Pelunasan Diverifikasi</span>
                    <span class="material-symbols-outlined text-emerald-600">verified</span>
                </div>
                <div class="text-xl font-black font-mono text-emerald-700"><?= format_rupiah($buyerPaidTotal) ?></div>
                <div class="text-[11px] text-emerald-600 mt-0.5">Dana masuk ke Escrow AkuAda</div>
            </div>
        </div>

        <!-- Tabel Aging Piutang -->
        <?= render_aging_table(
            $buyerAging ?? calculate_aging_summary($orders, 'buyer'),
            'Tabel Aging Piutang Buyer ke AkuAda (Accounts Receivable Aging)',
            'Kondisi umur piutang tagihan TOP per perusahaan buyer berdasarkan Current, 30 Hari, 60 Hari, 90 Hari, dan 120 Hari.',
            'Nama Perusahaan (Buyer)',
            'purple'
        ) ?>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Daftar Tagihan Hutang Buyer ke AkuAda & Verifikasi Pembayaran</h3>
                        <p class="text-[11px] text-slate-500">Cocokkan nomor referensi dan bukti transfer bank pengirim dengan mutasi rekening koran escrow AkuAda.</p>
                    </div>
                </div>

                <!-- Sub-filters -->
                <div class="flex items-center gap-1.5 text-xs">
                    <a href="<?= base_url('admin/debts?tab=buyer') ?>" 
                       class="px-3 py-1.5 rounded-xl font-bold transition <?= empty($statusFilter) ? 'bg-purple-100 text-purple-900 border border-purple-300' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Semua
                    </a>
                    <a href="<?= base_url('admin/debts?tab=buyer&status=pending') ?>" 
                       class="px-3 py-1.5 rounded-xl font-bold transition <?= $statusFilter === 'pending' ? 'bg-amber-100 text-amber-900 border border-amber-300' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Menunggu Verifikasi (<?= (int)($buyerPendingCount ?? 0) ?>)
                    </a>
                    <a href="<?= base_url('admin/debts?tab=buyer&status=unpaid') ?>" 
                       class="px-3 py-1.5 rounded-xl font-bold transition <?= $statusFilter === 'unpaid' ? 'bg-rose-100 text-rose-900 border border-rose-300' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Belum Lunas
                    </a>
                    <a href="<?= base_url('admin/debts?tab=buyer&status=paid') ?>" 
                       class="px-3 py-1.5 rounded-xl font-bold transition <?= $statusFilter === 'paid' ? 'bg-emerald-100 text-emerald-900 border border-emerald-300' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                        Lunas
                    </a>
                </div>
            </div>

            <div class="w-full">
                <table class="w-full text-xs text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px]">
                        <tr>
                            <th class="px-4 py-3.5">No. PO & Invoice</th>
                            <th class="px-4 py-3.5">Perusahaan Buyer</th>
                            <th class="px-4 py-3.5 text-center">Jatuh Tempo TOP</th>
                            <th class="px-4 py-3.5 text-right">Nominal Tagihan</th>
                            <th class="px-4 py-3.5 text-center">Status & Bukti Transfer</th>
                            <th class="px-4 py-3.5 text-center w-52">Aksi Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php
                            $filteredOrders = $orders;
                            if ($statusFilter === 'pending') {
                                $filteredOrders = array_filter($orders, fn($o) => ($o['payment_status'] === 'pending_approval'));
                            } elseif ($statusFilter === 'unpaid') {
                                $filteredOrders = array_filter($orders, fn($o) => in_array($o['payment_status'], ['unpaid', 'due', 'overdue']));
                            } elseif ($statusFilter === 'paid') {
                                $filteredOrders = array_filter($orders, fn($o) => ($o['payment_status'] === 'paid'));
                            }
                        ?>
                        <?php if (empty($filteredOrders)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">Tidak ada data tagihan sesuai filter.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($filteredOrders as $o): ?>
                            <?php
                                $dueDate = $o['due_date'] ?: date('Y-m-d', strtotime($o['created_at'] . ' +30 days'));
                                $daysDiff = (int)floor((strtotime($dueDate) - strtotime($today)) / 86400);
                                $isPaid = ($o['payment_status'] === 'paid');
                                $isPending = ($o['payment_status'] === 'pending_approval');
                                $hasRejection = !empty($o['payment_rejection_reason']);
                                $invNo = $o['invoice_number'] ?? ('INV/AKUADA/' . date('Ym', strtotime($o['created_at'])) . '/' . str_pad($o['id'], 4, '0', STR_PAD_LEFT));
                            ?>
                            <tr class="hover:bg-slate-50/80 transition-colors <?= $isPending ? 'bg-amber-50/30' : '' ?>">
                                <td class="px-4 py-3.5 align-top">
                                    <a href="<?= base_url('admin/orders/' . $o['id']) ?>" class="font-mono font-bold text-purple-700 hover:underline block">
                                        <?= esc($o['po_number'] ?? $o['order_number']) ?>
                                    </a>
                                    <span class="text-[10px] font-mono text-slate-500"><?= esc($invNo) ?></span>
                                    <div class="mt-1 flex items-center gap-1.5">
                                        <a href="<?= base_url('admin/orders/invoice-customer/' . $o['id']) ?>" target="_blank" class="text-[10px] text-purple-700 hover:underline flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[12px]">receipt_long</span> Invoice
                                        </a>
                                        <span class="text-slate-300">•</span>
                                        <a href="<?= base_url('admin/orders/bast/' . $o['id']) ?>" target="_blank" class="text-[10px] text-blue-700 hover:underline flex items-center gap-0.5">
                                            <span class="material-symbols-outlined text-[12px]">contract</span> BAST
                                        </a>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5 align-top">
                                    <div class="font-bold text-slate-900"><?= esc($o['buyer_name']) ?></div>
                                    <div class="text-[11px] text-slate-500">PIC: <?= esc($o['buyer_pic'] ?? '-') ?></div>
                                    <div class="mt-1"><?= badge_payment_method($o['payment_method']) ?></div>
                                </td>

                                <td class="px-4 py-3.5 align-top text-center">
                                    <div class="font-mono font-bold text-slate-900"><?= date('d M Y', strtotime($dueDate)) ?></div>
                                    <?php if ($isPaid): ?>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Lunas</span>
                                    <?php elseif ($isPending): ?>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 animate-pulse">Menunggu Verifikasi</span>
                                    <?php elseif ($daysDiff < 0): ?>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Lewat <?= abs($daysDiff) ?> Hari</span>
                                    <?php else: ?>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-800">Sisa <?= $daysDiff ?> Hari</span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-3.5 align-top text-right font-mono">
                                    <div class="font-black text-slate-900 text-sm"><?= format_rupiah($o['total_amount']) ?></div>
                                    <div class="text-[10px] text-slate-400">DPP: <?= format_rupiah($o['subtotal']) ?></div>
                                </td>

                                <td class="px-4 py-3.5 align-top text-center">
                                    <?php if ($isPaid): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                            <span class="material-symbols-outlined text-[13px]">check_circle</span> LUNAS TERVERIFIKASI
                                        </span>
                                        <div class="text-[10px] font-mono text-slate-500 mt-1"><?= esc($o['buyer_payment_ref'] ?? '') ?></div>
                                        <span class="text-[9px] text-slate-400 block"><?= !empty($o['buyer_paid_at']) ? date('d/m/Y H:i', strtotime($o['buyer_paid_at'])) : '' ?></span>
                                    <?php elseif ($isPending): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-400 shadow-sm">
                                            <span class="material-symbols-outlined text-[13px]">notification_important</span> BUTUH VERIFIKASI
                                        </span>
                                        <div class="text-[10px] font-mono text-slate-700 font-bold mt-1">Ref: <?= esc($o['buyer_payment_ref'] ?? '-') ?></div>
                                        <span class="text-[10px] text-slate-500 block"><?= esc($o['buyer_sender_bank'] ?? 'Bank') ?> &rarr; <?= esc($o['buyer_payment_bank'] ?? 'Escrow') ?></span>
                                    <?php else: ?>
                                        <?php if ($hasRejection): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300 mb-1">
                                                <span class="material-symbols-outlined text-[13px]">cancel</span> DITOLAK
                                            </span>
                                            <div class="text-[10px] text-rose-700 bg-rose-50 p-1 rounded border border-rose-200 text-left max-w-[150px] mx-auto leading-tight">
                                                <?= esc($o['payment_rejection_reason']) ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">BELUM DIBAYAR</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>

                                <td class="px-4 py-3.5 align-top text-center">
                                    <div class="flex flex-col gap-1.5">
                                        <?php if ($isPending): ?>
                                            <button type="button"
                                                    onclick="openVerifyModal(<?= htmlspecialchars(json_encode([
                                                        'id'           => $o['id'],
                                                        'po_number'    => $o['po_number'] ?? $o['order_number'],
                                                        'invoice_no'   => $invNo,
                                                        'buyer_name'   => $o['buyer_name'],
                                                        'amount'       => format_rupiah($o['total_amount']),
                                                        'sender_bank'  => $o['buyer_sender_bank'] ?? 'Bank Mandiri',
                                                        'sender_acc'   => ($o['buyer_sender_account_number'] ?? '-') . ' a/n ' . ($o['buyer_sender_account_name'] ?? '-'),
                                                        'target_bank'  => $o['buyer_payment_bank'] ?? 'Bank Mandiri (Escrow) AkuAda',
                                                        'payment_ref'  => $o['buyer_payment_ref'] ?? ('MUTASI-' . date('Ymd')),
                                                        'paid_date'    => !empty($o['buyer_paid_at']) ? (date('d M Y, H:i', strtotime($o['buyer_paid_at'])) . ' WIB') : '-',
                                                        'proof_url'    => $o['buyer_payment_proof'] ?? '',
                                                        'notes'        => $o['buyer_payment_notes'] ?? '-',
                                                    ]), ENT_QUOTES, 'UTF-8') ?>)"
                                                    class="w-full px-3 py-2 rounded-xl bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-800 hover:to-indigo-800 text-white font-extrabold text-xs transition inline-flex items-center justify-center gap-1.5 shadow-md shadow-purple-500/20">
                                                <span class="material-symbols-outlined text-[16px]">verified</span>
                                                <span>Verifikasi Bukti Bayar</span>
                                            </button>
                                        <?php elseif (!$isPaid): ?>
                                            <button type="button"
                                                    onclick="openConfirmBuyerModal(<?= $o['id'] ?>, '<?= esc($o['po_number'] ?? $o['order_number']) ?>', '<?= esc($o['buyer_name']) ?>', '<?= esc(format_rupiah($o['total_amount'])) ?>')"
                                                    class="w-full px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs transition inline-flex items-center justify-center gap-1 shadow-sm">
                                                <span class="material-symbols-outlined text-[15px]">payments</span>
                                                <span>Catat Pelunasan Manual</span>
                                            </button>
                                        <?php else: ?>
                                            <span class="px-2 py-1 rounded bg-slate-100 text-slate-500 text-[10px] font-semibold">Telah Terverifikasi</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>
        <!-- ================= TAB 2: HUTANG AKUADA KE VENDOR (AP SETTLEMENT) ================= -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 rounded-2xl p-5 border-2 border-blue-300 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-blue-950">Total Hutang Settlement ke Vendor</span>
                    <span class="material-symbols-outlined text-blue-600">outbox</span>
                </div>
                <div class="text-xl font-black font-mono text-blue-900"><?= format_rupiah($vendorUnpaidTotal) ?></div>
                <div class="text-[11px] text-slate-500 mt-0.5">Hak bersih vendor setelah dipotong fee platform</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-rose-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-rose-700">Settlement Lewat Jatuh Tempo</span>
                    <span class="material-symbols-outlined text-rose-600">warning</span>
                </div>
                <div class="text-xl font-black font-mono text-rose-700"><?= format_rupiah($vendorOverdueTotal) ?></div>
                <div class="text-[11px] text-rose-600 mt-0.5">Segera cairkan ke rekening vendor</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-700">Total Settlement Telah Dicairkan</span>
                    <span class="material-symbols-outlined text-emerald-600">verified</span>
                </div>
                <div class="text-xl font-black font-mono text-emerald-700"><?= format_rupiah($vendorPaidTotal) ?></div>
                <div class="text-[11px] text-emerald-600 mt-0.5">Telah ditransfer ke rekening vendor</div>
            </div>
        </div>

        <?= render_aging_table(
            $vendorAging ?? calculate_aging_summary($orders, 'vendor'),
            'Tabel Aging Hutang Settlement AkuAda ke Vendor',
            'Kondisi jadwal pencairan dana hak bersih vendor berdasarkan Current, 30 Hari, 60 Hari, 90 Hari, dan 120 Hari.',
            'Nama Mitra Vendor',
            'blue'
        ) ?>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <h3 class="font-bold text-slate-900 text-sm">Daftar Hutang Settlement AkuAda ke Vendor Beserta Rekening Pencairan</h3>
                </div>
            </div>

            <div class="w-full">
                <table class="w-full text-xs text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px]">
                        <tr>
                            <th class="px-4 py-3.5">No. PO & Faktur Vendor</th>
                            <th class="px-4 py-3.5">Mitra Vendor & Rekening</th>
                            <th class="px-4 py-3.5 text-center">Jatuh Tempo Settlement</th>
                            <th class="px-4 py-3.5 text-right">Rincian Fee & Hutang Bersih</th>
                            <th class="px-4 py-3.5 text-center">Status Pencairan</th>
                            <th class="px-4 py-3.5 text-center w-48">Aksi Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada data hutang settlement ke vendor.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($orders as $o): ?>
                            <?php
                                $fee = (float)($o['total_vendor_fee'] ?? 0);
                                $netDpp = (float)($o['vendor_net_payout'] ?? ($o['subtotal'] - $fee));
                                $totalPayableToVendor = $netDpp + (float)($o['ppn_amount'] ?? 0);
                                $vDue = $o['vendor_payout_due_date'] ?: date('Y-m-d', strtotime($o['created_at'] . ' +7 days'));
                                $vDays = (int)floor((strtotime($vDue) - strtotime($today)) / 86400);
                                $vPaid = (($o['vendor_payout_status'] ?? 'unpaid') === 'paid');
                                $vBank = ($o['vendor_bank_name'] ?: 'BCA Corporate') . ' - ' . ($o['vendor_bank_account_number'] ?: '8800-1122-3344') . ' a/n ' . ($o['vendor_bank_account_holder'] ?: $o['vendor_name']);
                            ?>
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3.5 align-top">
                                    <a href="<?= base_url('admin/orders/' . $o['id']) ?>" class="font-mono font-bold text-blue-700 hover:underline block">
                                        <?= esc($o['po_number'] ?? $o['order_number']) ?>
                                    </a>
                                    <span class="text-[10px] font-mono text-slate-500">Status Buyer: <?= strtoupper($o['payment_status']) ?></span>
                                </td>
                                <td class="px-4 py-3.5 align-top">
                                    <div class="font-bold text-slate-900"><?= esc($o['vendor_name']) ?></div>
                                    <div class="text-[11px] font-mono text-emerald-700 mt-0.5"><?= esc($vBank) ?></div>
                                </td>
                                <td class="px-4 py-3.5 align-top text-center">
                                    <div class="font-mono font-bold text-slate-900"><?= date('d M Y', strtotime($vDue)) ?></div>
                                    <?php if ($vPaid): ?>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">Dicairkan</span>
                                    <?php elseif ($vDays < 0): ?>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">Lewat <?= abs($vDays) ?> Hari</span>
                                    <?php else: ?>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-blue-50 text-blue-800">Sisa <?= $vDays ?> Hari</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 align-top text-right font-mono">
                                    <div class="font-black text-emerald-800 text-sm"><?= format_rupiah($totalPayableToVendor) ?></div>
                                    <div class="text-[10px] text-slate-500">DPPNet: <?= format_rupiah($netDpp) ?> | PPN: <?= format_rupiah($o['ppn_amount']) ?></div>
                                    <div class="text-[10px] text-purple-700">Potongan Fee AkuAda: <?= format_rupiah($fee) ?></div>
                                </td>
                                <td class="px-4 py-3.5 align-top text-center">
                                    <?php if ($vPaid): ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">LUNAS DICAIRKAN</span>
                                        <div class="text-[10px] font-mono text-slate-500 mt-1"><?= esc($o['vendor_payout_ref'] ?? '') ?></div>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">BELUM DIBAYAR</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 align-top text-center">
                                    <?php if (!$vPaid): ?>
                                        <button type="button"
                                                onclick="openPayVendorModal(<?= $o['id'] ?>, '<?= esc($o['po_number'] ?? $o['order_number']) ?>', '<?= esc($o['vendor_name']) ?>', '<?= esc(format_rupiah($totalPayableToVendor)) ?>', '<?= esc($vBank) ?>')"
                                                class="w-full px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition inline-flex items-center justify-center gap-1 shadow-sm">
                                            <span class="material-symbols-outlined text-[15px]">send_money</span>
                                            <span>Bayar ke Vendor</span>
                                        </button>
                                    <?php else: ?>
                                        <a href="<?= base_url('admin/orders/invoice-vendor/' . $o['id']) ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                                            <span>Faktur Settlement</span>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- ================= MODAL A: VERIFIKASI PEMBAYARAN MASUK BUYER (APPROVE / REJECT) ================= -->
<div id="verifyModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-2xl w-full p-6 sm:p-8 space-y-6 text-xs my-8 animate-in fade-in zoom-in-95 duration-150">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center shadow-md">
                    <span class="material-symbols-outlined text-xl">fact_check</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Verifikasi Pelunasan Piutang Corporate Buyer</h3>
                    <p class="text-[11px] text-slate-500">Pemeriksaan bukti transfer & mutasi rekening koran escrow</p>
                </div>
            </div>
            <button type="button" onclick="closeVerifyModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- 2 Columns: Summary Transfer & Bukti Resi -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Left: Transaction Info -->
            <div class="space-y-3">
                <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200 space-y-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-700 block">Rincian Dokumen & Nilai Tagihan:</span>
                    <div class="flex justify-between">
                        <span class="text-slate-500">No. PO & Invoice:</span>
                        <strong class="font-mono text-purple-900"><span id="vm_po"></span></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Perusahaan Buyer:</span>
                        <strong id="vm_buyer" class="text-slate-900 text-right"></strong>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-purple-200">
                        <span class="font-bold text-purple-950">Nominal Transfer:</span>
                        <strong id="vm_amount" class="font-mono text-base font-black text-purple-900"></strong>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5 leading-relaxed">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Jejak Mutasi Pengirim & Penerima:</span>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Bank Pengirim:</span>
                        <strong id="vm_sender_bank" class="text-slate-800"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Rekening Pengirim:</span>
                        <span id="vm_sender_acc" class="font-mono text-slate-800 text-right"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Rekening Tujuan:</span>
                        <span id="vm_target_bank" class="text-slate-800 text-right font-medium"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">No. Referensi Bank:</span>
                        <strong id="vm_ref" class="font-mono text-slate-900"></strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Waktu Bayar:</span>
                        <span id="vm_paid_date" class="font-mono text-slate-700"></span>
                    </div>
                </div>

                <div id="vm_notes_box" class="p-2.5 rounded-xl bg-slate-100 border border-slate-200 text-[11px] text-slate-600">
                    <span class="font-bold text-slate-700 block">Catatan Buyer:</span>
                    <p id="vm_notes" class="italic mt-0.5"></p>
                </div>
            </div>

            <!-- Right: Proof Slip Image -->
            <div class="space-y-2">
                <span class="font-bold text-slate-700 block">Lampiran Bukti Transfer / Resi Bank:</span>
                <div class="rounded-2xl border-2 border-slate-200 overflow-hidden bg-slate-100 flex items-center justify-center p-2 min-h-[220px]">
                    <img id="vm_proof_img" src="" alt="Bukti Transfer" class="max-h-64 max-w-full object-contain rounded-xl shadow-sm">
                </div>
                <a id="vm_proof_link" href="#" target="_blank" class="text-[11px] text-purple-700 hover:underline font-bold flex items-center justify-center gap-1 pt-1">
                    <span class="material-symbols-outlined text-[14px]">open_in_new</span>
                    <span>Buka Gambar Ukuran Penuh</span>
                </a>
            </div>
        </div>

        <!-- Action Form: Approve -->
        <form action="<?= base_url('admin/debts/confirm-buyer') ?>" method="POST" class="pt-4 border-t border-slate-200 space-y-4">
            <input type="hidden" name="order_id" id="vm_approve_order_id">
            <input type="hidden" name="payment_ref" id="vm_approve_payment_ref">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-emerald-50/60 p-4 rounded-2xl border border-emerald-200">
                <div>
                    <label class="block font-bold text-emerald-950 mb-1">Pilih Rekening Kas/Bank Penerima (COA GL) *</label>
                    <select name="bank_coa_id" required class="w-full px-3 py-2 rounded-xl border border-emerald-300 bg-white font-bold text-slate-800">
                        <?php if (!empty($bankAccounts)): ?>
                            <?php foreach ($bankAccounts as $ba): ?>
                                <option value="<?= $ba['id'] ?>">[<?= esc($ba['account_code']) ?>] <?= esc($ba['account_name']) ?> (Saldo: <?= format_rupiah($ba['ending_balance'] ?? 0) ?>)</option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="1">[111-002] Bank Mandiri (Escrow AkuAda)</option>
                            <option value="2">[111-003] Bank BCA (Escrow AkuAda)</option>
                        <?php endif; ?>
                    </select>
                    <p class="text-[10px] text-emerald-800 mt-1">Otomatis mencatat Jurnal Kas Masuk & mendebit rekening bank terpilih.</p>
                </div>

                <div>
                    <label class="block font-bold text-emerald-950 mb-1">Nama Rekening Bank Penerima AkuAda *</label>
                    <input type="text" name="payment_bank" id="vm_approve_payment_bank" required class="w-full px-3 py-2 rounded-xl border border-emerald-300 bg-white font-medium">
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-2">
                <button type="button" onclick="toggleRejectBox()" class="px-4 py-2 rounded-xl border border-rose-300 text-rose-700 hover:bg-rose-50 font-bold transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">cancel</span>
                    <span>Tolak Pembayaran Ini</span>
                </button>

                <div class="flex items-center gap-2">
                    <button type="button" onclick="closeVerifyModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Tutup</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold shadow-md flex items-center gap-1.5 transition">
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                        <span>Verifikasi Sah & Pulihkan Plafon TOP</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Hidden Reject Form Box -->
        <div id="vm_reject_box" class="hidden p-4 rounded-2xl bg-rose-50 border border-rose-200 space-y-3 animate-in fade-in duration-150">
            <h4 class="font-bold text-rose-900 text-xs flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px] text-rose-600">warning</span>
                <span>Form Penolakan Bukti Pembayaran</span>
            </h4>
            <form action="<?= base_url('admin/debts/reject-buyer') ?>" method="POST" class="space-y-3">
                <input type="hidden" name="order_id" id="vm_reject_order_id">
                <div>
                    <label class="block font-bold text-rose-800 mb-1">Alasan Penolakan (Akan tampil pada portal Buyer): *</label>
                    <textarea name="rejection_reason" rows="2" required placeholder="Contoh: Mutasi dana belum efektif di rekening koran Mandiri per hari ini, atau nominal transfer kurang dari invoice." class="w-full px-3.5 py-2 rounded-xl border border-rose-300"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="toggleRejectBox()" class="px-3.5 py-1.5 rounded-xl bg-white border border-rose-200 text-rose-700 font-bold">Batal</button>
                    <button type="submit" class="px-5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">send</span>
                        <span>Kirim Penolakan ke Buyer</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<!-- ================= MODAL B: CATAT PELUNASAN MANUAL OLEH ADMIN ================= -->
<div id="confirmBuyerModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-5 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900">Catat Pelunasan Tagihan Buyer (Manual)</h3>
            <button type="button" onclick="document.getElementById('confirmBuyerModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="<?= base_url('admin/debts/confirm-buyer') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="order_id" id="cb_order_id">
            <div class="p-4 rounded-2xl bg-purple-50 border border-purple-200 space-y-1">
                <div class="flex justify-between"><span>No. PO:</span><strong id="cb_po" class="font-mono"></strong></div>
                <div class="flex justify-between"><span>Buyer:</span><strong id="cb_buyer"></strong></div>
                <div class="flex justify-between pt-1 border-t border-purple-200"><span>Nominal Lunas:</span><strong id="cb_amount" class="font-mono text-sm text-purple-900"></strong></div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Rekening Kas/Bank Penerima AkuAda</label>
                <input type="text" name="payment_bank" value="Bank Mandiri (Escrow) AkuAda" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-semibold">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nomor Referensi Mutasi Bank</label>
                <input type="text" name="payment_ref" value="MUTASI-AR-<?= date('Ymd') ?>-<?= rand(100, 999) ?>" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-mono">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('confirmBuyerModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold">Simpan Lunas & Pulihkan TOP</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL C: PENCAIRAN HUTANG SETTLEMENT KE VENDOR ================= -->
<div id="payVendorModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-5 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900">Bayar Hutang Settlement ke Vendor</h3>
            <button type="button" onclick="document.getElementById('payVendorModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <form action="<?= base_url('admin/debts/pay-vendor') ?>" method="POST" class="space-y-4">
            <input type="hidden" name="order_id" id="pv_order_id">
            <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 space-y-1">
                <div class="flex justify-between"><span>No. PO:</span><strong id="pv_po" class="font-mono"></strong></div>
                <div class="flex justify-between"><span>Mitra Vendor:</span><strong id="pv_vendor"></strong></div>
                <div class="flex justify-between pt-1 border-t border-blue-200"><span>Total Pencairan:</span><strong id="pv_amount" class="font-mono text-sm text-emerald-800"></strong></div>
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Rekening Tujuan Vendor</label>
                <input type="text" name="payout_bank" id="pv_bank" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-mono">
            </div>
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nomor Referensi Bukti Transfer Settlement</label>
                <input type="text" name="payout_ref" value="SETTLE-<?= date('Ymd') ?>-<?= rand(100, 999) ?>" required class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-mono">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('payVendorModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-100 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold">Konfirmasi Transfer ke Vendor</button>
            </div>
        </form>
    </div>
</div>

<script>
function openVerifyModal(data) {
    document.getElementById('vm_approve_order_id').value = data.id;
    document.getElementById('vm_reject_order_id').value = data.id;
    document.getElementById('vm_po').textContent = data.po_number + ' / ' + data.invoice_no;
    document.getElementById('vm_buyer').textContent = data.buyer_name;
    document.getElementById('vm_amount').textContent = data.amount;
    document.getElementById('vm_sender_bank').textContent = data.sender_bank;
    document.getElementById('vm_sender_acc').textContent = data.sender_acc;
    document.getElementById('vm_target_bank').textContent = data.target_bank;
    document.getElementById('vm_ref').textContent = data.payment_ref;
    document.getElementById('vm_paid_date').textContent = data.paid_date;
    document.getElementById('vm_notes').textContent = data.notes;
    document.getElementById('vm_approve_payment_ref').value = data.payment_ref;
    document.getElementById('vm_approve_payment_bank').value = data.target_bank;

    const imgUrl = data.proof_url || 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80';
    document.getElementById('vm_proof_img').src = imgUrl;
    document.getElementById('vm_proof_link').href = imgUrl;

    document.getElementById('vm_reject_box').classList.add('hidden');
    const modal = document.getElementById('verifyModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeVerifyModal() {
    const modal = document.getElementById('verifyModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function toggleRejectBox() {
    document.getElementById('vm_reject_box').classList.toggle('hidden');
}

function openConfirmBuyerModal(id, po, buyer, amount) {
    document.getElementById('cb_order_id').value = id;
    document.getElementById('cb_po').textContent = po;
    document.getElementById('cb_buyer').textContent = buyer;
    document.getElementById('cb_amount').textContent = amount;
    const m = document.getElementById('confirmBuyerModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function openPayVendorModal(id, po, vendor, amount, bank) {
    document.getElementById('pv_order_id').value = id;
    document.getElementById('pv_po').textContent = po;
    document.getElementById('pv_vendor').textContent = vendor;
    document.getElementById('pv_amount').textContent = amount;
    document.getElementById('pv_bank').value = bank;
    const m = document.getElementById('payVendorModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}
</script>

<?= $this->endSection() ?>
