<?php

namespace App\Controllers;

class PageController extends BaseController
{
    public function show($slug)
    {
        $appName       = get_system_setting('app_name', 'AkuAda');
        $ppnLabel      = format_tax_rate();
        $pph22Rate     = get_pph_22_rate();
        $pph22Label    = ((float)$pph22Rate == (int)$pph22Rate) ? (int)$pph22Rate . '%' : rtrim(rtrim(number_format($pph22Rate, 2, '.', ''), '0'), '.') . '%';
        $minTop        = get_min_top_days();
        $maxTop        = get_max_top_days();
        $topTermsList  = array_map(fn($d) => 'Net ' . $d . ' hari', get_allowed_top_terms());
        $topTermsText  = implode(', ', $topTermsList);
        $defCredit     = format_rupiah((float)get_system_setting('default_credit_limit', 100000000));
        $maxCredit     = format_rupiah((float)get_system_setting('max_credit_limit', 500000000));
        $officeAddress = get_system_setting('inv_issuer_address', 'Gedung AkuAda Procurement Tower Lt. 18, Jakarta Selatan');
        $contactPhone  = get_system_setting('contact_phone', '(021) 5088-9900');
        $contactWa     = get_system_setting('contact_whatsapp', '+62 811-9988-2200');
        $contactEmail  = get_system_setting('contact_email', 'b2b@akuada.co.id');
        $contactHours  = get_system_setting('contact_hours', 'Senin - Jumat, 08:30 - 17:30 WIB');
        $payoutDays    = (int)get_system_setting('vendor_payout_days', 7);

        $pages = [
            'panduan-pengadaan' => [
                'title' => 'Panduan Pengadaan Operasional Perusahaan (E-Procurement)',
                'icon'  => 'help_center',
                'body'  => "Alur pengadaan di platform {$appName} dirancang transparan, akuntabel, dan sesuai standar audit perusahaan:
                \n1. Pemilihan Barang & Kuantitas: Pilih barang kebutuhan operasional dari katalog atau ajukan RFQ untuk barang spesifik.
                \n2. Penerbitan PO Resmi: Masukkan nomor Purchase Order (PO) internal dan pilih metode pembayaran (Term of Payment {$minTop}-{$maxTop} hari, On-Account, atau Virtual Account).
                \n3. Pengiriman & Surat Jalan: Vendor menyiapkan barang dengan Surat Jalan resmi.
                \n4. Pengesahan BAST & e-Faktur: Saat barang diterima dan diperiksa staf logistik, sahkan Berita Acara Serah Terima (BAST) di dashboard pembeli. Tanggal jatuh tempo TOP resmi berjalan sejak BAST disahkan."
            ],
            'kontak-b2b' => [
                'title' => 'Layanan Pelanggan & Kontak Korporat B2B',
                'icon'  => 'call',
                'body'  => "Tim Key Account Manager {$appName} siap membantu kebutuhan tender pengadaan perusahaan Anda:
                \n• Kantor Pusat: {$officeAddress}
                \n• Call Center Khusus B2B: {$contactPhone} ({$contactHours})
                \n• WhatsApp Dedicated Account: {$contactWa}
                \n• Email Resmi Pengadaan: {$contactEmail}"
            ],
            'kredit-top' => [
                'title' => "Fasilitas Term of Payment (TOP) Tanpa Bunga Hingga {$maxTop} Hari",
                'icon'  => 'event_repeat',
                'body'  => "{$appName} menyediakan fasilitas kredit tempo (TOP) eksklusif untuk menjaga likuiditas arus kas operasional perusahaan Anda:
                \n• Pilihan Tenor Fleksibel: {$topTermsText}.
                \n• Plafon Kredit Awal: {$defCredit} untuk perusahaan baru dan dapat ditingkatkan hingga {$maxCredit} per PT.
                \n• Syarat Mudah: Cukup lampirkan NPWP Badan, NIB aktif, dan rekening koran 3 bulan terakhir."
            ],
            'pajak-faktur' => [
                'title' => "Ketentuan Faktur Pajak PPN {$ppnLabel} Resmi DJP",
                'icon'  => 'receipt_long',
                'body'  => "Semua transaksi di {$appName} 100% mematuhi regulasi perpajakan Republik Indonesia:
                \n• PPN {$ppnLabel} Otomatis: Dihitung transparan pada setiap lembar Purchase Order (PO) dan Invoice.
                \n• e-Faktur Resmi: Terbit dengan Nomor Seri Faktur Pajak (NSFP) DJP terintegrasi langsung dengan NPWP 16 Digit badan usaha Anda.
                \n• PPh Pasal 22 ({$pph22Label}): Tersedia opsi pemotongan PPh Pasal 22 untuk instansi BUMN dan lembaga pemerintah."
            ],
            'syarat-vendor' => [
                'title' => "Syarat & Tata Cara Menjadi Mitra Vendor Resmi {$appName}",
                'icon'  => 'store',
                'body'  => "Tingkatkan omset bisnis B2B Anda dengan menjadi supplier resmi {$appName}:
                \n• Memiliki badan usaha resmi (PT, CV, atau Koperasi) yang sah di Indonesia.
                \n• Memiliki Nomor Induk Berusaha (NIB) berbasis risiko dan NPWP aktif berstatus PKP.
                \n• Menjamin keaslian barang resmi (original prinsipal) dan komitmen waktu pengiriman (SLA).
                \n• Pencairan dana otomatis ke rekening vendor H+{$payoutDays} setelah BAST disahkan."
            ],
            'simulasi-top' => [
                'title' => 'Kalkulator & Simulasi Perputaran Modal Pengadaan',
                'icon'  => 'calculate',
                'body'  => "Dengan fasilitas TOP {$minTop}-{$maxTop} hari dan plafon hingga {$maxCredit}, perusahaan dapat mengalokasikan anggaran belanja modal (Capex) ke aktivitas bisnis produktif lainnya sambil tetap memastikan operasional kantor berjalan lancar tanpa hambatan pasokan."
            ]
        ];

        $page = $pages[$slug] ?? [
            'title' => "Informasi Pengadaan {$appName}",
            'icon'  => 'info',
            'body'  => "Informasi mengenai layanan pengadaan B2B {$appName}."
        ];

        return view('marketplace/page', [
            'title' => $page['title'] . ' - ' . $appName . ' B2B',
            'page'  => $page
        ]);
    }
}
