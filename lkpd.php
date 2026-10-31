<?php
require 'config.php';
$s = siswa_aktif();
if (!$s) { header('Location: index.php'); exit; }
if ($s['selesai']) { header('Location: selesai.php'); exit; }
$soal = db()->query('SELECT * FROM soal ORDER BY urutan, id')->fetchAll();
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= JUDUL ?></title><link rel="stylesheet" href="assets/style.css"></head><body>
<header class="top"><b>📚 <?= JUDUL ?></b><span><a href="belajar.php" target="_blank">📖 Materi</a> 👤 <?= e($s['nama']) ?></span></header>
<div class="wrap">
<form method="post" action="simpan.php" onsubmit="return confirm('Kirim jawaban? Jawaban tidak bisa diubah lagi.')">
<?= csrf_field() ?>
<?php foreach ($soal as $i => $q): ?>
<div class="card panel"><span class="no">Soal <?= $i + 1 ?></span><span class="badge"><?= e($q['kategori']) ?></span>
  <?php if ($q['gambar']): ?><img src="uploads/<?= e($q['gambar']) ?>" alt="Panel komik <?= $i + 1 ?>"><?php endif; ?>
  <?php if ($q['dialog']): ?><div class="bubble"><b><?= e($q['tokoh']) ?>:</b> <?= nl2br(e($q['dialog'])) ?></div><?php endif; ?>
  <h3><?= nl2br(e($q['pertanyaan'])) ?></h3>
  <?php if ($q['tipe'] === 'pg'): foreach (['A', 'B', 'C', 'D'] as $h): $t = $q['opsi_' . strtolower($h)]; if ($t === null || $t === '') continue; ?>
    <label class="opt"><input type="radio" name="j[<?= $q['id'] ?>]" value="<?= $h ?>"> <?= $h ?>. <?= e($t) ?></label>
  <?php endforeach; else: ?>
    <textarea name="j[<?= $q['id'] ?>]" placeholder="Tulis jawabanmu di sini..."></textarea>
  <?php endif; ?>
</div>
<?php endforeach; ?>
<?php if (!$soal): ?><div class="card">Soal belum tersedia. Hubungi guru.</div><?php else: ?>
<button class="btn blue" type="submit">Kirim Jawaban ✔</button><?php endif; ?>
</form></div></body></html>
