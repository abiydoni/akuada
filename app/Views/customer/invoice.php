<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Invoice Penjualan Resmi - AkuAda') ?></title>
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
<?php
    // Ensure order financials & items dynamically reflect current system settings (ppn_rate, vendor_fee, etc.)
    $order = recalculate_order_from_settings($order);
    $ppnRateVal   = get_ppn_rate();
    $ppnRateLabel = format_tax_rate($ppnRateVal);

    // Load configurable invoice settings with defaults
    $invIssuerName     = get_system_setting('inv_issuer_name', 'AKUADA');
    $invIssuerSubtitle = get_system_setting('inv_issuer_subtitle', 'Marketplace B2B & Solusi Pengadaan Terpadu');
    $invIssuerAddr1    = get_system_setting('inv_issuer_address_1', 'Sahid Sudirman Center Lt. 28, Jl. Jend. Sudirman Kav. 86');
    $invIssuerAddr2    = get_system_setting('inv_issuer_address_2', 'Karet Tengsin, Tanah Abang, Jakarta Pusat 10220');
    $invIssuerNpwp     = get_system_setting('inv_issuer_npwp', '01.999.888.7-011.000');
    $invIssuerNib      = get_system_setting('inv_issuer_nib', '9120008899112');
    $invBillingEmail   = get_system_setting('inv_billing_email', 'billing@akuada.id');
    $invBillingPhone   = get_system_setting('inv_billing_phone', '(021) 5088-9900');

    $invBank1Name      = get_system_setting('inv_bank1_name', 'Bank Mandiri (Escrow)');
    $invBank1Number    = get_system_setting('inv_bank1_number', '122-00-1122334-5');
    $invBank1Holder    = get_system_setting('inv_bank1_holder', 'AkuAda');
    $invBank1Currency  = get_system_setting('inv_bank1_currency', 'IDR');

    $invBank2Name      = get_system_setting('inv_bank2_name', 'Bank BCA (Escrow)');
    $invBank2Number    = get_system_setting('inv_bank2_number', '535-088-9900');
    $invBank2Holder    = get_system_setting('inv_bank2_holder', 'AkuAda');
    $invBank2Currency  = get_system_setting('inv_bank2_currency', 'IDR');

    $invPaymentNote    = get_system_setting('inv_payment_note', 'Pembayaran dianggap sah apabila ditransfer ke rekening resmi di atas dengan mencantumkan Nomor Invoice pada kolom berita.');

    $invSignGreeting   = get_system_setting('inv_sign_greeting', 'Hormat Kami,');
    $invSignDivision   = get_system_setting('inv_sign_division', 'Finance & Corporate Accounting Division');
    $invSignName       = get_system_setting('inv_sign_name', 'Dra. Maya Anggraini, Ak., CA.');
    $invSignTitle      = get_system_setting('inv_sign_title', 'Finance & Treasury Director');
    $invSealText       = get_system_setting('inv_seal_text', 'AKUADA');
    $invSealSubtext    = get_system_setting('inv_seal_subtext', 'OFFICIAL SEAL');

    $invFooterNote     = get_system_setting('inv_footer_note', 'Dokumen ini merupakan Faktur Penjualan Sah yang diterbitkan oleh sistem elektronik AkuAda sesuai ketentuan perpajakan dan transaksi elektronik Republik Indonesia.');
