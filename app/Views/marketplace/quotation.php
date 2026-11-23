<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Surat Penawaran Harga Resmi - AkuAda B2B') ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            body { background: #fff !important; padding: 0 !important; color: #000 !important; }
            .no-print { display: none !important; }
            .print-sheet { box-shadow: none !important; border: 1px solid #cbd5e1 !important; margin: 0 !important; padding: 10mm 12mm !important; max-width: 100% !important; border-radius: 0 !important; }
            .page-break-inside-avoid { page-break-inside: avoid; }
            @page { margin: 8mm 10mm; size: A4 portrait; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen py-6 sm:py-10 px-4">
<?php
    $appName       = get_system_setting('app_name', 'AkuAda');
    $appWebsite    = get_system_setting('app_website', 'akuada.appsbee.my.id');
    $issuerName    = get_system_setting('inv_issuer_name', 'PT AKUADA TEKNOLOGI PENGADAAN NUSANTARA');
    $issuerSub     = get_system_setting('inv_issuer_subtitle', 'Marketplace B2B & Platform Pengadaan Operasional Terpadu');
    $issuerAddr1   = get_system_setting('inv_issuer_address_1', 'Randuares Kel. Kumpulrejo, Kec. Argomulyo');
    $issuerAddr2   = get_system_setting('inv_issuer_address_2', 'Kota Salatiga 50734');
    $issuerNpwp    = get_system_setting('inv_issuer_npwp', '-');
    $issuerNib     = get_system_setting('inv_issuer_nib', '-');
    $billingEmail  = get_system_setting('contact_email') ?: get_system_setting('inv_billing_email', 'akuada@appsbee.my.id');

    $signName      = get_system_setting('inv_sign_name', 'Doni Abiyantoro');
    $signTitle     = get_system_setting('inv_sign_title', 'Finance & Treasury Director');
    $signDivision  = get_system_setting('inv_sign_division', 'Commercial & Corporate Sales Division');
    $sealText      = get_system_setting('inv_seal_text', 'AKUADA');
    $sealSubtext   = get_system_setting('inv_seal_subtext', 'OFFICIAL SEAL');

    $ppnRateVal    = get_ppn_rate();
    $ppnRateLabel  = format_tax_rate($ppnRateVal);
?>

    <!-- Top Action Bar (Hidden on Print) -->
    <div class="max-w-4xl mx-auto mb-6 no-print flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-2.5">
            <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-[11px] font-extrabold uppercase tracking-wider flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px]">request_quote</span>
                <span>Surat Penawaran Harga Resmi</span>
            </span>
            <span class="text-xs text-slate-500 font-mono font-bold"><?= esc($quotationNumber) ?></span>
        </div>

        <div class="flex items-center gap-2">
            <a href="<?= base_url('cart') ?>" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold transition flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali ke Keranjang</span>
            </a>

            <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs flex items-center gap-1.5 shadow-md transition">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Cetak / Unduh PDF (Ctrl+P)</span>
            </button>

            <a href="<?= base_url('checkout') ?>" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md transition">
                <span class="material-symbols-outlined text-[16px]">assignment_turned_in</span>
                <span>Lanjut Terbitkan PO</span>
            </a>
        </div>
    </div>

    <!-- Main Printable Sheet (A4 Dimensions) -->
    <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-12 space-y-6 print-sheet">

        <!-- KOP SURAT RESMI -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pb-6 border-b-2 border-slate-900">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <img src="<?= base_url('apple-touch-icon.png') ?>" alt="AkuAda" class="w-12 h-12 rounded-2xl object-contain shadow-xs">
                    <div>
                        <span class="text-2xl font-black text-slate-900 tracking-tight font-sans">
                            <span class="text-purple-700">Aku</span><span class="text-blue-600">Ada</span><span class="text-pink-600">.</span>
                        </span>
                        <span class="block text-[11px] font-extrabold uppercase text-purple-900 tracking-wider"><?= esc($issuerName) ?></span>
                    </div>
                </div>
                <p class="text-xs text-slate-600 max-w-lg pt-1 leading-relaxed">
                    <?= esc($issuerAddr1) ?><?= (!empty($issuerAddr2) ? ', ' . esc($issuerAddr2) : '') ?><br>
                    <?php if (!empty($issuerNpwp) && $issuerNpwp !== '-'): ?><strong>NPWP:</strong> <?= esc($issuerNpwp) ?> &bull; <?php endif; ?>
                    <?php if (!empty($issuerNib) && $issuerNib !== '-'): ?><strong>NIB:</strong> <?= esc($issuerNib) ?> &bull; <?php endif; ?>
                    <strong>Web Resmi:</strong> <a href="https://<?= esc($appWebsite) ?>" target="_blank" class="hover:underline font-bold text-purple-700"><?= esc($appWebsite) ?></a> &bull; 
                    <strong>Layanan:</strong> <a href="<?= base_url('chat') ?>" target="_blank" class="hover:underline font-bold text-blue-600 inline-flex items-center gap-0.5"><span class="material-symbols-outlined text-[13px] text-blue-600">forum</span> Live Chat Web Platform AkuAda</a> &bull; 
                    <strong>Email:</strong> <?= esc($billingEmail) ?>
                </p>
            </div>

            <div class="text-left sm:text-right space-y-1 self-stretch sm:self-auto">
                <div class="inline-block px-3 py-1 rounded-lg bg-slate-900 text-white text-[11px] font-extrabold uppercase tracking-wider">
                    Official Formal Quotation
                </div>
                <div class="text-xs font-mono font-black text-purple-950 pt-1">
                    No: <?= esc($quotationNumber) ?>
                </div>
                <?php if (!empty($rfqRef)): ?>
                    <div class="text-[11px] text-slate-500 font-mono">
                        Ref RFQ: <strong><?= esc($rfqRef) ?></strong>
                    </div>
                <?php endif; ?>
                <div class="text-[11px] text-slate-500">
                    Tanggal Terbit: <strong><?= date('d M Y', strtotime($quotationDate)) ?></strong>
                </div>
                <div class="text-[11px] text-emerald-700 font-semibold">
                    Masa Berlaku s/d: <strong><?= date('d M Y', strtotime($validUntil)) ?></strong> (14 Hari)
                </div>
            </div>
        </div>

        <!-- JUDUL & INFORMASI PENAWARAN -->
        <div class="space-y-1 text-center py-2">
            <h1 class="text-lg font-black text-slate-900 tracking-wide uppercase font-sans">
                Surat Penawaran Harga Pengadaan Barang (Quotation)
            </h1>
            <p class="text-xs text-slate-500">
                Dokumen ini merupakan penawaran harga resmi, valid, dan mengikat sesuai ketersediaan stok &amp; kebijakan grosir B2B platform AkuAda.
            </p>
        </div>

        <!-- DITUJUKAN KEPADA (BUYER INFO) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
            <div class="space-y-1">
                <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">Ditujukan Kepada Perusahaan Pemesan:</span>
                <h3 class="text-sm font-black text-slate-900"><?= esc($buyer['company_name'] ?? 'Corporate Buyer') ?></h3>
                <p class="text-slate-600 leading-snug">
                    <strong>UP:</strong> <?= esc($buyer['pic_name'] ?? 'Bpk/Ibu Pimpinan Procurement') ?> (<?= esc($buyer['position'] ?? 'Procurement Officer') ?>)<br>
                    <strong>Email:</strong> <?= esc($buyer['email'] ?? '-') ?> &bull; <strong>Saluran Koordinasi:</strong> <a href="<?= base_url('chat') ?>" target="_blank" class="hover:underline font-bold text-purple-700 inline-flex items-center gap-0.5"><span class="material-symbols-outlined text-[13px] text-purple-600">forum</span> Live Chat Portal AkuAda (Terverifikasi)</a>
                </p>
            </div>
            <div class="space-y-1 sm:border-l sm:border-slate-200 sm:pl-4">
                <span class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider block">Alamat Pengiriman Kebutuhan:</span>
                <h4 class="text-sm font-black text-slate-900 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-purple-600">storefront</span>
                    <span><?= esc($vendor['company_name'] ?? ($items[0]['vendor_name'] ?? 'Mitra Vendor Pemasok AkuAda')) ?></span>
                </h4>
                <div class="pt-1 text-[11px] text-purple-700 font-bold flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">local_shipping</span>
                    <span>Wilayah Layanan: <?= esc(get_system_setting('free_shipping_region', 'Jabodetabek & Nasional')) ?></span>
                </div>
            </div>
        </div>

        <!-- TABEL RINCIAN ITEM BARANG YANG DIPILIH -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-purple-600">inventory_2</span>
                    <span>Rincian Barang &amp; Spesifikasi Teknis Terpilih:</span>
                </span>
                <span class="text-[11px] text-slate-500 font-semibold"><?= count($items) ?> Item Penawaran</span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-900 text-white font-bold text-[11px]">
                            <th class="py-2.5 px-3 w-10 text-center">No</th>
                            <th class="py-2.5 px-3 w-28">SKU</th>
                            <th class="py-2.5 px-3">Nama Barang &amp; Spesifikasi</th>
                            <th class="py-2.5 px-3 w-32">Mitra Vendor</th>
                            <th class="py-2.5 px-3 text-center w-24">Jumlah</th>
                            <th class="py-2.5 px-3 text-right w-28">Harga Satuan (Rp)</th>
                            <th class="py-2.5 px-3 text-right w-28">Subtotal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        <?php $no = 1; foreach ($items as $item): ?>
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                                <td class="py-3 px-3 font-mono font-bold text-purple-900 text-[11px] whitespace-nowrap">
                                    <?= esc($item['sku']) ?>
                                </td>
                                <td class="py-3 px-3">
                                    <div class="font-bold text-slate-900 leading-snug"><?= esc($item['name']) ?></div>
                                    <?php if (!empty($item['brand']) && $item['brand'] !== 'Generic'): ?>
                                        <span class="inline-block px-1.5 py-0.5 rounded bg-slate-100 text-[10px] text-slate-600 font-semibold mt-0.5">
                                            Merk: <?= esc($item['brand']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <?php if (!empty($item['spec_details'])): ?>
                                        <p class="text-[10px] text-slate-500 mt-0.5 line-clamp-2 leading-tight">
                                            <?= esc($item['spec_details']) ?>
                                        </p>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-3 text-[11px] text-slate-600">
                                    <?= esc($item['vendor_name'] ?? 'Mitra Vendor') ?>
                                </td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <span class="font-extrabold text-slate-900"><?= number_format($item['qty']) ?></span>
                                    <span class="text-[10px] text-slate-500"><?= esc($item['uom']) ?></span>
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-slate-800 whitespace-nowrap">
                                    <?= format_rupiah($item['unit_price'], false) ?>
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-extrabold text-slate-900 whitespace-nowrap">
                                    <?= format_rupiah($item['subtotal'], false) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KALKULASI & TOTAL FINANSIAL -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-6 pt-2">
            <!-- Left Terms & Conditions (7 Cols) -->
            <div class="sm:col-span-7 space-y-3">
                <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-200 text-xs space-y-2">
                    <span class="font-extrabold text-purple-950 flex items-center gap-1.5 uppercase text-[11px] tracking-wider">
                        <span class="material-symbols-outlined text-[16px] text-purple-700">verified</span>
                        <span>Syarat &amp; Ketentuan Komersial (Terms of Commerce):</span>
                    </span>
                    <ol class="text-[11px] text-purple-900 space-y-1 list-decimal list-inside leading-relaxed">
                        <li><strong>Harga Mengikat:</strong> Seluruh harga satuan yang tercantum mengikat selama masa penawaran berlaku (14 hari sejak tanggal terbit).</li>
                        <li><strong>Faktur Pajak e-Faktur:</strong> Harga barang dikenakan PPN <?= esc($ppnRateLabel) ?> resmi DJP dan faktur pajak elektronik akan diterbitkan atas nama perusahaan pemesan.</li>
                        <li><strong>Fasilitas Kredit TOP:</strong> Bagi Corporate Buyer terverifikasi, pembayaran dapat menggunakan fasilitas Term of Payment (TOP 14 / 30 / 60 / 90 hari) terhitung sejak Berita Acara Serah Terima (BAST) ditandatangani.</li>
                        <li><strong>Rekening Escrow Resmi:</strong> Pembayaran sah ditransfer ke rekening penampung resmi: <strong>Bank Mandiri (122-00-1122334-5) a/n AkuAda</strong> atau <strong>Bank BCA (535-088-9900)</strong>.</li>
                        <li><strong>Konfirmasi Pemesanan:</strong> Untuk melanjutkan penawaran ini menjadi pesanan resmi, silakan klik tombol <em>Lanjut Terbitkan PO</em> atau hubungi tim via <a href="<?= base_url('chat') ?>" target="_blank" class="text-purple-700 underline font-bold">Live Chat resmi di web portal AkuAda (<?= esc($appWebsite) ?>)</a>. Seluruh komunikasi &amp; negosiasi diproses melalui sistem chat terpadu platform.</li>
                    </ol>
                </div>
            </div>

            <!-- Right Financial Summary (5 Cols) -->
            <div class="sm:col-span-5 bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2.5 text-xs">
                <div class="flex justify-between items-center text-slate-600">
                    <span>Subtotal Nilai Barang:</span>
                    <span class="font-mono font-bold text-slate-900"><?= format_rupiah($subtotal) ?></span>
                </div>
                <div class="flex justify-between items-center text-slate-600">
                    <span>PPN <?= esc($ppnRateLabel) ?> (Faktur Pajak):</span>
                    <span class="font-mono font-bold text-blue-700"><?= format_rupiah($ppnTotal) ?></span>
                </div>
                <div class="flex justify-between items-center text-slate-600">
                    <span>Biaya Pengiriman Logistik:</span>
                    <span class="font-mono font-bold text-emerald-600">
                        <?= ($shippingAmount > 0) ? format_rupiah($shippingAmount) : 'GRATIS (Corporate)' ?>
                    </span>
                </div>
                <div class="border-t-2 border-slate-300 pt-2 flex justify-between items-baseline">
                    <span class="font-extrabold text-slate-900 text-sm">TOTAL PENAWARAN:</span>
                    <span class="font-mono font-black text-purple-950 text-base"><?= format_rupiah($grandTotal) ?></span>
                </div>
                <div class="p-2.5 rounded-xl bg-purple-100/70 border border-purple-200 text-[10px] text-purple-950 leading-relaxed font-semibold">
                    <em>Terbilang:</em> <?= esc(terbilang_rupiah($grandTotal)) ?>
                </div>
            </div>
        </div>

        <!-- TANDA TANGAN & STEMPEL RESMI -->
        <div class="pt-6 border-t border-slate-200 page-break-inside-avoid">
            <div class="grid grid-cols-2 gap-8 text-center text-xs">
                <!-- Sisi Kiri: Penerbit AkuAda -->
                <div class="space-y-1 relative">
                    <span class="text-slate-500 text-[11px]">Diterbitkan Resmi Oleh:</span>
                    <div class="font-extrabold text-slate-900 uppercase tracking-wider text-[11px]">
                        <?= esc($issuerName) ?>
                    </div>
                    
                    <!-- Area Stempel Digital & TTD -->
                    <div class="h-24 flex items-center justify-center relative my-1">
                        <!-- Digital Seal Graphic -->
                        <div class="w-20 h-20 rounded-full border-2 border-purple-700/60 flex flex-col items-center justify-center text-center p-1 bg-purple-50/40 rotate-[-8deg] shadow-xs">
                            <span class="text-[8px] font-black uppercase text-purple-900 leading-tight"><?= esc($sealText) ?></span>
                            <span class="material-symbols-outlined text-purple-700 text-[20px]">verified</span>
                            <span class="text-[7px] font-extrabold tracking-widest text-purple-800 uppercase"><?= esc($sealSubtext) ?></span>
                        </div>
                    </div>

                    <div class="font-black text-slate-900 underline text-xs">
                        <?= esc($signName) ?>
                    </div>
                    <div class="text-[11px] text-slate-600 font-semibold">
                        <?= esc($signTitle) ?>
                    </div>
                    <div class="text-[10px] text-slate-400">
                        <?= esc($signDivision) ?>
                    </div>
                </div>

                <!-- Sisi Kanan: Persetujuan Pemesan / Buyer -->
                <div class="space-y-1">
                    <span class="text-slate-500 text-[11px]">Disetujui &amp; Dikonfirmasi Oleh:</span>
                    <div class="font-extrabold text-slate-900 uppercase tracking-wider text-[11px]">
                        <?= esc($buyer['company_name'] ?? 'Pihak Pemesan') ?>
                    </div>
                    
                    <div class="h-24 flex items-end justify-center my-1 pb-2">
                        <span class="text-[10px] text-slate-300 italic border-b border-dashed border-slate-300 px-8 pb-1">
                            ( Tanda Tangan &amp; Stempel Perusahaan )
                        </span>
                    </div>

                    <div class="font-black text-slate-900 underline text-xs">
                        <?= esc($buyer['pic_name'] ?? '................................................') ?>
                    </div>
                    <div class="text-[11px] text-slate-600 font-semibold">
                        <?= esc($buyer['position'] ?? 'Procurement / Finance Manager') ?>
                    </div>
                    <div class="text-[10px] text-slate-400">
                        Tanggal Persetujuan: .......................................
                    </div>
                </div>
            </div>
        </div>

        <!-- FOOTER DOKUMEN -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center text-[10px] text-slate-400 gap-2">
            <span>Surat Penawaran Harga Elektronik Sah AkuAda B2B E-Procurement Platform</span>
            <span>Halaman 1 dari 1 &bull; Dicetak Otomatis pada <?= date('d/m/Y H:i') ?> WIB</span>
        </div>

    </div>

</body>
</html>
