<?= $this->extend('layouts/main') ?>

<?= $this->section('subcontent') ?>

<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-extrabold text-slate-900 flex items-center gap-2">
            <span class="material-symbols-outlined text-purple-600 text-3xl">verified</span>
            Penerbitan Purchase Order (PO) & Checkout B2B
        </h1>
        <p class="text-xs text-slate-500 mt-1">
            Lengkapi data legalitas perusahaan, nomor PO internal, metode pembayaran tempo (TOP), dan instruksi serah terima barang (BAST).
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
                        <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-700 font-bold text-[10px]">Verified B2B</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                        <div>
                            <label class="block text-slate-500 mb-1">Nama Perusahaan Resmi:</label>
                            <input type="text" readonly value="<?= esc($buyerCompany['company_name'] ?? 'PT Mega Solusi Digital Tbk') ?>" 
                                   class="w-full bg-slate-50 font-bold text-slate-800 rounded-xl border border-slate-200 px-3.5 py-2.5">
                        </div>
                        <div>
                            <label class="block text-slate-500 mb-1">Bentuk Legalitas:</label>
                            <input type="text" readonly value="<?= esc($buyerCompany['legal_type'] ?? 'PT Tbk') ?>" 
                                   class="w-full bg-slate-50 font-bold text-slate-800 rounded-xl border border-slate-200 px-3.5 py-2.5">
                        </div>
                        <div>
                            <label class="block text-slate-500 mb-1">NPWP Perusahaan (16 Digit):</label>
                            <input type="text" readonly value="<?= esc($buyerCompany['npwp'] ?? '01.999.888.7-077.000') ?>" 
                                   class="w-full bg-slate-50 font-bold text-slate-800 rounded-xl border border-slate-200 px-3.5 py-2.5">
                        </div>
                        <div>
                            <label class="block text-slate-500 mb-1">Nomor Induk Berusaha (NIB):</label>
                            <input type="text" readonly value="<?= esc($buyerCompany['nib'] ?? '9120001122334') ?>" 
                                   class="w-full bg-slate-50 font-bold text-slate-800 rounded-xl border border-slate-200 px-3.5 py-2.5">
                        </div>
                    </div>

                    <!-- Internal PO Number -->
                    <div class="pt-2">
                        <label for="po_number" class="block text-xs font-bold text-slate-800 mb-1">
                            Nomor Purchase Order (PO) Internal Perusahaan Anda: <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="po_number" name="po_number" required placeholder="Contoh: PO/MSD/PROC/<?= date('Y/m') ?>/0091" 
                               value="PO/CORP/<?= date('Ym') ?>/<?= rand(100, 999) ?>"
                               class="w-full font-mono font-bold text-sm text-purple-900 bg-purple-50/50 rounded-xl border border-purple-300 focus:border-purple-600 focus:ring-4 focus:ring-purple-100 px-4 py-2.5">
                        <p class="text-[11px] text-slate-400 mt-1">Nomor PO ini akan tercetak otomatis pada e-Invoice, Surat Jalan, dan BAST.</p>
                    </div>
                </div>

                <!-- 2. B2B Payment Methods (TOP, VA, PO) -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-blue-600">payments</span>
                            <h2 class="text-sm font-bold text-slate-800">2. Metode Pembayaran & Termin Kredit (TOP)</h2>
                        </div>
                        <span class="text-[11px] text-slate-500">Plafon Tersedia: <strong class="text-emerald-600"><?= format_rupiah($buyerCompany['credit_limit_top'] - $buyerCompany['credit_used_top']) ?></strong></span>
                    </div>

                    <div class="space-y-3">
                        <!-- TOP 30 Hari (Default) -->
                        <label class="flex items-start gap-3 p-4 rounded-xl border-2 border-purple-500 bg-purple-50/40 cursor-pointer transition hover:bg-purple-50/70">
                            <input type="radio" name="payment_method" value="top_30" checked class="mt-1 text-purple-600 focus:ring-purple-500">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 text-xs">Term of Payment 30 Hari (TOP 30) - Direkomendasikan</span>
                                    <span class="badge-top text-[10px]">Tempo 30 Hari</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Pembayaran dilakukan maksimal 30 hari kalender setelah Berita Acara Serah Terima (BAST) ditandatangani.</p>
                            </div>
                        </label>

                        <!-- TOP 60 Hari -->
                        <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-purple-300 cursor-pointer transition">
                            <input type="radio" name="payment_method" value="top_60" class="mt-1 text-purple-600 focus:ring-purple-500">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 text-xs">Term of Payment 60 Hari (TOP 60)</span>
                                    <span class="badge-top text-[10px]">Tempo 60 Hari</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Fasilitas kredit tempo khusus pengadaan operasional skala besar.</p>
                            </div>
                        </label>

                        <!-- TOP 14 Hari -->
                        <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-purple-300 cursor-pointer transition">
                            <input type="radio" name="payment_method" value="top_14" class="mt-1 text-purple-600 focus:ring-purple-500">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 text-xs">Term of Payment 14 Hari (TOP 14)</span>
                                    <span class="badge-top text-[10px]">Tempo 14 Hari</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Proses approval super cepat 1x24 jam.</p>
                            </div>
                        </label>

                        <!-- Corporate PO On-Account -->
                        <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-purple-300 cursor-pointer transition">
                            <input type="radio" name="payment_method" value="corporate_po" class="mt-1 text-purple-600 focus:ring-purple-500">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 text-xs">Corporate Purchase Order (PO) On-Account</span>
                                    <span class="px-2 py-0.5 rounded bg-pink-100 text-pink-700 text-[10px] font-bold">On-Account</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Penagihan faktur bulanan gabungan (Monthly Statement) langsung ke Finance Departemen.</p>
                            </div>
                        </label>

                        <!-- Bank Virtual Account -->
                        <label class="flex items-start gap-3 p-4 rounded-xl border border-slate-200 hover:border-purple-300 cursor-pointer transition">
                            <input type="radio" name="payment_method" value="bank_va" class="mt-1 text-purple-600 focus:ring-purple-500">
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-slate-900 text-xs">Corporate Virtual Account (BCA, Mandiri, BNI, BRI)</span>
                                    <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] font-bold">Instant</span>
                                </div>
                                <p class="text-[11px] text-slate-500 mt-0.5">Bayar instan melalui corporate internet banking (KlikBCA Bisnis, Mandiri MCM, dll).</p>
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
                            <textarea name="shipping_address" rows="3" required class="w-full bg-slate-50 rounded-xl border border-slate-300 px-3.5 py-2.5 text-xs focus:ring-2 focus:ring-purple-100 focus:border-purple-600"><?= esc($buyerCompany['address'] ?? 'Menara Mandiri Tower 1 Lt. 24, Jl. Jend. Sudirman Kav 54-55, Jakarta Selatan') ?></textarea>
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
                                    Terbitkan e-Faktur Pajak PPN 11% Resmi DJP
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
                        <span>PPN 11% (e-Faktur DJP):</span>
                        <span class="font-bold text-blue-700"><?= format_rupiah($ppnTotal) ?></span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Biaya Kirim Logistik:</span>
                        <span class="font-bold text-emerald-600">Rp 0 (Corporate Free)</span>
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

<?= $this->endSection() ?>
