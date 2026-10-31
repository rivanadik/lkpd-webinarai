<?php
require 'config.php';
$s = siswa_aktif();
if (!$s) { header('Location: index.php'); exit; }
if (!$s['selesai']) { header('Location: lkpd.php'); exit; }
$sid = $s['id']; $judul_laporan = 'Hasil LKPD - ' . $s['nama'];
$base = '';
require 'partials_hasil.php';
