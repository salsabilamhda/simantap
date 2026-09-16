<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$nama   = post('nama');
$status = post('status_tenaga_kerja', 'PKWTT');
$jk     = post('jenis_kelamin', 'LAKI');

if (empty($nama)) {
    flash_set('error', 'Nama tenaga kerja tidak boleh kosong.');
    redirect(BASE_URL . '/tenaga-kerja.php');
}

$fields = [
    'nama', 'nik', 'nomor_perjanjian', 'nama_perusahaan', 'tempat_lahir',
    'tanggal_lahir', 'no_telepon', 'email',
    'jenis_kelamin', 'alamat_domisili', 'kota_kabupaten', 'provinsi',
    'jabatan_terakhir', 'fungsi_pekerjaan', 'unit', 'unit_layanan_id',
    'nomor_bpjs_kesehatan', 'nomor_bpjs_ketenagakerjaan', 'nomor_dplk', 'bank_dplk',
    'no_perjanjian_kerja', 'tanggal_masuk_kerja', 'status_tenaga_kerja', 'skema_tenaga_kerja',
    'nomor_perjanjian',
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
db_exec($sql, $params);

flash_set('success', "Tenaga kerja $nama berhasil ditambahkan!");
redirect(BASE_URL . '/tenaga-kerja.php');
