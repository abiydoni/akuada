<?php

namespace App\Controllers;

class PageController extends BaseController
{
    public function show($slug)
    {
        $pages = [
            'panduan-pengadaan' => [
                'title' => 'Panduan Pengadaan Operasional Perusahaan (E-Procurement)',
                'icon'  => 'help_center',
                'body'  => "Alur pengadaan di platform AkuAda dirancang transparan, akuntabel, dan sesuai standar audit perusahaan:
                \n1. Pemilihan Barang & Kuantitas: Pilih barang kebutuhan operasional dari katalog atau ajukan RFQ untuk barang spesifik.
                \n2. Penerbitan PO Resmi: Masukkan nomor Purchase Order (PO) internal dan pilih metode pembayaran (Term of Payment 14-90 hari, On-Account, atau Virtual Account).
                \n3. Pengiriman & Surat Jalan: Vendor menyiapkan barang dengan Surat Jalan resmi.
                \n4. Pengesahan BAST & e-Faktur: Saat barang diterima dan diperiksa staf logistik, sahkan Berita Acara Serah Terima (BAST) di dashboard pembeli. Tanggal jatuh tempo TOP resmi berjalan sejak BAST disahkan."
            ],
            'kontak-b2b' => [
                'title' => 'Layanan Pelanggan & Kontak Korporat B2B',
                'icon'  => 'call',
                'body'  => "Tim Key Account Manager AkuAda siap membantu kebutuhan tender pengadaan perusahaan Anda:
                \n• Kantor Pusat: Gedung AkuAda Procurement Tower Lt. 18, Jl. TB Simatupang No. 22, Jakarta Selatan
                \n• Call Center Khusus B2B: (021) 5088-9900 (Senin - Jumat, 08:30 - 17:30 WIB)
                \n• WhatsApp Dedicated Account: +62 811-9988-2200
                \n• Email Resmi Pengadaan: b2b@akuada.co.id"
            ],
            'kredit-top' => [
                'title' => 'Fasilitas Term of Payment (TOP) Tanpa Bunga Hingga 90 Hari',
                'icon'  => 'event_repeat',
                'body'  => "AkuAda menyediakan fasilitas kredit tempo (TOP) eksklusif untuk menjaga likuiditas arus kas operasional perusahaan Anda:
                \n• Pilihan Tenor Fleksibel: Net 14 hari, Net 30 hari, Net 60 hari, hingga Net 90 hari.
                \n• Plafon Kredit Awal: Rp 100.000.000 untuk perusahaan baru dan dapat ditingkatkan hingga Rp 1.000.000.000 per PT.
                \n• Syarat Mudah: Cukup lampirkan NPWP Badan, NIB aktif, dan rekening koran 3 bulan terakhir."
            ],
            'pajak-faktur' => [
                'title' => 'Ketentuan Faktur Pajak PPN 11% Resmi DJP',
                'icon'  => 'receipt_long',
                'body'  => "Semua transaksi di AkuAda 100% mematuhi regulasi perpajakan Republik Indonesia:
                \n• PPN 11% Otomatis: Dihitung transparan pada setiap lembar Purchase Order (PO) dan Invoice.
                \n• e-Faktur Resmi: Terbit dengan Nomor Seri Faktur Pajak (NSFP) DJP terintegrasi langsung dengan NPWP 16 Digit badan usaha Anda.
                \n• PPh Pasal 22: Tersedia opsi pemotongan PPh Pasal 22 untuk instansi BUMN dan lembaga pemerintah."
            ],
            'syarat-vendor' => [
                'title' => 'Syarat & Tata Cara Menjadi Mitra Vendor Resmi AkuAda',
                'icon'  => 'store',
                'body'  => "Tingkatkan omset bisnis B2B Anda dengan menjadi supplier resmi AkuAda:
                \n• Memiliki badan usaha resmi (PT, CV, atau Koperasi) yang sah di Indonesia.
                \n• Memiliki Nomor Induk Berusaha (NIB) berbasis risiko dan NPWP aktif berstatus PKP.
                \n• Menjamin keaslian barang resmi (original prinsipal) dan komitmen waktu pengiriman (SLA)."
            ],
            'simulasi-top' => [
                'title' => 'Kalkulator & Simulasi Perputaran Modal Pengadaan',
                'icon'  => 'calculate',
                'body'  => "Dengan fasilitas TOP 60-90 hari, perusahaan dapat mengalokasikan anggaran belanja modal (Capex) ke aktivitas bisnis produktif lainnya sambil tetap memastikan operasional kantor berjalan lancar tanpa hambatan pasokan."
            ]
        ];

        $page = $pages[$slug] ?? [
            'title' => 'Informasi Pengadaan AkuAda',
            'icon'  => 'info',
            'body'  => 'Informasi mengenai layanan pengadaan B2B AkuAda.'
        ];

        return view('marketplace/page', [
            'title' => $page['title'] . ' - AkuAda B2B',
            'page'  => $page
        ]);
    }
}
