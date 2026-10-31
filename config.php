<?php
// ====== PENGATURAN DATABASE (ubah sesuai cPanel) ======
define('DB_HOST', 'localhost');
define('DB_NAME', 'lkpd_ecomic');
define('DB_USER', 'root');
define('DB_PASS', '');
define('JUDUL', 'LKPD E-Comic Matematika');
define('COOKIE_SISWA', 'lkpd_token');

session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

function db(): PDO {
    static $pdo = null;
    if (!$pdo) {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function csrf(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf() . '">'; }
function csrf_check(): void {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(400); exit('Permintaan tidak valid.'); }
}
function is_admin(): bool { return !empty($_SESSION['admin_id']); }
function require_admin(): void { if (!is_admin()) { header('Location: index.php'); exit; } }

// Siswa dikenali dari cookie token (1 nama = 1 perangkat/pengerjaan)
function siswa_aktif(): ?array {
    $t = $_COOKIE[COOKIE_SISWA] ?? '';
    if ($t === '' || !preg_match('/^[a-f0-9]{32}$/', $t)) return null;
    $st = db()->prepare('SELECT * FROM siswa WHERE token = ?');
    $st->execute([$t]);
    return $st->fetch() ?: null;
}
function nama_key(string $n): string {
    return mb_strtolower(trim(preg_replace('/\s+/', ' ', $n)), 'UTF-8');
}
