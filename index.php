<?php
require 'config.php';
$err = '';
$s = siswa_aktif();
if ($s) { header('Location: ' . ($s['selesai'] ? 'selesai.php' : 'belajar.php')); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nama  = trim(preg_replace('/\s+/', ' ', $_POST['nama'] ?? ''));
    $kelas = trim($_POST['kelas'] ?? '');
    if (mb_strlen($nama) < 3 || mb_strlen($nama) > 100) {
        $err = 'Nama minimal 3 karakter.';
    } else {
        $key = nama_key($nama);
        $cek = db()->prepare('SELECT id FROM siswa WHERE nama_key = ?');
        $cek->execute([$key]);
        if ($cek->fetch()) {
            $err = 'Nama ini sudah terdaftar / sudah mengerjakan. Satu nama hanya boleh satu kali. Hubungi guru jika ini kesalahan.';
        } else {
            $token = bin2hex(random_bytes(16));
            db()->prepare('INSERT INTO siswa (nama,nama_key,kelas,token) VALUES (?,?,?,?)')
                ->execute([$nama, $key, mb_substr($kelas, 0, 30), $token]);
            setcookie(COOKIE_SISWA, $token, ['expires' => time() + 86400 * 30, 'path' => '/', 'httponly' => true, 'samesite' => 'Lax']);
            header('Location: belajar.php'); exit;
        }
    }
}
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= JUDUL ?></title><link rel="stylesheet" href="assets/style.css"></head><body>
<header class="top"><b>📚 <?= JUDUL ?></b><span><a href="admin/index.php">Admin</a></span></header>
<div class="wrap"><div class="card">
<h1>Selamat datang, calon matematikawan! 🎉</h1>
<p>Pelajari materi Informatika lewat komik (hardware, software, dan prompt AI), lalu kerjakan tes. Isi nama dengan benar karena <b>satu nama hanya bisa mengerjakan satu kali</b>.</p>
<?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
<label>Nama lengkap</label><input type="text" name="nama" required maxlength="100">
<label>Kelas</label><input type="text" name="kelas" maxlength="30" placeholder="contoh: 7A">
<button class="btn" type="submit">Mulai Mengerjakan ▶</button></form>
</div></div></body></html>
