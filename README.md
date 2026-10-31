# LKPD E-Comic Informatika SMP (PHP + MySQL)

Isi: materi Hardware, Software, Prompt AI (rumus P-T-K-F-B), Hitung Informatika (perkalian/pembagian satuan data), lalu tes 14 soal.

## Pasang di hosting (cPanel)
1. Buat database MySQL + user di cPanel, lalu import `database.sql` lewat phpMyAdmin (database kosong).
2. Edit `config.php` -> isi DB_NAME, DB_USER, DB_PASS.
3. Upload semua isi folder ke `public_html` (atau subfolder). Pastikan folder `uploads/` bisa ditulis (chmod 755/775).
4. Siswa: alamat utama. Admin: `/admin/` (awal: admin / admin123 -> segera ganti di menu Ganti Password).

## Alur
- Siswa isi nama+kelas -> cookie `lkpd_token` (30 hari); nama unik di database (1 nama = 1x mengerjakan) -> baca materi -> tes -> hasil.
- Admin: Kelola Materi, Kelola Soal (upload panel komik Canva AI), lihat hasil, Reset siswa, unduh PDF per siswa & rekap.
- PDF dibuat di browser (html2pdf.js dari CDN), butuh internet saat Unduh PDF. Tombol Cetak tetap bisa "Simpan sebagai PDF".
