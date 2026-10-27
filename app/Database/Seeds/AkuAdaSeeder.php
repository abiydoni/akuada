<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AkuAdaSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');

        // 1. System Settings
        $settings = [
            ['setting_group' => 'general', 'setting_key' => 'app_name', 'setting_value' => 'AkuAda', 'setting_type' => 'string', 'description' => 'Nama Resmi Marketplace B2B'],
            ['setting_group' => 'general', 'setting_key' => 'app_tagline', 'setting_value' => 'Solusi Terlengkap Pengadaan Operasional Perusahaan Anda', 'setting_type' => 'string', 'description' => 'Tagline Utama'],
            ['setting_group' => 'general', 'setting_key' => 'contact_email', 'setting_value' => 'b2b@akuada.co.id', 'setting_type' => 'string', 'description' => 'Email Layanan Pengadaan Korporat'],
            ['setting_group' => 'general', 'setting_key' => 'contact_phone', 'setting_value' => '(021) 5088-9900', 'setting_type' => 'string', 'description' => 'Call Center Pengadaan B2B'],
            ['setting_group' => 'tax', 'setting_key' => 'ppn_rate', 'setting_value' => '11', 'setting_type' => 'number', 'description' => 'Tarif PPN Standar (%)'],
            ['setting_group' => 'tax', 'setting_key' => 'pph_22_rate', 'setting_value' => '1.5', 'setting_type' => 'number', 'description' => 'Tarif PPh Pasal 22 untuk Pengadaan Instansi / BUMN (%)'],
            ['setting_group' => 'payment', 'setting_key' => 'allowed_top_terms', 'setting_value' => '14,30,60,90', 'setting_type' => 'string', 'description' => 'Opsi Term of Payment (Hari)'],
            ['setting_group' => 'payment', 'setting_key' => 'default_credit_limit', 'setting_value' => '100000000', 'setting_type' => 'number', 'description' => 'Plafon Kredit Default Pengajuan Baru (Rp)'],
            ['setting_group' => 'design', 'setting_key' => 'primary_color', 'setting_value' => '#6366f1', 'setting_type' => 'string', 'description' => 'Warna Utama Ungu/Indigo'],
            ['setting_group' => 'design', 'setting_key' => 'secondary_color', 'setting_value' => '#2563eb', 'setting_type' => 'string', 'description' => 'Warna Kedua Biru'],
            ['setting_group' => 'design', 'setting_key' => 'accent_color', 'setting_value' => '#ec4899', 'setting_type' => 'string', 'description' => 'Warna Aksen Pink/Magenta'],
        ];

        foreach ($settings as $setting) {
            $setting['updated_at'] = $now;
            $db->table('system_settings')->upsert($setting);
        }

        // 2. Companies (Vendors & Corporate Buyers)
        $companies = [
            [
                'id' => 1,
                'type' => 'vendor',
                'company_name' => 'PT Mitra Office Solusindo',
                'legal_type' => 'PT',
                'npwp' => '01.345.678.9-012.000',
                'nib' => '9120003456781',
                'address' => 'Kawasan Industri Pulogadung Blok B No. 12',
                'city' => 'Jakarta Timur',
                'province' => 'DKI Jakarta',
                'phone' => '021-46823344',
                'email' => 'sales@mitraoffice.com',
                'pic_name' => 'Hendro Prasetyo, S.E.',
                'pic_phone' => '081288991122',
                'pic_email' => 'hendro@mitraoffice.com',
                'credit_limit_top' => 0.00,
                'credit_used_top' => 0.00,
                'is_verified' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'type' => 'vendor',
                'company_name' => 'PT Techno Prima Nusantara',
                'legal_type' => 'PT',
                'npwp' => '02.456.789.1-034.000',
                'nib' => '9120005678902',
                'address' => 'Cyber Building 2 Lt. 8, Jl. HR Rasuna Said',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'phone' => '021-52901188',
                'email' => 'enterprise@technoprima.id',
                'pic_name' => 'Andi Wijaya',
                'pic_phone' => '081377889900',
                'pic_email' => 'andi.w@technoprima.id',
                'credit_limit_top' => 0.00,
                'credit_used_top' => 0.00,
                'is_verified' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'type' => 'vendor',
                'company_name' => 'PT Garda K3 Indonesia',
                'legal_type' => 'PT',
                'npwp' => '03.567.890.2-045.000',
                'nib' => '9120007890123',
                'address' => 'Ruko Golden Boulevard Blok W No. 5, BSD City',
                'city' => 'Tangerang Selatan',
                'province' => 'Banten',
                'phone' => '021-5389022',
                'email' => 'order@gardak3.co.id',
                'pic_name' => 'Bambang Santoso',
                'pic_phone' => '081199223344',
                'pic_email' => 'bambang@gardak3.co.id',
                'credit_limit_top' => 0.00,
                'credit_used_top' => 0.00,
                'is_verified' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'type' => 'buyer',
                'company_name' => 'PT Mega Solusi Digital Tbk',
                'legal_type' => 'PT Tbk',
                'npwp' => '01.999.888.7-077.000',
                'nib' => '9120001122334',
                'address' => 'Menara Mandiri Tower 1 Lt. 24, Jl. Jend. Sudirman Kav 54-55',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'phone' => '021-5268899',
                'email' => 'procurement@megasolusi.com',
                'pic_name' => 'Rina Kartika (Procurement Manager)',
                'pic_phone' => '081234567890',
                'pic_email' => 'rina.k@megasolusi.com',
                'credit_limit_top' => 250000000.00, // Limit Rp 250 Juta
                'credit_used_top' => 31250000.00,
                'is_verified' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'type' => 'buyer',
                'company_name' => 'PT Nusantara Konstruksi Infrastruktur',
                'legal_type' => 'PT',
                'npwp' => '02.888.777.6-066.000',
                'nib' => '9120002233445',
                'address' => 'Jl. TB Simatupang No. 88',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'phone' => '021-7890123',
                'email' => 'purchasing@nusantarakonstruksi.co.id',
                'pic_name' => 'Dimas Prakoso',
                'pic_phone' => '081299887766',
                'pic_email' => 'dimas@nusantarakonstruksi.co.id',
                'credit_limit_top' => 500000000.00, // Limit Rp 500 Juta
                'credit_used_top' => 104062500.00,
                'is_verified' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        foreach ($companies as $comp) {
            $db->table('companies')->upsert($comp);
        }

        // 3. Users Table
        $defaultPassword = password_hash('admin123', PASSWORD_BCRYPT);
        $users = [
            [
                'id' => 1,
                'name' => 'Super Administrator AkuAda',
                'email' => 'admin@akuada.co.id',
                'password' => $defaultPassword,
                'role' => 'admin',
                'phone' => '081100011122',
                'company_id' => null,
                'position' => 'Head of B2B Operations',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=256&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Hendro Prasetyo (Vendor Mitra Office)',
                'email' => 'vendor@mitraoffice.com',
                'password' => $defaultPassword,
                'role' => 'vendor',
                'phone' => '081288991122',
                'company_id' => 1,
                'position' => 'Head of Corporate Sales',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=256&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Andi Wijaya (Vendor Techno Prima)',
                'email' => 'vendor@technoprima.id',
                'password' => $defaultPassword,
                'role' => 'vendor',
                'phone' => '081377889900',
                'company_id' => 2,
                'position' => 'Key Account Director',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=256&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Rina Kartika (Buyer PT Mega Solusi)',
                'email' => 'procurement@megasolusi.com',
                'password' => $defaultPassword,
                'role' => 'customer',
                'phone' => '081234567890',
                'company_id' => 4,
                'position' => 'Senior Procurement Manager',
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=256&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Dimas Prakoso (Buyer PT Nusantara)',
                'email' => 'purchasing@nusantarakonstruksi.co.id',
                'password' => $defaultPassword,
                'role' => 'customer',
                'phone' => '081299887766',
                'company_id' => 5,
                'position' => 'Supply Chain & Purchasing Specialist',
                'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=256&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        foreach ($users as $user) {
            $db->table('users')->upsert($user);
        }

        // 4. Categories
        $categories = [
            [
                'id' => 1,
                'name' => 'Alat Tulis Kantor & Kertas',
                'slug' => 'alat-tulis-kantor-kertas',
                'icon' => 'description',
                'description' => 'Kertas A4/F4, pulpen korporat, ordner, binder, toner, dan perlengkapan meja kerja kantor.',
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Komputer & IT Infrastructure',
                'slug' => 'komputer-it-infrastructure',
                'icon' => 'devices',
                'description' => 'Laptop bisnis enterprise, PC Desktop, Server, Rackmount, Networking Cisco/Mikrotik, UPS industri.',
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Perlengkapan K3 & Safety APD',
                'slug' => 'k3-safety-apd',
                'icon' => 'health_and_safety',
                'description' => 'Helm proyek SNI, Rompi safety reflektif, Safety shoes, Kacamata pelindung, APAR, P3K kit.',
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name' => 'Hygiene, Cleaning & Fasilitas',
                'slug' => 'hygiene-cleaning-fasilitas',
                'icon' => 'cleaning_services',
                'description' => 'Sabun cuci tangan curah 5L, cairan disinfektan, mesin vacuum industri, tempat sampah pilah kantor.',
                'sort_order' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name' => 'Mesin Kantor & MRO Maintenance',
                'slug' => 'mesin-kantor-mro',
                'icon' => 'precision_manufacturing',
                'description' => 'Printer heavy-duty, mesin penghancur kertas (paper shredder), laminator, perkakas teknisi, power tools.',
                'sort_order' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name' => 'Pantry & Breakroom Korporat',
                'slug' => 'pantry-breakroom-korporat',
                'icon' => 'coffee',
                'description' => 'Kopi biji/bubuk hotel & resto, dispenser galon otomatis, cangkir dan peralatan makan pantry korporat.',
                'sort_order' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'name' => 'Furnitur & Workstation Kantor',
                'slug' => 'furnitur-workstation-kantor',
                'icon' => 'chair',
                'description' => 'Meja kantor modular, kursi kerja ergonomis bergaransi resmi, lemari arsip metal anti-karat.',
                'sort_order' => 7,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($categories as $cat) {
            $db->table('categories')->upsert($cat);
        }

        // 5. Products (Lengkap B2B Data)
        $products = [
            [
                'id' => 1,
                'vendor_company_id' => 1,
                'category_id' => 1,
                'name' => 'Kertas Fotocopy PaperOne Digital A4 75 GSM (Karton isi 5 Rim)',
                'slug' => 'kertas-fotocopy-paperone-digital-a4-75gsm-karton',
                'sku' => 'PAP-A4-75-BOX5',
                'barcode' => '8993214500124',
                'brand' => 'PaperOne',
                'uom' => 'Karton (Box)',
                'description' => 'Kertas HVS putih bersih teknologi ProDigi Nanotechnology, anti macet (jam-free guarantee) untuk printer laser, inkjet, dan mesin fotokopi berkecepatan tinggi di kantor korporat.',
                'spec_details' => "Ukuran: A4 (210 x 297 mm)\nGramatur: 75 GSM\nIsi: 5 Rim / Box (Total 2.500 Lembar)\nSertifikasi: ISO 9001, ISO 14001, PEFC Certified Eco-Friendly\nTKDN: 88.42%",
                'price_regular' => 245000.00,
                'moq' => 5, // Minimal 5 Box
                'weight_gram' => 12500,
                'stock' => 1500,
                'lead_time_days' => 1,
                'is_taxable' => 1,
                'ppn_percent' => 11.00,
                'tkdn_percent' => 88.42,
                'is_sample_available' => 1,
                'image_url' => 'https://images.unsplash.com/photo-1589330694653-dad6d3240e2b?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'vendor_company_id' => 2,
                'category_id' => 2,
                'name' => 'Lenovo ThinkPad T14 Gen 4 Core i7-1365U vPro 16GB 512GB SSD Windows 11 Pro Enterprise',
                'slug' => 'lenovo-thinkpad-t14-gen4-core-i7-vpro-16gb-enterprise',
                'sku' => 'LNV-TP-T14G4-I7',
                'barcode' => '196803209844',
                'brand' => 'Lenovo Enterprise',
                'uom' => 'Unit',
                'description' => 'Laptop bisnis standar korporat dunia dengan durabilitas standar militer MIL-STD-810H, security ThinkShield, dTPM 2.0, fingerprint scanner, dan garansi resmi Lenovo Premier Support 3 Tahun Onsite.',
                'spec_details' => "Processor: Intel Core i7-1365U vPro (10-Core, up to 5.2GHz)\nRAM: 16GB DDR5 4800MHz (upgradable to 48GB)\nStorage: 512GB PCIe Gen4 NVMe Opal2\nDisplay: 14.0\" WUXGA IPS Antiglare 400 nits\nOS: Windows 11 Pro 64-bit National OEM Corporate\nGaransi: 3 Tahun Lenovo Premier Support Onsite NBD\nTKDN: 42.15%",
                'price_regular' => 20500000.00,
                'moq' => 1,
                'weight_gram' => 2200,
                'stock' => 45,
                'lead_time_days' => 3,
                'is_taxable' => 1,
                'ppn_percent' => 11.00,
                'tkdn_percent' => 42.15,
                'is_sample_available' => 0,
                'image_url' => 'https://images.unsplash.com/photo-1588872657578-7efd1f1555ed?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'vendor_company_id' => 3,
                'category_id' => 3,
                'name' => 'Krisbow Safety Helmet V-Gard ANSI Type 1 + Chin Strap & Fast-Trac Suspension (Karton 20 Pcs)',
                'slug' => 'krisbow-safety-helmet-vgard-ansi-karton-20pcs',
                'sku' => 'KRIS-HLM-VG20-W',
                'barcode' => '899478201299',
                'brand' => 'Krisbow Pro',
                'uom' => 'Karton (20 Pcs)',
                'description' => 'Helm pengaman proyek standar SNI dan ANSI/ISEA Z89.1 Type 1 Class E & G. Dilengkapi ratchet adjustment 4-point suspension dan tali dagu elastis nyaman untuk pekerja lapangan dan staf audit.',
                'spec_details' => "Material: High-Density Polyethylene (HDPE) Tahan Benturan dan Dielektrik\nWarna: Putih / Kuning (Bisa request mix)\nSertifikasi: SNI ISO 3873:2012, ANSI Z89.1-2014\nIsi kemasan: 20 Pcs per Karton\nGaransi: 1 Tahun Cacat Pabrikan",
                'price_regular' => 1150000.00,
                'moq' => 2,
                'weight_gram' => 8400,
                'stock' => 250,
                'lead_time_days' => 2,
                'is_taxable' => 1,
                'ppn_percent' => 11.00,
                'tkdn_percent' => 65.10,
                'is_sample_available' => 1,
                'image_url' => 'https://images.unsplash.com/photo-1578873375972-04e8d3886f4a?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'vendor_company_id' => 1,
                'category_id' => 5,
                'name' => 'HP LaserJet Enterprise MFP M430f All-in-One Heavy Duty Network Printer',
                'slug' => 'hp-laserjet-enterprise-mfp-m430f-printer',
                'sku' => 'HP-MFP-M430F',
                'barcode' => '194850239102',
                'brand' => 'HP Enterprise',
                'uom' => 'Unit',
                'description' => 'Printer monokrom laser korporat multifungsi: Print, Copy, Scan, Fax. Kecepatan hingga 42 ppm dengan auto duplex scanner dan keamanan HP Sure Start self-healing BIOS.',
                'spec_details' => "Kecepatan Cetak: Hingga 42 ppm (Letter/A4)\nDuty Cycle: Hingga 100.000 halaman per bulan\nKonektivitas: Gigabit Ethernet, USB 2.0 High Speed, AirPrint\nKapasitas Kertas: Tray 1 100 lembar, Tray 2 250 lembar, optional 550 lembar\nGaransi: 1 Tahun HP Onsite Service Enterprise",
                'price_regular' => 9850000.00,
                'moq' => 1,
                'weight_gram' => 18000,
                'stock' => 30,
                'lead_time_days' => 3,
                'is_taxable' => 1,
                'ppn_percent' => 11.00,
                'tkdn_percent' => 35.80,
                'is_sample_available' => 0,
                'image_url' => 'https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'vendor_company_id' => 3,
                'category_id' => 4,
                'name' => 'Dettol Pro Solutions Antibacterial Hand Wash 5 Liter Jerigen (Chemical Grade Industri)',
                'slug' => 'dettol-pro-solutions-antibacterial-handwash-5l',
                'sku' => 'DET-PRO-HW-5L',
                'barcode' => '899123049588',
                'brand' => 'Dettol Professional',
                'uom' => 'Jerigen (5 Liter)',
                'description' => 'Sabun cuci tangan cair antibakteri khusus korporat dan perkantoran. Membunuh 99.9% kuman dan bakteri, lembut di tangan dengan pelembab alami, aroma pinus segar.',
                'spec_details' => "Isi Bersih: 5 Liter Jerigen Industri\nIzin Edar Kemenkes RI: PKD 20501120934\nKemasan: Jerigen HDPE Food Grade tebal anti bocor\nStandar: BPOM & Sertifikasi Halal MUI",
                'price_regular' => 285000.00,
                'moq' => 3,
                'weight_gram' => 5500,
                'stock' => 400,
                'lead_time_days' => 1,
                'is_taxable' => 1,
                'ppn_percent' => 11.00,
                'tkdn_percent' => 78.50,
                'is_sample_available' => 1,
                'image_url' => 'https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'vendor_company_id' => 1,
                'category_id' => 7,
                'name' => 'Kursi Kerja Kantor Ergonomis Pexio Jervis Ergonomic Mesh Chair with 3D Armrest & Lumbar Support',
                'slug' => 'pexio-jervis-ergonomic-mesh-chair-kursi-kantor',
                'sku' => 'PEX-CHR-JERVIS-BLK',
                'barcode' => '899551203948',
                'brand' => 'Pexio',
                'uom' => 'Unit',
                'description' => 'Kursi kerja ergonomis standar kesehatan postur K3 perkantoran. Mengurangi kelelahan duduk berjam-jam, sandaran mesh breathable Korea, gaslift Class 4 BIFMA Certified tahan beban 150 kg.',
                'spec_details' => "Material: Breathable Mesh Korea High Elasticity, Nylon Base BIFMA X5.1\nFitur: 3D Adjustable Armrest, Adjustable Lumbar Support, Reclining Lock 90-135 Derajat\nGaransi: 2 Tahun Garansi Mekanisme & Gaslift Resmi",
                'price_regular' => 1850000.00,
                'moq' => 2,
                'weight_gram' => 16500,
                'stock' => 85,
                'lead_time_days' => 2,
                'is_taxable' => 1,
                'ppn_percent' => 11.00,
                'tkdn_percent' => 52.30,
                'is_sample_available' => 1,
                'image_url' => 'https://images.unsplash.com/photo-1580481077195-c2f82d13470f?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'vendor_company_id' => 2,
                'category_id' => 2,
                'name' => 'Cisco Catalyst 1000 Series 24-Port Gigabit Managed Switch (C1000-24T-4G-L)',
                'slug' => 'cisco-catalyst-1000-24port-gigabit-managed-switch',
                'sku' => 'CSCO-C1000-24T-4G',
                'barcode' => '0882658849301',
                'brand' => 'Cisco Systems',
                'uom' => 'Unit',
                'description' => 'Switch jaringan enterprise level untuk ruang server kantor pusat dan kantor cabang. 24 port 10/100/1000 + 4x 1G SFP uplink, fanless design hening untuk open workspace.',
                'spec_details' => "Port: 24x 10/100/1000 RJ45 + 4x 1G SFP uplinks\nForwarding Rate: 41.66 Mpps\nSwitching Capacity: 56 Gbps\nManagement: Web UI, Cisco DNA Center, CLI Command\nGaransi: Cisco Enhanced Limited Lifetime Warranty (E-LLW)",
                'price_regular' => 13500000.00,
                'moq' => 1,
                'weight_gram' => 4500,
                'stock' => 20,
                'lead_time_days' => 4,
                'is_taxable' => 1,
                'ppn_percent' => 11.00,
                'tkdn_percent' => 28.50,
                'is_sample_available' => 0,
                'image_url' => 'https://images.unsplash.com/photo-1544197150-b99a580bb7a8?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 8,
                'vendor_company_id' => 1,
                'category_id' => 6,
                'name' => 'Paket Pantry Korporat: Kopi Biji Arabica Mandheling 5Kg + Gula Sachet 500 Pcs + Teh Celup Kotak',
                'slug' => 'paket-pantry-korporat-arabica-gula-teh-bulanan',
                'sku' => 'PANTRY-PKT-01',
                'barcode' => '899701239845',
                'brand' => 'AkuAda Hospitality',
                'uom' => 'Paket',
                'description' => 'Paket lengkap suplai pantry bulanan perusahaan (estimasi 50 karyawan). Terdiri dari biji kopi sangrai fresh grade 1, gula pasir higienis kemasan sachet 8g, dan teh celup melati premium.',
                'spec_details' => "Isi Paket:\n- 5x 1Kg Kopi Biji Arabica Specialty Roast\n- 1 Karton (500 Sachet) Gula Tebu Higienis\n- 4 Box (100 Kantung) Teh Celup Melati Sosro Heritage\nKemasan kedap udara dengan valve degassing satu arah",
                'price_regular' => 950000.00,
                'moq' => 1,
                'weight_gram' => 8000,
                'stock' => 120,
                'lead_time_days' => 1,
                'is_taxable' => 1,
                'ppn_percent' => 11.00,
                'tkdn_percent' => 95.00,
                'is_sample_available' => 1,
                'image_url' => 'https://images.unsplash.com/photo-1509785307050-d4066910ec1e?auto=format&fit=crop&w=600&q=80',
                'status' => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        foreach ($products as $prod) {
            $db->table('products')->upsert($prod);
        }

        // 6. Product Price Tiers (Volume B2B Discounts)
        $tiers = [
            // PaperOne A4
            ['product_id' => 1, 'min_qty' => 5, 'max_qty' => 19, 'unit_price' => 245000.00],
            ['product_id' => 1, 'min_qty' => 20, 'max_qty' => 99, 'unit_price' => 232000.00],
            ['product_id' => 1, 'min_qty' => 100, 'max_qty' => null, 'unit_price' => 218000.00],

            // Lenovo ThinkPad
            ['product_id' => 2, 'min_qty' => 1, 'max_qty' => 4, 'unit_price' => 20500000.00],
            ['product_id' => 2, 'min_qty' => 5, 'max_qty' => 14, 'unit_price' => 19750000.00],
            ['product_id' => 2, 'min_qty' => 15, 'max_qty' => null, 'unit_price' => 18900000.00],

            // Krisbow Safety Helmet
            ['product_id' => 3, 'min_qty' => 2, 'max_qty' => 9, 'unit_price' => 1150000.00],
            ['product_id' => 3, 'min_qty' => 10, 'max_qty' => 49, 'unit_price' => 1050000.00],
            ['product_id' => 3, 'min_qty' => 50, 'max_qty' => null, 'unit_price' => 960000.00],

            // Dettol Pro 5L
            ['product_id' => 5, 'min_qty' => 3, 'max_qty' => 9, 'unit_price' => 285000.00],
            ['product_id' => 5, 'min_qty' => 10, 'max_qty' => 29, 'unit_price' => 265000.00],
            ['product_id' => 5, 'min_qty' => 30, 'max_qty' => null, 'unit_price' => 245000.00],

            // Pexio Ergonomic Chair
            ['product_id' => 6, 'min_qty' => 2, 'max_qty' => 9, 'unit_price' => 1850000.00],
            ['product_id' => 6, 'min_qty' => 10, 'max_qty' => 29, 'unit_price' => 1680000.00],
            ['product_id' => 6, 'min_qty' => 30, 'max_qty' => null, 'unit_price' => 1520000.00],
        ];

        $db->table('product_price_tiers')->emptyTable();
        foreach ($tiers as $tier) {
            $tier['created_at'] = $now;
            $tier['updated_at'] = $now;
            $db->table('product_price_tiers')->insert($tier);
        }

        // 7. Orders & Order Items
        $orders = [
            [
                'id' => 1,
                'order_number' => 'ORD-202610-0012',
                'po_number' => 'PO/MSD/PROC/2026/10/089',
                'customer_company_id' => 4, // PT Mega Solusi Digital Tbk
                'vendor_company_id' => 1, // PT Mitra Office Solusindo
                'subtotal' => 28150000.00,
                'ppn_amount' => 3096500.00, // 11%
                'shipping_amount' => 0.00, // Free B2B corporate shipping
                'total_amount' => 31246500.00,
                'payment_method' => 'top_30',
                'payment_status' => 'pending_approval',
                'order_status' => 'processing',
                'tax_invoice_required' => 1,
                'customer_notes' => 'Harap kirim langsung ke Gudang Logistik Lt. 2 bersama faktur pajak asli bertanda tangan e-Faktur.',
                'shipping_address' => 'Menara Mandiri Tower 1 Lt. 24, Jl. Jend. Sudirman Kav 54-55, Jakarta Selatan',
                'bast_number' => 'BAST/MOS/2026/10/045',
                'invoice_number' => 'INV/MOS/2026/10/0112',
                'tax_invoice_number' => '010.002-26.89201928',
                'due_date' => date('Y-m-d', strtotime('+30 days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'order_number' => 'ORD-202610-0015',
                'po_number' => 'PO/NKI/IT/2026/10/041',
                'customer_company_id' => 5, // PT Nusantara Konstruksi Infrastruktur
                'vendor_company_id' => 2, // PT Techno Prima Nusantara
                'subtotal' => 93750000.00, // 5 Unit ThinkPad @ 18.750.000
                'ppn_amount' => 10312500.00,
                'shipping_amount' => 0.00,
                'total_amount' => 104062500.00,
                'payment_method' => 'top_60',
                'payment_status' => 'unpaid',
                'order_status' => 'shipped',
                'tax_invoice_required' => 1,
                'customer_notes' => 'Pengadaan laptop divisi arsitektur dan estimator proyek IKN. Tolong QC hardware sebelum dikirim.',
                'shipping_address' => 'Jl. TB Simatupang No. 88, Cilandak, Jakarta Selatan',
                'bast_number' => 'BAST/TPN/2026/10/078',
                'invoice_number' => 'INV/TPN/2026/10/0099',
                'tax_invoice_number' => '010.002-26.77291034',
                'due_date' => date('Y-m-d', strtotime('+55 days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => $now,
            ]
        ];

        foreach ($orders as $ord) {
            $db->table('orders')->upsert($ord);
        }

        // Order Items
        $orderItems = [
            // Order 1 items (PaperOne & Dettol)
            [
                'id' => 1,
                'order_id' => 1,
                'product_id' => 1,
                'product_name' => 'Kertas Fotocopy PaperOne Digital A4 75 GSM (Karton isi 5 Rim)',
                'sku' => 'PAP-A4-75-BOX5',
                'uom' => 'Karton (Box)',
                'qty' => 100,
                'unit_price' => 218000.00,
                'ppn_amount' => 2398000.00,
                'subtotal' => 21800000.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'order_id' => 1,
                'product_id' => 5,
                'product_name' => 'Dettol Pro Solutions Antibacterial Hand Wash 5 Liter Jerigen',
                'sku' => 'DET-PRO-HW-5L',
                'uom' => 'Jerigen (5 Liter)',
                'qty' => 25,
                'unit_price' => 254000.00,
                'ppn_amount' => 698500.00,
                'subtotal' => 6350000.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            // Order 2 items (Lenovo ThinkPad)
            [
                'id' => 3,
                'order_id' => 2,
                'product_id' => 2,
                'product_name' => 'Lenovo ThinkPad T14 Gen 4 Core i7-1365U vPro 16GB 512GB SSD Enterprise',
                'sku' => 'LNV-TP-T14G4-I7',
                'uom' => 'Unit',
                'qty' => 5,
                'unit_price' => 18750000.00,
                'ppn_amount' => 10312500.00,
                'subtotal' => 93750000.00,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        foreach ($orderItems as $it) {
            $db->table('order_items')->upsert($it);
        }

        // 8. RFQ (Request for Quotation)
        $rfqs = [
            [
                'id' => 1,
                'rfq_number' => 'RFQ-202610-0044',
                'customer_company_id' => 4,
                'vendor_company_id' => 2,
                'title' => 'Pengadaan 25 Unit Laptop Bisnis & 10 Unit Switch Rackmount Cabang Surabaya',
                'notes' => 'Membutuhkan penawaran resmi ber-kop surat dengan termin pembayaran TOP 60 hari. Termasuk SLA perbaikan on-site H+1.',
                'target_delivery_date' => date('Y-m-d', strtotime('+20 days')),
                'status' => 'quoted',
                'total_estimate' => 580000000.00,
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'rfq_number' => 'RFQ-202610-0051',
                'customer_company_id' => 5,
                'vendor_company_id' => 3,
                'title' => 'Pengadaan Paket APD Lengkap (Helm, Rompi, Kacamata, Sepatu Safety) Proyek Jalan Tol',
                'notes' => 'Target sertifikasi K3 OHSAS. Sertakan sertifikat hasil uji SNI dan lembar garansi.',
                'target_delivery_date' => date('Y-m-d', strtotime('+14 days')),
                'status' => 'submitted',
                'total_estimate' => 85000000.00,
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 days')),
                'updated_at' => $now,
            ]
        ];

        foreach ($rfqs as $rfq) {
            $db->table('rfqs')->upsert($rfq);
        }

        // RFQ Items
        $rfqItems = [
            [
                'id' => 1,
                'rfq_id' => 1,
                'product_id' => 2,
                'item_name' => 'Lenovo ThinkPad T14 Gen 4 Enterprise Core i7',
                'qty' => 25,
                'uom' => 'Unit',
                'target_unit_price' => 18500000.00,
                'vendor_offered_price' => 18650000.00,
                'notes' => 'Include tas laptop backpack Lenovo original',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'rfq_id' => 1,
                'product_id' => 7,
                'item_name' => 'Cisco Catalyst 1000 24-Port Gigabit Managed Switch',
                'qty' => 10,
                'uom' => 'Unit',
                'target_unit_price' => 12500000.00,
                'vendor_offered_price' => 12800000.00,
                'notes' => 'Include rackmount bracket kit',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'rfq_id' => 2,
                'product_id' => 3,
                'item_name' => 'Krisbow Safety Helmet V-Gard ANSI (Karton 20 Pcs)',
                'qty' => 50,
                'uom' => 'Karton (20 Pcs)',
                'target_unit_price' => 950000.00,
                'vendor_offered_price' => null,
                'notes' => 'Mix: 30 Karton Putih, 20 Karton Kuning',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        foreach ($rfqItems as $it) {
            $db->table('rfq_items')->upsert($it);
        }
    }
}
