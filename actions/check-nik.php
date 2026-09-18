<?php
// actions/check-nik.php
// Endpoint JSON untuk memeriksa apakah NIK sudah terdaftar di database
require_once dirname(__DIR__) . '/db.php';

if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
}

$nik = preg_replace('/\s+/', '', (string)($_GET['nik'] ?? $_POST['nik'] ?? ''));
$excludeId = (int)($_GET['exclude_id'] ?? $_POST['exclude_id'] ?? 0);

if ($nik === '') {
    echo json_encode(['exists' => false, 'message' => 'NIK kosong']);
    exit;
}

if ($excludeId > 0) {
    $existing = db_row('SELECT id, nama, nik FROM tenaga_kerjas WHERE nik = ? AND id != ? LIMIT 1', [$nik, $excludeId]);
} else {
    $existing = db_row('SELECT id, nama, nik FROM tenaga_kerjas WHERE nik = ? LIMIT 1', [$nik]);
}

if ($existing) {
    echo json_encode([
        'exists' => true,
        'id'     => (int) $existing['id'],
        'nama'   => $existing['nama'],
        'nik'    => $existing['nik'],
        'message'=> 'NIK sudah terdaftar atas nama ' . $existing['nama']
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'exists' => false,
        'message'=> 'NIK tersedia'
    ], JSON_UNESCAPED_UNICODE);
}
