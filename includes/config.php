<?php
// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'banina_store');

// Konfigurasi Aplikasi
define('SITE_URL', 'http://localhost/banina-website');
define('UPLOAD_PATH', __DIR__ . '/../assets/images/uploads/');
define('UPLOAD_URL', SITE_URL . '/assets/images/uploads/');

// Koneksi Database
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }
    return $pdo;
}

if (session_status() === PHP_SESSION_NONE) { session_start(); }

function getSetting($key) {
    $db = getDB();
    $stmt = $db->prepare("SELECT value FROM settings WHERE `key` = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    return $row ? $row['value'] : '';
}
function isAdmin() { return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true; }
function redirect($url) { header("Location: $url"); exit; }
function sanitize($str) { return htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES, 'UTF-8'); }
function formatPrice($price) { return 'Rp ' . number_format($price, 0, ',', '.'); }
function uploadImage($file, $folder = '') {
    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) return false;
    if ($file['size'] > 5 * 1024 * 1024) return false;
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $path = UPLOAD_PATH . $folder;
    if (!is_dir($path)) mkdir($path, 0755, true);
    if (move_uploaded_file($file['tmp_name'], $path . $filename)) return ($folder ?: '') . $filename;
    return false;
}
?>
