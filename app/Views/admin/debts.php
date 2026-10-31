<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<?php
    $activeTab = service('request')->getGet('tab') === 'vendor' ? 'vendor' : 'buyer';
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
            <p class="text-xs text-slate-500 mt-0.5">Kelola tagihan piutang dari Corporate Buyer ke AkuAda serta pembayaran hutang settlement dari AkuAda ke Mitra Vendor.</p>
        </div>

        <!-- Tab Switcher -->
        <div class="inline-flex p-1 rounded-2xl bg-slate-200/80 border border-slate-300 self-start sm:self-auto">
            <a href="<?= base_url('admin/debts?tab=buyer') ?>"
               class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 <?= $activeTab === 'buyer' ? 'bg-purple-600 text-white shadow' : 'text-slate-700 hover:text-slate-900' ?>">
                <span class="material-symbols-outlined text-[16px]">call_received</span>
                <span>1. Hutang Buyer ke AkuAda</span>
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
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-purple-50 via-white to-pink-50 rounded-2xl p-5 border-2 border-purple-300 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-purple-950">Total Piutang Buyer (Belum Lunas)</span>
                    <span class="material-symbols-outlined text-purple-600">account_balance_wallet</span>
                </div>
                <div class="text-xl font-black font-mono text-purple-900"><?= format_rupiah($buyerUnpaidTotal) ?></div>
                <div class="text-[11px] text-slate-500 mt-0.5">Tagihan TOP Buyer yang sedang berjalan</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-rose-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-rose-700">Piutang Lewat Jatuh Tempo (Overdue)</span>
                    <span class="material-symbols-outlined text-rose-600">warning</span>
                </div>
                <div class="text-xl font-black font-mono text-rose-700"><?= format_rupiah($buyerOverdueTotal) ?></div>
                <div class="text-[11px] text-rose-600 mt-0.5">Melewati tanggal jatuh tempo TOP</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-700">Total Pelunasan Diterima dari Buyer</span>
                    <span class="material-symbols-outlined text-emerald-600">verified</span>
                </div>
                <div class="text-xl font-black font-mono text-emerald-700"><?= format_rupiah($buyerPaidTotal) ?></div>
                <div class="text-[11px] text-emerald-600 mt-0.5">Dana masuk ke Escrow AkuAda</div>
            </div>
        </div>

        <!-- Tabel Aging Piutang (Nama Perusahaan, Current, 30, 60, 90, 120, Total) -->
        <?= render_aging_table(
            $buyerAging ?? calculate_aging_summary($orders, 'buyer'),
            'Tabel Aging Piutang Buyer ke AkuAda (Accounts Receivable Aging)',
            'Kondisi umur piutang tagihan TOP per perusahaan buyer berdasarkan Current, 30 Hari, 60 Hari, 90 Hari, dan 120 Hari.',
            'Nama Perusahaan (Buyer)',
            'purple'
        ) ?>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                    <h3 class="font-bold text-slate-900 text-sm">Daftar Hutang Tagihan Buyer ke AkuAda Beserta Jatuh Tempo</h3>
                </div>
            </div>

            <div class="w-full">
                <table class="w-full text-xs text-left border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px]">
                        <tr>
                            <th class="px-4 py-3.5">No. PO & Invoice</th>
                            <th class="px-4 py-3.5">Perusahaan Buyer</th>
                            <th class="px-4 py-3.5 text-center">Jatuh Tempo TOP</th>
                            <th class="px-4 py-3.5 text-right">Nominal Hutang Buyer</th>
                            <th class="px-4 py-3.5 text-center">Status & Bukti Bayar</th>
                            <th class="px-4 py-3.5 text-center w-44">Aksi Pelunasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada data tagihan hutang buyer.</td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($orders as $o): ?>
                            <?php
                                $dueDate = $o['due_date'] ?: date('Y-m-d', strtotime($o['created_at'] . ' +30 days'));
                                $daysDiff = (int)floor((strtotime($dueDate) - strtotime($today)) / 86400);
                                $isPaid = ($o['payment_status'] === 'paid');
                            ?>
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3.5 align-top">
                                    <a href="<?= base_url('admin/orders/' . $o['id']) ?>" class="font-mono font-bold text-purple-700 hover:underline block">
                                        <?= esc($o['po_number'] ?? $o['order_number']) ?>
                                    </a>
                                    <span class="text-[10px] font-mono text-slate-500"><?= esc($o['invoice_number'] ?? '-') ?></span>
                                </td>
                                <td class="px-4 py-3.5 align-top">
                                    <div class="font-bold text-slate-900"><?= esc($o['buyer_name']) ?></div>
                                    <div class="mt-1"><?= badge_payment_method($o['payment_method']) ?></div>
                                </td>
                                <td class="px-4 py-3.5 align-top text-center">
                                    <div class="font-mono font-bold text-slate-900"><?= date('d M Y', strtotime($dueDate)) ?></div>
                                    <?php if ($isPaid): ?>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">Lunas</span>
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
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">LUNAS</span>
                                        <div class="text-[10px] font-mono text-slate-500 mt-1"><?= esc($o['buyer_payment_ref'] ?? '') ?></div>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">BELUM LUNAS</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 align-top text-center">
                                    <?php if (!$isPaid): ?>
                                        <button type="button"
                                                onclick="openConfirmBuyerModal(<?= $o['id'] ?>, '<?= esc($o['po_number'] ?? $o['order_number']) ?>', '<?= esc($o['buyer_name']) ?>', '<?= esc(format_rupiah($o['total_amount'])) ?>')"
                                                class="w-full px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs transition inline-flex items-center justify-center gap-1 shadow-sm">
                                            <span class="material-symbols-outlined text-[15px]">payments</span>
                                            <span>Catat Pelunasan</span>
                                        </button>
                                    <?php else: ?>
                                        <a href="<?= base_url('admin/orders/invoice-customer/' . $o['id']) ?>" target="_blank" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs inline-flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">receipt_long</span>
                                            <span>Bukti Invoice</span>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php else: ?>
        <!-- ================= TAB 2: HUTANG AKUADA KE VENDOR (SETTLEMENT AP) ================= -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 rounded-2xl p-5 border-2 border-blue-300 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-blue-950">Total Hutang ke Vendor (Belum Cair)</span>
                    <span class="material-symbols-outlined text-blue-600">payments</span>
                </div>
                <div class="text-xl font-black font-mono text-blue-900"><?= format_rupiah($vendorUnpaidTotal) ?></div>
                <div class="text-[11px] text-slate-500 mt-0.5">Hak bersih vendor + PPN yang wajib dibayarkan AkuAda</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-rose-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-rose-700">Settlement Jatuh Tempo</span>
                    <span class="material-symbols-outlined text-rose-600">event_busy</span>
                </div>
                <div class="text-xl font-black font-mono text-rose-700"><?= format_rupiah($vendorOverdueTotal) ?></div>
                <div class="text-[11px] text-rose-600 mt-0.5">Segera cairkan ke rekening vendor</div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-700">Total Hutang ke Vendor Telah Dibayar</span>
                    <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                </div>
                <div class="text-xl font-black font-mono text-emerald-700"><?= format_rupiah($vendorPaidTotal) ?></div>
                <div class="text-[11px] text-emerald-600 mt-0.5">Telah ditransfer ke mitra supplier</div>
            </div>
        </div>

        <!-- Tabel Aging Hutang (Nama Perusahaan, Current, 30, 60, 90, 120, Total) -->
        <?= render_aging_table(
            $vendorAging ?? calculate_aging_summary($orders, 'vendor'),
            'Tabel Aging Hutang AkuAda ke Vendor (Accounts Payable Aging)',
            'Kondisi umur hutang settlement per perusahaan vendor berdasarkan Current, 30 Hari, 60 Hari, 90 Hari, dan 120 Hari.',
            'Nama Perusahaan (Vendor)',
            'blue'
        ) ?>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <h3 class="font-bold text-slate-900 text-sm">Daftar Hutang Settlement AkuAda ke Vendor Beserta Jatuh Tempo</h3>
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

<!-- Modal 1: Konfirmasi Pelunasan Buyer ke AkuAda -->
<div id="confirmBuyerModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-5 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-extrabold text-slate-900">Verifikasi Pelunasan Hutang Buyer</h3>
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
                <label class="block font-bold text-slate-700 mb-1">Rekening Penerima AkuAda</label>
                <input type="text" name="payment_bank" value="Bank Mandiri Escrow AkuAda" class="w-full px-3.5 py-2 rounded-xl border border-slate-300">
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

<!-- Modal 2: Pembayaran Hutang AkuAda ke Vendor -->
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

