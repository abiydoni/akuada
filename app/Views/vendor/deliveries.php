<?= $this->extend('layouts/vendor') ?>

<?= $this->section('vendor_content') ?>

<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold uppercase tracking-wider">Logistik & Armada Sendiri</span>
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Akses HP Kurir Siap Pakai</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Monitoring Pengiriman PO & Update Status via HP Driver</h1>
            <p class="text-xs text-slate-500 mt-0.5">
                Pantau pengiriman PO yang diantar armada sendiri. Driver/kurir dapat membuka tautan khusus di HP tanpa login untuk update status OTW, Tiba, dan upload foto bukti serah terima.
            </p>
        </div>
        <a href="<?= base_url('vendor/orders') ?>" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1.5 transition self-start sm:self-auto">
            <span class="material-symbols-outlined text-[16px]">arrow_back</span>
            <span>Kembali ke Semua PO</span>
        </a>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">local_shipping</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block tracking-wider">Total Antaran Aktif</span>
                <strong class="text-lg font-black text-slate-900"><?= $totalActive ?></strong>
                <span class="text-[10px] text-slate-400 block">Pesanan berjalan</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">route</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block tracking-wider">Sedang OTW Jalan</span>
                <strong class="text-lg font-black text-amber-600"><?= $otwCount ?></strong>
                <span class="text-[10px] text-slate-400 block">Dalam perjalanan</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">where_to_vote</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block tracking-wider">Tiba di Lokasi Buyer</span>
                <strong class="text-lg font-black text-purple-600"><?= $arrivedCount ?></strong>
                <span class="text-[10px] text-slate-400 block">Menunggu penurunan</span>
            </div>
        </div>

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-2xl">task_alt</span>
            </div>
            <div>
                <span class="text-[10px] font-bold uppercase text-slate-400 block tracking-wider">Selesai Diantar</span>
                <strong class="text-lg font-black text-emerald-600"><?= $deliveredCount ?></strong>
                <span class="text-[10px] text-slate-400 block">Menunggu BAST buyer</span>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-2 text-xs font-bold overflow-x-auto">
        <a href="<?= base_url('vendor/deliveries') ?>" 
           class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 <?= empty($currentFilter) ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' ?>">
            <span class="material-symbols-outlined text-[15px]">inventory</span>
            <span>Semua Antaran</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] <?= empty($currentFilter) ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-700' ?>"><?= count($deliveries) ?></span>
        </a>
        <a href="<?= base_url('vendor/deliveries?status=shipped') ?>" 
           class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 <?= ($currentFilter === 'shipped') ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' ?>">
            <span class="material-symbols-outlined text-[15px]">local_shipping</span>
            <span>Sedang OTW (<?= $otwCount ?>)</span>
        </a>
        <a href="<?= base_url('vendor/deliveries?status=delivered') ?>" 
           class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 <?= ($currentFilter === 'delivered') ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' ?>">
            <span class="material-symbols-outlined text-[15px]">check_circle</span>
            <span>Tuntas Diantar (<?= $deliveredCount ?>)</span>
        </a>
        <a href="<?= base_url('vendor/deliveries?status=preparing') ?>" 
           class="px-3 py-1.5 rounded-xl transition flex items-center gap-1.5 <?= ($currentFilter === 'preparing') ? 'bg-blue-600 text-white shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' ?>">
            <span class="material-symbols-outlined text-[15px]">precision_manufacturing</span>
            <span>Penyiapan & Packing</span>
        </a>
    </div>

    <!-- Deliveries List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                <h3 class="font-bold text-slate-800 text-sm">Daftar Antaran Pesanan</h3>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px]"><?= count($deliveries) ?> PO</span>
            </div>
            <div class="text-[11px] text-slate-500 flex items-center gap-1">
                <span class="material-symbols-outlined text-[15px] text-blue-600">smartphone</span>
                <span>Driver buka link tanpa login</span>
            </div>
        </div>

        <?php if (empty($deliveries)): ?>
            <div class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center text-slate-400">
                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">local_shipping</span>
                    <p class="font-semibold text-slate-600">Tidak ada pengiriman yang sesuai filter</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pesanan PO yang masuk dan siap dikirim akan muncul di halaman ini.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="divide-y divide-slate-100">
                <?php foreach ($deliveries as $del): ?>
                    <?php
                        $token = $del['delivery_token'];
                        $driverLink = base_url('delivery/' . $token);
                        $statusDetail = $del['delivery_status_detail'] ?? ($del['order_status'] === 'delivered' ? 'delivered' : 'on_the_way');
                        $isDelivered = ($del['order_status'] === 'delivered' || $statusDetail === 'delivered' || in_array($del['order_status'], ['bast_signed', 'completed']));
                        $isArrived = ($statusDetail === 'arrived');
                        $isOnTheWay = ($statusDetail === 'on_the_way' || $del['order_status'] === 'shipped');

                        $cleanBuyerPhone = preg_replace('/[^0-9]/', '', (string)($del['buyer_pic_phone'] ?? ''));
                        $cleanDriverPhone = preg_replace('/[^0-9]/', '', (string)($del['driver_phone'] ?? ''));
                        if (str_starts_with($cleanDriverPhone, '0')) {
                            $waDriverPhone = '62' . substr($cleanDriverPhone, 1);
                        } else {
                            $waDriverPhone = $cleanDriverPhone;
                        }

                        $waDriverMsg = "Halo " . ($del['driver_name'] ?: 'Driver') . ", berikut link update pengiriman untuk PO *" . ($del['po_number'] ?? $del['order_number']) . "* tujuan *" . $del['buyer_name'] . "*:\n\n" 
                                     . $driverLink . "\n\n"
                                     . "Silakan klik link di atas saat berangkat (OTW), saat tiba di lokasi, dan upload foto serah terima barang.";
                    ?>
                    <div class="p-5 hover:bg-slate-50/70 transition space-y-4">
                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                            <!-- Kolom 1: Info PO & Status -->
                            <div class="space-y-1.5 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="<?= base_url('vendor/orders/' . $del['id']) ?>" class="font-mono font-extrabold text-blue-700 hover:text-blue-900 text-sm hover:underline">
                                        <?= esc($del['po_number'] ?? $del['order_number']) ?>
                                    </a>
                                    <?= badge_order_status($del['order_status']) ?>
                                    <?php if ($isArrived): ?>
                                        <span class="px-2 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold border border-purple-200 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-purple-600 animate-ping"></span>
                                            Driver Tiba di Lokasi
                                        </span>
                                    <?php elseif ($isOnTheWay): ?>
                                        <span class="px-2 py-0.5 rounded-full bg-sky-100 text-sky-800 text-[10px] font-bold border border-sky-200">
                                            Sedang OTW
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="text-xs font-bold text-slate-900 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[16px] text-slate-400">domain</span>
                                    <span><?= esc($del['buyer_name']) ?></span>
                                </div>

                                <p class="text-xs text-slate-600 leading-relaxed">
                                    <span class="font-semibold text-slate-700">Tujuan:</span> 
                                    <?= esc($del['shipping_address'] ?: ($del['buyer_address'] ?? 'Alamat buyer belum terisi')) ?>
                                    <span class="text-slate-400 font-mono text-[11px] block mt-0.5"><?= esc($del['buyer_city'] ?? '') ?>, <?= esc($del['buyer_province'] ?? '') ?></span>
                                </p>

                                <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-500 pt-1">
                                    <span>PIC Buyer: <strong><?= esc($del['buyer_pic'] ?? '-') ?></strong> (<?= esc($del['buyer_pic_phone'] ?? '-') ?>)</span>
                                    <span>• Surat Jalan BAST: <strong><?= esc($del['bast_number'] ?? '-') ?></strong></span>
                                </div>
                            </div>

                            <!-- Kolom 2: Info Driver Armada -->
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 min-w-[280px] lg:max-w-[320px] space-y-2 text-xs">
                                <div class="flex items-center justify-between border-b border-slate-200 pb-1.5">
                                    <div class="flex items-center gap-1.5 font-bold text-slate-800 text-[11px]">
                                        <span class="material-symbols-outlined text-[16px] text-blue-600">badge</span>
                                        <span>Data Driver Armada</span>
                                    </div>
                                    <button type="button" 
                                            onclick="openAssignDriverModal(<?= $del['id'] ?>, '<?= esc($del['po_number'] ?? $del['order_number']) ?>', '<?= esc($del['driver_name'] ?? '') ?>', '<?= esc($del['driver_phone'] ?? '') ?>', '<?= esc($del['vehicle_plate'] ?? '') ?>', '<?= esc($del['delivery_method'] ?? 'vendor_fleet') ?>')"
                                            class="text-blue-600 hover:text-blue-800 font-bold text-[10px] flex items-center gap-0.5">
                                        <span class="material-symbols-outlined text-[13px]">edit</span>
                                        <span>Ubah Driver</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-1.5 text-[11px]">
                                    <div>
                                        <span class="text-slate-400 text-[10px] block">Nama Driver:</span>
                                        <strong class="text-slate-800"><?= esc($del['driver_name'] ?: 'Belum ditentukan') ?></strong>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 text-[10px] block">No. Plat:</span>
                                        <strong class="font-mono text-slate-800"><?= esc($del['vehicle_plate'] ?: '-') ?></strong>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="text-slate-400 text-[10px] block">No. WhatsApp Driver:</span>
                                        <span class="font-mono text-slate-700"><?= esc($del['driver_phone'] ?: '-') ?></span>
                                    </div>
                                </div>

                                <!-- Foto Bukti Pengiriman jika ada -->
                                <?php if (!empty($del['delivery_proof_photo'])): ?>
                                    <div class="pt-1.5 border-t border-slate-200 flex items-center justify-between">
                                        <span class="text-[10px] font-bold text-emerald-700 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[14px]">photo_camera</span>
                                            Foto Bukti Tersedia
                                        </span>
                                        <a href="<?= base_url($del['delivery_proof_photo']) ?>" target="_blank" class="px-2 py-0.5 rounded bg-emerald-100 hover:bg-emerald-200 text-emerald-800 font-bold text-[10px] transition">
                                            Buka Foto
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Kolom 3: Aksi Akses HP Kurir & WA -->
                            <div class="flex flex-col sm:flex-row lg:flex-col gap-1.5 min-w-[200px] justify-center">
                                <a href="<?= $driverLink ?>" target="_blank" 
                                   class="px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-sm transition">
                                    <span class="material-symbols-outlined text-[16px]">smartphone</span>
                                    <span>Buka Tampilan HP</span>
                                </a>

                                <button type="button" 
                                        onclick="copyDriverLink('<?= $driverLink ?>')" 
                                        class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center gap-1.5 border border-slate-300 transition">
                                    <span class="material-symbols-outlined text-[16px] text-slate-600">content_copy</span>
                                    <span>Salin Link HP</span>
                                </button>

                                <?php if (!empty($waDriverPhone)): ?>
                                    <a href="https://api.whatsapp.com/send?phone=<?= $waDriverPhone ?>&text=<?= urlencode($waDriverMsg) ?>" target="_blank"
                                       class="px-3 py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center gap-1.5 border border-emerald-300 transition">
                                        <span class="material-symbols-outlined text-[16px] text-emerald-600">chat</span>
                                        <span>Kirim ke WA Driver</span>
                                    </a>
                                <?php endif; ?>

                                <button type="button" 
                                        onclick="openQrModal('<?= esc($del['po_number'] ?? $del['order_number']) ?>', '<?= $driverLink ?>')" 
                                        class="px-3 py-1.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-50 font-semibold text-[11px] flex items-center justify-center gap-1 transition">
                                    <span class="material-symbols-outlined text-[15px]">qr_code_2</span>
                                    <span>Scan QR Code</span>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Penetapan Driver / Armada -->
