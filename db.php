<?php
// ============================================================
// SIMANTAP — Helper Koneksi & Query Database (PDO)
// Kompatibel dengan PHP 5.6 hingga PHP 8.x
// ============================================================

require_once __DIR__ . '/config.php';

// Polyfill random_bytes untuk PHP 5.6
if (!function_exists('random_bytes')) {
    function random_bytes($length) {
        return openssl_random_pseudo_bytes($length);
    }
}

// Polyfill hash_equals untuk PHP < 5.6 jika dibutuhkan
if (!function_exists('hash_equals')) {
    function hash_equals($known_string, $user_string) {
        $ret = 0;
        if (strlen($known_string) !== strlen($user_string)) {
            $user_string = $known_string;
            $ret = 1;
        }
        $res = $known_string ^ $user_string;
        for ($i = strlen($res) - 1; $i >= 0; --$i) {
            $ret |= ord($res[$i]);
        }
        return $ret === 0;
    }
}

function db() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ));
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:20px;background:#fee;border:1px solid #fcc;border-radius:8px;"><strong>Koneksi Database Gagal:</strong> ' . htmlspecialchars($e->getMessage()) . '<br><small>Pastikan MySQL aktif dan konfigurasi di config.php sudah benar.</small></div>');
        }
    }
    return $pdo;
}

/** Jalankan query dan kembalikan semua baris */
function db_query($sql, $params = array()) {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/** Jalankan query dan kembalikan satu baris */
function db_row($sql, $params = array()) {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row ? $row : null;
}

/** Jalankan query dan kembalikan nilai scalar pertama */
function db_val($sql, $params = array()) {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchColumn();
}

/** Jalankan INSERT/UPDATE/DELETE dan kembalikan jumlah baris terpengaruh */
function db_exec($sql, $params = array()) {
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/** Kembalikan ID terakhir yang diinsert */
function db_last_id() {
    return db()->lastInsertId();
}

// ============================================================
// Helper: Session Flash Message
// ============================================================

function session_start_safe() {
    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
}

function flash_set($type, $message) {
    session_start_safe();
    $_SESSION['flash'] = array('type' => $type, 'message' => $message);
}

function flash_get() {
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

function csrf_token() {
    session_start_safe();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

function csrf_verify() {
    session_start_safe();
    $token = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    $sessToken = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';
    if (!hash_equals($sessToken, $token)) {
        http_response_code(403);
        die('Forbidden: Invalid CSRF token.');
    }
}

// ============================================================
// Helper: Autentikasi Admin & Session
// ============================================================

function auth_user() {
    session_start_safe();
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function auth_check() {
    return auth_user() !== null;
}

function auth_login($user) {
    session_start_safe();
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
    $_SESSION['user'] = array(
        'id'       => $user['id'],
        'name'     => $user['name'],
        'username' => isset($user['username']) ? $user['username'] : '',
        'email'    => $user['email'],
        'role'     => isset($user['role']) ? $user['role'] : 'admin',
        'status'   => isset($user['status']) ? $user['status'] : 'Aktif',
    );
}

function auth_logout() {
    session_start_safe();
    unset($_SESSION['user']);
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_destroy();
    }
}

function auth_require() {
    session_start_safe();
    if (!auth_check()) {
        flash_set('error', 'Silakan login terlebih dahulu untuk mengakses halaman ini.');
        redirect(BASE_URL . '/login.php');
    }
}

// ============================================================
// Helper: Redirect
// ============================================================

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function redirect_back($fallback = 'dashboard.php') {
    $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : (BASE_URL . '/' . $fallback);
    redirect($ref);
}

// ============================================================
// Helper: Sanitize Input
// ============================================================

function h($val) {
    return htmlspecialchars((string)($val !== null ? $val : ''), ENT_QUOTES, 'UTF-8');
}

function post($key, $default = '') {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

function get_param($key, $default = '') {
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

// ============================================================
// Helper: Hitung Usia dari Tanggal Lahir
// ============================================================

function hitung_usia($tanggal_lahir) {
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

function format_tanggal($date) {
    if (!$date) return '-';
    try {
        return (new DateTime($date))->format('d/m/Y');
    } catch (Exception $e) {
        return $date;
    }
}
