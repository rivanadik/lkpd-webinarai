<?php
require 'config.php';
$s = siswa_aktif();
if (!$s) { header('Location: index.php'); exit; }
$materi = db()->query('SELECT * FROM materi ORDER BY urutan, id')->fetchAll();
?><!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Materi - <?= JUDUL ?></title><link rel="stylesheet" href="assets/style.css"></head><body>
<header class="top"><b>📚 <?= JUDUL ?></b><span>👤 <?= e($s['nama']) ?></span></header>
<div class="wrap">
<div class="card"><h1>📖 Materi Pembelajaran</h1><p>Baca semua materi dengan teliti. Setelah itu kerjakan tes di bagian bawah halaman.</p></div>
<?php foreach ($materi as $i => $m): ?>
<div class="card panel materi"><span class="no">Materi <?= $i + 1 ?></span><span class="badge"><?= e($m['kategori']) ?></span>
  <h2><?= e($m['judul']) ?></h2>
  <?php if ($m['gambar']): ?><img src="uploads/<?= e($m['gambar']) ?>" alt="<?= e($m['judul']) ?>"><?php endif; ?>
  <?php if ($m['dialog']): ?><div class="bubble"><b><?= e($m['tokoh']) ?>:</b> <?= nl2br(e($m['dialog'])) ?></div><?php endif; ?>
  <?= fmt($m['isi']) ?>
  <?php if ($m['analogi']): ?><div class="analogi">💡 <b>Analogi:</b> <?= e($m['analogi']) ?></div><?php endif; ?>
</div>
<?php endforeach; if (!$materi): ?><div class="card">Materi belum tersedia.</div><?php endif; ?>
<div style="text-align:center;margin:24px 0"><?php if ($s['selesai']): ?><a class="btn blue" href="selesai.php">Lihat Hasil</a><?php else: ?><a class="btn" href="lkpd.php">Mulai Tes ▶</a><?php endif; ?></div>
</div></body></html>
