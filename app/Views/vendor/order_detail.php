<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<?php
    $fee = (float)($order['total_vendor_fee'] ?? 0);
    $netDpp = (float)($order['vendor_net_payout'] ?? ($order['subtotal'] - $fee));
    $totalReceive = $netDpp;
    $vPayoutStatus = $order['vendor_payout_status'] ?? 'unpaid';
    $canRequest = in_array($order['order_status'], ['delivered', 'bast_signed', 'completed']);
    $isTop = str_starts_with($order['payment_method'] ?? '', 'top_');
    $topDays = $isTop ? (int)str_replace('top_', '', $order['payment_method']) : 30;

    $seqId = str_pad((string)$order['id'], 4, '0', STR_PAD_LEFT);
    $orderYm = !empty($order['created_at']) ? date('Ym', strtotime($order['created_at'])) : date('Ym');
    $dispBastNumber = !empty($order['bast_number']) ? $order['bast_number'] : ('BAST/AKUADA/' . $orderYm . '/' . $seqId);
    $dispVendorInvoice = !empty($order['vendor_invoice_number']) ? $order['vendor_invoice_number'] : (!empty($order['invoice_number']) ? $order['invoice_number'] : ('VINV/' . $orderYm . '/' . $seqId));
    $dispTaxInvoice = !empty($order['tax_invoice_number']) ? $order['tax_invoice_number'] : ('010.002-26.' . sprintf('%08d', abs(crc32('order_' . $order['id']))));
?>

