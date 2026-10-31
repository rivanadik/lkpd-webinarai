<?php
require '../config.php'; require_admin();
$sid = (int)($_GET['id'] ?? 0);
$base = '../'; $judul_laporan = 'Detail hasil siswa';
require '../partials_hasil.php';
