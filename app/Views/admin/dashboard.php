<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-purple-800 via-indigo-800 to-slate-900 rounded-2xl p-6 text-white shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-purple-500/30 text-purple-200 text-[11px] font-bold border border-purple-400/30">Superadmin Platform</span>
            <h1 class="text-2xl font-extrabold mt-1">Dashboard Pengawasan Ekosistem B2B AkuAda</h1>
            <p class="text-xs text-purple-200 mt-0.5">Pemantauan transaksi PO korporat, kepatuhan e-Faktur PPN 11%, dan plafon kredit TOP.</p>
        </div>
        <a href="<?= base_url('admin/settings') ?>" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs transition flex items-center gap-1.5">
            <span class="material-symbols-outlined text-[16px]">tune</span>
            <span>Setting Pajak & Desain</span>
        </a>
    </div>

    <!-- Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">account_balance_wallet</span>
            </div>
            <div>
                <span class="text-[11px] text-slate-500 font-semibold block">Total GMV Pengadaan</span>
                <span class="text-base font-extrabold text-slate-900"><?= format_rupiah($totalGmv) ?></span>
                <span class="text-[10px] text-emerald-600 font-bold block mt-0.5">↑ 14.2% Bulan Ini</span>
            </div>
        </div>

        <div class="bg-gradient-to-br from-emerald-500/10 via-purple-500/10 to-pink-500/10 rounded-2xl p-5 border-2 border-emerald-300 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-md">
                <span class="material-symbols-outlined text-2xl">monetization_on</span>
            </div>
            <div>
                <span class="text-[11px] text-emerald-950 font-bold block">Fee Platform AkuAda</span>
                <span class="text-base font-black text-emerald-900"><?= format_rupiah($totalPlatformFee) ?></span>
                <span class="text-[10px] text-purple-800 font-bold block mt-0.5">Komisi Vendor Standar <?= get_default_vendor_fee_rate() ?>%</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">receipt_long</span>
            </div>
            <div>
                <span class="text-[11px] text-slate-500 font-semibold block">Purchase Order (PO)</span>
                <span class="text-base font-extrabold text-slate-900"><?= $totalOrders ?> Transaksi</span>
                <span class="text-[10px] text-blue-600 font-bold block mt-0.5">Semua Ber-Faktur Pajak</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">corporate_fare</span>
            </div>
            <div>
                <span class="text-[11px] text-slate-500 font-semibold block">Corporate Buyers</span>
                <span class="text-base font-extrabold text-slate-900"><?= $totalBuyers ?> Perusahaan</span>
                <span class="text-[10px] text-pink-600 font-bold block mt-0.5">Plafon TOP Aktif</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">store</span>
            </div>
            <div>
                <span class="text-[11px] text-slate-500 font-semibold block">Mitra Supplier</span>
                <span class="text-base font-extrabold text-slate-900"><?= $totalVendors ?> Vendor</span>
                <span class="text-[10px] text-indigo-600 font-bold block mt-0.5">Hak: <?= format_rupiah($totalVendorPayout) ?></span>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Transaksi & Purchase Order (PO) Terbaru</h3>
                <p class="text-xs text-slate-500">Daftar PO yang diterbitkan corporate buyer kepada mitra vendor.</p>
            </div>
            <a href="<?= base_url('admin/transactions') ?>" class="text-xs font-bold text-purple-700 hover:text-purple-900">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="p-4">No. PO</th>
                        <th class="p-4">Perusahaan Pembeli</th>
                        <th class="p-4">Vendor</th>
                        <th class="p-4">Metode Bayar</th>
                        <th class="p-4">Total Nilai Tagihan</th>
                        <th class="p-4">Status Pengadaan</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($recentOrders as $ord): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 font-mono font-bold text-purple-700"><?= esc($ord['po_number'] ?? $ord['order_number']) ?></td>
                            <td class="p-4 font-bold text-slate-800"><?= esc($ord['buyer_name']) ?></td>
                            <td class="p-4 text-slate-600"><?= esc($ord['vendor_name']) ?></td>
                            <td class="p-4"><?= badge_payment_method($ord['payment_method']) ?></td>
                            <td class="p-4">
                                <strong class="font-extrabold text-slate-900 block"><?= format_rupiah($ord['total_amount']) ?></strong>
                                <span class="text-[10px] text-emerald-700 font-semibold block">Fee AkuAda: <?= format_rupiah($ord['total_vendor_fee'] ?? 0) ?></span>
                            </td>
                            <td class="p-4"><?= badge_order_status($ord['order_status']) ?></td>
                            <td class="p-4 text-center">
                                <a href="<?= base_url('admin/transactions') ?>" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-purple-100 text-purple-700 font-bold transition">
                                    Kelola
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
