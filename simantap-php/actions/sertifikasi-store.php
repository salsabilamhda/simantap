<?php
require_once dirname(__DIR__) . '/db.php';
csrf_verify();

$tkId  = (int) post('tenaga_kerja_id');
$judul = post('judul_sertifikasi');

if (!$tkId || empty($judul)) {
    flash_set('error', 'Judul sertifikasi tidak boleh kosong.');
    redirect(BASE_URL . '/tenaga-kerja.php');
}

$tk = db_row("SELECT nama FROM tenaga_kerjas WHERE id = ?", [$tkId]);
if (!$tk) {
    flash_set('error', 'Tenaga kerja tidak ditemukan.');
    redirect(BASE_URL . '/tenaga-kerja.php');
}

// Handle file upload
$filePath = null;
if (isset($_FILES['gambar_sertifikat']) && $_FILES['gambar_sertifikat']['error'] === UPLOAD_ERR_OK) {
    $file     = $_FILES['gambar_sertifikat'];
    $maxSize  = 5 * 1024 * 1024; // 5MB
    $allowed  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $finfo    = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if ($file['size'] > $maxSize) {
        flash_set('error', 'File terlalu besar. Maksimum 5MB.');
        redirect(BASE_URL . '/tenaga-kerja.php');
    }

    if (!in_array($mimeType, $allowed)) {
        flash_set('error', 'Format file tidak didukung. Gunakan JPG, PNG, atau WebP.');
        redirect(BASE_URL . '/tenaga-kerja.php');
    }

    // Create upload dir if not exists
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'SERT_' . $tkId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . strtolower($ext);
    $dest     = UPLOAD_DIR . $fileName;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        $filePath = UPLOAD_URL . $fileName;
    } else {
        flash_set('error', 'Gagal menyimpan file. Periksa permission folder uploads/sertifikat/');
        redirect(BASE_URL . '/tenaga-kerja.php');
    }
}

db_exec(
    "INSERT INTO sertifikasis (tenaga_kerja_id, nomor_sertifikat, judul_sertifikasi, gambar_sertifikat_url, tanggal_terbit, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
    [
        $tkId,
        post('nomor_sertifikat') ?: null,
        $judul,
        $filePath,
        post('tanggal_terbit') ?: null,
    ]
);

flash_set('success', "Sertifikasi berhasil ditambahkan untuk {$tk['nama']}!");
redirect(BASE_URL . '/tenaga-kerja.php');
