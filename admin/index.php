<?php
require '../config.php';
if (is_admin()) { header('Location: dashboard.php'); exit; }
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $_SESSION['gagal'] = $_SESSION['gagal'] ?? 0;
    if ($_SESSION['gagal'] >= 5) { $err = 'Terlalu banyak percobaan. Tutup browser lalu coba lagi.'; }
    else {
        $st = db()->prepare('SELECT * FROM admin WHERE username=?'); $st->execute([trim($_POST['username'] ?? '')]);
        $a = $st->fetch();
        if ($a && password_verify($_POST['password'] ?? '', $a['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = $a['id']; $_SESSION['gagal'] = 0;
            header('Location: dashboard.php'); exit;
        }
        $_SESSION['gagal']++; $err = 'Username atau password salah.';
    }
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Login Admin</title><link rel="stylesheet" href="../assets/style.css"></head><body>
<header class="top"><b>🛠 Login Admin</b><a href="../index.php">Halaman Siswa</a></header>
<div class="wrap"><div class="card" style="max-width:420px;margin:30px auto">
<h2>Masuk Admin</h2><?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
<label>Username</label><input type="text" name="username" required>
<label>Password</label><input type="password" name="password" required>
<button class="btn blue" type="submit">Masuk</button></form></div></div></body></html>
