-- LKPD E-Comic Informatika SMP
-- Import lewat phpMyAdmin pada database yang sudah dibuat (database baru/kosong).
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

CREATE TABLE IF NOT EXISTS materi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  urutan INT NOT NULL DEFAULT 1,
  kategori VARCHAR(40) NOT NULL,
  judul VARCHAR(150) NOT NULL,
  tokoh VARCHAR(50) DEFAULT 'Guru',
  dialog TEXT,                              -- pembuka gaya komik
  gambar VARCHAR(255) DEFAULT NULL,         -- panel komik dari Canva AI (folder uploads/)
  isi TEXT NOT NULL,                        -- "- " = poin, **tebal**
  analogi TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS soal (
  id INT AUTO_INCREMENT PRIMARY KEY,
  urutan INT NOT NULL DEFAULT 1,
  kategori VARCHAR(40) NOT NULL DEFAULT 'Hardware',
  tokoh VARCHAR(50) DEFAULT 'Guru',
  dialog TEXT,
  gambar VARCHAR(255) DEFAULT NULL,
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

-- Admin awal: admin / admin123 (SEGERA ganti lewat menu Ganti Password)
INSERT INTO admin (username, password) VALUES
('admin', '$2y$10$jarcxPAyRmq93O1mDUFFIuq7EmKaTSGPMp9BUa2WS6GSkDRW5NnFm');

-- ================= MATERI =================
INSERT INTO materi (urutan,kategori,judul,tokoh,dialog,isi,analogi) VALUES
(1,'Hardware','Hardware: Bagian Komputer yang Bisa Disentuh','Rani','Kak, kenapa laptopku lemot kalau membuka banyak aplikasi sekaligus?',
'**Hardware** (perangkat keras) adalah bagian komputer yang bisa dilihat dan disentuh.\n- **Input**: memasukkan data, contoh keyboard, mouse, mikrofon\n- **Proses**: otak komputer, yaitu CPU (prosesor)\n- **Memori**: RAM menyimpan data sementara yang sedang dipakai; storage (SSD, harddisk, flashdisk) menyimpan data permanen\n- **Output**: menampilkan hasil, contoh monitor, speaker, printer\nCPU mengerjakan perintah satu per satu dengan sangat cepat. Jika RAM penuh, komputer menjadi lemot.',
'CPU seperti koki, RAM seperti meja kerja di dapur, dan storage seperti lemari bahan makanan. Meja kerja yang sempit membuat koki sulit memasak banyak menu sekaligus.'),
(2,'Software','Software: Perintah yang Menghidupkan Hardware','Dodi','Laptopku punya Windows dan Chrome. Dua-duanya software, tapi kok beda ya?',
'**Software** (perangkat lunak) adalah program atau instruksi yang membuat hardware bisa bekerja.\n- **Software sistem**: mengatur hardware, contoh sistem operasi Windows, Android, Linux\n- **Software aplikasi**: dipakai pengguna untuk mengerjakan tugas, contoh browser, Microsoft Word, game\n- **Driver**: software kecil yang membuat hardware (misalnya printer) dikenali oleh sistem operasi\nHardware tanpa software tidak bisa bekerja, dan software tanpa hardware tidak bisa dijalankan.',
'Sistem operasi seperti manajer restoran yang mengatur koki, pelayan, dan pesanan. Aplikasi adalah menu yang dipesan pelanggan.'),
(3,'Prompt AI','Prompt AI: Cara Bertanya yang Baik, Benar, dan Tepat Sasaran','Kak Guru','AI hanya bisa menebak dari kata-kata kita. Makin jelas prompt, makin tepat jawabannya!',
'**Prompt** adalah perintah atau pertanyaan yang kita tulis untuk AI. Gunakan rumus **P-T-K-F-B**:\n- **P**eran: AI berperan sebagai siapa? (contoh: guru Informatika SMP)\n- **T**ugas: apa yang harus dikerjakan? (contoh: jelaskan fungsi RAM)\n- **K**onteks: untuk siapa dan untuk apa? (contoh: untuk siswa kelas 7)\n- **F**ormat: bentuk jawabannya (contoh: 3 poin singkat)\n- **B**atasan: aturan tambahan (contoh: maksimal 100 kata, bahasa sederhana)\n**Kurang tepat:** "Jelaskan komputer."\n**Tepat sasaran:** "Kamu adalah guru Informatika SMP. Jelaskan fungsi RAM untuk siswa kelas 7 dengan analogi dapur, dalam 3 poin, maksimal 100 kata."\n**Etika dan keamanan:**\n- Periksa kembali jawaban AI karena bisa saja keliru\n- Jangan memasukkan data pribadi (password, alamat, nomor HP)\n- Gunakan AI untuk membantu belajar, bukan menyalin mentah untuk tugas',
'Prompt seperti memesan makanan. "Makanan satu" membingungkan, tetapi "nasi goreng, pedas sedang, tanpa telur" membuat pesanan tepat.'),
(4,'Hitung Informatika','Matematika di Informatika: Menghitung Ukuran Data','Rani','Ternyata perkalian dan pembagian dipakai untuk menghitung kapasitas file!',
'Pada latihan ini kita pakai 1 GB = 1.000 MB agar hitungan mudah.\n- Total ukuran = jumlah file × ukuran satu file\n- Banyak file yang muat = kapasitas ÷ ukuran satu file\n**Contoh 1:** 3 foto @ 5 MB, total = 3 × 5 = 15 MB.\n**Contoh 2:** Flashdisk 8 GB diisi file 2 GB, muat 8 ÷ 2 = 4 file.',
'Flashdisk seperti kardus dan file seperti kotak. Kita menghitung berapa kotak yang muat di dalam kardus.');

-- ================= SOAL TES =================
INSERT INTO soal (urutan,kategori,tokoh,dialog,pertanyaan,tipe,opsi_a,opsi_b,opsi_c,opsi_d,kunci) VALUES
(1,'Hardware','Rani','Aku mengetik pakai keyboard, lalu hasilnya muncul di monitor.','Keyboard dan monitor berturut-turut termasuk perangkat ...','pg','Input dan output','Output dan input','Proses dan input','Storage dan output','A'),
(2,'Hardware','Dodi','Laptopku lemot kalau membuka banyak aplikasi.','Komponen yang menyimpan data sementara yang sedang dipakai CPU adalah ...','pg','Harddisk','RAM','Monitor','Printer','B'),
(3,'Hardware','Rani','Ingat analogi dapur tadi!','Pada analogi dapur, CPU berperan sebagai ...','pg','Meja kerja','Lemari bahan','Koki','Piring','C'),
(4,'Software','Dodi','Chrome dan Word itu sama-sama program yang kupakai sehari-hari.','Berikut yang termasuk software aplikasi adalah ...','pg','Windows','Linux','Microsoft Word','Driver printer','C'),
(5,'Software','Rani','Tanpa Windows atau Android, perangkat kita tidak bisa dipakai.','Tugas utama sistem operasi adalah ...','pg','Mengatur hardware dan menjalankan program','Khusus untuk bermain game','Hanya untuk mengetik dokumen','Hanya untuk menyimpan foto','A'),
(6,'Software','Dodi','Printer baruku tidak terdeteksi oleh laptop.','Software yang perlu dipasang agar printer dikenali laptop adalah ...','pg','Game','Driver','Antivirus','Browser','B'),
(7,'Prompt AI','Kak Guru','Aku ingin belajar RAM dengan bantuan AI.','Prompt yang paling tepat sasaran adalah ...','pg','Jelaskan komputer.','RAM itu apa??','Kamu guru Informatika SMP. Jelaskan fungsi RAM untuk siswa kelas 7 dengan analogi dapur, maksimal 100 kata.','Tolong buatkan tugasku.','C'),
(8,'Prompt AI','Kak Guru','Dalam rumus P-T-K-F-B, setiap huruf punya arti.','Bagian prompt yang menentukan AI berperan sebagai siapa disebut ...','pg','Peran','Format','Batasan','Konteks','A'),
(9,'Prompt AI','Kak Guru','AI sudah menjawab pertanyaanmu.','Sikap yang benar setelah menerima jawaban AI adalah ...','pg','Langsung percaya sepenuhnya','Memeriksa kembali dengan sumber lain','Menyebarkan tanpa dibaca','Memberi password agar AI lebih akurat','B'),
(10,'Prompt AI','Kak Guru','Sekarang giliranmu menulis prompt sendiri!','Tulis satu prompt untuk meminta AI menjelaskan perbedaan hardware dan software. Gunakan unsur Peran, Tugas, Konteks, Format, dan Batasan.','uraian',NULL,NULL,NULL,NULL,NULL),
(11,'Hitung Informatika','Rani','Aku punya 4 foto, masing-masing 5 MB.','Total ukuran semua foto adalah ...','pg','9 MB','20 MB','10 MB','45 MB','B'),
(12,'Hitung Informatika','Dodi','Flashdisk-ku 16 GB. Aku ingin mengisinya dengan video 2 GB per file.','Banyak video yang muat di flashdisk adalah ...','pg','4 video','8 video','14 video','32 video','B'),
(13,'Hitung Informatika','Rani','Sekolah kita punya 6 lab komputer, tiap lab 30 komputer.','Jumlah komputer seluruhnya adalah ...','pg','36','180','90','200','B'),
(14,'Hitung Informatika','Dodi','Ingat, 1 GB = 1.000 MB di latihan ini.','Maka 3 GB sama dengan ... MB','pg','300 MB','3.000 MB','30.000 MB','1.000 MB','B');
