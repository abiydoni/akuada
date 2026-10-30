<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Faktur Penagihan Vendor ke AkuAda') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        @media print {
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            @page { margin: 12mm 15mm; size: A4 portrait; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased min-h-screen py-6 sm:py-10 px-4">

    <div class="max-w-4xl mx-auto mb-6 no-print">
        <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <a href="<?= !empty($isAdmin) ? ($backUrl ?? base_url('admin/transactions')) : base_url('vendor/orders/' . $order['id']) ?>" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Kembali</span>
                </a>
                <span class="text-xs text-slate-500 font-mono">Faktur Settlement Vendor: <strong class="text-slate-900"><?= esc($order['vendor_invoice_number'] ?? ('VINV/' . date('Ym') . '/' . str_pad($order['id'], 4, '0', STR_PAD_LEFT))) ?></strong></span>
            </div>
            <button type="button" onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition flex items-center gap-2 shadow-md">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak Faktur Penagihan / PDF</span>
            </button>
        </div>
    </div>

    <?php
        $feeTotal = (float)($order['total_vendor_fee'] ?? 0);
        $netPayout = (float)($order['vendor_net_payout'] ?? ($order['subtotal'] - $feeTotal));
        $totalSettlementWithPpn = $netPayout + (float)$order['ppn_amount'];
    ?>

    <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-2xl p-8 sm:p-12 space-y-8">
        <!-- Header: Vendor as Issuer -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 pb-6 border-b-2 border-slate-900">
            <div class="space-y-1.5">
                <span class="inline-block px-2.5 py-0.5 rounded bg-blue-100 text-blue-800 font-bold text-[10px] uppercase tracking-wider">Diterbitkan Oleh Mitra Supplier</span>
                <h2 class="text-xl font-black text-slate-900"><?= esc($order['vendor_name']) ?></h2>
                <div class="text-xs text-slate-600 space-y-0.5">
                    <p><?= esc($order['vendor_address'] ?? 'Indonesia') ?> - <?= esc($order['vendor_city'] ?? '') ?></p>
                    <p class="font-mono">NPWP Vendor: <strong><?= esc($order['vendor_npwp'] ?? '-') ?></strong> | NIB: <strong><?= esc($order['vendor_nib'] ?? '-') ?></strong></p>
                    <p>PIC: <?= esc($order['vendor_pic'] ?? '-') ?> (<?= esc($order['vendor_phone'] ?? '-') ?>)</p>
                </div>
            </div>

            <div class="text-left sm:text-right space-y-1">
                <span class="inline-block px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 font-extrabold text-xs uppercase tracking-wider border border-emerald-200">
                    Faktur Penagihan Settlement Vendor
                </span>
                <h1 class="text-2xl font-black text-slate-900 font-mono pt-1">
                    <?= esc($order['vendor_invoice_number'] ?? ('VINV/' . date('Ym') . '/' . str_pad($order['id'], 4, '0', STR_PAD_LEFT))) ?>
                </h1>
                <div class="text-xs text-slate-600 space-y-0.5 pt-1">
                    <div>Tanggal PO: <strong><?= date('d F Y', strtotime($order['created_at'])) ?></strong></div>
                    <div>Referensi PO: <strong class="font-mono text-blue-700"><?= esc($order['po_number'] ?? $order['order_number']) ?></strong></div>
                    <div>No. BAST: <strong class="font-mono"><?= esc($order['bast_number'] ?? '-') ?></strong></div>
                </div>
            </div>
        </div>

        <!-- Billed To PT Aku Ada Indonesia -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">DITAGIHKAN KEPADA PLATFORM:</span>
                <strong class="text-sm font-black text-slate-900 block">PT AKU ADA INDONESIA (E-Procurement Aggregator)</strong>
                <p class="text-slate-600">Sahid Sudirman Center Lt. 28, Jl. Jend. Sudirman Kav. 86, Jakarta Pusat</p>
                <p class="text-slate-600 font-mono">NPWP: 01.999.888.7-011.000</p>
                <p class="text-slate-500 pt-1">Untuk Pengadaan Buyer: <strong><?= esc($order['buyer_name']) ?></strong></p>
            </div>
            <div class="space-y-1 md:border-l md:border-slate-200 md:pl-6">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">REKENING PENCAIRAN DANA VENDOR:</span>
                <p class="text-slate-700">Bank Tujuan: <strong><?= esc($order['vendor_bank_name'] ?? 'BCA Corporate') ?></strong></p>
                <p class="text-slate-700 font-mono">No. Rekening: <strong><?= esc($order['vendor_bank_account_number'] ?? '8800-1122-3344') ?></strong></p>
                <p class="text-slate-700">Atas Nama: <strong><?= esc($order['vendor_bank_account_holder'] ?? $order['vendor_name']) ?></strong></p>
            </div>
        </div>

        <!-- Items & Fee Deduction Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-900 text-white font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-3 py-3 text-center w-10">No</th>
                        <th class="px-4 py-3">Nama Barang & SKU</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-right">Subtotal DPP</th>
                        <th class="px-4 py-3 text-right">Potongan Fee Platform</th>
                        <th class="px-4 py-3 text-right">Hak Bersih Vendor (Net)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($order['items'] as $idx => $it): ?>
                        <tr>
                            <td class="px-3 py-3 text-center font-mono text-slate-400"><?= $idx + 1 ?></td>
                            <td class="px-4 py-3">
                                <strong class="text-slate-900 block"><?= esc($it['product_name']) ?></strong>
                                <span class="font-mono text-[10px] text-slate-500">SKU: <?= esc($it['sku']) ?></span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold"><?= number_format($it['qty']) ?> <?= esc($it['uom']) ?></td>
                            <td class="px-4 py-3 text-right font-mono"><?= format_rupiah($it['subtotal']) ?></td>
                            <td class="px-4 py-3 text-right font-mono text-rose-700">-<?= format_rupiah($it['vendor_fee_amount']) ?> (<?= (float)$it['vendor_fee_percent'] ?>%)</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700"><?= format_rupiah($it['vendor_net_amount']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="border-t-2 border-slate-900 font-bold bg-slate-50 divide-y divide-slate-200">
                    <tr>
                        <td colspan="5" class="px-4 py-2.5 text-right text-slate-600">Total Penjualan Barang (DPP):</td>
                        <td class="px-4 py-2.5 text-right font-mono"><?= format_rupiah($order['subtotal']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="px-4 py-2.5 text-right text-rose-700">Dikurangi Komisi Layanan Platform AkuAda:</td>
                        <td class="px-4 py-2.5 text-right font-mono text-rose-700">-<?= format_rupiah($feeTotal) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="px-4 py-2.5 text-right text-emerald-900">Hak Bersih Pokok Barang (Net Payout DPP):</td>
                        <td class="px-4 py-2.5 text-right font-mono text-emerald-800"><?= format_rupiah($netPayout) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="px-4 py-2.5 text-right text-blue-700">PPN 11% Sesuai e-Faktur Vendor:</td>
                        <td class="px-4 py-2.5 text-right font-mono text-blue-700">+<?= format_rupiah($order['ppn_amount']) ?></td>
                    </tr>
                    <tr class="bg-emerald-50 text-sm">
                        <td colspan="5" class="px-4 py-3.5 text-right text-emerald-950 font-black">TOTAL PENCAIRAN KE REKENING VENDOR:</td>
                        <td class="px-4 py-3.5 text-right font-mono text-emerald-900 font-black text-base"><?= format_rupiah($totalSettlementWithPpn) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-200 text-xs">
            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-widest block mb-0.5">Terbilang:</span>
            <p class="font-extrabold text-emerald-950 italic text-sm"># <?= esc(terbilang_rupiah($totalSettlementWithPpn)) ?> #</p>
        </div>
    </div>
</body>
</html>
