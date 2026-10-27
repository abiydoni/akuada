<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AkuAda extends BaseConfig
{
    /**
     * Application Branding
     */
    public string $appName = 'AkuAda';
    public string $tagline = 'B2B Marketplace & E-Procurement Operasional Perusahaan';
    public string $companyLegal = 'PT Aku Ada Nusantara Bersama';
    public string $contactEmail = 'b2b@akuada.co.id';
    public string $contactPhone = '(021) 5088-9900';
    public string $whatsapp = '+62 811-9988-2200';
    public string $address = 'AkuAda Procurement Tower Lt. 18, Jl. TB Simatupang No. 22, Jakarta Selatan';

    /**
     * Theme & Color Palettes (Ungu, Biru, Pink)
     * Dedicated tokens for uniform design system across all modules
     */
    public array $theme = [
        'primary' => [
            'name'    => 'Electric Purple',
            'hex'     => '#7C3AED',
            'rgb'     => '124, 58, 237',
            'twClass' => 'bg-purple-600',
            'twText'  => 'text-purple-600',
        ],
        'secondary' => [
            'name'    => 'Corporate Blue',
            'hex'     => '#2563EB',
            'rgb'     => '37, 99, 235',
            'twClass' => 'bg-blue-600',
            'twText'  => 'text-blue-600',
        ],
        'accent' => [
            'name'    => 'Vibrant Pink',
            'hex'     => '#EC4899',
            'rgb'     => '236, 72, 153',
            'twClass' => 'bg-pink-500',
            'twText'  => 'text-pink-500',
        ],
        'gradient' => [
            'primary' => 'bg-gradient-to-r from-purple-700 via-indigo-600 to-pink-500',
            'brand'   => 'bg-gradient-to-r from-indigo-600 via-blue-600 to-pink-500',
            'header'  => 'bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900',
            'card'    => 'bg-gradient-to-br from-white via-purple-50/30 to-pink-50/20',
        ]
    ];

    /**
     * Fiscal & Tax Configurations
     */
    public float $defaultPpnRate = 11.0;
    public float $defaultPph22Rate = 1.5;

    /**
     * Vendor Marketplace Fee & Platform Commission Configurations
     */
    public float $defaultVendorFeeRate = 2.5; // Persentase Fee Platform default dari Vendor (%)
    public float $minVendorFeeAmount = 0.0; // Nominal Fee minimum per transaksi (Rp)
    public string $vendorFeeCalcBasis = 'dpp'; // 'dpp' (Dasar Pengenaan Pajak / Sebelum PPN) atau 'gross' (Setelah PPN)
    public string $vendorFeeSettlementTerm = 'H+3 BAST'; // Jadwal pencairan saldo ke rekening vendor

    /**
     * B2B Payment Methods & Terms
     */
    public array $paymentMethods = [
        'top_14' => [
            'label' => 'Term of Payment 14 Hari (TOP 14)',
            'desc'  => 'Tempo pembayaran 14 hari kalender setelah Berita Acara Serah Terima (BAST)',
            'icon'  => 'calendar_month',
            'type'  => 'credit',
        ],
        'top_30' => [
            'label' => 'Term of Payment 30 Hari (TOP 30)',
            'desc'  => 'Tempo pembayaran net 30 hari standar pengadaan korporat',
            'icon'  => 'calendar_today',
            'type'  => 'credit',
        ],
        'top_60' => [
            'label' => 'Term of Payment 60 Hari (TOP 60)',
            'desc'  => 'Tempo khusus kontrak pengadaan B2B skala menengah ke atas',
            'icon'  => 'event_repeat',
            'type'  => 'credit',
        ],
        'top_90' => [
            'label' => 'Term of Payment 90 Hari (TOP 90)',
            'desc'  => 'Tempo kontrak enterprise & tender institusi/BUMN',
            'icon'  => 'schedule',
            'type'  => 'credit',
        ],
        'corporate_po' => [
            'label' => 'Purchase Order (PO) On-Account',
            'desc'  => 'Pembayaran mengacu nomor PO resmi yang disetujui Finance',
            'icon'  => 'receipt_long',
            'type'  => 'po',
        ],
        'bank_va' => [
            'label' => 'Virtual Account Corporate (BCA, Mandiri, BNI, BRI)',
            'desc'  => 'Otomatis terverifikasi instan 24/7 tanpa perlu kirim bukti transfer',
            'icon'  => 'account_balance',
            'type'  => 'direct',
        ],
        'escrow' => [
            'label' => 'Rekening Bersama / Escrow AkuAda Proteksi',
            'desc'  => 'Dana ditahan aman hingga barang diterima dan BAST disahkan buyer',
            'icon'  => 'verified_user',
            'type'  => 'escrow',
        ]
    ];

    /**
     * Standard Units of Measure (UOM) for B2B procurement
     */
    public array $uomList = [
        'Pcs'    => 'Pieces / Satuan',
        'Rim'    => 'Rim (500 Lembar)',
        'Box'    => 'Box / Dus',
        'Karton' => 'Karton Besar / Master Box',
        'Pack'   => 'Pack / Bungkus',
        'Lot'    => 'Lot Pengadaan',
        'Unit'   => 'Unit Mesin / Hardware',
        'Set'    => 'Set Komplit',
        'Roll'   => 'Gulungan / Roll',
        'Jerigen'=> 'Jerigen Cairan Industri',
    ];

    /**
     * B2B Granular Order Lifecycle Statuses & Workflow Pipeline
     */
    public array $orderStatuses = [
        'po_issued' => [
            'label'       => 'Penerbitan PO',
            'desc'        => 'Dokumen Purchase Order resmi diterbitkan pembeli korporat',
            'color'       => 'amber',
            'icon'        => 'receipt_long',
            'step'        => 1,
        ],
        'po_approved' => [
            'label'       => 'PO Dikonfirmasi Vendor',
            'desc'        => 'Vendor memverifikasi PO dan mengunci komitmen stok pengadaan',
            'color'       => 'blue',
            'icon'        => 'verified',
            'step'        => 2,
        ],
        'in_production' => [
            'label'       => 'Proses Produksi / Penyiapan',
            'desc'        => 'Barang dalam proses assembling, pabrikasi atau penyiapan gudang',
            'color'       => 'purple',
            'icon'        => 'precision_manufacturing',
            'step'        => 3,
        ],
        'quality_control' => [
            'label'       => 'Pemeriksaan Mutu (QC)',
            'desc'        => 'Inspeksi kualitas fisik, barcode, nomor batch & sertifikasi',
            'color'       => 'indigo',
            'icon'        => 'fact_check',
            'step'        => 4,
        ],
        'packaging' => [
            'label'       => 'Pengemasan & Paletisasi',
            'desc'        => 'Pengepakan master box dengan pallet & segel korporat',
            'color'       => 'pink',
            'icon'        => 'inventory_2',
            'step'        => 5,
        ],
        'shipped' => [
            'label'       => 'Dalam Pengiriman / Surat Jalan',
            'desc'        => 'Barang diberangkatkan kurir korporat dengan Surat Jalan resmi',
            'color'       => 'sky',
            'icon'        => 'local_shipping',
            'step'        => 6,
        ],
        'delivered' => [
            'label'       => 'Tiba di Lokasi Buyer',
            'desc'        => 'Barang sampai di alamat kantor/gudang dan diperiksa staf logistik',
            'color'       => 'violet',
            'icon'        => 'where_to_vote',
            'step'        => 7,
        ],
        'bast_signed' => [
            'label'       => 'Pengesahan BAST',
            'desc'        => 'Berita Acara Serah Terima (BAST) resmi disahkan oleh Manager Buyer',
            'color'       => 'emerald',
            'icon'        => 'assignment_turned_in',
            'step'        => 8,
        ],
        'completed' => [
            'label'       => 'Selesai & Lunas',
            'desc'        => 'Pelunasan termin pembayaran TOP dan pelaporan e-Faktur tuntas',
            'color'       => 'green',
            'icon'        => 'check_circle',
            'step'        => 9,
        ],
        'cancelled' => [
            'label'       => 'Dibatalkan',
            'desc'        => 'Pesanan dibatalkan atas kesepakatan para pihak',
            'color'       => 'rose',
            'icon'        => 'cancel',
            'step'        => 0,
        ],
    ];
}
