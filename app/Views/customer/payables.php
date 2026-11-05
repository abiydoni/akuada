<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-pink-100 text-pink-800 text-[10px] font-bold uppercase tracking-wider">Accounts Payable (AP) Korporat</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Daftar Hutang Tagihan TOP & Pembayaran ke AkuAda</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau jadwal jatuh tempo tagihan Purchase Order (PO) tempo Anda dan lakukan konfirmasi pelunasan ke rekening resmi AkuAda.</p>
        </div>
    </div>

    <?php
        $limit = (float)($company['credit_limit_top'] ?? 250000000);
        $used = (float)($company['credit_used_top'] ?? 0);
        $remaining = max(0, $limit - $used);
        $today = date('Y-m-d');
    ?>

    <!-- 4 Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-amber-50/80 via-white to-orange-50/50 rounded-2xl p-5 border-2 border-amber-300 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-amber-950 font-bold">Total Hutang Berjalan</span>
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
                    <span class="material-symbols-outlined text-xl">receipt_long</span>
                </div>
            </div>
            <div>
                <div class="text-xl font-black text-amber-900 font-mono"><?= format_rupiah($totalUnpaid) ?></div>
                <div class="text-[11px] text-amber-700 font-medium mt-0.5">Tagihan PO Belum Lunas</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-rose-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-rose-700 font-bold">Jatuh Tempo / Overdue</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">event_busy</span>
                </div>
            </div>
            <div>
                <div class="text-xl font-black text-rose-700 font-mono"><?= format_rupiah($totalOverdue) ?></div>
                <div class="text-[11px] text-rose-600 font-medium mt-0.5">Segera lakukan pelunasan</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-emerald-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-emerald-700 font-bold">Hutang Telah Dilunasi</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">task_alt</span>
                </div>
            </div>
            <div>
                <div class="text-xl font-black text-emerald-700 font-mono"><?= format_rupiah($totalPaid) ?></div>
                <div class="text-[11px] text-emerald-600 font-medium mt-0.5">Tercatat Lunas di AkuAda</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-purple-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-purple-900 font-bold">Sisa Plafon Kredit TOP</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">account_balance</span>
                </div>
            </div>
            <div>
                <div class="text-xl font-black text-purple-900 font-mono"><?= format_rupiah($remaining) ?></div>
                <div class="text-[11px] text-slate-500 font-medium mt-0.5">Pulih otomatis setelah verifikasi lunas</div>
            </div>
        </div>
    </div>

    <!-- Tabel Aging Hutang Buyer -->
    <?php
        $buyerAging = calculate_aging_summary($orders, 'buyer', !empty($company) ? [$company] : [], 'buyer_name');
    ?>
    <?= render_aging_table(
        $buyerAging,
        'Tabel Aging Hutang Tagihan TOP Perusahaan',
        'Ringkasan kondisi umur hutang berdasarkan kategori Current, 30 Hari, 60 Hari, 90 Hari, dan 120 Hari.',
        'Nama Perusahaan',
        'pink'
    ) ?>

    <!-- Payables Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-pink-600"></span>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Daftar Tagihan Hutang PO & Jadwal Jatuh Tempo</h3>
                    <p class="text-[11px] text-slate-500">Klik tombol "Bayar Tagihan" untuk mengisi konfirmasi transfer & upload bukti bayar ke rekening Escrow AkuAda.</p>
                </div>
            </div>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">No. Invoice & PO</th>
                        <th class="px-4 py-3.5">Vendor & Termin TOP</th>
                        <th class="px-4 py-3.5 text-center">Tanggal Jatuh Tempo</th>
                        <th class="px-4 py-3.5 text-right">Total Hutang Tagihan</th>
                        <th class="px-4 py-3.5 text-center">Status Pelunasan</th>
                        <th class="px-4 py-3.5 text-center w-48">Aksi Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                Belum ada tagihan hutang Purchase Order (PO) saat ini.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($orders as $o): ?>
                        <?php
                            $dueDate = $o['due_date'] ?: date('Y-m-d', strtotime($o['created_at'] . ' +30 days'));
                            $daysDiff = (int)floor((strtotime($dueDate) - strtotime($today)) / 86400);
                            $isPaid = ($o['payment_status'] === 'paid');
                            $isPending = ($o['payment_status'] === 'pending_approval');
                            $hasRejection = !empty($o['payment_rejection_reason']);
                            $invNo = $o['invoice_number'] ?? ('INV/AKUADA/' . date('Ym', strtotime($o['created_at'])) . '/' . str_pad($o['id'], 4, '0', STR_PAD_LEFT));
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-mono font-bold text-slate-900"><?= esc($invNo) ?></div>
                                <a href="<?= base_url('customer/orders/' . $o['id']) ?>" class="font-mono text-[11px] text-purple-700 hover:underline block mt-0.5">
                                    PO: <?= esc($o['po_number'] ?? $o['order_number']) ?>
                                </a>
                                <span class="text-[10px] text-slate-400 block">Terbit: <?= date('d/m/Y', strtotime($o['created_at'])) ?></span>
                            </td>

                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-800"><?= esc($o['vendor_name']) ?></div>
                                <div class="mt-1"><?= badge_payment_method($o['payment_method']) ?></div>
                            </td>

                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="font-mono font-bold text-slate-900"><?= date('d M Y', strtotime($dueDate)) ?></div>
                                <?php if ($isPaid): ?>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Lunas <?= !empty($o['buyer_paid_at']) ? date('d/m/Y', strtotime($o['buyer_paid_at'])) : '' ?>
                                    </span>
                                <?php elseif ($isPending): ?>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                        Menunggu Verifikasi Admin
                                    </span>
                                <?php elseif ($daysDiff < 0): ?>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                        Lewat <?= abs($daysDiff) ?> Hari (Overdue)
                                    </span>
                                <?php elseif ($daysDiff <= 7): ?>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        Jatuh Tempo <?= $daysDiff === 0 ? 'Hari Ini' : $daysDiff . ' Hari Lagi' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-600">
                                        Sisa <?= $daysDiff ?> Hari
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-slate-900 text-sm"><?= format_rupiah($o['total_amount']) ?></div>
                                <div class="text-[10px] text-slate-500">DPP: <?= format_rupiah($o['subtotal']) ?></div>
                            </td>

                            <td class="px-4 py-3.5 align-top text-center">
                                <?php if ($isPaid): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        <span class="material-symbols-outlined text-[13px]">check_circle</span> LUNAS TERVERIFIKASI
                                    </span>
                                    <div class="text-[10px] font-mono text-slate-500 mt-1">Ref: <?= esc($o['buyer_payment_ref'] ?? '-') ?></div>
                                <?php elseif ($isPending): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-900 border border-amber-300 animate-pulse">
                                        <span class="material-symbols-outlined text-[13px]">hourglass_top</span> MENUNGGU VERIFIKASI
                                    </span>
                                    <div class="text-[10px] font-mono text-slate-600 mt-1">Ref: <?= esc($o['buyer_payment_ref'] ?? '-') ?></div>
                                    <span class="text-[9px] text-slate-400 block"><?= esc($o['buyer_payment_bank'] ?? 'Rekening Escrow') ?></span>
                                <?php else: ?>
                                    <?php if ($hasRejection): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-300 mb-1">
                                            <span class="material-symbols-outlined text-[13px]">cancel</span> BUKTI DITOLAK
                                        </span>
                                        <div class="text-[10px] text-rose-700 bg-rose-50 p-1.5 rounded-lg border border-rose-200 text-left max-w-[160px] mx-auto leading-tight">
                                            <?= esc($o['payment_rejection_reason']) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-300">
                                            <span class="material-symbols-outlined text-[13px]">schedule</span> BELUM LUNAS
                                        </span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            <td class="px-4 py-3.5 align-top text-center">
                                <div class="flex flex-col gap-1.5">
                                    <?php if ($isPending): ?>
                                        <button type="button"
                                                onclick="viewPaymentProof(<?= htmlspecialchars(json_encode([
                                                    'po_number'    => $o['po_number'] ?? $o['order_number'],
                                                    'invoice_no'   => $invNo,
                                                    'amount'       => format_rupiah($o['total_amount']),
                                                    'sender_bank'  => $o['buyer_sender_bank'] ?? '-',
                                                    'sender_acc'   => ($o['buyer_sender_account_number'] ?? '-') . ' a/n ' . ($o['buyer_sender_account_name'] ?? '-'),
                                                    'target_bank'  => $o['buyer_payment_bank'] ?? '-',
                                                    'payment_ref'  => $o['buyer_payment_ref'] ?? '-',
                                                    'paid_date'    => !empty($o['buyer_paid_at']) ? (date('d M Y, H:i', strtotime($o['buyer_paid_at'])) . ' WIB') : '-',
                                                    'proof_url'    => $o['buyer_payment_proof'] ?? '',
                                                    'notes'        => $o['buyer_payment_notes'] ?? '-',
                                                ]), ENT_QUOTES, 'UTF-8') ?>)"
                                                class="w-full px-2.5 py-1.5 rounded-lg bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-300 font-bold text-xs inline-flex items-center justify-center gap-1 shadow-sm">
                                            <span class="material-symbols-outlined text-[15px]">visibility</span>
                                            <span>Lihat Bukti Bayar</span>
                                        </button>
                                    <?php elseif (!$isPaid): ?>
                                        <button type="button"
                                                onclick="openPayModal(<?= $o['id'] ?>, '<?= esc($o['po_number'] ?? $o['order_number']) ?>', '<?= esc(format_rupiah($o['total_amount'])) ?>', '<?= date('d M Y', strtotime($dueDate)) ?>', '<?= esc($invNo) ?>')"
                                                class="w-full px-3 py-1.5 rounded-lg bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-bold text-xs transition inline-flex items-center justify-center gap-1 shadow-sm">
                                            <span class="material-symbols-outlined text-[15px]">payments</span>
                                            <span><?= $hasRejection ? 'Upload Ulang Bukti' : 'Bayar Tagihan' ?></span>
                                        </button>
                                    <?php endif; ?>

                                    <div class="flex items-center gap-1">
                                        <a href="<?= base_url('customer/orders/invoice/' . $o['id']) ?>" target="_blank"
                                           class="flex-1 px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-[11px] transition inline-flex items-center justify-center gap-0.5 border border-slate-200">
                                            <span class="material-symbols-outlined text-[13px]">receipt_long</span>
                                            <span>Invoice</span>
                                        </a>
                                        <a href="<?= base_url('customer/orders/bast/' . $o['id']) ?>" target="_blank"
                                           class="flex-1 px-2 py-1 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 font-semibold text-[11px] transition inline-flex items-center justify-center gap-0.5 border border-purple-200">
                                            <span class="material-symbols-outlined text-[13px]">contract</span>
                                            <span>BAST</span>
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

