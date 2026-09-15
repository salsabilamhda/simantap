<?php
require_once __DIR__ . '/db.php';

$fileName = 'DATA_TENAGA_KERJA_SIMANTAP_' . date('Ymd_His') . '.csv';
$workers  = db_query("
    SELECT tk.*, u.nama AS unit_nama,
        (SELECT COUNT(*) FROM sertifikasis s WHERE s.tenaga_kerja_id = tk.id) AS sertifikasi_count
    FROM tenaga_kerjas tk
    LEFT JOIN unit_layanans u ON u.id = tk.unit_layanan_id
    ORDER BY tk.id ASC
");

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$fileName\"");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Expires: 0");

$file = fopen('php://output', 'w');

// UTF-8 BOM for Excel compatibility
fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Header columns
fputcsv($file, [
    'NO', 'NOMOR PERJANJIAN', 'NAMA PERUSAHAAN', 'NAMA', 'NIK',
    'TEMPAT LAHIR', 'TANGGAL LAHIR', 'USIA', 'PENDIDIKAN TERAKHIR', 'JURUSAN',
    'NO TELEPON', 'EMAIL', 'JENIS KELAMIN', 'ALAMAT DOMISILI', 'KOTA KABUPATEN',
    'PROVINSI', 'JABATAN TERAKHIR', 'FUNGSI PEKERJAAN', 'UNIT', 'UNIT LAYANAN',
    'NOMOR BPJS KESEHATAN', 'NOMOR BPJS KETENAGAKERJAAN', 'NOMOR DPLK', 'BANK DPLK',
    'NO PERJANJIAN KERJA', 'TANGGAL MASUK KERJA', 'STATUS TENAGA KERJA', 'SKEMA TENAGA KERJA',
    'JUMLAH SERTIFIKASI',
]);

$i = 1;
foreach ($workers as $row) {
    fputcsv($file, [
        $row['no_urut'] ?: $i,
        $row['nomor_perjanjian'],
        $row['nama_perusahaan'],
        $row['nama'],
        "'" . $row['nik'],    // prefix ' to protect leading zeros
        $row['tempat_lahir'],
        $row['tanggal_lahir'],
        hitung_usia($row['tanggal_lahir']),
        $row['pendidikan_terakhir'],
        $row['jurusan'],
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
        "'" . $row['nomor_bpjs_kesehatan'],
        "'" . $row['nomor_bpjs_ketenagakerjaan'],
        "'" . $row['nomor_dplk'],
        $row['bank_dplk'],
        $row['no_perjanjian_kerja'],
        $row['tanggal_masuk_kerja'],
        $row['status_tenaga_kerja'],
        $row['skema_tenaga_kerja'],
        $row['sertifikasi_count'],
    ]);
    $i++;
}

fclose($file);
exit;