<div id="assignDriverModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden text-xs">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-blue-600">local_shipping</span>
                <h3 class="font-bold text-slate-900 text-sm">Tugaskan Driver & Armada Vendor</h3>
            </div>
            <button type="button" onclick="closeAssignDriverModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form action="<?= base_url('vendor/orders/assign-driver') ?>" method="POST" class="p-6 space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="order_id" id="assign_order_id">

            <div>
                <span class="text-slate-400 text-[11px] block">Purchase Order:</span>
                <strong id="assign_po_number" class="text-slate-900 text-sm font-bold font-mono"></strong>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Metode Pengiriman:</label>
                <select name="delivery_method" id="assign_delivery_method" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 bg-white font-bold">
                    <option value="vendor_fleet">Armada / Kurir Vendor Sendiri (Rekomendasi)</option>
                    <option value="courier_service">Ekspedisi Kargo / 3PL Eksternal</option>
                    <option value="pickup">Pengambilan Mandiri oleh Buyer (Self Pick-up)</option>
                </select>
            </div>

            <div class="space-y-3">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Driver / Kurir: <span class="text-rose-500">*</span></label>
                    <input type="text" name="driver_name" id="assign_driver_name" required placeholder="Contoh: Budi Santoso" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp Driver: <span class="text-rose-500">*</span></label>
                    <input type="tel" name="driver_phone" id="assign_driver_phone" required placeholder="08123456789" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 font-mono">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Digunakan untuk mengirim link update HP langsung ke WhatsApp driver.</span>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Plat Kendaraan:</label>
                    <input type="text" name="vehicle_plate" id="assign_vehicle_plate" placeholder="B 1234 XYZ (Truk Engkel / Motor)" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 font-mono uppercase">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeAssignDriverModal()" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-600 hover:bg-slate-100 font-bold transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition shadow-sm">
                    Simpan Data Driver
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal QR Code -->
<div id="qrModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 text-center space-y-4 shadow-2xl border border-slate-200">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-sm">Scan QR Code dari HP Driver</h3>
            <button type="button" onclick="closeQrModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <div>
            <div id="qrPoTitle" class="font-mono font-bold text-xs text-blue-700 mb-2"></div>
            <div class="w-56 h-56 mx-auto bg-slate-50 p-2 rounded-2xl border border-slate-200 flex items-center justify-center shadow-inner">
                <img id="qrImage" src="" alt="QR Code Driver" class="w-full h-full object-contain">
            </div>
            <p class="text-[11px] text-slate-500 mt-3 leading-relaxed">
                Minta driver mengarahkan kamera HP ke QR Code di atas untuk langsung membuka halaman update pengiriman tanpa perlu login.
            </p>
        </div>

        <button type="button" onclick="closeQrModal()" class="w-full py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs transition">
            Tutup
        </button>
    </div>
