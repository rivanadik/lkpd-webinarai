<?php
require '../config.php'; require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus'])) {
    csrf_check();
    db()->prepare('DELETE FROM siswa WHERE id=?')->execute([(int)$_POST['hapus']]); // jawaban ikut terhapus, nama bisa dipakai lagi
    header('Location: dashboard.php'); exit;
}
$list = db()->query('SELECT * FROM siswa ORDER BY mulai DESC')->fetchAll();
require '_nav.php';
?>
<div class="card"><h2>Hasil Siswa (<?= count($list) ?>)</h2>
<table><tr><th>Nama</th><th>Kelas</th><th>Status</th><th>Skor</th><th>Aksi</th></tr>
<?php foreach ($list as $s): ?>
<tr><td><?= e($s['nama']) ?></td><td><?= e($s['kelas']) ?></td>
<td><?= $s['selesai'] ? 'Selesai<br><small>' . e($s['selesai']) . '</small>' : 'Sedang mengerjakan' ?></td>
<td><?= $s['skor'] !== null ? e(rtrim(rtrim((string)$s['skor'], '0'), '.')) : '-' ?></td>
<td><?php if ($s['selesai']): ?><a class="btn sm blue" href="detail.php?id=<?= $s['id'] ?>">Detail / PDF</a> <?php endif; ?>
<form method="post" style="display:inline" onsubmit="return confirm('Hapus data siswa ini? Nama bisa dipakai mengerjakan lagi.')"><?= csrf_field() ?>
<button class="btn sm red" name="hapus" value="<?= $s['id'] ?>">Reset</button></form></td></tr>
<?php endforeach; if (!$list) echo '<tr><td colspan="5">Belum ada siswa.</td></tr>'; ?>
</table></div></div></body></html>
