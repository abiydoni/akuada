<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    
    <!-- Welcome Header Card -->
    <div class="bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="space-y-1 max-w-3xl">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-purple-500/30 text-purple-200 text-[11px] font-bold border border-purple-400/30 uppercase tracking-wider">Superadmin Platform</span>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-semibold border border-emerald-400/30">Sistem Aktif & Terpantau</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight">Dashboard Pengawasan Ekosistem B2B AkuAda</h1>
            <p class="text-xs text-purple-200 leading-relaxed">
                Pemantauan real-time transaksi purchase order (PO) korporat, penerbitan faktur pajak e-Faktur PPN <?= esc(format_tax_rate()) ?>, dan fasilitas plafon kredit TOP.
            </p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0 self-start md:self-auto">
            <a href="<?= base_url('admin/settings') ?>" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs transition flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[18px]">tune</span>
                <span>Pengaturan Sistem & Pajak</span>
            </a>
        </div>
    </div>

    <!-- Metric Cards Grid - Fully responsive, no text clipping -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
        <!-- Card 1: GMV -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-slate-500 font-semibold">Total GMV Pengadaan</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">account_balance_wallet</span>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-lg font-black text-slate-900 font-mono tracking-tight break-words"><?= format_rupiah($totalGmv) ?></div>
                <div class="flex items-center gap-1 text-[11px] text-emerald-600 font-bold">
                    <span class="material-symbols-outlined text-[14px]">trending_up</span>
                    <span>Volume Belanja B2B</span>
                </div>
            </div>
        </div>

        <!-- Card 2: Fee Platform AkuAda -->
        <div class="bg-gradient-to-br from-emerald-50/70 via-white to-purple-50/50 rounded-2xl p-5 border-2 border-emerald-300 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-emerald-950 font-bold">Fee Platform AkuAda</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-xl">monetization_on</span>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-lg font-black text-emerald-900 font-mono tracking-tight break-words"><?= format_rupiah($totalPlatformFee) ?></div>
                <div class="text-[11px] text-purple-800 font-semibold">
                    Komisi Kesepakatan PKS Vendor
                </div>
            </div>
        </div>

        <!-- Card 3: Purchase Orders -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-slate-500 font-semibold">Purchase Order (PO)</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">receipt_long</span>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-lg font-black text-slate-900 font-mono tracking-tight"><?= $totalOrders ?> Transaksi</div>
                <div class="text-[11px] text-blue-600 font-semibold">
                    Terhubung e-Faktur & BAST
                </div>
            </div>
        </div>

        <!-- Card 4: Corporate Buyers -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-slate-500 font-semibold">Corporate Buyers</span>
                <div class="w-10 h-10 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">corporate_fare</span>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-lg font-black text-slate-900 font-mono tracking-tight"><?= $totalBuyers ?> Perusahaan</div>
                <div class="text-[11px] text-pink-600 font-semibold">
                    Fasilitas TOP Plafon Aktif
                </div>
            </div>
        </div>

        <!-- Card 5: Vendors -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-slate-500 font-semibold">Mitra Supplier</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">store</span>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-lg font-black text-slate-900 font-mono tracking-tight"><?= $totalVendors ?> Vendor</div>
                <div class="text-[11px] text-slate-500 break-words font-medium">
                    Hak Vendor: <span class="text-slate-800 font-bold"><?= format_rupiah($totalVendorPayout) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table Card - No Horizontal Scroll -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Purchase Order (PO) Terbaru Masuk</h3>
                    <p class="text-[11px] text-slate-500">Daftar PO yang diterbitkan corporate buyer kepada mitra vendor resmi.</p>
                </div>
            </div>
            <a href="<?= base_url('admin/transactions') ?>" class="text-xs font-bold text-purple-700 hover:text-purple-900 transition flex items-center gap-1 self-start sm:self-auto">
                <span>Lihat Semua Transaksi</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5">No. PO & Tanggal</th>
                        <th class="px-4 py-3.5">Pihak Transaksi (Buyer & Vendor)</th>
                        <th class="px-4 py-3.5 text-right">Nilai Tagihan & Fee</th>
                        <th class="px-4 py-3.5 text-center">Status & Bayar</th>
                        <th class="px-4 py-3.5 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                Belum ada pesanan terbaru saat ini.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($recentOrders as $ord): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- PO & Date -->
                            <td class="px-4 py-3.5 align-middle">
                                <a href="<?= base_url('admin/orders/' . $ord['id']) ?>" 
                                   class="font-mono font-bold text-purple-700 hover:text-purple-900 hover:underline block text-xs">
                                    <?= esc($ord['po_number'] ?? $ord['order_number']) ?>
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono block mt-0.5"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></span>
                            </td>

                            <!-- Buyer & Vendor -->
                            <td class="px-4 py-3.5 align-middle">
                                <div class="font-bold text-slate-900 leading-snug"><?= esc($ord['buyer_name']) ?></div>
                                <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[13px] text-slate-400">store</span>
                                    <span>Penyedia: <strong class="text-slate-700"><?= esc($ord['vendor_name']) ?></strong></span>
                                </div>
                            </td>

                            <!-- Total & Fee -->
                            <td class="px-4 py-3.5 align-middle text-right font-mono">
                                <div class="font-black text-slate-900 text-sm"><?= format_rupiah($ord['total_amount']) ?></div>
                                <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">Fee AkuAda: <?= format_rupiah($ord['total_vendor_fee'] ?? 0) ?></div>
                            </td>

                            <!-- Status & Payment -->
                            <td class="px-4 py-3.5 align-middle text-center">
                                <div class="inline-flex flex-col items-center gap-1">
                                    <?= badge_order_status($ord['order_status']) ?>
                                    <span class="text-[10px] text-slate-500 font-medium"><?= badge_payment_method($ord['payment_method']) ?></span>
                                </div>
                            </td>

                            <!-- Action -->
                            <td class="px-4 py-3.5 align-middle text-center">
                                <a href="<?= base_url('admin/orders/' . $ord['id']) ?>" 
                                   class="px-3 py-1.5 rounded-lg bg-purple-50 hover:bg-purple-100 text-purple-700 font-bold transition inline-flex items-center gap-1 border border-purple-200"
                                   title="Buka Lembar Kerja Detail PO">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    <span>Detail</span>
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
