<?php
require '../config.php'; require_admin(); require '_upload.php';
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    try {
        if (isset($_POST['hapus'])) {
            $st = db()->prepare('SELECT gambar FROM soal WHERE id=?'); $st->execute([(int)$_POST['hapus']]);
            hapus_gambar($st->fetchColumn() ?: null);
            db()->prepare('DELETE FROM soal WHERE id=?')->execute([(int)$_POST['hapus']]);
        } else {
            $id = (int)($_POST['id'] ?? 0);
            $tipe = ($_POST['tipe'] ?? '') === 'uraian' ? 'uraian' : 'pg';
            $kunci = strtoupper(substr($_POST['kunci'] ?? '', 0, 1));
            $kat = in_array($_POST['kategori'] ?? '', KATEGORI, true) ? $_POST['kategori'] : KATEGORI[0];
            if ($tipe === 'pg' && !in_array($kunci, ['A', 'B', 'C', 'D'])) throw new Exception('Kunci jawaban harus A/B/C/D untuk pilihan ganda.');
            $lama = null;
            if ($id) { $st = db()->prepare('SELECT gambar FROM soal WHERE id=?'); $st->execute([$id]); $lama = $st->fetchColumn() ?: null; }
            $gbr = simpan_gambar($lama);
            $v = [(int)$_POST['urutan'], $kat, trim($_POST['tokoh']), trim($_POST['dialog']), $gbr, trim($_POST['pertanyaan']), $tipe,
                  trim($_POST['a']), trim($_POST['b']), trim($_POST['c']), trim($_POST['d']), $tipe === 'pg' ? $kunci : null];
            if ($v[5] === '') throw new Exception('Pertanyaan wajib diisi.');
            if ($id) { $v[] = $id; db()->prepare('UPDATE soal SET urutan=?,kategori=?,tokoh=?,dialog=?,gambar=?,pertanyaan=?,tipe=?,opsi_a=?,opsi_b=?,opsi_c=?,opsi_d=?,kunci=? WHERE id=?')->execute($v); }
            else db()->prepare('INSERT INTO soal (urutan,kategori,tokoh,dialog,gambar,pertanyaan,tipe,opsi_a,opsi_b,opsi_c,opsi_d,kunci) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')->execute($v);
        }
        header('Location: soal.php'); exit;
    } catch (Exception $ex) { $err = $ex->getMessage(); }
}
$edit = null;
if (isset($_GET['edit'])) { $st = db()->prepare('SELECT * FROM soal WHERE id=?'); $st->execute([(int)$_GET['edit']]); $edit = $st->fetch() ?: null; }
$list = db()->query('SELECT * FROM soal ORDER BY urutan, id')->fetchAll();
require '_nav.php';
?>
<div class="card"><h2><?= $edit ? 'Edit' : 'Tambah' ?> Soal Tes</h2>
<?php if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<form method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $edit['id'] ?? 0 ?>">
<label>Urutan</label><input type="number" name="urutan" value="<?= e($edit['urutan'] ?? count($list) + 1) ?>" required>
<label>Bagian</label><?= pilih_kategori($edit['kategori'] ?? KATEGORI[0]) ?>
<label>Gambar panel komik (hasil Canva AI, opsional)</label><input type="file" name="gambar" accept="image/*">
<?php if (!empty($edit['gambar'])): ?><small>Gambar saat ini: <?= e($edit['gambar']) ?> (unggah baru untuk mengganti)</small><?php endif; ?>
<label>Nama tokoh</label><input type="text" name="tokoh" value="<?= e($edit['tokoh'] ?? 'Guru') ?>">
<label>Dialog / narasi</label><textarea name="dialog"><?= e($edit['dialog'] ?? '') ?></textarea>
<label>Pertanyaan</label><textarea name="pertanyaan" required><?= e($edit['pertanyaan'] ?? '') ?></textarea>
<label>Tipe soal</label><select name="tipe"><option value="pg" <?= ($edit['tipe'] ?? 'pg') === 'pg' ? 'selected' : '' ?>>Pilihan ganda</option><option value="uraian" <?= ($edit['tipe'] ?? '') === 'uraian' ? 'selected' : '' ?>>Uraian</option></select>
<?php foreach (['a', 'b', 'c', 'd'] as $o): ?><label>Opsi <?= strtoupper($o) ?></label><input type="text" name="<?= $o ?>" value="<?= e($edit['opsi_' . $o] ?? '') ?>"><?php endforeach; ?>
<label>Kunci (A/B/C/D, untuk pilihan ganda)</label><input type="text" name="kunci" maxlength="1" value="<?= e($edit['kunci'] ?? '') ?>">
<button class="btn blue">Simpan</button> <?php if ($edit): ?><a class="btn gray" href="soal.php">Batal</a><?php endif; ?></form></div>

<div class="card"><h2>Daftar Soal (<?= count($list) ?>)</h2>
<table><tr><th>#</th><th>Bagian</th><th>Pertanyaan</th><th>Kunci</th><th>Aksi</th></tr>
<?php foreach ($list as $q): ?><tr><td><?= e($q['urutan']) ?></td><td><?= e($q['kategori']) ?></td><td><?= e(mb_strimwidth($q['pertanyaan'], 0, 80, '…')) ?></td><td><?= $q['tipe'] === 'pg' ? e($q['kunci']) : 'uraian' ?></td>
<td><a class="btn sm blue" href="soal.php?edit=<?= $q['id'] ?>">Edit</a>
<form method="post" style="display:inline" onsubmit="return confirm('Hapus soal ini beserta jawaban siswanya?')"><?= csrf_field() ?><button class="btn sm red" name="hapus" value="<?= $q['id'] ?>">Hapus</button></form></td></tr>
<?php endforeach; ?></table></div></div></body></html>
