<?php
require 'config.php';
$s = siswa_aktif();
if (!$s || $_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
csrf_check();
if ($s['selesai']) { header('Location: selesai.php'); exit; }

$pdo = db();
$soal = $pdo->query('SELECT * FROM soal')->fetchAll();
$j = $_POST['j'] ?? [];
$pdo->beginTransaction();
$totalPg = 0; $benarPg = 0;
$ins = $pdo->prepare('INSERT INTO jawaban (siswa_id,soal_id,jawaban,benar) VALUES (?,?,?,?)
                      ON DUPLICATE KEY UPDATE jawaban=VALUES(jawaban), benar=VALUES(benar)');
foreach ($soal as $q) {
    $ans = trim((string)($j[$q['id']] ?? ''));
    $benar = null;
    if ($q['tipe'] === 'pg') {
        $totalPg++;
        $benar = ($ans !== '' && strtoupper($ans) === strtoupper((string)$q['kunci'])) ? 1 : 0;
        $benarPg += $benar;
        $ans = strtoupper(mb_substr($ans, 0, 1));
    } else {
        $ans = mb_substr($ans, 0, 3000);
    }
    $ins->execute([$s['id'], $q['id'], $ans, $benar]);
}
$skor = $totalPg ? round($benarPg / $totalPg * 100, 2) : 0;
$pdo->prepare('UPDATE siswa SET selesai = NOW(), skor = ? WHERE id = ?')->execute([$skor, $s['id']]);
$pdo->commit();
header('Location: selesai.php');
