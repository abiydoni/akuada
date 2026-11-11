<?php

namespace App\Libraries;

use App\Models\CategoryModel;
use App\Models\CompanyModel;

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
     * Sheet 1: Data Barang (kolom terpisah rapi, contoh input angka)
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
                    '',  // Kosongkan agar otomatis
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

        // Shared strings manager
        $sharedStrings = [];
        $stringMap = [];
        $getStringIndex = function(string $str) use (&$sharedStrings, &$stringMap): int {
            if (isset($stringMap[$str])) {
                return $stringMap[$str];
            }
            $idx = count($sharedStrings);
            $sharedStrings[] = $str;
            $stringMap[$str] = $idx;
            return $idx;
        };

        // Build Sheet 1 (Data Barang)
        $sheet1Xml = self::buildSheetXmlData($headers, $sampleRows, $getStringIndex);

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

        $sheet2Xml = self::buildSheetXmlData($guideHeaders, $guideRows, $getStringIndex);

        // Build SharedStrings XML
        $uniqueCount = count($sharedStrings);
        $sharedStringsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $sharedStringsXml .= '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" count="' . $uniqueCount . '" uniqueCount="' . $uniqueCount . '">' . "\n";
        foreach ($sharedStrings as $str) {
            $safe = htmlspecialchars($str, ENT_XML1, 'UTF-8');
            $sharedStringsXml .= '  <si><t>' . $safe . '</t></si>' . "\n";
        }
        $sharedStringsXml .= '</sst>';

        // Build Workbook XML
        $wbXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $wbXml .= '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">' . "\n";
        $wbXml .= '  <sheets>' . "\n";
        $wbXml .= '    <sheet name="Data Barang" sheetId="1" r:id="rId1"/>' . "\n";
        $wbXml .= '    <sheet name="Panduan &amp; Kode Angka" sheetId="2" r:id="rId2"/>' . "\n";
        $wbXml .= '  </sheets>' . "\n";
        $wbXml .= '</workbook>';

        // Package into ZIP as XLSX (auto-detect ZipArchive or zero-dependency pure PHP fallback)
        $scaffoldDir = __DIR__ . '/templates/xlsx_scaffold';

        $zipFiles = [
            '[Content_Types].xml'        => self::getFileOrFallback($scaffoldDir . '/[Content_Types].xml', self::defaultContentTypesXml()),
            '_rels/.rels'                => self::getFileOrFallback($scaffoldDir . '/_rels/.rels', self::defaultRootRelsXml()),
            'docProps/app.xml'           => self::getFileOrFallback($scaffoldDir . '/docProps/app.xml', self::defaultAppXml()),
            'docProps/core.xml'          => self::getFileOrFallback($scaffoldDir . '/docProps/core.xml', self::defaultCoreXml()),
            'xl/_rels/workbook.xml.rels' => self::getFileOrFallback($scaffoldDir . '/xl/_rels/workbook.xml.rels', self::defaultWorkbookRelsXml()),
            'xl/theme/theme1.xml'        => self::getFileOrFallback($scaffoldDir . '/xl/theme/theme1.xml', self::defaultTheme1Xml()),
            'xl/styles.xml'              => self::getFileOrFallback($scaffoldDir . '/xl/styles.xml', self::defaultStylesXml()),
            'xl/workbook.xml'            => $wbXml,
            'xl/worksheets/sheet1.xml'   => $sheet1Xml,
            'xl/worksheets/sheet2.xml'   => $sheet2Xml,
            'xl/sharedStrings.xml'       => $sharedStringsXml,
        ];

        return self::packageZip($zipFiles);
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
     * Parse file format Excel .xlsx menggunakan ZipArchive atau Pure PHP Zip Reader & SimpleXML murni
     */
    private static function parseXlsxFile(string $filePath): array
    {
        $files = self::extractZip($filePath);
        if (empty($files)) {
            return [];
        }

        // Shared strings jika ada
        $sharedStrings = [];
        $sharedStringsXml = $files['xl/sharedStrings.xml'] ?? null;
        if ($sharedStringsXml) {
            $xml = @simplexml_load_string($sharedStringsXml);
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
        $sheetXmlStr = $files['xl/worksheets/sheet1.xml'] ?? null;
        if (!$sheetXmlStr) {
            foreach ($files as $fname => $fcontent) {
                if (str_starts_with($fname, 'xl/worksheets/sheet') && str_ends_with($fname, '.xml')) {
                    $sheetXmlStr = $fcontent;
                    break;
                }
            }
        }

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
     * Helper membuat XML Worksheet dengan shared strings & skipping cell kosong
     */
    private static function buildSheetXmlData(array $headers, array $rows, callable $getStringIndex): string
    {
        $maxCol = count($headers);
        $totalRows = count($rows) + 1;
        $lastColLetter = self::indexToColLetter($maxCol - 1);
        $dimRef = "A1:{$lastColLetter}{$totalRows}";

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
        $xml .= '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . "\n";
        $xml .= '  <dimension ref="' . $dimRef . '"/>' . "\n";
        $xml .= '  <sheetViews><sheetView tabSelected="1" workbookViewId="0"/></sheetViews>' . "\n";
        $xml .= '  <sheetFormatPr defaultRowHeight="15"/>' . "\n";
        $xml .= '  <sheetData>' . "\n";

        // Row 1: Headers
        $xml .= '    <row r="1" spans="1:' . $maxCol . '">' . "\n";
        foreach ($headers as $cIdx => $h) {
            $colLetter = self::indexToColLetter($cIdx);
            $sIdx = $getStringIndex((string)$h);
            $xml .= '      <c r="' . $colLetter . '1" t="s"><v>' . $sIdx . '</v></c>' . "\n";
        }
        $xml .= '    </row>' . "\n";

        // Rows 2..N
        $rNum = 2;
        foreach ($rows as $row) {
            $xml .= '    <row r="' . $rNum . '" spans="1:' . $maxCol . '">' . "\n";
            foreach ($row as $cIdx => $val) {
                // Sangat penting: Lewati cell kosong agar tidak menimbulkan string property corrupt di Excel!
                if ($val === '' || $val === null) {
                    continue;
                }
                $colLetter = self::indexToColLetter($cIdx);
                $ref = $colLetter . $rNum;

                if (is_int($val) || is_float($val)) {
                    $xml .= '      <c r="' . $ref . '"><v>' . $val . '</v></c>' . "\n";
                } elseif (is_numeric($val) && !str_starts_with((string)$val, '0')) {
                    $xml .= '      <c r="' . $ref . '"><v>' . $val . '</v></c>' . "\n";
                } else {
                    $sIdx = $getStringIndex((string)$val);
                    $xml .= '      <c r="' . $ref . '" t="s"><v>' . $sIdx . '</v></c>' . "\n";
                }
            }
            $xml .= '    </row>' . "\n";
            $rNum++;
        }

        $xml .= '  </sheetData>' . "\n";
        $xml .= '  <pageMargins left="0.7" right="0.7" top="0.75" bottom="0.75" header="0.3" footer="0.3"/>' . "\n";
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

    private static function getFileOrFallback(string $path, string $fallback): string
    {
        if (file_exists($path)) {
            $c = @file_get_contents($path);
            if ($c !== false && strlen($c) > 0) {
                return $c;
            }
        }
        return $fallback;
    }

    private static function defaultContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/theme/theme1.xml" ContentType="application/vnd.openxmlformats-officedocument.theme+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/xl/sharedStrings.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>';
    }

    private static function defaultRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>';
    }

    private static function defaultWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme" Target="theme/theme1.xml"/>
  <Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId5" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings" Target="sharedStrings.xml"/>
</Relationships>';
    }

    private static function defaultAppXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
  <Application>Microsoft Excel</Application>
  <DocSecurity>0</DocSecurity>
  <ScaleCrop>false</ScaleCrop>
  <HeadingPairs>
    <vt:vector size="2" baseType="variant">
      <vt:variant><vt:lpstr>Worksheets</vt:lpstr></vt:variant>
      <vt:variant><vt:i4>2</vt:i4></vt:variant>
    </vt:vector>
  </HeadingPairs>
  <TitlesOfParts>
    <vt:vector size="2" baseType="lpstr">
      <vt:lpstr>Data Barang</vt:lpstr>
      <vt:lpstr>Panduan &amp; Kode Angka</vt:lpstr>
    </vt:vector>
  </TitlesOfParts>
  <Company>AkuAda B2B</Company>
  <LinksUpToDate>false</LinksUpToDate>
  <SharedDoc>false</SharedDoc>
  <HyperlinksChanged>false</HyperlinksChanged>
  <AppVersion>16.0300</AppVersion>
