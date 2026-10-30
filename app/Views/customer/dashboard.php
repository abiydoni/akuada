<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">
    
    <!-- Welcome Header Card -->
    <div class="bg-gradient-to-r from-pink-900 via-purple-900 to-slate-900 rounded-2xl p-6 text-white shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="space-y-1 max-w-3xl">
            <span class="px-2.5 py-0.5 rounded-full bg-pink-500/30 text-pink-200 text-[11px] font-bold border border-pink-400/30 uppercase tracking-wider">Akun Corporate Buyer Terverifikasi</span>
            <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight break-words"><?= esc($company['company_name'] ?? 'PT Mega Solusi Digital Tbk') ?></h1>
            <p class="text-xs text-purple-200 leading-relaxed">
                Portal Pengadaan Operasional, Pembelian Grosir B2B, dan Fasilitas Plafon Kredit Tempo (Term of Payment / TOP).
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 flex-shrink-0 self-start md:self-auto">
            <a href="<?= base_url('products') ?>" class="px-4 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">shopping_cart</span>
                <span>Buat Pengadaan Baru</span>
            </a>
            <a href="<?= base_url('rfq/create') ?>" class="px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[18px]">request_quote</span>
                <span>Minta RFQ Tender</span>
            </a>
        </div>
    </div>

    <!-- Plafon & Key Metrics - Fully responsive, no text clipping -->
    <?php
        $limit = (float)($company['credit_limit_top'] ?? 250000000);
        $used = (float)($company['credit_used_top'] ?? 31250000);
        $remaining = max(0, $limit - $used);
        $usedPercent = $limit > 0 ? min(100, round(($used / $limit) * 100)) : 0;
    ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Plafon Kredit -->
        <div class="bg-gradient-to-br from-purple-50/80 via-white to-pink-50/50 rounded-2xl p-5 border-2 border-purple-300 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-purple-950 font-bold">Sisa Plafon Kredit TOP</span>
                <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-xl">account_balance</span>
                </div>
            </div>
            <div class="space-y-1.5">
                <div class="text-xl font-black text-purple-900 font-mono tracking-tight break-words"><?= format_rupiah($remaining) ?></div>
                <div class="text-[11px] text-slate-500 font-medium">
                    Total Plafon: <strong class="text-slate-800"><?= format_rupiah($limit) ?></strong>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-1.5 rounded-full" style="width: <?= $usedPercent ?>%"></div>
                </div>
            </div>
        </div>

        <!-- Card 2: Active Orders -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-slate-500 font-semibold">PO Sedang Berjalan</span>
                <div class="w-10 h-10 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">sync</span>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-xl font-black text-slate-900 font-mono tracking-tight"><?= $activeOrders ?> Pesanan</div>
                <div class="text-[11px] text-pink-600 font-medium">
                    Menunggu Pengiriman / BAST
                </div>
            </div>
        </div>

        <!-- Card 3: Total Spending -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-slate-500 font-semibold">Total Pengadaan Selesai</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">receipt_long</span>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-lg font-black text-slate-900 font-mono tracking-tight break-words"><?= format_rupiah($totalSpending) ?></div>
                <div class="text-[11px] text-emerald-600 font-medium">
                    Lengkap Faktur Pajak e-Faktur
                </div>
            </div>
        </div>

        <!-- Card 4: Legalitas PT -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex flex-col justify-between hover:shadow-md transition">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs text-slate-500 font-semibold">Status Legalitas Perusahaan</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-xl">verified_user</span>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-base font-black text-slate-900">PKP Terverifikasi</div>
                <div class="text-[11px] text-emerald-600 font-medium">
                    NPWP & NIB Badan Valid
                </div>
            </div>
        </div>
    </div>

    <!-- Active Purchase Orders Table Card - Fits 100% width with NO horizontal scroll -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-pink-600"></span>
                <div>
                    <h3 class="font-bold text-slate-900 text-sm">Riwayat Purchase Order (PO) Aktif</h3>
                    <p class="text-[11px] text-slate-500">Pantau proses pengiriman, konfirmasi serah terima barang (BAST), dan unduh e-Faktur.</p>
                </div>
            </div>
            <a href="<?= base_url('customer/orders') ?>" class="text-xs font-bold text-pink-700 hover:text-pink-900 transition flex items-center gap-1 self-start sm:self-auto">
                <span>Lihat Semua Pesanan</span>
                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>

        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3.5 w-44">No. PO & Tanggal</th>
                        <th class="px-4 py-3.5">Vendor Rekanan</th>
                        <th class="px-4 py-3.5 text-right w-44">Nilai Tagihan PO</th>
                        <th class="px-4 py-3.5 text-center w-36">Status & Bayar</th>
                        <th class="px-4 py-3.5 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-slate-400">
                                Belum ada purchase order aktif saat ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($recentOrders as $o): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3.5 align-top">
                                <a href="<?= base_url('customer/orders/' . $o['id']) ?>" 
                                   class="font-mono font-bold text-purple-700 hover:text-purple-900 hover:underline block text-xs">
                                    <?= esc($o['po_number'] ?? $o['order_number']) ?>
                                </a>
                                <span class="text-[11px] text-slate-400 font-mono block mt-0.5"><?= date('d/m/Y', strtotime($o['created_at'])) ?></span>
                            </td>
                            <td class="px-4 py-3.5 align-top">
                                <div class="font-bold text-slate-900 leading-snug break-words"><?= esc($o['vendor_name']) ?></div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    BAST: <?= !empty($o['bast_number']) ? esc($o['bast_number']) : '<span class="text-slate-400 italic">Belum Terbit</span>' ?>
                                </div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-right font-mono">
                                <div class="font-black text-slate-900 text-sm"><?= format_rupiah($o['total_amount']) ?></div>
                                <div class="text-[10px] text-slate-400 mt-0.5"><?= badge_payment_method($o['payment_method']) ?></div>
                            </td>
                            <td class="px-4 py-3.5 align-top text-center">
                                <?= badge_order_status($o['order_status']) ?>
                            </td>
                            <td class="px-4 py-3.5 align-top text-center">
                                <a href="<?= base_url('customer/orders/' . $o['id']) ?>" 
                                   class="px-3 py-1.5 rounded-lg bg-pink-50 text-pink-700 hover:bg-pink-100 font-bold transition inline-flex items-center gap-1 border border-pink-200"
                                   title="Lihat Rincian PO & Sahkan BAST">
                                    <span class="material-symbols-outlined text-[14px]">visibility</span>
                                    <span>Lihat PO</span>
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
