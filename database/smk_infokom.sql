CREATE DATABASE IF NOT EXISTS smk_infokom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smk_infokom;

CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS berita (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(200) NOT NULL,
  ringkasan TEXT NOT NULL,
  tanggal DATE NOT NULL
);
CREATE TABLE IF NOT EXISTS pengumuman (
  id INT AUTO_INCREMENT PRIMARY KEY,
  isi VARCHAR(300) NOT NULL,
  tanggal DATE NOT NULL
);
CREATE TABLE IF NOT EXISTS agenda (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATE NOT NULL,
  kegiatan VARCHAR(200) NOT NULL,
  tempat VARCHAR(120) NOT NULL
);
CREATE TABLE IF NOT EXISTS prestasi (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tahun YEAR NOT NULL,
  prestasi VARCHAR(200) NOT NULL,
  tingkat VARCHAR(40) NOT NULL
);
CREATE TABLE IF NOT EXISTS lowongan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  perusahaan VARCHAR(120) NOT NULL,
  posisi VARCHAR(120) NOT NULL,
  kualifikasi TEXT NULL,
  batas DATE NOT NULL
);
CREATE TABLE IF NOT EXISTS jadwal_tes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATE NOT NULL,
  perusahaan VARCHAR(120) NOT NULL,
  tahap VARCHAR(80) NOT NULL
);
CREATE TABLE IF NOT EXISTS mitra (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  bidang_usaha VARCHAR(160) NULL,
  jenis_kerjasama VARCHAR(160) NULL,
  deskripsi TEXT NULL
);
CREATE TABLE IF NOT EXISTS ppdb_pendaftar (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(120) NOT NULL,
  asal_sekolah VARCHAR(120) NULL,
  hp VARCHAR(20) NOT NULL,
  jurusan ENUM('TKJ','RPL','DKV','PSPT') NOT NULL,
  status ENUM('baru','diproses','diterima','ditolak') NOT NULL DEFAULT 'baru',
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS ppdb_dokumen (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(180) NOT NULL,
  deskripsi TEXT NOT NULL,
  nama_file VARCHAR(100) NOT NULL,
  nama_asli VARCHAR(255) NOT NULL DEFAULT '',
  ukuran INT UNSIGNED NOT NULL,
  diunggah_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS site_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  page_key VARCHAR(40) NOT NULL,
  slot_key VARCHAR(40) NOT NULL,
  judul VARCHAR(160) NOT NULL,
  alt_text VARCHAR(200) NOT NULL,
  nama_file VARCHAR(100) NOT NULL,
  mime_type VARCHAR(30) NOT NULL,
  ukuran INT UNSIGNED NOT NULL,
  diunggah_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY page_slot (page_key, slot_key)
);
CREATE TABLE IF NOT EXISTS school_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category ENUM('guru','staff','kepala') NOT NULL,
  nama VARCHAR(160) NOT NULL,
  jabatan VARCHAR(160) NOT NULL,
  informasi TEXT NOT NULL,
  urutan INT NOT NULL DEFAULT 0,
  dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY category_order (category, urutan)
);
CREATE TABLE IF NOT EXISTS program_keahlian (
  kode VARCHAR(12) PRIMARY KEY,
  nama VARCHAR(180) NOT NULL,
  deskripsi TEXT NOT NULL,
  kompetensi TEXT NOT NULL,
  fasilitas TEXT NOT NULL,
  kegiatan TEXT NOT NULL,
  proyek TEXT NOT NULL,
  karier TEXT NOT NULL,
  diperbarui_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS school_settings (
  setting_key VARCHAR(80) PRIMARY KEY,
  setting_value TEXT NOT NULL
);

INSERT INTO berita (judul, ringkasan, tanggal) VALUES
('Siswa RPL juarai LKS tingkat provinsi','Tim Web Technologies SMK Infokom meraih juara pertama setelah menyelesaikan tantangan pembuatan aplikasi dalam waktu dua hari.','2026-10-12'),
('Kunjungan industri ke pusat data','Siswa TKJ melihat langsung cara kerja pusat data dan berdiskusi dengan para insinyur jaringan.','2026-10-03'),
('Workshop UI/UX bersama mitra industri','Siswa DKV belajar menyusun alur pengguna dan prototipe aplikasi.','2026-09-25');
INSERT INTO pengumuman (isi, tanggal) VALUES
('Pengumpulan laporan PKL paling lambat 30 Oktober.','2026-10-20'),
('Jadwal Penilaian Tengah Semester sudah tersedia di menu Akademik.','2026-10-18'),
('Pendaftaran beasiswa prestasi dibuka sampai 1 November.','2026-10-15');
INSERT INTO agenda (tanggal, kegiatan, tempat) VALUES
('2026-10-27','Rapat orang tua kelas XII','Aula'),
('2026-11-02','Lomba Kebersihan Kelas','Lingkungan sekolah'),
('2026-11-10','Upacara Hari Pahlawan','Lapangan'),
('2026-11-21','Job Fair SMK Infokom','Aula dan halaman');
INSERT INTO prestasi (tahun, prestasi, tingkat) VALUES
(2026,'Juara 1 LKS Web Technologies','Provinsi'),
(2026,'Juara 2 Lomba Desain Poster Digital','Kota'),
(2025,'Juara 1 Kompetisi Robotik Pelajar','Nasional'),
(2025,'Juara 3 LKS IT Network Systems','Provinsi');
INSERT INTO lowongan (perusahaan, posisi, batas) VALUES
('PT Nusa Teknologi','Teknisi Jaringan','2026-10-30'),
('PT Karya Digital','Junior Web Developer','2026-11-05'),
('CV Kreasi Visual','Desainer Grafis','2026-11-12');
INSERT INTO jadwal_tes (tanggal, perusahaan, tahap) VALUES
('2026-11-04','PT Nusa Teknologi','Tes teknis'),
('2026-11-09','PT Karya Digital','Wawancara'),
('2026-11-16','CV Kreasi Visual','Review portofolio');
INSERT INTO mitra (nama) VALUES
('PT Nusa Teknologi'),('PT Karya Digital'),('CV Kreasi Visual'),('PT Jaringan Mandiri'),('Universitas Contoh'),('Politeknik Contoh');
