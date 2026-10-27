<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold">Arsitektur Modular Terpusat</span>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Pengaturan Sistem, Fiskal & Design Tokens</h1>
            <p class="text-xs text-slate-500 mt-0.5">Konfigurasi sentral seragam yang mengatur seluruh modul marketplace, vendor, dan customer.</p>
        </div>
    </div>

    <form action="<?= base_url('admin/settings/update') ?>" method="POST" class="space-y-6">
        
        <!-- 1. Identitas Platform -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4 text-xs">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-purple-600">domain</span>
                <h2 class="text-sm font-bold text-slate-800">1. Identitas & Layanan Pelanggan AkuAda B2B</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Aplikasi / Brand:</label>
                    <input type="text" name="app_name" value="<?= esc($settings['general']['app_name']['setting_value'] ?? 'AkuAda') ?>" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 font-bold text-purple-900">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tagline Resmi:</label>
                    <input type="text" name="app_tagline" value="<?= esc($settings['general']['app_tagline']['setting_value'] ?? 'Solusi Terlengkap Pengadaan Operasional Perusahaan Anda') ?>" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Layanan Pengadaan B2B:</label>
                    <input type="email" name="contact_email" value="<?= esc($settings['general']['contact_email']['setting_value'] ?? 'b2b@akuada.co.id') ?>" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 font-mono">
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Call Center Pengadaan Korporat:</label>
                    <input type="text" name="contact_phone" value="<?= esc($settings['general']['contact_phone']['setting_value'] ?? '(021) 5088-9900') ?>" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100">
                </div>
            </div>
        </div>

        <!-- 2. Pengaturan Pajak & Fiskal (PPN 11%, PPh 22) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4 text-xs">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-blue-600">receipt</span>
                <h2 class="text-sm font-bold text-slate-800">2. Tarif Pajak & Integrasi e-Faktur Resmi</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tarif Standar PPN (%):</label>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.1" name="ppn_rate" value="<?= esc($settings['tax']['ppn_rate']['setting_value'] ?? '11') ?>" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 font-bold text-slate-900">
                        <span class="text-slate-500 font-bold">%</span>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Dihitung otomatis saat kalkulasi invoice dan penerbitan faktur pajak DJP.</p>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Tarif PPh Pasal 22 (%):</label>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.1" name="pph_22_rate" value="<?= esc($settings['tax']['pph_22_rate']['setting_value'] ?? '1.5') ?>" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-blue-600 focus:ring-2 focus:ring-blue-100 font-bold text-slate-900">
                        <span class="text-slate-500 font-bold">%</span>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">Berlaku untuk pengadaan instansi pemerintah, BUMN, dan badan usaha tertentu.</p>
                </div>
            </div>
        </div>

        <!-- 3. Pembayaran & Kebijakan TOP -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4 text-xs">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-purple-600">event_repeat</span>
                <h2 class="text-sm font-bold text-slate-800">3. Kebijakan Kredit Tempo (Term of Payment / TOP)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Opsi Hari TOP yang Diizinkan:</label>
                    <input type="text" name="allowed_top_terms" value="<?= esc($settings['payment']['allowed_top_terms']['setting_value'] ?? '14,30,60,90') ?>" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 font-mono">
                    <p class="text-[10px] text-slate-400 mt-1">Pisahkan dengan koma (contoh: 14,30,60,90).</p>
                </div>
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Plafon Kredit Default Buyer Baru (Rp):</label>
                    <input type="number" name="default_credit_limit" value="<?= esc($settings['payment']['default_credit_limit']['setting_value'] ?? '100000000') ?>" step="5000000"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 font-bold text-slate-900">
                    <p class="text-[10px] text-slate-400 mt-1">Limit awal saat perusahaan pembeli baru terdaftar.</p>
                </div>
            </div>
        </div>

        <!-- 4. Design Tokens & Brand Palettes (Ungu, Biru, Pink) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4 text-xs">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-pink-600">palette</span>
                <h2 class="text-sm font-bold text-slate-800">4. Design Tokens & Brand Color Palette (Ungu, Biru, Pink)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-3.5 rounded-xl border border-purple-200 bg-purple-50/50 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-purple-900">Warna Utama (Ungu):</label>
                        <div class="w-6 h-6 rounded-md shadow-sm border border-purple-300" style="background-color: <?= esc($settings['design']['primary_color']['setting_value'] ?? '#6366f1') ?>"></div>
                    </div>
                    <input type="text" name="primary_color" value="<?= esc($settings['design']['primary_color']['setting_value'] ?? '#6366f1') ?>" 
                           class="w-full px-3 py-2 rounded-lg border border-purple-300 font-mono text-center font-bold">
                    <span class="text-[10px] text-purple-700 block">Digunakan untuk primary CTA, header aksen, dan logo.</span>
                </div>

                <div class="p-3.5 rounded-xl border border-blue-200 bg-blue-50/50 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-blue-900">Warna Sekunder (Biru):</label>
                        <div class="w-6 h-6 rounded-md shadow-sm border border-blue-300" style="background-color: <?= esc($settings['design']['secondary_color']['setting_value'] ?? '#2563eb') ?>"></div>
                    </div>
                    <input type="text" name="secondary_color" value="<?= esc($settings['design']['secondary_color']['setting_value'] ?? '#2563eb') ?>" 
                           class="w-full px-3 py-2 rounded-lg border border-blue-300 font-mono text-center font-bold">
                    <span class="text-[10px] text-blue-700 block">Digunakan untuk verifikasi resmi, e-Faktur, dan portal vendor.</span>
                </div>

                <div class="p-3.5 rounded-xl border border-pink-200 bg-pink-50/50 space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="font-bold text-pink-900">Warna Aksen (Pink):</label>
                        <div class="w-6 h-6 rounded-md shadow-sm border border-pink-300" style="background-color: <?= esc($settings['design']['accent_color']['setting_value'] ?? '#ec4899') ?>"></div>
                    </div>
                    <input type="text" name="accent_color" value="<?= esc($settings['design']['accent_color']['setting_value'] ?? '#ec4899') ?>" 
                           class="w-full px-3 py-2 rounded-lg border border-pink-300 font-mono text-center font-bold">
                    <span class="text-[10px] text-pink-700 block">Digunakan untuk badge MOQ, notifikasi, dan tombol RFQ.</span>
                </div>
            </div>
        </div>

        <!-- 5. Pengaturan Fee Vendor & Komisi Platform Marketplace B2B -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4 text-xs">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-emerald-600">monetization_on</span>
                    <h2 class="text-sm font-bold text-slate-800">5. Pengaturan Fee Vendor & Komisi Platform Marketplace B2B</h2>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold">Otomatisasi Komisi Penjualan</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/40 space-y-1.5">
                    <label class="block font-bold text-emerald-950">Tarif Fee Default Platform (%):</label>
                    <div class="flex items-center gap-2">
                        <input type="number" step="0.1" min="0" max="100" name="vendor_fee_default_percent" 
                               value="<?= esc($settings['fee']['vendor_fee_default_percent']['setting_value'] ?? '2.5') ?>" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-emerald-300 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 font-bold text-slate-900 bg-white">
                        <span class="text-emerald-700 font-bold">%</span>
                    </div>
                    <p class="text-[10px] text-emerald-800">Dikenakan dari penjualan tiap barang yang belum memiliki fee khusus.</p>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1.5">
                    <label class="block font-bold text-slate-700">Dasar Pengenaan Komisi:</label>
                    <select name="vendor_fee_calc_basis" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-semibold">
                        <option value="dpp" <?= ($settings['fee']['vendor_fee_calc_basis']['setting_value'] ?? 'dpp') === 'dpp' ? 'selected' : '' ?>>DPP Subtotal (Sebelum PPN 11%) - Standar B2B</option>
                        <option value="gross" <?= ($settings['fee']['vendor_fee_calc_basis']['setting_value'] ?? '') === 'gross' ? 'selected' : '' ?>>Gross Nilai Tagihan (Inc. PPN 11%)</option>
                    </select>
                    <p class="text-[10px] text-slate-400">Direkomendasikan DPP agar vendor tidak terpotong pajak keluaran.</p>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1.5">
                    <label class="block font-bold text-slate-700">Nominal Minimum Fee (Rp):</label>
                    <input type="number" name="vendor_fee_min_amount" 
                           value="<?= esc($settings['fee']['vendor_fee_min_amount']['setting_value'] ?? '0') ?>" step="1000"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-bold text-slate-900">
                    <p class="text-[10px] text-slate-400">Biaya minimum per transaksi (isi 0 jika tanpa batas minimum).</p>
                </div>

                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/60 space-y-1.5">
                    <label class="block font-bold text-slate-700">Ketentuan Settlement Hak Vendor:</label>
                    <input type="text" name="vendor_fee_settlement_term" 
                           value="<?= esc($settings['fee']['vendor_fee_settlement_term']['setting_value'] ?? 'H+3 BAST') ?>" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-mono font-bold text-blue-900">
                    <p class="text-[10px] text-slate-400">Jadwal transfer pencairan saldo setelah dokumen BAST disahkan.</p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-purple-50/60 border border-purple-200 text-purple-950 flex items-start gap-3">
                <span class="material-symbols-outlined text-purple-700 text-[20px] flex-shrink-0 mt-0.5">info</span>
                <div class="space-y-0.5">
                    <strong class="font-bold text-xs text-purple-900">Skema Komisi Bertingkat & Khusus per Barang:</strong>
                    <p class="text-[11px] text-purple-800 leading-relaxed">
                        Vendor juga dapat menentukan persentase fee khusus pada formulir penambahan produk (misal barang margin tipis 1.5% atau kategori promosi). Sistem secara otomatis menghitung nilai bagi hasil platform dan hak bersih vendor pada setiap baris item pemesanan Purchase Order (PO).
                    </p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="submit" class="btn-akuada-primary px-8 py-3 text-xs shadow-lg">
                <span class="material-symbols-outlined text-[18px]">save</span>
                <span>Simpan Seluruh Pengaturan Sistem</span>
            </button>
        </div>

    </form>
</div>

<?= $this->endSection() ?>
