<?php
// ============================================================
// SIMANTAP — Helper Koneksi & Query Database (PDO)
// ============================================================

require_once __DIR__ . '/config.php';

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:20px;background:#fee;border:1px solid #fcc;border-radius:8px;"><strong>Koneksi Database Gagal:</strong> ' . htmlspecialchars($e->getMessage()) . '<br><small>Pastikan MySQL aktif dan konfigurasi di config.php sudah benar.</small></div>');
        }
    }
    return $pdo;
}

/** Jalankan query dan kembalikan semua baris */
function db_query(string $sql, array $params = []): array {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/** Jalankan query dan kembalikan satu baris */
function db_row(string $sql, array $params = []): ?array {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ?: null;
}

/** Jalankan query dan kembalikan nilai scalar pertama */
function db_val(string $sql, array $params = []) {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

/** Jalankan INSERT/UPDATE/DELETE dan kembalikan jumlah baris terpengaruh */
function db_exec(string $sql, array $params = []): int {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/** Kembalikan ID terakhir yang diinsert */
function db_last_id(): string {
    return db()->lastInsertId();
}

// ============================================================
// Helper: Session Flash Message
// ============================================================

function session_start_safe(): void {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
}

function flash_set(string $type, string $message): void {
    session_start_safe();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array {
    session_start_safe();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// ============================================================
// Helper: CSRF Token
// ============================================================

function csrf_token(): string {
    session_start_safe();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verify(): void {
    session_start_safe();
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Forbidden: Invalid CSRF token.');
    }
}

// ============================================================
// Helper: Autentikasi Admin & Session
// ============================================================

function auth_user(): ?array {
    session_start_safe();
    return $_SESSION['user'] ?? null;
}

function auth_check(): bool {
    return auth_user() !== null;
}

function auth_login(array $user): void {
    session_start_safe();
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
    $_SESSION['user'] = [
        'id'       => $user['id'],
        'name'     => $user['name'],
        'username' => $user['username'] ?? '',
        'email'    => $user['email'],
        'role'     => $user['role'] ?? 'admin',
        'status'   => $user['status'] ?? 'Aktif',
    ];
}

function auth_logout(): void {
    session_start_safe();
    unset($_SESSION['user']);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

function auth_require(): void {
    session_start_safe();
    if (!auth_check()) {
        flash_set('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        redirect(BASE_URL . '/login.php');
    }
}

// ============================================================
// Helper: Redirect
// ============================================================

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

function redirect_back(string $fallback = 'dashboard.php'): void {
    $ref = $_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/' . $fallback);
    redirect($ref);
}

// ============================================================
// Helper: Sanitize Input
// ============================================================

function h(mixed $val): string {
    return htmlspecialchars((string)($val ?? ''), ENT_QUOTES, 'UTF-8');
}

function post(string $key, string $default = ''): string {
    return trim($_POST[$key] ?? $default);
}

function get_param(string $key, string $default = ''): string {
    return trim($_GET[$key] ?? $default);
}

// ============================================================
// Helper: Hitung Usia dari Tanggal Lahir
// ============================================================

function hitung_usia(?string $tanggal_lahir): string {
    if (!$tanggal_lahir) return '-';
    try {
        $dob = new DateTime($tanggal_lahir);
        $now = new DateTime();
        return $now->diff($dob)->y . ' Tahun';
    } catch (Exception $e) {
        return '-';
    }
}

// ============================================================
// Helper: Format Tanggal ke Indonesia
// ============================================================

function format_tanggal(?string $date): string {
    if (!$date) return '-';
    try {
        return (new DateTime($date))->format('d/m/Y');
    } catch (Exception $e) {
        return $date;
    }
}
