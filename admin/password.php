<?php
require '../config.php'; require_admin();
$msg = ''; $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $st = db()->prepare('SELECT * FROM admin WHERE id=?'); $st->execute([$_SESSION['admin_id']]); $a = $st->fetch();
    $baru = $_POST['baru'] ?? '';
    if (!password_verify($_POST['lama'] ?? '', $a['password'])) $err = 'Password lama salah.';
    elseif (strlen($baru) < 8) $err = 'Password baru minimal 8 karakter.';
    else { db()->prepare('UPDATE admin SET password=? WHERE id=?')->execute([password_hash($baru, PASSWORD_DEFAULT), $a['id']]); $msg = 'Password berhasil diganti.'; }
}
require '_nav.php';
?>
<div class="card" style="max-width:460px"><h2>Ganti Password</h2>
<?php if ($msg): ?><div class="ok"><?= e($msg) ?></div><?php endif; if ($err): ?><div class="err"><?= e($err) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
<label>Password lama</label><input type="password" name="lama" required>
<label>Password baru (min. 8 karakter)</label><input type="password" name="baru" required>
<button class="btn blue">Simpan</button></form></div></div></body></html>
