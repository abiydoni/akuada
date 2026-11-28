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

$contractDate    = !empty($company['updated_at']) ? $company['updated_at'] : ($company['created_at'] ?? date('Y-m-d'));
$contractEndDate = date('Y-m-d', strtotime($contractDate . ' +1 year'));
$contractNumber  = 'PKS/AKUADA-CORP/' . date('Y/m', strtotime($contractDate)) . '/' . str_pad((string)$company['id'], 4, '0', STR_PAD_LEFT);

$creditLimit    = (float)($company['credit_limit_top'] ?? get_system_setting('default_credit_limit', 100000000));
$cashbackRate   = (float)get_buyer_cashback_rate((int)$company['id']);
$maxUnpaidNotes = (int)get_max_unpaid_notes_limit();
$allowedTop     = get_allowed_top_terms();
$topRangeText   = !empty($allowedTop) ? (min($allowedTop) . ' hingga ' . max($allowedTop) . ' Hari Kalender') : '14 hingga 90 Hari Kalender';
$freeShipRegion = get_system_setting('free_shipping_region', 'Corporate Jabodetabek');
$picName        = $user['name'] ?? ($company['pic_name'] ?? session('user_name') ?? 'Pimpinan Pengadaan');
$picPosition    = $user['position'] ?? 'PIC Procurement / Kuasa Direksi';
$buyerEmail     = $company['email'] ?? ($user['email'] ?? '-');
$buyerPhone     = $company['pic_phone'] ?? ($company['phone'] ?? '-');

$hariIndo = [
    'Sunday'    => 'Minggu',
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
];
$bulanIndo = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
];
$tsContract = strtotime($contractDate);
$namaHari   = $hariIndo[date('l', $tsContract)] ?? date('l', $tsContract);
$tglIndo    = date('d', $tsContract) . ' ' . ($bulanIndo[(int)date('n', $tsContract)] ?? date('F', $tsContract)) . ' ' . date('Y', $tsContract);
$tglBerakhirIndo = date('d', strtotime($contractEndDate)) . ' ' . ($bulanIndo[(int)date('n', strtotime($contractEndDate))] ?? date('F', strtotime($contractEndDate))) . ' ' . date('Y', strtotime($contractEndDate));
?>

