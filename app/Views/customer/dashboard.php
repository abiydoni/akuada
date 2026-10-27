<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">
    
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-pink-800 via-purple-900 to-slate-900 rounded-2xl p-6 text-white shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-pink-500/30 text-pink-200 text-[11px] font-bold border border-pink-400/30">Akun Corporate Buyer Terverifikasi</span>
            <h1 class="text-2xl font-extrabold mt-1"><?= esc($company['company_name'] ?? 'PT Mega Solusi Digital Tbk') ?></h1>
            <p class="text-xs text-purple-200 mt-0.5">Departemen Procurement & General Affairs (GA) Perusahaan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('products') ?>" class="px-4 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold text-xs shadow-md transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">shopping_cart</span>
                <span>Buat Pengadaan Baru</span>
            </a>
            <a href="<?= base_url('rfq/create') ?>" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold text-xs transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">request_quote</span>
                <span>Minta RFQ</span>
            </a>
        </div>
    </div>

    <!-- Plafon & Key Metrics -->
    <?php
        $limit = (float)($company['credit_limit_top'] ?? 250000000);
        $used = (float)($company['credit_used_top'] ?? 31250000);
        $remaining = max(0, $limit - $used);
    ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-purple-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">account_balance</span>
            </div>
            <div>
                <span class="text-[11px] text-slate-500 font-semibold block">Sisa Plafon Kredit TOP</span>
                <span class="text-lg font-extrabold text-purple-900"><?= format_rupiah($remaining) ?></span>
                <span class="text-[10px] text-slate-400 block mt-0.5">Dari Total <?= format_rupiah($limit) ?></span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">sync</span>
            </div>
            <div>
                <span class="text-[11px] text-slate-500 font-semibold block">PO Sedang Berjalan</span>
                <span class="text-lg font-extrabold text-slate-900"><?= $activeOrders ?> Pesanan</span>
                <span class="text-[10px] text-pink-600 font-bold block mt-0.5">Menunggu Pengiriman/BAST</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">receipt_long</span>
            </div>
            <div>
                <span class="text-[11px] text-slate-500 font-semibold block">Total Pengadaan Selesai</span>
                <span class="text-lg font-extrabold text-slate-900"><?= format_rupiah($totalSpending) ?></span>
                <span class="text-[10px] text-emerald-600 font-bold block mt-0.5">Lengkap Faktur Pajak</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">verified_user</span>
            </div>
            <div>
                <span class="text-[11px] text-slate-500 font-semibold block">Status Legalitas PT</span>
                <span class="text-base font-extrabold text-slate-900">PKP Terverifikasi</span>
                <span class="text-[10px] text-emerald-600 font-bold block mt-0.5">NPWP & NIB Valid</span>
            </div>
        </div>
    </div>

    <!-- Active Purchase Orders Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Riwayat Purchase Order (PO) Aktif</h3>
                <p class="text-xs text-slate-500">Pantau proses pengiriman, konfirmasi serah terima barang (BAST), dan unduh e-Faktur.</p>
            </div>
            <a href="<?= base_url('customer/orders') ?>" class="text-xs font-bold text-pink-700 hover:text-pink-900">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="p-4">No. PO Internal</th>
                        <th class="p-4">Vendor Rekanan</th>
                        <th class="p-4">Termin Bayar</th>
                        <th class="p-4">Total Nilai Tagihan</th>
                        <th class="p-4">No. BAST</th>
                        <th class="p-4">Status Pengadaan</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($recentOrders as $o): ?>
                        <tr class="hover:bg-slate-50 transition">
                            <td class="p-4 font-mono font-bold text-purple-700"><?= esc($o['po_number'] ?? $o['order_number']) ?></td>
                            <td class="p-4 font-bold text-slate-800"><?= esc($o['vendor_name']) ?></td>
                            <td class="p-4"><?= badge_payment_method($o['payment_method']) ?></td>
                            <td class="p-4 font-extrabold text-slate-900"><?= format_rupiah($o['total_amount']) ?></td>
                            <td class="p-4 font-mono text-slate-600"><?= esc($o['bast_number'] ?? '-') ?></td>
                            <td class="p-4"><?= badge_order_status($o['order_status']) ?></td>
                            <td class="p-4 text-center">
                                <a href="<?= base_url('customer/orders/' . $o['id']) ?>" class="px-3 py-1 rounded bg-pink-50 text-pink-700 hover:bg-pink-100 font-bold transition">
                                    Lihat & Sahkan BAST
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