</div>

<script>
    function copyDriverLink(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert('Tautan HP Kurir berhasil disalin ke clipboard:\n' + url);
        }).catch(() => {
            prompt('Salin link kurir berikut:', url);
        });
    }

    function openAssignDriverModal(orderId, poNumber, driverName, driverPhone, vehiclePlate, deliveryMethod) {
        document.getElementById('assign_order_id').value = orderId;
        document.getElementById('assign_po_number').innerText = poNumber;
        document.getElementById('assign_driver_name').value = driverName || '';
        document.getElementById('assign_driver_phone').value = driverPhone || '';
        document.getElementById('assign_vehicle_plate').value = vehiclePlate || '';
        document.getElementById('assign_delivery_method').value = deliveryMethod || 'vendor_fleet';
        document.getElementById('assignDriverModal').classList.remove('hidden');
    }

    function closeAssignDriverModal() {
        document.getElementById('assignDriverModal').classList.add('hidden');
    }

    function openQrModal(poNumber, url) {
        document.getElementById('qrPoTitle').innerText = 'PO: ' + poNumber;
        document.getElementById('qrImage').src = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent(url);
        document.getElementById('qrModal').classList.remove('hidden');
    }

    function closeQrModal() {
        document.getElementById('qrModal').classList.add('hidden');
    }
</script>

<?= $this->endSection() ?>
