<?= $this->extend('layouts/main') ?>

<?= $this->section('subcontent') ?>
<?php
$allowedTopTerms    = get_allowed_top_terms();
$minTopDays         = get_min_top_days();
$maxTopDays         = get_max_top_days();
$shipAmt            = (float)($shippingAmount ?? get_system_setting('default_shipping_amount', 0));
$freeRegion         = get_system_setting('free_shipping_region', 'Corporate Jabodetabek');

$creditLimit        = (float)($buyerCompany['credit_limit_top'] ?? 0);
$creditUsed         = (float)($buyerCompany['credit_used_top'] ?? 0);
$availableCredit    = max(0, $creditLimit - $creditUsed);
$isSufficientCredit = !empty($isRegistered) && ($grandTotal <= $availableCredit);
$creditShortfall    = max(0, $grandTotal - $availableCredit);
?>

<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
            <span class="material-symbols-outlined text-purple-600 text-3xl">verified</span>
            Penerbitan Purchase Order (PO) & Checkout B2B
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Lengkapi data legalitas perusahaan, nomor PO internal, metode pembayaran tempo (TOP <?= $minTopDays ?>-<?= $maxTopDays ?> hari), dan instruksi serah terima barang (BAST).
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <form action="<?= base_url('checkout/process') ?>" method="POST" id="checkoutForm">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Left Form: Company Details & Payment Terms (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- 1. Corporate Identity Verification -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-purple-600">domain</span>
                            <h2 class="text-sm font-bold text-slate-800">1. Identitas Perusahaan Pembeli (Corporate Buyer)</h2>
                        </div>
                        <?php if (!empty($isRegistered)): ?>
                            <span class="px-2.5 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">verified</span> Terdaftar & Fasilitas Kredit Aktif
                            </span>
                        <?php else: ?>
                            <span class="px-2.5 py-0.5 rounded bg-amber-100 text-amber-800 font-bold text-[10px] flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">lock</span> Belum Terdaftar Akun Kredit AkuAda
                            </span>
                        <?php endif; ?>
                    </div>

                    <?php if (empty($isRegistered)): ?>
                        <!-- Alert for Unregistered Buyer -->
                        <div class="p-4 rounded-xl bg-amber-50/80 border border-amber-200 text-xs text-amber-900 flex items-start gap-3">
                            <span class="material-symbols-outlined text-amber-600 text-[22px] mt-0.5">info</span>
                            <div class="space-y-1">
                                <strong class="font-bold text-amber-950 block">Perusahaan Anda Belum Terdaftar Fasilitas Kredit:</strong>
                                <p class="text-amber-800 leading-relaxed">
                                    Sesuai SOP AkuAda, pembayaran PO dengan <strong>Fasilitas Kredit Tempo (TOP <?= $minTopDays ?>-<?= $maxTopDays ?> Hari)</strong> hanya dapat digunakan oleh perusahaan yang telah diverifikasi dan dibuatkan akun resmi oleh AkuAda. Untuk transaksi ini, Anda dapat melanjutkan pemesanan dengan pembayaran langsung (<strong>Bank Virtual Account</strong>).
                                </p>
                            </div>
                        </div>

                        <!-- Guest Company Form Inputs -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div class="md:col-span-2">
                                <label class="block font-bold text-slate-700 mb-1">Nama Perusahaan Pembeli: <span class="text-rose-500">*</span></label>
                                <input type="text" name="guest_company_name" required placeholder="Contoh: PT Multi Sinergi Utama" 
                                       class="w-full font-bold text-slate-900 rounded-xl border border-slate-300 focus:border-purple-600 px-3.5 py-2.5">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Bentuk Legalitas: <span class="text-rose-500">*</span></label>
                                <select name="guest_legal_type" class="w-full bg-white font-bold text-slate-800 rounded-xl border border-slate-300 focus:border-purple-600 px-3.5 py-2.5">
                                    <option value="PT">PT (Perseroan Terbatas)</option>
                                    <option value="CV">CV (Commanditaire Vennootschap)</option>
                                    <option value="PT Tbk">PT Tbk (Perusahaan Terbuka)</option>
                                    <option value="BUMN">BUMN / BUMD</option>
                                    <option value="Yayasan">Yayasan / Lembaga</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">NPWP Perusahaan (16 Digit):</label>
                                <input type="text" name="guest_npwp" placeholder="01.234.567.8-901.000" 
                                       class="w-full font-mono text-slate-800 rounded-xl border border-slate-300 focus:border-purple-600 px-3.5 py-2.5">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nama Lengkap PIC Procurement: <span class="text-rose-500">*</span></label>
                                <input type="text" name="guest_pic_name" required placeholder="Nama PIC Purchasing" 
                                       class="w-full text-slate-800 rounded-xl border border-slate-300 focus:border-purple-600 px-3.5 py-2.5">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / HP PIC: <span class="text-rose-500">*</span></label>
                                <input type="text" name="guest_phone" required placeholder="081234567890" 
                                       class="w-full text-slate-800 rounded-xl border border-slate-300 focus:border-purple-600 px-3.5 py-2.5">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block font-bold text-slate-700 mb-1">Email Resmi Kantor (Untuk Pengiriman Invoice): <span class="text-rose-500">*</span></label>
                                <input type="email" name="guest_email" required placeholder="purchasing@perusahaan.co.id" 
                                       class="w-full text-slate-800 rounded-xl border border-slate-300 focus:border-purple-600 px-3.5 py-2.5">
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Registered Company Profile (Read-Only) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                            <div>
                                <label class="block text-slate-500 mb-1">Nama Perusahaan Resmi:</label>
                                <input type="text" readonly value="<?= esc($buyerCompany['company_name'] ?? '') ?>" 
                                       class="w-full bg-slate-50 font-bold text-slate-800 rounded-xl border border-slate-200 px-3.5 py-2.5">
                            </div>
                            <div>
                                <label class="block text-slate-500 mb-1">Bentuk Legalitas:</label>
                                <input type="text" readonly value="<?= esc($buyerCompany['legal_type'] ?? 'PT') ?>" 
                                       class="w-full bg-slate-50 font-bold text-slate-800 rounded-xl border border-slate-200 px-3.5 py-2.5">
                            </div>
                            <div>
                                <label class="block text-slate-500 mb-1">NPWP Perusahaan (16 Digit):</label>
                                <input type="text" readonly value="<?= esc($buyerCompany['npwp'] ?? '-') ?>" 
                                       class="w-full bg-slate-50 font-bold text-slate-800 rounded-xl border border-slate-200 px-3.5 py-2.5">
                            </div>
                            <div>
                                <label class="block text-slate-500 mb-1">Nomor Induk Berusaha (NIB):</label>
                                <input type="text" readonly value="<?= esc($buyerCompany['nib'] ?? '-') ?>" 
                                       class="w-full bg-slate-50 font-bold text-slate-800 rounded-xl border border-slate-200 px-3.5 py-2.5">
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Internal PO Number -->
                    <div class="pt-2">
                        <label for="po_number" class="block text-xs font-bold text-slate-800 mb-1">
                            Nomor Purchase Order (PO) Internal Perusahaan Anda: <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="po_number" name="po_number" required placeholder="Contoh: PO/CORP/<?= date('Y/m') ?>/0091" 
                               value="PO/CORP/<?= date('Ym') ?>/<?= rand(100, 999) ?>"
                               class="w-full font-mono font-bold text-sm text-purple-900 bg-purple-50/50 rounded-xl border border-purple-300 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 px-4 py-2.5">
                        <p class="text-[11px] text-slate-400 mt-1">Nomor PO ini akan tercetak otomatis pada e-Invoice, Surat Jalan, dan BAST.</p>
                    </div>
                </div>

                <!-- 2. B2B Payment Methods (Dynamic TOP from Settings, Over-Limit Option, VA, PO) -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600">payments</span>
                            <h2 class="text-sm font-bold text-slate-800">2. Metode Pembayaran & Termin Kredit (TOP)</h2>
                        </div>
                        <?php if (!empty($isRegistered)): ?>
                            <div class="flex items-center gap-2 text-[11px]">
                                <span class="text-slate-500">Sisa Plafon Tersedia:</span>
                                <strong class="px-2 py-0.5 rounded font-mono <?= $isSufficientCredit ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                                    <?= format_rupiah($availableCredit) ?>
                                </strong>
                            </div>
                        <?php else: ?>
                            <span class="text-[11px] text-rose-600 font-bold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[15px]">block</span> Kredit Tidak Tersedia (Belum Terdaftar)
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Visual Indicator Box When Registered Buyer Has Insufficient Credit Limit -->
                    <?php if (!empty($isRegistered) && !$isSufficientCredit): ?>
                        <div class="p-4 rounded-2xl bg-rose-50/90 border-2 border-rose-300 text-xs space-y-3">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-rose-600 text-2xl mt-0.5">warning</span>
                                <div class="flex-1 space-y-1">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <strong class="text-rose-950 font-extrabold text-sm">Plafon Kredit TOP Reguler Tidak Mencukupi!</strong>
                                        <span class="px-2.5 py-0.5 rounded-full bg-rose-600 text-white font-mono font-bold text-[11px]">
                                            Kurang <?= format_rupiah($creditShortfall) ?>
                                        </span>
                                    </div>
                                    <p class="text-rose-800 leading-relaxed">
                                        Nilai tagihan PO ini (<strong><?= format_rupiah($grandTotal) ?></strong>) melebihi sisa plafon kredit perusahaan Anda (<strong><?= format_rupiah($availableCredit) ?></strong> dari total limit <?= format_rupiah($creditLimit) ?>).
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 pt-2 border-t border-rose-200/80 text-[11px]">
                                <div class="p-2.5 rounded-xl bg-white border border-rose-200">
                                    <span class="text-slate-400 block">Sisa Plafon Saat Ini:</span>
                                    <strong class="font-mono text-slate-800"><?= format_rupiah($availableCredit) ?></strong>
                                </div>
                                <div class="p-2.5 rounded-xl bg-white border border-rose-200">
                                    <span class="text-slate-400 block">Total Tagihan PO Ini:</span>
                                    <strong class="font-mono text-purple-900"><?= format_rupiah($grandTotal) ?></strong>
                                </div>
                                <div class="p-2.5 rounded-xl bg-rose-100/80 border border-rose-300">
                                    <span class="text-rose-700 font-semibold block">Selisih Kekurangan Limit:</span>
                                    <strong class="font-mono text-rose-900"><?= format_rupiah($creditShortfall) ?></strong>
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                                <span class="text-[11px] text-rose-800 font-medium">
                                    Pilih <strong>"Tetap Open PO (Pengajuan Over-Limit TOP)"</strong> di bawah, atau lunasi tagihan lama / ajukan kenaikan limit:
                                </span>
                                <div class="flex items-center gap-2">
                                    <a href="<?= base_url('customer/payables') ?>" class="px-3 py-1.5 rounded-lg bg-white hover:bg-rose-100 text-rose-800 border border-rose-300 font-bold text-[11px] flex items-center gap-1 transition">
                                        <span class="material-symbols-outlined text-[14px]">payments</span> Lunasi Tagihan Lama
                                    </a>
                                    <a href="<?= base_url('customer/credit-limit') ?>" class="px-3 py-1.5 rounded-lg bg-rose-700 hover:bg-rose-800 text-white font-bold text-[11px] flex items-center gap-1 transition">
                                        <span class="material-symbols-outlined text-[14px]">trending_up</span> Naikkan Plafon
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="space-y-3">
                        <?php
                        $defaultTopChecked = in_array(30, $allowedTopTerms) ? 30 : ($allowedTopTerms[0] ?? 30);
                        foreach ($allowedTopTerms as $topDay):
                            $isDefTop = ($topDay === $defaultTopChecked);
                        ?>
                            <?php if (!empty($isRegistered) && $isSufficientCredit): ?>
                                <label class="flex items-start gap-3 p-4 rounded-xl <?= $isDefTop ? 'border-2 border-purple-500 bg-purple-50/40' : 'border border-slate-200 hover:border-purple-300' ?> cursor-pointer transition hover:bg-purple-50/70">
                                    <input type="radio" name="payment_method" value="top_<?= $topDay ?>" <?= $isDefTop ? 'checked' : '' ?> onchange="toggleOverlimitBox()" class="mt-1 text-purple-600 focus:ring-purple-500">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-900 text-xs">Term of Payment <?= $topDay ?> Hari (TOP <?= $topDay ?>)<?= $isDefTop ? ' - Direkomendasikan' : '' ?></span>
                                            <span class="badge-top text-[10px]">Tempo <?= $topDay ?> Hari • Plafon Cukup</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Pembayaran dilakukan maksimal <?= $topDay ?> hari kalender setelah Berita Acara Serah Terima (BAST) ditandatangani.</p>
                                    </div>
                                </label>
                            <?php elseif (!empty($isRegistered) && !$isSufficientCredit): ?>
                                <!-- Visual Locked Indicator when Insufficient Credit -->
                                <div class="flex items-start gap-3 p-3.5 rounded-xl border border-rose-200 bg-rose-50/40 opacity-75 cursor-not-allowed">
                                    <input type="radio" disabled class="mt-1 text-rose-300 cursor-not-allowed">
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center justify-between gap-1">
                                            <span class="font-bold text-slate-500 text-xs line-through">Term of Payment <?= $topDay ?> Hari (TOP <?= $topDay ?> Reguler)</span>
                                            <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 border border-rose-300 text-[10px] font-bold">
                                                ⚠️ Plafon Tidak Cukup — Kurang <?= format_rupiah($creditShortfall) ?>
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-rose-600/80 mt-0.5">Gunakan opsi <strong>Tetap Open PO (Pengajuan Over-Limit)</strong> di bawah jika ingin tetap menerbitkan PO dengan kredit tempo.</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50/70 opacity-60 cursor-not-allowed">
                                    <input type="radio" disabled class="mt-1 text-slate-300 cursor-not-allowed">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-500 text-xs line-through">Term of Payment <?= $topDay ?> Hari (TOP <?= $topDay ?>)</span>
                                            <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-600 text-[10px] font-bold">🔒 Khusus Akun Terdaftar</span>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Fasilitas kredit tempo hanya untuk akun perusahaan yang telah disetujui AkuAda.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <!-- Corporate PO On-Account -->
                        <?php if (!empty($isRegistered) && $isSufficientCredit): ?>
                            <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-purple-300 cursor-pointer transition">
                                <input type="radio" name="payment_method" value="corporate_po" onchange="toggleOverlimitBox()" class="mt-1 text-purple-600 focus:ring-purple-500">
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-slate-900 text-xs">Corporate Purchase Order (PO) On-Account</span>
                                        <span class="px-2 py-0.5 rounded bg-pink-100 text-pink-700 text-[10px] font-bold">On-Account</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Penagihan faktur bulanan gabungan (Monthly Statement) langsung ke Finance Departemen.</p>
                                </div>
                            </label>
                        <?php elseif (!empty($isRegistered) && !$isSufficientCredit): ?>
                            <div class="flex items-start gap-3 p-3.5 rounded-xl border border-rose-200 bg-rose-50/40 opacity-75 cursor-not-allowed">
                                <input type="radio" disabled class="mt-1 text-rose-300 cursor-not-allowed">
                                <div class="flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-1">
                                        <span class="font-bold text-slate-500 text-xs line-through">Corporate Purchase Order (PO) On-Account</span>
                                        <span class="px-2 py-0.5 rounded bg-rose-100 text-rose-700 border border-rose-300 text-[10px] font-bold">
                                            ⚠️ Plafon Tidak Cukup — Kurang <?= format_rupiah($creditShortfall) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- SPECIAL OPTION WHEN OVER-LIMIT: Tetap Open PO dengan Pengajuan Over-Limit TOP -->
                        <?php if (!empty($isRegistered) && !$isSufficientCredit): ?>
                            <div class="rounded-2xl border-2 border-amber-500 bg-amber-50/50 p-4 space-y-3 shadow-sm">
                                <label class="flex items-start gap-3 cursor-pointer">
                                    <input type="radio" name="payment_method" value="overlimit_top" id="radioOverlimitTop" checked onchange="toggleOverlimitBox()" class="mt-1 text-amber-600 focus:ring-amber-500">
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <span class="font-extrabold text-amber-950 text-xs flex items-center gap-1.5">
                                                <span class="material-symbols-outlined text-[16px] text-amber-600">bolt</span>
                                                Tetap Open PO dengan Pengajuan Over-Limit TOP (Menunggu Approval Plafon Admin)
                                            </span>
                                            <span class="px-2.5 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-extrabold">
                                                Tetap Bisa Open PO
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-amber-900 mt-1 leading-relaxed">
                                            PO Anda <strong>tetap diterbitkan sekarang</strong> dan masuk ke antrean prioritas Superadmin AkuAda untuk persetujuan penambahan limit kredit sebesar <strong><?= format_rupiah($creditShortfall) ?></strong>.
                                        </p>
                                    </div>
                                </label>

                                <!-- Overlimit Tenor & Reason Inputs -->
                                <div id="overlimitDetailBox" class="pl-7 pt-2 border-t border-amber-200/80 grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                                    <div>
                                        <label class="block font-bold text-amber-950 mb-1 text-[11px]">Pilih Tenor TOP yang Diajukan:</label>
                                        <select name="overlimit_top_days" class="w-full px-3 py-2 rounded-xl border border-amber-300 bg-white font-bold text-slate-900">
                                            <?php foreach ($allowedTopTerms as $tDay): ?>
                                                <option value="<?= $tDay ?>" <?= $tDay === $defaultTopChecked ? 'selected' : '' ?>>TOP <?= $tDay ?> Hari Kalender</option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label class="block font-bold text-amber-950 mb-1 text-[11px]">Catatan / Alasan Pengajuan Over-Limit ke Admin:</label>
                                        <input type="text" name="overlimit_reason" value="Kebutuhan pengadaan operasional mendesak, mohon persetujuan ekstra plafon TOP."
                                               placeholder="Contoh: Proyek tender baru / pelunasan invoice sebelumnya sedang diproses finance..."
                                               class="w-full px-3 py-2 rounded-xl border border-amber-300 bg-white text-slate-800 font-medium">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Bank Virtual Account (Always available) -->
                        <label class="flex items-start gap-3 p-4 rounded-xl border-2 <?= empty($isRegistered) ? 'border-purple-500 bg-purple-50/40 shadow-sm' : 'border-slate-200 hover:border-purple-300' ?> cursor-pointer transition">
                            <input type="radio" name="payment_method" value="bank_va" <?= empty($isRegistered) ? 'checked' : '' ?> onchange="toggleOverlimitBox()" class="mt-1 text-purple-600 focus:ring-purple-500">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 text-xs">Corporate Virtual Account (<?= esc(get_system_setting('inv_bank1_name', 'Bank Mandiri')) ?>, <?= esc(get_system_setting('inv_bank2_name', 'Bank BCA')) ?>, <?= esc(get_system_setting('inv_bank3_name', 'Bank BNI')) ?>)</span>
                                    <span class="px-2.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">Tanpa Batas Plafon (Non-Kredit)</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Bayar langsung melalui corporate internet banking tanpa memotong plafon kredit TOP perusahaan Anda.</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- 3. Shipping & Delivery Instruction -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="material-symbols-outlined text-pink-600">local_shipping</span>
                        <h2 class="text-sm font-bold text-slate-800">3. Alamat Pengiriman Kantor / Gudang Logistik</h2>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Alamat Lengkap Pengiriman:</label>
                            <textarea name="shipping_address" rows="3" required class="w-full bg-slate-50 rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-purple-100 focus:border-purple-600"><?= esc($buyerCompany['address'] ?? get_system_setting('inv_issuer_address', 'Jakarta Selatan')) ?></textarea>
                        </div>

                        <div>
                            <label class="block text-slate-600 font-semibold mb-1">Instruksi Khusus untuk Vendor / Logistik Pengiriman:</label>
                            <textarea name="customer_notes" rows="2" placeholder="Contoh: Harap lampirkan BAST 3 rangkap bermeterai, kirim pada hari kerja jam 09.00 - 16.00 WIB." class="w-full bg-slate-50 rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-purple-100 focus:border-purple-600"></textarea>
                        </div>

                        <!-- Faktur Pajak Toggle -->
                        <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-200 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <input type="checkbox" id="tax_invoice_required" name="tax_invoice_required" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                                <label for="tax_invoice_required" class="font-bold text-blue-900 cursor-pointer">
                                    Terbitkan e-Faktur Pajak PPN <?= esc(format_tax_rate()) ?> Resmi DJP
                                </label>
                            </div>
                            <span class="text-[11px] text-blue-700 font-semibold">Tercatat ke NPWP Badan</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Order PO Summary & Submit (4 Cols) -->
            <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-purple-200 shadow-xl shadow-purple-900/5 space-y-5 sticky top-24">
                <h3 class="text-sm font-bold text-slate-900 pb-3 border-b border-slate-100">Rincian Dokumen PO</h3>

                <div class="space-y-3 text-xs max-h-60 overflow-y-auto pr-1">
                    <?php foreach ($cart as $item): ?>
                        <div class="flex justify-between items-start text-slate-700 pb-2 border-b border-slate-100">
                            <div>
                                <span class="font-bold block"><?= esc($item['name']) ?></span>
                                <span class="text-[11px] text-slate-400"><?= $item['qty'] ?> <?= esc($item['uom']) ?> × <?= format_rupiah($item['unit_price']) ?></span>
                            </div>
                            <span class="font-bold"><?= format_rupiah($item['subtotal']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="space-y-2 pt-2 border-t border-slate-200 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal Barang:</span>
                        <span class="font-bold text-slate-800"><?= format_rupiah($subtotal) ?></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>PPN <?= esc(format_tax_rate()) ?> (e-Faktur DJP):</span>
                        <span class="font-bold text-blue-700"><?= format_rupiah($ppnTotal) ?></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Biaya Kirim Logistik:</span>
                        <span class="font-bold text-emerald-600"><?= $shipAmt > 0 ? format_rupiah($shipAmt) : 'Rp 0 (' . esc($freeRegion) . ')' ?></span>
                    </div>
                    <div class="border-t border-slate-200 pt-3 flex justify-between items-baseline font-bold">
                        <span class="text-sm text-slate-900">Total Nilai Tagihan:</span>
                        <span class="text-xl text-purple-900 font-extrabold"><?= format_rupiah($grandTotal) ?></span>
                    </div>
                </div>

                <button type="submit" class="w-full btn-akuada-primary py-3.5 text-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">task_alt</span>
                    <span>Konfirmasi & Terbitkan PO Resmi</span>
                </button>

                <p class="text-[11px] text-slate-400 text-center leading-normal">
                    Dengan menerbitkan PO, Anda menyetujui syarat & ketentuan pengadaan B2B AkuAda serta verifikasi dokumen Berita Acara Serah Terima (BAST).
                </p>
            </div>

        </div>
    </form>
</div>

<script>
function toggleOverlimitBox() {
    const box = document.getElementById('overlimitDetailBox');
    const radio = document.getElementById('radioOverlimitTop');
    if (!box || !radio) return;
    if (radio.checked) {
        box.classList.remove('hidden');
    } else {
        box.classList.add('hidden');
    }
}
</script>

<?= $this->endSection() ?>
