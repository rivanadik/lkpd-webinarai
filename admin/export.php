<?php
require '../config.php'; require_admin();
$full = isset($_GET['full']);
$list = db()->query('SELECT * FROM siswa WHERE selesai IS NOT NULL ORDER BY nama')->fetchAll();
$soal = db()->query('SELECT * FROM soal ORDER BY urutan, id')->fetchAll();
require '_nav.php';
?>
<div class="noprint">
<button class="btn blue" onclick="unduh()">⬇ Unduh PDF</button>
<?php if ($full): ?><a class="btn gray" href="export.php">Rekap nilai saja</a><?php else: ?><a class="btn gray" href="export.php?full=1">Dengan jawaban lengkap</a><?php endif; ?>
</div>
<div id="laporan" class="card">
<h2>Rekap Hasil <?= JUDUL ?></h2>
<p>Dicetak: <?= date('d-m-Y H:i') ?> · Jumlah siswa: <?= count($list) ?></p>
<table><tr><th>No</th><th>Nama</th><th>Kelas</th><th>Selesai</th><th>Skor</th></tr>
<?php foreach ($list as $i => $s): ?>
<tr><td><?= $i + 1 ?></td><td><?= e($s['nama']) ?></td><td><?= e($s['kelas']) ?></td><td><?= e($s['selesai']) ?></td><td><?= e(rtrim(rtrim((string)$s['skor'], '0'), '.')) ?></td></tr>
<?php endforeach; ?></table>
<?php if (count($list)): $avg = array_sum(array_column($list, 'skor')) / count($list); ?><p><b>Rata-rata kelas: <?= round($avg, 2) ?></b></p><?php endif; ?>

<?php if ($full): foreach ($list as $s):
  $r = db()->prepare('SELECT soal_id, jawaban, benar FROM jawaban WHERE siswa_id=?'); $r->execute([$s['id']]);
  $jw = []; foreach ($r->fetchAll() as $x) $jw[$x['soal_id']] = $x; ?>
  <div class="pb"></div>
  <h3><?= e($s['nama']) ?> (<?= e($s['kelas']) ?>) - Skor <?= e(rtrim(rtrim((string)$s['skor'], '0'), '.')) ?></h3>
  <table><tr><th>No</th><th>Pertanyaan</th><th>Jawaban</th><th>Hasil</th></tr>
  <?php foreach ($soal as $i => $q): $x = $jw[$q['id']] ?? null; ?>
  <tr><td><?= $i + 1 ?></td><td><?= nl2br(e($q['pertanyaan'])) ?></td><td><?= nl2br(e($x['jawaban'] ?? '(kosong)')) ?></td>
  <td><?php if ($q['tipe'] === 'pg') echo ($x && $x['benar']) ? '<span class="benar">Benar</span>' : '<span class="salah">Salah</span>'; else echo 'Uraian'; ?></td></tr>
  <?php endforeach; ?></table>
<?php endforeach; endif; ?>
</div></div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>function unduh(){html2pdf().set({margin:10,filename:'Rekap_LKPD<?= $full ? '_lengkap' : '' ?>.pdf',pagebreak:{mode:['css']},html2canvas:{scale:2},jsPDF:{unit:'mm',format:'a4'}}).from(document.getElementById('laporan')).save();}</script>
</body></html>
