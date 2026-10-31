# LKPD E-Comic Webinar AI Canva Code (PHP + MySQL)

## Pasang di hosting (cPanel)
1. Buat database MySQL + user di cPanel, lalu import `database.sql` lewat phpMyAdmin.
2. Edit `config.php` -> isi DB_NAME, DB_USER, DB_PASS.
3. Upload semua isi folder ke `public_html` (atau subfolder). Pastikan folder `uploads/` bisa ditulis (chmod 755/775).
4. Siswa: buka alamat utama. Admin: `/admin/`  (awal: admin / admin123 -> segera ganti di menu Ganti Password).

## Alur
- Siswa isi nama+kelas -> cookie `lkpd_token` (30 hari) disimpan; nama unik di database (1 nama = 1x mengerjakan).
- Admin: Kelola Soal (tambah panel komik dari Canva AI, dialog, soal PG/uraian), lihat hasil, Reset siswa, unduh PDF per siswa & rekap.
- PDF dibuat di browser (html2pdf.js dari CDN) - butuh internet saat menekan Unduh PDF. Tombol Cetak tetap bisa "Simpan sebagai PDF".
