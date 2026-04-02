<?php
// ForTransac POS - Configuration

define('APP_NAME', 'ForTransac');
define('APP_LOCATION', 'Racoon City');
define('APP_VERSION', '1.0.0');

// Database
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'fortransac_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

// Base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
define('BASE_URL', $protocol . '://' . $host);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DB Connection (mysqli)
function db() {
    static $conn = null;
    if ($conn === null) {
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$conn) {
            die(json_encode(['error' => 'Database connection failed: ' . mysqli_connect_error()]));
        }
        mysqli_set_charset($conn, 'utf8mb4');
    }
    return $conn;
}

// Auth helpers
function isLoggedIn() {
    return isset($_SESSION['kasir_id']) && !empty($_SESSION['kasir_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/pages/login.php');
        exit;
    }
}

function currentUser() {
    if (!isLoggedIn()) return null;
    $conn = db();
    $id = (int)$_SESSION['kasir_id'];
    $res = mysqli_query($conn, "SELECT id, username, email FROM kasir WHERE id = $id LIMIT 1");
    return mysqli_fetch_assoc($res);
}

// Sanitize
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function sanitize($str) {
    return mysqli_real_escape_string(db(), trim($str ?? ''));
}

// Format currency (Rupiah)
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Generate transaction code
function generateTrxCode() {
    $conn = db();
    $date = date('Ymd');
    $res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM transaksi WHERE DATE(created_at) = CURDATE()");
    $row = mysqli_fetch_assoc($res);
    $seq = str_pad(($row['cnt'] + 1), 4, '0', STR_PAD_LEFT);
    return "TRX-{$date}-{$seq}";
}

// Generate SKU from kategori alias, name, berat
function generateSKU($alias, $name, $berat) {
    $namePart = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $name));
    $namePart = substr($namePart, 0, 8);
    return strtoupper($alias) . '-' . $namePart . '-' . $berat;
}

// Flash messages
function setFlash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}
?>
