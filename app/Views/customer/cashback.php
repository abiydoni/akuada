<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">

    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('customer/dashboard') ?>" class="hover:text-pink-600">Portal Buyer</a>
                <span>/</span>
                <span class="text-slate-700 font-bold">Saldo Cashback & Penarikan</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-pink-600 text-2xl">savings</span>
                Saldo Cashback Pengadaan & Penarikan Dana
            </h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Reward cashback sebesar <strong><?= esc($cashbackRate) ?>%</strong> otomatis ditambahkan ke saldo perusahaan Anda setiap kali transaksi Purchase Order (PO) lunas terbayar.
            </p>
        </div>

        <?php if (!empty($company['is_verified'])): ?>
            <button type="button" onclick="openWithdrawModal()"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-extrabold text-xs shadow-md shadow-pink-600/25 flex items-center gap-2 transition cursor-pointer active:scale-95">
                <span class="material-symbols-outlined text-[18px]">payments</span>
                <span>Tarik Saldo Cashback</span>
            </button>
        <?php endif; ?>
    </div>

    <!-- Alert for Unregistered / Unverified Buyer -->
    <?php if (empty($company['is_verified'])): ?>
        <div class="p-4 rounded-2xl bg-amber-50 border-2 border-amber-300 text-xs text-amber-950 flex items-start gap-3 shadow-xs">
            <span class="material-symbols-outlined text-amber-600 text-2xl mt-0.5">lock</span>
            <div class="space-y-1">
                <strong class="font-extrabold text-amber-950 block text-sm">Akun Pembeli Belum Terverifikasi Resmi</strong>
                <p class="text-amber-900 leading-relaxed">
                    Sesuai ketentuan B2B AkuAda, fasilitas cashback transaksi dan penarikan saldo hanya diberikan kepada <strong>akun perusahaan resmi yang telah terdaftar & diverifikasi</strong> oleh Superadmin AkuAda. Hubungi CS AkuAda untuk verifikasi legalitas PT Anda.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Saldo Cashback Tersedia -->
        <div class="bg-gradient-to-br from-purple-900 via-indigo-900 to-slate-900 rounded-2xl p-5 text-white shadow-md space-y-2 flex flex-col justify-between">
            <div class="flex items-center justify-between text-xs">
                <span class="text-purple-200 font-bold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] text-pink-400">account_balance_wallet</span>
                    Saldo Cashback Tersedia
                </span>
                <span class="px-2 py-0.5 rounded-full bg-pink-500/30 text-pink-300 border border-pink-500/40 text-[10px] font-bold">Siap Ditarik</span>
            </div>
            <div>
                <div class="text-2xl font-black font-mono tracking-tight text-white"><?= format_rupiah($cashbackBalance) ?></div>
                <p class="text-[10px] text-slate-300 mt-0.5">Saldo aktif yang dapat dicairkan ke rekening bank</p>
            </div>
            <?php if (!empty($company['is_verified']) && $cashbackBalance > 0): ?>
                <button type="button" onclick="openWithdrawModal()" class="w-full mt-1 py-1.5 rounded-lg bg-pink-600 hover:bg-pink-700 text-white font-extrabold text-[11px] transition">
                    Cairkan Sekarang &rarr;
                </button>
            <?php endif; ?>
        </div>

        <!-- Card 2: Total Cashback Diterima -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-slate-500 font-semibold">
                <span>Total Cashback Diterima</span>
                <span class="material-symbols-outlined text-purple-600 text-xl">savings</span>
            </div>
            <div class="text-xl font-black font-mono text-slate-900"><?= format_rupiah($totalEarned) ?></div>
            <p class="text-[11px] text-purple-700 font-semibold">+<?= esc($cashbackRate) ?>% dari seluruh PO lunas</p>
        </div>

        <!-- Card 3: Total Dana Berhasil Dicairkan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-slate-500 font-semibold">
                <span>Total Dana Berhasil Ditarik</span>
                <span class="material-symbols-outlined text-emerald-600 text-xl">verified</span>
            </div>
            <div class="text-xl font-black font-mono text-emerald-700"><?= format_rupiah($totalWithdrawn) ?></div>
            <p class="text-[11px] text-slate-400">Telah ditransfer ke rekening perusahaan</p>
        </div>

        <!-- Card 4: Menunggu Persetujuan Admin -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-1">
            <div class="flex items-center justify-between text-xs text-slate-500 font-semibold">
                <span>Menunggu Pencairan Admin</span>
                <span class="material-symbols-outlined text-amber-600 text-xl">schedule</span>
            </div>
            <div class="text-xl font-black font-mono text-amber-700"><?= format_rupiah($pendingWithdrawal) ?></div>
            <p class="text-[11px] text-slate-400">Sedang diproses transfer oleh Finance</p>
        </div>
    </div>

    <!-- Tabs Container: Riwayat Penarikan Dana & Riwayat Mutasi Cashback -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" x-data="{ tab: 'withdrawals' }">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <button type="button" onclick="switchTab('withdrawals')" id="tabBtnWithdrawals"
                        class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-pink-600 text-white shadow-xs">
                    Riwayat Penarikan Dana (<?= count($withdrawals) ?>)
                </button>
                <button type="button" onclick="switchTab('mutations')" id="tabBtnMutations"
                        class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition text-slate-600 hover:bg-slate-200/70">
                    Buku Mutasi Saldo Cashback (<?= count($mutations) ?>)
                </button>
            </div>
            <span class="text-[11px] text-slate-400 font-medium">Rekening: <?= esc($company['bank_name'] ?: 'Belum diatur') ?> - <?= esc($company['bank_account_number'] ?: '-') ?></span>
        </div>

        <!-- Panel 1: Riwayat Penarikan Dana (Withdrawals) -->
        <div id="panelWithdrawals" class="overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">No. Penarikan & Tanggal</th>
                        <th class="px-4 py-3.5 text-right">Nominal Penarikan</th>
                        <th class="px-4 py-3.5">Rekening Bank Tujuan</th>
                        <th class="px-4 py-3.5 text-center">Status Pencairan</th>
                        <th class="px-4 py-3.5">Keterangan / Bukti Transfer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($withdrawals)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-400 space-y-1">
                                    <span class="material-symbols-outlined text-4xl text-slate-300">receipt_long</span>
                                    <p class="font-bold text-slate-700">Belum Ada Pengajuan Penarikan Dana</p>
                                    <p class="text-[11px] text-slate-400">Klik tombol "Tarik Saldo Cashback" di atas untuk mencairkan saldo Anda.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($withdrawals as $w): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5">
                                <strong class="font-mono font-bold text-slate-900 block"><?= esc($w['withdrawal_number']) ?></strong>
                                <span class="text-[11px] text-slate-400"><?= date('d M Y, H:i', strtotime($w['created_at'])) ?> WIB</span>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-black text-sm text-slate-900">
                                <?= format_rupiah($w['amount']) ?>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-800 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px] text-blue-600">account_balance</span>
                                    <?= esc($w['bank_name']) ?>
                                </div>
                                <div class="font-mono text-slate-900 text-xs"><?= esc($w['bank_account_number']) ?></div>
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
                                    <div class="text-[10px] text-slate-400 mt-1"><?= date('d/m/Y H:i', strtotime($w['processed_at'] ?? $w['updated_at'])) ?></div>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 text-[10px] font-extrabold border border-rose-200">
                                        <span class="material-symbols-outlined text-[13px]">cancel</span> Ditolak
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5">
                                <?php if (!empty($w['transfer_proof'])): ?>
                                    <div class="font-mono text-[11px] text-emerald-800 font-bold flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">receipt</span>
                                        Ref: <?= esc($w['transfer_proof']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($w['admin_notes'])): ?>
                                    <div class="text-[11px] text-slate-600 mt-0.5"><?= esc($w['admin_notes']) ?></div>
                                <?php endif; ?>
                                <?php if (empty($w['transfer_proof']) && empty($w['admin_notes'])): ?>
                                    <span class="text-slate-400 text-[11px]">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Panel 2: Buku Mutasi Saldo Cashback (Mutations) -->
        <div id="panelMutations" class="overflow-x-auto hidden">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">Tanggal & Waktu</th>
                        <th class="px-4 py-3.5">Deskripsi Transaksi</th>
                        <th class="px-4 py-3.5 text-center">Tipe</th>
                        <th class="px-4 py-3.5 text-right">Nominal</th>
                        <th class="px-4 py-3.5 text-right">Saldo Sesudah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($mutations)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-4xl text-slate-300 mb-1">history</span>
                                <p class="font-bold text-slate-700">Belum Ada Riwayat Mutasi Cashback</p>
                                <p class="text-[11px] text-slate-400">Cashback otomatis tercatat saat tagihan PO lunas diverifikasi.</p>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($mutations as $m): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                                <?= date('d M Y, H:i', strtotime($m['created_at'])) ?> WIB
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-slate-800">
                                <?= esc($m['description']) ?>
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <?php if ($m['type'] === 'credit'): ?>
                                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-extrabold">+ MASUK</span>
                                <?php else: ?>
                                    <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-800 text-[10px] font-extrabold">- KELUAR</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-bold <?= $m['type'] === 'credit' ? 'text-emerald-700' : 'text-rose-700' ?>">
                                <?= $m['type'] === 'credit' ? '+' : '-' ?><?= format_rupiah($m['amount']) ?>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-black text-slate-900">
                                <?= format_rupiah($m['balance_after']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Form Penarikan Saldo Cashback -->
<div id="withdrawModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-pink-600 text-2xl">payments</span>
                <h3 class="font-extrabold text-slate-900 text-sm">Ajukan Penarikan Saldo Cashback</h3>
            </div>
            <button type="button" onclick="closeWithdrawModal()" class="text-slate-400 hover:text-slate-700">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <form action="<?= base_url('customer/cashback/withdraw') ?>" method="POST" class="space-y-4 text-xs">
            <?= csrf_field() ?>

            <!-- Available Balance Info -->
            <div class="p-3.5 rounded-xl bg-purple-50/80 border border-purple-200 flex items-center justify-between">
                <div>
                    <span class="text-slate-500 text-[10px] block font-bold uppercase tracking-wider">Saldo Cashback Anda:</span>
                    <strong class="font-mono text-base font-black text-purple-950"><?= format_rupiah($cashbackBalance) ?></strong>
                </div>
                <span class="px-2 py-0.5 rounded bg-purple-200 text-purple-800 text-[10px] font-bold">Reward B2B</span>
            </div>

            <!-- Amount Input -->
            <div>
                <label class="block font-bold text-slate-700 mb-1">Nominal yang Ingin Ditarik (Rp): <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3.5 top-2.5 font-bold text-slate-400">Rp</span>
                    <input type="number" id="withdrawAmountInput" name="amount" required min="10000" max="<?= (float)$cashbackBalance ?>" step="1000"
                           placeholder="Minimal Rp 10.000"
                           class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 font-mono font-bold text-slate-900 focus:border-pink-600">
                </div>
                <!-- Quick Amount Buttons -->
                <div class="flex items-center gap-1.5 mt-2">
                    <button type="button" onclick="setQuickAmount(0.25)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold">25%</button>
                    <button type="button" onclick="setQuickAmount(0.50)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold">50%</button>
                    <button type="button" onclick="setQuickAmount(0.75)" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[10px] font-bold">75%</button>
                    <button type="button" onclick="setQuickAmount(1.00)" class="px-2.5 py-1 rounded-lg bg-pink-100 hover:bg-pink-200 text-pink-700 text-[10px] font-bold">Tarik Semua</button>
                </div>
            </div>

            <!-- Bank Destination Details -->
            <div class="space-y-3 pt-2 border-t border-slate-100">
                <h4 class="font-bold text-slate-800 text-xs flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px] text-blue-600">account_balance</span>
                    Rekening Bank Tujuan Pencairan
                </h4>

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Nama Bank: <span class="text-rose-500">*</span></label>
                    <input type="text" name="bank_name" required placeholder="Contoh: Bank Central Asia (BCA) / Bank Mandiri" 
                           value="<?= esc($company['bank_name'] ?? '') ?>"
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-900 focus:border-pink-600">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Nomor Rekening: <span class="text-rose-500">*</span></label>
                        <input type="text" name="bank_account_number" required placeholder="Contoh: 1234567890" 
                               value="<?= esc($company['bank_account_number'] ?? '') ?>"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-mono font-bold text-slate-900 focus:border-pink-600">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-600 mb-1">Nama Pemilik Rekening: <span class="text-rose-500">*</span></label>
                        <input type="text" name="bank_account_holder" required placeholder="Contoh: <?= esc($company['company_name'] ?? 'Nama PT') ?>" 
                               value="<?= esc($company['bank_account_holder'] ?: ($company['company_name'] ?? '')) ?>"
                               class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-semibold text-slate-900 focus:border-pink-600">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-600 mb-1">Catatan Tambahan (Opsional):</label>
                    <input type="text" name="buyer_notes" placeholder="Contoh: Keperluan operasional kas kecil PT" 
                           class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-slate-800 focus:border-pink-600">
                </div>
            </div>

            <p class="text-[10px] text-slate-400 leading-relaxed">
                *Penarikan dana akan diproses dan ditransfer oleh Finance AkuAda. Setelah penarikan berhasil disetujui, saldo cashback Anda akan otomatis berkurang.
            </p>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeWithdrawModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-extrabold text-xs shadow-md shadow-pink-600/20 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">send</span> Kirim Permintaan Penarikan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const maxBalance = <?= (float)$cashbackBalance ?>;

    function openWithdrawModal() {
        if (maxBalance <= 0) {
            AkuAdaAlert.error('Saldo Kosong', 'Saldo cashback Anda saat ini Rp 0. Lakukan pemesanan PO dan selesaikan pembayaran untuk mengumpulkan cashback!');
            return;
        }
        document.getElementById('withdrawModal').classList.remove('hidden');
    }

    function closeWithdrawModal() {
        document.getElementById('withdrawModal').classList.add('hidden');
    }

    function setQuickAmount(fraction) {
        const val = Math.floor(maxBalance * fraction);
        document.getElementById('withdrawAmountInput').value = val;
    }

    function switchTab(type) {
        const pWd = document.getElementById('panelWithdrawals');
        const pMt = document.getElementById('panelMutations');
        const bWd = document.getElementById('tabBtnWithdrawals');
        const bMt = document.getElementById('tabBtnMutations');

        if (type === 'withdrawals') {
            pWd.classList.remove('hidden');
            pMt.classList.add('hidden');
            bWd.className = 'tab-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-pink-600 text-white shadow-xs';
            bMt.className = 'tab-btn px-4 py-2 rounded-xl text-xs font-bold transition text-slate-600 hover:bg-slate-200/70';
        } else {
            pWd.classList.add('hidden');
            pMt.classList.remove('hidden');
            bMt.className = 'tab-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-pink-600 text-white shadow-xs';
            bWd.className = 'tab-btn px-4 py-2 rounded-xl text-xs font-bold transition text-slate-600 hover:bg-slate-200/70';
        }
    }
</script>

<?= $this->endSection() ?>

