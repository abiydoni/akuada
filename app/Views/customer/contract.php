<?php
$useAdminLayout = !empty($isAdminView);
?>
<?= $this->extend($useAdminLayout ? 'layouts/admin' : 'layouts/customer') ?>

<?= $this->section($useAdminLayout ? 'admin_content' : 'customer_content') ?>
<?php
$issuerCompany  = get_system_setting('inv_issuer_company', 'PT AkuAda Indonesia');
$issuerAddress  = get_system_setting('inv_issuer_address', 'Gedung Cyber Tower Lt. 12, Jl. HR. Rasuna Said, Jakarta Selatan');
$issuerNpwp     = get_system_setting('inv_issuer_npwp', '01.889.776.5-012.000');
$signerName     = get_system_setting('inv_signer_name', 'Hendra Wijaya, S.E., M.B.A.');
$signerPosition = get_system_setting('inv_signer_position', 'Direktur Operasional & Kemitraan B2B');

$buyerLegalName = trim(($company['legal_type'] ?? 'PT') . ' ' . ($company['company_name'] ?? 'Perusahaan Mitra'));
if (str_starts_with(strtoupper($company['company_name'] ?? ''), strtoupper(($company['legal_type'] ?? 'PT') . ' '))) {
    $buyerLegalName = $company['company_name'];
}

$contractDate   = !empty($company['updated_at']) ? $company['updated_at'] : ($company['created_at'] ?? date('Y-m-d'));
$contractNumber = 'PKS/AKUADA-B2B/' . date('Y/m', strtotime($contractDate)) . '/' . str_pad((string)$company['id'], 4, '0', STR_PAD_LEFT);

$creditLimit    = (float)($company['credit_limit_top'] ?? get_system_setting('default_credit_limit', 100000000));
$cashbackRate   = (float)get_buyer_cashback_rate((int)$company['id']);
$maxUnpaidNotes = (int)get_max_unpaid_notes_limit();
$allowedTop     = get_allowed_top_terms();
$topRangeText   = !empty($allowedTop) ? (min($allowedTop) . ' hingga ' . max($allowedTop) . ' Hari Kalender') : '14 hingga 90 Hari Kalender';
$freeShipRegion = get_system_setting('free_shipping_region', 'Corporate Jabodetabek');
$picName        = $user['name'] ?? ($company['pic_name'] ?? session('user_name') ?? 'Pimpinan Pengadaan');
$picPosition    = $user['position'] ?? 'PIC Procurement / Perwakilan Sah';
?>