</Properties>';
    }

    private static function defaultCoreXml(): string
    {
        $now = gmdate('Y-m-d\TH:i:s\Z');
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:creator>AkuAda B2B</dc:creator>
  <cp:lastModifiedBy>AkuAda B2B</cp:lastModifiedBy>
  <dcterms:created xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:created>
  <dcterms:modified xsi:type="dcterms:W3CDTF">' . $now . '</dcterms:modified>
</cp:coreProperties>';
    }

    private static function defaultStylesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="1">
    <font><sz val="11"/><color theme="1"/><name val="Calibri"/><family val="2"/><scheme val="minor"/></font>
  </fonts>
  <fills count="2">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
  </fills>
  <borders count="1">
    <border><left/><right/><top/><bottom/><diagonal/></border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>
  </cellXfs>
  <cellStyles count="1">
    <cellStyle name="Normal" xfId="0" builtinId="0"/>
  </cellStyles>
</styleSheet>';
    }

    private static function defaultTheme1Xml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<a:theme xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" name="Office Theme">
  <a:themeElements>
    <a:clrScheme name="Office">
      <a:dk1><a:sysClr val="windowText" lastClr="000000"/></a:dk1>
      <a:lt1><a:sysClr val="window" lastClr="FFFFFF"/></a:lt1>
      <a:dk2><a:srgbClr val="44546A"/></a:dk2>
      <a:lt2><a:srgbClr val="E7E6E6"/></a:lt2>
      <a:accent1><a:srgbClr val="4472C4"/></a:accent1>
      <a:accent2><a:srgbClr val="ED7D31"/></a:accent2>
      <a:accent3><a:srgbClr val="A5A5A5"/></a:accent3>
      <a:accent4><a:srgbClr val="FFC000"/></a:accent4>
      <a:accent5><a:srgbClr val="5B9BD5"/></a:accent5>
      <a:accent6><a:srgbClr val="70AD47"/></a:accent6>
      <a:hlink><a:srgbClr val="0563C1"/></a:hlink>
      <a:folHlink><a:srgbClr val="954F72"/></a:folHlink>
    </a:clrScheme>
    <a:fontScheme name="Office">
      <a:majorFont><a:latin typeface="Calibri Light"/></a:majorFont>
      <a:minorFont><a:latin typeface="Calibri"/></a:minorFont>
    </a:fontScheme>
    <a:fmtScheme name="Office">
      <a:fillStyleLst><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:fillStyleLst>
      <a:lnStyleLst><a:ln w="9525"><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:ln></a:lnStyleLst>
      <a:effectStyleLst><a:effectStyle><a:effectLst/></a:effectStyle></a:effectStyleLst>
      <a:bgFillStyleLst><a:solidFill><a:schemeClr val="phClr"/></a:solidFill></a:bgFillStyleLst>
    </a:fmtScheme>
  </a:themeElements>
