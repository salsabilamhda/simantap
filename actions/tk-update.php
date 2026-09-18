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

$nik = preg_replace('/\s+/', '', post('nik'));
if ($nik !== '') {
    $duplicate = db_row("SELECT id, nama FROM tenaga_kerjas WHERE nik = ? AND id != ? LIMIT 1", [$nik, $id]);
    if ($duplicate) {
        flash_set('error', 'Gagal memperbarui: NIK ' . h($nik) . ' sudah digunakan oleh tenaga kerja lain ("' . h($duplicate['nama']) . '"). Perubahan tidak dapat disimpan.');
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
try {
    db_exec($sql, $params);
} catch (PDOException $e) {
    if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
        flash_set('error', 'Gagal memperbarui: NIK tersebut sudah digunakan oleh data tenaga kerja lain.');
    } else {
        flash_set('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
    }
    redirect(BASE_URL . '/tenaga-kerja.php');
}

flash_set('success', "Data $nama berhasil diperbarui!");
redirect(BASE_URL . '/tenaga-kerja.php');
