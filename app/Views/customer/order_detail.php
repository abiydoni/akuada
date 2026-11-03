<?= $this->extend('layouts/customer') ?>

<?= $this->section('customer_content') ?>

<div class="space-y-6">
    <!-- Header with Breadcrumbs & Actions -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('customer/orders') ?>" class="hover:text-pink-600">Riwayat Purchase Order</a>
                <span>/</span>
                <span class="font-mono text-purple-700 font-bold"><?= esc($order['po_number'] ?? $order['order_number']) ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900">Status Pemrosesan & Pelacakan PO</h1>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= base_url('customer/orders/invoice/' . $order['id']) ?>" target="_blank" class="px-3.5 py-2 rounded-xl bg-purple-100 hover:bg-purple-200 text-purple-900 font-extrabold text-xs transition flex items-center gap-1.5 shadow-sm border border-purple-200">
                <span class="material-symbols-outlined text-[18px] text-purple-700">receipt_long</span>
                <span>Faktur / Invoice AkuAda</span>
            </a>
            <a href="<?= base_url('customer/orders/bast/' . $order['id']) ?>" target="_blank" class="px-3.5 py-2 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-900 font-bold text-xs transition flex items-center gap-1.5 shadow-sm border border-blue-200">
                <span class="material-symbols-outlined text-[18px] text-blue-700">contract</span>
                <span>BAST & Surat Jalan</span>
            </a>
            <a href="<?= base_url('customer/orders/tax-invoice/' . $order['id']) ?>" target="_blank" class="px-3.5 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 font-bold text-xs transition flex items-center gap-1.5 shadow-sm border border-emerald-200">
                <span class="material-symbols-outlined text-[18px] text-emerald-700">receipt</span>
                <span>e-Faktur PPN</span>
            </a>
            <a href="<?= base_url('customer/chat?key=admin_comp_' . $order['customer_company_id']) ?>" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-extrabold text-xs transition flex items-center gap-1.5 shadow-md shadow-purple-600/20">
                <span class="material-symbols-outlined text-[18px]">forum</span>
                <span>Chat dengan Tim AkuAda</span>
            </a>
            <button type="button" onclick="window.print()" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                <span class="material-symbols-outlined text-[16px]">print</span>
                <span>Cetak Lembar PO</span>
            </button>

            <!-- Return / Claim Damaged Goods Button -->
            <?php if (in_array($order['order_status'], ['shipped', 'delivered', 'bast_signed', 'completed', 'return_requested'])): ?>
                <button type="button" onclick="openReturnModal()" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold text-xs transition flex items-center gap-1.5 border border-rose-200 shadow-sm">
                    <span class="material-symbols-outlined text-[18px] text-rose-600">assignment_return</span>
                    <span>Klaim Retur / Barang Rusak</span>
                </button>
            <?php endif; ?>

            <!-- Action Status Flow Buttons -->
            <?php if ($order['order_status'] === 'delivered'): ?>
                <button type="button" onclick="openCompleteOrderModal()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-xs transition flex items-center gap-1.5 shadow-md shadow-emerald-600/30">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Selesaikan Pesanan & Sahkan BAST</span>
                </button>
            <?php elseif ($order['order_status'] === 'shipped'): ?>
                <button type="button" onclick="openCompleteOrderModal()" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-extrabold text-xs transition flex items-center gap-1.5 shadow-md shadow-emerald-600/30">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Konfirmasi Selesai & Terima Barang</span>
                </button>
            <?php elseif (in_array($order['order_status'], ['bast_signed', 'completed'])): ?>
                <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-300">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>Pesanan Telah Selesai & BAST Sah</span>
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- 1. Real-Time Order Progress Stepper (Timeline Tahapan Pemesanan) -->
    <div class="bg-white rounded-3xl border border-purple-200 shadow-md p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-purple-600">route</span>
                <h3 class="text-sm font-bold text-slate-900">Status & Tahapan Pemrosesan Pengadaan (Lifecycle Timeline)</h3>
            </div>
            <div>
                <?= badge_order_status($order['order_status']) ?>
            </div>
        </div>

        <!-- Stepper Component -->
        <?= render_order_progress_stepper($order['order_status']) ?>

        <!-- Arrival Notice: Vendor marked delivered, waiting for Buyer to complete -->
        <?php if ($order['order_status'] === 'delivered'): ?>
            <div class="mt-4 p-4 rounded-2xl bg-gradient-to-r from-violet-50 to-purple-50 border-2 border-violet-200 text-violet-950 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-violet-600 text-white flex items-center justify-center flex-shrink-0 shadow-md">
                        <span class="material-symbols-outlined text-[24px]">where_to_vote</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full bg-violet-200 text-violet-900 text-[10px] font-bold uppercase tracking-wider">Update Vendor</span>
                            <h4 class="font-bold text-xs">Barang Pesanan Telah Sampai di Lokasi Tujuan!</h4>
                        </div>
                        <p class="text-[11px] text-violet-800 mt-0.5">
                            Mitra Vendor telah memperbarui status bahwa barang telah tiba di lokasi. Silakan periksa kelengkapan fisik, kemudian selesaikan pesanan.
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button type="button" onclick="openCompleteOrderModal()" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center gap-1 shadow-md shadow-emerald-600/20">
                        <span class="material-symbols-outlined text-[16px]">verified</span>
                        <span>Selesaikan Pesanan & BAST</span>
                    </button>
                    <button type="button" onclick="openReturnModal()" class="px-3 py-2 rounded-xl bg-white hover:bg-rose-50 text-rose-700 font-bold text-xs transition flex items-center gap-1 border border-rose-300">
                        <span class="material-symbols-outlined text-[16px]">assignment_return</span>
                        <span>Klaim Retur</span>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <!-- Historical Activity Log -->
        <?php if (!empty($order['status_logs'])): ?>
            <div class="mt-6 pt-5 border-t border-slate-100 space-y-3">
                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-pink-600 text-[18px]">history</span>
                    Riwayat Jejak Pelacakan Pesanan:
                </span>
                
                <div class="relative pl-6 space-y-4 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gradient-to-b before:from-purple-600 before:via-blue-500 before:to-pink-500">
                    <?php foreach ($order['status_logs'] as $log): ?>
                        <div class="relative flex items-start gap-3 text-xs">
                            <div class="absolute -left-6 top-1 w-5 h-5 rounded-full bg-white border-2 border-purple-600 flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-pink-500"></div>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 w-full space-y-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <strong class="font-bold text-slate-900 text-xs"><?= esc($log['title']) ?></strong>
                                    <span class="text-[10px] text-slate-400 font-mono"><?= date('d M Y, H:i', strtotime($log['created_at'])) . ' WIB' ?></span>
                                </div>
                                <?php if (!empty($log['description'])): ?>
                                    <p class="text-slate-600 text-[11px] leading-relaxed"><?= esc($log['description']) ?></p>
                                <?php endif; ?>
                                <span class="text-[10px] text-purple-700 font-semibold block pt-0.5">Oleh: <?= esc($log['updated_by']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- 2. Klaim Retur / Pengembalian Barang Aktif (Jika Ada) -->
    <?php if (!empty($latestReturn)): ?>
        <div class="p-6 rounded-3xl bg-rose-50 border-2 border-rose-200 shadow-md space-y-3 text-xs">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 pb-3 border-b border-rose-200">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-rose-600 text-[22px]">assignment_return</span>
                    <div>
                        <strong class="text-sm font-black text-rose-950">Status Klaim Pengembalian Barang (Retur)</strong>
                        <span class="font-mono text-[11px] text-rose-700 ml-2">No: <?= esc($latestReturn['return_number']) ?></span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <?php if ($latestReturn['status'] === 'pending'): ?>
                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-900 border border-amber-300 font-bold text-[10px] animate-pulse">
                            Menunggu Respon Vendor
                        </span>
                    <?php elseif ($latestReturn['status'] === 'approved'): ?>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-300 font-bold text-[10px]">
                            Retur Disetujui Vendor
                        </span>
                    <?php elseif ($latestReturn['status'] === 'rejected'): ?>
                        <span class="px-3 py-1 rounded-full bg-rose-200 text-rose-900 border border-rose-300 font-bold text-[10px]">
                            Retur Ditolak
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 rounded-full bg-purple-100 text-purple-900 border border-purple-300 font-bold text-[10px]">
                            Retur Selesai
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                <div class="space-y-1">
                    <p class="text-slate-600">Kategori Masalah: <strong class="text-slate-900"><?= esc($latestReturn['reason_category']) ?></strong></p>
                    <p class="text-slate-600">Solusi yang Diajukan: <strong class="text-purple-900"><?= $latestReturn['resolution_requested'] === 'replacement' ? 'Kirim Pengganti Baru' : 'Potong Tagihan TOP (Credit Note)' ?></strong></p>
                    <p class="text-slate-600">Kronologi / Penjelasan: <span class="italic text-slate-800">"<?= esc($latestReturn['damage_description']) ?>"</span></p>
                </div>

                <div class="space-y-1">
                    <?php if (!empty($latestReturn['vendor_response'])): ?>
                        <div class="p-3 rounded-xl bg-white border border-rose-200 text-[11px] space-y-1">
                            <strong class="text-slate-900 block font-bold">Respon dari Vendor:</strong>
                            <p class="text-slate-700 italic">"<?= esc($latestReturn['vendor_response']) ?>"</p>
                            <?php if (!empty($latestReturn['replacement_tracking'])): ?>
                                <p class="text-purple-700 font-mono font-bold mt-1">Resi / Surat Jalan Pengganti: <?= esc($latestReturn['replacement_tracking']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900">
                            Permohonan ini sedang ditinjau. Anda juga dapat berkoordinasi langsung dengan Tim AkuAda melalui Pusat Chat.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- 3. Action Card Khusus Buyer: Penerimaan Barang & Pengesahan BAST -->
    <?php if ($order['order_status'] === 'shipped'): ?>
        <div class="p-6 rounded-3xl bg-gradient-to-r from-blue-700 via-indigo-700 to-purple-800 text-white shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 border border-white/10">
            <div class="flex items-start sm:items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center flex-shrink-0 text-white shadow-inner">
                    <span class="material-symbols-outlined text-[34px]">local_shipping</span>
                </div>
                <div>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/30 text-emerald-200 text-[11px] font-bold uppercase tracking-wider mb-1.5 border border-emerald-400/30">
                        <span class="material-symbols-outlined text-[14px]">done</span>
                        Barang Sedang Dalam Pengiriman Vendor
                    </div>
                    <h3 class="text-lg font-black tracking-tight">Pesanan Fisik Telah Sampai di Gudang / Kantor Anda?</h3>
                    <p class="text-blue-100 text-xs mt-1 leading-relaxed">
                        Surat Jalan / Resi Vendor: <strong class="text-white font-mono bg-white/10 px-2 py-0.5 rounded"><?= esc($order['bast_number'] ?? 'BAST Resmi') ?></strong>.
                        Pastikan seluruh kardus, segel, dan kuantitas barang sesuai dengan surat pesanan. Jika sudah lengkap, lakukan konfirmasi di bawah ini.
                    </p>
                </div>
            </div>
            <button type="button" onclick="openConfirmDeliveryModal()" class="px-6 py-3.5 rounded-2xl bg-white text-purple-950 hover:bg-emerald-50 font-black text-xs transition shadow-xl hover:shadow-emerald-500/20 flex items-center gap-2 flex-shrink-0">
                <span class="material-symbols-outlined text-[20px] text-emerald-600">where_to_vote</span>
                <span>Konfirmasi Barang Diterima</span>
            </button>
        </div>
    <?php elseif ($order['order_status'] === 'delivered'): ?>
        <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50 to-blue-50 border border-emerald-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-[24px]">where_to_vote</span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <strong class="text-slate-900 font-bold text-xs">Barang Telah Sampai di Lokasi Anda (Update dari Vendor)</strong>
                        <span class="px-2 py-0.5 rounded bg-emerald-200 text-emerald-900 text-[10px] font-bold">Menunggu Konfirmasi Buyer</span>
                    </div>
                    <p class="text-slate-600 text-[11px] mt-0.5">
                        Mitra Vendor telah memperbarui status barang tiba di lokasi Anda pada <?= !empty($order['delivered_at']) ? (date('d M Y, H:i', strtotime($order['delivered_at'])) . ' WIB') : (date('d M Y') . ' WIB') ?>.
                        Silakan periksa fisik barang. Bila sudah sesuai, klik tombol konfirmasi di samping untuk menyelesaikan pesanan & mengesahkan BAST.
                    </p>
                </div>
            </div>
            <button type="button" onclick="openCompleteOrderModal()" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-xs transition shadow-md flex items-center gap-2 flex-shrink-0">
                <span class="material-symbols-outlined text-[18px]">verified</span>
                <span>Konfirmasi Diterima & Sahkan BAST</span>
            </button>
        </div>
    <?php elseif (in_array($order['order_status'], ['completed', 'bast_signed'])): ?>
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-4 shadow-2xs">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0">
                    <span class="material-symbols-outlined text-[24px]">verified</span>
                </div>
                <div>
                    <strong class="text-slate-900 font-bold text-xs block">Pesanan Telah Selesai & Diterima Sah oleh Buyer</strong>
                    <p class="text-slate-500 text-[11px] mt-0.5">
                        Dikonfirmasi oleh <strong><?= esc($order['delivered_by_name'] ?? session('user_name')) ?></strong> pada <?= !empty($order['delivered_at']) ? (date('d M Y, H:i', strtotime($order['delivered_at'])) . ' WIB') : (date('d M Y') . ' WIB') ?>. Transaksi tuntas & form terkunci permanen.
                    </p>
                </div>
            </div>
            <div class="text-right text-[11px] font-mono text-slate-500 hidden sm:block">
                <span>No. BAST: <strong class="text-slate-800"><?= esc(!empty($order['bast_number']) ? $order['bast_number'] : ('BAST/' . date('Ym') . '/' . str_pad($order['id'], 4, '0', STR_PAD_LEFT))) ?></strong></span>
            </div>
        </div>
    <?php endif; ?>

    <!-- 4. Form & Tampilan Penilaian serta Bintang (Reviews & Star Ratings) -->
    <?php if (in_array($order['order_status'], ['delivered', 'bast_signed', 'completed'])): ?>
        <div class="bg-white rounded-3xl border border-amber-200/90 shadow-md p-6 sm:p-8 space-y-5" id="reviewSection">
            <div class="flex items-center justify-between pb-3 border-b border-amber-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[18px]">star</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Penilaian & Ulasan Mitra Vendor</h3>
                        <p class="text-[11px] text-slate-500">Ulasan kepuasan untuk vendor: <strong class="text-slate-700"><?= esc($order['vendor_name']) ?></strong></p>
                    </div>
                </div>
                <?php if (!empty($review)): ?>
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold border border-emerald-300 flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">check</span>
                        Telah Dinilai
                    </span>
                <?php endif; ?>
            </div>

            <?php if (empty($review)): ?>
                <!-- Form Input Penilaian Bintang Baru -->
                <form action="<?= base_url('customer/orders/review') ?>" method="POST" class="space-y-5"><?= csrf_field() ?>
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="rating" id="selectedRating" value="5">

                    <div class="p-5 rounded-2xl bg-amber-50/50 border border-amber-200/70 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">Tingkat Kepuasan Keseluruhan (Pilih Bintang 1 - 5):</label>
                            <div class="flex items-center gap-2" id="starRatingContainer">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button" 
                                            class="star-btn text-amber-400 hover:text-amber-500 hover:scale-110 transition duration-150 p-1" 
                                            data-rating="<?= $i ?>" 
                                            onclick="setStarRating(<?= $i ?>)"
                                            onmouseover="highlightStars(<?= $i ?>)"
                                            onmouseout="resetStars()">
                                        <span class="material-symbols-outlined text-[36px] font-fill">star</span>
                                    </button>
                                <?php endfor; ?>
                                <span id="ratingLabel" class="ml-2 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-black">
                                    5 Bintang - Sangat Puas / Rekomendasi
                                </span>
                            </div>
                        </div>

                        <!-- Sub-Ratings -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Kualitas & Kesesuaian Produk:</label>
                                <select name="rating_quality" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 bg-white font-medium">
                                    <option value="5" selected>⭐⭐⭐⭐⭐ Sempurna (100% Sesuai)</option>
                                    <option value="4">⭐⭐⭐⭐ Baik</option>
                                    <option value="3">⭐⭐⭐ Cukup</option>
                                    <option value="2">⭐⭐ Kurang Memuaskan</option>
                                    <option value="1">⭐ Buruk / Rusak</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Kecepatan Pengiriman & Packing:</label>
                                <select name="rating_delivery" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 bg-white font-medium">
                                    <option value="5" selected>⭐⭐⭐⭐⭐ Sangat Cepat & Rapi</option>
                                    <option value="4">⭐⭐⭐⭐ Tepat Waktu</option>
                                    <option value="3">⭐⭐⭐ Normal</option>
                                    <option value="2">⭐⭐ Agak Terlambat</option>
                                    <option value="1">⭐ Sangat Terlambat</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Keramahan & Komunikasi Vendor:</label>
                                <select name="rating_service" class="w-full text-xs py-2 px-3 rounded-xl border border-slate-300 bg-white font-medium">
                                    <option value="5" selected>⭐⭐⭐⭐⭐ Responsif & Profesional</option>
                                    <option value="4">⭐⭐⭐⭐ Ramah</option>
                                    <option value="3">⭐⭐⭐ Cukup</option>
                                    <option value="2">⭐⭐ Lambat Merespon</option>
                                    <option value="1">⭐ Tidak Responsif</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">Judul Ulasan (Opsional):</label>
                            <input type="text" name="review_title" placeholder="Contoh: Pengadaan Genset & Filter Sesuai Ekspektasi Hotel" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-800 mb-1">Ulasan Tertulis / Komentar Kepuasan: <span class="text-rose-500">*</span></label>
                            <textarea name="review_text" rows="3" required placeholder="Tuliskan ulasan detail mengenai kondisi barang saat diterima, kesesuaian spesifikasi teknik, dan ketepatan pengiriman dari vendor ini..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 bg-white text-xs leading-relaxed"></textarea>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-amber-500/20">
                            <span class="material-symbols-outlined text-[18px]">stars</span>
                            <span>Kirim Penilaian & Bintang</span>
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <!-- Tampilan Hasil Penilaian yang Sudah Diberikan -->
                <div class="p-5 rounded-2xl bg-amber-50/40 border border-amber-200/80 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center text-amber-400">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="material-symbols-outlined text-[26px] <?= $i <= (int)$review['rating'] ? 'text-amber-400 font-fill' : 'text-slate-300' ?>">star</span>
                                <?php endfor; ?>
                            </div>
                            <span class="text-base font-black text-slate-900"><?= (float)$review['rating'] ?>.0 / 5.0</span>
                            <span class="text-xs text-slate-500">| Oleh: <strong><?= esc($review['buyer_name']) ?></strong></span>
                        </div>
                        <span class="text-[11px] text-slate-400 font-mono"><?= date('d F Y, H:i', strtotime($review['created_at'])) . ' WIB' ?></span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-[11px]">
                        <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 font-medium">Kualitas: <strong><?= (int)($review['rating_quality'] ?? 5) ?>★</strong></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 font-medium">Pengiriman: <strong><?= (int)($review['rating_delivery'] ?? 5) ?>★</strong></span>
                        <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 font-medium">Pelayanan: <strong><?= (int)($review['rating_service'] ?? 5) ?>★</strong></span>
                    </div>

                    <?php if (!empty($review['review_title'])): ?>
                        <h4 class="font-bold text-slate-900 text-xs"><?= esc($review['review_title']) ?></h4>
                    <?php endif; ?>
                    <p class="text-slate-700 text-xs leading-relaxed bg-white p-3.5 rounded-xl border border-slate-200/80 italic">
                        "<?= esc($review['review_text']) ?>"
                    </p>

                    <!-- Balasan Vendor jika ada -->
                    <?php if (!empty($review['vendor_reply'])): ?>
                        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-xs space-y-1 mt-3">
                            <div class="flex items-center justify-between">
                                <strong class="text-blue-950 font-bold flex items-center gap-1.5">
                                    <span class="material-symbols-outlined text-blue-600 text-[16px]">reply</span>
                                    Tanggapan Resmi dari <?= esc($order['vendor_name']) ?>:
                                </strong>
                                <span class="text-[10px] text-blue-600 font-mono"><?= date('d M Y', strtotime($review['vendor_replied_at'])) ?></span>
                            </div>
                            <p class="text-slate-700 leading-relaxed"><?= esc($review['vendor_reply']) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- 5. Dokumen Resmi Purchase Order (PO) -->
    <div class="space-y-6">
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl p-6 sm:p-8 space-y-6 print:p-0 print:border-none print:shadow-none">
            
            <!-- Document Top Bar: Logo, PO Number, Status -->
            <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pb-5 border-b-2 border-slate-900">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-purple-700 via-blue-600 to-pink-500 flex items-center justify-center text-white font-bold text-lg">
                            A
                        </div>
                        <div>
                            <span class="text-lg font-black text-slate-900 tracking-tight">AkuAda<span class="text-pink-500">.</span></span>
                            <span class="block text-[9px] uppercase font-bold text-purple-700 tracking-wider">Official E-Procurement B2B</span>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-500 max-w-sm pt-0.5">
                        Gedung AkuAda Procurement Tower Lt. 18, Jakarta Selatan | Telp: (021) 5088-9900
                    </p>
                </div>

                <div class="text-left sm:text-right space-y-1">
                    <span class="text-[10px] uppercase font-extrabold tracking-widest text-slate-400 block">Surat Pesanan Resmi</span>
                    <h2 class="text-base sm:text-lg font-black text-purple-950 font-mono"><?= esc($order['po_number'] ?? $order['order_number']) ?></h2>
                    <div class="pt-1 flex sm:justify-end gap-1.5 flex-wrap">
                        <?= badge_order_status($order['order_status']) ?>
                        <?= badge_payment_method($order['payment_method']) ?>
                    </div>
                </div>
            </div>

            <!-- Buyer & Vendor Information Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-4 rounded-2xl bg-purple-50/50 border border-purple-100 space-y-2">
                    <span class="text-[10px] uppercase font-bold text-purple-700 block tracking-wider">Pemesan (Corporate Buyer):</span>
                    <strong class="text-slate-900 block text-sm"><?= esc($order['buyer_name']) ?></strong>
                    <div class="space-y-0.5 text-slate-600 text-[11px]">
                        <p>NPWP: <span class="font-mono"><?= esc($order['buyer_npwp'] ?? '-') ?></span></p>
                        <p>PIC: <?= esc($order['buyer_pic'] ?? 'Tim Procurement') ?></p>
                        <p>Alamat Kirim: <?= esc($order['shipping_address'] ?? ($order['buyer_address'] ?? 'Kantor / Gudang Pemesan')) ?></p>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-blue-50/50 border border-blue-100 space-y-2">
                    <span class="text-[10px] uppercase font-bold text-blue-700 block tracking-wider">Penyedia (Mitra Vendor):</span>
                    <strong class="text-slate-900 block text-sm"><?= esc($order['vendor_name']) ?></strong>
                    <div class="space-y-0.5 text-slate-600 text-[11px]">
                        <p>NPWP: <span class="font-mono"><?= esc($order['vendor_npwp'] ?? '-') ?></span></p>
                        <p>PIC: <?= esc($order['vendor_pic'] ?? 'Sales B2B') ?></p>
                        <p>Alamat: <?= esc($order['vendor_address'] ?? 'Pusat Distribusi Vendor') ?></p>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-100 text-slate-700 uppercase text-[10px] font-bold">
                        <tr>
                            <th class="px-3.5 py-2.5">No</th>
                            <th class="px-3.5 py-2.5">Nama Barang / Deskripsi</th>
                            <th class="px-3.5 py-2.5 text-right">Qty</th>
                            <th class="px-3.5 py-2.5 text-right">Harga Satuan</th>
                            <th class="px-3.5 py-2.5 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($order['items'])): ?>
                            <tr>
                                <td colspan="5" class="px-3.5 py-4 text-center text-slate-400">Tidak ada rincian item PO.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($order['items'] as $idx => $it): ?>
                                <tr>
                                    <td class="px-3.5 py-2.5 text-slate-400"><?= $idx + 1 ?></td>
                                    <td class="px-3.5 py-2.5">
                                        <strong class="text-slate-900 block"><?= esc($it['product_name']) ?></strong>
                                    </td>
                                    <td class="px-3.5 py-2.5 text-right font-mono"><?= number_format($it['qty'] ?? 0) ?> <?= esc($it['uom'] ?? 'Pcs') ?></td>
                                    <td class="px-3.5 py-2.5 text-right font-mono"><?= format_rupiah($it['unit_price'] ?? 0) ?></td>
                                    <td class="px-3.5 py-2.5 text-right font-mono font-bold text-slate-900"><?= format_rupiah($it['subtotal']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-slate-50 border-t-2 border-slate-200 text-xs font-semibold">
                        <tr>
                            <td colspan="4" class="px-3.5 py-2 text-right text-slate-600">Subtotal DPP:</td>
                            <td class="px-3.5 py-2 text-right font-mono text-slate-900"><?= format_rupiah($order['subtotal']) ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-3.5 py-2 text-right text-blue-700">Pajak Pertambahan Nilai (PPN <?= esc(format_tax_rate()) ?>):</td>
                            <td class="px-3.5 py-2 text-right font-mono text-blue-700"><?= format_rupiah($order['ppn_amount']) ?></td>
                        </tr>
                        <tr>
                            <td colspan="4" class="px-3.5 py-2 text-right text-slate-600">Biaya Pengiriman:</td>
                            <td class="px-3.5 py-2 text-right font-mono text-slate-900"><?= format_rupiah($order['shipping_amount'] ?? 0) ?></td>
                        </tr>
                        <tr class="text-xs bg-purple-50/70">
                            <td colspan="4" class="px-3.5 py-2.5 text-right text-purple-950 font-black">TOTAL NILAI PO:</td>
                            <td class="px-3.5 py-2.5 text-right font-mono text-purple-950 font-black text-sm"><?= format_rupiah($order['total_amount']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- B2B Terms & Signatures -->
            <div class="space-y-4 pt-3 border-t border-slate-100 text-xs">
                <div class="space-y-1 text-slate-600 text-[11px]">
                    <p>• Jatuh tempo pelunasan: <strong><?= $order['due_date'] ? date('d F Y', strtotime($order['due_date'])) : '30 Hari setelah BAST' ?></strong>.</p>
                    <p>• Nomor Surat Jalan / BAST: <strong><?= esc($order['bast_number'] ?? 'Diterbitkan saat pengiriman') ?></strong>.</p>
                </div>

                <!-- Signatures -->
                <div class="grid grid-cols-2 gap-3 text-center">
                    <div class="border border-slate-200 p-3 rounded-xl space-y-6">
                        <span class="text-[9px] text-slate-400 uppercase font-bold block">Penyedia (Vendor)</span>
                        <div class="h-6"></div>
                        <div>
                            <strong class="block text-slate-900 underline text-xs"><?= esc($order['vendor_name']) ?></strong>
                            <span class="text-[9px] text-slate-400"><?= esc(get_system_setting('inv_vendor_default_title', 'Head of Corporate Sales')) ?></span>
                        </div>
                    </div>

                    <div class="border border-purple-200 bg-purple-50/20 p-3 rounded-xl space-y-6">
                        <span class="text-[9px] text-purple-700 uppercase font-bold block">Disetujui Oleh (Buyer)</span>
                        <div class="h-6 flex items-center justify-center">
                            <?php if (in_array($order['order_status'], ['bast_signed', 'completed'])): ?>
                                <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[9px] font-bold border border-emerald-300">
                                    BAST VALID & TTD SAH
                                </span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <strong class="block text-slate-900 underline text-xs"><?= esc($order['buyer_pic'] ?? 'Rina Kartika') ?></strong>
                            <span class="text-[9px] text-slate-400"><?= esc(get_system_setting('inv_buyer_default_title', 'Senior Procurement Manager')) ?></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Banner Kolaborasi & Chat di luar PO -->
        <div class="p-5 rounded-2xl bg-gradient-to-r from-purple-50 via-indigo-50 to-white border border-purple-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="material-symbols-outlined text-[22px]">forum</span>
                </div>
                <div>
                    <strong class="text-slate-900 font-bold text-xs block">Ruang Komunikasi & Bantuan Khusus Pesanan Ini</strong>
                    <p class="text-slate-500 text-[11px] mt-0.5">
                        Koordinasikan status pengiriman, kendala barang, atau klarifikasi faktur langsung didampingi oleh <strong>Tim CS & Pengadaan AkuAda</strong> di Pusat Chat.
                    </p>
                </div>
            </div>
            <a href="<?= base_url('customer/chat?key=admin_comp_' . $order['customer_company_id']) ?>" class="px-5 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs transition flex items-center gap-2 shadow-md shadow-purple-600/20 flex-shrink-0">
                <span class="material-symbols-outlined text-[18px]">chat</span>
                <span>Buka Chat Bantuan PO ↗</span>
            </a>
        </div>
    </div>
</div>

<!-- Modal 1: Konfirmasi Pesanan Selesai & Sahkan BAST (Khusus Buyer) -->
<div id="confirmDeliveryModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl border border-emerald-200 animate-in fade-in zoom-in duration-150">
        <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
            <div class="w-10 h-10 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shadow-md">
                <span class="material-symbols-outlined text-[22px]">verified</span>
            </div>
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Konfirmasi Pesanan Selesai & BAST</h3>
                <p class="text-xs text-slate-500">Nomor PO: <span class="font-mono font-bold text-purple-700"><?= esc($order['po_number'] ?? $order['order_number']) ?></span></p>
            </div>
        </div>

        <form action="<?= base_url('customer/orders/complete') ?>" method="POST" class="space-y-4"><?= csrf_field() ?>
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pejabat / Staf Penerima di Gudang: <span class="text-rose-500">*</span></label>
                <input type="text" name="signer_name" value="<?= esc(session('user_name') ?? 'Rina Kartika') ?>" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Penerimaan & Kelengkapan Barang: <span class="text-rose-500">*</span></label>
                <textarea name="completion_notes" rows="3" required placeholder="Contoh: Seluruh kardus telah diperiksa, segel aman tidak rusak, kuantitas item lengkap 100% sesuai PO." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs leading-relaxed focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">Seluruh barang telah diperiksa fisik di lokasi kami dalam kondisi baik, lengkap 100%, dan dokumen BAST disahkan secara digital.</textarea>
            </div>

            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 flex items-start gap-2.5">
                <input type="checkbox" id="checkVerified" required class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500">
                <label for="checkVerified" class="text-[11px] leading-tight cursor-pointer">
                    Saya menyatakan secara resmi bahwa seluruh barang pesanan telah diperiksa, diterima dengan baik, dan dokumen BAST resmi disahkan. Status transaksi akan diselesaikan dan dikunci permanen.
                </label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeConfirmDeliveryModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-emerald-600/20">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Selesaikan Pesanan & BAST</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Pengajuan Klaim Retur / Pengembalian Barang Rusak -->
<div id="returnRequestModal" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-5 shadow-2xl border border-rose-300 animate-in fade-in zoom-in duration-150 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-600 text-white flex items-center justify-center shadow-md">
                    <span class="material-symbols-outlined text-[22px]">assignment_return</span>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900">Form Pengajuan Retur Barang</h3>
                    <p class="text-xs text-slate-500">PO: <span class="font-mono font-bold text-purple-700"><?= esc($order['po_number'] ?? $order['order_number']) ?></span></p>
                </div>
            </div>
            <button type="button" onclick="closeReturnModal()" class="text-slate-400 hover:text-slate-600">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <form action="<?= base_url('customer/orders/return-request') ?>" method="POST" enctype="multipart/form-data" class="space-y-4"><?= csrf_field() ?>
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Masalah / Alasan Retur: <span class="text-rose-500">*</span></label>
                <select name="reason_category" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-medium focus:border-rose-600 focus:ring-1 focus:ring-rose-600">
                    <option value="damaged">1. Barang Rusak / Pecah saat Pengiriman</option>
                    <option value="defective">2. Barang Cacat Produksi / Tidak Berfungsi</option>
                    <option value="wrong_item">3. Barang / Spesifikasi Tidak Sesuai PO</option>
                    <option value="incomplete">4. Kuantitas Kurang / Komponen Hilang</option>
                    <option value="expired">5. Melewati Masa Kadaluarsa (Expired)</option>
                    <option value="other">6. Alasan Khusus Lainnya</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Solusi / Penyelesaian yang Diharapkan: <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="p-3 rounded-xl border border-purple-200 bg-purple-50/50 cursor-pointer flex items-center gap-2 text-xs font-bold text-purple-950">
                        <input type="radio" name="resolution_requested" value="replacement" checked class="text-purple-600">
                        <span>Ganti Barang Baru</span>
                    </label>
                    <label class="p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 cursor-pointer flex items-center gap-2 text-xs font-bold text-emerald-950">
                        <input type="radio" name="resolution_requested" value="refund_credit" class="text-emerald-600">
                        <span>Potong Tagihan TOP</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Rincian Barang yang Bermasalah (Nama & Jumlah Unit):</label>
                <input type="text" name="item_description" placeholder="Contoh: 100 Pcs Slipper putih sobek jahitannya" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kronologi Kerusakan / Penjelasan Detail: <span class="text-rose-500">*</span></label>
                <textarea name="damage_description" rows="3" required placeholder="Jelaskan secara rinci kondisi fisik barang, bagaimana kerusakan teridentifikasi saat unboxing di gudang, dll..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs leading-relaxed focus:border-rose-600 focus:ring-1 focus:ring-rose-600"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Unggah Foto Bukti Fisik Kerusakan:</label>
                <input type="file" name="evidence_photo" accept="image/*" class="w-full text-xs file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 cursor-pointer">
                <span class="text-[10px] text-slate-400 block mt-1">Format: JPG, PNG, atau WebP. Maks 5MB.</span>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeReturnModal()" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold text-xs">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white font-bold text-xs flex items-center gap-1.5 shadow-md shadow-rose-600/20">
                    <span class="material-symbols-outlined text-[18px]">send</span>
                    <span>Kirim Klaim Retur</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Sign BAST Hidden Form -->
<form id="signBastForm" action="<?= base_url('customer/orders/sign-bast') ?>" method="POST" style="display: none;"><?= csrf_field() ?>
    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
    <input type="hidden" name="signer_name" value="<?= esc(session('user_name') ?? 'Rina Kartika') ?>">
</form>

<script>
    function openCompleteOrderModal() {
        const modal = document.getElementById('confirmDeliveryModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeCompleteOrderModal() {
        const modal = document.getElementById('confirmDeliveryModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openConfirmDeliveryModal() {
        openCompleteOrderModal();
    }

    function closeConfirmDeliveryModal() {
        closeCompleteOrderModal();
    }

    function openReturnModal() {
        const modal = document.getElementById('returnRequestModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeReturnModal() {
        const modal = document.getElementById('returnRequestModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function confirmSignBast() {
        AkuAdaAlert.confirm(
            'Sahkan Berita Acara Serah Terima (BAST)?',
            'Dengan mengesahkan dokumen BAST ini, Anda mengonfirmasi bahwa seluruh barang telah diterima dalam kondisi baik dan lengkap sesuai nomor PO. Periode tempo pembayaran TOP resmi berjalan.',
            function() {
                document.getElementById('signBastForm').submit();
            }
        );
    }

    // Star Rating Interactivity
    const starLabels = {
        1: '1 Bintang - Sangat Kecewa / Rusak',
        2: '2 Bintang - Kurang Puas',
        3: '3 Bintang - Cukup Baik',
        4: '4 Bintang - Puas & Memuaskan',
        5: '5 Bintang - Sangat Puas / Rekomendasi!'
    };

    let currentSelectedRating = 5;

    function setStarRating(rating) {
        currentSelectedRating = rating;
        document.getElementById('selectedRating').value = rating;
        updateStarUI(rating);
        document.getElementById('ratingLabel').innerText = starLabels[rating] || (rating + ' Bintang');
    }

    function highlightStars(rating) {
        updateStarUI(rating);
        document.getElementById('ratingLabel').innerText = starLabels[rating] || (rating + ' Bintang');
    }

    function resetStars() {
        updateStarUI(currentSelectedRating);
        document.getElementById('ratingLabel').innerText = starLabels[currentSelectedRating] || (currentSelectedRating + ' Bintang');
    }

    function updateStarUI(rating) {
        const buttons = document.querySelectorAll('.star-btn');
        buttons.forEach((btn, index) => {
            const starIcon = btn.querySelector('.material-symbols-outlined');
            if (index < rating) {
                starIcon.classList.add('text-amber-400');
                starIcon.classList.remove('text-slate-300');
            } else {
                starIcon.classList.remove('text-amber-400');
                starIcon.classList.add('text-slate-300');
            }
        });
    }
</script>

<style>
    .font-fill {
        font-variation-settings: 'FILL' 1;
    }
</style>

<?= $this->endSection() ?>
