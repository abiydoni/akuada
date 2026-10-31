<?php

namespace App\Models;

use CodeIgniter\Model;

class CoaModel extends Model
{
    protected $table            = 'acc_coa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'account_code',
        'account_name',
        'account_category',
        'account_group',
        'normal_balance',
        'report_position',
        'is_cash_bank',
        'bank_name',
        'bank_account_number',
        'opening_balance',
        'is_system',
        'is_active',
        'description',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;

    public static function ensureAccountingInitialized(): void
    {
        static $initialized = false;
        if ($initialized) {
            return;
        }
        $initialized = true;

        $db = \Config\Database::connect();

        // 1. Create acc_coa table if not exists
        if (!$db->tableExists('acc_coa')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `acc_coa` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `account_code` VARCHAR(15) NOT NULL,
                    `account_name` VARCHAR(180) NOT NULL,
                    `account_category` ENUM('ASSET','LIABILITY','EQUITY','REVENUE','COGS','EXPENSE','OTHER_INCOME','OTHER_EXPENSE') NOT NULL DEFAULT 'ASSET',
                    `account_group` VARCHAR(100) NOT NULL DEFAULT 'Aset Lancar',
                    `normal_balance` ENUM('DEBIT','CREDIT') NOT NULL DEFAULT 'DEBIT',
                    `report_position` ENUM('NERACA','LABA_RUGI') NOT NULL DEFAULT 'NERACA',
                    `is_cash_bank` TINYINT(1) NOT NULL DEFAULT 0,
                    `bank_name` VARCHAR(100) NULL DEFAULT NULL,
                    `bank_account_number` VARCHAR(80) NULL DEFAULT NULL,
                    `opening_balance` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
                    `is_system` TINYINT(1) NOT NULL DEFAULT 0,
                    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                    `description` TEXT NULL,
                    `created_at` DATETIME NULL,
                    `updated_at` DATETIME NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uk_account_code` (`account_code`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
        }

        // 2. Create acc_journals table if not exists
        if (!$db->tableExists('acc_journals')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `acc_journals` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `journal_number` VARCHAR(60) NOT NULL,
                    `journal_date` DATE NOT NULL,
                    `journal_type` ENUM('GENERAL','SALES_PO','RECEIPT_AR','PAYMENT_AP','CASH_IN','CASH_OUT','ADJUSTMENT') NOT NULL DEFAULT 'GENERAL',
                    `reference_type` VARCHAR(40) NULL DEFAULT NULL,
                    `reference_id` INT(11) NULL DEFAULT NULL,
                    `reference_number` VARCHAR(100) NULL DEFAULT NULL,
                    `description` TEXT NOT NULL,
                    `total_debit` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
                    `total_credit` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
                    `status` ENUM('POSTED','VOID') NOT NULL DEFAULT 'POSTED',
                    `created_by` VARCHAR(120) NULL DEFAULT 'System Auto-GL',
                    `created_at` DATETIME NULL,
                    `updated_at` DATETIME NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `uk_journal_number` (`journal_number`),
                    KEY `idx_journal_date` (`journal_date`),
                    KEY `idx_reference` (`reference_type`, `reference_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
        }

        // 3. Create acc_journal_items table if not exists
        if (!$db->tableExists('acc_journal_items')) {
            $db->query("
                CREATE TABLE IF NOT EXISTS `acc_journal_items` (
                    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                    `journal_id` INT(11) UNSIGNED NOT NULL,
                    `coa_id` INT(11) UNSIGNED NOT NULL,
                    `account_code` VARCHAR(15) NOT NULL,
                    `line_description` VARCHAR(255) NULL DEFAULT NULL,
                    `debit` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
                    `credit` DECIMAL(18,2) NOT NULL DEFAULT 0.00,
                    `created_at` DATETIME NULL,
                    `updated_at` DATETIME NULL,
                    PRIMARY KEY (`id`),
                    KEY `idx_journal_id` (`journal_id`),
                    KEY `idx_coa_id` (`coa_id`),
                    KEY `idx_account_code` (`account_code`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
            ");
        }

        // 4. Seed Default Master COA ###-### if empty
        $coaCount = $db->table('acc_coa')->countAllResults();
        if ($coaCount === 0) {
            self::seedDefaultCoa($db);
        } else {
            self::syncBankCoaNamesWithSettings($db);
        }

        // 5. Sync orders & operational sample journals
        self::syncOrdersToJournals($db);
    }

    public static function syncBankCoaNamesWithSettings($db): void
    {
        $b1Name = get_system_setting('inv_bank1_name', 'Bank Mandiri (Escrow)');
        $b1Num  = get_system_setting('inv_bank1_number', '122-00-1122334-5');
        $b2Name = get_system_setting('inv_bank2_name', 'Bank BCA (Escrow)');
        $b2Num  = get_system_setting('inv_bank2_number', '535-088-9900');
        $b3Name = get_system_setting('inv_bank3_name', 'Bank BNI Corporate VA');
        $b3Num  = get_system_setting('inv_bank3_number', '8809-1122-3344');

        $db->table('acc_coa')->where('account_code', '111-001')->update([
            'account_name'        => 'Kas Bank - ' . $b1Name,
            'bank_name'           => $b1Name,
            'bank_account_number' => $b1Num,
        ]);
        $db->table('acc_coa')->where('account_code', '111-002')->update([
            'account_name'        => 'Kas Bank - ' . $b2Name,
            'bank_name'           => $b2Name,
            'bank_account_number' => $b2Num,
        ]);
        $db->table('acc_coa')->where('account_code', '111-003')->update([
            'account_name'        => 'Kas Bank - ' . $b3Name,
            'bank_name'           => $b3Name,
            'bank_account_number' => $b3Num,
        ]);
    }

    protected static function seedDefaultCoa($db): void
    {
        $now    = date('Y-m-d H:i:s');
        $b1Name = get_system_setting('inv_bank1_name', 'Bank Mandiri (Escrow)');
        $b1Num  = get_system_setting('inv_bank1_number', '122-00-1122334-5');
        $b2Name = get_system_setting('inv_bank2_name', 'Bank BCA (Escrow)');
        $b2Num  = get_system_setting('inv_bank2_number', '535-088-9900');
        $b3Name = get_system_setting('inv_bank3_name', 'Bank BNI Corporate VA');
        $b3Num  = get_system_setting('inv_bank3_number', '8809-1122-3344');

        $accounts = [
            // 1xx-xxx: ASSET (AKTIVA)
            ['110-001', 'Kas Kecil Operasional (Petty Cash)', 'ASSET', 'Kas & Setara Kas', 'DEBIT', 'NERACA', 1, 'Kas Tunai Kantor', 'PETTY-CASH-01', 25000000.00, 1, 'Kas tunai operasional harian kantor pusat'],
            ['111-001', 'Kas Bank - ' . $b1Name, 'ASSET', 'Kas & Setara Kas', 'DEBIT', 'NERACA', 1, $b1Name, $b1Num, 450000000.00, 1, 'Rekening penampungan utama (Escrow) transaksi B2B'],
            ['111-002', 'Kas Bank - ' . $b2Name, 'ASSET', 'Kas & Setara Kas', 'DEBIT', 'NERACA', 1, $b2Name, $b2Num, 275000000.00, 1, 'Rekening operasional & penerimaan pembayaran kedua'],
            ['111-003', 'Kas Bank - ' . $b3Name, 'ASSET', 'Kas & Setara Kas', 'DEBIT', 'NERACA', 1, $b3Name, $b3Num, 120000000.00, 1, 'Rekening Corporate Virtual Account BNI'],
            ['112-001', 'Piutang Usaha - Corporate Buyer (AR)', 'ASSET', 'Piutang Usaha', 'DEBIT', 'NERACA', 0, null, null, 0.00, 1, 'Tagihan piutang TOP atas PO pelanggan korporat'],
            ['112-002', 'Cadangan Kerugian Piutang', 'ASSET', 'Piutang Usaha', 'CREDIT', 'NERACA', 0, null, null, 0.00, 0, 'Penyisihan piutang ragu-ragu'],
            ['113-001', 'Uang Muka Kepada Mitra Vendor', 'ASSET', 'Aset Lancar Lainnya', 'DEBIT', 'NERACA', 0, null, null, 0.00, 0, 'Deposit / DP pengadaan barang ke supplier'],
            ['114-001', 'PPN Masukan (Input VAT)', 'ASSET', 'Pajak Dibayar Dimuka', 'DEBIT', 'NERACA', 0, null, null, 0.00, 1, 'Faktur pajak masukan dari mitra vendor pengadaan'],
            ['114-002', 'PPh Pasal 22/23 Dibayar Dimuka', 'ASSET', 'Pajak Dibayar Dimuka', 'DEBIT', 'NERACA', 0, null, null, 0.00, 0, 'Kredit pajak penghasilan dipotong pihak ketiga'],
            ['121-001', 'Peralatan & Infrastruktur Server IT', 'ASSET', 'Aset Tetap', 'DEBIT', 'NERACA', 0, null, null, 180000000.00, 0, 'Inventaris server, lisensi sistem, dan perangkat kerja'],
            ['121-002', 'Inventaris & Furnitur Kantor', 'ASSET', 'Aset Tetap', 'DEBIT', 'NERACA', 0, null, null, 65000000.00, 0, 'Peralatan dan furnitur kantor pusat AkuAda'],
            ['122-001', 'Akumulasi Penyusutan Aset Tetap', 'ASSET', 'Aset Tetap', 'CREDIT', 'NERACA', 0, null, null, 35000000.00, 0, 'Akumulasi depresiasi peralatan & inventaris'],

            // 2xx-xxx: LIABILITY (KEWAJIBAN / HUTANG)
            ['210-001', 'Hutang Usaha - Settlement Mitra Vendor (AP)', 'LIABILITY', 'Kewajiban Jangka Pendek', 'CREDIT', 'NERACA', 0, null, null, 0.00, 1, 'Kewajiban pembayaran dana bersih (net payout) ke mitra vendor'],
            ['210-002', 'Biaya yang Masih Harus Dibayar (Accrued Expense)', 'LIABILITY', 'Kewajiban Jangka Pendek', 'CREDIT', 'NERACA', 0, null, null, 15000000.00, 0, 'Beban operasional akrual akhir bulan'],
            ['211-001', 'PPN Keluaran (Output VAT)', 'LIABILITY', 'Hutang Pajak', 'CREDIT', 'NERACA', 0, null, null, 0.00, 1, 'Kewajiban PPN e-Faktur atas penjualan ke Corporate Buyer'],
            ['212-001', 'Hutang PPh Pasal 21/22/23', 'LIABILITY', 'Hutang Pajak', 'CREDIT', 'NERACA', 0, null, null, 0.00, 0, 'Hutang pajak penghasilan potongan'],
            ['220-001', 'Kewajiban Pembiayaan Jangka Panjang', 'LIABILITY', 'Kewajiban Jangka Panjang', 'CREDIT', 'NERACA', 0, null, null, 0.00, 0, 'Fasilitas pendanaan modal kerja jangka panjang'],

            // 3xx-xxx: EQUITY (MODAL / EKUITAS)
            ['310-001', 'Modal Disetor Perusahaan (Paid-in Capital)', 'EQUITY', 'Ekuitas Pemegang Saham', 'CREDIT', 'NERACA', 0, null, null, 850000000.00, 1, 'Modal dasar dan disetor penuh pemegang saham AkuAda'],
            ['320-001', 'Saldo Laba Ditahan (Retained Earnings)', 'EQUITY', 'Ekuitas Pemegang Saham', 'CREDIT', 'NERACA', 0, null, null, 215000000.00, 1, 'Akumulasi laba bersih periode sebelumnya'],
            ['330-001', 'Dividen / Prive', 'EQUITY', 'Ekuitas Pemegang Saham', 'DEBIT', 'NERACA', 0, null, null, 0.00, 0, 'Pembagian dividen kepada pemegang saham'],

            // 4xx-xxx: REVENUE (PENDAPATAN USAHA)
            ['410-001', 'Pendapatan Penjualan Pengadaan B2B (Gross)', 'REVENUE', 'Pendapatan Usaha Utama', 'CREDIT', 'LABA_RUGI', 0, null, null, 0.00, 1, 'Nilai DPP penjualan barang pengadaan kepada Corporate Buyer'],
            ['411-001', 'Pendapatan Komisi Platform (Vendor Fee)', 'REVENUE', 'Pendapatan Usaha Utama', 'CREDIT', 'LABA_RUGI', 0, null, null, 0.00, 1, 'Pendapatan fee/komisi layanan platform dari transaksi mitra vendor'],
            ['412-001', 'Pendapatan Jasa Logistik & Pengiriman', 'REVENUE', 'Pendapatan Usaha Utama', 'CREDIT', 'LABA_RUGI', 0, null, null, 0.00, 1, 'Pendapatan biaya pengiriman logistik atas PO pelanggan'],
            ['413-001', 'Potongan & Retur Penjualan B2B', 'REVENUE', 'Pengurang Pendapatan', 'DEBIT', 'LABA_RUGI', 0, null, null, 0.00, 0, 'Diskon khusus penyelesaian klaim / retur pengadaan'],

            // 5xx-xxx: COGS (HARGA POKOK PENGADAAN / BEBAN POKOK)
            ['510-001', 'Harga Pokok Pengadaan Barang Vendor (COGS)', 'COGS', 'Harga Pokok Penjualan', 'DEBIT', 'LABA_RUGI', 0, null, null, 0.00, 1, 'Beban pokok pengadaan barang dari mitra pemasok (Vendor)'],
            ['511-001', 'Beban Ekspedisi & Kurir Logistik B2B', 'COGS', 'Harga Pokok Penjualan', 'DEBIT', 'LABA_RUGI', 0, null, null, 0.00, 0, 'Biaya armada pengiriman barang ke gudang pembeli'],

            // 6xx-xxx: EXPENSE (BEBAN OPERASIONAL)
            ['610-001', 'Beban Gaji, Tunjangan & Kesejahteraan Tim', 'EXPENSE', 'Beban Operasional & Umum', 'DEBIT', 'LABA_RUGI', 0, null, null, 0.00, 0, 'Gaji staf operasional, account manager, dan engineering'],
            ['611-001', 'Beban Server, Cloud Hosting & Lisensi IT', 'EXPENSE', 'Beban Operasional & Umum', 'DEBIT', 'LABA_RUGI', 0, null, null, 0.00, 0, 'Biaya infrastruktur cloud, keamanan data, dan integrasi e-Faktur'],
            ['612-001', 'Beban Pemasaran & Akuisisi Korporat', 'EXPENSE', 'Beban Pemasaran', 'DEBIT', 'LABA_RUGI', 0, null, null, 0.00, 0, 'Biaya promosi tender B2B dan pengembangan kemitraan'],
            ['613-001', 'Beban Administrasi Bank & Payment Gateway', 'EXPENSE', 'Beban Operasional & Umum', 'DEBIT', 'LABA_RUGI', 0, null, null, 0.00, 0, 'Biaya layanan escrow, kliring, dan virtual account'],
            ['614-001', 'Beban Sewa Kantor & Utilitas Gedung', 'EXPENSE', 'Beban Operasional & Umum', 'DEBIT', 'LABA_RUGI', 0, null, null, 0.00, 0, 'Sewa ruang kantor, listrik, internet dedicated, dan perawatan'],
            ['615-001', 'Beban Penyusutan Aset Tetap', 'EXPENSE', 'Beban Operasional & Umum', 'DEBIT', 'LABA_RUGI', 0, null, null, 0.00, 0, 'Beban depresiasi bulanan aset tetap'],

            // 7xx-xxx & 8xx-xxx: OTHER INCOME & EXPENSE
            ['710-001', 'Pendapatan Jasa Giro & Bunga Bank Escrow', 'OTHER_INCOME', 'Pendapatan Lain-Lain', 'CREDIT', 'LABA_RUGI', 0, null, null, 0.00, 0, 'Pendapatan bunga rekening bank korporat'],
            ['810-001', 'Beban Pajak Bunga & Administrasi Lainnya', 'OTHER_EXPENSE', 'Beban Lain-Lain', 'DEBIT', 'LABA_RUGI', 0, null, null, 0.00, 0, 'Pajak final jasa giro dan biaya non-operasional'],
        ];

        $batch = [];
        foreach ($accounts as $acc) {
            $batch[] = [
                'account_code'        => $acc[0],
                'account_name'        => $acc[1],
                'account_category'    => $acc[2],
                'account_group'       => $acc[3],
                'normal_balance'      => $acc[4],
                'report_position'     => $acc[5],
                'is_cash_bank'        => $acc[6],
                'bank_name'           => $acc[7],
                'bank_account_number' => $acc[8],
                'opening_balance'     => $acc[9],
                'is_system'           => $acc[10],
                'is_active'           => 1,
                'description'         => $acc[11],
                'created_at'          => $now,
                'updated_at'          => $now,
            ];
        }
        $db->table('acc_coa')->insertBatch($batch);
    }

    public static function syncOrdersToJournals($db = null): void
    {
        if (!$db) {
            $db = \Config\Database::connect();
        }
        if (!$db->tableExists('orders') || !$db->tableExists('acc_journals')) {
            return;
        }

        // Ensure orders are synced with active tax & fee settings first
        if (function_exists('sync_orders_with_system_settings')) {
            sync_orders_with_system_settings();
        }

        // Map account_code => id
        $coaRows = $db->table('acc_coa')->select('id, account_code')->get()->getResultArray();
        $coaMap  = [];
        foreach ($coaRows as $r) {
            $coaMap[$r['account_code']] = (int)$r['id'];
        }

        $requiredCodes = ['111-001', '111-002', '112-001', '114-001', '210-001', '211-001', '410-001', '411-001', '412-001', '510-001'];
        foreach ($requiredCodes as $code) {
            if (!isset($coaMap[$code])) {
                return;
            }
        }

        $orders = $db->table('orders')
            ->select('orders.*, buyer.company_name as buyer_name, vendor.company_name as vendor_name')
            ->join('companies as buyer', 'buyer.id = orders.customer_company_id', 'left')
            ->join('companies as vendor', 'vendor.id = orders.vendor_company_id', 'left')
            ->where('orders.order_status !=', 'cancelled')
            ->orderBy('orders.id', 'ASC')
            ->get()
            ->getResultArray();

        $now = date('Y-m-d H:i:s');

        foreach ($orders as $ord) {
            $oid        = (int)$ord['id'];
            $poNumber   = $ord['po_number'] ?? $ord['order_number'];
            $ordDate    = !empty($ord['created_at']) ? date('Y-m-d', strtotime($ord['created_at'])) : date('Y-m-d');
            $subtotal   = round((float)($ord['subtotal'] ?? 0), 2);
            $ppnAmount  = round((float)($ord['ppn_amount'] ?? 0), 2);
            $shipAmount = round((float)($ord['shipping_amount'] ?? 0), 2);
            $totalAmt   = round((float)($ord['total_amount'] ?? ($subtotal + $ppnAmount + $shipAmount)), 2);
            $vendorFee  = round((float)($ord['total_vendor_fee'] ?? 0), 2);
            $vendorNet  = round((float)($ord['vendor_net_payout'] ?? 0), 2);
            $buyerName  = $ord['buyer_name'] ?? 'Corporate Buyer';
            $vendorName = $ord['vendor_name'] ?? 'Mitra Vendor';

            // 1. SALES_PO Journal (Pengakuan Piutang Buyer, Penjualan, PPN, HPP Vendor, Hutang Vendor & Fee Platform)
            $jrnPoNum  = 'JRN/PO/' . str_pad((string)$oid, 4, '0', STR_PAD_LEFT);
            $existingPo = $db->table('acc_journals')->where('journal_number', $jrnPoNum)->get()->getRowArray();

            // Balance check for COGS side: vendorNet + vendorFee = subtotal + ppnAmount
            $cogsSideTotal = round($vendorNet + $vendorFee, 2);
            $cogsDpp       = round($cogsSideTotal - $ppnAmount, 2);
            if ($cogsDpp < 0) {
                $cogsDpp = $subtotal;
            }

            $totalJournalSide = round($totalAmt + $cogsSideTotal, 2);

            $poLines = [
                [
                    'coa_id'           => $coaMap['112-001'],
                    'account_code'     => '112-001',
                    'line_description' => 'Piutang Tagihan PO ' . $poNumber . ' - ' . $buyerName,
                    'debit'            => $totalAmt,
                    'credit'           => 0.00,
                ],
                [
                    'coa_id'           => $coaMap['410-001'],
                    'account_code'     => '410-001',
                    'line_description' => 'Pendapatan Pengadaan PO ' . $poNumber . ' (DPP)',
                    'debit'            => 0.00,
                    'credit'           => $subtotal,
                ],
                [
                    'coa_id'           => $coaMap['211-001'],
                    'account_code'     => '211-001',
                    'line_description' => 'PPN Keluaran e-Faktur PO ' . $poNumber,
                    'debit'            => 0.00,
                    'credit'           => $ppnAmount,
                ],
            ];

            if ($shipAmount > 0) {
                $poLines[] = [
                    'coa_id'           => $coaMap['412-001'],
                    'account_code'     => '412-001',
                    'line_description' => 'Pendapatan Logistik & Pengiriman PO ' . $poNumber,
                    'debit'            => 0.00,
                    'credit'           => $shipAmount,
                ];
            }

            // HPP & Kewajiban Settlement Vendor + Pendapatan Komisi Platform
            $poLines[] = [
                'coa_id'           => $coaMap['510-001'],
                'account_code'     => '510-001',
                'line_description' => 'Beban Pokok Pengadaan (COGS) ke ' . $vendorName,
                'debit'            => $cogsDpp,
                'credit'           => 0.00,
            ];
            if ($ppnAmount > 0) {
                $poLines[] = [
                    'coa_id'           => $coaMap['114-001'],
                    'account_code'     => '114-001',
                    'line_description' => 'PPN Masukan Faktur Vendor - ' . $vendorName,
                    'debit'            => $ppnAmount,
                    'credit'           => 0.00,
                ];
            }
            $poLines[] = [
                'coa_id'           => $coaMap['210-001'],
                'account_code'     => '210-001',
                'line_description' => 'Hutang Settlement Bersih (Net Payout) - ' . $vendorName,
                'debit'            => 0.00,
                'credit'           => $vendorNet,
            ];
            if ($vendorFee > 0) {
                $poLines[] = [
                    'coa_id'           => $coaMap['411-001'],
                    'account_code'     => '411-001',
                    'line_description' => 'Pendapatan Komisi Platform AkuAda dari ' . $vendorName,
                    'debit'            => 0.00,
                    'credit'           => $vendorFee,
                ];
            }

            if (!$existingPo) {
                $db->table('acc_journals')->insert([
                    'journal_number'   => $jrnPoNum,
                    'journal_date'     => $ordDate,
                    'journal_type'     => 'SALES_PO',
                    'reference_type'   => 'order_po',
                    'reference_id'     => $oid,
                    'reference_number' => $poNumber,
                    'description'      => 'Jurnal Otomatis Penerbitan PO & Pengakuan Tagihan #' . $poNumber . ' (' . $buyerName . ' -> ' . $vendorName . ')',
                    'total_debit'      => $totalJournalSide,
                    'total_credit'     => $totalJournalSide,
                    'status'           => 'POSTED',
                    'created_by'       => 'Auto-GL Engine',
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
                $jid = (int)$db->insertID();
            } else {
                $jid = (int)$existingPo['id'];
                $db->table('acc_journals')->where('id', $jid)->update([
                    'total_debit'  => $totalJournalSide,
                    'total_credit' => $totalJournalSide,
                    'updated_at'   => $now,
                ]);
                $db->table('acc_journal_items')->where('journal_id', $jid)->delete();
            }

            foreach ($poLines as $line) {
                $db->table('acc_journal_items')->insert(array_merge($line, [
                    'journal_id' => $jid,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }

            // 2. RECEIPT_AR Journal if Buyer has paid (payment_status === 'paid')
            $jrnArNum   = 'JRN/AR/' . str_pad((string)$oid, 4, '0', STR_PAD_LEFT);
            $existingAr = $db->table('acc_journals')->where('journal_number', $jrnArNum)->get()->getRowArray();

            if (($ord['payment_status'] ?? '') === 'paid') {
                $paidDate = !empty($ord['buyer_paid_at']) ? date('Y-m-d', strtotime($ord['buyer_paid_at'])) : $ordDate;
                $bankCode = (stripos($ord['buyer_payment_bank'] ?? '', 'BCA') !== false) ? '111-002' : '111-001';

                if (!$existingAr) {
                    $db->table('acc_journals')->insert([
                        'journal_number'   => $jrnArNum,
                        'journal_date'     => $paidDate,
                        'journal_type'     => 'RECEIPT_AR',
                        'reference_type'   => 'order_ar',
                        'reference_id'     => $oid,
                        'reference_number' => $ord['invoice_number'] ?? $poNumber,
                        'description'      => 'Pelunasan Piutang Invoice ' . ($ord['invoice_number'] ?? $poNumber) . ' dari ' . $buyerName,
                        'total_debit'      => $totalAmt,
                        'total_credit'     => $totalAmt,
                        'status'           => 'POSTED',
                        'created_by'       => 'Auto-GL Engine',
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                    $arJid = (int)$db->insertID();
                } else {
                    $arJid = (int)$existingAr['id'];
                    $db->table('acc_journals')->where('id', $arJid)->update([
                        'journal_date' => $paidDate,
                        'total_debit'  => $totalAmt,
                        'total_credit' => $totalAmt,
                        'updated_at'   => $now,
                    ]);
                    $db->table('acc_journal_items')->where('journal_id', $arJid)->delete();
                }

                $db->table('acc_journal_items')->insertBatch([
                    [
                        'journal_id'       => $arJid,
                        'coa_id'           => $coaMap[$bankCode],
                        'account_code'     => $bankCode,
                        'line_description' => 'Penerimaan Bank Pelunasan PO ' . $poNumber . ' (' . $buyerName . ')',
                        'debit'            => $totalAmt,
                        'credit'           => 0.00,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ],
                    [
                        'journal_id'       => $arJid,
                        'coa_id'           => $coaMap['112-001'],
                        'account_code'     => '112-001',
                        'line_description' => 'Pelunasan Piutang Usaha PO ' . $poNumber . ' (' . $buyerName . ')',
                        'debit'            => 0.00,
                        'credit'           => $totalAmt,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ],
                ]);
            }

            // 3. PAYMENT_AP Journal if Vendor Payout is paid (vendor_payout_status === 'paid')
            $jrnApNum   = 'JRN/AP/' . str_pad((string)$oid, 4, '0', STR_PAD_LEFT);
            $existingAp = $db->table('acc_journals')->where('journal_number', $jrnApNum)->get()->getRowArray();

            if (($ord['vendor_payout_status'] ?? '') === 'paid') {
                $payoutDate = !empty($ord['vendor_paid_at']) ? date('Y-m-d', strtotime($ord['vendor_paid_at'])) : $ordDate;

                if (!$existingAp) {
                    $db->table('acc_journals')->insert([
                        'journal_number'   => $jrnApNum,
                        'journal_date'     => $payoutDate,
                        'journal_type'     => 'PAYMENT_AP',
                        'reference_type'   => 'order_ap',
                        'reference_id'     => $oid,
                        'reference_number' => $ord['vendor_invoice_number'] ?? $poNumber,
                        'description'      => 'Pencairan Settlement Hutang Vendor ' . ($ord['vendor_invoice_number'] ?? $poNumber) . ' ke ' . $vendorName,
                        'total_debit'      => $vendorNet,
                        'total_credit'     => $vendorNet,
                        'status'           => 'POSTED',
                        'created_by'       => 'Auto-GL Engine',
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ]);
                    $apJid = (int)$db->insertID();
                } else {
                    $apJid = (int)$existingAp['id'];
                    $db->table('acc_journals')->where('id', $apJid)->update([
                        'journal_date' => $payoutDate,
                        'total_debit'  => $vendorNet,
                        'total_credit' => $vendorNet,
                        'updated_at'   => $now,
                    ]);
                    $db->table('acc_journal_items')->where('journal_id', $apJid)->delete();
                }

                $db->table('acc_journal_items')->insertBatch([
                    [
                        'journal_id'       => $apJid,
                        'coa_id'           => $coaMap['210-001'],
                        'account_code'     => '210-001',
                        'line_description' => 'Pelunasan Hutang Settlement Vendor PO ' . $poNumber . ' (' . $vendorName . ')',
                        'debit'            => $vendorNet,
                        'credit'           => 0.00,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ],
                    [
                        'journal_id'       => $apJid,
                        'coa_id'           => $coaMap['111-001'],
                        'account_code'     => '111-001',
                        'line_description' => 'Transfer Keluar Bank Escrow ke ' . $vendorName . ' (Ref: ' . ($ord['vendor_payout_ref'] ?? 'N/A') . ')',
                        'debit'            => 0.00,
                        'credit'           => $vendorNet,
                        'created_at'       => $now,
                        'updated_at'       => $now,
                    ],
                ]);
            }
        }

        // Seed 2 realistic operational expense journals once so R/L and Cash Book have OPEX examples too
        $opexJrn = $db->table('acc_journals')->where('journal_number', 'JRN/OPEX/0001')->get()->getRowArray();
        if (!$opexJrn && isset($coaMap['611-001'], $coaMap['610-001'], $coaMap['111-002'], $coaMap['710-001'])) {
            $db->table('acc_journals')->insert([
                'journal_number'   => 'JRN/OPEX/0001',
                'journal_date'     => date('Y-m-05'),
                'journal_type'     => 'CASH_OUT',
                'reference_type'   => 'opex',
                'reference_id'     => 1,
                'reference_number' => 'VCH/OPEX/' . date('Ym') . '/01',
                'description'      => 'Pembayaran Biaya Cloud Server, Integrasi e-Faktur & Operasional Tim Bulanan',
                'total_debit'      => 18500000.00,
                'total_credit'     => 18500000.00,
                'status'           => 'POSTED',
                'created_by'       => 'Finance Controller',
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
            $opJid = (int)$db->insertID();
            $db->table('acc_journal_items')->insertBatch([
                [
                    'journal_id'       => $opJid,
                    'coa_id'           => $coaMap['610-001'],
                    'account_code'     => '610-001',
                    'line_description' => 'Gaji & Tunjangan Tim Operasional E-Procurement',
                    'debit'            => 15000000.00,
                    'credit'           => 0.00,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ],
                [
                    'journal_id'       => $opJid,
                    'coa_id'           => $coaMap['611-001'],
                    'account_code'     => '611-001',
                    'line_description' => 'Langganan Dedicated Cloud Server & API e-Faktur DJP',
                    'debit'            => 3500000.00,
                    'credit'           => 0.00,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ],
                [
                    'journal_id'       => $opJid,
                    'coa_id'           => $coaMap['111-002'],
                    'account_code'     => '111-002',
                    'line_description' => 'Pengeluaran Bank Operasional BCA',
                    'debit'            => 0.00,
                    'credit'           => 18500000.00,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ],
            ]);
        }
    }

    /**
     * Get all COA accounts with calculated mutation & ending balance for an optional date range.
     */
    public function getCoaWithBalances(?string $startDate = null, ?string $endDate = null): array
    {
        self::ensureAccountingInitialized();
        $db = \Config\Database::connect();

        $accounts = $this->orderBy('account_code', 'ASC')->findAll();

        // Sum mutations before startDate (if startDate provided)
        $priorMap = [];
        if ($startDate) {
            $priorRows = $db->table('acc_journal_items ji')
                ->select('ji.coa_id, SUM(ji.debit) as sum_debit, SUM(ji.credit) as sum_credit')
                ->join('acc_journals j', 'j.id = ji.journal_id')
                ->where('j.status', 'POSTED')
                ->where('j.journal_date <', $startDate)
                ->groupBy('ji.coa_id')
                ->get()
                ->getResultArray();
            foreach ($priorRows as $pr) {
                $priorMap[(int)$pr['coa_id']] = [
                    'debit'  => (float)$pr['sum_debit'],
                    'credit' => (float)$pr['sum_credit'],
                ];
            }
        }

        // Sum mutations within [startDate, endDate]
        $periodBuilder = $db->table('acc_journal_items ji')
            ->select('ji.coa_id, SUM(ji.debit) as sum_debit, SUM(ji.credit) as sum_credit')
            ->join('acc_journals j', 'j.id = ji.journal_id')
            ->where('j.status', 'POSTED');

        if ($startDate) {
            $periodBuilder->where('j.journal_date >=', $startDate);
        }
        if ($endDate) {
            $periodBuilder->where('j.journal_date <=', $endDate);
        }

        $periodRows = $periodBuilder->groupBy('ji.coa_id')->get()->getResultArray();
        $periodMap  = [];
        foreach ($periodRows as $pr) {
            $periodMap[(int)$pr['coa_id']] = [
                'debit'  => (float)$pr['sum_debit'],
                'credit' => (float)$pr['sum_credit'],
            ];
        }

        foreach ($accounts as &$acc) {
            $cid     = (int)$acc['id'];
            $openBal = (float)($acc['opening_balance'] ?? 0);
            $isDebit = ($acc['normal_balance'] === 'DEBIT');

            $priorDebit  = $priorMap[$cid]['debit'] ?? 0.0;
            $priorCredit = $priorMap[$cid]['credit'] ?? 0.0;

            $effectiveOpening = $isDebit
                ? ($openBal + $priorDebit - $priorCredit)
                : ($openBal + $priorCredit - $priorDebit);

            $mutDebit  = $periodMap[$cid]['debit'] ?? 0.0;
            $mutCredit = $periodMap[$cid]['credit'] ?? 0.0;

            $endingBalance = $isDebit
                ? ($effectiveOpening + $mutDebit - $mutCredit)
                : ($effectiveOpening + $mutCredit - $mutDebit);

            $acc['effective_opening'] = round($effectiveOpening, 2);
            $acc['mutation_debit']    = round($mutDebit, 2);
            $acc['mutation_credit']   = round($mutCredit, 2);
            $acc['ending_balance']    = round($endingBalance, 2);
        }
        unset($acc);

        return $accounts;
    }
}

