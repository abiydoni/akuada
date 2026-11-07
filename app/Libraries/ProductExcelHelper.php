<?php

namespace App\Libraries;

use App\Models\CategoryModel;
use App\Models\CompanyModel;
use ZipArchive;

class ProductExcelHelper
{
    /**
     * Daftar kode angka untuk satuan jual (UOM)
     */
    public static array $uomMap = [
        1  => 'Pcs',
        2  => 'Box',
        3  => 'Unit',
        4  => 'Karton',
        5  => 'Rim',
        6  => 'Paket',
        7  => 'Set',
        8  => 'Roll',
        9  => 'Meter',
        10 => 'Kg',
        11 => 'Liter',
        12 => 'Pasang',
    ];

    /**
     * Generate file Excel (.xlsx) resmi dengan 2 Sheet:
     * Sheet 1: Data Barang (dengan kolom terpisah rapi & contoh input angka)
     * Sheet 2: Panduan & Kode Angka (daftar ID kategori & kode satuan UOM)
     */
    public static function generateTemplate(bool $isAdmin = false, ?int $vendorCompanyId = null): string
    {
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->orderBy('id', 'ASC')->findAll();

        $companyModel = new CompanyModel();
        $sampleVendors = $isAdmin ? $companyModel->where('type', 'vendor')->findAll(3) : [];
        $v1 = !empty($sampleVendors[0]['company_name']) ? $sampleVendors[0]['company_name'] : 'PT Mitra Pemasok Mandiri';
        $v2 = !empty($sampleVendors[1]['company_name']) ? $sampleVendors[1]['company_name'] : 'CV Berkah Teknik Sentosa';
        $v3 = !empty($sampleVendors[2]['company_name']) ? $sampleVendors[2]['company_name'] : $v1;

        // Headers
        if ($isAdmin) {
            $headers = [
                'Vendor (ID / Nama PT)',
                'Kategori (ID Angka / Teks)',
                'Nama Barang (Wajib)',
                'SKU (Kosong = Otomatis)',
                'Barcode (Kosong = Otomatis)',
                'Merk / Brand',
                'Satuan UOM (Kode Angka / Teks)',
                'Harga B2B Regular (Rp)',
                'Stok (Kosong = 100)',
                'MOQ Min Order (Kosong = 1)',
                'Berat Gram (Kosong = 500)',
                'Lead Time Hari (Kosong = 2)',
                'TKDN Persen',
                'Kena PPN (1=Ya, 0=Tidak)',
                'Bisa Sample (1=Ya, 0=Tidak)',
                'Deskripsi Barang',
                'Spesifikasi Teknis',
                'URL Foto Barang'
            ];
            $sampleRows = [
                [
                    $v1,
                    '1',
                    'Kertas HVS PaperOne A4 80gr (Box isi 5 Rim)',
                    '',
                    '',
                    'PaperOne',
                    '2', // Box
                    245000,
                    150,
                    2,
                    12500,
                    2,
                    85.5,
                    1,
                    1,
                    'Kertas HVS putih bersih ukuran A4 gramatur 80 gsm premium untuk dokumen resmi kantor.',
                    'Ukuran: 210 x 297 mm; Ketebalan: 80 gsm; Kemasan: 1 Box isi 5 Rim @ 500 lembar (Total 2.500 lembar); Sertifikasi PEFC & ISO 9001',
                    'https://images.unsplash.com/photo-1589330694653-dad6d3240e2b?auto=format&fit=crop&w=600&q=80'
                ],
                [
                    $v2,
                    '2',
                    'Keyboard Wireless Logitech MK270 Combo Mouse Silent',
                    '',
                    '',
                    'Logitech',
                    '3', // Unit
                    360000,
                    50,
                    1,
                    800,
                    3,
                    40.0,
                    1,
                    0,
                    'Paket keyboard dan mouse wireless 2.4 GHz baterai tahan lama dan koneksi stabil hingga 10 meter.',
                    'Koneksi: USB Receiver 2.4GHz; Jangkauan: 10 meter; Baterai: 2x AAA, 1x AA; Kompatibel: Windows 10/11 & macOS; Garansi resmi 1 tahun',
                    'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=600&q=80'
                ],
                [
                    $v3,
                    '3',
                    'Helm Safety Proyek SNI MSA V-Gard Fastrac Putih',
                    '',
                    '',
                    'MSA',
                    '1', // Pcs
                    125000,
                    80,
                    5,
                    450,
                    2,
                    78.0,
                    1,
                    1,
                    'Helm keselamatan kerja standar SNI dan ANSI dengan suspensi putar Fastrac untuk perlindungan benturan.',
                    'Material: Polietilen (HDPE); Standar: SNI ISO 3873 & ANSI/ISEA Z89.1; Suspensi: 4 titik Fastrac putar; Warna: Putih',
                    'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=600&q=80'
                ]
            ];
        } else {
            $headers = [
                'Kategori (ID Angka / Teks)',
                'Nama Barang (Wajib)',
                'SKU (Kosong = Otomatis)',
                'Barcode (Kosong = Otomatis)',
                'Merk / Brand',
                'Satuan UOM (Kode Angka / Teks)',
                'Harga B2B Regular (Rp)',
                'Stok (Kosong = 100)',
                'MOQ Min Order (Kosong = 1)',
                'Berat Gram (Kosong = 500)',
                'Lead Time Hari (Kosong = 2)',
                'TKDN Persen',
                'Kena PPN (1=Ya, 0=Tidak)',
                'Bisa Sample (1=Ya, 0=Tidak)',
                'Deskripsi Barang',
                'Spesifikasi Teknis',
                'URL Foto Barang'
            ];
            $sampleRows = [
                [
                    '1', // Kategori ID 1
                    'Kertas HVS PaperOne A4 80gr (Box isi 5 Rim)',
                    '',  // Kosongkan agar otomatis dibuat sistem
                    '',  // Kosongkan agar otomatis
                    'PaperOne',
                    '2', // Kode 2 = Box
                    245000,
                    150,
                    2,
                    12500,
                    2,
                    85.5,
                    1,
                    1,
                    'Kertas HVS putih bersih ukuran A4 gramatur 80 gsm premium untuk cetak dokumen resmi kantor.',
                    'Ukuran: 210 x 297 mm; Ketebalan: 80 gsm; Kemasan: 1 Box isi 5 Rim @ 500 lembar (Total 2.500 lembar); Sertifikasi PEFC & ISO 9001',
                    'https://images.unsplash.com/photo-1589330694653-dad6d3240e2b?auto=format&fit=crop&w=600&q=80'
                ],
                [
                    '2', // Kategori ID 2
                    'Keyboard Wireless Logitech MK270 Combo Mouse Silent',
                    '',
                    '',
                    'Logitech',
                    '3', // Kode 3 = Unit
                    360000,
                    50,
                    1,
                    800,
                    3,
                    40.0,
                    1,
                    0,
                    'Paket keyboard dan mouse wireless 2.4 GHz baterai tahan lama dan koneksi stabil hingga 10 meter.',
                    'Koneksi: USB Receiver 2.4GHz; Jangkauan: 10 meter; Baterai: 2x AAA, 1x AA; Kompatibel: Windows 10/11 & macOS; Garansi resmi 1 tahun',
                    'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=600&q=80'
                ],
                [
                    '3', // Kategori ID 3
                    'Helm Safety Proyek SNI MSA V-Gard Fastrac Putih',
                    '',
                    '',
                    'MSA',
                    '1', // Kode 1 = Pcs
                    125000,
                    80,
                    5,
                    450,
                    2,
                    78.0,
                    1,
                    1,
                    'Helm keselamatan kerja standar SNI dan ANSI dengan suspensi putar Fastrac untuk perlindungan industri.',
                    'Material: Polietilen (HDPE); Standar: SNI ISO 3873 & ANSI/ISEA Z89.1; Suspensi: 4 titik Fastrac putar; Warna: Putih',
                    'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=600&q=80'
                ]
            ];
        }

        // Build Sheet 1 (Data Barang)
        $sheet1Xml = self::buildSheetXml($headers, $sampleRows);

        // Build Sheet 2 (Panduan & Kode Angka)
        $guideHeaders = ['Kode / ID', 'Nama Standar Baku', 'Keterangan Penggunaan'];
        $guideRows = [
            ['=== DAFTAR KODE KATEGORI (KOLOM KATEGORI) ===', '', ''],
        ];
        foreach ($categories as $cat) {
            $guideRows[] = [(string)$cat['id'], (string)$cat['name'], 'Cukup tulis angka ' . $cat['id'] . ' pada kolom Kategori (atau ketik nama kategorinya)'];
        }
        $guideRows[] = ['', '', ''];
        $guideRows[] = ['=== DAFTAR KODE SATUAN JUAL (KOLOM SATUAN UOM) ===', '', ''];
        foreach (self::$uomMap as $code => $label) {
            $guideRows[] = [(string)$code, $label, 'Cukup tulis angka ' . $code . ' pada kolom Satuan UOM (atau ketik ' . $label . ')'];
        }
        $guideRows[] = ['', '', ''];
        $guideRows[] = ['=== KETENTUAN PENGISIAN PRAKTIS ===', '', ''];
        $guideRows[] = ['SKU', 'Kosongkan', 'Jika dikosongkan, sistem AkuAda otomatis membuatkan kode SKU unik'];
        $guideRows[] = ['Barcode', 'Kosongkan', 'Jika dikosongkan, sistem otomatis membuatkan nomor barcode unik'];
        $guideRows[] = ['Kena PPN', '1 atau 0', 'Tulis 1 untuk Kena PPN 11%, atau 0 untuk Bebas PPN (default: 1)'];
        $guideRows[] = ['Bisa Sample', '1 atau 0', 'Tulis 1 jika buyer boleh minta sample uji coba, atau 0 jika tidak (default: 1)'];
        $guideRows[] = ['Stok', 'Angka (Kosong = 100)', 'Jika dikosongkan otomatis diisi 100 unit'];
        $guideRows[] = ['MOQ (Min Order)', 'Angka (Kosong = 1)', 'Minimal jumlah pembelian per pesanan, default 1'];
        $guideRows[] = ['Lead Time Hari', 'Angka (Kosong = 2)', 'Estimasi lama hari kerja pengiriman barang, default 2'];
        $guideRows[] = ['Berat Gram', 'Angka (Kosong = 500)', 'Berat barang per satuan dalam gram, default 500'];

        $sheet2Xml = self::buildSheetXml($guideHeaders, $guideRows);

        // Package into ZIP as XLSX
        $tempFile = tempnam(sys_get_temp_dir(), 'akuada_tpl_');
        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Gagal membuat file template Excel.');
        }

