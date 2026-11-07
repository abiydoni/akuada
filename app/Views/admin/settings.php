<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>
<?php
    $gen = $settings['general'] ?? [];
    $tax = $settings['tax'] ?? [];
    $pay = $settings['payment'] ?? [];
    $fee = $settings['fee'] ?? [];
    $inv = $settings['invoice'] ?? [];
    $des = $settings['design'] ?? [];

    $val = function(array $grp, string $key, string $default = '') {
        return $grp[$key]['setting_value'] ?? get_system_setting($key, $default);
    };
?>

<div class="space-y-6">
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold uppercase tracking-wider">Arsitektur Modular Terpusat</span>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Pengaturan Sistem, Invoice, Pajak & Kebijakan B2B</h1>
            <p class="text-xs text-slate-500 mt-0.5">Seluruh halaman marketplace, PO, BAST, e-Faktur, dan Invoice terhubung langsung dengan konfigurasi di bawah ini.</p>
        </div>
    </div>

    <!-- Interactive Tab Navigation Bar -->
    <div class="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-sm flex flex-wrap gap-1.5" role="tablist">
        <button type="button" onclick="switchSettingsTab('tab-general')" id="btn-tab-general"
                class="settings-tab-btn px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-purple-600 text-white shadow-sm">
            <span class="material-symbols-outlined text-[18px]">domain</span>
            <span>1. Identitas & Kontak</span>
        </button>
        <button type="button" onclick="switchSettingsTab('tab-tax')" id="btn-tab-tax"
                class="settings-tab-btn px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 text-slate-600 hover:bg-slate-100">
            <span class="material-symbols-outlined text-[18px]">receipt</span>
            <span>2. Pajak & e-Faktur</span>
        </button>
        <button type="button" onclick="switchSettingsTab('tab-payment')" id="btn-tab-payment"
                class="settings-tab-btn px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 text-slate-600 hover:bg-slate-100">
            <span class="material-symbols-outlined text-[18px]">event_repeat</span>
            <span>3. Kredit TOP & Logistik</span>
        </button>
        <button type="button" onclick="switchSettingsTab('tab-vendor')" id="btn-tab-vendor"
                class="settings-tab-btn px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 text-slate-600 hover:bg-slate-100">
            <span class="material-symbols-outlined text-[18px]">monetization_on</span>
            <span>4. Fee Vendor & Cashback</span>
        </button>
        <button type="button" onclick="switchSettingsTab('tab-invoice')" id="btn-tab-invoice"
                class="settings-tab-btn px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 text-slate-600 hover:bg-slate-100">
            <span class="material-symbols-outlined text-[18px]">description</span>
            <span>5. Invoice, Bank & TTD</span>
        </button>
    </div>

    <form action="<?= base_url('admin/settings/update') ?>" method="POST" class="space-y-6">
        <input type="hidden" name="active_tab" id="active_tab_input" value="tab-general">

        <!-- =========================================================
             TAB 1: IDENTITAS & KONTAK PLATFORM
             ========================================================= -->
        <div id="panel-tab-general" class="settings-tab-panel bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-5 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-600">domain</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">1. Identitas Aplikasi, Hero Banner & Layanan Pelanggan B2B</h2>
                        <p class="text-[11px] text-slate-500">Data ini ditampilkan pada navbar, beranda marketplace, halaman informasi, dan footer.</p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold">Profil Platform</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Aplikasi / Brand Utama:</label>
                    <input type="text" name="app_name" value="<?= esc($val($gen, 'app_name', 'AkuAda')) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold text-purple-900">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Sub-brand Header:</label>
                    <input type="text" name="app_subtitle" value="<?= esc($val($gen, 'app_subtitle', 'B2B E-Procurement')) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Tagline Resmi Platform (Hero & Meta):</label>
                    <input type="text" name="app_tagline" value="<?= esc($val($gen, 'app_tagline', 'Solusi Terlengkap Pengadaan Operasional Perusahaan Anda')) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Deskripsi Singkat Platform (Footer):</label>
                    <textarea name="app_description" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600"><?= esc($val($gen, 'app_description', 'AkuAda hadir memastikan setiap kebutuhan operasional perusahaan Anda terpenuhi dengan tepat, cepat, dan transparan. E-Procurement B2B terpercaya di Indonesia dengan fleksibilitas TOP, harga bertingkat grosir korporat, dan kelengkapan faktur pajak resmi.')) ?></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Layanan Pengadaan B2B:</label>
                    <input type="email" name="contact_email" value="<?= esc($val($gen, 'contact_email', 'b2b@akuada.co.id')) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Call Center Pengadaan Korporat:</label>
                    <input type="text" name="contact_phone" value="<?= esc($val($gen, 'contact_phone', '(021) 5088-9900')) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">WhatsApp Dedicated Account:</label>
                    <input type="text" name="contact_whatsapp" value="<?= esc($val($gen, 'contact_whatsapp', '+62 811-9988-2200')) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Jam Operasional Layanan B2B:</label>
                    <input type="text" name="contact_hours" value="<?= esc($val($gen, 'contact_hours', 'Senin - Jumat, 08:30 - 17:30 WIB')) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Password Default Akun Baru:</label>
                    <input type="text" name="default_account_password" value="<?= esc($val($gen, 'default_account_password', 'admin123')) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono font-bold text-purple-800">
                </div>
            </div>
        </div>

        <!-- =========================================================
             TAB 2: PAJAK & INTEGRASI E-FAKTUR DJP
             ========================================================= -->
        <div id="panel-tab-tax" class="settings-tab-panel hidden bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-5 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-blue-600">receipt</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">2. Tarif Pajak & Penomoran e-Faktur Resmi DJP</h2>
                        <p class="text-[11px] text-slate-500">Mengubah tarif PPN di sini otomatis menghitung ulang seluruh invoice, PO, keranjang, dan produk.</p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-bold">Otomatisasi Fiskal</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/40 space-y-1.5">
                    <label class="block font-bold text-blue-950">Tarif Standar PPN (%):</label>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.1" min="0" max="100" name="ppn_rate" value="<?= esc($val($tax, 'ppn_rate', '11')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-blue-300 bg-white focus:border-blue-600 font-black text-base text-blue-900 font-mono">
                        <span class="text-blue-700 font-bold text-sm">%</span>
                    </div>
                    <p class="text-[10px] text-blue-800">Otomatis diterapkan ke semua Invoice Buyer, Faktur Vendor, PO, dan Katalog.</p>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1.5">
                    <label class="block font-bold text-slate-700">Tarif PPh Pasal 22 (%):</label>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.1" min="0" max="100" name="pph_22_rate" value="<?= esc($val($tax, 'pph_22_rate', '1.5')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white focus:border-blue-600 font-bold text-base text-slate-900 font-mono">
                        <span class="text-slate-500 font-bold text-sm">%</span>
                    </div>
                    <p class="text-[10px] text-slate-500">Berlaku untuk pengadaan instansi pemerintah, BUMN, dan badan usaha tertentu.</p>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1.5">
                    <label class="block font-bold text-slate-700">Kode Awalan Nomor Seri e-Faktur (NSFP):</label>
                    <input type="text" name="tax_nsfp_prefix" value="<?= esc($val($tax, 'tax_nsfp_prefix', '010.002-26.')) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white focus:border-blue-600 font-mono font-bold text-slate-900">
                    <p class="text-[10px] text-slate-500">Awalan standar penomoran otomatis faktur pajak keluaran DJP.</p>
                </div>
            </div>
        </div>

        <!-- =========================================================
             TAB 3: KEBIJAKAN KREDIT TEMPO (TOP) & BIAYA LOGISTIK
             ========================================================= -->
        <div id="panel-tab-payment" class="settings-tab-panel hidden bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-5 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-600">event_repeat</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">3. Kebijakan Kredit Tempo (Term of Payment / TOP) & Biaya Logistik</h2>
                        <p class="text-[11px] text-slate-500">Mengatur opsi tenor pembayaran saat checkout, plafon kredit perusahaan, dan tarif pengiriman.</p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold">TOP & Pengiriman</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl border-2 border-purple-400 bg-purple-50/70 space-y-1.5 md:col-span-3">
                    <div class="flex items-center justify-between">
                        <label class="block font-black text-purple-950 text-sm flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-purple-700 text-lg">receipt_long</span>
                            Batas Maksimal Nota Belum Terbayar per Bulan (Kredit TOP):
                        </label>
                        <span class="px-2.5 py-0.5 rounded-full bg-purple-200 text-purple-900 text-[10px] font-extrabold">Normal: 3 Nota / Bulan</span>
                    </div>
                    <div class="flex items-center gap-2 max-w-xs">
                        <input type="number" min="1" max="100" name="max_unpaid_notes_limit" 
                               value="<?= esc($val($pay, 'max_unpaid_notes_limit', '3')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-purple-300 bg-white font-mono font-black text-purple-950 text-base">
                        <span class="font-bold text-purple-900 text-xs whitespace-nowrap">Nota / PO</span>
                    </div>
                    <p class="text-[10px] text-purple-900 leading-relaxed">
                        Fasilitas transaksi kredit tempo <strong>diatur berdasarkan jumlah akumulasi nota yang belum lunas dalam satu bulan berjalan</strong> (bukan nominal rupiah plafon). Jika buyer mencapai batas nota belum terbayar ini, fasilitas kredit tempo otomatis terkunci sampai dilakukan pembayaran.
                    </p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Opsi Hari TOP yang Diizinkan:</label>
                    <input type="text" name="allowed_top_terms" value="<?= esc($val($pay, 'allowed_top_terms', '14,30,60,90')) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono font-bold text-purple-900">
                    <p class="text-[10px] text-slate-400 mt-1">Pisahkan dengan koma (contoh: 14,30,60,90). Otomatis muncul pada opsi tenor checkout.</p>
                </div>

                <div>
                    <label class="block font-bold text-emerald-800 mb-1">Biaya Logistik & Pengiriman Default (Rp):</label>
                    <input type="number" name="default_shipping_amount" value="<?= esc($val($pay, 'default_shipping_amount', '0')) ?>" min="0" step="5000"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50/40 focus:border-emerald-600 font-mono font-bold text-emerald-900">
                    <p class="text-[10px] text-slate-400 mt-1">Biaya pengiriman standar pada PO & Invoice (isi 0 jika gratis ongkir).</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Cakupan Wilayah Pengiriman Korporat:</label>
                    <input type="text" name="free_shipping_region" value="<?= esc($val($pay, 'free_shipping_region', 'Jabodetabek & Nasional')) ?>"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    <p class="text-[10px] text-slate-400 mt-1">Ditampilkan pada informasi pengiriman di katalog & keranjang.</p>
                </div>
            </div>
        </div>

        <!-- =========================================================
             TAB 4: PENGATURAN FEE VENDOR & CASHBACK BUYER
             ========================================================= -->
        <div id="panel-tab-vendor" class="settings-tab-panel hidden bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-5 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">monetization_on</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">4. Pengaturan Fee Vendor, Cashback Buyer & Tenor Pencairan</h2>
                        <p class="text-[11px] text-slate-500">Konfigurasi komisi platform yang dipotong dari vendor, cashback saldo untuk buyer, dan jatuh tempo hutang ke vendor.</p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Komisi & Cashback</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 rounded-xl border-2 border-purple-300 bg-purple-50/60 space-y-1.5">
                    <label class="block font-bold text-purple-950 flex items-center justify-between">
                        <span>Cashback Saldo Buyer (%):</span>
                        <span class="px-2 py-0.5 rounded bg-purple-200 text-purple-900 text-[10px] font-extrabold">Default 1%</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.1" min="0" max="100" name="buyer_cashback_percent"
                               value="<?= esc($val($fee, 'buyer_cashback_percent', '1.0')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-purple-300 bg-white font-bold text-purple-950 font-mono">
                        <span class="text-purple-700 font-bold">%</span>
                    </div>
                    <p class="text-[10px] text-purple-800 leading-relaxed">Persentase reward cashback dari nilai transaksi PO yang lunas. Masuk ke dompet saldo buyer korporat.</p>
                </div>

                <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/40 space-y-1.5">
                    <label class="block font-bold text-emerald-950 flex items-center justify-between">
                        <span>Tarif Fee Default Platform (%):</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-200 text-emerald-900 text-[10px] font-extrabold">Default 2.5%</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.1" min="0" max="100" name="vendor_fee_default_percent"
                               value="<?= esc($val($fee, 'vendor_fee_default_percent', '2.5')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 bg-white font-bold text-slate-900 font-mono">
                        <span class="text-emerald-700 font-bold">%</span>
                    </div>
                    <p class="text-[10px] text-emerald-800 leading-relaxed">Komisi standar platform AkuAda yang dipotong dari subtotal penjualan vendor jika belum diatur khusus per mitra.</p>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1.5">
                    <label class="block font-bold text-slate-700">Tenor Jatuh Tempo Pencairan Vendor (Hari):</label>
                    <div class="flex items-center gap-2">
                        <input type="number" name="vendor_payout_days" min="1" max="90"
                               value="<?= esc($val($fee, 'vendor_payout_days', '7')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white font-mono font-bold text-slate-900">
                        <span class="text-slate-500 font-bold text-xs">Hari</span>
                    </div>
                    <p class="text-[10px] text-slate-400">Jangka waktu standar pelunasan dana ke rekening vendor terhitung sejak pesanan selesai (BAST).</p>
                </div>
            </div>
        </div>

        <!-- =========================================================
             TAB 5: PENGATURAN INVOICE, REKENING PEMBAYARAN & TTD
             ========================================================= -->
        <div id="panel-tab-invoice" class="settings-tab-panel hidden bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-6 text-xs">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-600">description</span>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">5. Pengaturan Invoice AkuAda, Rekening Pembayaran & Pejabat Penandatangan</h2>
                        <p class="text-[11px] text-slate-500">Digunakan pada Invoice Penjualan Buyer, Faktur Settlement Vendor, Dokumen PO/BAST, dan Modal Pembayaran Hutang.</p>
                    </div>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold">Dokumen Resmi</span>
            </div>

            <!-- 5A. Rekening Pembayaran -->
            <div class="space-y-3">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-blue-600">account_balance</span>
                    <span>A. Rekening Bank Resmi & Instruksi Pembayaran AkuAda</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Bank 1 -->
                    <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/40 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-blue-900">Rekening Utama #1</span>
                            <span class="px-2 py-0.5 rounded bg-blue-200 text-blue-900 text-[10px] font-bold">Escrow 1</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <label class="block font-bold text-slate-700 mb-1">Nama Bank:</label>
                                <input type="text" name="inv_bank1_name" value="<?= esc($val($inv, 'inv_bank1_name', 'Bank Mandiri (Escrow)')) ?>"
                                       class="w-full px-3 py-2 rounded-lg border border-blue-300 bg-white font-bold text-slate-900">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Valuta:</label>
                                <input type="text" name="inv_bank1_currency" value="<?= esc($val($inv, 'inv_bank1_currency', 'IDR')) ?>"
                                       class="w-full px-2 py-2 rounded-lg border border-blue-300 bg-white font-mono font-bold text-center">
                            </div>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Rekening:</label>
                            <input type="text" name="inv_bank1_number" value="<?= esc($val($inv, 'inv_bank1_number', '122-00-1122334-5')) ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-blue-300 bg-white font-mono font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Atas Nama (a/n):</label>
                            <input type="text" name="inv_bank1_holder" value="<?= esc($val($inv, 'inv_bank1_holder', 'AkuAda')) ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-blue-300 bg-white font-semibold text-slate-800">
                        </div>
                    </div>

                    <!-- Bank 2 -->
                    <div class="p-4 rounded-xl border border-purple-200 bg-purple-50/40 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-purple-900">Rekening Kedua #2</span>
                            <span class="px-2 py-0.5 rounded bg-purple-200 text-purple-900 text-[10px] font-bold">Escrow 2</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="col-span-2">
                                <label class="block font-bold text-slate-700 mb-1">Nama Bank:</label>
                                <input type="text" name="inv_bank2_name" value="<?= esc($val($inv, 'inv_bank2_name', 'Bank BCA (Escrow)')) ?>"
                                       class="w-full px-3 py-2 rounded-lg border border-purple-300 bg-white font-bold text-slate-900">
                            </div>
                            <div>
                                <label class="block font-bold text-slate-700 mb-1">Valuta:</label>
                                <input type="text" name="inv_bank2_currency" value="<?= esc($val($inv, 'inv_bank2_currency', 'IDR')) ?>"
                                       class="w-full px-2 py-2 rounded-lg border border-purple-300 bg-white font-mono font-bold text-center">
                            </div>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Rekening:</label>
                            <input type="text" name="inv_bank2_number" value="<?= esc($val($inv, 'inv_bank2_number', '535-088-9900')) ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-purple-300 bg-white font-mono font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Atas Nama (a/n):</label>
                            <input type="text" name="inv_bank2_holder" value="<?= esc($val($inv, 'inv_bank2_holder', 'AkuAda')) ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-purple-300 bg-white font-semibold text-slate-800">
                        </div>
                    </div>

                    <!-- Bank 3 / Virtual Account -->
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/70 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-800">Rekening Ketiga #3 (VA)</span>
                            <span class="px-2 py-0.5 rounded bg-slate-200 text-slate-800 text-[10px] font-bold">Opsi Bayar</span>
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nama Bank / Virtual Account:</label>
                            <input type="text" name="inv_bank3_name" value="<?= esc($val($inv, 'inv_bank3_name', 'Bank BNI Corporate VA')) ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Nomor Rekening / VA:</label>
                            <input type="text" name="inv_bank3_number" value="<?= esc($val($inv, 'inv_bank3_number', '8809-1122-3344')) ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white font-mono font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">Atas Nama (a/n):</label>
                            <input type="text" name="inv_bank3_holder" value="<?= esc($val($inv, 'inv_bank3_holder', 'AkuAda')) ?>"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-300 bg-white font-semibold text-slate-800">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-1">
                    <div class="md:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Catatan Instruksi Pembayaran Invoice:</label>
                        <input type="text" name="inv_payment_note" value="<?= esc($val($inv, 'inv_payment_note', 'Pembayaran dianggap sah apabila ditransfer ke rekening resmi di atas dengan mencantumkan Nomor Invoice pada kolom berita.')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Konfirmasi Bukti Transfer:</label>
                        <input type="email" name="inv_billing_email" value="<?= esc($val($inv, 'inv_billing_email', 'billing@akuada.id')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                    </div>
                </div>
            </div>

            <!-- 5B. Pejabat Penandatangan & Stempel -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-purple-600">draw</span>
                    <span>B. Pejabat Penandatangan, Stempel Digital & Jabatan Dokumen PO/BAST</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Salam Penutup Invoice:</label>
                        <input type="text" name="inv_sign_greeting" value="<?= esc($val($inv, 'inv_sign_greeting', 'Hormat Kami,')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Pejabat Penandatangan:</label>
                        <input type="text" name="inv_sign_name" value="<?= esc($val($inv, 'inv_sign_name', 'Dra. Maya Anggraini, Ak., CA.')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jabatan Penandatangan:</label>
                        <input type="text" name="inv_sign_title" value="<?= esc($val($inv, 'inv_sign_title', 'Finance & Treasury Director')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Divisi Penerbit Faktur:</label>
                        <input type="text" name="inv_sign_division" value="<?= esc($val($inv, 'inv_sign_division', 'Finance & Corporate Accounting Division')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Teks Stempel Digital Utama:</label>
                        <input type="text" name="inv_seal_text" value="<?= esc($val($inv, 'inv_seal_text', 'AKUADA')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold uppercase">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Sub-teks Stempel Digital:</label>
                        <input type="text" name="inv_seal_subtext" value="<?= esc($val($inv, 'inv_seal_subtext', 'OFFICIAL SEAL')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono uppercase">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jabatan TTD Vendor di PO/BAST:</label>
                        <input type="text" name="inv_vendor_default_title" value="<?= esc($val($inv, 'inv_vendor_default_title', 'Head of Corporate Sales')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jabatan TTD Buyer di PO/BAST:</label>
                        <input type="text" name="inv_buyer_default_title" value="<?= esc($val($inv, 'inv_buyer_default_title', 'Senior Procurement Manager')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>
                </div>
            </div>

            <!-- 5C. Identitas Kop Surat, NPWP, NIB & Catatan Kaki -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px] text-pink-600">verified_user</span>
                    <span>C. Identitas Kop Surat, Alamat Kantor, NPWP, NIB & Catatan Kaki Legal</span>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Entitas Penerbit (Kop & TTD):</label>
                        <input type="text" name="inv_issuer_name" value="<?= esc($val($inv, 'inv_issuer_name', 'AKUADA')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold text-slate-900">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Sub-judul Kop Surat:</label>
                        <input type="text" name="inv_issuer_subtitle" value="<?= esc($val($inv, 'inv_issuer_subtitle', 'Marketplace B2B & Solusi Pengadaan Terpadu')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Hotline Billing (Kop Invoice):</label>
                        <input type="text" name="inv_billing_phone" value="<?= esc($val($inv, 'inv_billing_phone', '(021) 5088-9900')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Alamat Kantor Baris 1:</label>
                        <input type="text" name="inv_issuer_address_1" value="<?= esc($val($inv, 'inv_issuer_address_1', 'Sahid Sudirman Center Lt. 28, Jl. Jend. Sudirman Kav. 86')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Alamat Kantor Baris 2:</label>
                        <input type="text" name="inv_issuer_address_2" value="<?= esc($val($inv, 'inv_issuer_address_2', 'Karet Tengsin, Tanah Abang, Jakarta Pusat 10220')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">NPWP AkuAda:</label>
                            <input type="text" name="inv_issuer_npwp" value="<?= esc($val($inv, 'inv_issuer_npwp', '01.999.888.7-011.000')) ?>"
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-700 mb-1">NIB AkuAda:</label>
                            <input type="text" name="inv_issuer_nib" value="<?= esc($val($inv, 'inv_issuer_nib', '9120008899112')) ?>"
                                   class="w-full px-3 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                        </div>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block font-bold text-slate-700 mb-1">Catatan Kaki Legal (Footer Invoice):</label>
                        <input type="text" name="inv_footer_note" value="<?= esc($val($inv, 'inv_footer_note', 'Dokumen ini merupakan Faktur Penjualan Sah yang diterbitkan oleh sistem elektronik AkuAda sesuai ketentuan perpajakan dan transaksi elektronik Republik Indonesia.')) ?>"
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sticky / Bottom Save Action Bar -->
        <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-sm flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-xs text-slate-500">
                <span class="material-symbols-outlined text-purple-600 text-[18px]">sync</span>
                <span>Perubahan pada tab mana pun akan langsung diterapkan ke seluruh halaman & dokumen saat disimpan.</span>
            </div>
            <button type="submit" class="btn-akuada-primary px-8 py-3 text-xs shadow-lg">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Simpan Seluruh Pengaturan Sistem</span>
            </button>
        </div>

    </form>
</div>

<script>
function switchSettingsTab(tabId) {
    const panels = document.querySelectorAll('.settings-tab-panel');
    const buttons = document.querySelectorAll('.settings-tab-btn');

    panels.forEach(p => {
        if (p.id === 'panel-' + tabId) {
            p.classList.remove('hidden');
        } else {
            p.classList.add('hidden');
        }
    });

    buttons.forEach(b => {
        if (b.id === 'btn-' + tabId) {
            b.className = 'settings-tab-btn px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-purple-600 text-white shadow-sm';
        } else {
            b.className = 'settings-tab-btn px-3.5 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 text-slate-600 hover:bg-slate-100';
        }
    });

    const hiddenInput = document.getElementById('active_tab_input');
    if (hiddenInput) hiddenInput.value = tabId;
    if (history.replaceState) {
        history.replaceState(null, null, '#' + tabId);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const flashTab = '<?= esc(session()->getFlashdata('active_tab') ?? '') ?>';
    let hash = flashTab || window.location.hash.replace('#', '');
    if (hash === 'invoice-settings') hash = 'tab-invoice';
    const validTabs = ['tab-general', 'tab-tax', 'tab-payment', 'tab-vendor', 'tab-invoice'];
    if (validTabs.includes(hash)) {
        switchSettingsTab(hash);
    }
});
</script>

<?= $this->endSection() ?>