<div class="space-y-6">
    <!-- Top Header & Print Button (Hidden on Print) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 print:hidden">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300 text-[10px] font-extrabold uppercase tracking-wider flex items-center gap-1">
                    <span class="material-symbols-outlined text-[13px]">verified</span> Kontrak Korporat Terverifikasi
                </span>
                <span class="font-mono text-xs font-bold text-purple-700"><?= esc($contractNumber) ?></span>
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 text-[10px] font-bold">Masa Berlaku: 1 Tahun (Auto-Renewal)</span>
            </div>
            <h1 class="text-xl font-extrabold text-slate-900 mt-1">Perjanjian Kerjasama Induk Pengadaan Korporat (Master B2B Procurement Agreement)</h1>
            <p class="text-xs text-slate-500 mt-0.5">Naskah hukum kontrak kerjasama pengadaan barang &amp; jasa korporat lengkap 17 Pasal beserta rincian hak istimewa dan fasilitas kemitraan AkuAda.</p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
            <?php if (!empty($isAdminView)): ?>
                <a href="<?= base_url('admin/buyers') ?>" class="px-3.5 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                    <span>Kembali ke Daftar Buyer</span>
                </a>
            <?php endif; ?>
            <button type="button" onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-800 hover:to-indigo-800 text-white font-extrabold text-xs transition flex items-center gap-1.5 shadow-md">
                <span class="material-symbols-outlined text-[18px]">print</span>
                <span>Cetak / Simpan PDF Kontrak Resmi</span>
            </button>
        </div>
    </div>

    <!-- BAGIAN 1: RINGKASAN EKSEKUTIF & DETAIL BENEFIT EKSKLUSIF YANG DIBERIKAN AKUADA -->
    <div class="bg-gradient-to-br from-slate-900 via-purple-950 to-indigo-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl space-y-6 print:bg-white print:text-slate-900 print:border-2 print:border-slate-800 print:shadow-none">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-white/15 print:border-slate-300">
            <div class="space-y-1">
                <span class="px-3 py-1 rounded-full bg-pink-500/20 text-pink-300 border border-pink-400/30 text-[10px] font-black uppercase tracking-widest inline-block print:bg-slate-100 print:text-slate-800 print:border-slate-400">
                    Lampiran I — Hak Istimewa &amp; Fasilitas Mitra Korporat
                </span>
                <h2 class="text-lg sm:text-xl font-black tracking-tight text-white print:text-slate-900">
                    Detail Benefit &amp; Fasilitas Resmi AkuAda untuk <?= esc($buyerLegalName) ?>
                </h2>
                <p class="text-xs text-purple-200 print:text-slate-600">
                    Berlaku Efektif: <strong><?= esc($tglIndo) ?> s/d <?= esc($tglBerakhirIndo) ?></strong> (Diperpanjang Otomatis Selama Tidak Ada Pemutusan Tertulis).
                </p>
            </div>
            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-3.5 border border-white/20 text-left md:text-right flex-shrink-0 print:border-slate-300">
                <span class="text-[10px] uppercase font-bold text-purple-200 block print:text-slate-500">Status Kontrak &amp; Legalitas</span>
                <strong class="text-sm font-black text-emerald-300 flex items-center md:justify-end gap-1 mt-0.5 print:text-emerald-800">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    SAH &amp; MENGIKAT (17 PASAL)
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
                        Bunga 0% • Tanpa Agunan
                    </span>
                </div>
                <h3 class="font-extrabold text-sm text-white print:text-slate-900">1. Plafon Kredit Tempo (TOP <?= esc($topRangeText) ?>)</h3>
                <p class="text-purple-100/90 text-[11px] leading-relaxed print:text-slate-700">
                    Alokasi plafon kredit korporat sebesar <strong class="text-yellow-300 font-mono print:text-slate-900"><?= format_rupiah($creditLimit) ?></strong> dengan kuota hingga <strong><?= $maxUnpaidNotes ?> nota tagihan berjalan per bulan</strong> serta opsi pengajuan <em>Over-Limit</em> untuk pengadaan mendesak.
                </p>
            </div>

            <!-- Benefit 2: Program Cashback Korporat -->
            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2 print:bg-slate-50 print:border-slate-300">
                <div class="flex items-center justify-between">
                    <span class="w-9 h-9 rounded-xl bg-pink-500/30 text-pink-200 flex items-center justify-center font-bold print:bg-pink-100 print:text-pink-900">
                        <span class="material-symbols-outlined text-[20px]">savings</span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-pink-500/30 text-pink-200 font-mono font-bold text-[10px] border border-pink-400/30 print:bg-pink-100 print:text-pink-900">
                        Reward Tunai <?= number_format($cashbackRate, 2) ?>%
                    </span>
                </div>
                <h3 class="font-extrabold text-sm text-white print:text-slate-900">2. Cashback Tunai <?= number_format($cashbackRate, 2) ?>% Setiap PO Lunas</h3>
                <p class="text-purple-100/90 text-[11px] leading-relaxed print:text-slate-700">
                    Insentif <em>cashback</em> tunai sebesar <strong><?= number_format($cashbackRate, 2) ?>% dari nilai DPP barang</strong> untuk setiap PO yang selesai (BAST sah) dan telah dibayar lunas, dapat ditarik ke rekening bank tanpa potongan biaya.
                </p>
            </div>

            <!-- Benefit 3: Surat Penawaran Harga Resmi & Diskon Tiering -->
            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2 print:bg-slate-50 print:border-slate-300">
                <div class="flex items-center justify-between">
                    <span class="w-9 h-9 rounded-xl bg-blue-500/30 text-blue-200 flex items-center justify-center font-bold print:bg-blue-100 print:text-blue-900">
                        <span class="material-symbols-outlined text-[20px]">request_quote</span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-500/30 text-blue-200 font-mono font-bold text-[10px] border border-blue-400/30 print:bg-blue-100 print:text-blue-900">
                        Quotation &amp; RFQ
                    </span>
                </div>
                <h3 class="font-extrabold text-sm text-white print:text-slate-900">3. Surat Penawaran Resmi &amp; Harga Grosir B2B</h3>
                <p class="text-purple-100/90 text-[11px] leading-relaxed print:text-slate-700">
                    Akses penuh penerbitan <strong>Surat Penawaran Harga Resmi (Official Quotation)</strong> ber-KOP untuk persetujuan anggaran direksi, pengajuan tender RFQ, serta potongan harga grosir bertingkat (<em>volume tiering</em>).
                </p>
            </div>

            <!-- Benefit 4: Kurasi Desain Custom & Mockup Pra-Produksi -->
            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2 print:bg-slate-50 print:border-slate-300">
                <div class="flex items-center justify-between">
                    <span class="w-9 h-9 rounded-xl bg-amber-500/30 text-amber-200 flex items-center justify-center font-bold print:bg-amber-100 print:text-amber-900">
                        <span class="material-symbols-outlined text-[20px]">palette</span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-500/30 text-amber-200 font-mono font-bold text-[10px] border border-amber-400/30 print:bg-amber-100 print:text-amber-900">
                        Zero-Mistake Proofing
                    </span>
                </div>
                <h3 class="font-extrabold text-sm text-white print:text-slate-900">4. Mediasi Desain Custom &amp; Mockup Approval</h3>
                <p class="text-purple-100/90 text-[11px] leading-relaxed print:text-slate-700">
                    Proteksi kerahasiaan blueprint/logo perusahaan melalui kurasi Tim AkuAda serta fasilitas <strong>Preview Mockup Pra-Produksi</strong> yang wajib di-ACC oleh Buyer sebelum diproduksi massal oleh vendor.
                </p>
            </div>

            <!-- Benefit 5: Proteksi Retur, QC & Logistik -->
            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2 print:bg-slate-50 print:border-slate-300">
                <div class="flex items-center justify-between">
                    <span class="w-9 h-9 rounded-xl bg-emerald-500/30 text-emerald-200 flex items-center justify-center font-bold print:bg-emerald-100 print:text-emerald-900">
                        <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/30 text-emerald-200 font-mono font-bold text-[10px] border border-emerald-400/30 print:bg-emerald-100 print:text-emerald-900">
                        SLA &amp; Garansi Retur
                    </span>
                </div>
                <h3 class="font-extrabold text-sm text-white print:text-slate-900">5. Subsidi Logistik &amp; Garansi Penggantian Barang</h3>
                <p class="text-purple-100/90 text-[11px] leading-relaxed print:text-slate-700">
                    Fasilitas pengiriman logistik korporat (bebas biaya kirim area <strong><?= esc($freeShipRegion) ?></strong> sesuai ketentuan sistem), pelacakan pengiriman <em>real-time</em> dengan bukti foto POD kurir, serta garansi ganti barang baru / <em>Credit Note</em>.
                </p>
            </div>

            <!-- Benefit 6: Dokumen Legal & Account Manager Satu Pintu -->
            <div class="p-4 rounded-2xl bg-white/10 border border-white/15 space-y-2 print:bg-slate-50 print:border-slate-300">
                <div class="flex items-center justify-between">
                    <span class="w-9 h-9 rounded-xl bg-indigo-500/30 text-indigo-200 flex items-center justify-center font-bold print:bg-indigo-100 print:text-indigo-900">
                        <span class="material-symbols-outlined text-[20px]">verified_user</span>
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/30 text-indigo-200 font-mono font-bold text-[10px] border border-indigo-400/30 print:bg-indigo-100 print:text-indigo-900">
                        Audit &amp; Tax Ready
                    </span>
                </div>
                <h3 class="font-extrabold text-sm text-white print:text-slate-900">6. Dokumen Audit, BAST Digital &amp; Layanan 1-on-1</h3>
                <p class="text-purple-100/90 text-[11px] leading-relaxed print:text-slate-700">
                    Penerbitan otomatis Faktur Tagihan (Invoice), Surat Jalan, BAST Digital, dan dokumen perpajakan B2B yang siap diaudit, didampingi langsung oleh Account Manager AkuAda melalui Live Chat terenkripsi.
                </p>
            </div>
        </div>
    </div>

    <!-- BAGIAN 2: NASKAH HUKUM KONTRAK KERJASAMA KORPORAT LENGKAP (17 PASAL) -->
    <div class="bg-white rounded-3xl border-2 border-slate-200 shadow-xl p-6 sm:p-10 lg:p-14 space-y-8 text-xs leading-relaxed text-slate-800 print:border-none print:shadow-none print:p-0">
        
        <!-- KOP SURAT RESMI KONTRAK KERJASAMA -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 pb-6 border-b-4 border-double border-slate-900">
            <div class="flex items-center gap-3.5">
                <img src="<?= base_url('apple-touch-icon.png') ?>" alt="AkuAda" class="w-14 h-14 rounded-2xl object-contain border border-slate-200 p-1">
                <div>
                    <h2 class="text-xl font-black tracking-tight text-slate-900 uppercase"><?= esc($issuerCompany) ?></h2>
                    <p class="text-[11px] font-bold text-purple-800 uppercase tracking-wider">Platform Agregator &amp; E-Procurement B2B Korporat Terpadu</p>
                    <p class="text-[11px] text-slate-500"><?= esc($issuerAddress) ?> | NPWP: <span class="font-mono"><?= esc($issuerNpwp) ?></span></p>
                </div>
            </div>
            <div class="text-left sm:text-right space-y-1 bg-slate-50 p-3.5 rounded-2xl border border-slate-200">
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-slate-400 block">No. Perjanjian Induk Korporat</span>
                <strong class="font-mono text-sm font-black text-purple-900 block"><?= esc($contractNumber) ?></strong>
                <span class="text-[10px] text-emerald-700 font-bold block">Mulai Berlaku: <?= esc($tglIndo) ?></span>
                <span class="text-[10px] text-slate-500 font-semibold block">Masa Berlaku: 1 Tahun (Auto-Renewal)</span>
            </div>
        </div>

        <!-- JUDUL KONTRAK -->
        <div class="text-center space-y-1.5 py-2">
            <h3 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-wide underline decoration-2 underline-offset-4">
                PERJANJIAN KERJASAMA INDUK PENGADAAN BARANG DAN JASA KORPORAT (B2B)
            </h3>
            <p class="text-xs font-bold text-slate-600">
                NOMOR: <span class="font-mono text-slate-900"><?= esc($contractNumber) ?></span>
            </p>
        </div>

        <!-- KOMPARISI & IDENTITAS PARA PIHAK -->
        <div class="space-y-4 text-justify">
            <p>
                Pada hari ini, <strong><?= esc($namaHari) ?></strong>, tanggal <strong><?= esc($tglIndo) ?></strong>, bertempat di Jakarta, setelah dilakukannya proses verifikasi kepatuhan badan hukum (<em>Corporate Know Your Customer / KYC</em>) dan pengesahan dokumen legalitas perusahaan pada sistem elektronik <strong>AkuAda B2B E-Procurement Hub</strong>, kami yang bertanda tangan di bawah ini:
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- PIHAK PERTAMA -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-300 space-y-2">
                    <span class="px-2.5 py-0.5 rounded bg-purple-100 text-purple-900 font-extrabold text-[10px] uppercase tracking-wider">I. PIHAK PERTAMA (PENYELENGGARA &amp; AGREGATOR)</span>
                    <h4 class="text-sm font-black text-slate-900 pt-1"><?= esc($issuerCompany) ?></h4>
                    <div class="space-y-1 text-[11px] text-slate-700">
                        <p><strong>Pejabat Penandatangan:</strong> <?= esc($signerName) ?></p>
                        <p><strong>Jabatan Resmi:</strong> <?= esc($signerPosition) ?></p>
                        <p><strong>NPWP Perseroan:</strong> <span class="font-mono"><?= esc($issuerNpwp) ?></span></p>
                        <p><strong>Alamat Kedudukan:</strong> <?= esc($issuerAddress) ?></p>
                    </div>
                    <p class="text-[11px] text-slate-600 pt-1 leading-relaxed">
                        Dalam hal ini bertindak dalam jabatannya tersebut di atas, sah mewakili Direksi untuk dan atas nama <strong><?= esc($issuerCompany) ?></strong>, selanjutnya dalam Perjanjian ini disebut sebagai <strong>"PIHAK PERTAMA"</strong> atau <strong>"AKUADA"</strong>.
                    </p>
                </div>

                <!-- PIHAK KEDUA -->
                <div class="p-5 rounded-2xl bg-purple-50/40 border border-purple-300 space-y-2">
                    <span class="px-2.5 py-0.5 rounded bg-emerald-100 text-emerald-900 font-extrabold text-[10px] uppercase tracking-wider">II. PIHAK KEDUA (CORPORATE BUYER TERVERIFIKASI)</span>
                    <h4 class="text-sm font-black text-slate-900 pt-1"><?= esc($buyerLegalName) ?></h4>
                    <div class="space-y-1 text-[11px] text-slate-700">
                        <p><strong>Pejabat / PIC Berwenang:</strong> <?= esc($picName) ?></p>
                        <p><strong>Jabatan Resmi:</strong> <?= esc($picPosition) ?></p>
                        <p><strong>NPWP / NIB Badan Usaha:</strong> <span class="font-mono"><?= esc($company['npwp'] ?: '-') ?></span> / <span class="font-mono"><?= esc($company['nib'] ?: '-') ?></span></p>
                        <p><strong>Email &amp; Telepon Resmi:</strong> <span class="font-mono"><?= esc($buyerEmail) ?></span> | <span class="font-mono"><?= esc($buyerPhone) ?></span></p>
                        <p><strong>Alamat Kedudukan / Serah:</strong> <?= esc($company['address'] ?: '-') ?>, <?= esc($company['city'] ?? '') ?> <?= esc($company['province'] ?? '') ?></p>
                    </div>
                    <p class="text-[11px] text-slate-600 pt-1 leading-relaxed">
                        Dalam hal ini bertindak dalam jabatannya untuk dan atas nama <strong><?= esc($buyerLegalName) ?></strong>, selanjutnya dalam Perjanjian ini disebut sebagai <strong>"PIHAK KEDUA"</strong> atau <strong>"MITRA BUYER"</strong>.
                    </p>
                </div>
            </div>

            <p>
                PIHAK PERTAMA dan PIHAK KEDUA secara bersama-sama selanjutnya disebut sebagai <strong>"PARA PIHAK"</strong>, dan secara sendiri-sendiri disebut sebagai <strong>"PIHAK"</strong>. PARA PIHAK terlebih dahulu menerangkan hal-hal sebagai berikut (<strong>Konsiderans / Premis</strong>):
            </p>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-1.5 text-[11px] text-slate-700">
                <ol class="list-[upper-alpha] pl-5 space-y-1.5">
                    <li><strong>BAHWA</strong>, PIHAK PERTAMA adalah badan usaha resmi yang menyelenggarakan dan mengelola platform perdagangan elektronik pengadaan barang dan jasa antar-perusahaan (<em>Business-to-Business / B2B E-Procurement Agregator</em>) dengan nama <strong>AkuAda</strong>, yang mengintegrasikan jaringan pemasok/prinsipal terverifikasi, sistem manajemen pesanan (PO), kurasi kustomisasi desain, pembiayaan tempo korporat (<em>Term of Payment</em>), serta administrasi dokumen serah terima (BAST) dan perpajakan.</li>
                    <li><strong>BAHWA</strong>, PIHAK KEDUA adalah perusahaan/badan usaha yang membutuhkan pasokan barang operasional, perlengkapan kantor, barang cetakan/kustomisasi (<em>custom-made</em>), maupun pengadaan proyek secara berkelanjutan, transparan, akuntabel, dan dilengkapi dengan fasilitas pembayaran tempo serta dokumen legalitas resmi.</li>
                    <li><strong>BAHWA</strong>, PIHAK KEDUA telah menyerahkan dokumen legalitas badan usaha (NPWP, NIB, Akta Perusahaan, dan Identitas Penanggung Jawab) kepada PIHAK PERTAMA, dan PIHAK PERTAMA telah memverifikasi serta menyetujui kelayakan PIHAK KEDUA untuk menerima fasilitas kemitraan pengadaan korporat.</li>
                    <li><strong>BAHWA</strong>, berdasarkan pertimbangan-pertimbangan tersebut di atas, PARA PIHAK sepakat untuk mengadakan dan menandatangani Perjanjian Kerjasama Induk Pengadaan Barang dan Jasa Korporat ini (selanjutnya disebut <strong>"Perjanjian"</strong>) dengan syarat-syarat dan ketentuan-ketentuan yang diatur dalam 17 (tujuh belas) Pasal di bawah ini:</li>
                </ol>
            </div>
        </div>

        <!-- ISI 17 PASAL KONTRAK KERJASAMA KORPORAT -->
        <div class="space-y-6 text-justify">

            <!-- PASAL 1 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 1</h4>
                    <p class="font-bold text-purple-900 text-xs">DEFINISI DAN PENAFSIRAN ISTILAH</p>
                </div>
                <p>Kecuali ditentukan lain secara tegas dalam konteks kalimatnya, istilah-istilah yang diawali huruf kapital dalam Perjanjian ini memiliki arti sebagai berikut:</p>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li><strong>"Platform AkuAda"</strong> adalah sistem aplikasi web pengadaan B2B resmi yang dikelola oleh PIHAK PERTAMA untuk memfasilitasi katalog produk, permintaan penawaran (RFQ), penerbitan Surat Penawaran Harga Resmi (Quotation), penerbitan Purchase Order (PO), pelacakan pengiriman, hingga penyelesaian tagihan.</li>
                    <li><strong>"Mitra Vendor / Pemasok"</strong> adalah produsen, distributor, atau pemasok barang/jasa yang telah terikat kontrak kemitraan dengan PIHAK PERTAMA untuk menyediakan dan mengirimkan barang pesanan PIHAK KEDUA melalui mediasi satu pintu PIHAK PERTAMA.</li>
                    <li><strong>"Surat Penawaran Harga Resmi (Official Quotation)"</strong> adalah dokumen penawaran harga resmi ber-KOP yang diterbitkan melalui sistem AkuAda atas item barang yang dipilih oleh PIHAK KEDUA sebagai dasar persetujuan anggaran internal sebelum penerbitan PO.</li>
                    <li><strong>"Request for Quotation (RFQ)"</strong> adalah pengajuan permintaan penawaran harga khusus atau tender pengadaan volume besar/kustom yang diajukan oleh PIHAK KEDUA melalui Platform AkuAda.</li>
                    <li><strong>"Purchase Order (PO)"</strong> adalah surat pesanan pembelian resmi yang diterbitkan secara elektronik oleh PIHAK KEDUA melalui Platform AkuAda yang bersifat mengikat secara hukum sebagai dasar pelaksanaan pengadaan dan penagihan.</li>
                    <li><strong>"Mockup Pra-Produksi (Design Proofing)"</strong> adalah sampel visual, cetak biru (<em>blueprint</em>), atau pratinjau desain barang kustom yang disiapkan oleh Mitra Vendor dan dikurasi oleh PIHAK PERTAMA untuk dimintakan persetujuan (ACC) dari PIHAK KEDUA sebelum produksi massal dijalankan.</li>
                    <li><strong>"Berita Acara Serah Terima (BAST)"</strong> adalah dokumen bukti sah secara hukum bahwa seluruh barang dalam PO telah dikirimkan, tiba di lokasi tujuan, diperiksa kuantitas serta kualitas fisiknya, dan diterima dengan baik oleh PIHAK KEDUA.</li>
                    <li><strong>"Term of Payment (TOP)"</strong> adalah fasilitas pembayaran kredit berjangka waktu (tempo) tanpa bunga yang diberikan oleh PIHAK PERTAMA kepada PIHAK KEDUA dengan masa jatuh tempo terhitung sejak pesanan diterbitkan/diserahterimakan sesuai ketentuan sistem.</li>
                    <li><strong>"Dasar Pengenaan Pajak (DPP)"</strong> adalah nilai harga bersih barang/jasa sebelum ditambah Pajak Pertambahan Nilai (PPN) dan sebelum ditambah biaya pengiriman logistik.</li>
                    <li><strong>"Hari Kerja"</strong> adalah hari Senin sampai dengan hari Jumat, tidak termasuk hari Sabtu, hari Minggu, dan hari libur nasional resmi yang ditetapkan oleh Pemerintah Republik Indonesia. <strong>"Hari Kalender"</strong> adalah setiap hari dalam 1 (satu) tahun takwim tanpa terkecuali.</li>
                </ol>
            </div>

            <!-- PASAL 2 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 2</h4>
                    <p class="font-bold text-purple-900 text-xs">MAKSUD, TUJUAN, DAN RUANG LINGKUP KERJASAMA SATU PINTU</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>PIHAK PERTAMA dengan ini mengesahkan PIHAK KEDUA sebagai <strong>Mitra Corporate Buyer Terverifikasi</strong>, dan PIHAK KEDUA menunjuk PIHAK PERTAMA sebagai mitra agregator pengadaan barang dan jasa operasional bagi kebutuhan perusahaan PIHAK KEDUA.</li>
                    <li>Kerjasama ini diselenggarakan dengan sistem <strong>Pengelolaan Satu Pintu (<em>Single Point of Contact &amp; Managed Marketplace</em>)</strong>, di mana:
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>Seluruh pemesanan barang, pengiriman spesifikasi desain, negosiasi RFQ, dan layanan purna jual (klaim retur) dari PIHAK KEDUA wajib diajukan hanya melalui Platform AkuAda dan Tim Account Manager PIHAK PERTAMA;</li>
                            <li>PIHAK PERTAMA bertanggung jawab meneruskan instruksi kerja yang telah terverifikasi kepada Mitra Vendor, mengawasi standar kualitas (<em>Quality Control</em>), serta menerbitkan dokumen penagihan (Faktur/Invoice) atas nama PIHAK PERTAMA kepada PIHAK KEDUA;</li>
                            <li>PIHAK KEDUA hanya melakukan pembayaran tagihan kepada rekening resmi PIHAK PERTAMA dan tidak memiliki kewajiban maupun hubungan penagihan langsung dengan Mitra Vendor.</li>
                        </ul>
                    </li>
                </ol>
            </div>

            <!-- PASAL 3 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 3</h4>
                    <p class="font-bold text-purple-900 text-xs">HAK ISTIMEWA, FASILITAS, DAN DETAIL BENEFIT MITRA CORPORATE BUYER</p>
                </div>
                <p>Selama Perjanjian ini berlaku dan status verifikasi legalitas PIHAK KEDUA aktif, PIHAK PERTAMA memberikan paket benefit korporat kepada PIHAK KEDUA yang meliputi:</p>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li><strong>Akses Penuh Surat Penawaran Harga Resmi (Official Quotation) &amp; RFQ:</strong> PIHAK KEDUA berhak mengunduh Surat Penawaran Harga Resmi ber-KOP secara instan dari keranjang pengadaan maupun mengajukan tender RFQ untuk kebutuhan internal perusahaan.</li>
                    <li><strong>Alokasi Plafon Kredit Tempo (TOP):</strong> PIHAK KEDUA memperoleh fasilitas plafon kredit pengadaan sebesar <strong><?= format_rupiah($creditLimit) ?></strong> dengan bunga <strong>0% (nol persen)</strong> untuk pilihan termin pembayaran <strong><?= esc($topRangeText) ?></strong> serta kuota hingga <strong><?= $maxUnpaidNotes ?> (<?= $maxUnpaidNotes === 3 ? 'tiga' : $maxUnpaidNotes ?>) nota tagihan berjalan yang belum jatuh tempo/lunas</strong> dalam setiap bulan.</li>
                    <li><strong>Program Insentif Reward Cashback (<?= number_format($cashbackRate, 2) ?>%):</strong> PIHAK KEDUA berhak mendapatkan <em>cashback</em> tunai sebesar <strong><?= number_format($cashbackRate, 2) ?>% dari nilai DPP barang</strong> atas setiap transaksi PO yang telah selesai (BAST sah) dan tagihannya telah dibayar lunas 100%.</li>
                    <li><strong>Harga Grosir Bertingkat (Volume Tiering Discount):</strong> Pemberian diskon harga satuan secara otomatis apabila kuantitas pemesanan memenuhi ambang batas grosir (<em>Minimum Order Quantity / Tier</em>) yang tertera pada katalog produk.</li>
                    <li><strong>Fasilitas Mediasi Desain Custom &amp; Persetujuan Mockup:</strong> Pendampingan teknis pemeriksaan file desain/logo oleh Tim AkuAda serta penyediaan <em>Mockup Pra-Produksi</em> tanpa biaya tambahan untuk memastikan hasil produksi sesuai standar korporat PIHAK KEDUA.</li>
                    <li><strong>Fasilitas Subsidi Pengiriman Logistik:</strong> Pembebasan biaya pengiriman logistik standar untuk pengantaran ke alamat gudang/kantor di wilayah <strong><?= esc($freeShipRegion) ?></strong> sesuai ketentuan yang berlaku pada sistem checkout.</li>
                </ol>
            </div>

            <!-- PASAL 4 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 4</h4>
                    <p class="font-bold text-purple-900 text-xs">HAK DAN KEWAJIBAN MASING-MASING PIHAK</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li><strong>Kewajiban PIHAK PERTAMA:</strong>
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>Menyediakan akses akun Portal Corporate Buyer yang aman dan dapat diakses 24/7 kecuali saat pemeliharaan sistem terjadwal;</li>
                            <li>Memastikan Mitra Vendor memenuhi spesifikasi teknis, kuantitas, keaslian barang, dan jadwal pengiriman sesuai PO yang diterbitkan;</li>
                            <li>Menerbitkan dokumen resmi berupa Surat Penawaran Harga (Quotation), Faktur Tagihan (Invoice), Surat Jalan/BAST Digital, dan dokumen perpajakan sesuai peraturan yang berlaku;</li>
                            <li>Mengkreditkan dan mencairkan dana <em>Reward Cashback</em> yang menjadi hak PIHAK KEDUA secara tepat waktu setelah tagihan PO dinyatakan lunas.</li>
                        </ul>
                    </li>
                    <li><strong>Kewajiban PIHAK KEDUA:</strong>
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>Menjamin kebenaran, keaslian, dan kemutakhiran seluruh dokumen legalitas perusahaan (NPWP, NIB, Akta, SPPKP, KTP PIC) serta segera memperbarui data apabila terdapat perubahan pengurus atau alamat kantor/gudang;</li>
                            <li>Menjaga kerahasiaan kredensial akun login (email dan kata sandi) Portal Corporate Buyer; segala aktivitas penerbitan PO dan pengesahan BAST yang dilakukan menggunakan akun resmi PIHAK KEDUA dianggap sah dan mengikat PIHAK KEDUA sepenuhnya;</li>
                            <li>Melakukan pemeriksaan fisik barang pada saat serah terima dan mengesahkan dokumen BAST tepat waktu;</li>
                            <li>Melaksanakan pembayaran pelunasan tagihan PO secara penuh paling lambat pada tanggal jatuh tempo (<em>Due Date</em>) yang tertera pada Faktur/Invoice resmi.</li>
                        </ul>
                    </li>
                </ol>
            </div>

            <!-- PASAL 5 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 5</h4>
                    <p class="font-bold text-purple-900 text-xs">PROSEDUR SURAT PENAWARAN HARGA (QUOTATION), PENERBITAN PO, DAN PEMBATALAN PESANAN</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li><strong>Masa Berlaku Penawaran (Quotation):</strong> Setiap Surat Penawaran Harga Resmi yang diunduh oleh PIHAK KEDUA berlaku mengikat selama <strong>14 (empat belas) Hari Kalender</strong> sejak tanggal diterbitkan. Setelah melewati batas waktu tersebut, harga dan ketersediaan stok dapat menyesuaikan kondisi pasar terkini.</li>
                    <li><strong>Keabsahan Purchase Order (PO) Elektronik:</strong> Penerbitan PO oleh PIHAK KEDUA melalui tombol konfirmasi checkout di Platform AkuAda merupakan persetujuan kehendak yang sah dan mengikat secara hukum sesuai Pasal 5 dan Pasal 11 Undang-Undang Informasi dan Transaksi Elektronik (UU ITE), tanpa memerlukan tanda tangan basah di atas kertas.</li>
                    <li><strong>Ketentuan Perubahan dan Pembatalan PO:</strong>
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>Permohonan perubahan atau pembatalan PO hanya dapat diajukan secara tertulis melalui Live Chat resmi kepada PIHAK PERTAMA <strong>sebelum</strong> status pesanan berubah menjadi <em>"Proses Produksi / Penyiapan Barang (In Production)"</em> atau <em>"Dalam Pengiriman (Shipped)"</em>;</li>
                            <li>Apabila pesanan (khususnya barang kustomisasi/cetak logo) telah memasuki tahap produksi setelah desain di-ACC, maka PO <strong>tidak dapat dibatalkan secara sepihak</strong>. Apabila PIHAK KEDUA tetap membatalkan secara sepihak, PIHAK KEDUA wajib menanggung biaya produksi yang telah dikeluarkan hingga maksimal 100% (seratus persen) dari nilai PO terkait.</li>
                        </ul>
                    </li>
                </ol>
            </div>

            <!-- PASAL 6 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 6</h4>
                    <p class="font-bold text-purple-900 text-xs">SPESIFIKASI DESAIN CUSTOM, HAK KEKAYAAN INTELEKTUAL (HKI), DAN PERSETUJUAN MOCKUP</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>Untuk pesanan barang kustom (seperti pencetakan logo, kemasan khusus, seragam, atau fabrikasi teknis), PIHAK KEDUA wajib mengunggah file master desain dan rincian spesifikasi (dimensi, kode warna Pantone/CMYK, jenis bahan, dan posisi cetak) melalui panel desain pada halaman Checkout PO, RFQ, atau Detail Pesanan.</li>
                    <li><strong>Alur Kurasi &amp; Mockup Approval:</strong>
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>PIHAK PERTAMA melakukan kurasi atas file desain PIHAK KEDUA (termasuk sanitasi informasi kontak langsung) sebelum meneruskannya kepada Mitra Vendor;</li>
                            <li>Mitra Vendor wajib menyerahkan pratinjau <strong>Mockup Pra-Produksi</strong> melalui PIHAK PERTAMA untuk ditampilkan pada halaman Detail PO PIHAK KEDUA;</li>
                            <li>Setelah PIHAK KEDUA menekan tombol <strong>"Setujui Desain (ACC Produksi Massal)"</strong>, maka spesifikasi desain tersebut bersifat final dan mengikat. Segala kesalahan ejaan teks atau tata letak yang telah tertera jelas pada Mockup yang sudah di-ACC oleh PIHAK KEDUA bukan lagi merupakan tanggung jawab PIHAK PERTAMA maupun Mitra Vendor.</li>
                        </ul>
                    </li>
                    <li><strong>Toleransi Standar Industri:</strong> PIHAK KEDUA memahami dan menyetujui adanya batas toleransi teknis wajar dalam industri percetakan/manufaktur massal sebesar <strong>&plusmn;2% hingga &plusmn;5%</strong> untuk gradasi warna cetak (antara tampilan layar monitor RGB dengan hasil cetak fisik CMYK/bahan tekstil) serta dimensi potong, sepanjang tidak mengubah fungsi utama barang.</li>
                    <li><strong>Jaminan Hak Kekayaan Intelektual (HKI):</strong> PIHAK KEDUA menjamin bahwa seluruh logo, merek dagang, gambar, maupun desain yang diserahkan kepada PIHAK PERTAMA untuk diproduksi adalah milik sah PIHAK KEDUA atau telah memiliki izin lisensi resmi, serta membebaskan PIHAK PERTAMA dari segala tuntutan hukum pihak ketiga atas dugaan pelanggaran HKI.</li>
                </ol>
            </div>

            <!-- PASAL 7 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 7</h4>
                    <p class="font-bold text-purple-900 text-xs">HARGA, TERMIN KREDIT (TOP), BATAS NOTA, DAN SANKSI KETERLAMBATAN PEMBAYARAN</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>Seluruh harga barang dinyatakan dalam mata uang <strong>Rupiah (IDR)</strong>. Total tagihan PO mencakup Subtotal DPP Barang, Pajak Pertambahan Nilai (PPN) sesuai tarif resmi yang berlaku, serta biaya pengiriman logistik (apabila ada).</li>
                    <li><strong>Ketentuan Kuota Kredit Nota &amp; Plafon TOP:</strong>
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>PIHAK KEDUA dapat menerbitkan PO dengan metode kredit tempo (TOP) sepanjang jumlah nota yang belum lunas tidak melebihi <strong><?= $maxUnpaidNotes ?> nota aktif</strong> dan total nilai tagihan belum lunas tidak melampaui plafon kredit <strong><?= format_rupiah($creditLimit) ?></strong>;</li>
                            <li>Apabila batas kuota nota atau plafon kredit tercapai, sistem secara otomatis mengunci opsi TOP reguler. PIHAK KEDUA tetap dapat bertransaksi menggunakan <strong>Corporate Virtual Account (Non-Kredit)</strong> atau mengajukan opsi <strong>Open PO Over-Limit</strong> yang memerlukan persetujuan khusus dari Superadmin PIHAK PERTAMA.</li>
                        </ul>
                    </li>
                    <li><strong>Rekening Pembayaran Sah:</strong> Pembayaran tagihan hanya dinyatakan sah apabila ditransfer ke rekening bank resmi (Rekening Escrow / Virtual Account) atas nama <strong><?= esc($issuerCompany) ?></strong> yang tercantum pada halaman Detail PO dan lembar Invoice. PIHAK PERTAMA tidak bertanggung jawab atas pembayaran yang ditransfer ke rekening pribadi karyawan, kurir, atau rekening langsung milik vendor.</li>
                    <li><strong>Keterlambatan Pembayaran (Overdue):</strong> Apabila PIHAK KEDUA melewati tanggal jatuh tempo pembayaran (<em>Due Date</em>):
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>Fasilitas penerbitan PO baru dengan kredit tempo (TOP) akan dibekukan sementara secara otomatis oleh sistem hingga tagihan yang jatuh tempo dilunasi;</li>
                            <li>Apabila keterlambatan melampaui 14 (empat belas) Hari Kalender setelah tanggal jatuh tempo tanpa adanya kesepakatan restrukturisasi jadwal bayar, PIHAK PERTAMA berhak mengenakan denda keterlambatan sebesar <strong>0,1% (nol koma satu persen) per hari kalender</strong> dari nominal tagihan tertunggak (dengan batas maksimal denda 5% dari nilai tagihan) serta menahan pencairan saldo cashback yang masih mengendap.</li>
                        </ul>
                    </li>
                </ol>
            </div>

            <!-- PASAL 8 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 8</h4>
                    <p class="font-bold text-purple-900 text-xs">TATA CARA PERHITUNGAN, PENGKREDITAN, DAN PENCAIRAN REWARD CASHBACK</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>Besaran Reward Cashback ditetapkan sebesar <strong><?= number_format($cashbackRate, 2) ?>%</strong> dikalikan dengan nilai <strong>Subtotal DPP Barang</strong> (tidak termasuk nilai PPN dan ongkos kirim).</li>
                    <li>Sejak PO diterbitkan hingga BAST disahkan, nominal cashback tercatat pada rincian pesanan dengan status <em>"Estimasi / Menunggu Pelunasan Tagihan PO"</em>.</li>
                    <li>Nominal cashback resmi masuk (dikreditkan) ke dalam <strong>Saldo Cashback Aktif</strong> PIHAK KEDUA secara otomatis pada saat Tim Finance PIHAK PERTAMA mengesahkan status pembayaran PO menjadi <strong>LUNAS (Paid)</strong>.</li>
                    <li>Apabila atas suatu PO terjadi klaim retur yang diselesaikan dengan pengembalian dana penuh (<em>Full Refund / Credit Note</em>), maka estimasi cashback atas item yang diretur tersebut otomatis gugur atau disesuaikan secara proporsional.</li>
                    <li>Pencairan (<em>Withdrawal</em>) Saldo Cashback Aktif dapat diajukan oleh PIHAK KEDUA kapan saja melalui dashboard untuk ditransfer oleh Tim Finance PIHAK PERTAMA dalam waktu maksimal <strong>2 (dua) s/d 3 (tiga) Hari Kerja</strong>.</li>
                </ol>
            </div>

            <!-- PASAL 9 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 9</h4>
                    <p class="font-bold text-purple-900 text-xs">PENGIRIMAN LOGISTIK, SERAH TERIMA BARANG (BAST), DAN PERALIHAN RISIKO</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>Pengiriman barang dilakukan ke alamat gudang/kantor yang dicantumkan oleh PIHAK KEDUA pada saat penerbitan PO. Setiap pengantaran wajib dilengkapi Surat Jalan/BAST dan bukti serah terima lapangan (<em>Proof of Delivery / POD</em>) yang diunggah oleh petugas logistik/kurir.</li>
                    <li><strong>Pemeriksaan Barang &amp; Pengesahan BAST:</strong>
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>Petugas penerima di lokasi PIHAK KEDUA wajib memeriksa jumlah koli/kemasan, kondisi segel, dan kesesuaian fisik barang pada saat serah terima berlangsung;</li>
                            <li>PIHAK KEDUA wajib melakukan konfirmasi <strong>"Selesaikan Pesanan &amp; Sahkan BAST"</strong> atau mengajukan <strong>"Klaim Retur"</strong> melalui dashboard paling lambat dalam waktu <strong>3 &times; 24 (tiga kali dua puluh empat) jam</strong> sejak status pengiriman tercatat tiba di lokasi (<em>Delivered</em>);</li>
                            <li>Apabila dalam batas waktu 3 &times; 24 jam setelah barang tercatat tiba di lokasi (dilengkapi bukti foto POD dan nama penerima) PIHAK KEDUA tidak mengajukan klaim retur atau sanggahan, maka sistem berhak menyatakan pesanan <strong>Diterima Lengkap dan BAST Sah secara otomatis</strong>.</li>
                        </ul>
                    </li>
                    <li><strong>Peralihan Risiko:</strong> Segala risiko kehilangan, kerusakan fisik, atau penurunan mutu barang berada pada tanggung jawab PIHAK PERTAMA/Mitra Vendor selama barang masih dalam perjalanan, dan beralih sepenuhnya kepada PIHAK KEDUA terhitung sejak barang diterima di lokasi PIHAK KEDUA dan BAST dinyatakan sah.</li>
                </ol>
            </div>

            <!-- PASAL 10 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 10</h4>
                    <p class="font-bold text-purple-900 text-xs">JAMINAN MUTU (QUALITY CONTROL), GARANSI, DAN PROSEDUR KLAIM RETUR</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>PIHAK PERTAMA menjamin bahwa barang yang diserahkan kepada PIHAK KEDUA adalah barang baru, asli (original), bebas dari cacat produksi, dan sesuai dengan spesifikasi yang tercantum dalam PO maupun Mockup yang telah di-ACC.</li>
                    <li><strong>Syarat Pengajuan Klaim Retur:</strong> Apabila ditemukan barang rusak saat pengiriman, cacat produksi, kuantitas kurang, atau ketidaksesuaian spesifikasi, PIHAK KEDUA berhak mengajukan klaim melalui tombol <strong>"Klaim Retur / Barang Rusak"</strong> pada halaman Detail PO dengan wajib melampirkan:
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>Foto/video bukti kondisi fisik kerusakan barang dan kemasan pada saat diterima;</li>
                            <li>Rincian nama item dan jumlah unit yang mengalami kendala;</li>
                            <li>Pilihan solusi penyelesaian yang diminta: <strong>(a) Pengiriman Barang Pengganti Baru (Replacement)</strong> tanpa biaya tambahan, atau <strong>(b) Pemotongan Nilai Tagihan / Pengembalian Dana (Credit Note / Refund)</strong>.</li>
                        </ul>
                    </li>
                    <li><strong>Pengecualian Garansi:</strong> Klaim retur tidak berlaku apabila kerusakan barang disebabkan oleh kesalahan penyimpanan di gudang PIHAK KEDUA (seperti terkena rembesan air, kelembaban ekstrem, atau kelalaian penanganan staf internal PIHAK KEDUA setelah serah terima) atau penyalahgunaan fungsi pemakaian.</li>
                </ol>
            </div>

            <!-- PASAL 11 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 11</h4>
                    <p class="font-bold text-purple-900 text-xs">KEPATUHAN PERPAJAKAN DAN ADMINISTRASI DOKUMEN FISKAL</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>PARA PIHAK sepakat untuk mematuhi seluruh peraturan perundang-undangan perpajakan yang berlaku di Negara Republik Indonesia.</li>
                    <li>Data Nomor Pokok Wajib Pajak (NPWP) Badan Usaha PIHAK KEDUA (<span class="font-mono font-bold"><?= esc($company['npwp'] ?: '-') ?></span>) dan alamat resmi yang terdaftar pada profil legalitas digunakan sebagai dasar pencantuman identitas perpajakan pada seluruh dokumen Faktur Tagihan (Invoice) dan dokumen pajak yang diterbitkan dalam transaksi.</li>
                    <li>Apabila berdasarkan ketentuan peraturan perpajakan PIHAK KEDUA diwajibkan melakukan pemotongan Pajak Penghasilan (PPh Pasal 22 / PPh Pasal 23) atas transaksi tertentu, maka PIHAK KEDUA wajib menyerahkan <strong>Bukti Pemotongan Pajak (Bukti Potong Unifikasi DJP)</strong> resmi yang sah kepada Tim Finance PIHAK PERTAMA paling lambat pada akhir bulan berikutnya setelah bulan pembayaran.</li>
                </ol>
            </div>

            <!-- PASAL 12 -->
            <div class="p-5 rounded-2xl bg-purple-50/60 border-2 border-purple-300 space-y-2.5">
                <div class="text-center pb-2 border-b border-purple-200">
                    <h4 class="font-black text-purple-950 uppercase">PASAL 12</h4>
                    <p class="font-bold text-purple-800 text-xs">JANGKA WAKTU PERJANJIAN (MASA BERLAKU 1 TAHUN &amp; PERPANJANGAN OTOMATIS) SERTA PENGAKHIRAN</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li><strong>Masa Berlaku 1 (Satu) Tahun:</strong> Perjanjian Kerjasama ini berlaku untuk jangka waktu awal selama <strong>1 (satu) tahun</strong>, terhitung efektif mulai tanggal <strong><?= esc($tglIndo) ?></strong> sampai dengan tanggal <strong><?= esc($tglBerakhirIndo) ?></strong>.</li>
                    <li><strong>Perpanjangan Otomatis (<em>Auto-Renewal</em>):</strong> Apabila sampai dengan <strong>30 (tiga puluh) Hari Kalender</strong> sebelum berakhirnya jangka waktu Perjanjian sebagaimana dimaksud pada ayat (1) tidak terdapat pemberitahuan tertulis dari salah satu PIHAK untuk mengakhiri Perjanjian ini, maka Perjanjian ini <strong>secara otomatis diperpanjang untuk jangka waktu 1 (satu) tahun berikutnya secara terus-menerus (<em>evergreen / auto-renewal</em>)</strong> dengan syarat dan ketentuan yang sama, selama tidak ada pemutusan kerjasama secara tertulis dari salah satu PIHAK.</li>
                    <li><strong>Tata Cara Pemutusan / Pengakhiran Kerjasama:</strong>
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li>Salah satu PIHAK yang bermaksud mengakhiri Perjanjian ini wajib menyampaikan surat pemberitahuan pengakhiran secara tertulis kepada PIHAK lainnya paling lambat <strong>30 (tiga puluh) Hari Kalender</strong> sebelum tanggal efektif pengakhiran yang dikehendaki;</li>
                            <li>PIHAK PERTAMA berhak mengakhiri Perjanjian ini atau mencabut status verifikasi kemitraan secara seketika apabila PIHAK KEDUA terbukti memberikan dokumen legalitas palsu, melakukan tindak pidana penipuan, dinyatakan pailit/PKPU oleh pengadilan, melakukan transaksi pintas (<em>bypass</em>) dengan vendor, atau lalai melunasi hutang tagihan TOP melampaui 60 (enam puluh) Hari Kalender sejak jatuh tempo.</li>
                        </ul>
                    </li>
                    <li><strong>Keberlangsungan Kewajiban Finansial (<em>Survival Clause</em>):</strong> Berakhirnya atau diputuskannya Perjanjian ini tidak menghapuskan kewajiban masing-masing PIHAK yang telah timbul sebelum tanggal efektif berakhirnya Perjanjian. PIHAK KEDUA tetap wajib melunasi seluruh sisa hutang tagihan PO/TOP yang masih belum lunas beserta denda keterlambatan (jika ada), dan PIHAK PERTAMA tetap wajib menyelesaikan pengiriman PO yang telah dibayar serta mencairkan sisa Saldo Cashback Aktif milik PIHAK KEDUA setelah dikurangi kewajiban tertunggak.</li>
                    <li><strong>Pengesampingan Pasal 1266 dan Pasal 1267 KUHPerdata:</strong> Untuk keperluan pengakhiran Perjanjian ini, PARA PIHAK secara tegas sepakat mengesampingkan berlakunya ketentuan Pasal 1266 dan Pasal 1267 Kitab Undang-Undang Hukum Perdata Indonesia sepanjang mengenai diperlukannya putusan hakim pengadilan terlebih dahulu untuk membatalkan atau mengakhiri suatu perjanjian.</li>
                </ol>
            </div>

            <!-- PASAL 13 -->
            <div class="p-5 rounded-2xl bg-amber-50/50 border-2 border-amber-300 space-y-2.5">
                <div class="text-center pb-2 border-b border-amber-200">
                    <h4 class="font-black text-amber-950 uppercase">PASAL 13</h4>
                    <p class="font-bold text-amber-900 text-xs">KEADAAN KAHAR (FORCE MAJEURE) — BENCANA ALAM, GEMPA BUMI, DAN KEJADIAN LUAR BIASA</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li><strong>Definisi Keadaan Kahar (<em>Force Majeure</em>):</strong> Yang dimaksud dengan Keadaan Kahar dalam Perjanjian ini adalah setiap peristiwa atau keadaan di luar kemampuan, kendali wajar, dan kekuasaan PARA PIHAK yang tidak dapat diduga serta tidak dapat dicegah meskipun PIHAK yang mengalaminya telah melakukan upaya pencegahan yang optimal, yang secara langsung mengakibatkan terhentinya atau tertundanya pelaksanaan kewajiban berdasarkan Perjanjian ini, termasuk namun tidak terbatas pada:
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li><strong>Bencana Alam Geologis dan Hidrometeorologis:</strong> Gempa bumi, tsunami, letusan gunung berapi, likuifaksi, tanah longsor, banjir bandang, angin puting beliung/topan, badai ekstrem, atau kebakaran hebat yang tidak disebabkan oleh kelalaian;</li>
                            <li><strong>Wabah dan Darurat Kesehatan:</strong> Epidemi, pandemi penyakit menular, atau pemberlakuan karantina wilayah (<em>lockdown</em>) resmi oleh Pemerintah;</li>
                            <li><strong>Gangguan Keamanan dan Sosial:</strong> Perang, invasi, pemberontakan bersenjata, aksi terorisme, sabotase, kerusuhan sosial/huru-hara massal, blokade jalan raya utama, atau pemogokan kerja berskala nasional;</li>
                            <li><strong>Kegagalan Infrastruktur Vital &amp; Kebijakan Pemerintah:</strong> Pemadaman listrik total berskala regional/nasional (<em>blackout</em>), lumpuhnya jaringan telekomunikasi/internet tulang punggung nasional, serangan siber masif terhadap pusat data nasional, atau diterbitkannya Peraturan Pemerintah/Keputusan Presiden di bidang moneter dan perdagangan yang melarang pelaksanaan objek pengadaan dalam Perjanjian ini.</li>
                        </ul>
                    </li>
                    <li><strong>Kewajiban Pemberitahuan Tertulis:</strong> PIHAK yang mengalami peristiwa Keadaan Kahar wajib memberitahukan secara tertulis (atau melalui saluran elektronik resmi apabila akses fisik terputus) kepada PIHAK lainnya paling lambat dalam waktu <strong>3 &times; 24 (tiga kali dua puluh empat) jam</strong> sejak terjadinya peristiwa Keadaan Kahar, dan wajib disusul dengan bukti keterangan resmi dari instansi berwenang (seperti BMKG, BNPB, Kepolisian, atau Pemerintah Daerah setempat) paling lambat dalam waktu <strong>14 (empat belas) Hari Kalender</strong>.</li>
                    <li><strong>Pembebasan Sanksi Keterlambatan &amp; Penjadwalan Ulang:</strong> Selama masa berlangsungnya Keadaan Kahar yang sah, PIHAK yang terdampak dibebaskan dari tuntutan ganti rugi maupun denda keterlambatan pengiriman/pelaksanaan, dan PARA PIHAK akan bermusyawarah untuk menyepakati penjadwalan ulang (<em>rescheduling</em>) pengiriman barang. Apabila Keadaan Kahar berlangsung terus-menerus melampaui <strong>60 (enam puluh) Hari Kalender</strong> sehingga pesanan tidak mungkin lagi dipenuhi, maka PO yang belum dikirimkan dapat dibatalkan atas kesepakatan bersama tanpa penalti.</li>
                    <li><strong>Kewajiban Pembayaran Tagihan yang Telah Diserahterimakan:</strong> Terjadinya Keadaan Kahar (seperti gempa bumi atau bencana lainnya) <strong>tidak menghapuskan kewajiban pembayaran hutang/tagihan PO</strong> atas barang-barang yang telah selesai dikirimkan dan telah diterima secara sah (BAST) oleh PIHAK KEDUA sebelum terjadinya peristiwa Keadaan Kahar tersebut; namun demikian, atas permohonan tertulis PIHAK KEDUA yang terdampak langsung oleh bencana tersebut, PIHAK PERTAMA dapat memberikan kebijakan relaksasi penjadwalan ulang tanggal jatuh tempo pembayaran (<em>grace period</em>) tanpa denda.</li>
                </ol>
            </div>

            <!-- PASAL 14 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 14</h4>
                    <p class="font-bold text-purple-900 text-xs">KERAHASIAAN INFORMASI (NON-DISCLOSURE), PERLINDUNGAN DATA, DAN ANTI-BYPASS</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>Masing-masing PIHAK wajib menjaga kerahasiaan seluruh informasi komersial, struktur harga khusus korporat, skema komisi/cashback, data pribadi pengurus (sesuai UU No. 27 Tahun 2022 tentang Pelindungan Data Pribadi), serta spesifikasi teknis/desain milik PIHAK lainnya, dan dilarang membocorkannya kepada pihak ketiga mana pun tanpa persetujuan tertulis terlebih dahulu, baik selama Perjanjian berlaku maupun hingga <strong>2 (dua) tahun</strong> setelah Perjanjian berakhir.</li>
                    <li><strong>Klausul Anti-Bypass (<em>Non-Circumvention</em>):</strong> Mengingat seluruh fasilitas kurasi vendor, penjaminan mutu, dan harga grosir dibangun oleh ekosistem PIHAK PERTAMA, maka PIHAK KEDUA mengikatkan diri untuk tidak melakukan kontak langsung, negosiasi di belakang layar, ataupun transaksi pembelian langsung (<em>bypass</em>) di luar Platform AkuAda dengan Mitra Vendor yang diperkenalkan atau diketahui melalui transaksi di AkuAda. Pelanggaran atas ketentuan ini memberikan hak kepada PIHAK PERTAMA untuk membekukan akun kemitraan, membatalkan saldo cashback yang belum dicairkan, serta menuntut ganti rugi senilai potensi fee platform yang hilang.</li>
                </ol>
            </div>

            <!-- PASAL 15 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 15</h4>
                    <p class="font-bold text-purple-900 text-xs">TATA KELOLA PERUSAHAAN YANG BAIK (GCG), ANTI-SUAP, DAN ANTI-KORUPSI</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li>PARA PIHAK berkomitmen menjalankan kerjasama ini berdasarkan prinsip <em>Good Corporate Governance</em> (Transparansi, Akuntabilitas, Tanggung Jawab, Independensi, dan Kewajaran).</li>
                    <li>Di luar program resmi <strong>Reward Cashback Korporat (<?= number_format($cashbackRate, 2) ?>%)</strong> yang tercatat secara transparan di dalam sistem akuntansi dan jurnal keuangan Platform AkuAda, PARA PIHAK melarang keras setiap karyawan, staf pengadaan, maupun perwakilannya untuk meminta, menawarkan, atau menerima suap, komisi gelap (<em>kickback</em>), maupun pemalsuan nilai kuitansi/faktur (<em>mark-up</em> fiktif) yang melanggar hukum.</li>
                </ol>
            </div>

            <!-- PASAL 16 -->
            <div class="p-5 rounded-2xl bg-rose-50/40 border-2 border-rose-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-rose-200">
                    <h4 class="font-black text-rose-950 uppercase">PASAL 16</h4>
                    <p class="font-bold text-rose-900 text-xs">HUKUM YANG BERLAKU, PENYELESAIAN PERSELISIHAN, DAN ALAT BUKTI ELEKTRONIK</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li><strong>Hukum yang Berlaku:</strong> Perjanjian ini beserta seluruh dokumen turunan di dalamnya (Quotation, PO, BAST, dan Invoice) diatur, ditafsirkan, dan dilaksanakan berdasarkan <strong>Hukum Negara Republik Indonesia</strong>.</li>
                    <li><strong>Penyelesaian Perselisihan Secara Bertahap (<em>Multi-Tiered Dispute Resolution</em>):</strong>
                        <ul class="list-disc pl-5 mt-1 space-y-1">
                            <li><strong>Tahap Pertama (Musyawarah untuk Mufakat):</strong> Apabila timbul perbedaan pendapat, sengketa kualitas barang, perselisihan perhitungan tagihan, atau sengketa pelaksanaan Perjanjian ini, PARA PIHAK sepakat untuk terlebih dahulu menyelesaikannya secara musyawarah untuk mufakat melalui perundingan langsung antara Pejabat Berwenang PARA PIHAK dalam jangka waktu <strong>30 (tiga puluh) Hari Kalender</strong> sejak salah satu PIHAK menyampaikan pemberitahuan sengketa secara tertulis;</li>
                            <li><strong>Tahap Kedua (Mediasi &amp; Rekonsiliasi):</strong> Selama proses perundingan berlangsung, PARA PIHAK akan melakukan rekonsiliasi berdasarkan data rekam jejak transaksi (<em>Audit Trail</em>), riwayat Live Chat, foto POD pengiriman, dan dokumen BAST yang tersimpan pada server PIHAK PERTAMA;</li>
                            <li><strong>Tahap Ketiga (Penyelesaian Hukum &amp; Domisili Pengadilan):</strong> Apabila setelah melewati jangka waktu 30 (tiga puluh) Hari Kalender musyawarah untuk mufakat tetap tidak menghasilkan penyelesaian yang disepakati bersama, maka PARA PIHAK sepakat untuk menyerahkan penyelesaian perselisihan tersebut secara final dan mengikat melalui jalur hukum dengan memilih domisili hukum yang tetap dan tidak berubah pada <strong>Kepaniteraan Pengadilan Negeri Jakarta Selatan</strong> (atau melalui <strong>Badan Arbitrase Nasional Indonesia / BANI</strong> apabila disepakati secara tertulis oleh PARA PIHAK).</li>
                        </ul>
                    </li>
                    <li><strong>Kekuatan Pembuktian Data Elektronik:</strong> Sesuai ketentuan Pasal 5 ayat (1) dan ayat (2) Undang-Undang Nomor 11 Tahun 2008 sebagaimana telah diubah dengan Undang-Undang Nomor 19 Tahun 2016 tentang Informasi dan Transaksi Elektronik (UU ITE), PARA PIHAK sepakat bahwa seluruh catatan elektronik pada basis data Platform AkuAda — termasuk log penerbitan PO, log persetujuan ACC Desain/Mockup, riwayat percakapan Pusat Chat, bukti unggah pembayaran, dan pengesahan BAST Digital — merupakan <strong>Alat Bukti Hukum yang Sah dan Mengikat</strong> di hadapan pengadilan maupun lembaga arbitrase.</li>
                </ol>
            </div>

            <!-- PASAL 17 -->
            <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-2.5">
                <div class="text-center pb-2 border-b border-slate-200">
                    <h4 class="font-black text-slate-900 uppercase">PASAL 17</h4>
                    <p class="font-bold text-purple-900 text-xs">KETENTUAN LAIN-LAIN (KETERPISAHAN, ADDENDUM, DAN PENUTUP)</p>
                </div>
                <ol class="list-decimal pl-5 space-y-1.5">
                    <li><strong>Keterpisahan (<em>Severability</em>):</strong> Apabila terdapat satu atau beberapa ketentuan dalam Perjanjian ini yang dinyatakan batal, tidak sah, atau tidak dapat diberlakukan berdasarkan peraturan perundang-undangan atau putusan pengadilan, maka ketentuan-ketentuan lainnya dalam Perjanjian ini tetap berlaku secara penuh dan mengikat PARA PIHAK.</li>
                    <li><strong>Perubahan dan Addendum:</strong> Hal-hal yang belum diatur atau belum cukup diatur dalam Perjanjian ini, termasuk penyesuaian plafon kredit TOP maupun perubahan tarif khusus cashback yang disetujui melalui sistem, akan diatur dalam suatu Addendum atau catatan pembaruan sistem (<em>System Audit Log</em>) yang merupakan satu kesatuan dan bagian yang tidak terpisahkan dari Perjanjian ini.</li>
                    <li><strong>Pengakuan dan Persetujuan:</strong> Demikian Perjanjian Kerjasama Induk Pengadaan Barang dan Jasa Korporat (B2B) ini dibuat, disetujui, dan disahkan secara elektronik dalam keadaan sadar serta tanpa paksaan dari pihak mana pun, serta mempunyai kekuatan hukum yang sama dengan perjanjian tertulis bermeterai bagi kedua belah pihak.</li>
                </ol>
            </div>

        </div>

        <!-- BLOK TANDA TANGAN PENGESAHAN KONTRAK KERJASAMA -->
        <div class="pt-6 border-t-2 border-slate-300 grid grid-cols-1 sm:grid-cols-2 gap-8 text-center">
            <!-- Tanda Tangan Pihak Pertama -->
            <div class="p-6 rounded-2xl border border-slate-200 bg-slate-50/50 flex flex-col justify-between space-y-6">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 block">PIHAK PERTAMA (AKUADA)</span>
                    <strong class="text-sm font-black text-slate-900 block mt-0.5"><?= esc($issuerCompany) ?></strong>
                </div>

                <div class="py-2 flex flex-col items-center justify-center">
                    <div class="px-4 py-2.5 rounded-xl border-2 border-purple-600/70 bg-purple-50/80 text-purple-900 font-mono text-[10px] font-bold shadow-2xs rotate-[-2deg]">
                        ✓ DIGITALLY SIGNED &amp; SEALED<br>
                        AKUADA LEGAL &amp; COMPLIANCE HUB<br>
                        <span class="text-[9px] text-purple-700">Ref: <?= esc($contractNumber) ?></span>
                    </div>
                </div>

                <div>
                    <strong class="text-xs font-black text-slate-900 underline block"><?= esc($signerName) ?></strong>
                    <span class="text-[11px] text-slate-600 block"><?= esc($signerPosition) ?></span>
                </div>
            </div>

            <!-- Tanda Tangan Pihak Kedua -->
            <div class="p-6 rounded-2xl border border-purple-200 bg-purple-50/20 flex flex-col justify-between space-y-6">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-700 block">PIHAK KEDUA (MITRA BUYER)</span>
                    <strong class="text-sm font-black text-slate-900 block mt-0.5"><?= esc($buyerLegalName) ?></strong>
                </div>

                <div class="py-2 flex flex-col items-center justify-center">
                    <div class="px-4 py-2.5 rounded-xl border-2 border-emerald-600/70 bg-emerald-50/80 text-emerald-900 font-mono text-[10px] font-bold shadow-2xs">
                        ✓ TERVERIFIKASI LEGALITAS BADAN USAHA<br>
                        NPWP: <?= esc($company['npwp'] ?: '-') ?><br>
                        <span class="text-[9px] text-emerald-700">NIB: <?= esc($company['nib'] ?: '-') ?></span>
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
