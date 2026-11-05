<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('admin/dashboard') ?>" class="hover:text-purple-600">Admin</a>
                <span>/</span>
                <span class="text-slate-700 font-bold">Pencairan Cashback Buyer</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600 text-2xl">savings</span>
                Manajemen Pencairan Saldo Cashback Buyer
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Verifikasi dan proses penarikan saldo cashback resmi perusahaan pembeli (Corporate Buyer). Saldo buyer otomatis berkurang setelah penarikan disetujui.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/settings') ?>" class="px-3.5 py-2 rounded-xl bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 font-bold text-xs flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">tune</span> Atur % Fee Cashback (<?= esc(get_buyer_cashback_rate()) ?>%)
            </a>
            <a href="<?= base_url('admin/buyers') ?>" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 font-bold text-xs flex items-center gap-1.5 transition">
                <span class="material-symbols-outlined text-[16px]">corporate_fare</span> Kelola Akun Buyer
            </a>
        </div>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Card 1: Menunggu Persetujuan -->
        <div class="p-5 rounded-2xl bg-amber-50/80 border-2 border-amber-300 shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-amber-900 font-bold">
                <span>Menunggu Pencairan</span>
                <span class="px-2 py-0.5 rounded-full bg-amber-200 text-amber-900 text-[10px] font-extrabold"><?= $pendingCount ?> Pengajuan</span>
            </div>
            <div class="text-2xl font-black font-mono text-amber-950"><?= format_rupiah($totalPending) ?></div>
            <p class="text-[11px] text-amber-800">Permintaan transfer dana yang belum diproses</p>
        </div>

        <!-- Card 2: Total Berhasil Dicairkan -->
        <div class="p-5 rounded-2xl bg-emerald-50/80 border border-emerald-200 shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-emerald-900 font-bold">
                <span>Total Sudah Dicairkan</span>
                <span class="material-symbols-outlined text-emerald-600 text-xl">check_circle</span>
            </div>
            <div class="text-2xl font-black font-mono text-emerald-950"><?= format_rupiah($totalCompleted) ?></div>
            <p class="text-[11px] text-emerald-800">Dana cashback yang telah berhasil ditransfer ke buyer</p>
        </div>

        <!-- Card 3: Total Saldo Cashback Semua Buyer -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-slate-600 font-bold">
                <span>Saldo Mengendap di Buyer</span>
                <span class="material-symbols-outlined text-purple-600 text-xl">account_balance_wallet</span>
            </div>
            <div class="text-2xl font-black font-mono text-purple-900"><?= format_rupiah($totalCashbackAllBuyers) ?></div>
            <p class="text-[11px] text-slate-400">Total akumulasi cashback aktif seluruh mitra buyer resmi</p>
        </div>
    </div>

    <!-- Table of Withdrawals -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-slate-500">list_alt</span>
                <h3 class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Daftar Permintaan Penarikan Dana</h3>
            </div>
            <span class="text-xs text-slate-500 font-medium">Total <?= count($withdrawals) ?> Data Riwayat</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">No. Pengajuan & Tanggal</th>
                        <th class="px-4 py-3.5">Perusahaan Buyer</th>
                        <th class="px-4 py-3.5 text-right">Nominal Penarikan</th>
                        <th class="px-4 py-3.5">Rekening Tujuan Transfer</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-center w-36">Aksi Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($withdrawals)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400">
                                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">savings</span>
                                    <p class="font-bold text-slate-700">Belum Ada Permintaan Penarikan Dana</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Buyer terdaftar yang memiliki saldo cashback dapat mengajukan penarikan dari portal mereka.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($withdrawals as $w): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5">
                                <strong class="font-mono font-bold text-slate-900 block"><?= esc($w['withdrawal_number']) ?></strong>
                                <span class="text-[11px] text-slate-400 block"><?= date('d M Y, H:i', strtotime($w['created_at'])) ?> WIB</span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900"><?= esc($w['company_name'] ?: 'Perusahaan Pembeli') ?></div>
                                <div class="text-[11px] text-slate-500">PIC: <?= esc($w['user_name'] ?: '-') ?> (<?= esc($w['user_email'] ?: '-') ?>)</div>
                                <div class="text-[10px] text-purple-700 font-semibold mt-0.5">Saldo Saat Ini: <?= format_rupiah($w['cashback_balance'] ?? 0) ?></div>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="font-mono font-black text-sm text-slate-900"><?= format_rupiah($w['amount']) ?></div>
                                <?php if (!empty($w['buyer_notes'])): ?>
                                    <span class="text-[10px] text-slate-400 italic block mt-0.5" title="Catatan Buyer">"<?= esc($w['buyer_notes']) ?>"</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-800 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px] text-blue-600">account_balance</span>
                                    <?= esc($w['bank_name']) ?>
                                </div>
                                <div class="font-mono font-bold text-xs text-slate-900"><?= esc($w['bank_account_number']) ?></div>
                                <div class="text-[11px] text-slate-500">a/n <?= esc($w['bank_account_holder']) ?></div>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <?php if ($w['status'] === 'pending'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-extrabold border border-amber-200">
                                        <span class="material-symbols-outlined text-[13px] animate-spin">sync</span> Menunggu Verifikasi
                                    </span>
                                <?php elseif ($w['status'] === 'completed'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold border border-emerald-200">
                                        <span class="material-symbols-outlined text-[13px]">check_circle</span> Berhasil Dicairkan
                                    </span>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        Oleh: <?= esc($w['processed_by'] ?? 'Admin') ?><br>
                                        <?= date('d/m/Y H:i', strtotime($w['processed_at'] ?? $w['updated_at'])) ?>
                                    </div>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[10px] font-extrabold border border-rose-200">
                                        <span class="material-symbols-outlined text-[13px]">cancel</span> Ditolak
                                    </span>
                                    <?php if (!empty($w['admin_notes'])): ?>
                                        <div class="text-[10px] text-rose-700 mt-0.5 italic">"<?= esc($w['admin_notes']) ?>"</div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <?php if ($w['status'] === 'pending'): ?>
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                onclick="openApproveModal(<?= (int)$w['id'] ?>, '<?= esc($w['withdrawal_number']) ?>', <?= (float)$w['amount'] ?>, '<?= esc($w['company_name']) ?>', '<?= esc($w['bank_name']) ?>', '<?= esc($w['bank_account_number']) ?>', '<?= esc($w['bank_account_holder']) ?>')"
                                                class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-xs flex items-center gap-1 transition"
                                                title="Setujui dan Kurangi Saldo Buyer">
                                            <span class="material-symbols-outlined text-[14px]">check</span> Cairkan
                                        </button>
                                        <button type="button" 
                                                onclick="openRejectModal(<?= (int)$w['id'] ?>, '<?= esc($w['withdrawal_number']) ?>', <?= (float)$w['amount'] ?>, '<?= esc($w['company_name']) ?>')"
                                                class="px-2.5 py-1.5 rounded-lg bg-white hover:bg-rose-50 text-rose-700 border border-rose-300 font-bold text-[11px] shadow-xs flex items-center gap-1 transition"
                                                title="Tolak Pengajuan">
                                            <span class="material-symbols-outlined text-[14px]">close</span>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span class="text-[11px] text-slate-400 font-medium">Selesai</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Setujui & Cairkan Dana -->
<div id="approveModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-emerald-600 text-2xl">check_circle</span>
                <h3 class="font-extrabold text-slate-900 text-sm">Konfirmasi Pencairan Saldo Cashback</h3>
            </div>
            <button type="button" onclick="closeApproveModal()" class="text-slate-400 hover:text-slate-700">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/buyer-withdrawals/process') ?>" method="POST" class="space-y-4 text-xs">
            <?= csrf_field() ?>
            <input type="hidden" name="withdrawal_id" id="modalApproveId">
            <input type="hidden" name="action" value="approve">

            <div class="p-3.5 rounded-xl bg-purple-50/80 border border-purple-200 text-purple-950 space-y-1">
                <div class="flex justify-between items-center text-[11px]">
                    <span class="text-purple-700">No. Pengajuan:</span>
                    <strong class="font-mono font-bold" id="modalApproveNum">-</strong>
                </div>
                <div class="flex justify-between items-center text-[11px]">
                    <span class="text-purple-700">Perusahaan Pembeli:</span>
                    <strong class="font-bold" id="modalApproveCompany">-</strong>
                </div>
                <div class="flex justify-between items-center text-sm pt-1 border-t border-purple-200/60">
                    <span class="font-bold text-purple-900">Nominal Pencairan:</span>
                    <strong class="font-mono font-black text-purple-950" id="modalApproveAmount">Rp 0</strong>
                </div>
                <div class="text-[11px] text-purple-800 pt-1">
                    <strong>Rekening Tujuan:</strong> <span id="modalApproveBank">-</span>
                </div>
            </div>

            <div class="p-3 rounded-xl bg-amber-50 text-amber-900 border border-amber-200 text-[11px] flex items-start gap-2">
                <span class="material-symbols-outlined text-amber-600 text-lg mt-0.5">info</span>
                <p>
                    <strong>Perhatian:</strong> Setelah Anda menekan tombol konfirmasi di bawah, sistem akan <strong>secara otomatis memotong saldo cashback</strong> milik perusahaan buyer tersebut dan mencatat riwayat mutasi debit.
                </p>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Nomor Referensi Transfer / Bukti Bank: <span class="text-rose-500">*</span></label>
                <input type="text" name="transfer_proof" required placeholder="Contoh: TRF-BCA-20261105-9982" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono font-bold text-slate-900 focus:border-purple-600">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Catatan Admin (Opsional):</label>
                <input type="text" name="admin_notes" placeholder="Contoh: Dana telah ditransfer via Internet Banking Mandiri Escrow" 
                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-800 focus:border-purple-600">
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">verified</span> Konfirmasi Transfer Selesai & Potong Saldo
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tolak Penarikan -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-rose-600 text-2xl">cancel</span>
                <h3 class="font-extrabold text-slate-900 text-sm">Tolak Pengajuan Penarikan</h3>
            </div>
            <button type="button" onclick="closeRejectModal()" class="text-slate-400 hover:text-slate-700">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form action="<?= base_url('admin/buyer-withdrawals/process') ?>" method="POST" class="space-y-4 text-xs">
            <?= csrf_field() ?>
            <input type="hidden" name="withdrawal_id" id="modalRejectId">
            <input type="hidden" name="action" value="reject">

            <p class="text-slate-600">
                Apakah Anda yakin ingin menolak pengajuan penarikan <strong id="modalRejectNum">-</strong> sebesar <strong id="modalRejectAmount">Rp 0</strong> dari <strong id="modalRejectCompany">-</strong>? Saldo buyer tidak akan terpotong.
            </p>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Alasan Penolakan: <span class="text-rose-500">*</span></label>
                <textarea name="admin_notes" required rows="3" placeholder="Contoh: Nomor rekening bank tidak cocok dengan nama perusahaan pada NPWP/Legalitas PT."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-slate-800 focus:border-rose-600"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md">
                    Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function formatRupiah(num) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(num);
    }

    function openApproveModal(id, num, amount, company, bank, accNo, holder) {
        document.getElementById('modalApproveId').value = id;
        document.getElementById('modalApproveNum').textContent = num;
        document.getElementById('modalApproveCompany').textContent = company;
        document.getElementById('modalApproveAmount').textContent = formatRupiah(amount);
        document.getElementById('modalApproveBank').textContent = bank + ' ' + accNo + ' a/n ' + holder;
        document.getElementById('approveModal').classList.remove('hidden');
    }

    function closeApproveModal() {
        document.getElementById('approveModal').classList.add('hidden');
    }

    function openRejectModal(id, num, amount, company) {
        document.getElementById('modalRejectId').value = id;
        document.getElementById('modalRejectNum').textContent = num;
        document.getElementById('modalRejectCompany').textContent = company;
        document.getElementById('modalRejectAmount').textContent = formatRupiah(amount);
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
    }
</script>

<?= $this->endSection() ?>

