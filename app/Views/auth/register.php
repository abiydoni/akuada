<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<div class="min-h-screen w-full py-10 sm:py-16 px-4 sm:px-6 lg:px-8 bg-gradient-to-br from-slate-900 via-purple-950 to-slate-900 flex justify-center items-start relative overflow-x-hidden">
    <!-- Ambient glow decorations -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-purple-600/25 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-pink-500/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-4xl w-full bg-white/95 backdrop-blur-xl p-6 sm:p-10 rounded-3xl border border-white/20 shadow-2xl relative z-10 space-y-8 my-6">
        
        <!-- Header & Logo -->
        <div class="text-center space-y-2">
            <a href="<?= base_url('/') ?>" class="inline-flex items-center gap-2.5">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-purple-700 via-blue-600 to-pink-500 flex items-center justify-center text-white shadow-lg shadow-purple-500/30">
                    <span class="material-symbols-outlined text-3xl">domain</span>
                </div>
            </a>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                Pendaftaran Akun Perusahaan (Corporate Buyer)
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-2xl mx-auto leading-relaxed">
                Daftarkan profil badan usaha dan unggah kelengkapan legalitas untuk mengakses katalog B2B, harga grosir bertingkat, Faktur Pajak PPN resmi, dan fasilitas Term of Payment (TOP).
            </p>
        </div>

        <!-- Compliance & Risk Mitigation Notice -->
        <div class="p-4 rounded-2xl bg-gradient-to-r from-purple-50 via-indigo-50 to-blue-50 border border-purple-200 text-xs text-purple-950 flex items-start gap-3 shadow-xs">
            <span class="material-symbols-outlined text-purple-600 text-2xl flex-shrink-0 mt-0.5">verified_user</span>
            <div class="space-y-1">
                <strong class="font-bold text-slate-900 block text-xs">Standar Kepatuhan Legalitas & Mitigasi Risiko Pengadaan B2B:</strong>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    Sesuai prinsip <em>Know Your Customer</em> (KYC) dan tata kelola transaksi korporat AkuAda, pengunggahan dokumen legalitas (NPWP, NIB, Akta Pendirian, & KTP PIC) diwajibkan demi mencegah penipuan pesanan fiktif, validasi Faktur Pajak resmi, dan penilaian kelayakan fasilitas kredit tempo.
                </p>
            </div>
        </div>

        <form action="<?= base_url('auth/register/process') ?>" method="POST" enctype="multipart/form-data" class="space-y-8 text-xs">
            <?= csrf_field() ?>

            <!-- 1. DATA LEGALITAS & IDENTITAS BADAN USAHA -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                    <span class="w-7 h-7 rounded-xl bg-purple-100 text-purple-700 font-extrabold flex items-center justify-center text-xs">1</span>
                    <h2 class="text-sm font-extrabold text-slate-900">Profil & Legalitas Badan Usaha</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="md:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Nama Resmi Perusahaan / Badan Usaha: <span class="text-rose-500">*</span></label>
                        <input type="text" name="company_name" value="<?= old('company_name') ?>" required placeholder="Contoh: PT Sinergi Nusantara Abadi" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 font-bold text-slate-900">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Bentuk Badan Usaha: <span class="text-rose-500">*</span></label>
                        <select name="legal_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 focus:ring-2 focus:ring-purple-100 bg-white font-bold text-slate-800">
                            <option value="PT" <?= old('legal_type') === 'PT' ? 'selected' : '' ?>>PT (Perseroan Terbatas)</option>
                            <option value="PT Tbk" <?= old('legal_type') === 'PT Tbk' ? 'selected' : '' ?>>PT Tbk (Perusahaan Terbuka)</option>
                            <option value="CV" <?= old('legal_type') === 'CV' ? 'selected' : '' ?>>CV (Commanditaire Vennootschap)</option>
                            <option value="BUMN" <?= old('legal_type') === 'BUMN' ? 'selected' : '' ?>>BUMN / BUMD</option>
                            <option value="Hotel/Resto" <?= old('legal_type') === 'Hotel/Resto' ? 'selected' : '' ?>>Hotel / Grup Restoran</option>
                            <option value="Yayasan" <?= old('legal_type') === 'Yayasan' ? 'selected' : '' ?>>Yayasan / Lembaga</option>
                            <option value="UD" <?= old('legal_type') === 'UD' ? 'selected' : '' ?>>UD / Perorangan Dagang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">NPWP Perusahaan (16 / 15 Digit): <span class="text-rose-500">*</span></label>
                        <input type="text" name="npwp" value="<?= old('npwp') ?>" required placeholder="01.234.567.8-901.000" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono font-bold text-slate-900">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor Induk Berusaha (NIB OSS): <span class="text-rose-500">*</span></label>
                        <input type="text" name="nib" value="<?= old('nib') ?>" required placeholder="9120001122334" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono font-bold text-slate-900">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Sektor / Bidang Usaha: <span class="text-rose-500">*</span></label>
                        <select name="business_field" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-medium text-slate-800">
                            <option value="Perhotelan & Hospitality" <?= old('business_field') === 'Perhotelan & Hospitality' ? 'selected' : '' ?>>Perhotelan & Hospitality</option>
                            <option value="Manufaktur & Pabrikasi" <?= old('business_field') === 'Manufaktur & Pabrikasi' ? 'selected' : '' ?>>Manufaktur & Pabrikasi</option>
                            <option value="Konstruksi & Properti" <?= old('business_field') === 'Konstruksi & Properti' ? 'selected' : '' ?>>Konstruksi & Properti</option>
                            <option value="F&B / Restoran & Kafe" <?= old('business_field') === 'F&B / Restoran & Kafe' ? 'selected' : '' ?>>F&B / Restoran & Kafe</option>
                            <option value="Kesehatan, Rumah Sakit & Farmasi" <?= old('business_field') === 'Kesehatan, Rumah Sakit & Farmasi' ? 'selected' : '' ?>>Kesehatan, RS & Farmasi</option>
                            <option value="Teknologi, Telekomunikasi & IT" <?= old('business_field') === 'Teknologi, Telekomunikasi & IT' ? 'selected' : '' ?>>Teknologi & IT</option>
                            <option value="Logistik & Transportasi" <?= old('business_field') === 'Logistik & Transportasi' ? 'selected' : '' ?>>Logistik & Transportasi</option>
                            <option value="Pendidikan, Yayasan & Lembaga" <?= old('business_field') === 'Pendidikan, Yayasan & Lembaga' ? 'selected' : '' ?>>Pendidikan & Yayasan</option>
                            <option value="Retail & Perdagangan Umum" <?= old('business_field') === 'Retail & Perdagangan Umum' ? 'selected' : '' ?>>Retail & Perdagangan Umum</option>
                            <option value="Lainnya" <?= old('business_field') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">No. Telepon Kantor / Hotline: <span class="text-rose-500">*</span></label>
                        <input type="text" name="phone" value="<?= old('phone') ?>" required placeholder="(021) 5290-8800" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kota Domisili Kantor / Serah: <span class="text-rose-500">*</span></label>
                        <input type="text" name="city" value="<?= old('city') ?>" required placeholder="Contoh: Jakarta Selatan / Surabaya" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Provinsi: <span class="text-rose-500">*</span></label>
                        <input type="text" name="province" value="<?= old('province', 'DKI Jakarta') ?>" required placeholder="DKI Jakarta / Jawa Timur" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block font-bold text-slate-700 mb-1">Alamat Lengkap Kantor & Gudang Penerimaan Barang: <span class="text-rose-500">*</span></label>
                        <textarea name="address" required rows="2" placeholder="Gedung Menara Sudirman Lt. 12, Jl. Jend. Sudirman Kav. 60, RT 05 / RW 03, Kel. Senayan, Kec. Kebayoran Baru"
                                  class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-purple-600 leading-relaxed"><?= old('address') ?></textarea>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Website Perusahaan (Opsional):</label>
                        <input type="text" name="website" value="<?= old('website') ?>" placeholder="https://perusahaan.co.id" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>
                </div>
            </div>

            <!-- 2. UPLOAD DOKUMEN LEGALITAS SANGAT RISKAN & WAJIB -->
            <div class="space-y-4">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                    <div class="flex items-center gap-2">
                        <span class="w-7 h-7 rounded-xl bg-purple-100 text-purple-700 font-extrabold flex items-center justify-center text-xs">2</span>
                        <div>
                            <h2 class="text-sm font-extrabold text-slate-900">Upload Dokumen Legalitas & Kepatuhan Usaha (Sangat Krusial)</h2>
                            <p class="text-[11px] text-slate-400">Format yang diterima: <strong>PDF, JPG, JPEG, PNG</strong> (Maks. 5 MB per berkas)</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-extrabold border border-rose-200">Kepatuhan Wajib</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Dokumen 1: NPWP / SKT -->
                    <div class="p-4 rounded-2xl border-2 border-dashed border-purple-200 bg-purple-50/40 hover:bg-purple-50/70 transition space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-purple-600 text-xl">credit_card</span>
                                <label class="font-bold text-slate-900 text-xs">1. Kartu NPWP Perusahaan / SKT Pajak <span class="text-rose-500">*</span></label>
                            </div>
                            <span class="px-1.5 py-0.5 rounded bg-purple-200 text-purple-800 text-[9px] font-extrabold uppercase">Wajib</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-snug">
                            Krusial untuk penerbitan e-Faktur Pajak PPN 11%/12% resmi dan verifikasi NPWP 16 digit.
                        </p>
                        <input type="file" name="npwp_file" accept=".pdf,.jpg,.jpeg,.png" required
                               class="w-full text-xs text-slate-600 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-purple-600 file:text-white hover:file:bg-purple-700 cursor-pointer">
                    </div>

                    <!-- Dokumen 2: NIB OSS -->
                    <div class="p-4 rounded-2xl border-2 border-dashed border-purple-200 bg-purple-50/40 hover:bg-purple-50/70 transition space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 text-xl">license</span>
                                <label class="font-bold text-slate-900 text-xs">2. Izin Usaha / Dokumen NIB OSS <span class="text-rose-500">*</span></label>
                            </div>
                            <span class="px-1.5 py-0.5 rounded bg-blue-200 text-blue-800 text-[9px] font-extrabold uppercase">Wajib</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-snug">
                            Krusial sebagai bukti legalitas izin berusaha resmi dari Kementerian Investasi / BKPM (OSS RBA).
                        </p>
                        <input type="file" name="nib_file" accept=".pdf,.jpg,.jpeg,.png" required
                               class="w-full text-xs text-slate-600 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                    </div>

                    <!-- Dokumen 3: Akta Pendirian / SK Kemenkumham -->
                    <div class="p-4 rounded-2xl border-2 border-dashed border-purple-200 bg-purple-50/40 hover:bg-purple-50/70 transition space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-amber-600 text-xl">gavel</span>
                                <label class="font-bold text-slate-900 text-xs">3. Akta Pendirian / SK Kemenkumham <span class="text-rose-500">*</span></label>
                            </div>
                            <span class="px-1.5 py-0.5 rounded bg-amber-200 text-amber-800 text-[9px] font-extrabold uppercase">Wajib</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-snug">
                            Krusial untuk membuktikan keabsahan badan hukum, hak perikatan utang-piutang, dan susunan direksi.
                        </p>
                        <input type="file" name="deed_file" accept=".pdf,.jpg,.jpeg,.png" required
                               class="w-full text-xs text-slate-600 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-amber-600 file:text-white hover:file:bg-amber-700 cursor-pointer">
                    </div>

                    <!-- Dokumen 4: KTP PIC / Direktur -->
                    <div class="p-4 rounded-2xl border-2 border-dashed border-purple-200 bg-purple-50/40 hover:bg-purple-50/70 transition space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-emerald-600 text-xl">badge</span>
                                <label class="font-bold text-slate-900 text-xs">4. Foto / Scan KTP PIC Pengadaan <span class="text-rose-500">*</span></label>
                            </div>
                            <span class="px-1.5 py-0.5 rounded bg-emerald-200 text-emerald-800 text-[9px] font-extrabold uppercase">Wajib</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-snug">
                            Krusial untuk verifikasi identitas personal penanggung jawab pemesanan agar bebas dari order fiktif.
                        </p>
                        <input type="file" name="id_card_file" accept=".pdf,.jpg,.jpeg,.png" required
                               class="w-full text-xs text-slate-600 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                    </div>

                    <!-- Dokumen 5: SPPKP (Opsional bila PKP) -->
                    <div class="p-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 hover:bg-slate-50 transition space-y-2 md:col-span-2">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-indigo-600 text-xl">receipt_long</span>
                                <label class="font-bold text-slate-900 text-xs">5. Surat Pengukuhan Pengusaha Kena Pajak (SPPKP) - Khusus PKP</label>
                            </div>
                            <span class="px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 text-[9px] font-bold uppercase">Opsional</span>
                        </div>
                        <p class="text-[11px] text-slate-500 leading-snug">
                            Lampirkan berkas SPPKP bila perusahaan Anda berstatus PKP untuk kelancaran pengkreditan e-Faktur Pajak PPN Masukan di DJP.
                        </p>
                        <input type="file" name="sppkp_file" accept=".pdf,.jpg,.jpeg,.png"
                               class="w-full text-xs text-slate-600 file:mr-2.5 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-slate-700 file:text-white hover:file:bg-slate-800 cursor-pointer">
                    </div>
                </div>
            </div>

            <!-- 3. DATA PIC PROCUREMENT & AKUN LOGIN -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-200">
                    <span class="w-7 h-7 rounded-xl bg-purple-100 text-purple-700 font-extrabold flex items-center justify-center text-xs">3</span>
                    <h2 class="text-sm font-extrabold text-slate-900">Data PIC Pengadaan & Akses Akun Login</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nama Lengkap PIC: <span class="text-rose-500">*</span></label>
                        <input type="text" name="pic_name" value="<?= old('pic_name') ?>" required placeholder="Contoh: Rina Kartika, S.E." 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold text-slate-900">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Jabatan di Perusahaan: <span class="text-rose-500">*</span></label>
                        <input type="text" name="position" value="<?= old('position', 'Procurement Manager') ?>" required placeholder="Procurement Manager / Purchasing Lead / Direktur" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / HP PIC: <span class="text-rose-500">*</span></label>
                        <input type="text" name="pic_phone" value="<?= old('pic_phone') ?>" required placeholder="081234567890" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold">
                        <p class="text-[10px] text-slate-400 mt-0.5">Digunakan untuk konfirmasi pesanan PO dan koordinasi pengiriman barang.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Email Resmi Kantor (Untuk Login): <span class="text-rose-500">*</span></label>
                        <input type="email" name="email" value="<?= old('email') ?>" required placeholder="procurement@perusahaan.co.id" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold text-slate-900">
                        <p class="text-[10px] text-slate-400 mt-0.5">Alamat email ini menjadi username resmi untuk masuk ke portal AkuAda.</p>
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Kata Sandi Akun: <span class="text-rose-500">*</span></label>
                        <input type="password" name="password" required placeholder="Minimal 6 karakter" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                    </div>

                    <div>
                        <label class="block font-bold text-slate-700 mb-1">Konfirmasi Kata Sandi: <span class="text-rose-500">*</span></label>
                        <input type="password" name="password_confirm" required placeholder="Ketik ulang kata sandi di atas" 
                               class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                    </div>
                </div>
            </div>

            <!-- 4. PERNYATAAN KEABSAHAN & SUBMIT -->
            <div class="space-y-4 pt-2">
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" required class="mt-0.5 rounded border-slate-300 text-purple-600 focus:ring-purple-500 w-4 h-4">
                        <span class="text-[11px] text-slate-600 leading-relaxed">
                            Saya dengan ini menyatakan bahwa seluruh data perusahaan dan berkas dokumen legalitas (NPWP, NIB, Akta, dan KTP) yang diunggah adalah <strong>asli, sah, masih berlaku</strong>, dan dapat dipertanggungjawabkan secara hukum. Saya memberikan wewenang kepada tim Compliance AkuAda untuk melakukan verifikasi legalitas guna pembukaan fasilitas transaksi pengadaan B2B.
                        </span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-purple-700 via-indigo-600 to-blue-600 hover:opacity-95 text-white font-extrabold text-sm shadow-xl shadow-purple-600/25 flex items-center justify-center gap-2 transition transform active:scale-[0.99]">
                    <span class="material-symbols-outlined text-xl">how_to_reg</span>
                    <span>Daftarkan Akun Perusahaan & Unggah Berkas Legalitas</span>
                </button>
            </div>
        </form>

        <!-- Footer Links -->
        <div class="space-y-3 pt-4 border-t border-slate-100 text-center text-xs">
            <div class="text-slate-600">
                Sudah memiliki akun perusahaan? 
                <a href="<?= base_url('auth/login') ?>" class="font-extrabold text-purple-700 hover:underline">
                    Masuk ke Akun Anda &rarr;
                </a>
            </div>

            <!-- Notice for Vendors -->
            <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-[11px] text-slate-600 text-left flex items-start gap-2.5">
                <span class="material-symbols-outlined text-blue-600 text-xl flex-shrink-0 mt-0.5">storefront</span>
                <div>
                    <strong class="text-slate-900 block font-semibold">Pendaftaran Rekanan Mitra Supplier / Vendor:</strong>
                    <span>Formulir di atas diperuntukkan khusus bagi pembeli korporat (Corporate Buyer). Pendaftaran rekanan vendor supplier diproses tersendiri oleh Admin AkuAda melalui Perjanjian Kerjasama (PKS). Hubungi kami via email <a href="mailto:vendor@akuada.co.id" class="text-blue-700 font-bold underline">vendor@akuada.co.id</a>.</span>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