?>

    <!-- Top Action Bar (Hidden on Print) -->
    <div class="max-w-4xl mx-auto mb-6 no-print space-y-3">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs font-bold flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600 text-[18px]">check_circle</span>
                    <span><?= esc(session()->getFlashdata('success')) ?></span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">
                    <span class="material-symbols-outlined text-[16px]">close</span>
                </button>
            </div>
        <?php endif; ?>

        <?php if (!empty($isAdmin)): ?>
            <div class="p-3.5 rounded-2xl bg-gradient-to-r from-purple-700 via-indigo-600 to-blue-600 text-white flex items-center justify-between shadow-lg">
                <div class="flex items-center gap-2.5 text-xs font-bold">
                    <span class="material-symbols-outlined text-[20px]">admin_panel_settings</span>
                    <span>Admin View: Faktur Penjualan Resmi dari AkuAda kepada Perusahaan / Hotel Pembeli</span>
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

            <div class="flex flex-wrap items-center gap-2">
                <button type="button" onclick="openInvoiceSettingsModal()" class="px-4 py-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-800 border border-purple-200 font-bold text-xs transition flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">tune</span>
                    <span>Pengaturan Invoice (Pembayaran & TTD)</span>
                </button>
                <button type="button" onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-700 via-blue-600 to-pink-500 hover:opacity-95 text-white font-bold text-xs transition flex items-center gap-2 shadow-md shadow-purple-500/25">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    <span>Cetak Invoice / Simpan PDF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Official Invoice Document Paper (A4 Formatted) -->
    <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-2xl p-8 sm:p-12 print:p-0 print:border-none print:shadow-none print-shadow-none space-y-8">

        <!-- 1. Header: AkuAda (Issuer) & Invoice Metadata -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 pb-6 border-b-2 border-slate-900">
            <!-- Left: Issuer Logo & Legal Info -->
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-700 via-blue-600 to-pink-500 flex items-center justify-center text-white font-black text-2xl shadow-md shadow-purple-500/20">
                        A
                    </div>
                    <div>
                        <span class="text-2xl font-black text-slate-900 tracking-tight">AkuAda<span class="text-pink-500">.</span></span>
                        <span class="block text-[11px] uppercase font-extrabold text-purple-700 tracking-widest"><?= esc($invIssuerSubtitle) ?></span>
                    </div>
                </div>

                <div class="text-xs text-slate-600 space-y-0.5 pt-1">
                    <strong class="text-slate-900 block font-bold"><?= esc($invIssuerName) ?></strong>
                    <p><?= esc($invIssuerAddr1) ?></p>
                    <p><?= esc($invIssuerAddr2) ?></p>
                    <p class="font-mono text-slate-700">NPWP: <strong><?= esc($invIssuerNpwp) ?></strong> | NIB: <strong><?= esc($invIssuerNib) ?></strong></p>
                    <p>Email: <a href="mailto:<?= esc($invBillingEmail) ?>" class="text-blue-600"><?= esc($invBillingEmail) ?></a> | Hotline: <?= esc($invBillingPhone) ?></p>
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
        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="w-full text-xs text-left border-collapse">
                <thead class="bg-slate-900 text-white font-bold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-3 py-3 text-center w-12">No</th>
                        <th class="px-4 py-3">Uraian Kebutuhan Operasional & Spesifikasi</th>
                        <th class="px-4 py-3 text-center whitespace-nowrap">Kuantitas</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Harga Satuan B2B</th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">PPN <?= esc($ppnRateLabel) ?></th>
                        <th class="px-4 py-3 text-right whitespace-nowrap">Jumlah Total (IDR)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    <?php foreach ($order['items'] as $idx => $it): ?>
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-3 py-3 text-center text-slate-400 font-mono"><?= $idx + 1 ?></td>
                            <td class="px-4 py-3">
                                <strong class="text-slate-900 font-bold text-xs block"><?= esc($it['product_name']) ?></strong>
                                <span class="text-[10px] text-slate-500">Kategori: Pengadaan Korporat Resmi AkuAda</span>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-slate-800 whitespace-nowrap"><?= number_format($it['qty']) ?> <span class="text-[10px] text-slate-500 font-normal"><?= esc($it['uom']) ?></span></td>
                            <td class="px-4 py-3 text-right font-mono text-slate-700 whitespace-nowrap"><?= format_rupiah($it['unit_price']) ?></td>
                            <td class="px-4 py-3 text-right font-mono text-blue-700 font-medium whitespace-nowrap"><?= format_rupiah($it['ppn_amount']) ?></td>
                            <td class="px-4 py-3 text-right font-mono font-extrabold text-slate-900 whitespace-nowrap"><?= format_rupiah($it['subtotal'] + $it['ppn_amount']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="border-t-2 border-slate-900 text-xs divide-y divide-slate-100 font-bold bg-slate-50/50">
                    <tr>
                        <td colspan="4" class="px-4 py-2.5 text-right text-slate-600">Subtotal Nilai Pengadaan Barang (DPP):</td>
                        <td colspan="2" class="px-4 py-2.5 text-right font-mono text-slate-900 font-bold whitespace-nowrap"><?= format_rupiah($order['subtotal']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-4 py-2.5 text-right text-blue-700">Pajak Pertambahan Nilai (PPN <?= esc($ppnRateLabel) ?> e-Faktur Terlampir):</td>
                        <td colspan="2" class="px-4 py-2.5 text-right font-mono text-blue-700 font-bold whitespace-nowrap"><?= format_rupiah($order['ppn_amount']) ?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="px-4 py-2.5 text-right text-slate-600">Biaya Logistik & Pengiriman Terjamin:</td>
                        <td colspan="2" class="px-4 py-2.5 text-right font-mono text-slate-900 font-bold whitespace-nowrap"><?= format_rupiah($order['shipping_amount'] ?? 0) ?></td>
                    </tr>
                    <tr class="bg-gradient-to-r from-purple-50 via-indigo-50 to-pink-50 text-sm">
                        <td colspan="4" class="px-4 py-3.5 text-right text-purple-950 font-black">
                            <span class="flex items-center justify-end gap-1.5">
                                <span class="material-symbols-outlined text-purple-700 text-[20px]">payments</span>
                                <span>TOTAL TAGIHAN PEMBAYARAN RESMI:</span>
                            </span>
                        </td>
                        <td colspan="2" class="p-4 text-right text-purple-950 font-black text-lg whitespace-nowrap">
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
                <span class="font-bold text-slate-900 uppercase tracking-wider text-[11px] block">Instruksi Pembayaran Resmi AkuAda:</span>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3.5 rounded-xl border border-blue-200 bg-blue-50/40 space-y-1">
                        <div class="flex items-center justify-between">
                            <strong class="text-blue-900 font-bold"><?= esc($invBank1Name) ?></strong>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-blue-200 text-blue-900 font-mono font-bold"><?= esc($invBank1Currency) ?></span>
                        </div>
                        <p class="text-slate-600 text-[11px]">No. Rekening Rekanan:</p>
                        <strong class="font-mono text-sm text-slate-900 block font-bold tracking-wider"><?= esc($invBank1Number) ?></strong>
                        <span class="text-[10px] text-slate-500 block">a/n <?= esc($invBank1Holder) ?></span>
                    </div>

                    <div class="p-3.5 rounded-xl border border-purple-200 bg-purple-50/40 space-y-1">
                        <div class="flex items-center justify-between">
                            <strong class="text-purple-900 font-bold"><?= esc($invBank2Name) ?></strong>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-purple-200 text-purple-900 font-mono font-bold"><?= esc($invBank2Currency) ?></span>
                        </div>
                        <p class="text-slate-600 text-[11px]">No. Rekening Rekanan:</p>
                        <strong class="font-mono text-sm text-slate-900 block font-bold tracking-wider"><?= esc($invBank2Number) ?></strong>
                        <span class="text-[10px] text-slate-500 block">a/n <?= esc($invBank2Holder) ?></span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 space-y-1">
                    <p>• <?= esc($invPaymentNote) ?> Nomor Invoice: <strong class="font-mono text-purple-700"><?= esc($order['invoice_number'] ?? $order['po_number']) ?></strong>.</p>
                    <p>• Bukti transfer dapat diunggah melalui portal customer atau dikirimkan ke <a href="mailto:<?= esc($invBillingEmail) ?>" class="text-blue-600 underline"><?= esc($invBillingEmail) ?></a>.</p>
                </div>
            </div>

            <!-- Right: Corporate Seal & Signatures (5 Cols) -->
            <div class="md:col-span-5 flex flex-col justify-between text-center border border-slate-200 rounded-2xl p-4 bg-slate-50/50">
                <div class="space-y-0.5">
                    <span class="text-[10px] text-slate-400 uppercase font-extrabold tracking-wider block"><?= esc($invSignGreeting) ?></span>
                    <strong class="text-xs text-slate-900 font-extrabold block"><?= esc($invIssuerName) ?></strong>
                    <span class="text-[10px] text-slate-500"><?= esc($invSignDivision) ?></span>
                </div>

                <!-- Digital Stamp & Signature Area -->
                <div class="my-3 flex items-center justify-center relative">
                    <div class="w-24 h-24 rounded-full border-2 border-dashed border-purple-300 flex flex-col items-center justify-center text-[10px] text-purple-700 font-bold rotate-[-12deg] bg-purple-50/40">
                        <span class="material-symbols-outlined text-[24px] text-purple-600">verified</span>
                        <span><?= esc($invSealText) ?></span>
                        <span class="text-[8px] font-mono"><?= esc($invSealSubtext) ?></span>
                    </div>
                </div>

                <div>
                    <strong class="block text-slate-900 font-bold underline text-xs"><?= esc($invSignName) ?></strong>
                    <span class="text-[10px] text-slate-500 block"><?= esc($invSignTitle) ?></span>
                    <span class="text-[9px] text-slate-400 font-mono block">Digital Signed on <?= date('d M Y') ?></span>
                </div>
            </div>

        </div>

        <!-- 6. Footer Legal Note -->
        <div class="pt-4 border-t border-slate-200 text-center text-[10px] text-slate-400">
            <?= esc($invFooterNote) ?>
        </div>

    </div>

    <!-- Modal Pengaturan Cepat Invoice (Pembayaran, Penandatangan & Identitas Faktur) -->
    <div id="invoiceSettingsModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 no-print overflow-y-auto">
        <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-3xl w-full p-6 sm:p-8 space-y-5 text-xs my-8 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200 sticky top-0 bg-white z-10">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">tune</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Pengaturan Invoice AkuAda</h3>
                        <p class="text-[11px] text-slate-500">Sesuaikan rekening pembayaran, pejabat penandatangan, stempel, dan identitas faktur.</p>
                    </div>
                </div>
                <button type="button" onclick="closeInvoiceSettingsModal()" class="text-slate-400 hover:text-slate-600 p-1">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <form action="<?= base_url('admin/settings/update') ?>" method="POST" class="space-y-5">
                <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                <!-- A. Rekening Pembayaran -->
                <div class="space-y-3">
                    <h4 class="font-extrabold uppercase tracking-wider text-purple-800 text-[11px] flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">account_balance</span>
                        <span>1. Pengaturan Rekening Pembayaran & Instruksi Transfer</span>
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="p-3.5 rounded-2xl border border-blue-200 bg-blue-50/40 space-y-2">
                            <span class="font-bold text-blue-900 block">Rekening Bank #1</span>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Nama Bank</label>
                                    <input type="text" name="inv_bank1_name" value="<?= esc($invBank1Name) ?>" class="w-full px-2.5 py-1.5 rounded-lg border border-blue-300 bg-white font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Mata Uang</label>
                                    <input type="text" name="inv_bank1_currency" value="<?= esc($invBank1Currency) ?>" class="w-full px-2.5 py-1.5 rounded-lg border border-blue-300 bg-white font-mono text-center font-bold">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 mb-0.5">No. Rekening</label>
                                    <input type="text" name="inv_bank1_number" value="<?= esc($invBank1Number) ?>" class="w-full px-2.5 py-1.5 rounded-lg border border-blue-300 bg-white font-mono font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Atas Nama (a/n)</label>
                                    <input type="text" name="inv_bank1_holder" value="<?= esc($invBank1Holder) ?>" class="w-full px-2.5 py-1.5 rounded-lg border border-blue-300 bg-white font-semibold">
                                </div>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl border border-purple-200 bg-purple-50/40 space-y-2">
                            <span class="font-bold text-purple-900 block">Rekening Bank #2</span>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Nama Bank</label>
                                    <input type="text" name="inv_bank2_name" value="<?= esc($invBank2Name) ?>" class="w-full px-2.5 py-1.5 rounded-lg border border-purple-300 bg-white font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Mata Uang</label>
                                    <input type="text" name="inv_bank2_currency" value="<?= esc($invBank2Currency) ?>" class="w-full px-2.5 py-1.5 rounded-lg border border-purple-300 bg-white font-mono text-center font-bold">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 mb-0.5">No. Rekening</label>
                                    <input type="text" name="inv_bank2_number" value="<?= esc($invBank2Number) ?>" class="w-full px-2.5 py-1.5 rounded-lg border border-purple-300 bg-white font-mono font-bold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-600 mb-0.5">Atas Nama (a/n)</label>
                                    <input type="text" name="inv_bank2_holder" value="<?= esc($invBank2Holder) ?>" class="w-full px-2.5 py-1.5 rounded-lg border border-purple-300 bg-white font-semibold">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-bold text-blue-800 mb-1">Tarif Pajak PPN (%)</label>
                            <input type="number" step="0.1" name="ppn_rate" value="<?= esc($ppnRateVal) ?>" class="w-full px-3 py-2 rounded-xl border border-blue-300 bg-blue-50/40 font-mono font-bold text-blue-900">
                        </div>
                        <div>
                            <label class="block font-bold text-emerald-800 mb-1">Biaya Logistik Invoice Ini (Rp)</label>
                            <input type="number" step="1000" min="0" name="order_shipping_amount" value="<?= (float)($order['shipping_amount'] ?? 0) ?>" class="w-full px-3 py-2 rounded-xl border border-emerald-300 bg-emerald-50/40 font-mono font-bold text-emerald-900">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Catatan Instruksi Pembayaran</label>
                            <input type="text" name="inv_payment_note" value="<?= esc($invPaymentNote) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Email Konfirmasi Billing</label>
                            <input type="email" name="inv_billing_email" value="<?= esc($invBillingEmail) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
                        </div>
                    </div>
                </div>

                <!-- B. Penandatangan & Stempel -->
                <div class="space-y-3 pt-3 border-t border-slate-200">
                    <h4 class="font-extrabold uppercase tracking-wider text-purple-800 text-[11px] flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">draw</span>
                        <span>2. Pengaturan Penandatangan & Stempel Resmi</span>
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Ucapan Penutup</label>
                            <input type="text" name="inv_sign_greeting" value="<?= esc($invSignGreeting) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nama Pejabat Penandatangan</label>
                            <input type="text" name="inv_sign_name" value="<?= esc($invSignName) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Jabatan Penandatangan</label>
                            <input type="text" name="inv_sign_title" value="<?= esc($invSignTitle) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Divisi Penerbit Faktur</label>
                            <input type="text" name="inv_sign_division" value="<?= esc($invSignDivision) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Teks Stempel Utama</label>
                            <input type="text" name="inv_seal_text" value="<?= esc($invSealText) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold uppercase">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Sub-teks Stempel</label>
                            <input type="text" name="inv_seal_subtext" value="<?= esc($invSealSubtext) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono uppercase">
                        </div>
                    </div>
                </div>

                <!-- C. Identitas Kop & Lain-lain -->
                <div class="space-y-3 pt-3 border-t border-slate-200">
                    <h4 class="font-extrabold uppercase tracking-wider text-purple-800 text-[11px] flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">domain</span>
                        <span>3. Kop Surat, NPWP, NIB & Catatan Kaki Legal</span>
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nama Entitas Penerbit</label>
                            <input type="text" name="inv_issuer_name" value="<?= esc($invIssuerName) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-bold">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Sub-judul Brand Kop</label>
                            <input type="text" name="inv_issuer_subtitle" value="<?= esc($invIssuerSubtitle) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Hotline Billing</label>
                            <input type="text" name="inv_billing_phone" value="<?= esc($invBillingPhone) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Alamat Baris 1</label>
                            <input type="text" name="inv_issuer_address_1" value="<?= esc($invIssuerAddr1) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Alamat Baris 2</label>
                            <input type="text" name="inv_issuer_address_2" value="<?= esc($invIssuerAddr2) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">NPWP</label>
                                <input type="text" name="inv_issuer_npwp" value="<?= esc($invIssuerNpwp) ?>" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-mono">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">NIB</label>
                                <input type="text" name="inv_issuer_nib" value="<?= esc($invIssuerNib) ?>" class="w-full px-2.5 py-2 rounded-xl border border-slate-300 font-mono">
                            </div>
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block font-bold text-slate-700 mb-1">Catatan Kaki Legal (Footer Invoice)</label>
                            <input type="text" name="inv_footer_note" value="<?= esc($invFooterNote) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300">
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-200">
                    <a href="<?= base_url('admin/settings#invoice-settings') ?>" class="text-purple-700 hover:underline font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">open_in_new</span>
                        <span>Buka Halaman Pengaturan Admin Lengkap</span>
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeInvoiceSettingsModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold">Batal</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-700 via-blue-600 to-pink-500 text-white font-bold shadow-md flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            <span>Simpan & Terapkan ke Invoice</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openInvoiceSettingsModal() {
            const m = document.getElementById('invoiceSettingsModal');
            m.classList.remove('hidden');
            m.classList.add('flex');
        }
        function closeInvoiceSettingsModal() {
            const m = document.getElementById('invoiceSettingsModal');
            m.classList.add('hidden');
            m.classList.remove('flex');
        }
    </script>

</body>
</html>