</a:theme>';
    }

    /**
     * Kompres kumpulan file XML/scaffold menjadi arsip XLSX
     * Prioritas: Gunakan ZipArchive jika ada, jika tidak otomatis gunakan Pure PHP ZIP Engine (zero-dependency).
     */
    public static function packageZip(array $files): string
    {
        if (class_exists(\ZipArchive::class)) {
            try {
                $tempFile = tempnam(sys_get_temp_dir(), 'akuada_tpl_');
                $zip = new \ZipArchive();
                if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                    foreach ($files as $name => $data) {
                        $zip->addFromString($name, $data);
                    }
                    $zip->close();
                    $content = @file_get_contents($tempFile);
                    @unlink($tempFile);
                    if ($content !== false && strlen($content) > 0) {
                        return $content;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback to pure PHP zip generator
            }
        }

        return self::buildPurePhpZip($files);
    }

    /**
     * Engine pembuat ZIP murni PHP (standar RFC 1951 / PKZIP)
     * Tidak membutuhkan ekstensi eksternal, kompatibel penuh dengan Excel / Google Sheets
     */
    public static function buildPurePhpZip(array $files): string
    {
        $time = time();
        $d = getdate($time);
        $dosTime = (($d['hours'] << 11) | ($d['minutes'] << 5) | ($d['seconds'] >> 1)) & 0xFFFF;
        $dosDate = ((($d['year'] - 1980) << 9) | ($d['mon'] << 5) | $d['mday']) & 0xFFFF;

        $ctrlDir = '';
        $dataBlocks = '';
        $offset = 0;
        $entriesCount = 0;

        foreach ($files as $name => $content) {
            $name = str_replace('\\', '/', $name);
            $uncompressedSize = strlen($content);
            $crc = crc32($content);

            if (function_exists('gzdeflate')) {
                $compressed = (string)@gzdeflate($content);
                $compressedSize = strlen($compressed);
                $method = 8;
            } else {
                $compressed = $content;
                $compressedSize = $uncompressedSize;
                $method = 0;
            }

            $localHeader = pack(
                'VvvvvvVVVvv',
                0x04034b50,
                20,
                0x0800,
                $method,
                $dosTime,
                $dosDate,
                $crc,
                $compressedSize,
                $uncompressedSize,
                strlen($name),
                0
            ) . $name;

            $dataBlocks .= $localHeader . $compressed;

            $ctrlDir .= pack(
                'VvvvvvvVVVvvvvvVV',
                0x02014b50,
                20,
                20,
                0x0800,
                $method,
                $dosTime,
                $dosDate,
                $crc,
                $compressedSize,
                $uncompressedSize,
                strlen($name),
                0,
                0,
                0,
                0,
                0x00000020,
                $offset
            ) . $name;

            $offset = strlen($dataBlocks);
            $entriesCount++;
        }

        $eocd = pack(
            'VvvvvVVv',
            0x06054b50,
            0,
            0,
            $entriesCount,
            $entriesCount,
            strlen($ctrlDir),
            $offset,
            0
        );

        return $dataBlocks . $ctrlDir . $eocd;
    }

    /**
     * Ekstraksi arsip ZIP (XLSX) menjadi daftar file [nama => isi]
     */
    public static function extractZip(string $filePath): array
    {
        if (class_exists(\ZipArchive::class)) {
            try {
                $zip = new \ZipArchive();
                if ($zip->open($filePath) === true) {
                    $files = [];
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $name = $zip->getNameIndex($i);
                        $files[$name] = $zip->getFromIndex($i);
                    }
                    $zip->close();
                    if (!empty($files)) {
                        return $files;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback to pure PHP extractor
            }
        }

        return self::extractPurePhpZip($filePath);
    }

    /**
     * Engine ekstraksi ZIP murni PHP membaca Central Directory
     */
    public static function extractPurePhpZip(string $filePath): array
    {
        $data = @file_get_contents($filePath);
        if ($data === false || strlen($data) < 22) {
            return [];
        }

        $eocdPos = -1;
        $maxScan = min(strlen($data), 65557);
        $scanStart = strlen($data) - 22;
        for ($i = $scanStart; $i >= strlen($data) - $maxScan; $i--) {
            if (substr($data, $i, 4) === "\x50\x4b\x05\x06") {
                $eocdPos = $i;
                break;
            }
        }

        if ($eocdPos === -1) {
            return [];
        }

        $eocd = unpack('vdisk/vcdDisk/vdiskEntries/vtotalEntries/VcdSize/VcdOffset/vcommentLen', substr($data, $eocdPos + 4, 18));
        $totalEntries = $eocd['totalEntries'] ?? 0;
        $cdOffset = $eocd['cdOffset'] ?? 0;

        $files = [];
        $curCd = $cdOffset;

        for ($n = 0; $n < $totalEntries; $n++) {
            if ($curCd + 46 > strlen($data) || substr($data, $curCd, 4) !== "\x50\x4b\x01\x02") {
                break;
            }

            $cd = unpack(
                'vverMade/vverNeeded/vflag/vmethod/vtime/vdate/Vcrc/VcompSize/VuncompSize/vnameLen/vextraLen/vcommentLen/vdisk/vintAttr/VextAttr/VlocalOffset',
                substr($data, $curCd + 4, 42)
            );

            $curCd += 46;
            $name = substr($data, $curCd, $cd['nameLen']);
            $curCd += $cd['nameLen'] + $cd['extraLen'] + $cd['commentLen'];

            $localOffset = $cd['localOffset'];
            if ($localOffset + 30 > strlen($data) || substr($data, $localOffset, 4) !== "\x50\x4b\x03\x04") {
                continue;
            }

            $local = unpack('vver/vflag/vmethod/vtime/vdate/Vcrc/VcompSize/VuncompSize/vnameLen/vextraLen', substr($data, $localOffset + 4, 26));
            $dataOffset = $localOffset + 30 + $local['nameLen'] + $local['extraLen'];

            $compressedData = substr($data, $dataOffset, $cd['compSize']);
            $uncompressed = '';

            if ($cd['method'] === 8) {
                $uncompressed = @gzinflate($compressedData);
                if ($uncompressed === false) {
                    $uncompressed = @gzuncompress($compressedData);
                }
            } elseif ($cd['method'] === 0) {
                $uncompressed = $compressedData;
            }

            $files[$name] = (string)$uncompressed;
        }

        return $files;
    }

    /**
     * Generate template berformat CSV murni ber-BOM UTF-8 (agar langsung terbuka rapi berkolom di Excel)
     */
    public static function generateCsvTemplate(bool $isAdmin = false, ?int $vendorCompanyId = null): string
    {
        $companyModel = new CompanyModel();
        $sampleVendors = $isAdmin ? $companyModel->where('type', 'vendor')->findAll(3) : [];
        $v1 = !empty($sampleVendors[0]['company_name']) ? $sampleVendors[0]['company_name'] : 'PT Mitra Pemasok Mandiri';
        $v2 = !empty($sampleVendors[1]['company_name']) ? $sampleVendors[1]['company_name'] : 'CV Berkah Teknik Sentosa';
        $v3 = !empty($sampleVendors[2]['company_name']) ? $sampleVendors[2]['company_name'] : $v1;

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
                    'Paket mouse keyboard nirkabel 2.4GHz USB nano receiver tahan tumpahan air.',
                    'Konektivitas: Nirkabel 2.4GHz; Jangkauan: 10 meter; Baterai: 2x AAA (Keyboard), 1x AA (Mouse); Garansi: 1 Tahun',
                    'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=600&q=80'
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
                    '3',
                    'Helm Proyek Keselamatan Safety Helmet V-Gard Fastrac',
                    '',
                    '',
                    'MSA',
                    '1', // Pcs
                    78000,
                    200,
                    5,
                    450,
                    1,
                    65.0,
                    1,
                    1,
                    'Helm keselamatan kerja standar SNI dan ANSI dengan suspensi putar Fastrac untuk perlindungan industri.',
                    'Material: Polietilen (HDPE); Standar: SNI ISO 3873 & ANSI/ISEA Z89.1; Suspensi: 4 titik Fastrac putar; Warna: Putih',
                    'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&w=600&q=80'
                ]
            ];
        }

        $f = fopen('php://temp', 'r+');
        // UTF-8 BOM agar Microsoft Excel langsung membaca sebagai UTF-8
        fputs($f, "\xEF\xBB\xBF");
        fputcsv($f, $headers);
        foreach ($sampleRows as $row) {
            fputcsv($f, $row);
        }
        rewind($f);
        $csv = stream_get_contents($f);
        fclose($f);

        return $csv;
    }
}
