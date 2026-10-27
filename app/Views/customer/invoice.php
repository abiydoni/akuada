<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Invoice Penjualan Resmi - PT Aku Ada Indonesia') ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            purple: '#7C3AED',
                            blue: '#2563EB',
                            pink: '#EC4899',
                            dark: '#0F172A',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            body { background: white !important; padding: 0 !important; color: black !important; }
            .no-print { display: none !important; }
            .print-shadow-none { box-shadow: none !important; border: 1px solid #cbd5e1 !important; }
            .page-break-inside-avoid { page-break-inside: avoid; }
            @page { margin: 12mm 15mm; size: A4 portrait; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased min-h-screen py-6 sm:py-10 px-4">

    <!-- Top Action Bar (Hidden on Print) -->
    <div class="max-w-4xl mx-auto mb-6 no-print">
        <?php if (!empty($isAdmin)): ?>
            <div class="mb-4 p-3.5 rounded-2xl bg-gradient-to-r from-purple-700 via-indigo-600 to-blue-600 text-white flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-2.5 text-xs font-bold">
                    <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
                    <span>Admin View: Faktur Penjualan Resmi dari PT AkuAda Indonesia kepada Perusahaan / Hotel Pembeli</span>
                </div>
                <a href="<?= esc($backUrl ?? base_url('admin/transactions')) ?>" class="px-3 py-1 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition flex items-center gap-1">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali ke Transaksi</span>
                </a>
            </div>
        <?php endif; ?>

        <div class="flex flex-wrap items-center justify-between gap-4 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <a href="<?= !empty($isAdmin) ? base_url('admin/transactions') : base_url('customer/orders/' . $order['id']) ?>" 
                   class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                    <span>Kembali</span>
                </a>
                <span class="text-xs text-slate-400 font-mono">No. Invoice: <strong class="text-slate-800"><?= esc($order['invoice_number'] ?? 'INV/AKUADA/' . date('Ym') . '/' . str_pad($order['id'], 4, '0', STR_PAD_LEFT)) ?></strong></span>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-700 via-blue-600 to-pink-500 hover:opacity-95 text-white font-bold text-xs transition flex items-center gap-2 shadow-md shadow-purple-500/25">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    <span>Cetak Invoice / Simpan PDF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Official Invoice Document Paper (A4 Formatted) -->
    <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-2xl p-8 sm:p-12 print:p-0 print:border-none print:shadow-none print-shadow-none space-y-8">

        <!-- 1. Header: PT Aku Ada Indonesia (Issuer) & Invoice Metadata -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 pb-6 border-b-2 border-slate-900">
            <!-- Left: Issuer Logo & Legal Info -->
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-700 via-blue-600 to-pink-500 flex items-center justify-center text-white font-black text-2xl shadow-md shadow-purple-500/20">
                        A
                    </div>
                    <div>
                        <span class="text-2xl font-black text-slate-900 tracking-tight">AkuAda<span class="text-pink-500">.</span></span>
                        <span class="block text-[11px] uppercase font-extrabold text-purple-700 tracking-widest">Marketplace B2B & Solusi Pengadaan Terpadu</span>
                    </div>
                </div>

                <div class="text-xs text-slate-600 space-y-0.5 pt-1">
                    <strong class="text-slate-900 block font-bold">PT AKU ADA INDONESIA</strong>
                    <p>Sahid Sudirman Center Lt. 28, Jl. Jend. Sudirman Kav. 86</p>
                    <p>Karet Tengsin, Tanah Abang, Jakarta Pusat 10220</p>
                    <p class="font-mono text-slate-700">NPWP: <strong>01.999.888.7-011.000</strong> | NIB: <strong>9120008899112</strong></p>
                    <p>Email: <a href="mailto:billing@akuada.id" class="text-blue-600">billing@akuada.id</a> | Hotline: (021) 5088-9900</p>
                </div>
            </div>

            <!-- Right: Document Title, Invoice Number & Date -->
            <div class="text-left sm:text-right space-y-1.5 self-stretch sm:self-auto">
                <span class="inline-block px-3 py-1 rounded-full bg-purple-100 text-purple-900 font-extrabold text-xs uppercase tracking-wider border border-purple-200">
                    Faktur Penjualan Resmi (Customer Invoice)
                </span>
                
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-mono tracking-tight pt-1">
                    <?= esc($order['invoice_number'] ?? ('INV/AKUADA/' . date('Ym') . '/' . str_pad($order['id'], 4, '0', STR_PAD_LEFT))) ?>
                </h1>

                <div class="text-xs text-slate-600 space-y-1 pt-1">
                    <div>Tanggal Terbit: <strong class="text-slate-900 font-bold"><?= date('d F Y', strtotime($order['created_at'])) ?></strong></div>
                    <div>Jatuh Tempo (TOP): <strong class="text-rose-700 font-bold"><?= $order['due_date'] ? date('d F Y', strtotime($order['due_date'])) : '30 Hari Setelah BAST' ?></strong></div>
                    <div>Referensi No. PO: <strong class="font-mono font-bold text-purple-700"><?= esc($order['po_number'] ?? $order['order_number']) ?></strong></div>
                    <div>Metode Pembayaran: <?= badge_payment_method($order['payment_method']) ?></div>
                </div>
            </div>
        </div>

        <!-- 2. Billed To (Hotel / Perusahaan Pembeli) & Delivery Destination -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-200/90 text-xs">
            <div class="space-y-1.5">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">DITAGIHKAN KEPADA (CORPORATE BUYER):</span>
                <h3 class="text-base font-black text-slate-900"><?= esc($order['buyer_name']) ?></h3>
                <p class="text-slate-600">Legalitas: <strong class="text-slate-800"><?= esc($order['buyer_legal_type'] ?? 'PT') ?></strong></p>
                <p class="text-slate-600">NPWP Badan: <strong class="font-mono text-slate-800"><?= esc($order['buyer_npwp'] ?? '-') ?></strong></p>
                <p class="text-slate-600">NIB: <strong class="font-mono text-slate-800"><?= esc($order['buyer_nib'] ?? '-') ?></strong></p>
                <p class="text-slate-600">PIC Finansial / Procurement: <strong class="text-slate-800"><?= esc($order['buyer_pic'] ?? '-') ?></strong> (<?= esc($order['buyer_phone'] ?? '-') ?>)</p>
                <?php if (!empty($order['buyer_email'])): ?>
                    <p class="text-slate-600">Email: <?= esc($order['buyer_email']) ?></p>
                <?php endif; ?>
            </div>

            <div class="space-y-1.5 md:border-l md:border-slate-200 md:pl-6">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">LOKASI PENGIRIMAN & PENERIMAAN:</span>
                <p class="text-slate-800 font-semibold leading-relaxed"><?= nl2br(esc($order['shipping_address'] ?? 'Kantor Pusat / Gudang Operasional')) ?></p>
                
                <div class="pt-2 space-y-1 border-t border-slate-200 mt-2">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Nomor BAST Sah:</span>
                        <strong class="font-mono text-slate-900"><?= esc($order['bast_number'] ?? 'BAST Terlampir') ?></strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">No. e-Faktur Pajak PPN:</span>
                        <strong class="font-mono text-blue-700"><?= esc($order['tax_invoice_number'] ?? '010.002-26.' . rand(10000000, 99999999)) ?></strong>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Status Pembayaran:</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $order['payment_status'] === 'paid' ? 'bg-emerald-100 text-emerald-800 border border-emerald-300' : 'bg-amber-100 text-amber-800 border border-amber-300' ?>">
                            <?= strtoupper($order['payment_status'] === 'paid' ? 'LUNAS (PAID)' : 'MENUNGGU PEMBAYARAN (PENDING)') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Line Items Table (Rincian Barang yang Dipesan Hotel/Perusahaan) -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-900 text-white font-bold uppercase text-[10px]">
                    <tr>
                        <th class="p-3 text-center w-12">No</th>
                        <th class="p-3">Uraian Kebutuhan Operasional & Spesifikasi</th>
                        <th class="p-3">Kode SKU</th>
                        <th class="p-3 text-center">Kuantitas</th>
                        <th class="p-3 text-right">Harga Satuan B2B</th>
                        <th class="p-3 text-right">PPN 11%</th>
                        <th class="p-3 text-right">Jumlah Total (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <?php foreach ($order['items'] as $idx => $it): ?>
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="p-3 text-center text-slate-400 font-mono"><?= $idx + 1 ?></td>
                            <td class="p-3">
                                <strong class="text-slate-900 font-bold text-xs block"><?= esc($it['product_name']) ?></strong>
                                <span class="text-[10px] text-slate-500">Kategori: Pengadaan Korporat Resmi AkuAda</span>
                            </td>
                            <td class="p-3 font-mono text-slate-500"><?= esc($it['sku']) ?></td>
                            <td class="p-3 text-center font-bold text-slate-800"><?= $it['qty'] ?> <span class="text-[10px] text-slate-500 font-normal"><?= esc($it['uom']) ?></span></td>
                            <td class="p-3 text-right text-slate-700"><?= format_rupiah($it['unit_price']) ?></td>
                            <td class="p-3 text-right text-blue-700 font-medium"><?= format_rupiah($it['ppn_amount']) ?></td>
                            <td class="p-3 text-right font-extrabold text-slate-900"><?= format_rupiah($it['subtotal'] + $it['ppn_amount']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="border-t-2 border-slate-900 text-xs divide-y divide-slate-100 font-bold">
                    <tr>
                        <td colspan="5" class="p-3 text-right text-slate-600">Subtotal Nilai Pengadaan Barang (DPP):</td>
                        <td colspan="2" class="p-3 text-right text-slate-900 font-bold"><?= format_rupiah($order['subtotal']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="p-3 text-right text-blue-700">Pajak Pertambahan Nilai (PPN 11% e-Faktur Terlampir):</td>
                        <td colspan="2" class="p-3 text-right text-blue-700 font-bold"><?= format_rupiah($order['ppn_amount']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="5" class="p-3 text-right text-slate-600">Biaya Logistik & Pengiriman Terjamin:</td>
                        <td colspan="2" class="p-3 text-right text-slate-900 font-bold"><?= format_rupiah($order['shipping_amount'] ?? 0) ?></td>
                    </tr>
                    <tr class="bg-gradient-to-r from-purple-50 via-indigo-50 to-pink-50 text-sm">
                        <td colspan="5" class="p-4 text-right text-purple-950 font-black">
                            <span class="flex items-center justify-end gap-1.5">
                                <span class="material-symbols-outlined text-purple-700 text-[20px]">payments</span>
                                <span>TOTAL TAGIHAN PEMBAYARAN RESMI:</span>
                            </span>
                        </td>
                        <td colspan="2" class="p-4 text-right text-purple-950 font-black text-lg">
                            <?= format_rupiah($order['total_amount']) ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 4. Terbilang Box -->
        <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-200/80 text-xs">
            <span class="text-[10px] font-bold text-purple-800 uppercase tracking-widest block mb-0.5">Jumlah Pembayaran Terbilang:</span>
            <p class="font-extrabold text-purple-950 italic text-sm">
                # <?= esc(terbilang_rupiah($order['total_amount'])) ?> #
            </p>
        </div>

        <!-- 5. Payment Bank Details & Signatures -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 pt-4 border-t border-slate-200 text-xs page-break-inside-avoid">
            
            <!-- Left: Payment Bank Escrow Instructions (7 Cols) -->
            <div class="md:col-span-7 space-y-3">
                <span class="font-bold text-slate-900 uppercase tracking-wider text-[11px] block">Instruksi Pembayaran Resmi PT Aku Ada Indonesia:</span>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3.5 rounded-xl border border-blue-200 bg-blue-50/40 space-y-1">
                        <div class="flex items-center justify-between">
                            <strong class="text-blue-900 font-bold">Bank Mandiri (Escrow)</strong>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-200 text-blue-900 font-mono font-bold">IDR</span>
                        </div>
                        <p class="text-slate-600 text-[11px]">No. Rekening Rekanan:</p>
                        <strong class="font-mono text-sm text-slate-900 block font-bold tracking-wider">122-00-1122334-5</strong>
                        <span class="text-[10px] text-slate-500 block">a/n PT Aku Ada Indonesia</span>
                    </div>

                    <div class="p-3.5 rounded-xl border border-purple-200 bg-purple-50/40 space-y-1">
                        <div class="flex items-center justify-between">
                            <strong class="text-purple-900 font-bold">Bank BCA (Escrow)</strong>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-200 text-purple-900 font-mono font-bold">IDR</span>
                        </div>
                        <p class="text-slate-600 text-[11px]">No. Rekening Rekanan:</p>
                        <strong class="font-mono text-sm text-slate-900 block font-bold tracking-wider">535-088-9900</strong>
                        <span class="text-[10px] text-slate-500 block">a/n PT Aku Ada Indonesia</span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 space-y-1">
                    <p>• Pembayaran dianggap sah apabila ditransfer ke rekening resmi di atas dengan mencantumkan Nomor Invoice: <strong class="font-mono text-purple-700"><?= esc($order['invoice_number'] ?? $order['po_number']) ?></strong> pada kolom berita.</p>
                    <p>• Bukti transfer dapat diunggah melalui portal customer atau dikirimkan ke <a href="mailto:billing@akuada.id" class="text-blue-600 underline">billing@akuada.id</a>.</p>
                </div>
            </div>

            <!-- Right: Corporate Seal & Signatures (5 Cols) -->
            <div class="md:col-span-5 flex flex-col justify-between text-center border border-slate-200 rounded-2xl p-4 bg-slate-50/50">
                <div class="space-y-0.5">
                    <span class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider block">Hormat Kami,</span>
                    <strong class="text-xs text-slate-900 font-extrabold block">PT AKU ADA INDONESIA</strong>
                    <span class="text-[10px] text-slate-500">Finance & Corporate Accounting Division</span>
                </div>

                <!-- Digital Stamp & Signature Area -->
                <div class="my-3 flex items-center justify-center relative">
                    <div class="w-24 h-24 rounded-full border-2 border-dashed border-purple-300 flex flex-col items-center justify-center text-[10px] text-purple-700 font-bold rotate-[-12deg] bg-purple-50/40">
                        <span class="material-symbols-outlined text-[24px] text-purple-600">verified</span>
                        <span>AKUADA</span>
                        <span class="text-[8px] font-mono">OFFICIAL SEAL</span>
                    </div>
                </div>

                <div>
                    <strong class="block text-slate-900 font-bold underline text-xs">Dra. Maya Anggraini, Ak., CA.</strong>
                    <span class="text-[10px] text-slate-500 block">Finance & Treasury Director</span>
                    <span class="text-[9px] text-slate-400 font-mono block">Digital Signed on <?= date('d M Y') ?></span>
                </div>
            </div>

        </div>

        <!-- 6. Footer Legal Note -->
        <div class="pt-4 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Dokumen ini merupakan Faktur Penjualan Sah yang diterbitkan oleh sistem elektronik PT Aku Ada Indonesia sesuai ketentuan perpajakan dan transaksi elektronik Republik Indonesia.
        </div>

    </div>

</body>
</html>