<div class="space-y-6">
    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('vendor/orders') ?>" class="hover:text-blue-600">Pesanan PO & BAST</a>
                <span>/</span>
                <span class="font-mono text-blue-700 font-bold"><?= esc($order['po_number'] ?? $order['order_number']) ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900">Pemenuhan Pesanan PO & Penerbitan Surat Jalan BAST</h1>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="<?= base_url('vendor/orders/vendor-invoice/' . $order['id']) ?>" target="_blank" class="px-4 py-2 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-emerald-900 font-extrabold text-xs transition flex items-center gap-1.5 shadow-sm border border-emerald-200">
                <span class="material-symbols-outlined text-[18px] text-emerald-700">receipt_long</span>
                <span>Cetak Faktur Tagihan ke AkuAda</span>
            </a>
            <a href="<?= base_url('vendor/orders/bast/' . $order['id']) ?>" target="_blank" class="px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-900 font-bold text-xs transition flex items-center gap-1.5 shadow-sm border border-blue-200">
                <span class="material-symbols-outlined text-[18px] text-blue-700">contract</span>
                <span>BAST & Surat Jalan</span>
            </a>
            <a href="<?= base_url('vendor/chat?key=admin_comp_' . $order['vendor_company_id']) ?>" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs transition flex items-center gap-1.5 shadow-md shadow-blue-600/20">
                <span class="material-symbols-outlined text-[18px]">forum</span>
                <span>Chat dengan AkuAda</span>
            </a>
            <div>
                <?= badge_order_status($order['order_status']) ?>
            </div>
        </div>
    </div>

    <!-- Stepper (8 Tahapan Standar B2B) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">route</span>
                <h3 class="text-sm font-bold text-slate-900">Progres Pemenuhan Pengadaan (8 Tahap Standar B2B)</h3>
            </div>
        </div>
        <?= render_order_progress_stepper($order['order_status']) ?>
    </div>

    <!-- 1. INFORMASI TUJUAN PENGIRIMAN & KETENTUAN PO PEMBELI -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        <!-- Tujuan Pengiriman Buyer -->
        <div class="bg-white p-5 rounded-2xl border-2 border-blue-200 shadow-sm space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
                <span class="text-[10px] font-extrabold text-blue-700 uppercase tracking-widest flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">location_on</span>
                    TUJUAN PENGIRIMAN (CORPORATE BUYER):
                </span>
                <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-800 font-bold font-mono text-[10px] border border-blue-200">
                    PO: <?= esc($order['po_number'] ?? $order['order_number']) ?>
                </span>
            </div>
            <div>
                <strong class="text-base text-slate-900 block font-black"><?= esc($order['buyer_name']) ?></strong>
                <p class="text-slate-500 font-mono text-[11px] mt-0.5">NPWP: <strong><?= esc($order['buyer_npwp'] ?? '-') ?></strong></p>
            </div>
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                <span class="text-slate-500 font-bold text-[10px] uppercase block tracking-wider">Alamat Lengkap Pengiriman:</span>
                <p class="text-slate-800 font-semibold leading-relaxed">
                    <?= esc($order['shipping_address'] ?: $order['buyer_address']) ?>
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-2 pt-1 text-slate-600">
                <div>
                    <span class="text-slate-400 block text-[10px]">PIC Lapangan Penerima:</span>
                    <strong class="text-slate-900"><?= esc($order['buyer_pic'] ?? '-') ?></strong>
                </div>
                <div>
                    <span class="text-slate-400 block text-[10px]">No. Telepon / WA:</span>
                    <strong class="font-mono text-blue-700"><?= esc($order['buyer_phone'] ?? '-') ?></strong>
                </div>
            </div>
            <?php if (!empty($order['customer_notes'])): ?>
                <div class="p-3 rounded-xl bg-amber-50/80 border border-amber-200 text-amber-950 space-y-0.5">
                    <strong class="text-[10px] uppercase font-bold text-amber-800 block">Instruksi Khusus dari Pembeli:</strong>
                    <p class="italic">"<?= esc($order['customer_notes']) ?>"</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Ketentuan Komersial PO & Garansi AkuAda -->
        <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-blue-950 text-white p-5 rounded-2xl shadow-sm border border-slate-800 space-y-3.5 flex flex-col justify-between">
            <div class="space-y-2">
                <div class="flex items-center justify-between border-b border-white/10 pb-2.5">
                    <span class="text-[10px] font-extrabold text-blue-300 uppercase tracking-widest flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px]">verified_user</span>
                        KETENTUAN PEMBAYARAN PO:
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-200 border border-purple-400/30 text-[10px] font-bold">
                        <?= $isTop ? 'TOP ' . $topDays . ' Hari' : 'Kredit AkuAda' ?>
                    </span>
                </div>
                <div>
                    <span class="text-xs text-blue-200 block">Metode Pembayaran Pembeli:</span>
                    <strong class="text-sm font-black text-white">Term of Payment <?= $topDays ?> Hari (TOP <?= $topDays ?>) • DP 0%</strong>
                    <p class="text-[11px] text-blue-200/80 mt-1 leading-relaxed">
                        Pembeli resmi menggunakan fasilitas kredit tempo korporat tanpa uang muka (DP 0%). Pembayaran hak bersih penjualan Anda dijamin 100% aman oleh platform AkuAda.
                    </p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-white/10 border border-white/15 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] text-blue-200 font-medium">Estimasi Hak Bersih Vendor:</span>
                    <strong class="text-base font-black font-mono text-emerald-300"><?= format_rupiah($totalReceive) ?></strong>
                </div>
                <div class="pt-2 border-t border-white/10 flex items-center justify-between text-[11px]">
                    <span class="text-blue-300">Pencairan Dana Vendor:</span>
                    <span class="font-bold text-amber-300 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">lock_clock</span> Dibuka setelah BAST disahkan
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-between text-[11px] text-blue-200 pt-1">
                <span>e-Faktur PPN <?= esc(format_tax_rate()) ?>:</span>
                <strong class="text-white"><?= !empty($order['tax_invoice_required']) ? 'Wajib Diterbitkan DJP' : 'Tidak Diterbitkan' ?></strong>
            </div>
        </div>
    </div>

    <!-- 2. PANEL DESAIN CUSTOM DARI AKUADA & UPLOAD MOCKUP PRA-PRODUKSI (JIKA ADA) -->
    <?php
        $dStatus = $order['design_status'] ?? 'none';
    ?>
    <?php if ($dStatus !== 'none'): ?>
        <div class="bg-white rounded-3xl border-2 border-blue-200 shadow-sm p-6 space-y-4 text-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[22px]">palette</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Instruksi Desain Custom dari AkuAda & Upload Mockup Produksi</h3>
                        <p class="text-[11px] text-slate-500">Seluruh koordinasi desain dan persetujuan mockup dijembatani satu pintu melalui Tim Pengadaan AkuAda.</p>
                    </div>
                </div>
                <?php if ($dStatus === 'approved_by_buyer'): ?>
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-300 font-extrabold text-[11px]">
                        ✅ DESAIN DI-ACC (SIAP PRODUKSI MASSAL)
                    </span>
                <?php elseif ($dStatus === 'revision_requested'): ?>
                    <span class="px-3 py-1 rounded-full bg-rose-100 text-rose-900 border border-rose-300 font-extrabold text-[11px] animate-pulse">
                        🔄 ADA PERMINTAAN REVISI MOCKUP
                    </span>
                <?php elseif ($dStatus === 'submitted_to_admin'): ?>
                    <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 font-bold text-[11px]">
                        ⏳ File Desain Sedang Dikurasi Tim AkuAda
                    </span>
                <?php else: ?>
                    <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-900 border border-blue-300 font-bold text-[11px]">
                        Status: <?= esc(str_replace('_', ' ', strtoupper($dStatus))) ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if ($dStatus === 'submitted_to_admin'): ?>
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 flex items-center gap-3">
                    <span class="material-symbols-outlined text-amber-600 text-[24px]">hourglass_top</span>
                    <div>
                        <strong class="font-bold block">File Desain Sedang Diverifikasi oleh Tim AkuAda</strong>
                        <p class="text-[11px] text-amber-800">Pemesan telah melampirkan detail desain untuk PO ini. Tim AkuAda sedang melakukan pengecekan standar spesifikasi sebelum meneruskannya ke dashboard Anda.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 items-start">
                    <!-- Kolom Kiri: Instruksi & File Desain Resmi dari AkuAda -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                        <strong class="text-slate-900 font-bold block">1. Spesifikasi & File Desain Resmi dari Tim AkuAda:</strong>
                        <p class="text-slate-700 bg-white p-3 rounded-xl border border-slate-200 leading-relaxed">
                            <?= !empty($order['design_notes_vendor']) ? nl2br(esc($order['design_notes_vendor'])) : (!empty($order['design_specs']) ? nl2br(esc($order['design_specs'])) : 'Ikuti lampiran file desain.') ?>
                        </p>
                        <?php if (!empty($order['design_file_vendor'])): ?>
                            <a href="<?= base_url($order['design_file_vendor']) ?>" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">download</span>
                                <span>Unduh File Master Desain dari AkuAda</span>
                            </a>
                        <?php endif; ?>

                        <?php if (!empty($order['mockup_notes'])): ?>
                            <div class="p-3 rounded-xl <?= $dStatus === 'revision_requested' ? 'bg-rose-50 border border-rose-200 text-rose-900' : 'bg-emerald-50 border border-emerald-200 text-emerald-900' ?>">
                                <strong class="block font-bold text-[11px]">Catatan Status Mockup / Feedback:</strong>
                                <p class="mt-0.5"><?= nl2br(esc($order['mockup_notes'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Kolom Kanan: Upload Mockup Pra-Produksi ke AkuAda -->
                    <form action="<?= base_url('vendor/orders/upload-mockup') ?>" method="POST" enctype="multipart/form-data" class="p-4 rounded-2xl bg-blue-50/40 border border-blue-200 space-y-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <strong class="text-blue-950 font-bold block">2. Kirim Gambar / File Mockup Pra-Produksi ke Tim AkuAda:</strong>
                        <div>
                            <label class="block text-slate-700 font-semibold mb-1 text-[11px]">Upload File Preview / Mockup (JPG, PNG, PDF, ZIP):</label>
                            <input type="file" name="mockup_file" accept=".pdf,.png,.jpg,.jpeg,.webp,.zip" class="w-full bg-white rounded-xl border border-slate-300 px-3 py-1.5 text-[11px] file:mr-2 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:font-bold file:bg-blue-600 file:text-white cursor-pointer">
                            <?php if (!empty($order['mockup_file_vendor'])): ?>
                                <a href="<?= base_url($order['mockup_file_vendor']) ?>" target="_blank" class="text-[11px] text-blue-700 font-bold hover:underline inline-block mt-1">✓ File Mockup Anda sudah terunggah (Klik untuk lihat)</a>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-slate-700 font-semibold mb-1 text-[11px]">Keterangan Mockup untuk Tim AkuAda:</label>
                            <textarea name="mockup_notes" rows="2" placeholder="Contoh: Berikut preview posisi cetak logo dan takaran warna sebelum naik cetak massal..." class="w-full px-3 py-2 rounded-xl border border-slate-300 bg-white"></textarea>
                        </div>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold flex items-center gap-1.5 shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">cloud_upload</span>
                            <span>Kirim Mockup ke Tim AkuAda</span>
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- 3. DAFTAR ITEM BARANG YANG HARUS DISIAPKAN & DIKIRIM -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-6 space-y-4">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">inventory_2</span>
                Daftar Item Barang yang Harus Dikirim
            </h3>
            <span class="text-xs text-slate-500">Total <?= count($order['items']) ?> Jenis Item</span>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="w-full text-xs text-left border-collapse min-w-[600px]">
                <thead class="bg-slate-50/90 border-b border-slate-200 text-slate-600 font-semibold uppercase text-[11px] tracking-wider">
                    <tr>
                        <th class="px-4 py-3">Nama Barang & SKU</th>
                        <th class="px-4 py-3 text-center w-28">Kuantitas</th>
                        <th class="px-4 py-3 text-right w-36">Harga Satuan</th>
                        <th class="px-4 py-3 text-right w-44">Fee Platform</th>
                        <th class="px-4 py-3 text-right w-44">Hak Bersih Item</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($order['items'] as $it): ?>
                        <tr>
                            <td class="px-4 py-3.5">
                                <div class="font-bold text-slate-900"><?= esc($it['product_name']) ?></div>
                                <div class="text-[10px] font-mono text-blue-700 font-bold">SKU: <?= esc($it['sku']) ?></div>
                            </td>
                            <td class="px-4 py-3.5 text-center font-bold text-slate-800">
                                <?= number_format($it['qty']) ?> <?= esc($it['uom']) ?>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono text-slate-700">
                                <?= format_rupiah($it['unit_price']) ?>
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono text-purple-700">
                                -<?= format_rupiah($it['vendor_fee_amount']) ?> (<?= (float)$it['vendor_fee_percent'] ?>%)
                            </td>
                            <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-700">
                                <?= format_rupiah($it['vendor_net_amount']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Rincian Nilai Penjualan & Hak Bersih -->
        <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row justify-end items-end">
            <div class="w-full sm:w-96 space-y-2 text-xs bg-slate-50 p-4 rounded-xl border border-slate-200">
                <div class="flex justify-between">
                    <span class="text-slate-600">Total Penjualan Barang (DPP):</span>
                    <strong class="font-mono text-slate-900"><?= format_rupiah($order['subtotal']) ?></strong>
                </div>
                <div class="flex justify-between text-purple-800">
                    <span>Potongan Biaya Layanan Platform:</span>
                    <strong class="font-mono">- <?= format_rupiah($order['total_vendor_fee'] ?? 0) ?></strong>
                </div>
                <div class="flex justify-between pt-2 border-t border-slate-200">
                    <span class="font-bold text-slate-900">Hak Bersih Vendor (Net Payout):</span>
                    <strong class="text-base font-black font-mono text-emerald-700"><?= format_rupiah($order['vendor_net_payout'] ?? ($order['subtotal'] - ($order['total_vendor_fee'] ?? 0))) ?></strong>
                </div>
                <div class="text-[10px] text-slate-500 pt-1 border-t border-slate-200">
                    PPN <?= esc(format_tax_rate()) ?> (<?= format_rupiah($order['ppn_amount']) ?>) dikelola sesuai regulasi faktur pajak B2B DJP.
                </div>
            </div>
        </div>
    </div>

    <!-- 4. OPERASIONAL PENGIRIMAN & UPDATE TAHAPAN (KURIR / DRIVER / EKSPEDISI) -->
    <div class="space-y-6 text-xs">
        <?php if (in_array($order['order_status'], ['completed', 'bast_signed'])): ?>
            <!-- STATUS TERKUNCI: Pesanan Sudah Selesai oleh Buyer -->
            <div class="bg-white p-6 rounded-2xl border border-slate-300 shadow-sm space-y-3">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="material-symbols-outlined text-slate-500">lock</span>
                    <h3 class="text-sm font-bold text-slate-900">Status Transaksi Selesai & Terkunci</h3>
                </div>
                <div class="p-4 rounded-xl bg-slate-100/90 border border-slate-200 text-slate-700 space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-600 text-[20px]">verified</span>
                        <strong class="text-xs font-black text-slate-900 uppercase">Pesanan Telah Selesai & Diterima Sah oleh Buyer</strong>
                    </div>
                    <p class="text-[11px] text-slate-600 leading-relaxed">
                        Pesanan ini telah dikonfirmasi selesai secara sah oleh pihak Corporate Buyer (<strong><?= esc($order['delivered_by_name'] ?? 'Pihak Pemesan') ?></strong>) pada <?= !empty($order['delivered_at']) ? (date('d M Y, H:i', strtotime($order['delivered_at'])) . ' WIB') : (date('d M Y') . ' WIB') ?>.
                        Seluruh tahapan pengadaan telah tuntas dan dokumen form telah dikunci secara permanen.
                    </p>
                    <div class="pt-3 border-t border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-[11px] font-mono text-slate-600">
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs">
                            <span class="text-slate-400 block text-[10px] font-sans font-bold">No. Surat Jalan / BAST:</span>
                            <strong class="text-slate-900 text-xs mt-0.5 block truncate" title="<?= esc($dispBastNumber) ?>"><?= esc($dispBastNumber) ?></strong>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs">
                            <span class="text-slate-400 block text-[10px] font-sans font-bold">No. Faktur Tagihan (Invoice):</span>
                            <strong class="text-slate-900 text-xs mt-0.5 block truncate" title="<?= esc($dispVendorInvoice) ?>"><?= esc($dispVendorInvoice) ?></strong>
                        </div>
                        <div class="bg-white p-2.5 rounded-xl border border-slate-200 shadow-2xs">
                            <span class="text-slate-400 block text-[10px] font-sans font-bold">No. e-Faktur Pajak PPN:</span>
                            <strong class="text-slate-900 text-xs mt-0.5 block truncate" title="<?= esc($dispTaxInvoice) ?>"><?= esc($dispTaxInvoice) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <?php
                $isExpedition = (($order['delivery_method'] ?? '') === 'expedition') || !empty($order['tracking_number']);
                $token = $order['delivery_token'] ?? '';
                $driverLink = !empty($token) ? base_url('delivery/' . $token) : '';
                $cleanDriverPhone = preg_replace('/[^0-9]/', '', (string)($order['driver_phone'] ?? ''));
                $waDriverPhone = str_starts_with($cleanDriverPhone, '0') ? ('62' . substr($cleanDriverPhone, 1)) : $cleanDriverPhone;
                $waDriverMsg = "Halo " . ($order['driver_name'] ?: 'Driver') . ", berikut link update pengiriman untuk PO *" . ($order['po_number'] ?? $order['order_number']) . "* tujuan *" . ($order['buyer_name'] ?? '') . "*:\n\n" . $driverLink . "\n\nSilakan klik link di atas saat berangkat (OTW), saat tiba di lokasi, dan upload foto serah terima barang.";
            ?>
            <?php if ($isExpedition): ?>
                <!-- Card Khusus: Pengiriman Ekspedisi / Kargo (Resi) -->
                <div class="bg-gradient-to-br from-indigo-950 via-slate-900 to-blue-950 text-white p-5 rounded-2xl shadow-sm border border-indigo-800/80 space-y-4">
                    <div class="flex items-center justify-between border-b border-white/10 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-amber-400 text-xl">local_shipping</span>
                            <h3 class="font-extrabold text-xs text-white uppercase tracking-wider">Pengiriman Ekspedisi / Logistik Kargo</h3>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-400/20 text-amber-300 font-bold text-[10px] border border-amber-400/30">
                            3PL / Ekspedisi
                        </span>
                    </div>

                    <p class="text-xs text-slate-300 leading-relaxed">
                        Pesanan ini dikirimkan melalui jasa ekspedisi/kargo eksternal. Anda dan pihak Buyer dapat memantau perjalanan paket menggunakan nomor resi di bawah.
                    </p>

                    <div class="p-3.5 rounded-xl bg-white/5 border border-white/10 space-y-3 text-xs">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 block">Nama Ekspedisi:</span>
                                <strong class="text-white text-sm font-bold block mt-0.5"><?= esc($order['courier_name'] ?: 'Ekspedisi') ?></strong>
                                <?php if (!empty($order['expedition_service'])): ?>
                                    <span class="text-[10px] text-indigo-300 block font-semibold"><?= esc($order['expedition_service']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block">Nomor Resi / AWB:</span>
                                <strong class="font-mono text-amber-300 text-sm tracking-wider font-extrabold block mt-0.5"><?= esc($order['tracking_number'] ?: 'Belum diisi resi') ?></strong>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block">Tanggal Pengiriman:</span>
                                <span class="text-white font-medium text-[11px] block mt-0.5"><?= !empty($order['shipped_at']) ? (date('d M Y, H:i', strtotime($order['shipped_at'])) . ' WIB') : '-' ?></span>
                            </div>
                        </div>

                        <?php if (!empty($order['tracking_number'])): ?>
                            <div class="pt-2.5 border-t border-white/10 flex flex-wrap items-center gap-2">
                                <a href="<?= get_expedition_tracking_url($order['courier_name'] ?? '', $order['tracking_number'] ?? '') ?>" target="_blank" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs flex items-center gap-1.5 transition shadow-sm">
                                    <span class="material-symbols-outlined text-[16px]">travel_explore</span>
                                    <span>Lacak Status Resi di CekResi ↗</span>
                                </a>
                                <button type="button" onclick="navigator.clipboard.writeText('<?= esc($order['tracking_number']) ?>'); alert('Nomor Resi <?= esc($order['tracking_number']) ?> berhasil disalin ke clipboard!');" class="px-3.5 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center gap-1.5 border border-white/20 transition">
                                    <span class="material-symbols-outlined text-[16px]">content_copy</span>
                                    <span>Salin Resi</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Card Khusus: Pengantaran Langsung Armada Vendor / HP Driver -->
                <div class="bg-gradient-to-br from-slate-900 to-blue-950 text-white p-5 rounded-2xl shadow-sm border border-blue-900 space-y-4">
                    <div class="flex items-center justify-between border-b border-white/10 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-400">two_wheeler</span>
                            <h3 class="font-extrabold text-xs text-white uppercase tracking-wider">Akses HP Kurir / Driver Armada Vendor</h3>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-bold text-[10px] border border-emerald-500/30">
                            Akses Langsung HP
                        </span>
                    </div>

                    <p class="text-xs text-blue-200 leading-relaxed">
                        Khusus pengantaran sendiri oleh armada vendor, bagikan tautan ini ke kurir/driver. Driver cukup membuka tautan dari HP sekali saja saat tiba di lokasi untuk memotret bukti serah terima (POD) & mengisi nama penerima tanpa perlu login.
                    </p>

                    <div class="p-3 rounded-xl bg-white/5 border border-white/10 grid grid-cols-2 gap-2 text-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Nama Driver:</span>
                            <strong class="text-white"><?= esc($order['driver_name'] ?: 'Belum ditentukan') ?></strong>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block">No. Plat Kendaraan:</span>
                            <strong class="font-mono text-white"><?= esc($order['vehicle_plate'] ?: '-') ?></strong>
                        </div>
                        <div class="col-span-2">
                            <span class="text-[10px] text-slate-400 block">No. WhatsApp Driver:</span>
                            <span class="font-mono text-blue-300"><?= esc($order['driver_phone'] ?: '-') ?></span>
                        </div>
                        <?php if (!empty($order['delivery_proof_photo'])): ?>
                            <div class="col-span-2 pt-2 border-t border-white/10 flex items-center justify-between">
                                <span class="text-emerald-400 font-bold text-[11px] flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[15px]">photo_camera</span>
                                    Foto Bukti Serah Terima Tersedia
                                </span>
                                <a href="<?= base_url($order['delivery_proof_photo']) ?>" target="_blank" class="px-2.5 py-1 rounded-lg bg-emerald-500 text-white font-bold text-[10px] hover:bg-emerald-600 transition">
                                    Lihat Bukti Foto
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <a href="<?= $driverLink ?>" target="_blank" class="px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition">
                            <span class="material-symbols-outlined text-[16px]">smartphone</span>
                            <span>Buka di HP Kurir</span>
                        </a>
                        <button type="button" onclick="navigator.clipboard.writeText('<?= $driverLink ?>'); alert('Tautan HP Kurir berhasil disalin!');" class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs flex items-center justify-center gap-1.5 border border-white/20 transition">
                            <span class="material-symbols-outlined text-[16px]">content_copy</span>
                            <span>Salin Link HP</span>
                        </button>
                        <?php if (!empty($waDriverPhone)): ?>
                            <a href="https://api.whatsapp.com/send?phone=<?= $waDriverPhone ?>&text=<?= urlencode($waDriverMsg) ?>" target="_blank" class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition">
                                <span class="material-symbols-outlined text-[16px]">chat</span>
                                <span>Kirim ke WA Driver</span>
                            </a>
                        <?php else: ?>
                            <a href="https://api.whatsapp.com/send?text=<?= urlencode($waDriverMsg) ?>" target="_blank" class="px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-1.5 transition">
                                <span class="material-symbols-outlined text-[16px]">share</span>
                                <span>Bagikan ke WA</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Form Update Shipping (Aktif Selama Belum Selesai oleh Buyer) -->
            <div class="bg-white p-6 rounded-2xl border border-blue-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
                    <span class="material-symbols-outlined text-blue-600">local_shipping</span>
                    Update Tahapan Produksi, Pengemasan & Pengiriman
                </h3>

                <!-- Info Banner mengenai Alur Pengiriman & Buyer -->
                <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-[11px] leading-relaxed">
                    <strong>Alur Pembaruan Vendor:</strong> Vendor bertugas memperbarui tahapan penyiapan, pengemasan, pengiriman, hingga <strong>Sampai di Tujuan (Lokasi Buyer)</strong>. Tahap penutupan transaksi (<strong>Pesanan Selesai & Pengesahan BAST</strong>) dikonfirmasi langsung oleh pihak <strong>Corporate Buyer</strong>.
                </div>

                <form action="<?= base_url('vendor/orders/update-shipping') ?>" method="POST" class="space-y-4"><?= csrf_field() ?>
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Tahapan Pemrosesan oleh Vendor:</label>
                        <select name="order_status" class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-blue-600 bg-white font-semibold">
                            <option value="po_approved" <?= $order['order_status'] === 'po_approved' ? 'selected' : '' ?>>1. PO Dikonfirmasi Vendor</option>
                            <option value="in_production" <?= in_array($order['order_status'], ['in_production', 'processing']) ? 'selected' : '' ?>>2. Proses Produksi / Penyiapan Barang</option>
                            <option value="quality_control" <?= $order['order_status'] === 'quality_control' ? 'selected' : '' ?>>3. Pemeriksaan Mutu & Standar (QC)</option>
                            <option value="packaging" <?= $order['order_status'] === 'packaging' ? 'selected' : '' ?>>4. Pengemasan & Paletisasi Master Box</option>
                            <option value="shipped" <?= $order['order_status'] === 'shipped' ? 'selected' : '' ?>>5. Dalam Pengiriman (Surat Jalan Diterbitkan)</option>
                            <option value="delivered" <?= in_array($order['order_status'], ['delivered', 'bast_signed', 'completed']) ? 'selected' : '' ?>>6. Sampai di Tujuan (Barang Tiba di Lokasi Buyer)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor Surat Jalan / BAST:</label>
                        <input type="text" name="bast_number" value="<?= esc($dispBastNumber) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor Faktur Tagihan Vendor (Invoice Penagihan):</label>
                        <input type="text" name="vendor_invoice_number" value="<?= esc($dispVendorInvoice) ?>" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor Seri e-Faktur Pajak PPN <?= esc(format_tax_rate()) ?>:</label>
                        <input type="text" name="tax_invoice_number" value="<?= esc($dispTaxInvoice) ?>" placeholder="010.002-26.xxxxxxxx" class="w-full px-3 py-2 rounded-xl border border-slate-300 font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-emerald-800 mb-1">Biaya Logistik / Pengiriman (Rp):</label>
                        <input type="number" name="shipping_amount" value="<?= (float)($order['shipping_amount'] ?? 0) ?>" min="0" step="1000" class="w-full px-3 py-2 rounded-xl border border-emerald-300 bg-emerald-50/40 font-mono font-bold text-emerald-900">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Catatan Pengiriman / Ekspedisi / Armada:</label>
                        <textarea name="status_notes" rows="2" placeholder="Contoh: Barang dikirim via armada logistik internal / ekspedisi, no resi: JNE-1234567, estimasi tiba pukul 14:00 WIB." class="w-full px-3 py-2 rounded-xl border border-slate-300"></textarea>
                    </div>

                    <!-- Pilihan Metode Pengiriman: Armada Sendiri vs Ekspedisi -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3.5">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                            <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px] text-blue-600">local_shipping</span>
                                <span>Metode & Rincian Pengiriman Barang:</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-blue-300 transition font-bold" id="label_method_fleet">
                                <input type="radio" name="delivery_method" value="vendor_fleet" <?= ($order['delivery_method'] ?? 'vendor_fleet') !== 'expedition' ? 'checked' : '' ?> onchange="toggleDeliveryMethodVendor(this.value)" class="text-blue-600">
                                <span>Armada Vendor Sendiri</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-white cursor-pointer hover:border-blue-300 transition font-bold" id="label_method_expedition">
                                <input type="radio" name="delivery_method" value="expedition" <?= ($order['delivery_method'] ?? '') === 'expedition' ? 'checked' : '' ?> onchange="toggleDeliveryMethodVendor(this.value)" class="text-blue-600">
                                <span>Ekspedisi / Kargo (Resi)</span>
                            </label>
                        </div>

                        <!-- Opsi 1: Driver Armada Sendiri -->
                        <div id="section_vendor_fleet" class="<?= ($order['delivery_method'] ?? 'vendor_fleet') === 'expedition' ? 'hidden' : '' ?> space-y-2">
                            <span class="text-[11px] font-bold text-slate-700 block">Informasi Driver Vendor:</span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Nama Driver:</label>
                                    <input type="text" name="driver_name" value="<?= esc($order['driver_name'] ?? '') ?>" placeholder="Nama pengemudi" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">No. WA Driver:</label>
                                    <input type="tel" name="driver_phone" value="<?= esc($order['driver_phone'] ?? '') ?>" placeholder="08123456789" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white font-mono">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">No. Plat Kendaraan:</label>
                                    <input type="text" name="vehicle_plate" value="<?= esc($order['vehicle_plate'] ?? '') ?>" placeholder="B 1234 ABC" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white font-mono uppercase">
                                </div>
                            </div>
                        </div>

                        <!-- Opsi 2: Ekspedisi / Kargo (Resi) -->
                        <div id="section_expedition" class="<?= ($order['delivery_method'] ?? 'vendor_fleet') !== 'expedition' ? 'hidden' : '' ?> space-y-2.5">
                            <span class="text-[11px] font-bold text-blue-900 block">Informasi Ekspedisi / Kargo Logistik:</span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 mb-0.5">Nama Ekspedisi / Kurir: <span class="text-rose-500">*</span></label>
                                    <input type="text" list="expedition_options" name="courier_name" value="<?= esc($order['courier_name'] ?? '') ?>" placeholder="JNE / J&T / SiCepat / dll." class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white font-semibold">
                                    <datalist id="expedition_options">
                                        <?php foreach (get_popular_expeditions() as $cCode => $cLabel): ?>
                                            <option value="<?= esc($cCode) ?>"><?= esc($cLabel) ?></option>
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-blue-800 mb-0.5">Nomor Resi / AWB: <span class="text-rose-500">*</span></label>
                                    <input type="text" name="tracking_number" value="<?= esc($order['tracking_number'] ?? '') ?>" placeholder="Nomor Resi Pengiriman" class="w-full px-2.5 py-1.5 rounded-lg border border-blue-400 bg-blue-50/40 font-mono font-bold text-blue-900 uppercase">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-600 mb-0.5">Jenis Layanan (Opsional):</label>
                                    <input type="text" name="expedition_service" value="<?= esc($order['expedition_service'] ?? '') ?>" placeholder="Reguler / Kargo Trucking / ONS" class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white">
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-500 block">
                                Setelah nomor resi disimpan, Buyer dan Admin dapat langsung melacak posisi barang secara langsung.
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">save</span>
                        <span>Simpan Pembaruan Pengiriman</span>
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- 5. KLAIM RETUR / BARANG RUSAK DARI BUYER (JIKA ADA) -->
    <?php if (!empty($latestReturn)): ?>
        <div class="p-6 rounded-3xl bg-rose-50 border-2 border-rose-300 shadow-md space-y-4 text-xs">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-rose-200">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center shadow-md">
                        <span class="material-symbols-outlined text-[22px]">assignment_return</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-rose-950">PERMOHONAN KLAIM RETUR / BARANG RUSAK DARI BUYER</h3>
                        <p class="text-[11px] text-rose-700 font-mono">No. Pengajuan: <strong><?= esc($latestReturn['return_number']) ?></strong> | Diajukan oleh: <strong><?= esc($latestReturn['requester_name']) ?></strong></p>
                    </div>
                </div>
                <div>
                    <?php if ($latestReturn['status'] === 'pending'): ?>
                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 font-black text-[10px] animate-pulse">
                            MEMBUTUHKAN RESPON VENDOR
                        </span>
                    <?php elseif ($latestReturn['status'] === 'approved'): ?>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold text-[10px]">
                            Retur Disetujui (Ganti Barang)
                        </span>
                    <?php elseif ($latestReturn['status'] === 'rejected'): ?>
                        <span class="px-3 py-1 rounded-full bg-rose-200 text-rose-900 border border-rose-300 font-bold text-[10px]">
                            Klaim Retur Ditolak
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-900 border border-purple-300 font-bold text-[10px]">
                            Retur Selesai (Credit Note / Refund)
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detail Kerusakan -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <p class="text-slate-600">Kategori Masalah: <strong class="text-slate-900"><?= esc($latestReturn['reason_category']) ?></strong></p>
                    <p class="text-slate-600">Solusi yang Diminta Buyer: <strong class="text-purple-900"><?= $latestReturn['resolution_requested'] === 'replacement' ? 'Kirim Barang Pengganti Baru' : 'Potong Tagihan TOP (Credit Note)' ?></strong></p>
                    <?php if (!empty($latestReturn['item_description'])): ?>
                        <p class="text-slate-600">Item Bermasalah: <strong class="text-slate-900"><?= esc($latestReturn['item_description']) ?></strong></p>
                    <?php endif; ?>
                    <p class="text-slate-600">Kronologi / Penjelasan: <span class="italic text-slate-800 block mt-0.5 bg-white p-3 rounded-xl border border-rose-200">"<?= esc($latestReturn['damage_description']) ?>"</span></p>
                </div>

                <div class="space-y-2">
                    <?php if (!empty($latestReturn['evidence_photo'])): ?>
                        <div>
                            <span class="text-slate-600 font-semibold block text-[11px] mb-1">Bukti Foto Fisik Kerusakan dari Buyer:</span>
                            <a href="<?= esc($latestReturn['evidence_photo']) ?>" target="_blank" class="inline-block rounded-xl overflow-hidden border border-rose-300 shadow-sm max-w-xs hover:opacity-90">
                                <img src="<?= esc($latestReturn['evidence_photo']) ?>" alt="Bukti Foto Kerusakan" class="h-32 object-cover">
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($latestReturn['status'] !== 'pending'): ?>
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 space-y-1">
                            <strong class="text-slate-900 block font-bold">Respon Keputusan Vendor:</strong>
                            <p class="text-slate-700 italic">"<?= esc($latestReturn['vendor_response']) ?>"</p>
                            <?php if (!empty($latestReturn['replacement_tracking'])): ?>
                                <span class="text-purple-700 font-mono font-bold block pt-1">No. Resi Pengganti: <?= esc($latestReturn['replacement_tracking']) ?></span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Keputusan Vendor jika masih pending -->
            <?php if ($latestReturn['status'] === 'pending'): ?>
                <form action="<?= base_url('vendor/orders/return-respond') ?>" method="POST" class="pt-3 border-t border-rose-200 space-y-3"><?= csrf_field() ?>
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="return_id" value="<?= $latestReturn['id'] ?>">

                    <div>
                        <label class="block font-bold text-slate-800 text-xs mb-1">Pilih Keputusan Vendor Terhadap Klaim Ini:</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                            <label class="p-3 rounded-xl border border-emerald-300 bg-white cursor-pointer flex items-center gap-2 font-bold text-emerald-900 text-xs shadow-sm hover:border-emerald-500">
                                <input type="radio" name="action" value="approve_replacement" checked class="text-emerald-600">
                                <span>1. Ganti Barang Baru</span>
                            </label>
                            <label class="p-3 rounded-xl border border-purple-300 bg-white cursor-pointer flex items-center gap-2 font-bold text-purple-900 text-xs shadow-sm hover:border-purple-500">
                                <input type="radio" name="action" value="approve_refund" class="text-purple-600">
                                <span>2. Potong Tagihan (Refund)</span>
                            </label>
                            <label class="p-3 rounded-xl border border-rose-300 bg-white cursor-pointer flex items-center gap-2 font-bold text-rose-900 text-xs shadow-sm hover:border-rose-500">
                                <input type="radio" name="action" value="reject" class="text-rose-600">
                                <span>3. Tolak Klaim Retur</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-800 text-xs mb-1">Nomor Resi / Surat Jalan Barang Pengganti (Jika Mengirim Pengganti):</label>
                        <input type="text" name="replacement_tracking" placeholder="Contoh: RESI-GANTI/2026/001" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 font-mono text-xs">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-800 text-xs mb-1">Catatan Tanggapan / Keterangan Resmi Vendor:</label>
                        <textarea name="vendor_response" rows="2" placeholder="Tuliskan respon atau penjelasan resmi untuk pihak buyer..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs"></textarea>
                    </div>

                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-800 hover:to-indigo-800 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-md">
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                        <span>Kirim Tanggapan Resmi</span>
                    </button>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- 6. REVIEW CARD DARI BUYER (JIKA ADA) -->
    <?php if (!empty($review)): ?>
        <div class="bg-white rounded-2xl border border-amber-200/90 shadow-sm p-6 space-y-4 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-amber-100">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">star</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Penilaian & Ulasan dari Pembeli (Buyer)</h3>
                        <p class="text-[11px] text-slate-500">Diberikan oleh <strong><?= esc($review['buyer_name']) ?></strong> (<?= esc($order['buyer_name']) ?>)</p>
                    </div>
                </div>
                <span class="text-[11px] text-slate-400 font-mono"><?= date('d M Y, H:i', strtotime($review['created_at'])) . ' WIB' ?></span>
            </div>

            <div class="p-4 rounded-xl bg-amber-50/50 border border-amber-200/80 space-y-3">
                <div class="flex items-center gap-3">
                    <div class="flex items-center text-amber-400">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="material-symbols-outlined text-[24px] <?= $i <= (int)$review['rating'] ? 'text-amber-400' : 'text-slate-300' ?>">star</span>
                        <?php endfor; ?>
                    </div>
                    <strong class="text-sm font-black text-slate-900"><?= (float)$review['rating'] ?>.0 / 5.0</strong>
                </div>

                <div class="flex flex-wrap items-center gap-2 text-[11px]">
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">Kualitas: <strong><?= (int)($review['rating_quality'] ?? 5) ?>★</strong></span>
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">Pengiriman: <strong><?= (int)($review['rating_delivery'] ?? 5) ?>★</strong></span>
                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700">Pelayanan: <strong><?= (int)($review['rating_service'] ?? 5) ?>★</strong></span>
                </div>

                <?php if (!empty($review['review_title'])): ?>
                    <h4 class="font-bold text-slate-900"><?= esc($review['review_title']) ?></h4>
                <?php endif; ?>
                <p class="text-slate-700 italic bg-white p-3 rounded-lg border border-slate-200">
                    "<?= esc($review['review_text']) ?>"
                </p>

                <!-- Tanggapan Vendor -->
                <?php if (!empty($review['vendor_reply'])): ?>
                    <div class="p-3.5 rounded-xl bg-blue-50 border border-blue-200 space-y-1">
                        <div class="flex items-center justify-between">
                            <strong class="text-blue-900 font-bold">Balasan Anda:</strong>
                            <span class="text-[10px] text-blue-600 font-mono"><?= date('d M Y', strtotime($review['vendor_replied_at'])) ?></span>
                        </div>
                        <p class="text-slate-700"><?= esc($review['vendor_reply']) ?></p>
                    </div>
                <?php else: ?>
                    <form action="<?= base_url('vendor/orders/review-reply') ?>" method="POST" class="space-y-3 pt-2"><?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <label class="block font-bold text-slate-800">Tanggapi Ulasan Pembeli:</label>
                        <textarea name="vendor_reply" rows="2" required placeholder="Tuliskan apresiasi terima kasih atau konfirmasi atas ulasan pembeli..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs"></textarea>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm">
                            <span class="material-symbols-outlined text-[16px]">reply</span>
                            <span>Kirim Balasan Ulasan</span>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- 7. KETENTUAN PEMBAYARAN & STATUS HAK BERSIH VENDOR (SETTLEMENT AKUADA) -->
    <div class="bg-gradient-to-r from-blue-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-7 text-white shadow-xl space-y-5">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-4 border-b border-white/10">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <?php if ($canRequest): ?>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500 text-white text-[10px] font-black uppercase tracking-wider">Settlement & Pembayaran</span>
                    <?php else: ?>
                        <span class="px-2.5 py-0.5 rounded-full bg-purple-500 text-white text-[10px] font-black uppercase tracking-wider">Sistem Tempo (<?= $isTop ? 'TOP ' . $topDays . ' Hari' : 'Kredit AkuAda' ?>)</span>
                    <?php endif; ?>
                    <span class="text-xs text-blue-200">Faktur Tagihan: <strong class="font-mono text-white"><?= esc($dispVendorInvoice) ?></strong></span>
                </div>
                <h3 class="text-lg font-black tracking-tight text-white">
                    <?= $canRequest ? 'Hak Bersih Penjualan & Pencairan Dana dari AkuAda' : 'Garansi Pembayaran & Pencairan Hak Bersih Vendor dari AkuAda' ?>
                </h3>
                <p class="text-xs text-blue-200 leading-relaxed max-w-2xl">
                    <?php if ($canRequest): ?>
                        Barang telah diterima oleh pihak pembeli dan BAST telah disahkan. Hak bersih hasil penjualan Anda langsung dicairkan ke rekening bank resmi perusahaan vendor.
                    <?php else: ?>
                        Pembeli resmi menggunakan fasilitas kredit tempo (TOP <?= $topDays ?> Hari) dengan sistem DP 0%. Anda tidak perlu khawatir: hak bersih pembayaran Anda sebesar <strong class="text-white"><?= format_rupiah($totalReceive) ?></strong> telah digaransi 100% aman oleh platform AkuAda dan akan dicairkan segera setelah BAST disahkan.
                    <?php endif; ?>
                </p>
            </div>

            <!-- Total Hak Bersih Box -->
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-4 border border-white/20 text-left md:text-right min-w-[240px]">
                <span class="text-[10px] uppercase font-bold text-blue-200 tracking-wider block">
                    <?= $canRequest ? 'Dana Bersih yang Diterima Vendor' : 'Estimasi Hak Bersih yang Akan Diterima' ?>
                </span>
                <span class="text-2xl font-black font-mono text-emerald-300 block mt-0.5"><?= format_rupiah($totalReceive) ?></span>
                <span class="text-[10px] text-blue-200 block mt-0.5">(DPP: <?= format_rupiah($order['subtotal']) ?> - Fee Layanan: <?= format_rupiah($fee) ?>)</span>
            </div>
        </div>

        <!-- Rincian Rekening & Status Payout -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2">
                <span class="text-[10px] uppercase font-bold text-blue-200 block tracking-wider">Rekening Tujuan Pencairan:</span>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-300">account_balance</span>
                    <strong class="text-white text-sm"><?= esc($company['bank_name'] ?? 'Bank BCA Corporate') ?></strong>
                </div>
                <p class="text-blue-100 font-mono text-base font-bold text-yellow-300"><?= esc($company['bank_account_number'] ?? '-') ?></p>
                <p class="text-blue-200 text-[11px]">A/N: <?= esc($company['bank_account_holder'] ?? ($company['company_name'] ?? 'Mitra Vendor')) ?></p>
            </div>

            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 flex flex-col justify-between space-y-2">
                <div>
                    <span class="text-[10px] uppercase font-bold text-blue-200 block tracking-wider">Status Pembayaran dari AkuAda:</span>
                    <div class="mt-2">
                        <?php if (!$canRequest): ?>
                            <!-- Order masih tahap produksi/pengiriman -->
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-700/80 text-blue-200 font-bold text-xs border border-white/20">
                                <span class="material-symbols-outlined text-[16px]">lock_clock</span>
                                <span>MENUNGGU PENGIRIMAN & PENGESAHAN BAST</span>
                            </span>
                            <p class="text-[11px] text-blue-200 mt-2 leading-relaxed">
                                Pesanan masih dalam proses pemenuhan barang. Sesuai ketentuan TOP <?= $topDays ?> Hari (DP 0%), pencairan dana sebesar <strong class="text-white"><?= format_rupiah($totalReceive) ?></strong> akan otomatis dibuka segera setelah barang tiba di lokasi pembeli & BAST fisik/digital disahkan.
                            </p>
                        <?php elseif ($vPayoutStatus === 'paid'): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500 text-white font-black text-xs shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                                <span>LUNAS DICAIRKAN</span>
                            </span>
                            <p class="text-[11px] text-emerald-200 mt-2">
                                Dana telah ditransfer oleh AkuAda pada <?= !empty($order['vendor_paid_at']) ? date('d M Y, H:i', strtotime($order['vendor_paid_at'])) : '-' ?> WIB (No. Ref: <span class="font-mono"><?= esc($order['vendor_payout_ref'] ?? '-') ?></span>).
                            </p>
                        <?php elseif ($vPayoutStatus === 'pending_payout'): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400 text-slate-950 font-black text-xs animate-pulse shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">hourglass_top</span>
                                <span>PENGAJUAN SEDANG DIPROSES</span>
                            </span>
                            <p class="text-[11px] text-amber-200 mt-2">
                                Permohonan pencairan dana telah diterima. Tim Finance AkuAda sedang memproses transfer ke rekening bank Anda.
                            </p>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/80 text-white font-bold text-xs">
                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                <span>SIAP DICAIRKAN</span>
                            </span>
                            <p class="text-[11px] text-blue-200 mt-2">
                                Barang telah diterima pembeli & BAST telah disahkan. Anda dapat langsung mengajukan pencairan dana ke rekening Anda di bawah.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($canRequest && $vPayoutStatus !== 'paid' && $vPayoutStatus !== 'pending_payout'): ?>
                    <form action="<?= base_url('vendor/orders/request-payout') ?>" method="POST" data-confirm="Ajukan pencairan dana sebesar <?= format_rupiah($totalReceive) ?> ke rekening bank perusahaan Anda?"><?= csrf_field() ?>
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs transition flex items-center justify-center gap-1.5 shadow-md shadow-emerald-500/30">
                            <span class="material-symbols-outlined text-[18px]">payments</span>
                            <span>⚡ Ajukan Pencairan Dana ke Rekening Saya</span>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 8. RIWAYAT AKTIVITAS & PELACAKAN PO -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 text-xs">
        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2 pb-3 border-b border-slate-100">
            <span class="material-symbols-outlined text-blue-600">history</span>
            Riwayat Aktivitas & Pelacakan PO
        </h3>
        <div class="relative pl-6 space-y-3 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-blue-300">
            <?php if (empty($order['status_logs'])): ?>
                <p class="text-slate-400 italic">Belum ada catatan aktivitas.</p>
            <?php else: ?>
                <?php foreach ($order['status_logs'] as $log): ?>
                    <div class="relative flex items-start gap-3">
                        <div class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-white border-2 border-blue-600 flex items-center justify-center">
                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                        </div>
                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200 w-full space-y-1">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <strong class="font-bold text-slate-900"><?= esc($log['title']) ?></strong>
                                <span class="text-[10px] text-slate-400 font-mono"><?= date('d M Y, H:i', strtotime($log['created_at'])) . ' WIB' ?></span>
                            </div>
                            <?php if (!empty($log['description'])): ?>
                                <p class="text-slate-600 text-[11px]"><?= esc($log['description']) ?></p>
                            <?php endif; ?>
                            <span class="text-[10px] text-blue-700 font-semibold block">Oleh: <?= esc($log['updated_by']) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- 9. SALURAN KOORDINASI & LIVE CHAT DENGAN TIM AKUADA -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-blue-50 via-indigo-50 to-white border border-blue-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm text-xs">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <span class="material-symbols-outlined text-[22px]">forum</span>
            </div>
            <div>
                <strong class="text-slate-900 font-bold text-xs block">Saluran Koordinasi & Live Chat dengan Tim AkuAda</strong>
                <p class="text-slate-500 text-[11px] mt-0.5">
                    Hubungi Tim AkuAda untuk koordinasi penyiapan pesanan, informasi serah terima barang (BAST), atau konfirmasi penagihan invoice vendor.
                </p>
            </div>
        </div>
        <a href="<?= base_url('vendor/chat?key=admin_comp_' . $order['vendor_company_id']) ?>" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition flex items-center gap-2 shadow-md shadow-blue-600/20 flex-shrink-0">
            <span class="material-symbols-outlined text-[18px]">chat</span>
            <span>Buka Percakapan dengan AkuAda ↗</span>
        </a>
    </div>
</div>

<script>
    function toggleDeliveryMethodVendor(method) {
        const fleetSec = document.getElementById('section_vendor_fleet');
        const expSec = document.getElementById('section_expedition');
        if (fleetSec && expSec) {
            if (method === 'expedition') {
                fleetSec.classList.add('hidden');
                expSec.classList.remove('hidden');
            } else {
                fleetSec.classList.remove('hidden');
                expSec.classList.add('hidden');
            }
        }
    }
</script>

<?= $this->endSection() ?>
