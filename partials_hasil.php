<?php
// Dipakai selesai.php (siswa) dan admin/detail.php. Butuh: $sid, $base ('' atau '../')
$st = db()->prepare('SELECT * FROM siswa WHERE id=?'); $st->execute([$sid]); $sw = $st->fetch();
if (!$sw) exit('Data tidak ditemukan');
$rows = db()->prepare('SELECT q.*, j.jawaban, j.benar FROM soal q LEFT JOIN jawaban j ON j.soal_id=q.id AND j.siswa_id=? ORDER BY q.urutan, q.id');
$rows->execute([$sid]); $rows = $rows->fetchAll();
$rekap = [];
foreach ($rows as $q) if ($q['tipe'] === 'pg') { $k = $q['kategori']; $rekap[$k] = $rekap[$k] ?? [0, 0]; $rekap[$k][1]++; $rekap[$k][0] += (int)$q['benar']; }
$fn = 'LKPD_' . preg_replace('/[^A-Za-z0-9]+/', '_', $sw['nama']) . '.pdf';
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($judul_laporan ?? 'Hasil') ?></title><link rel="stylesheet" href="<?= $base ?>assets/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script></head><body>
<header class="top"><b>📚 <?= JUDUL ?></b><span><?php if ($base): ?><a href="dashboard.php">← Dashboard</a><?php endif; ?></span></header>
<div class="wrap">
<div class="noprint"><button class="btn blue" onclick="unduh()">⬇ Unduh PDF</button> <button class="btn gray" onclick="window.print()">🖨 Cetak</button></div>
<div id="laporan" class="card">
  <h2>Hasil <?= JUDUL ?></h2>
  <table><tr><td width="25%">Nama</td><td><?= e($sw['nama']) ?></td></tr>
  <tr><td>Kelas</td><td><?= e($sw['kelas']) ?></td></tr>
  <tr><td>Selesai</td><td><?= e($sw['selesai']) ?></td></tr>
  <tr><td>Skor (pilihan ganda)</td><td><span class="skor"><?= e(rtrim(rtrim((string)$sw['skor'], '0'), '.')) ?></span> / 100</td></tr></table>
  <h3 style="margin-top:16px">Skor per Bagian</h3>
  <table><tr><th>Bagian</th><th>Benar</th></tr>
  <?php foreach ($rekap as $k => $r): ?><tr><td><?= e($k) ?></td><td><?= $r[0] ?> dari <?= $r[1] ?></td></tr><?php endforeach; ?></table>
  <h3 style="margin-top:16px">Rincian Jawaban</h3>
  <table><tr><th>No</th><th>Bagian</th><th>Pertanyaan</th><th>Jawaban</th><th>Kunci</th><th>Hasil</th></tr>
  <?php foreach ($rows as $i => $q): ?>
  <tr><td><?= $i + 1 ?></td><td><?= e($q['kategori']) ?></td><td><?= nl2br(e($q['pertanyaan'])) ?></td>
  <td><?= nl2br(e($q['jawaban'] !== null && $q['jawaban'] !== '' ? $q['jawaban'] : '(kosong)')) ?></td>
  <td><?= $q['tipe'] === 'pg' ? e($q['kunci']) : '-' ?></td>
  <td><?php if ($q['tipe'] === 'pg') echo $q['benar'] ? '<span class="benar">Benar</span>' : '<span class="salah">Salah</span>'; else echo 'Uraian'; ?></td></tr>
  <?php endforeach; ?></table>
</div></div>
<script>
function unduh(){html2pdf().set({margin:10,filename:<?= json_encode($fn) ?>,html2canvas:{scale:2},jsPDF:{unit:'mm',format:'a4'}}).from(document.getElementById('laporan')).save();}
</script></body></html>
