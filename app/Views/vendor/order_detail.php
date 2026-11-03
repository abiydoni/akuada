<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

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

    <!-- Stepper -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">route</span>
                <h3 class="text-sm font-bold text-slate-900">Progres Pemenuhan Pengadaan (8 Tahap Standar B2B)</h3>
            </div>
        </div>
        <?= render_order_progress_stepper($order['order_status']) ?>
    </div>

    <!-- Klaim Retur / Barang Rusak dari Buyer (Jika Ada) -->
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

    <!-- Buyer Info & Payout Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-2">
            <span class="text-[10px] font-extrabold text-blue-700 uppercase tracking-widest block">TUJUAN PENGIRIMAN (CORPORATE BUYER):</span>
            <strong class="text-base text-slate-900 block"><?= esc($order['buyer_name']) ?></strong>
            <p class="text-slate-600">NPWP Buyer: <strong class="font-mono"><?= esc($order['buyer_npwp'] ?? '-') ?></strong></p>
            <p class="text-slate-600">Alamat Kirim: <strong><?= esc($order['shipping_address'] ?? $order['buyer_address']) ?></strong></p>
            <p class="text-slate-600">PIC Penerima: <?= esc($order['buyer_pic'] ?? '-') ?> (<?= esc($order['buyer_phone'] ?? '-') ?>)</p>
        </div>

        <div class="bg-gradient-to-br from-emerald-50/70 to-blue-50/60 p-5 rounded-2xl border border-emerald-200 shadow-sm space-y-2">
            <span class="text-[10px] font-extrabold text-emerald-900 uppercase tracking-widest block">RINCIAN SETTLEMENT HAK VENDOR:</span>
            <div class="space-y-1.5 pt-1">
                <div class="flex justify-between">
                    <span class="text-slate-600">Total Penjualan Barang (DPP):</span>
                    <strong class="font-mono text-slate-900"><?= format_rupiah($order['subtotal']) ?></strong>
                </div>
                <div class="flex justify-between text-purple-800">
                    <span>Potongan Fee Platform AkuAda:</span>
                    <strong class="font-mono">- <?= format_rupiah($order['total_vendor_fee'] ?? 0) ?></strong>
                </div>
                <div class="flex justify-between pt-2 border-t border-emerald-200">
                    <span class="font-bold text-emerald-950">Hak Bersih Diterima Vendor (Net Payout):</span>
                    <strong class="text-base font-black font-mono text-emerald-800"><?= format_rupiah($order['vendor_net_payout'] ?? ($order['subtotal'] - ($order['total_vendor_fee'] ?? 0))) ?></strong>
                </div>
                <div class="text-[11px] text-slate-500 pt-1">
                    PPN <?= esc(format_tax_rate()) ?> (<?= format_rupiah($order['ppn_amount']) ?>) dikelola sesuai e-Faktur Pajak B2B.
                </div>
            </div>
        </div>
    </div>

    <!-- Review Card dari Buyer jika ada -->
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
                    <!-- Form Balas Ulasan -->
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

    <!-- Order Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
            <span class="material-symbols-outlined text-blue-600">inventory_2</span>
            Daftar Item Barang yang Harus Dikirim
        </h3>
        <div class="w-full">
            <table class="w-full text-xs text-left border-collapse">
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
    </div>

    <!-- Update Shipping & Activity Logs -->
    <div class="space-y-6 text-xs">
        <div class="space-y-6">
            
            <!-- Update Form OR Locked Card -->
            <?php 
                $seqId = str_pad((string)$order['id'], 4, '0', STR_PAD_LEFT);
                $orderYm = !empty($order['created_at']) ? date('Ym', strtotime($order['created_at'])) : date('Ym');
                $dispBastNumber = !empty($order['bast_number']) ? $order['bast_number'] : ('BAST/AKUADA/' . $orderYm . '/' . $seqId);
                $dispVendorInvoice = !empty($order['vendor_invoice_number']) ? $order['vendor_invoice_number'] : (!empty($order['invoice_number']) ? $order['invoice_number'] : ('VINV/' . $orderYm . '/' . $seqId));
                $dispTaxInvoice = !empty($order['tax_invoice_number']) ? $order['tax_invoice_number'] : ('010.002-26.' . sprintf('%08d', abs(crc32('order_' . $order['id']))));
            ?>
            <?php if (in_array($order['order_status'], ['completed', 'bast_signed'])): ?>
                <!-- STATUS TERKUNCI PERMANEN: Pesanan Sudah Selesai oleh Buyer -->
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

                        <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">save</span>
                            <span>Simpan Pembaruan Pengiriman</span>
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Historical Activity Logs -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
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
        </div>

        <!-- Banner Komunikasi & Chat di luar PO -->
        <div class="p-5 rounded-2xl bg-gradient-to-r from-blue-50 via-indigo-50 to-white border border-blue-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
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
</div>

<?= $this->endSection() ?>