<div class="space-y-6">
    <!-- Top Header & Print Button (Hidden on Print) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 print:hidden">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">verified</span> Legalitas Terverifikasi AkuAda
                </span>
                <span class="font-mono text-xs font-bold text-purple-700"><?= esc($contractNumber) ?></span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Kontrak Kerjasama Pengadaan B2B & Rincian Benefit Mitra</h1>
            <p class="text-xs text-slate-500 mt-0.5">Dokumen perjanjian kerjasama resmi beserta hak istimewa dan fasilitas korporat yang diberikan oleh AkuAda kepada perusahaan Anda.</p>
        </div>
        <div class="flex items-center gap-2">
            <?php if (!empty($isAdminView)): ?>
                <a href="<?= base_url('admin/buyers') ?>" class="px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali ke Daftar Buyer</span>
                </a>
            <?php endif; ?>
            <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-800 hover:to-indigo-800 text-white font-extrabold text-xs transition flex items-center gap-1.5 shadow-md">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak / Unduh PDF Kontrak</span>
            </button>
        </div>
    </div>

    <!-- BAGIAN 1: DETAIL BENEFIT EKSKLUSIF YANG DIBERIKAN AKUADA -->
    <div class="bg-gradient-to-br from-slate-900 via-purple-950 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl space-y-6 print:bg-white print:text-slate-900 print:border-2 print:border-slate-800 print:shadow-none">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-white/15 print:border-slate-300">
            <div class="space-y-1">
                <span class="px-3 py-1 rounded-full bg-pink-500/20 text-pink-300 border border-pink-400/30 text-[10px] font-black uppercase tracking-widest inline-block print:bg-slate-100 print:text-slate-800 print:border-slate-400">
                    Hak Istimewa Corporate Buyer Terverifikasi
                </span>
                <h2 class="text-lg sm:text-xl font-black tracking-tight text-white print:text-slate-900">
                    Rincian Benefit & Fasilitas Resmi dari AkuAda untuk <?= esc($buyerLegalName) ?>
                </h2>
                <p class="text-xs text-purple-200 print:text-slate-600">
                    Seluruh fasilitas di bawah ini telah aktif secara penuh setelah dokumen legalitas perusahaan Anda disahkan oleh Tim Compliance AkuAda.
                </p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3.5 border border-white/20 text-left md:text-right flex-shrink-0 print:border-slate-300">
                <span class="text-[10px] uppercase font-bold text-purple-200 block print:text-slate-500">Status Kemitraan</span>
                <strong class="text-sm font-black text-emerald-300 flex items-center md:justify-end gap-1 mt-0.5 print:text-emerald-800">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    AKTIF & TERVERIFIKASI
                </strong>
            </div>
        </div>

        <!-- 6 Benefit Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
            <!-- Benefit 1: Plafon Kredit TOP -->
            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2 print:bg-slate-50 print:border-slate-300">
                <div class="flex items-center justify-between">
                    <span class="w-9 h-9 rounded-xl bg-purple-500/30 text-purple-200 flex items-center justify-center font-bold print:bg-purple-100 print:text-purple-900">
                        <span class="material-symbols-outlined text-[20px]">credit_score</span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-400/30 print:bg-emerald-100 print:text-emerald-900">
                        Bunga 0%
                    </span>
                </div>
                <h3 class="font-extrabold text-sm text-white print:text-slate-900">1. Plafon Kredit Tempo (TOP <?= esc($topRangeText) ?>)</h3>
                <p class="text-purple-100/90 text-[11px] leading-relaxed print:text-slate-700">
                    Alokasi plafon kredit pengadaan sebesar <strong class="text-yellow-300 font-mono print:text-slate-900"><?= format_rupiah($creditLimit) ?></strong> dengan kuota hingga <strong><?= $maxUnpaidNotes ?> nota berjalan per bulan</strong>. Jatuh tempo dihitung sejak barang diterima dan BAST disahkan.
                </p>
            </div>

            <!-- Benefit 2: Program Cashback Korporat -->
            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2 print:bg-slate-50 print:border-slate-300">
                <div class="flex items-center justify-between">
                    <span class="w-9 h-9 rounded-xl bg-pink-500/30 text-pink-200 flex items-center justify-center font-bold print:bg-pink-100 print:text-pink-900">
                        <span class="material-symbols-outlined text-[20px]">savings</span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-pink-500/30 text-pink-200 font-mono font-bold text-[10px] border border-pink-400/30 print:bg-pink-100 print:text-pink-900">
                        Reward <?= number_format($cashbackRate, 1) ?>%
                    </span>
                </div>
                <h3 class="font-extrabold text-sm text-white print:text-slate-900">2. Cashback Tunai <?= number_format($cashbackRate, 1) ?>% Setiap PO Lunas</h3>
                <p class="text-purple-100/90 text-[11px] leading-relaxed print:text-slate-700">
                    Setiap transaksi PO yang telah selesai (BAST sah) dan tagihannya dibayar lunas otomatis memberikan <strong>Cashback <?= number_format($cashbackRate, 1) ?>% dari nilai DPP barang</strong> yang dapat dicairkan langsung ke rekening bank.
                </p>
            </div>

            <!-- Benefit 3: Surat Penawaran Harga Resmi & Diskon Tiering -->
            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2 print:bg-slate-50 print:border-slate-300">
                <div class="flex items-center justify-between">
                    <span class="w-9 h-9 rounded-xl bg-blue-500/30 text-blue-200 flex items-center justify-center font-bold print:bg-blue-100 print:text-blue-900">
                        <span class="material-symbols-outlined text-[20px]">request_quote</span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-500/30 text-blue-200 font-mono font-bold text-[10px] border border-blue-400/30 print:bg-blue-100 print:text-blue-900">
                        Full Access
                    </span>
                </div>
                <h3 class="font-extrabold text-sm text-white print:text-slate-900">3. Akses Surat Penawaran Resmi & Harga Grosir</h3>
                <p class="text-purple-100/90 text-[11px] leading-relaxed print:text-slate-700">
                    Hak penuh menerbitkan & mengunduh <strong>Surat Penawaran Harga Resmi (Official Quotation)</strong> ber-KOP dan bertandatangan untuk persetujuan direksi/finance internal, lengkap dengan harga diskon grosir bertingkat (tiering) & tender RFQ.
                </p>
            </div>

            <!-- Benefit 4: Kurasi Desain Custom & Mockup Pra-Produksi -->
            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2 print:bg-slate-50 print:border-slate-300">
                <div class="flex items-center justify-between">
                    <span class="w-9 h-9 rounded-xl bg-amber-500/30 text-amber-200 flex items-center justify-center font-bold print:bg-amber-100 print:text-amber-900">
                        <span class="material-symbols-outlined text-[20px]">palette</span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-500/30 text-amber-200 font-mono font-bold text-[10px] border border-amber-400/30 print:bg-amber-100 print:text-amber-900">
                        QC & Proofing
                    </span>
                </div>
                <h3 class="font-extrabold text-sm text-white print:text-slate-900">4. Jaminan Desain Custom & Mockup Approval</h3>
                <p class="text-purple-100/90 text-[11px] leading-relaxed print:text-slate-700">
                    Layanan kurasi spesifikasi barang custom dan pengiriman <strong>Preview Mockup Pra-Produksi</strong> oleh Tim AkuAda. Produksi massal baru dijalankan setelah desain disetujui (ACC) oleh pihak Anda.
                </p>
            </div>

            <!-- Benefit 5: Proteksi Retur, QC & Logistik -->
            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2 print:bg-slate-50 print:border-slate-300">
                <div class="flex items-center justify-between">
                    <span class="w-9 h-9 rounded-xl bg-emerald-500/30 text-emerald-200 flex items-center justify-center font-bold print:bg-emerald-100 print:text-emerald-900">
                        <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/30 text-emerald-200 font-mono font-bold text-[10px] border border-emerald-400/30 print:bg-emerald-100 print:text-emerald-900">
                        Garansi BAST
                    </span>
                </div>
                <h3 class="font-extrabold text-sm text-white print:text-slate-900">5. Subsidi Logistik & Garansi Klaim Retur</h3>
                <p class="text-purple-100/90 text-[11px] leading-relaxed print:text-slate-700">
                    Fasilitas pengiriman logistik korporat (bebas biaya kirim area <strong><?= esc($freeShipRegion) ?></strong> sesuai ketentuan sistem) serta jaminan penggantian barang baru atau *Credit Note* apabila barang rusak/tidak sesuai spesifikasi PO.
                </p>
            </div>

            <!-- Benefit 6: Dokumen Legal & Account Manager Satu Pintu -->
            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2 print:bg-slate-50 print:border-slate-300">
                <div class="flex items-center justify-between">
                    <span class="w-9 h-9 rounded-xl bg-indigo-500/30 text-indigo-200 flex items-center justify-center font-bold print:bg-indigo-100 print:text-indigo-900">
                        <span class="material-symbols-outlined text-[20px]">verified_user</span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/30 text-indigo-200 font-mono font-bold text-[10px] border border-indigo-400/30 print:bg-indigo-100 print:text-indigo-900">
                        Satu Pintu
                    </span>
                </div>
                <h3 class="font-extrabold text-sm text-white print:text-slate-900">6. Dokumen Legal Lengkap & Pendampingan 1-on-1</h3>
                <p class="text-purple-100/90 text-[11px] leading-relaxed print:text-slate-700">
                    Penerbitan otomatis Invoice Resmi, Surat Jalan, BAST Digital, dan dokumen perpajakan B2B, serta layanan konsultasi pengadaan satu pintu bersama Account Manager AkuAda.
                </p>
            </div>
        </div>
    </div>

    <!-- BAGIAN 2: DOKUMEN KONTRAK KERJASAMA RESMI SESUAI PASAL-PASALNYA -->
    <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-xl p-6 sm:p-10 lg:p-12 space-y-8 text-xs leading-relaxed text-slate-800 print:border-none print:shadow-none print:p-0">
        
        <!-- KOP SURAT KONTRAK KERJASAMA -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b-4 border-double border-slate-900">
            <div class="flex items-center gap-3.5">
                <img src="<?= base_url('apple-touch-icon.png') ?>" alt="AkuAda" class="w-14 h-14 rounded-2xl object-contain border border-slate-200 p-1">
                <div>
                    <h2 class="text-xl font-black tracking-tight text-slate-900 uppercase"><?= esc($issuerCompany) ?></h2>
                    <p class="text-[11px] font-bold text-purple-800 uppercase tracking-wider">Platform Agregator & E-Procurement B2B Terpadu</p>
                    <p class="text-[11px] text-slate-500"><?= esc($issuerAddress) ?> | NPWP: <span class="font-mono"><?= esc($issuerNpwp) ?></span></p>
                </div>
            </div>
            <div class="text-left sm:text-right space-y-1 bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">Nomor Perjanjian Kerjasama</span>
                <strong class="font-mono text-sm font-black text-purple-900 block"><?= esc($contractNumber) ?></strong>
                <span class="text-[10px] text-emerald-700 font-bold block">Disahkan: <?= date('d F Y', strtotime($contractDate)) ?></span>
            </div>
        </div>

        <!-- JUDUL KONTRAK -->
        <div class="text-center space-y-1.5 py-2">
            <h3 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-wide underline decoration-2 underline-offset-4">
                PERJANJIAN KERJASAMA PENGADAAN BARANG DAN JASA KORPORAT (B2B)
            </h3>
            <p class="text-xs font-bold text-slate-600">
                NOMOR: <span class="font-mono text-slate-900"><?= esc($contractNumber) ?></span>
            </p>
        </div>

        <!-- PEMBUKAAN & IDENTITAS PARA PIHAK -->
        <div class="space-y-4 text-justify">
            <p>
                Pada hari ini, tanggal <strong><?= date('d F Y', strtotime($contractDate)) ?></strong>, setelah dilakukannya verifikasi dan pengesahan dokumen legalitas badan usaha secara resmi pada sistem <strong>AkuAda B2B E-Procurement Hub</strong>, yang bertanda tangan di bawah ini:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- PIHAK PERTAMA -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5">
                    <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-900 font-extrabold text-[10px] uppercase">PIHAK PERTAMA (PENYELENGGARA PLATFORM)</span>
                    <h4 class="text-sm font-black text-slate-900 pt-1"><?= esc($issuerCompany) ?></h4>
                    <div class="space-y-1 text-[11px] text-slate-600">
                        <p><strong>Diwakili Oleh:</strong> <?= esc($signerName) ?></p>
                        <p><strong>Jabatan:</strong> <?= esc($signerPosition) ?></p>
                        <p><strong>NPWP:</strong> <span class="font-mono"><?= esc($issuerNpwp) ?></span></p>
                        <p><strong>Alamat Kedudukan:</strong> <?= esc($issuerAddress) ?></p>
                    </div>
                    <p class="text-[11px] text-slate-500 pt-1 italic">
                        Selanjutnya dalam Perjanjian ini disebut sebagai <strong>"PIHAK PERTAMA"</strong> atau <strong>"AKUADA"</strong>.
                    </p>
                </div>

                <!-- PIHAK KEDUA -->
                <div class="p-4 rounded-2xl bg-purple-50/40 border border-purple-200 space-y-1.5">
                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-900 font-extrabold text-[10px] uppercase">PIHAK KEDUA (CORPORATE BUYER TERVERIFIKASI)</span>
                    <h4 class="text-sm font-black text-slate-900 pt-1"><?= esc($buyerLegalName) ?></h4>
                    <div class="space-y-1 text-[11px] text-slate-600">
                        <p><strong>Diwakili Oleh:</strong> <?= esc($picName) ?></p>
                        <p><strong>Jabatan:</strong> <?= esc($picPosition) ?></p>
                        <p><strong>NPWP / NIB:</strong> <span class="font-mono"><?= esc($company['npwp'] ?: '-') ?></span> / <span class="font-mono"><?= esc($company['nib'] ?: '-') ?></span></p>
                        <p><strong>Alamat Kedudukan:</strong> <?= esc($company['address'] ?: '-') ?>, <?= esc($company['city'] ?? '') ?> <?= esc($company['province'] ?? '') ?></p>
                    </div>
                    <p class="text-[11px] text-slate-500 pt-1 italic">
                        Selanjutnya dalam Perjanjian ini disebut sebagai <strong>"PIHAK KEDUA"</strong> atau <strong>"MITRA BUYER"</strong>.
                    </p>
                </div>
            </div>

            <p>
                PIHAK PERTAMA dan PIHAK KEDUA (selanjutnya secara bersama-sama disebut <strong>"PARA PIHAK"</strong>) sepakat untuk mengikatkan diri dalam Perjanjian Kerjasama Pengadaan Barang dan Jasa Korporat dengan ketentuan dan pasal-pasal sebagai berikut:
            </p>
        </div>

        <!-- ISI PASAL-PASAL KONTRAK KERJASAMA -->
        <div class="space-y-6 text-justify">

            <!-- PASAL 1 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 1</h4>
                    <p class="font-bold text-purple-900 text-xs">RUANG LINGKUP KERJASAMA PENGADAAN SATU PINTU</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>PIHAK PERTAMA menunjuk dan mengesahkan PIHAK KEDUA sebagai <strong>Mitra Corporate Buyer Terverifikasi</strong> yang berhak melakukan pengadaan barang operasional, perlengkapan kantor, barang kustomisasi (<em>custom-made</em>), maupun tender pengadaan melalui platform resmi AkuAda.</li>
                    <li>AkuAda bertindak sebagai <strong>Agregator Pengadaan Satu Pintu (<em>Managed B2B Marketplace</em>)</strong> yang menjembatani seluruh proses pemesanan, kurasi desain, penjaminan mutu vendor, penerbitan dokumen serah terima (BAST), hingga penagihan faktur resmi kepada PIHAK KEDUA.</li>
                    <li>Seluruh komunikasi, negosiasi, dan koordinasi pesanan wajib dilakukan secara terpusat melalui saluran resmi PIHAK PERTAMA demi menjamin keamanan transaksi dan perlindungan hukum bagi PARA PIHAK.</li>
                </ol>
            </div>

            <!-- PASAL 2 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 2</h4>
                    <p class="font-bold text-purple-900 text-xs">HAK ISTIMEWA DAN RINCIAN BENEFIT MITRA CORPORATE BUYER</p>
                </div>
                <p>Sebagai perusahaan yang telah lulus verifikasi kelengkapan legalitas (KYC Korporat), PIHAK KEDUA berhak memperoleh benefit eksklusif dari PIHAK PERTAMA yang mencakup:</p>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li><strong>Akses Surat Penawaran Harga Resmi (Official Quotation):</strong> Hak membuka, mengunduh, dan mencetak Surat Penawaran Harga Resmi ber-KOP dan bertandatangan pejabat berwenang untuk keperluan persetujuan anggaran internal PIHAK KEDUA, serta hak mengajukan tender <em>Request for Quotation</em> (RFQ).</li>
                    <li><strong>Fasilitas Plafon Kredit Tempo (Term of Payment / TOP):</strong> Pemberian plafon kredit pengadaan sebesar <strong><?= format_rupiah($creditLimit) ?></strong> tanpa bunga (0%) dengan pilihan masa jatuh tempo <strong><?= esc($topRangeText) ?></strong>.</li>
                    <li><strong>Program Reward Cashback Pengadaan (<?= number_format($cashbackRate, 1) ?>%):</strong> Pemberian insentif <em>cashback</em> tunai sebesar <strong><?= number_format($cashbackRate, 1) ?>% (<?= number_format($cashbackRate, 1) ?> persen)</strong> dari nilai Dasar Pengenaan Pajak (DPP) barang pada setiap Purchase Order (PO) yang telah selesai dan dilunasi.</li>
                    <li><strong>Harga Grosir Bertingkat (Volume Tiering):</strong> Potongan harga otomatis berdasarkan kuantitas pesanan sesuai tabel harga grosir B2B pada katalog platform.</li>
                    <li><strong>Layanan Kurasi Desain &amp; Persetujuan Mockup Pra-Produksi:</strong> Fasilitas pemeriksaan desain barang kustom dan pengiriman sampel visual (<em>Mockup Proofing</em>) untuk disetujui (ACC) oleh PIHAK KEDUA sebelum produksi massal dimulai.</li>
                    <li><strong>Fasilitas Pengiriman Logistik Korporat:</strong> Pembebasan biaya pengiriman standar untuk wilayah layanan <strong><?= esc($freeShipRegion) ?></strong> sesuai kebijakan operasional sistem yang berlaku.</li>
                </ol>
            </div>

            <!-- PASAL 3 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 3</h4>
                    <p class="font-bold text-purple-900 text-xs">TATA CARA SURAT PENAWARAN HARGA (QUOTATION), PURCHASE ORDER (PO), DAN DESAIN CUSTOM</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>PIHAK KEDUA dapat memilih barang di keranjang pengadaan untuk menerbitkan <strong>Surat Penawaran Harga Resmi (Quotation)</strong> yang berlaku selama 14 (empat belas) hari kalender sejak tanggal diterbitkan.</li>
                    <li>Pemesanan dinyatakan mengikat secara sah apabila PIHAK KEDUA telah menerbitkan <strong>Purchase Order (PO)</strong> melalui sistem checkout AkuAda dengan mencantumkan Nomor PO Internal perusahaan.</li>
                    <li>Untuk pesanan yang membutuhkan kustomisasi desain/logo/spesifikasi khusus, PIHAK KEDUA wajib mengunggah file desain dan rincian spesifikasi kepada PIHAK PERTAMA. Mitra pemasok hanya akan melakukan produksi massal setelah PIHAK KEDUA menekan persetujuan (<strong>ACC Desain/Mockup</strong>) pada dashboard pesanan.</li>
                </ol>
            </div>

            <!-- PASAL 4 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 4</h4>
                    <p class="font-bold text-purple-900 text-xs">KETENTUAN PEMBAYARAN, TERMIN KREDIT (TOP), DAN KUOTA NOTA BERJALAN</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>PIHAK KEDUA dapat memilih metode pembayaran langsung melalui <strong>Corporate Virtual Account / Rekening Escrow Resmi AkuAda</strong> atau menggunakan fasilitas kredit tempo (<strong>TOP <?= esc($topRangeText) ?></strong> / <strong>Corporate PO On-Account</strong>).</li>
                    <li>Penggunaan fasilitas kredit tempo (TOP) dibatasi maksimal sebanyak <strong><?= $maxUnpaidNotes ?> (<?= $maxUnpaidNotes === 3 ? 'tiga' : $maxUnpaidNotes ?>) nota tagihan yang belum lunas</strong> dalam satu periode berjalan serta tidak melampaui sisa plafon kredit perusahaan.</li>
                    <li>Apabila kuota <?= $maxUnpaidNotes ?> nota berjalan telah tercapai namun PIHAK KEDUA membutuhkan pengadaan mendesak, PIHAK KEDUA dapat mengajukan fitur <strong>Open PO Over-Limit</strong> yang akan ditinjau oleh Superadmin / Risk Manager PIHAK PERTAMA, atau melakukan pembayaran melalui Virtual Account.</li>
                    <li>Seluruh pembayaran tagihan wajib ditransfer murni ke rekening bank resmi atas nama <strong><?= esc($issuerCompany) ?></strong> yang tertera pada dokumen Faktur/Invoice resmi.</li>
                </ol>
            </div>

            <!-- PASAL 5 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 5</h4>
                    <p class="font-bold text-purple-900 text-xs">MEKANISME PERHITUNGAN DAN PENCAIRAN REWARD CASHBACK</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>Estimasi nilai <em>cashback</em> sebesar <strong><?= number_format($cashbackRate, 1) ?>% dari subtotal DPP barang</strong> dihitung dan ditampilkan secara transparan sejak PO diterbitkan dan saat Berita Acara Serah Terima (BAST) disahkan.</li>
                    <li>Saldo <em>cashback</em> resmi dikreditkan ke dompet <strong>Saldo Cashback Aktif</strong> milik PIHAK KEDUA segera setelah pembayaran tagihan PO terkait dinyatakan <strong>LUNAS dan Terverifikasi</strong> oleh Tim Finance PIHAK PERTAMA.</li>
                    <li>PIHAK KEDUA dapat mengajukan penarikan (<em>withdrawal</em>) saldo <em>cashback</em> kapan saja melalui menu <strong>Saldo Cashback &amp; Tarik Dana</strong> untuk ditransfer ke rekening bank yang ditunjuk tanpa potongan biaya administrasi.</li>
                </ol>
            </div>

            <!-- PASAL 6 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 6</h4>
                    <p class="font-bold text-purple-900 text-xs">SERAH TERIMA BARANG (BAST), JAMINAN MUTU, DAN KLAIM RETUR</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>Setiap pengiriman barang disertai dengan Surat Jalan dan dokumen <strong>Berita Acara Serah Terima (BAST)</strong> resmi.</li>
                    <li>Pada saat barang tiba di lokasi gudang/kantor PIHAK KEDUA, petugas penerima wajib memeriksa kesesuaian kuantitas dan kondisi fisik barang sebelum melakukan pengesahan BAST pada sistem.</li>
                    <li>Apabila ditemukan barang rusak, cacat produksi, atau tidak sesuai spesifikasi PO, PIHAK KEDUA berhak mengajukan <strong>Tiket Klaim Retur</strong> dengan melampirkan bukti foto fisik untuk memperoleh penggantian unit baru (<em>replacement</em>) atau pemotongan nilai tagihan (<em>credit note / refund</em>).</li>
                </ol>
            </div>

            <!-- PASAL 7 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 7</h4>
                    <p class="font-bold text-purple-900 text-xs">KERAHASIAAN DATA, LARANGAN TRANSAKSI PINTAS (ANTI-BYPASS), DAN PENUTUP</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>PARA PIHAK wajib menjaga kerahasiaan data harga khusus korporat, dokumen legalitas, serta spesifikasi desain yang dipertukarkan di dalam platform.</li>
                    <li>PIHAK KEDUA sepakat untuk tidak melakukan transaksi pintas (<em>bypass</em>) secara langsung di luar platform AkuAda dengan mitra pemasok yang terdaftar di ekosistem AkuAda.</li>
                    <li>Perjanjian Kerjasama ini berlaku efektif sejak tanggal disahkannya verifikasi legalitas PIHAK KEDUA dan tetap berlaku selama akun kemitraan PIHAK KEDUA berstatus aktif di platform AkuAda.</li>
                </ol>
            </div>

        </div>

        <!-- BLOK TANDA TANGAN PENGESAHAN KONTRAK KERJASAMA -->
        <div class="pt-6 border-t-2 border-slate-300 grid grid-cols-1 sm:grid-cols-2 gap-8 text-center">
            <!-- Tanda Tangan Pihak Pertama -->
            <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between space-y-6">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">PIHAK PERTAMA</span>
                    <strong class="text-sm font-black text-slate-900 block mt-0.5"><?= esc($issuerCompany) ?></strong>
                </div>

                <div class="py-2 flex flex-col items-center justify-center">
                    <div class="px-4 py-2 rounded-xl border-2 border-purple-600/70 bg-purple-50/80 text-purple-900 font-mono text-[10px] font-bold shadow-2xs rotate-[-2deg]">
                        ✓ DIGITALLY SIGNED &amp; VERIFIED<br>
                        AKUADA COMPLIANCE SYSTEM
                    </div>
                </div>

                <div>
                    <strong class="text-xs font-black text-slate-900 underline block"><?= esc($signerName) ?></strong>
                    <span class="text-[11px] text-slate-600 block"><?= esc($signerPosition) ?></span>
                </div>
            </div>

            <!-- Tanda Tangan Pihak Kedua -->
            <div class="p-5 rounded-2xl border border-purple-200 bg-purple-50/20 flex flex-col justify-between space-y-6">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-700 block">PIHAK KEDUA (MITRA BUYER)</span>
                    <strong class="text-sm font-black text-slate-900 block mt-0.5"><?= esc($buyerLegalName) ?></strong>
                </div>

                <div class="py-2 flex flex-col items-center justify-center">
                    <div class="px-4 py-2 rounded-xl border-2 border-emerald-600/70 bg-emerald-50/80 text-emerald-900 font-mono text-[10px] font-bold shadow-2xs">
                        ✓ TERVERIFIKASI LEGALITAS BADAN USAHA<br>
                        NPWP: <?= esc($company['npwp'] ?: '-') ?>
                    </div>
                </div>

                <div>
                    <strong class="text-xs font-black text-slate-900 underline block"><?= esc($picName) ?></strong>
                    <span class="text-[11px] text-slate-600 block"><?= esc($picPosition) ?></span>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>
