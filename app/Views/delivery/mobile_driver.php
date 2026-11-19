<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= esc($title ?? 'Kurir Pengantaran PO - AkuAda') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts & Material Symbols -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased pb-24">

    <!-- Top Sticky Mobile Header -->
    <header class="sticky top-0 z-30 bg-slate-900 text-white shadow-md border-b border-slate-800">
        <div class="max-w-lg mx-auto px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center font-black text-white text-base shadow">
                    A
                </div>
                <div>
                    <div class="text-[10px] font-bold text-blue-400 uppercase tracking-widest leading-none">AkuAda Logistics</div>
                    <div class="font-mono font-extrabold text-sm text-white leading-tight mt-0.5">
                        <?= esc($order['po_number'] ?? $order['order_number']) ?>
                    </div>
                </div>
            </div>
            <div>
                <?= badge_order_status($order['order_status']) ?>
            </div>
        </div>
    </header>

    <!-- Main Container (Mobile App Width) -->
    <main class="max-w-lg mx-auto px-4 py-4 space-y-4">

        <!-- Flash Message Alert -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="p-3.5 rounded-2xl bg-emerald-500 text-white text-xs font-bold flex items-start gap-2 shadow-lg animate-bounce">
                <span class="material-symbols-outlined text-[20px] flex-shrink-0">check_circle</span>
                <div class="flex-grow leading-snug">
                    <?= session()->getFlashdata('success') ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="p-3.5 rounded-2xl bg-rose-600 text-white text-xs font-bold flex items-start gap-2 shadow-lg">
                <span class="material-symbols-outlined text-[20px] flex-shrink-0">error</span>
                <div class="flex-grow leading-snug">
                    <?= session()->getFlashdata('error') ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Hero Card: Pengirim & Armada -->
        <div class="bg-gradient-to-br from-blue-900 via-indigo-900 to-slate-900 text-white rounded-2xl p-4 shadow-md space-y-3 relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-28 h-28 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="flex items-center justify-between text-[11px] text-blue-200">
                <span class="flex items-center gap-1 font-semibold">
                    <span class="material-symbols-outlined text-[15px] text-blue-400">store</span>
                    <?= esc($order['vendor_name'] ?? 'Mitra Vendor') ?>
                </span>
                <span class="px-2 py-0.5 rounded-md bg-blue-500/20 text-blue-300 font-bold font-mono text-[10px]">
                    Armada Sendiri
                </span>
            </div>

            <div class="flex items-center gap-3 pt-1">
                <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-white flex-shrink-0 shadow-inner">
                    <span class="material-symbols-outlined text-2xl text-blue-300">two_wheeler</span>
                </div>
                <div class="leading-tight">
                    <div class="text-[11px] text-slate-300">Driver / Kurir Pengantar:</div>
                    <div class="font-extrabold text-base text-white mt-0.5">
                        <?= esc($order['driver_name'] ?? 'Belum Diisi Driver') ?>
                    </div>
                    <div class="text-xs text-blue-300 font-mono mt-0.5 flex items-center gap-2">
                        <span>Plat: <strong><?= esc($order['vehicle_plate'] ?? '-') ?></strong></span>
                        <?php if (!empty($order['driver_phone'])): ?>
                            <span>• <?= esc($order['driver_phone']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Progress Bar -->
            <?php
                $statusDetail = $order['delivery_status_detail'] ?? ($order['order_status'] === 'delivered' ? 'delivered' : 'on_the_way');
                $isDelivered = ($order['order_status'] === 'delivered' || $statusDetail === 'delivered' || in_array($order['order_status'], ['bast_signed', 'completed']));
                $isArrived = ($statusDetail === 'arrived');
                $isOnTheWay = ($statusDetail === 'on_the_way' || $order['order_status'] === 'shipped');
            ?>
            <div class="pt-2 border-t border-white/10">
                <div class="flex items-center justify-between text-[10px] font-bold mb-1.5">
                    <span class="<?= $isOnTheWay ? 'text-amber-300' : 'text-slate-400' ?>">1. OTW</span>
                    <span class="<?= $isArrived ? 'text-amber-300' : 'text-slate-400' ?>">2. Tiba di Lokasi</span>
                    <span class="<?= $isDelivered ? 'text-emerald-400' : 'text-slate-400' ?>">3. Selesai Serah Terima</span>
                </div>
                <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden flex">
                    <div class="h-full bg-blue-500" style="width: 33%"></div>
                    <div class="h-full <?= ($isArrived || $isDelivered) ? 'bg-amber-400' : 'bg-slate-700' ?>" style="width: 34%"></div>
                    <div class="h-full <?= $isDelivered ? 'bg-emerald-400' : 'bg-slate-700' ?>" style="width: 33%"></div>
                </div>
            </div>
        </div>

        <!-- Card: Titik Tujuan Pengiriman & Tombol Navigasi HP -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-rose-500 text-[18px]">location_on</span>
                    <h2 class="font-extrabold text-xs text-slate-800 uppercase tracking-wide">Tujuan Pengiriman</h2>
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase">Titik Serah</span>
            </div>

            <div>
                <div class="font-black text-sm text-slate-900 leading-snug">
                    <?= esc($order['buyer_name']) ?>
                </div>
                <?php
                    $fullAddress = trim(($order['shipping_address'] ?? '') ?: ($order['buyer_address'] ?? ''));
                    $destinationCity = trim(($order['buyer_city'] ?? '') . ', ' . ($order['buyer_province'] ?? ''));
                ?>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    <?= esc($fullAddress ?: 'Alamat kantor buyer belum terisi') ?>
                    <?php if (!empty($destinationCity)): ?>
                        <span class="font-semibold block text-slate-800 mt-0.5"><?= esc($destinationCity) ?></span>
                    <?php endif; ?>
                </p>
            </div>

            <!-- PIC Info -->
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                <div>
                    <span class="text-[10px] text-slate-400 block font-semibold">PIC Penerima Lapangan:</span>
                    <strong class="text-slate-800 font-bold"><?= esc($order['buyer_pic'] ?? 'PIC Pengadaan Buyer') ?></strong>
                    <div class="text-[11px] text-slate-500 font-mono"><?= esc($order['buyer_pic_phone'] ?? '-') ?></div>
                </div>
                <div class="flex items-center gap-1.5">
                    <?php
                        $cleanPhone = preg_replace('/[^0-9]/', '', (string)($order['buyer_pic_phone'] ?? ''));
                        if (str_starts_with($cleanPhone, '0')) {
                            $waPhone = '62' . substr($cleanPhone, 1);
                        } else {
                            $waPhone = $cleanPhone;
                        }
                    ?>
                    <?php if (!empty($waPhone)): ?>
                        <a href="https://wa.me/<?= $waPhone ?>?text=Halo%20<?= urlencode($order['buyer_pic'] ?? '') ?>,%20saya%20kurir%20dari%20<?= urlencode($order['vendor_name'] ?? 'Vendor') ?>%20sedang%20mengantarkan%20pesanan%20PO%20<?= urlencode($order['po_number'] ?? $order['order_number']) ?>.%20Mohon%20info%20titik%20penurunan%20barang." 
                           target="_blank"
                           class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white border border-emerald-200 transition flex items-center justify-center shadow-xs"
                           title="Chat WhatsApp PIC">
                            <span class="material-symbols-outlined text-[18px]">chat</span>
                        </a>
                        <a href="tel:<?= esc($cleanPhone) ?>" 
                           class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white border border-blue-200 transition flex items-center justify-center shadow-xs"
                           title="Telepon PIC">
                            <span class="material-symbols-outlined text-[18px]">call</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Google Maps 1-Tap Action Button -->
            <?php
                $mapQuery = urlencode($fullAddress . ' ' . $destinationCity . ' ' . ($order['buyer_name'] ?? ''));
            ?>
            <a href="https://www.google.com/maps/search/?api=1&query=<?= $mapQuery ?>" 
               target="_blank" 
               class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold text-xs flex items-center justify-center gap-2 shadow-md hover:from-blue-700 hover:to-indigo-700 transition">
                <span class="material-symbols-outlined text-[18px]">navigation</span>
                <span>Buka Rute di Google Maps (GPS)</span>
            </a>
        </div>

        <!-- Card: Muatan Barang / Kargo Pesanan -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3" x-data="{ open: false }">
            <div class="flex items-center justify-between cursor-pointer" onclick="document.getElementById('cargoList').classList.toggle('hidden')">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-purple-600 text-[18px]">inventory_2</span>
                    <h2 class="font-extrabold text-xs text-slate-800 uppercase tracking-wide">
                        Muatan Barang (<?= count($order['items'] ?? []) ?> Item)
                    </h2>
                </div>
                <button type="button" class="text-blue-600 text-xs font-bold flex items-center gap-0.5">
                    <span>Lihat Daftar</span>
                    <span class="material-symbols-outlined text-[16px]">expand_more</span>
                </button>
            </div>

            <div id="cargoList" class="space-y-2 border-t border-slate-100 pt-2">
                <?php if (!empty($order['items'])): ?>
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-start justify-between text-xs gap-2">
                            <div>
                                <div class="font-bold text-slate-900"><?= esc($item['product_name']) ?></div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">SKU: <?= esc($item['sku'] ?? '-') ?></div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="px-2 py-0.5 rounded-lg bg-blue-100 text-blue-900 font-extrabold font-mono text-xs">
                                    <?= (int)$item['quantity'] ?> <?= esc($item['unit'] ?? 'Unit') ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-xs text-slate-400 text-center py-2">Daftar item tidak tersedia.</p>
                <?php endif; ?>

                <?php if (!empty($order['bast_number'])): ?>
                    <a href="<?= base_url('vendor/orders/bast/' . $order['id']) ?>" target="_blank" 
                       class="w-full py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] flex items-center justify-center gap-1.5 transition mt-2">
                        <span class="material-symbols-outlined text-[16px] text-slate-600">description</span>
                        <span>Lihat Dokumen Surat Jalan / BAST Resmi</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- FORM UPDATE STATUS PERJALANAN (HP TOUCH FIRST) -->
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                <div class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-amber-500 text-[20px]">electric_bolt</span>
                    <h2 class="font-extrabold text-xs text-slate-900 uppercase tracking-wide">Update Status Pengiriman</h2>
                </div>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                    Kamera Aktif
                </span>
            </div>

            <form action="<?= base_url('delivery/update/' . $token) ?>" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?= csrf_field() ?>

                <input type="hidden" name="delivery_latitude" id="delivery_lat" value="<?= esc($order['delivery_latitude'] ?? '') ?>">
                <input type="hidden" name="delivery_longitude" id="delivery_lng" value="<?= esc($order['delivery_longitude'] ?? '') ?>">

                <!-- Tahapan Status: Radio Button Bergaya Tombol Besar -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-2">Pilih Tahap Pengiriman Saat Ini:</label>
                    <div class="grid grid-cols-1 gap-2">
                        <!-- OTW -->
                        <label class="relative flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/70 transition shadow-xs">
                            <input type="radio" name="stage" value="on_the_way" class="sr-only" <?= ($statusDetail === 'on_the_way' || $order['order_status'] === 'shipped') ? 'checked' : '' ?>>
                            <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center flex-shrink-0 mr-3">
                                <span class="material-symbols-outlined text-[18px]">local_shipping</span>
                            </div>
                            <div class="flex-grow">
                                <div class="font-extrabold text-xs text-slate-900">1. Dalam Perjalanan (OTW)</div>
                                <div class="text-[10px] text-slate-500 leading-tight">Barang telah dimuat dan sedang meluncur ke lokasi buyer.</div>
                            </div>
                            <span class="material-symbols-outlined text-blue-600 text-[20px] ml-2 hidden [[type=radio]:checked~*>&]:block">check_circle</span>
                        </label>

                        <!-- Tiba di Lokasi -->
                        <label class="relative flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/70 transition shadow-xs">
                            <input type="radio" name="stage" value="arrived" class="sr-only" <?= ($statusDetail === 'arrived') ? 'checked' : '' ?>>
                            <div class="w-8 h-8 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0 mr-3">
                                <span class="material-symbols-outlined text-[18px]">where_to_vote</span>
                            </div>
                            <div class="flex-grow">
                                <div class="font-extrabold text-xs text-slate-900">2. Tiba di Lokasi Buyer</div>
                                <div class="text-[10px] text-slate-500 leading-tight">Driver sudah sampai di pos satpam / gerbang / titik penurunan.</div>
                            </div>
                            <span class="material-symbols-outlined text-amber-600 text-[20px] ml-2 hidden [[type=radio]:checked~*>&]:block">check_circle</span>
                        </label>

                        <!-- Selesai Serah Terima -->
                        <label class="relative flex items-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/70 transition shadow-xs">
                            <input type="radio" name="stage" value="delivered" class="sr-only" <?= ($isDelivered) ? 'checked' : '' ?>>
                            <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0 mr-3">
                                <span class="material-symbols-outlined text-[18px]">task_alt</span>
                            </div>
                            <div class="flex-grow">
                                <div class="font-extrabold text-xs text-slate-900">3. Barang Diserahkan & Selesai</div>
                                <div class="text-[10px] text-slate-500 leading-tight">Barang telah tuntas diturunkan dan diterima oleh pihak buyer.</div>
                            </div>
                            <span class="material-symbols-outlined text-emerald-600 text-[20px] ml-2 hidden [[type=radio]:checked~*>&]:block">check_circle</span>
                        </label>
                    </div>
                </div>

                <!-- Foto Bukti Pengantaran (POD) Langsung Kamera HP -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">
                        Foto Bukti Serah Terima / Barang di Lokasi (POD):
                    </label>
                    <div class="mt-1">
                        <label for="delivery_proof_input" class="w-full border-2 border-dashed border-blue-300 rounded-2xl p-4 bg-blue-50/40 hover:bg-blue-50 text-center flex flex-col items-center justify-center cursor-pointer transition">
                            <span class="material-symbols-outlined text-blue-600 text-3xl mb-1">photo_camera</span>
                            <span class="font-bold text-xs text-blue-900">Buka Kamera HP & Ambil Foto</span>
                            <span class="text-[10px] text-slate-500 mt-0.5">Atau pilih dari galeri foto (JPG, PNG, WebP)</span>
                            <input type="file" name="delivery_proof" id="delivery_proof_input" accept="image/*" capture="environment" class="hidden" onchange="previewPhoto(this)">
                        </label>

                        <!-- Preview Container -->
                        <div id="photoPreviewContainer" class="hidden mt-2 relative rounded-xl overflow-hidden border border-slate-200 shadow-sm bg-black">
                            <img id="photoPreviewImg" src="" alt="Bukti Foto" class="w-full max-h-56 object-contain mx-auto">
                            <div class="absolute bottom-2 right-2 bg-black/70 text-white text-[10px] px-2 py-0.5 rounded-full font-mono">
                                Foto Siap Dikirim
                            </div>
                        </div>

                        <?php if (!empty($order['delivery_proof_photo'])): ?>
                            <div class="mt-2 p-2 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                <span class="text-slate-600 font-semibold flex items-center gap-1 text-[11px]">
                                    <span class="material-symbols-outlined text-emerald-600 text-[16px]">image</span>
                                    Foto sebelumnya tersimpan
                                </span>
                                <a href="<?= base_url($order['delivery_proof_photo']) ?>" target="_blank" class="text-blue-600 font-bold text-[11px] hover:underline">
                                    Lihat Foto
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Form Driver & Kendaraan -->
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nama Driver: <span class="text-rose-500">*</span></label>
                        <input type="text" name="driver_name" value="<?= esc($order['driver_name'] ?? '') ?>" required placeholder="Contoh: Pak Herman" 
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-blue-600 bg-white font-semibold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Plat Kendaraan:</label>
                        <input type="text" name="vehicle_plate" value="<?= esc($order['vehicle_plate'] ?? '') ?>" placeholder="B 1234 ABC" 
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-blue-600 bg-white font-mono uppercase">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Nomor WhatsApp / HP Driver:</label>
                        <input type="tel" name="driver_phone" value="<?= esc($order['driver_phone'] ?? '') ?>" placeholder="08123456789" 
                               class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-blue-600 bg-white font-mono">
                    </div>
                </div>

                <!-- Form Penerima di Lapangan -->
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
                    <div class="font-bold text-slate-800 text-[11px] flex items-center gap-1">
                        <span class="material-symbols-outlined text-[15px] text-blue-600">person_pin</span>
                        <span>Identitas Penerima di Lokasi Buyer:</span>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <input type="text" name="recipient_name" value="<?= esc($order['recipient_name'] ?? '') ?>" placeholder="Nama Penerima (Misal: Pak Budi Gudang)" 
                                   class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs">
                        </div>
                        <div>
                            <input type="tel" name="recipient_phone" value="<?= esc($order['recipient_phone'] ?? '') ?>" placeholder="No. Telp Penerima" 
                                   class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white font-mono text-xs">
                        </div>
                    </div>
                </div>

                <!-- Catatan Pengiriman -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan Tambahan Kurir / Kondisi Penyerahan:</label>
                    <textarea name="delivery_notes" rows="2" placeholder="Contoh: Barang diturunkan 15 karton dalam keadaan segel utuh, diterima bagian security pos 2..." 
                              class="w-full px-3 py-2 rounded-xl border border-slate-300 focus:border-blue-600 bg-white text-xs leading-relaxed"><?= esc($order['delivery_notes'] ?? '') ?></textarea>
                </div>

                <!-- GPS Location Capture -->
                <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-100 text-xs">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-blue-600 text-[18px]">my_location</span>
                        <span id="gpsStatusText" class="text-[11px] text-slate-600 font-medium">
                            <?= (!empty($order['delivery_latitude'])) ? 'Lokasi tercatat' : 'GPS belum terdeteksi' ?>
                        </span>
                    </div>
                    <button type="button" onclick="detectGpsLocation()" class="px-2.5 py-1 rounded-lg bg-white border border-slate-300 text-blue-700 font-bold text-[11px] shadow-xs active:scale-95 transition">
                        Deteksi GPS
                    </button>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm flex items-center justify-center gap-2 shadow-lg shadow-emerald-600/30 active:scale-98 transition">
                    <span class="material-symbols-outlined text-[20px]">send</span>
                    <span>Kirim Pembaruan Status Sekarang</span>
                </button>
            </form>
        </div>

        <!-- Histori Catatan Pengiriman -->
        <?php if (!empty($order['status_logs'])): ?>
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200/80 space-y-3">
                <div class="flex items-center gap-1.5 border-b border-slate-100 pb-2">
                    <span class="material-symbols-outlined text-slate-500 text-[18px]">history</span>
                    <h2 class="font-extrabold text-xs text-slate-800 uppercase tracking-wide">Catatan Histori Pengiriman</h2>
                </div>

                <div class="space-y-2.5">
                    <?php foreach ($order['status_logs'] as $log): ?>
                        <div class="text-xs pl-3 border-l-2 border-blue-500 space-y-0.5">
                            <div class="flex items-center justify-between">
                                <strong class="font-bold text-slate-900"><?= esc($log['title']) ?></strong>
                                <span class="text-[10px] font-mono text-slate-400"><?= date('d/m H:i', strtotime($log['created_at'])) ?></span>
                            </div>
                            <?php if (!empty($log['description'])): ?>
                                <p class="text-[11px] text-slate-600 leading-snug"><?= esc($log['description']) ?></p>
                            <?php endif; ?>
                            <div class="text-[10px] text-slate-400 italic">Oleh: <?= esc($log['updated_by']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <!-- Bottom Sticky Contact Bar -->
    <div class="fixed bottom-0 inset-x-0 z-20 bg-white/95 backdrop-blur-md border-t border-slate-200 py-2.5 px-4 shadow-xl">
        <div class="max-w-lg mx-auto flex items-center justify-between gap-2">
            <div class="text-xs leading-tight">
                <span class="text-[10px] text-slate-400 block font-semibold">AkuAda Logistics Support:</span>
                <span class="font-extrabold text-slate-800">Butuh Bantuan Kendala?</span>
            </div>
            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20AkuAda,%20saya%20driver%20vendor%20<?= urlencode($order['vendor_name'] ?? '') ?>%20sedang%20mengantar%20PO%20<?= urlencode($order['po_number'] ?? $order['order_number']) ?>%20mengalami%20kendala." 
               target="_blank"
               class="px-3.5 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs flex items-center gap-1.5 shadow-sm active:scale-95 transition">
                <span class="material-symbols-outlined text-[16px] text-emerald-400">support_agent</span>
                <span>Hubungi CS</span>
            </a>
        </div>
    </div>

    <!-- JavaScript Helpers -->
    <script>
        function previewPhoto(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.getElementById('photoPreviewImg');
                    img.src = e.target.result;
                    document.getElementById('photoPreviewContainer').classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function detectGpsLocation() {
            const statusText = document.getElementById('gpsStatusText');
            statusText.innerText = 'Mencari sinyal GPS...';

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        document.getElementById('delivery_lat').value = lat;
                        document.getElementById('delivery_lng').value = lng;
                        statusText.innerText = 'GPS Terdeteksi: ' + lat.toFixed(4) + ', ' + lng.toFixed(4);
                        statusText.classList.add('text-emerald-700', 'font-bold');
                    },
                    function(error) {
                        statusText.innerText = 'GPS ditolak / nonaktif';
                        alert('Tidak dapat mendeteksi lokasi GPS. Pastikan izin lokasi diaktifkan di browser HP Anda.');
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            } else {
                statusText.innerText = 'Browser tidak mendukung GPS';
            }
        }
    </script>
</body>
</html>
