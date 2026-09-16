<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$id   = (int) post('id');
$nama = post('nama');

if (!$id || empty($nama)) {
    flash_set('error', 'Data tidak valid.');
    redirect(BASE_URL . '/tenaga-kerja.php');
}

$existing = db_row("SELECT id FROM tenaga_kerjas WHERE id = ?", [$id]);
if (!$existing) {
    flash_set('error', 'Data tenaga kerja tidak ditemukan.');
    redirect(BASE_URL . '/tenaga-kerja.php');
}

$fields = [
    'nama', 'nik', 'nomor_perjanjian', 'nama_perusahaan', 'tempat_lahir',
    'tanggal_lahir', 'no_telepon', 'email',
    'jenis_kelamin', 'alamat_domisili', 'kota_kabupaten', 'provinsi',
    'jabatan_terakhir', 'fungsi_pekerjaan', 'unit', 'unit_layanan_id',
    'nomor_bpjs_kesehatan', 'nomor_bpjs_ketenagakerjaan', 'nomor_dplk', 'bank_dplk',
    'no_perjanjian_kerja', 'tanggal_masuk_kerja', 'status_tenaga_kerja', 'skema_tenaga_kerja',
];

$setClauses = [];
$params     = [];

foreach ($fields as $f) {
    $val = $_POST[$f] ?? null;
    $setClauses[] = "`$f` = ?";
    // Store empty string as NULL for date fields
    if (in_array($f, ['tanggal_lahir', 'tanggal_masuk_kerja']) && $val === '') {
        $params[] = null;
    } elseif ($f === 'unit_layanan_id' && $val === '') {
        $params[] = null;
    } else {
        $params[] = $val === '' ? null : $val;
    }
}

$params[] = $id;
$sql = "UPDATE tenaga_kerjas SET " . implode(', ', $setClauses) . " WHERE id = ?";
db_exec($sql, $params);

flash_set('success', "Data $nama berhasil diperbarui!");
redirect(BASE_URL . '/tenaga-kerja.php');
