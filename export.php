<?php
// export.php — Export Data Tenaga Kerja ke format Excel (.xls) / CSV kompatibel PHP 5.6 - 8.x
require_once __DIR__ . '/db.php';

auth_require();

// Filter opsional mengikuti filter halaman tenaga-kerja.php
$search       = get_param('q');
$unitFilter   = get_param('unit');
$statusFilter = get_param('status');
$format       = strtolower(get_param('format', 'xls')); // default .xls

$where = array('1=1');
$params = array();
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

$headers = array(
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
    'JUMLAH SERTIFIKASI',
);

$dateStamp = date('Ymd_His');

// ============================================================
// Format 1: CSV (Jika diminta ?format=csv)
// ============================================================
if ($format === 'csv') {
    $fileName = 'DATA_TENAGA_KERJA_SIMANTAP_' . $dateStamp . '.csv';
    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Expires: 0");

    $file = fopen('php://output', 'w');
    // UTF-8 BOM untuk kompatibilitas Excel
    fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($file, $headers);

    $i = 1;
    foreach ($workers as $row) {
        $unitVal = $row['unit_nama'] ? $row['unit_nama'] : ($row['unit'] ? $row['unit'] : '-');
        fputcsv($file, array(
            $i,
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
            $unitVal,
            $row['nomor_bpjs_kesehatan'],
            $row['nomor_bpjs_ketenagakerjaan'],
            $row['nomor_dplk'],
            $row['bank_dplk'],
            $row['no_perjanjian_kerja'],
            format_tanggal($row['tanggal_masuk_kerja']),
            $row['status_tenaga_kerja'],
            $row['skema_tenaga_kerja'],
            (int)$row['sertifikasi_count'],
        ));
        $i++;
    }
    fclose($file);
    exit;
}

// ============================================================
// Format 2: Excel (.xls HTML Spreadsheet yang rapi)
// ============================================================
$fileName = 'DATA_TENAGA_KERJA_SIMANTAP_' . $dateStamp . '.xls';

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$fileName\"");
header("Cache-Control: max-age=0");
header("Pragma: public");

echo "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\" xmlns:x=\"urn:schemas-microsoft-com:office:excel\" xmlns=\"http://www.w3.org/TR/REC-html40\">";
echo "<head><meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">";
echo "<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Data Tenaga Kerja</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
echo "<style>
    th { background-color: #1E8C86; color: #FFFFFF; font-weight: bold; border: 1px solid #146864; padding: 8px 12px; font-family: Calibri, sans-serif; font-size: 11pt; text-align: center; }
    td { font-family: Calibri, sans-serif; font-size: 10pt; border: 1px solid #CBD5E1; padding: 6px 10px; }
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .bold { font-weight: bold; }
    .mso-text { mso-number-format:'\@'; }
    .bg-zebra { background-color: #F8FAFC; }
</style></head><body>";
echo "<table border=\"1\"><thead><tr>";
foreach ($headers as $h) {
    echo "<th>" . htmlspecialchars($h, ENT_QUOTES, 'UTF-8') . "</th>";
}
echo "</tr></thead><tbody>";

$i = 1;
foreach ($workers as $row) {
    $bgClass = ($i % 2 === 0) ? ' class="bg-zebra"' : '';
    echo "<tr$bgClass>";
    echo "<td class=\"text-center\">" . $i . "</td>";
    echo "<td class=\"text-left mso-text\">" . htmlspecialchars($row['nomor_perjanjian'] ? $row['nomor_perjanjian'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-left\">" . htmlspecialchars($row['nama_perusahaan'] ? $row['nama_perusahaan'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-left bold\">" . htmlspecialchars($row['nama'] ? $row['nama'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center mso-text\">" . htmlspecialchars($row['nik'] ? $row['nik'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-left\">" . htmlspecialchars($row['tempat_lahir'] ? $row['tempat_lahir'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center\">" . htmlspecialchars(format_tanggal($row['tanggal_lahir']), ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center\">" . htmlspecialchars(hitung_usia($row['tanggal_lahir']), ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center mso-text\">" . htmlspecialchars($row['no_telepon'] ? $row['no_telepon'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-left\">" . htmlspecialchars($row['email'] ? $row['email'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center\">" . htmlspecialchars($row['jenis_kelamin'] ? $row['jenis_kelamin'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-left\">" . htmlspecialchars($row['alamat_domisili'] ? $row['alamat_domisili'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-left\">" . htmlspecialchars($row['kota_kabupaten'] ? $row['kota_kabupaten'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-left\">" . htmlspecialchars($row['provinsi'] ? $row['provinsi'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-left\">" . htmlspecialchars($row['jabatan_terakhir'] ? $row['jabatan_terakhir'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-left\">" . htmlspecialchars($row['fungsi_pekerjaan'] ? $row['fungsi_pekerjaan'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-left\">" . htmlspecialchars($row['unit'] ? $row['unit'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    $unitNama = $row['unit_nama'] ? $row['unit_nama'] : ($row['unit'] ? $row['unit'] : '-');
    echo "<td class=\"text-left\">" . htmlspecialchars($unitNama, ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center mso-text\">" . htmlspecialchars($row['nomor_bpjs_kesehatan'] ? $row['nomor_bpjs_kesehatan'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center mso-text\">" . htmlspecialchars($row['nomor_bpjs_ketenagakerjaan'] ? $row['nomor_bpjs_ketenagakerjaan'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center mso-text\">" . htmlspecialchars($row['nomor_dplk'] ? $row['nomor_dplk'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center\">" . htmlspecialchars($row['bank_dplk'] ? $row['bank_dplk'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-left mso-text\">" . htmlspecialchars($row['no_perjanjian_kerja'] ? $row['no_perjanjian_kerja'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center\">" . htmlspecialchars(format_tanggal($row['tanggal_masuk_kerja']), ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center\">" . htmlspecialchars($row['status_tenaga_kerja'] ? $row['status_tenaga_kerja'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center\">" . htmlspecialchars($row['skema_tenaga_kerja'] ? $row['skema_tenaga_kerja'] : '-', ENT_QUOTES, 'UTF-8') . "</td>";
    echo "<td class=\"text-center\">" . (int)$row['sertifikasi_count'] . "</td>";
    echo "</tr>";
    $i++;
}

echo "</tbody></table></body></html>";
exit;