<!-- Modal 1: Form Pembayaran Tagihan Buyer ke Rekening AkuAda -->
<div id="payDebtModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-4 text-xs my-8">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-pink-600 text-white flex items-center justify-center shadow-md">
                    <span class="material-symbols-outlined text-xl">payments</span>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900">Formulir Pelunasan Tagihan PO</h3>
                    <p class="text-[11px] text-slate-500">Konfirmasi transfer ke Rekening Escrow AkuAda</p>
                </div>
            </div>
            <button type="button" onclick="closePayModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form action="<?= base_url('customer/payables/pay') ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="order_id" id="modal_order_id">

            <!-- Summary Box -->
            <div class="p-4 rounded-2xl bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">No. PO & Invoice:</span>
                    <strong class="font-mono text-purple-900"><span id="modal_po_number"></span> / <span id="modal_invoice_no"></span></strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Jatuh Tempo:</span>
                    <strong id="modal_due_date" class="font-mono text-slate-800"></strong>
                </div>
                <div class="flex justify-between pt-1.5 border-t border-purple-200/80">
                    <span class="font-bold text-purple-950">Total Nominal Tagihan:</span>
                    <strong id="modal_amount" class="text-base font-black font-mono text-pink-700"></strong>
                </div>
            </div>

            <!-- Rekening Tujuan AkuAda -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Rekening Tujuan Resmi AkuAda (Escrow Account): <span class="text-rose-500">*</span></label>
                <select name="payment_bank" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white font-semibold">
                    <?php
                        $b1 = get_system_setting('inv_bank1_name', 'Bank Mandiri (Escrow)') . ' - ' . get_system_setting('inv_bank1_number', '122-00-1122334-5') . ' a/n ' . get_system_setting('inv_bank1_holder', 'AkuAda');
                        $b2 = get_system_setting('inv_bank2_name', 'Bank BCA (Escrow)') . ' - ' . get_system_setting('inv_bank2_number', '535-088-9900') . ' a/n ' . get_system_setting('inv_bank2_holder', 'AkuAda');
                        $b3 = get_system_setting('inv_bank3_name', 'Bank BNI Corporate VA') . ' - ' . get_system_setting('inv_bank3_number', '8809-1122-3344') . ' a/n ' . get_system_setting('inv_bank3_holder', 'AkuAda');
                    ?>
                    <option value="<?= esc($b1) ?>"><?= esc($b1) ?></option>
                    <option value="<?= esc($b2) ?>"><?= esc($b2) ?></option>
                    <option value="<?= esc($b3) ?>"><?= esc($b3) ?></option>
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Pastikan transfer dilakukan ke rekening resmi di atas.</p>
            </div>

            <!-- Data Bank Pengirim Buyer -->
            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Identitas Rekening Pengirim (Perusahaan Anda):</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Bank Pengirim *</label>
                        <select name="buyer_sender_bank" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white font-semibold">
                            <option value="Bank Mandiri">Bank Mandiri</option>
                            <option value="Bank BCA" selected>Bank Central Asia (BCA)</option>
                            <option value="Bank BNI">Bank Negara Indonesia (BNI)</option>
                            <option value="Bank BRI">Bank Rakyat Indonesia (BRI)</option>
                            <option value="Bank Permata">Bank Permata</option>
                            <option value="Bank CIMB Niaga">Bank CIMB Niaga</option>
                            <option value="Bank Syariah Indonesia">Bank Syariah Indonesia (BSI)</option>
                            <option value="Bank Lainnya">Bank Lainnya / Giro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">No. Rekening Pengirim (Opsional)</label>
                        <input type="text" name="buyer_sender_account_number" placeholder="Contoh: 535-099-1234 atau VA" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
                    </div>
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama Pemilik Rekening / Perusahaan</label>
                    <input type="text" name="buyer_sender_account_name" value="<?= esc($company['company_name'] ?? 'PT Corporate Buyer') ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-semibold">
                </div>
            </div>

            <!-- Detail Transaksi & Upload Bukti -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tanggal Transfer *</label>
                    <input type="date" name="transfer_date" required value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">No. Referensi / Mutasi Bank *</label>
                    <input type="text" name="payment_ref" required value="TRF-<?= date('Ymd') ?>-<?= rand(100, 999) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
                </div>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Unggah Foto / Berkas Bukti Transfer (Opsional):</label>
                <input type="file" name="payment_proof_file" accept="image/*,.pdf" class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:bg-purple-50 file:text-purple-700 file:font-bold hover:file:bg-purple-100">
                <p class="text-[10px] text-slate-400 mt-1">Tidak wajib — Anda tetap dapat konfirmasi tanpa melampirkan foto struk. Tim Finance AkuAda akan mencocokkan mutasi rekening bank.</p>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tambahan untuk Admin Finance:</label>
                <textarea name="buyer_payment_notes" rows="2" placeholder="Contoh: Pembayaran invoice PO lunas via kliring BI-FAST." class="w-full px-3 py-2 rounded-xl border border-slate-300"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closePayModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-bold shadow-md flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[17px]">send</span>
                    <span>Kirim Konfirmasi Pembayaran</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Lihat Detail Bukti Pembayaran yang Diajukan -->