        // 1. [Content_Types].xml
        $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
</Types>';
        $zip->addFromString('[Content_Types].xml', $contentTypes);

        // 2. _rels/.rels
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
</Relationships>';
        $zip->addFromString('_rels/.rels', $rels);

        // 3. xl/_rels/workbook.xml.rels
        $wbRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
</Relationships>';
        $zip->addFromString('xl/_rels/workbook.xml.rels', $wbRels);

        // 4. xl/workbook.xml
        $wb = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Data Barang" sheetId="1" r:id="rId1"/>
    <sheet name="Panduan &amp; Kode Angka" sheetId="2" r:id="rId2"/>
  </sheets>
</workbook>';
        $zip->addFromString('xl/workbook.xml', $wb);

        // 5. Worksheets
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheet1Xml);
        $zip->addFromString('xl/worksheets/sheet2.xml', $sheet2Xml);

        $zip->close();
        $content = file_get_contents($tempFile);
        @unlink($tempFile);

        return $content;
    }

    /**
     * Parse file yang diunggah, baik berformat .xlsx maupun .csv
     */
    public static function parseUploadedFile(string $filePath, string $extension): array
    {
        $extension = strtolower($extension);

        if ($extension === 'xlsx') {
            return self::parseXlsxFile($filePath);
        }

        return self::parseCsvFile($filePath);
    }

    /**
     * Parse file format Excel .xlsx menggunakan ZipArchive & SimpleXML murni
     */
    private static function parseXlsxFile(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            return [];
        }

        // Shared strings jika ada
        $sharedStrings = [];
        if (($idx = $zip->locateName('xl/sharedStrings.xml')) !== false) {
            $xml = @simplexml_load_string($zip->getFromIndex($idx));
            if ($xml && isset($xml->si)) {
                foreach ($xml->si as $si) {
                    if (isset($si->t)) {
                        $sharedStrings[] = (string)$si->t;
                    } elseif (isset($si->r)) {
                        $fullStr = '';
                        foreach ($si->r as $rPart) {
                            $fullStr .= (string)($rPart->t ?? '');
                        }
                        $sharedStrings[] = $fullStr;
                    } else {
                        $sharedStrings[] = '';
                    }
                }
            }
        }

        // Baca worksheet pertama (sheet1.xml)
        $sheetXmlStr = $zip->getFromName('xl/worksheets/sheet1.xml');
        if (!$sheetXmlStr) {
            // Coba temukan file worksheet apa pun di xl/worksheets/
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $filename = $zip->getNameIndex($i);
                if (str_starts_with($filename, 'xl/worksheets/sheet') && str_ends_with($filename, '.xml')) {
                    $sheetXmlStr = $zip->getFromIndex($i);
                    break;
                }
            }
        }

        $zip->close();

        if (!$sheetXmlStr) {
            return [];
        }

        $sheetXml = @simplexml_load_string($sheetXmlStr);
        if (!$sheetXml || !isset($sheetXml->sheetData->row)) {
            return [];
        }

        $parsedRows = [];
        foreach ($sheetXml->sheetData->row as $row) {
            $rowCells = [];
            $maxColIdx = 0;

            foreach ($row->c as $c) {
                $ref = (string)$c['r']; // e.g. "A1", "B2"
                $colIdx = self::colLetterToIndex($ref);
                if ($colIdx > $maxColIdx) {
                    $maxColIdx = $colIdx;
                }

                $val = '';
                $type = (string)($c['t'] ?? '');

                if ($type === 'inlineStr') {
                    $val = (string)($c->is->t ?? '');
                } elseif ($type === 's') {
                    $sIdx = (int)($c->v ?? 0);
                    $val = $sharedStrings[$sIdx] ?? '';
                } elseif ($type === 'b') {
                    $val = ((string)($c->v ?? '') === '1') ? '1' : '0';
                } else {
                    $val = (string)($c->v ?? '');
                }

                $rowCells[$colIdx] = trim($val);
            }

            // Normalisasi array berurutan tanpa gap
            $orderedRow = [];
            for ($i = 0; $i <= $maxColIdx; $i++) {
                $orderedRow[$i] = $rowCells[$i] ?? '';
            }

            if (!empty(array_filter($orderedRow, fn($v) => trim((string)$v) !== ''))) {
                $parsedRows[] = $orderedRow;
            }
        }

        return $parsedRows;
    }

    /**
     * Parse file format CSV dengan auto-detect separator (, / ; / \t) & BOM
     */
    private static function parseCsvFile(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return [];
        }

        $firstLine = fgets($handle);
        $delimiter = ',';
        $semicolons = substr_count($firstLine, ';');
        $commas = substr_count($firstLine, ',');
        $tabs = substr_count($firstLine, "\t");

        if ($semicolons > $commas && $semicolons > $tabs) {
            $delimiter = ';';
        } elseif ($tabs > $commas && $tabs > $semicolons) {
            $delimiter = "\t";
        }

        rewind($handle);

        // Strip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (!empty(array_filter($row, fn($v) => trim((string)$v) !== ''))) {
                $rows[] = array_map('trim', $row);
            }
        }

        fclose($handle);
        return $rows;
    }

    /**
     * Konversi kode angka atau teks UOM menjadi nama UOM standar
     */
    public static function resolveUom(string $input): string
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            return 'Pcs';
        }

        if (is_numeric($trimmed)) {
            $num = (int)$trimmed;
            if (isset(self::$uomMap[$num])) {
                return self::$uomMap[$num];
            }
        }

        // Cek nama langsung (case-insensitive)
        foreach (self::$uomMap as $name) {
            if (strcasecmp($trimmed, $name) === 0) {
                return $name;
            }
        }

        // Jika teks lain seperti "Botol", "Liter", "Kaleng", tetap simpan
        return ucfirst(strtolower($trimmed));
    }

    /**
     * Konversi ID angka atau nama kategori menjadi category_id valid
     */
    public static function resolveCategoryId(string $input, array $allCategories, ?int $defaultId = 1): int
    {
        $trimmed = trim($input);
        if ($trimmed === '') {
            return $defaultId ?: 1;
        }

        // 1. Jika diisi angka ID kategori
        if (is_numeric($trimmed)) {
            $num = (int)$trimmed;
            foreach ($allCategories as $cat) {
                if ((int)$cat['id'] === $num) {
                    return $num;
                }
            }
        }

        // 2. Jika diisi teks nama kategori
        foreach ($allCategories as $cat) {
            if (strcasecmp($cat['name'], $trimmed) === 0 || stripos($cat['name'], $trimmed) !== false || stripos($trimmed, $cat['name']) !== false) {
                return (int)$cat['id'];
            }
        }

        return $defaultId ?: (int)($allCategories[0]['id'] ?? 1);
    }

    /**
     * Helper membuat XML Sheet OpenXML
     */
    private static function buildSheetXml(array $headers, array $rows): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";
        $xml .= '  <sheetData>' . "\n";

        // Row 1: Headers
        $xml .= '    <row r="1">' . "\n";
        foreach ($headers as $colIdx => $hText) {
            $colLetter = self::indexToColLetter($colIdx);
            $safeText = htmlspecialchars((string)$hText, ENT_XML1, 'UTF-8');
            $xml .= '      <c r="' . $colLetter . '1" t="inlineStr"><is><t>' . $safeText . '</t></is></c>' . "\n";
        }
        $xml .= '    </row>' . "\n";

        // Data Rows
        $rowNum = 2;
        foreach ($rows as $row) {
            $xml .= '    <row r="' . $rowNum . '">' . "\n";
            foreach ($row as $colIdx => $val) {
                $colLetter = self::indexToColLetter($colIdx);
                $ref = $colLetter . $rowNum;

                if (is_int($val) || is_float($val)) {
                    $xml .= '      <c r="' . $ref . '"><v>' . $val . '</v></c>' . "\n";
                } elseif (is_numeric($val) && !str_starts_with((string)$val, '0')) {
                    $xml .= '      <c r="' . $ref . '"><v>' . $val . '</v></c>' . "\n";
                } else {
                    $safeText = htmlspecialchars((string)$val, ENT_XML1, 'UTF-8');
                    $xml .= '      <c r="' . $ref . '" t="inlineStr"><is><t>' . $safeText . '</t></is></c>' . "\n";
                }
            }
            $xml .= '    </row>' . "\n";
            $rowNum++;
        }

        $xml .= '  </sheetData>' . "\n";
        $xml .= '</worksheet>';

        return $xml;
    }

    private static function indexToColLetter(int $index): string
    {
        $letter = '';
        $index += 1;
        while ($index > 0) {
            $modulo = ($index - 1) % 26;
            $letter = chr(65 + $modulo) . $letter;
            $index = (int)(($index - $modulo) / 26);
        }
        return $letter;
    }

    private static function colLetterToIndex(string $ref): int
    {
        $col = preg_replace('/[0-9]/', '', $ref);
        $col = strtoupper($col);
        $idx = 0;
        for ($i = 0; $i < strlen($col); $i++) {
            $idx = $idx * 26 + (ord($col[$i]) - 64);
        }
        return $idx - 1;
    }
}
