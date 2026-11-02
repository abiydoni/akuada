<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Berita Acara Serah Terima (BAST) & Surat Jalan') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            body { background: #fff !important; }
            .no-print { display: none !important; }
            .print-sheet { box-shadow: none !important; border: none !important; margin: 0 !important; padding: 0 !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 py-8 px-4">
<?php
$appName       = get_system_setting('app_name', 'AkuAda');
$companyEntity = get_system_setting('company_legal_name', 'PT AkuAda Solusi Pengadaan Indonesia');
$hqAddress     = get_system_setting('support_office_address', 'Gedung AkuAda Procurement Tower Lt. 18, Jakarta Selatan');
$hqPhone       = get_system_setting('support_phone', '(021) 5088-9900');
$bastNo        = $order['bast_number'] ?: ('BAST/' . strtoupper($appName) . '/' . date('Ym', strtotime($order['created_at'])) . '/' . str_pad($order['id'], 4, '0', STR_PAD_LEFT));
$doNo          = 'DO/' . date('Ym', strtotime($order['created_at'])) . '/' . str_pad($order['id'], 4, '0', STR_PAD_LEFT);
$poNo          = $order['po_number'] ?? $order['order_number'];
$isBastSigned  = in_array($order['order_status'], ['bast_signed', 'completed'], true);
?>

    <!-- Top Floating Action Bar -->
    <div class="max-w-4xl mx-auto mb-5 flex flex-wrap items-center justify-between gap-3 bg-white px-5 py-3.5 rounded-2xl border border-slate-200 shadow-sm no-print">
        <div class="flex items-center gap-2.5 text-xs">
            <span class="px-2.5 py-1 rounded-lg bg-emerald-100 text-emerald-800 font-extrabold uppercase">Dokumen Legal 3-Way Matching</span>
            <span class="font-mono font-bold text-slate-700"><?= esc($bastNo) ?></span>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="window.close()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                Tutup
            </button>
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Cetak BAST & Surat Jalan (PDF)</span>
            </button>
        </div>
    </div>

    <!-- Printable Document Sheet -->
    <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-12 space-y-7 print-sheet">

        <!-- Header Kop Dokumen -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pb-6 border-b-2 border-slate-900">
            <div class="space-y-1">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-black text-xl">
                        <?= esc(strtoupper(substr($appName, 0, 1))) ?>
                    </div>
                    <div>
                        <span class="text-xl font-black text-slate-900 tracking-tight"><?= esc($appName) ?><span class="text-pink-600">.</span></span>
                        <span class="block text-[10px] uppercase font-extrabold text-purple-700 tracking-wider"><?= esc($companyEntity) ?></span>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 max-w-md pt-1">
                    <?= esc($hqAddress) ?> | Telp: <?= esc($hqPhone) ?>
                </p>
            </div>

            <div class="text-left sm:text-right space-y-1">
                <span class="px-2.5 py-0.5 rounded bg-slate-900 text-white text-[10px] font-extrabold uppercase tracking-widest">Berita Acara Serah Terima & Surat Jalan</span>
                <h1 class="text-lg font-black font-mono text-purple-950 pt-1"><?= esc($bastNo) ?></h1>
                <p class="text-[11px] font-mono text-slate-500">No. Surat Jalan: <strong><?= esc($doNo) ?></strong></p>
                <p class="text-[11px] font-mono text-slate-500">Ref. Purchase Order: <strong><?= esc($poNo) ?></strong></p>
            </div>
        </div>

        <!-- Pernyataan Pembuka BAST -->
        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs leading-relaxed text-slate-700">
            Pada hari ini, tanggal <strong><?= date('d F Y', strtotime($order['updated_at'] ?? $order['created_at'])) ?></strong>, bertempat di lokasi gudang/kantor penerima, yang bertanda tangan di bawah ini telah melaksanakan pemeriksaan fisik, uji fungsi/spesifikasi (Quality Control), serta serah terima barang pengadaan korporat berdasarkan Surat Pesanan (Purchase Order) Nomor <strong class="font-mono text-purple-800"><?= esc($poNo) ?></strong> antara:
        </div>

        <!-- Identitas Pihak Pertama & Pihak Kedua -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
            <div class="p-4 rounded-2xl border border-blue-200 bg-blue-50/40 space-y-1.5">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-blue-800 block">PIHAK PERTAMA (PENYEDIA / PENGIRIM BARANG):</span>
                <h3 class="text-sm font-black text-slate-900"><?= esc($order['vendor_name']) ?></h3>
                <p class="text-slate-600">NPWP Badan: <strong class="font-mono"><?= esc($order['vendor_npwp'] ?? '-') ?></strong></p>
                <p class="text-slate-600">Alamat Asal: <?= esc($order['vendor_address'] ?? '-') ?>, <?= esc($order['vendor_city'] ?? '') ?></p>
                <p class="text-slate-600">Penanggung Jawab: <strong><?= esc($order['vendor_pic'] ?? 'Head of Logistics & Fulfillment') ?></strong> (<?= esc($order['vendor_phone'] ?? '-') ?>)</p>
            </div>

            <div class="p-4 rounded-2xl border border-purple-200 bg-purple-50/40 space-y-1.5">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-800 block">PIHAK KEDUA (PEMBELI / PENERIMA BARANG):</span>
                <h3 class="text-sm font-black text-slate-900"><?= esc($order['buyer_name']) ?></h3>
                <p class="text-slate-600">NPWP Badan: <strong class="font-mono"><?= esc($order['buyer_npwp'] ?? '-') ?></strong></p>
                <p class="text-slate-600">Alamat Serah Terima: <strong><?= esc($order['shipping_address'] ?: $order['buyer_address']) ?></strong></p>
                <p class="text-slate-600">PIC Penerima: <strong><?= esc($order['buyer_pic'] ?? 'Procurement & Receiving Manager') ?></strong> (<?= esc($order['buyer_phone'] ?? '-') ?>)</p>
            </div>
        </div>

        <!-- Tabel Rincian Barang yang Diserahterimakan -->
        <div class="space-y-2">
            <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700">Daftar Pemeriksaan Fisik & Kuantitas Barang yang Diserahterimakan:</h3>
            <div class="border border-slate-300 rounded-2xl overflow-hidden">
                <table class="w-full text-xs text-left border-collapse">
                    <thead class="bg-slate-900 text-white text-[10px] uppercase tracking-wider">
                        <tr>
                            <th class="py-3 px-3 text-center w-10">No</th>
                            <th class="py-3 px-4">Nama Barang & Spesifikasi Teknis</th>
                            <th class="py-3 px-3 w-32">Kode SKU</th>
                            <th class="py-3 px-3 text-center w-28">Qty PO</th>
                            <th class="py-3 px-3 text-center w-28">Qty Diterima</th>
                            <th class="py-3 px-4 text-center w-40">Hasil Inspeksi QC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($order['items'] as $idx => $item): ?>
                            <tr>
                                <td class="py-3 px-3 text-center font-mono text-slate-500"><?= $idx + 1 ?></td>
                                <td class="py-3 px-4 font-bold text-slate-900"><?= esc($item['product_name']) ?></td>
                                <td class="py-3 px-3 font-mono text-[11px] text-purple-800 font-semibold"><?= esc($item['sku'] ?? '-') ?></td>
                                <td class="py-3 px-3 text-center font-mono font-bold text-slate-700"><?= number_format($item['qty']) ?> <?= esc($item['uom']) ?></td>
                                <td class="py-3 px-3 text-center font-mono font-black text-emerald-700"><?= number_format($item['qty']) ?> <?= esc($item['uom']) ?></td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">
                                        ✓ Lengkap & Baik
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Klausul Pengesahan BAST -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 space-y-1.5 leading-relaxed">
            <strong class="text-slate-900 block uppercase text-[10px] tracking-wider">Ketentuan Berita Acara Serah Terima (BAST):</strong>
            <ol class="list-decimal list-inside space-y-1">
                <li>PIHAK PERTAMA telah menyerahkan seluruh barang tersebut di atas kepada PIHAK KEDUA dalam keadaan baru, baik, lengkap, dan sesuai spesifikasi Purchase Order <strong class="font-mono"><?= esc($poNo) ?></strong>.</li>
                <li>Dengan ditandatanganinya/disahkannya BAST ini, maka risiko atas barang beralih kepada PIHAK KEDUA dan dokumen ini menjadi dasar sah penagihan Faktur (Invoice Nomor: <strong class="font-mono"><?= esc($order['invoice_number']) ?></strong>) serta dimulainya perhitungan jatuh tempo Term of Payment (TOP).</li>
            </ol>
        </div>

        <!-- 3 Kolom Tanda Tangan Resmi -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 pt-4 text-xs text-center">
            <div class="border border-slate-200 rounded-2xl p-4 flex flex-col justify-between min-h-[165px]">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Diserahkan Oleh (PIHAK PERTAMA)</span>
                    <strong class="text-slate-800 text-[11px] block mt-0.5"><?= esc($order['vendor_name']) ?></strong>
                </div>
                <div class="my-3 flex items-center justify-center">
                    <span class="px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 font-mono text-[10px] font-bold">
                        SURAT JALAN TERVERIFIKASI
                    </span>
                </div>
                <div>
                    <strong class="underline text-slate-900 block"><?= esc($order['vendor_pic'] ?? 'Manager Logistik Vendor') ?></strong>
                    <span class="text-[10px] text-slate-500"><?= esc(get_system_setting('inv_vendor_default_title', 'Head of Corporate Sales')) ?></span>
                </div>
            </div>

            <div class="border border-slate-200 rounded-2xl p-4 flex flex-col justify-between min-h-[165px]">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400 block">Mengetahui / Verifikator Escrow</span>
                    <strong class="text-slate-800 text-[11px] block mt-0.5"><?= esc($companyEntity) ?></strong>
                </div>
                <div class="my-3 flex items-center justify-center">
                    <span class="px-2.5 py-1 rounded-lg bg-purple-50 text-purple-700 border border-purple-200 font-mono text-[10px] font-bold">
                        <?= esc(get_system_setting('inv_akuada_seal_text', 'AKUADA OFFICIAL SEAL')) ?>
                    </span>
                </div>
                <div>
                    <strong class="underline text-slate-900 block"><?= esc(get_system_setting('inv_akuada_signer_name', 'Dra. Maya Anggraini, Ak., CA.')) ?></strong>
                    <span class="text-[10px] text-slate-500">Quality & Fulfillment Assurance</span>
                </div>
            </div>

            <div class="border-2 <?= $isBastSigned ? 'border-emerald-300 bg-emerald-50/20' : 'border-amber-300 bg-amber-50/20' ?> rounded-2xl p-4 flex flex-col justify-between min-h-[165px]">
                <div>
                    <span class="text-[10px] font-bold uppercase text-purple-800 block">Diterima & Disahkan (PIHAK KEDUA)</span>
                    <strong class="text-slate-800 text-[11px] block mt-0.5"><?= esc($order['buyer_name']) ?></strong>
                </div>
                <div class="my-3 flex items-center justify-center">
                    <?php if ($isBastSigned): ?>
                        <div class="px-3 py-1.5 rounded-xl bg-emerald-100 text-emerald-800 border border-emerald-300 font-extrabold text-[10px] uppercase tracking-wider">
                            ✓ BAST SAH & TERVERIFIKASI DIGITAL
                        </div>
                    <?php else: ?>
                        <div class="px-3 py-1.5 rounded-xl bg-amber-100 text-amber-800 border border-amber-300 font-bold text-[10px] uppercase">
                            Menunggu Tanda Tangan Penerima
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <strong class="underline text-slate-900 block"><?= esc($order['buyer_pic'] ?? 'PIC Penerima Barang') ?></strong>
                    <span class="text-[10px] text-slate-500"><?= esc(get_system_setting('inv_buyer_default_title', 'Senior Procurement Manager')) ?></span>
                </div>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Dokumen Berita Acara Serah Terima (BAST) & Surat Jalan ini diterbitkan secara elektronik melalui sistem e-Procurement <?= esc($appName) ?> dan sah sebagai bukti serah terima barang korporat.
        </div>
    </div>
</body>
</html>

