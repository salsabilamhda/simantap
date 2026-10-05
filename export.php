<?php
// export.php — Export Data Tenaga Kerja ke format Excel (.xlsx) / CSV yang rapi dan profesional
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

auth_require();

// Filter opsional mengikuti filter halaman tenaga-kerja.php
$search       = get_param('q');
$unitFilter   = get_param('unit');
$statusFilter = get_param('status');
$format       = strtolower(get_param('format', 'xlsx')); // default .xlsx

$where = ['1=1'];
$params = [];
if ($search !== '') {
    $where[] = "(tk.nama LIKE ? OR tk.nik LIKE ? OR tk.jabatan_terakhir LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
if ($unitFilter !== '' && $unitFilter !== 'ALL') {
    $where[] = "tk.unit_layanan_id = ?";
    $params[] = $unitFilter;
}
if ($statusFilter !== '' && $statusFilter !== 'ALL') {
    $where[] = "tk.status_tenaga_kerja = ?";
    $params[] = $statusFilter;
}
$whereStr = implode(' AND ', $where);

$workers = db_query("
    SELECT tk.*, u.nama AS unit_nama,
        (SELECT COUNT(*) FROM sertifikasis s WHERE s.tenaga_kerja_id = tk.id) AS sertifikasi_count
    FROM tenaga_kerjas tk
    LEFT JOIN unit_layanans u ON u.id = tk.unit_layanan_id
    WHERE $whereStr
    ORDER BY tk.id ASC
", $params);

$headers = [
    'NO',
    'NOMOR PERJANJIAN',
    'NAMA PERUSAHAAN',
    'NAMA',
    'NIK',
    'TEMPAT LAHIR',
    'TANGGAL LAHIR',
    'USIA',
    'NO TELEPON (WA)',
    'EMAIL',
    'JENIS KELAMIN',
    'ALAMAT DOMISILI',
    'KOTA / KABUPATEN',
    'PROVINSI',
    'JABATAN TERAKHIR',
    'FUNGSI PEKERJAAN',
    'UNIT',
    'UNIT LAYANAN',
    'NOMOR BPJS KESEHATAN',
    'NOMOR BPJS KETENAGAKERJAAN',
    'NOMOR DPLK',
    'BANK DPLK',
    'NO PERJANJIAN KERJA (SPK)',
    'TANGGAL MASUK KERJA',
    'STATUS TENAGA KERJA',
    'SKEMA TENAGA KERJA',
    'JUMLAH SERTIFIKASI'
];

$dateStamp = date('Ymd_His');

// ============================================================
// Format 1: EXCEL (.XLSX) — Rapi, Berwarna, Auto-Width, Text Preserved
// ============================================================
if ($format !== 'csv') {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Data Tenaga Kerja');

    // 1. Tulis Header Kolom
    $colIdx = 1;
    foreach ($headers as $h) {
        $colLetter = Coordinate::stringFromColumnIndex($colIdx);
        $sheet->setCellValueExplicit($colLetter . '1', $h, DataType::TYPE_STRING);
        $colIdx++;
    }
    $lastColLetter = Coordinate::stringFromColumnIndex(count($headers));

    // 2. Styling Header: Background Teal Korporat, Font Putih Bold, Center, Row Height
    $headerStyle = [
        'font' => [
            'name'  => 'Calibri',
            'bold'  => true,
            'color' => ['rgb' => 'FFFFFF'],
            'size'  => 11,
        ],
        'fill' => [
            'fillType'   => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '0F766E'], // Teal SIMANTAP
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
            'wrapText'   => false,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color'       => ['rgb' => '0D5E58'],
            ],
        ],
    ];
    $sheet->getStyle("A1:{$lastColLetter}1")->applyFromArray($headerStyle);
    $sheet->getRowDimension(1)->setRowHeight(30);

    // 3. Tulis Baris Data
    $rowNum = 2;
    foreach ($workers as $idx => $row) {
        $isEven = ($rowNum % 2 === 0);
        $bgColor = $isEven ? 'FFFFFF' : 'F8FAFC'; // Zebra striping lembut

        $cells = [
            [$row['no_urut'] ?: ($idx + 1), DataType::TYPE_NUMERIC, Alignment::HORIZONTAL_CENTER],
            [$row['nomor_perjanjian'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [$row['nama_perusahaan'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [$row['nama'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [$row['nik'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_CENTER], // Text string murni (tanpa kutip, tanpa notasi ilmiah)
            [$row['tempat_lahir'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [format_tanggal($row['tanggal_lahir']), DataType::TYPE_STRING, Alignment::HORIZONTAL_CENTER], // DD/MM/YYYY (tidak akan jadi ########)
            [hitung_usia($row['tanggal_lahir']), DataType::TYPE_STRING, Alignment::HORIZONTAL_CENTER],
            [$row['no_telepon'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_CENTER], // Text string murni (angka 0 di awal aman, tidak jadi 8.58E+10)
            [$row['email'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [$row['jenis_kelamin'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_CENTER],
            [$row['alamat_domisili'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [$row['kota_kabupaten'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [$row['provinsi'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [$row['jabatan_terakhir'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [$row['fungsi_pekerjaan'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [$row['unit'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [$row['unit_nama'] ?: $row['unit'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [$row['nomor_bpjs_kesehatan'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_CENTER],
            [$row['nomor_bpjs_ketenagakerjaan'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_CENTER],
            [$row['nomor_dplk'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_CENTER],
            [$row['bank_dplk'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_CENTER],
            [$row['no_perjanjian_kerja'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_LEFT],
            [format_tanggal($row['tanggal_masuk_kerja']), DataType::TYPE_STRING, Alignment::HORIZONTAL_CENTER],
            [$row['status_tenaga_kerja'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_CENTER],
            [$row['skema_tenaga_kerja'] ?: '-', DataType::TYPE_STRING, Alignment::HORIZONTAL_CENTER],
            [(int)$row['sertifikasi_count'], DataType::TYPE_NUMERIC, Alignment::HORIZONTAL_CENTER],
        ];

        $colIdx = 1;
        foreach ($cells as $cell) {
            $colLetter = Coordinate::stringFromColumnIndex($colIdx);
            $cellCoord = $colLetter . $rowNum;
            $val = (string)$cell[0];
            $type = $cell[1];
            $align = $cell[2];

            $sheet->setCellValueExplicit($cellCoord, $val, $type);
            
            $cellStyle = $sheet->getStyle($cellCoord);
            $cellStyle->getAlignment()->setHorizontal($align)->setVertical(Alignment::VERTICAL_CENTER);
            $cellStyle->getFont()->setName('Calibri')->setSize(10);
            if ($colIdx === 4) { // Nama tenaga kerja dibuat tebal
                $cellStyle->getFont()->setBold(true);
            }
            $cellStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bgColor);
            $cellStyle->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E2E8F0');

            $colIdx++;
        }

        $sheet->getRowDimension($rowNum)->setRowHeight(22);
        $rowNum++;
    }

    // 4. Set Lebar Kolom yang lapang & proporsional agar tidak ada teks terpotong / ########
    $columnWidths = [
        'A' => 6,   // NO
        'B' => 22,  // NOMOR PERJANJIAN
        'C' => 26,  // NAMA PERUSAHAAN
        'D' => 32,  // NAMA
        'E' => 22,  // NIK
        'F' => 18,  // TEMPAT LAHIR
        'G' => 16,  // TANGGAL LAHIR
        'H' => 12,  // USIA
        'I' => 18,  // NO TELEPON (WA)
        'J' => 28,  // EMAIL
        'K' => 16,  // JENIS KELAMIN
        'L' => 38,  // ALAMAT DOMISILI
        'M' => 20,  // KOTA / KABUPATEN
        'N' => 18,  // PROVINSI
        'O' => 26,  // JABATAN TERAKHIR
        'P' => 26,  // FUNGSI PEKERJAAN
        'Q' => 20,  // UNIT
        'R' => 24,  // UNIT LAYANAN
        'S' => 24,  // NOMOR BPJS KESEHATAN
        'T' => 26,  // NOMOR BPJS KETENAGAKERJAAN
        'U' => 20,  // NOMOR DPLK
        'V' => 15,  // BANK DPLK
        'W' => 26,  // NO PERJANJIAN KERJA (SPK)
        'X' => 20,  // TANGGAL MASUK KERJA
        'Y' => 18,  // STATUS TENAGA KERJA
        'Z' => 25,  // SKEMA TENAGA KERJA
        'AA'=> 18,  // JUMLAH SERTIFIKASI
    ];
    foreach ($columnWidths as $col => $w) {
        $sheet->getColumnDimension($col)->setWidth($w);
    }

    // 5. Freeze Panes: Baris header tetap terlihat saat scroll ke bawah
    $sheet->freezePane('A2');

    // 6. Pasang Filter Otomatis (AutoFilter) pada header
    $lastDataRow = max(2, $rowNum - 1);
    $sheet->setAutoFilter("A1:{$lastColLetter}{$lastDataRow}");

    // 7. Output ke Browser
    $fileName = 'DATA_TENAGA_KERJA_SIMANTAP_' . $dateStamp . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $fileName . '"');
    header('Cache-Control: max-age=0');
    header('Pragma: public');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// ============================================================
// Format 2: CSV (Opsional jika pengguna request ?format=csv)
// ============================================================
$fileName = 'DATA_TENAGA_KERJA_SIMANTAP_' . $dateStamp . '.csv';

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$fileName\"");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Expires: 0");

$file = fopen('php://output', 'w');
// UTF-8 BOM untuk kompatibilitas Excel
fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($file, $headers);

$i = 1;
foreach ($workers as $row) {
    fputcsv($file, [
        $row['no_urut'] ?: $i,
        $row['nomor_perjanjian'],
        $row['nama_perusahaan'],
        $row['nama'],
        $row['nik'],
        $row['tempat_lahir'],
        format_tanggal($row['tanggal_lahir']),
        hitung_usia($row['tanggal_lahir']),
        $row['no_telepon'],
        $row['email'],
        $row['jenis_kelamin'],
        $row['alamat_domisili'],
        $row['kota_kabupaten'],
        $row['provinsi'],
        $row['jabatan_terakhir'],
        $row['fungsi_pekerjaan'],
        $row['unit'],
        $row['unit_nama'] ?: $row['unit'],
        $row['nomor_bpjs_kesehatan'],
        $row['nomor_bpjs_ketenagakerjaan'],
        $row['nomor_dplk'],
        $row['bank_dplk'],
        $row['no_perjanjian_kerja'],
        format_tanggal($row['tanggal_masuk_kerja']),
        $row['status_tenaga_kerja'],
        $row['skema_tenaga_kerja'],
        (int)$row['sertifikasi_count'],
    ]);
    $i++;
}

fclose($file);
exit;
