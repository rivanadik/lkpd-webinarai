<?php
require '../config.php'; require_admin(); require '_upload.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        if (isset($_POST['hapus'])) {
            $st = db()->prepare('SELECT gambar FROM materi WHERE id=?'); $st->execute([(int)$_POST['hapus']]);
            hapus_gambar($st->fetchColumn() ?: null);
            db()->prepare('DELETE FROM materi WHERE id=?')->execute([(int)$_POST['hapus']]);
        } else {
            $id = (int)($_POST['id'] ?? 0);
            $kat = in_array($_POST['kategori'] ?? '', KATEGORI, true) ? $_POST['kategori'] : KATEGORI[0];
            $lama = null;
            if ($id) { $st = db()->prepare('SELECT gambar FROM materi WHERE id=?'); $st->execute([$id]); $lama = $st->fetchColumn() ?: null; }
            $gbr = simpan_gambar($lama);
            $v = [(int)$_POST['urutan'], $kat, trim($_POST['judul']), trim($_POST['tokoh']), trim($_POST['dialog']), $gbr, trim($_POST['isi']), trim($_POST['analogi'])];
            if ($v[2] === '' || $v[6] === '') throw new Exception('Judul dan isi materi wajib diisi.');
            if ($id) { $v[] = $id; db()->prepare('UPDATE materi SET urutan=?,kategori=?,judul=?,tokoh=?,dialog=?,gambar=?,isi=?,analogi=? WHERE id=?')->execute($v); }
            else db()->prepare('INSERT INTO materi (urutan,kategori,judul,tokoh,dialog,gambar,isi,analogi) VALUES (?,?,?,?,?,?,?,?)')->execute($v);
        }
        header('Location: materi.php'); exit;
    } catch (Exception $ex) { $err = $ex->getMessage(); }
}
$edit = null;
if (isset($_GET['edit'])) { $st = db()->prepare('SELECT * FROM materi WHERE id=?'); $st->execute([(int)$_GET['edit']]); $edit = $st->fetch() ?: null; }
$list = db()->query('SELECT * FROM materi ORDER BY urutan, id')->fetchAll();
require '_nav.php';
?>
<div class="card"><h2><?= $edit ? 'Edit' : 'Tambah' ?> Materi</h2>
<?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">
<label>Urutan</label><input type="number" name="urutan" value="<?= e($edit['urutan'] ?? count($list) + 1) ?>" required>
<label>Bagian</label><?= pilih_kategori($edit['kategori'] ?? KATEGORI[0]) ?>
<label>Judul materi</label><input type="text" name="judul" value="<?= e($edit['judul'] ?? '') ?>" required>
<label>Gambar komik (hasil Canva AI, opsional)</label><input type="file" name="gambar" accept="image/*">
<?php if (!empty($edit['gambar'])): ?><small>Gambar saat ini: <?= e($edit['gambar']) ?> (unggah baru untuk mengganti)</small><?php endif; ?>
<label>Nama tokoh</label><input type="text" name="tokoh" value="<?= e($edit['tokoh'] ?? 'Guru') ?>">
<label>Dialog pembuka (gaya komik)</label><textarea name="dialog"><?= e($edit['dialog'] ?? '') ?></textarea>
<label>Isi materi (awali baris dengan "- " untuk poin, **teks** untuk tebal)</label><textarea name="isi" style="min-height:200px" required><?= e($edit['isi'] ?? '') ?></textarea>
<label>Analogi</label><textarea name="analogi"><?= e($edit['analogi'] ?? '') ?></textarea>
<button class="btn blue">Simpan</button> <?php if ($edit): ?><a class="btn gray" href="materi.php">Batal</a><?php endif; ?></form></div>

<div class="card"><h2>Daftar Materi (<?= count($list) ?>)</h2>
<table><tr><th>#</th><th>Bagian</th><th>Judul</th><th>Aksi</th></tr>
<?php foreach ($list as $m): ?><tr><td><?= e($m['urutan']) ?></td><td><?= e($m['kategori']) ?></td><td><?= e($m['judul']) ?></td>
<td><a class="btn sm blue" href="materi.php?edit=<?= $m['id'] ?>">Edit</a>
<form method="post" style="display:inline" onsubmit="return confirm('Hapus materi ini?')"><?= csrf_field() ?><button class="btn sm red" name="hapus" value="<?= $m['id'] ?>">Hapus</button></form></td></tr>
<?php endforeach; ?></table></div></div></body></html>
