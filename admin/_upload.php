<?php
// Unggah gambar panel (Canva AI): JPG/PNG/WEBP/GIF, maks 3 MB. Kembalikan nama file, atau $lama jika tidak ada file baru.
function simpan_gambar(?string $lama): ?string {
    if (empty($_FILES['gambar']['name'])) return $lama;
    $f = $_FILES['gambar'];
    if ($f['error'] !== UPLOAD_ERR_OK || $f['size'] > 3 * 1024 * 1024) throw new Exception('Gambar gagal diunggah / lebih dari 3 MB.');
    $info = @getimagesize($f['tmp_name']);
    $ext = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp', IMAGETYPE_GIF => 'gif'][$info[2] ?? 0] ?? null;
    if (!$ext) throw new Exception('Format gambar harus JPG, PNG, WEBP, atau GIF.');
    $nama = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], __DIR__ . '/../uploads/' . $nama)) throw new Exception('Gagal menyimpan gambar.');
    if ($lama) @unlink(__DIR__ . '/../uploads/' . basename($lama));
    return $nama;
}
function hapus_gambar(?string $g): void { if ($g) @unlink(__DIR__ . '/../uploads/' . basename($g)); }
function pilih_kategori(string $sel): string {
    $h = '<select name="kategori">';
    foreach (KATEGORI as $k) $h .= '<option ' . ($k === $sel ? 'selected' : '') . '>' . e($k) . '</option>';
    return $h . '</select>';
}
