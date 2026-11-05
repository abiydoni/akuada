<?= $this->extend('layouts/admin') ?>

<?= $this->section('admin_content') ?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?= base_url('admin/buyers') ?>" class="hover:text-purple-600">Daftar Perusahaan Buyer</a>
                <span>/</span>
                <span class="text-purple-700 font-bold">Pendaftaran Akun Buyer Baru</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900">Daftarkan Perusahaan Buyer & Alokasikan Fasilitas Kredit</h1>
            <p class="text-xs text-slate-500 mt-0.5">AkuAda mendaftarkan akun corporate buyer resmi dan memberikan fasilitas plafon kredit Term of Payment (TOP).</p>
        </div>
    </div>

    <form action="<?= base_url('admin/buyers/store') ?>" method="POST" enctype="multipart/form-data" class="space-y-6 text-xs">
        
        <!-- 1. Legalitas Perusahaan Buyer -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-purple-600">domain</span>
                <h2 class="text-sm font-bold text-slate-800">1. Data Legalitas & Profil Badan Usaha Pembeli (Corporate Buyer)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Nama Perusahaan Resmi: <span class="text-rose-500">*</span></label>
                    <input type="text" name="company_name" required placeholder="Contoh: PT Surya Makmur Sentosa" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-bold text-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Bentuk Badan Usaha: <span class="text-rose-500">*</span></label>
                    <select name="legal_type" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 bg-white font-bold">
                        <option value="PT">PT (Perseroan Terbatas)</option>
                        <option value="PT Tbk">PT Tbk (Perusahaan Terbuka)</option>
                        <option value="BUMN">BUMN / BUMD</option>
                        <option value="CV">CV (Commanditaire Vennootschap)</option>
                        <option value="Yayasan">Yayasan / Lembaga</option>
                        <option value="Hotel/Resto">Hotel / Grup Restoran</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">NPWP Perusahaan (16 Digit): <span class="text-rose-500">*</span></label>
                    <input type="text" name="npwp" required placeholder="01.999.888.7-077.000" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Induk Berusaha (NIB): <span class="text-rose-500">*</span></label>
                    <input type="text" name="nib" required placeholder="9120001122334" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Kota Domisili Kantor / Titik Serah: <span class="text-rose-500">*</span></label>
                    <input type="text" name="city" required placeholder="Jakarta Selatan / Surabaya" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Alamat Kantor & Gudang Penerimaan Barang:</label>
                    <textarea name="address" rows="2" placeholder="Menara Sudirman Lt. 15, Jl. Jend. Sudirman Kav. 60, Jakarta"
                              class="w-full px-3.5 py-2 rounded-xl border border-slate-300 focus:border-purple-600"></textarea>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Sektor / Bidang Usaha:</label>
                    <input type="text" name="business_field" placeholder="Contoh: Perhotelan / Manufaktur" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor Telepon Kantor / Hotline:</label>
                    <input type="text" name="phone" placeholder="(021) 5290-8800" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Website Perusahaan:</label>
                    <input type="text" name="website" placeholder="https://perusahaan.co.id" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>
            </div>
        </div>

        <!-- 2. Berkas Dokumen Legalitas (Opsional) -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-600">folder_open</span>
                    <h2 class="text-sm font-bold text-slate-800">2. Berkas Dokumen Legalitas & Kepatuhan Usaha (PDF / Gambar maks 5MB)</h2>
                </div>
                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">Opsional</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-3.5 rounded-xl border border-slate-200 space-y-1">
                    <label class="block font-bold text-slate-700">Kartu NPWP Perusahaan / SKT Pajak:</label>
                    <input type="file" name="npwp_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-purple-600 file:text-white">
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200 space-y-1">
                    <label class="block font-bold text-slate-700">Nomor Induk Berusaha (NIB OSS):</label>
                    <input type="file" name="nib_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-blue-600 file:text-white">
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200 space-y-1">
                    <label class="block font-bold text-slate-700">Akta Pendirian / SK Kemenkumham:</label>
                    <input type="file" name="deed_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-amber-600 file:text-white">
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200 space-y-1">
                    <label class="block font-bold text-slate-700">Foto / Scan KTP PIC Pengadaan:</label>
                    <input type="file" name="id_card_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-emerald-600 file:text-white">
                </div>

                <div class="p-3.5 rounded-xl border border-slate-200 space-y-1 md:col-span-2">
                    <label class="block font-bold text-slate-700">Surat Pengukuhan Pengusaha Kena Pajak (SPPKP - Khusus PKP):</label>
                    <input type="file" name="sppkp_file" accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-600 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-slate-700 file:text-white">
                </div>
            </div>
        </div>

        <!-- 2. Alokasi Fasilitas Kredit Term of Payment (TOP) -->
        <div class="bg-gradient-to-br from-purple-50/70 via-blue-50/60 to-emerald-50/50 rounded-2xl p-6 border-2 border-purple-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-purple-100">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-purple-700">credit_card</span>
                    <h2 class="text-sm font-bold text-purple-950">2. Fasilitas Kredit Term of Payment (TOP) & Plafon Pengadaan</h2>
                </div>
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[10px]">Fasilitas Kredit Aktif</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                <div class="p-4 bg-white rounded-xl border border-purple-300 shadow-sm space-y-2">
                    <label class="block font-extrabold text-slate-900">Alokasi Plafon Kredit Awal (Rp): <span class="text-rose-500">*</span></label>
                    <div class="flex items-center gap-2">
                        <span class="text-base font-black text-slate-500">Rp</span>
                        <input type="number" step="5000000" min="0" name="credit_limit_top" value="<?= $defaultCredit ?? 100000000 ?>" required
                               class="w-full px-4 py-2.5 rounded-xl border-2 border-purple-400 focus:border-purple-600 font-black text-xl text-purple-900">
                    </div>
                    <p class="text-[11px] text-slate-500">
                        Plafon ini adalah batas kredit belanja tempo di web AkuAda. Setiap pesanan PO akan otomatis memotong plafon ini dan kembali pulih saat pelunasan diverifikasi.
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-purple-100/60 border border-purple-200 text-xs text-purple-900 space-y-2">
                    <strong class="font-bold flex items-center gap-1">
                        <span class="material-symbols-outlined text-purple-700 text-[18px]">verified</span>
                        Hak Akses Fasilitas Pembayaran Tempo:
                    </strong>
                    <ul class="space-y-1 text-[11px] text-slate-700 list-disc list-inside">
                        <li>Term of Payment <?= implode(' Hari, ', get_allowed_top_terms()) ?> Hari.</li>
                        <li>Jatuh tempo dihitung resmi setelah Berita Acara Serah Terima (BAST) disahkan.</li>
                        <li>Faktur Pajak PPN <?= esc(format_tax_rate()) ?> resmi diterbitkan otomatis atas nama buyer.</li>
                    </ul>
                </div>

                <div class="p-4 bg-white rounded-xl border border-purple-300 shadow-sm space-y-2 md:col-span-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <label class="block font-extrabold text-slate-900">Persentase Fee Cashback Khusus Buyer (%):</label>
                            <p class="text-[11px] text-slate-500">Persentase cashback saldo untuk setiap transaksi yang lunas. Kosongkan jika ingin mengikuti default sistem (<?= esc(get_buyer_cashback_rate()) ?>%). Tiap buyer dapat disetting persentase berbeda.</p>
                        </div>
                        <div class="flex items-center gap-1.5 w-36 flex-shrink-0">
                            <input type="number" step="0.01" min="0" max="100" name="cashback_rate" placeholder="<?= esc(get_buyer_cashback_rate()) ?>"
                                   class="w-full px-3 py-2 rounded-xl border border-purple-300 focus:border-purple-600 font-mono font-bold text-sm text-purple-900 text-right">
                            <span class="font-bold text-slate-500 text-sm">%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Akun Login PIC Tim Procurement / Purchasing -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                <span class="material-symbols-outlined text-purple-600">badge</span>
                <h2 class="text-sm font-bold text-slate-800">3. Akun Login PIC Pengadaan (Procurement Manager / Lead)</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nama Lengkap PIC Procurement: <span class="text-rose-500">*</span></label>
                    <input type="text" name="pic_name" required placeholder="Contoh: Rina Kartika, S.E." 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-semibold">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Jabatan PIC di Perusahaan: <span class="text-rose-500">*</span></label>
                    <input type="text" name="position" value="Procurement Manager" required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Nomor WhatsApp / HP PIC: <span class="text-rose-500">*</span></label>
                    <input type="text" name="pic_phone" required placeholder="081234567890" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">Email Resmi Perusahaan (Untuk Login): <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="procurement@perusahaan.co.id" 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-semibold text-slate-900">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1">Password Akun Sementara: <span class="text-rose-500">*</span></label>
                    <input type="text" name="password" value="<?= esc(get_system_setting('default_account_password', 'admin123')) ?>" required 
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 focus:border-purple-600 font-mono text-purple-700 font-bold">
                    <p class="text-[10px] text-slate-400 mt-1">Default: <strong class="font-mono text-slate-600"><?= esc(get_system_setting('default_account_password', 'admin123')) ?></strong>. Buyer dapat mengganti password setelah login pertama kali.</p>
                </div>
            </div>
        </div>

        <!-- Action Button -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
            <a href="<?= base_url('admin/buyers') ?>" class="px-5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-100 font-bold text-slate-700 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-700 via-indigo-600 to-blue-600 hover:opacity-95 text-white font-bold transition shadow-lg shadow-purple-500/25 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">verified</span>
                <span>Daftarkan Akun Buyer & Terbitkan Fasilitas Kredit</span>
            </button>
        </div>

    </form>
</div>

<?= $this->endSection() ?>
