-- LKPD E-Comic Matematika
-- Import lewat phpMyAdmin pada database yang sudah dibuat.
SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS siswa (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  nama_key VARCHAR(100) NOT NULL UNIQUE,   -- nama huruf kecil: 1 nama hanya 1 data
  kelas VARCHAR(30) DEFAULT NULL,
  token CHAR(32) NOT NULL UNIQUE,          -- disimpan juga di cookie siswa
  mulai DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  selesai DATETIME DEFAULT NULL,
  skor DECIMAL(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS soal (
  id INT AUTO_INCREMENT PRIMARY KEY,
  urutan INT NOT NULL DEFAULT 1,
  tokoh VARCHAR(50) DEFAULT 'Guru',
  dialog TEXT,                              -- narasi/dialog panel komik
  gambar VARCHAR(255) DEFAULT NULL,         -- file hasil desain Canva AI (folder uploads/)
  pertanyaan TEXT NOT NULL,
  tipe ENUM('pg','uraian') NOT NULL DEFAULT 'pg',
  opsi_a VARCHAR(255), opsi_b VARCHAR(255), opsi_c VARCHAR(255), opsi_d VARCHAR(255),
  kunci CHAR(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS jawaban (
  id INT AUTO_INCREMENT PRIMARY KEY,
  siswa_id INT NOT NULL,
  soal_id INT NOT NULL,
  jawaban TEXT,
  benar TINYINT(1) DEFAULT NULL,
  UNIQUE KEY uq (siswa_id, soal_id),
  FOREIGN KEY (siswa_id) REFERENCES siswa(id) ON DELETE CASCADE,
  FOREIGN KEY (soal_id) REFERENCES soal(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Admin awal: username admin / password admin123 (SEGERA ganti lewat menu Admin > Ganti Password)
INSERT INTO admin (username, password) VALUES
('admin', '$2y$10$jarcxPAyRmq93O1mDUFFIuq7EmKaTSGPMp9BUa2WS6GSkDRW5NnFm');

-- Contoh soal (topik Pecahan) - bisa diganti/diedit admin
INSERT INTO soal (urutan,tokoh,dialog,pertanyaan,tipe,opsi_a,opsi_b,opsi_c,opsi_d,kunci) VALUES
(1,'Dina','Ibu membagi 1 pizza menjadi 8 potong sama besar. Aku makan 3 potong!','Pecahan yang menyatakan bagian pizza yang dimakan Dina adalah ...','pg','3/5','3/8','8/3','5/8','B'),
(2,'Budi','Aku makan 2 potong. Berarti aku makan lebih sedikit dari Dina, ya?','Perbandingan yang benar antara bagian Dina dan Budi adalah ...','pg','3/8 > 2/8','3/8 < 2/8','3/8 = 2/8','2/8 > 3/8','A'),
(3,'Ibu','Sisa pizza kita simpan untuk besok.','Berapa pecahan sisa pizza jika Dina dan Budi sudah memakan 5 potong?','pg','3/8','5/8','8/5','2/8','A'),
(4,'Dina','Besok kita beli 2 pizza lagi, masing-masing dibagi 4 potong.','Pecahan 2/4 senilai dengan ...','pg','1/4','1/2','2/8','3/4','B'),
(5,'Guru','Sekarang giliran kamu membuat cerita pecahanmu sendiri!','Tuliskan satu contoh situasi sehari-hari yang menggunakan pecahan, lalu jelaskan cara menentukan nilainya.','uraian',NULL,NULL,NULL,NULL,NULL);
