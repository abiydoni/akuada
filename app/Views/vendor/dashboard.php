<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<div class="space-y-6">
    <!-- Welcome Header Card -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="space-y-1 max-w-3xl">
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-500/30 text-blue-200 text-[11px] font-bold border border-blue-400/30 uppercase tracking-wider">Mitra Vendor Terverifikasi</span>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[11px] font-semibold border border-emerald-400/30">Komisi PKS: <?= esc($company['commission_rate'] ?? 2.5) ?>%</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight break-words"><?= esc($company['company_name'] ?? 'Mitra Supplier B2B') ?></h1>
            <p class="text-xs text-blue-200 leading-relaxed">
                Kelola katalog barang pengadaan korporat, konfirmasi Purchase Order (PO), terbitkan Surat Jalan / BAST, dan pantau pencairan dana (Net Payout).
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 flex-shrink-0 self-start md:self-auto">
            <a href="<?= base_url('vendor/products/new') ?>" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">add_box</span>
                <span>+ Tambah Barang Baru</span>
            </a>
            <a href="<?= base_url('vendor/rfqs') ?>" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">price_change</span>
                <span>Cek Tender RFQ (<?= $totalRfqs ?>)</span>
            </a>
        </div>
    </div>

    <!-- Key Financial & Operational Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Net Payout -->
        <div class="bg-gradient-to-br from-emerald-50/80 via-white to-blue-50/50 rounded-2xl p-5 border-2 border-emerald-300 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-emerald-950 font-bold">Hak Bersih Vendor (Net Payout)</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-xl">payments</span>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-xl font-black text-emerald-900 font-mono tracking-tight break-words"><?= format_rupiah($totalNetPayout) ?></div>
                <div class="text-[11px] text-slate-500 font-medium">
                    DPP Kotor: <strong class="text-slate-800"><?= format_rupiah($totalGrossSales) ?></strong>
                </div>
            </div>
        </div>

        <!-- Card 2: Platform Fee -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-slate-500 font-semibold">Potongan Fee Platform</span>
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">percent</span>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-lg font-black text-slate-900 font-mono tracking-tight break-words"><?= format_rupiah($totalPlatformFee) ?></div>
                <div class="text-[11px] text-purple-700 font-medium">
                    Sesuai Kontrak PKS AkuAda
                </div>
            </div>
        </div>

        <!-- Card 3: Total PO Orders -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-slate-500 font-semibold">Total Pesanan PO Masuk</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">local_shipping</span>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-xl font-black text-slate-900 font-mono tracking-tight"><?= $totalOrders ?> PO</div>
                <div class="text-[11px] text-blue-600 font-medium">
                    Nilai Bruto: <?= format_rupiah($totalRevenue) ?>
                </div>
            </div>
        </div>

        <!-- Card 4: Active Catalog SKU -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-slate-500 font-semibold">Katalog Produk B2B</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">inventory_2</span>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-xl font-black text-slate-900 font-mono tracking-tight"><?= $totalProducts ?> SKU</div>
                <div class="text-[11px] text-indigo-600 font-medium">
                    <?= $totalRfqs ?> Permintaan RFQ Masuk
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Pesanan Purchase Order (PO) Terbaru</h3>
                    <p class="text-[11px] text-slate-500">Segera proses PO yang masuk, perbarui tahapan pengiriman, dan unggah nomor BAST & e-Faktur.</p>
                </div>
            </div>
            <a href="<?= base_url('vendor/orders') ?>" class="text-xs font-bold text-blue-700 hover:text-blue-900 transition flex items-center gap-1 self-start sm:self-auto">
                <span>Lihat Semua PO</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-44">No. PO & Tanggal</th>
                        <th class="px-4 py-3.5">Perusahaan Pembeli (Buyer)</th>
                        <th class="px-4 py-3.5 text-right w-48">Hak Bersih (Net Payout)</th>
                        <th class="px-4 py-3.5 text-center w-36">Status Pengadaan</th>
                        <th class="px-4 py-3.5 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                Belum ada Purchase Order (PO) yang masuk saat ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($recentOrders as $o): ?>
                        <?php
                            $netPayout = (float)($o['vendor_net_payout'] ?? ($o['subtotal'] - ($o['total_vendor_fee'] ?? 0)));
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 align-top">
                                <a href="<?= base_url('vendor/orders/' . $o['id']) ?>" class="font-mono font-bold text-blue-700 hover:text-blue-900 hover:underline block text-xs">
                                    <?= esc($o['po_number'] ?? $o['order_number']) ?>
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono block mt-0.5"><?= date('d/m/Y', strtotime($o['created_at'])) ?></span>
                            </td>
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-900 leading-snug break-words"><?= esc($o['buyer_name']) ?></div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    BAST: <?= !empty($o['bast_number']) ? esc($o['bast_number']) : '<span class="text-slate-400 italic">Belum Terbit</span>' ?>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-emerald-700 text-sm"><?= format_rupiah($netPayout) ?></div>
                                <div class="text-[10px] text-slate-400 mt-0.5">DPP: <?= format_rupiah($o['subtotal']) ?></div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-center">
                                <?= badge_order_status($o['order_status']) ?>
                            </td>
                            <td class="px-4 py-3.5 align-top text-center">
                                <a href="<?= base_url('vendor/orders/' . $o['id']) ?>" class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold transition inline-flex items-center gap-1 border border-blue-200">
                                    <span class="material-symbols-outlined text-[14px]">local_shipping</span>
                                    <span>Proses PO</span>
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
