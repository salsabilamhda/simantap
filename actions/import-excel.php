<?php
require_once dirname(__DIR__) . '/db.php';

header('Content-Type: application/json; charset=utf-8');
session_start_safe();

function import_json_error(string $message, int $status = 400): never
{
    http_response_code($status);
    echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    import_json_error('Metode request tidak diizinkan.', 405);
}

$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrfToken)) {
    import_json_error('Token keamanan tidak valid.', 403);
}

$payload = json_decode(file_get_contents('php://input'), true);
$rows = $payload['rows'] ?? null;
if (!is_array($rows) || !$rows) {
    import_json_error('Tidak ada data untuk diimport.');
}
if (count($rows) > 5000) {
    import_json_error('Maksimal 5.000 baris per import.');
}

function import_value(array $row, string $key): ?string
{
    $value = trim((string) ($row[$key] ?? ''));
    return $value === '' ? null : ltrim($value, "'");
}

function import_required(array $row, string $key, int $rowNumber): string
{
    $value = import_value($row, $key);
    if ($value === null) {
        throw new RuntimeException("Kolom $key pada baris $rowNumber wajib diisi.");
    }
    return $value;
}

function import_date(?string $value, string $label, int $rowNumber): ?string
{
    if ($value === null) return null;
    foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y', 'm-d-Y'] as $format) {
        $date = DateTime::createFromFormat('!' . $format, $value);
        if ($date && $date->format($format) === $value) return $date->format('Y-m-d');
    }
    throw new RuntimeException("$label pada baris $rowNumber tidak valid.");
}

function import_unit_id(?string $value, int $rowNumber): ?int
{
    if ($value === null) {
        throw new RuntimeException("UNIT LAYANAN pada baris $rowNumber wajib diisi.");
    }
    $row = db_row('SELECT id FROM unit_layanans WHERE UPPER(TRIM(nama)) = UPPER(TRIM(?)) OR UPPER(TRIM(kode)) = UPPER(TRIM(?))', [$value, $value]);
    if (!$row) {
        throw new RuntimeException("UNIT LAYANAN '$value' pada baris $rowNumber tidak ditemukan di master data.");
    }
    return (int) $row['id'];
}

try {
    $records = [];
    foreach ($rows as $index => $row) {
        if (!is_array($row)) throw new RuntimeException('Format baris Excel tidak valid.');
        $rowNumber = $index + 2;
        $records[] = [
            'worker' => [
            import_required($row, 'NO', $rowNumber),
            import_required($row, 'NOMOR PERJANJIAN', $rowNumber),
            import_required($row, 'NAMA PERUSAHAAN', $rowNumber),
            import_required($row, 'NAMA', $rowNumber),
            import_required($row, 'NIK', $rowNumber),
            import_required($row, 'TEMPAT LAHIR', $rowNumber),
            import_date(import_required($row, 'TANGGAL TAHUN LAHIR', $rowNumber), 'TANGGAL TAHUN LAHIR', $rowNumber),
            null,
            null,
            import_required($row, 'NO TELEPON (WA)', $rowNumber),
            import_required($row, 'EMAIL', $rowNumber),
            import_required($row, 'JENIS KELAMIN', $rowNumber),
            import_required($row, 'ALAMAT DOMISILI', $rowNumber),
            import_required($row, 'KOTA/KABUPATEN', $rowNumber),
            import_required($row, 'PROVINSI', $rowNumber),
            import_required($row, 'JABATAN TERAKHIR', $rowNumber),
            null,
            import_required($row, 'UNIT', $rowNumber),
            import_unit_id(import_value($row, 'UNIT LAYANAN'), $rowNumber),
            import_required($row, 'NOMOR BPJS KESEHATAN', $rowNumber),
            import_required($row, 'NOMOR BPJS KETENAGAKERJAAN', $rowNumber),
            import_required($row, 'NOMOR DPLK', $rowNumber),
            import_required($row, 'BANK DPLK', $rowNumber),
            import_required($row, 'NOMOR PERJANJIAN KERJA PKWT/PKWTT', $rowNumber),
            import_date(import_required($row, 'TANGGAL MASUK KERJA', $rowNumber), 'TANGGAL MASUK KERJA', $rowNumber),
            import_required($row, 'STATUS TENAGA KERJA (PKWT/PKWTT)', $rowNumber),
            import_required($row, 'SKEMA TENAGA KERJA (PEMBORONGAN / VENDOR BASED)', $rowNumber),
            ],
            'certificate' => [
                import_required($row, 'NOMOR SERTIFIKAT (SERTIFIKASI WAJIB)', $rowNumber),
                import_required($row, 'JUDUL SERTIFIKASI', $rowNumber),
            ],
        ];
    }

    $columns = ['no_urut', 'nomor_perjanjian', 'nama_perusahaan', 'nama', 'nik', 'tempat_lahir', 'tanggal_lahir', 'pendidikan_terakhir', 'jurusan', 'no_telepon', 'email', 'jenis_kelamin', 'alamat_domisili', 'kota_kabupaten', 'provinsi', 'jabatan_terakhir', 'fungsi_pekerjaan', 'unit', 'unit_layanan_id', 'nomor_bpjs_kesehatan', 'nomor_bpjs_ketenagakerjaan', 'nomor_dplk', 'bank_dplk', 'no_perjanjian_kerja', 'tanggal_masuk_kerja', 'status_tenaga_kerja', 'skema_tenaga_kerja'];
    $statement = db()->prepare('INSERT INTO tenaga_kerjas (`' . implode('`, `', $columns) . '`, created_at, updated_at) VALUES (' . implode(', ', array_fill(0, count($columns), '?')) . ', NOW(), NOW())');
    $certificateStatement = db()->prepare('INSERT INTO sertifikasis (tenaga_kerja_id, nomor_sertifikat, judul_sertifikasi, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');

    db()->beginTransaction();
    foreach ($records as $record) {
        $statement->execute($record['worker']);
        $certificateStatement->execute([db()->lastInsertId(), $record['certificate'][0], $record['certificate'][1]]);
    }
    db()->commit();
    echo json_encode(['success' => true, 'message' => count($records) . ' data tenaga kerja berhasil diimport.'], JSON_UNESCAPED_UNICODE);
} catch (Throwable $exception) {
    if (db()->inTransaction()) db()->rollBack();
    import_json_error('Import dibatalkan: ' . $exception->getMessage());
}