<div id="viewProofModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-md w-full p-6 space-y-4 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600 text-xl">receipt</span>
                <h3 class="text-sm font-extrabold text-slate-900">Detail Konfirmasi Pembayaran</h3>
            </div>
            <button type="button" onclick="document.getElementById('viewProofModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <div class="space-y-2.5 p-4 rounded-2xl bg-purple-50/50 border border-purple-200">
            <div class="flex justify-between">
                <span class="text-slate-500">Nomor PO & Invoice:</span>
                <strong id="vp_po" class="font-mono text-purple-900"></strong>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Nominal Transfer:</span>
                <strong id="vp_amount" class="font-mono text-pink-700 text-sm font-black"></strong>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Waktu Kirim Konfirmasi:</span>
                <span id="vp_date" class="font-mono text-slate-800"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">No. Referensi Bank:</span>
                <strong id="vp_ref" class="font-mono text-slate-900"></strong>
            </div>
            <div class="flex justify-between pt-1 border-t border-purple-200">
                <span class="text-slate-500">Rekening Pengirim:</span>
                <span id="vp_sender" class="text-slate-800 text-right"></span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Tujuan Escrow:</span>
                <span id="vp_target" class="text-slate-800 text-right"></span>
            </div>
        </div>

        <div>
            <span class="block font-bold text-slate-700 mb-1.5">Lampiran Bukti Transfer / Resi:</span>
            <div id="vp_proof_container" class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50 max-h-48 flex items-center justify-center p-2">
                <img id="vp_img" src="" alt="Bukti Transfer" class="max-h-44 object-contain rounded-lg">
            </div>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="button" onclick="document.getElementById('viewProofModal').classList.add('hidden')" class="px-5 py-2 rounded-xl bg-slate-900 text-white font-bold">Tutup</button>
        </div>
    </div>
</div>

<script>
function openPayModal(orderId, poNumber, amount, dueDate, invoiceNo) {
    document.getElementById('modal_order_id').value = orderId;
    document.getElementById('modal_po_number').textContent = poNumber;
    document.getElementById('modal_invoice_no').textContent = invoiceNo || '-';
    document.getElementById('modal_amount').textContent = amount;
    document.getElementById('modal_due_date').textContent = dueDate;
    const modal = document.getElementById('payDebtModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closePayModal() {
    const modal = document.getElementById('payDebtModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
function viewPaymentProof(data) {
    document.getElementById('vp_po').textContent = data.po_number + ' / ' + data.invoice_no;
    document.getElementById('vp_amount').textContent = data.amount;
    document.getElementById('vp_date').textContent = data.paid_date;
    document.getElementById('vp_ref').textContent = data.payment_ref;
    document.getElementById('vp_sender').textContent = data.sender_bank + ' (' + data.sender_acc + ')';
    document.getElementById('vp_target').textContent = data.target_bank;
    document.getElementById('vp_img').src = data.proof_url || 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=600&q=80';
    const m = document.getElementById('viewProofModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}
</script>

<?= $this->endSection() ?>
