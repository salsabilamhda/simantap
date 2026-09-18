<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$nama   = post('nama');
$nik    = preg_replace('/\s+/', '', post('nik'));
$status = post('status_tenaga_kerja', 'PKWTT');
$jk     = post('jenis_kelamin', 'LAKI');

if (empty($nama)) {
    flash_set('error', 'Nama tenaga kerja tidak boleh kosong.');
    redirect(BASE_URL . '/tenaga-kerja.php');
}

// Cek apakah NIK sudah terdaftar
if ($nik !== '') {
    $existing = db_row("SELECT id, nama FROM tenaga_kerjas WHERE nik = ? LIMIT 1", [$nik]);
    if ($existing) {
        flash_set('error', 'Gagal menambahkan: Tenaga kerja dengan NIK ' . h($nik) . ' sudah terdaftar atas nama "' . h($existing['nama']) . '". Data tidak dapat ditambahkan lagi.');
        redirect(BASE_URL . '/tenaga-kerja.php');
    }
}

$fields = [
    'nama', 'nik', 'nomor_perjanjian', 'nama_perusahaan', 'tempat_lahir',
    'tanggal_lahir', 'no_telepon', 'email',
    'jenis_kelamin', 'alamat_domisili', 'kota_kabupaten', 'provinsi',
    'jabatan_terakhir', 'fungsi_pekerjaan', 'unit', 'unit_layanan_id',
    'nomor_bpjs_kesehatan', 'nomor_bpjs_ketenagakerjaan', 'nomor_dplk', 'bank_dplk',
    'no_perjanjian_kerja', 'tanggal_masuk_kerja', 'status_tenaga_kerja', 'skema_tenaga_kerja',
];

$cols   = [];
$vals   = [];
$params = [];

foreach ($fields as $f) {
    $val = post($f);
    if ($val !== '') {
        $cols[]   = "`$f`";
        $vals[]   = '?';
        $params[] = $val;
    }
}

// null-able date fields: set NULL if empty
foreach (['tanggal_lahir', 'tanggal_masuk_kerja'] as $df) {
    if (post($df) === '') {
        // already excluded by the loop above, handled via empty string check
    }
}

if (empty($cols)) {
    flash_set('error', 'Data tidak valid.');
    redirect(BASE_URL . '/tenaga-kerja.php');
}

$sql = "INSERT INTO tenaga_kerjas (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ")";
try {
    db_exec($sql, $params);
} catch (PDOException $e) {
    if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
        flash_set('error', 'Gagal menambahkan: NIK sudah terdaftar di database.');
    } else {
        flash_set('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
    }
    redirect(BASE_URL . '/tenaga-kerja.php');
}

flash_set('success', "Tenaga kerja $nama berhasil ditambahkan!");
redirect(BASE_URL . '/tenaga-kerja.php');
