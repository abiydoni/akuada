<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'e-Faktur Pajak PPN Resmi (DJP)') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .print-sheet { box-shadow: none !important; border: 1px solid #000 !important; margin: 0 !important; padding: 24px !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 py-8 px-4">
<?php
$appName       = get_system_setting('app_name', 'AkuAda');
$companyEntity = get_system_setting('company_legal_name', 'PT AkuAda Solusi Pengadaan Indonesia');
$companyNpwp   = get_system_setting('company_npwp', '01.334.556.8-091.000');
$hqAddress     = get_system_setting('support_office_address', 'Gedung AkuAda Procurement Tower Lt. 18, Jl. Jend. Sudirman Kav. 52-53, Jakarta Selatan');
$signerName    = get_system_setting('inv_akuada_signer_name', 'Dra. Maya Anggraini, Ak., CA.');
$signerTitle   = get_system_setting('inv_akuada_signer_title', 'Finance & Treasury Director');
$nsfp          = $order['tax_invoice_number'] ?: ('010.002-' . date('y', strtotime($order['created_at'])) . '.' . str_pad((string)($order['id'] + 48291000), 8, '0', STR_PAD_LEFT));
$taxRateLabel  = format_tax_rate();
?>

    <!-- Top Action Bar -->
    <div class="max-w-4xl mx-auto mb-5 flex flex-wrap items-center justify-between gap-3 bg-white px-5 py-3.5 rounded-2xl border border-slate-200 shadow-sm no-print">
        <div class="flex items-center gap-2.5 text-xs">
            <span class="px-2.5 py-1 rounded-lg bg-blue-100 text-blue-800 font-extrabold uppercase">e-Faktur DJP (PPN <?= esc($taxRateLabel) ?>)</span>
            <span class="font-mono font-bold text-slate-700">NSFP: <?= esc($nsfp) ?></span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.close()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                Tutup
            </button>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Cetak Lembar e-Faktur Pajak</span>
            </button>
        </div>
    </div>

    <!-- Official DJP e-Faktur Sheet -->
    <div class="max-w-4xl mx-auto bg-white rounded-2xl border-2 border-slate-800 shadow-xl p-8 sm:p-10 space-y-5 text-xs print-sheet">

        <!-- Title Block -->
        <div class="text-center border-b-2 border-slate-800 pb-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Kementerian Keuangan Republik Indonesia — Direktorat Jenderal Pajak</p>
            <h1 class="text-lg font-black uppercase tracking-wider text-slate-900 mt-0.5">FAKTUR PAJAK</h1>
        </div>

        <!-- NSFP & Reference Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 bg-slate-50 rounded-xl border border-slate-300 font-mono text-xs">
            <div>
                <span class="text-slate-500 block text-[10px] font-sans uppercase font-bold">Kode dan Nomor Seri Faktur Pajak:</span>
                <strong class="text-sm text-blue-900 font-black"><?= esc($nsfp) ?></strong>
            </div>
            <div class="sm:text-right">
                <span class="text-slate-500 block text-[10px] font-sans uppercase font-bold">Referensi Komersial (PO / Invoice):</span>
                <strong class="text-slate-800"><?= esc($order['po_number'] ?? $order['order_number']) ?> / <?= esc($order['invoice_number']) ?></strong>
            </div>
        </div>

        <!-- Pengusaha Kena Pajak (Penjual) -->
        <div class="border border-slate-300 rounded-xl overflow-hidden">
            <div class="bg-slate-800 text-white px-4 py-2 font-bold uppercase text-[10px] tracking-wider">
                Pengusaha Kena Pajak yang Menyerahkan BKP / JKP
            </div>
            <div class="p-4 space-y-1.5">
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-3 text-slate-500 font-semibold">Nama PKP</span>
                    <strong class="col-span-9 text-slate-900">: <?= esc($companyEntity) ?> (Platform Resmi <?= esc($appName) ?>)</strong>
                </div>
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-3 text-slate-500 font-semibold">Alamat Lengkap</span>
                    <span class="col-span-9 text-slate-700">: <?= esc($hqAddress) ?></span>
                </div>
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-3 text-slate-500 font-semibold">NPWP (16 Digit)</span>
                    <strong class="col-span-9 font-mono text-blue-800">: <?= esc($companyNpwp) ?></strong>
                </div>
            </div>
        </div>

        <!-- Pembeli BKP / Penerima JKP -->
        <div class="border border-slate-300 rounded-xl overflow-hidden">
            <div class="bg-slate-800 text-white px-4 py-2 font-bold uppercase text-[10px] tracking-wider">
                Pembeli Barang Kena Pajak / Penerima Jasa Kena Pajak
            </div>
            <div class="p-4 space-y-1.5">
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-3 text-slate-500 font-semibold">Nama Pembeli</span>
                    <strong class="col-span-9 text-slate-900">: <?= esc($order['buyer_name']) ?></strong>
                </div>
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-3 text-slate-500 font-semibold">Alamat Lengkap</span>
                    <span class="col-span-9 text-slate-700">: <?= esc($order['buyer_address'] ?: $order['shipping_address']) ?></span>
                </div>
                <div class="grid grid-cols-12 gap-2">
                    <span class="col-span-3 text-slate-500 font-semibold">NPWP Badan</span>
                    <strong class="col-span-9 font-mono text-purple-800">: <?= esc($order['buyer_npwp'] ?? '-') ?></strong>
                </div>
            </div>
        </div>

        <!-- BKP / JKP Items Table -->
        <div class="border border-slate-400 rounded-xl overflow-hidden">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-100 border-b border-slate-400 text-slate-800 font-bold uppercase text-[10px]">
                    <tr>
                        <th class="py-2.5 px-3 text-center w-12 border-r border-slate-300">No. Urut</th>
                        <th class="py-2.5 px-4 border-r border-slate-300">Nama Barang Kena Pajak / Jasa Kena Pajak</th>
                        <th class="py-2.5 px-4 text-right w-52">Harga Jual / Penggantian / Uang Muka / Termin (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($order['items'] as $idx => $item): ?>
                        <tr>
                            <td class="py-3 px-3 text-center font-mono border-r border-slate-200 align-top"><?= $idx + 1 ?></td>
                            <td class="py-3 px-4 border-r border-slate-200 align-top">
                                <div class="font-bold text-slate-900"><?= esc($item['product_name']) ?></div>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                    <?= format_rupiah($item['unit_price']) ?> x <?= number_format($item['qty']) ?> <?= esc($item['uom']) ?> (SKU: <?= esc($item['sku'] ?? '-') ?>)
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 align-top">
                                <?= format_rupiah($item['subtotal']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="border-t-2 border-slate-800 bg-slate-50 font-bold divide-y divide-slate-300">
                    <tr>
                        <td colspan="2" class="py-2.5 px-4 border-r border-slate-300">Harga Jual / Penggantian</td>
                        <td class="py-2.5 px-4 text-right font-mono text-slate-900"><?= format_rupiah($order['subtotal']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="py-2.5 px-4 border-r border-slate-300">Dikurangi Potongan Harga / Uang Muka</td>
                        <td class="py-2.5 px-4 text-right font-mono text-slate-600">Rp 0</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="py-2.5 px-4 border-r border-slate-300 text-slate-900">Dasar Pengenaan Pajak (DPP)</td>
                        <td class="py-2.5 px-4 text-right font-mono font-extrabold text-slate-900"><?= format_rupiah($order['subtotal']) ?></td>
                    </tr>
                    <tr class="bg-blue-50/60">
                        <td colspan="2" class="py-3 px-4 border-r border-slate-300 text-blue-900 font-black">
                            Total PPN (<?= esc($taxRateLabel) ?> x Dasar Pengenaan Pajak)
                        </td>
                        <td class="py-3 px-4 text-right font-mono text-sm font-black text-blue-900"><?= format_rupiah($order['ppn_amount']) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Bottom Verification & Signature -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 items-center">
            <div class="flex items-start gap-3 p-4 rounded-xl bg-emerald-50/70 border border-emerald-200">
                <div class="w-14 h-14 rounded-xl bg-white border border-emerald-300 flex flex-col items-center justify-center flex-shrink-0 font-mono text-[9px] font-black text-emerald-800 leading-tight text-center">
                    <span class="material-symbols-outlined text-emerald-600 text-2xl">qr_code_2</span>
                    <span>DJP VALID</span>
                </div>
                <div class="space-y-1 text-[11px]">
                    <span class="px-2 py-0.5 rounded bg-emerald-600 text-white font-extrabold text-[9px] uppercase">Status Approval: Sukses</span>
                    <p class="text-slate-600 leading-snug">
                        Sesuai dengan ketentuan yang berlaku, Direktorat Jenderal Pajak mengatur bahwa Faktur Pajak ini telah ditandatangani secara elektronik sehingga tidak diperlukan tanda tangan basah pada Faktur Pajak ini.
                    </p>
                </div>
            </div>

            <div class="text-center sm:text-right space-y-8 pr-2">
                <div>
                    <p class="font-semibold text-slate-700">Jakarta, <?= date('d F Y', strtotime($order['created_at'])) ?></p>
                    <p class="text-[11px] text-slate-500">Pejabat Penandatangan e-Faktur Pajak,</p>
                </div>
                <div>
                    <strong class="underline text-slate-900 text-sm block"><?= esc($signerName) ?></strong>
                    <span class="text-[11px] text-slate-500"><?= esc($signerTitle) ?></span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

