-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 21, 2026 at 09:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smk_infokom`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'almauri', '$2y$10$zv7RwFhDtSFaMC6h7fFd4efPafBIYJ0UzGhCcVPMB5.gu53l09Y8m');

-- --------------------------------------------------------

--
-- Table structure for table `agenda`
--

CREATE TABLE `agenda` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `kegiatan` varchar(200) NOT NULL,
  `tempat` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `agenda`
--

INSERT INTO `agenda` (`id`, `tanggal`, `kegiatan`, `tempat`) VALUES
(3, '2026-11-10', 'Upacara Hari Pahlawan', 'Lapangan'),
(5, '2026-10-27', 'Rapat orang tua kelas XII', 'Aula'),
(10, '2026-10-12', 'Ulangan Tengah Semester', 'Lab Komputer'),
(12, '2026-08-17', 'U[pacara Kemerdekaan Indonesia', 'Lapangan');

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int(11) NOT NULL,
  `judul` varchar(200) NOT NULL,
  `ringkasan` text NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `berita`
--

INSERT INTO `berita` (`id`, `judul`, `ringkasan`, `tanggal`) VALUES
(1, 'Tim futsal Smk Infokom Juarai tingkat smk', 'Juara Futsal adalah prestasi yang diraih oleh tim futsal setelah berhasil memenangkan pertandingan atau turnamen. Prestasi ini menunjukkan kerja sama tim, sportivitas, disiplin, dan semangat pantang menyerah.', '2026-04-17'),
(5, 'Tim futsal Smk Infokom Juarai Tingkat Kota Bogor', 'Juara Futsal adalah prestasi yang diraih oleh tim futsal setelah berhasil memenangkan pertandingan atau turnamen. Prestasi ini menunjukkan kerja sama tim, sportivitas, disiplin, dan semangat pantang menyerah.', '2025-09-19'),
(7, 'Tim E-Sport Smk Infokom Juarai Tingkat Kota', 'Meraih Juara 2 dalam kompetisi esport antarkota.', '2026-10-21');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_tes`
--

CREATE TABLE `jadwal_tes` (
  `id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `perusahaan` varchar(120) NOT NULL,
  `tahap` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jadwal_tes`
--

INSERT INTO `jadwal_tes` (`id`, `tanggal`, `perusahaan`, `tahap`) VALUES
(1, '2026-11-04', 'PT Nusa Teknologi', 'Tes teknis'),
(2, '2026-11-09', 'PT Karya Digital', 'Wawancara'),
(3, '2026-11-16', 'CV Kreasi Visual', 'Review portofolio'),
(4, '2026-11-04', 'PT Nusa Teknologi', 'Tes teknis'),
(5, '2026-11-09', 'PT Karya Digital', 'Wawancara'),
(6, '2026-11-16', 'CV Kreasi Visual', 'Review portofolio');

-- --------------------------------------------------------

--
-- Table structure for table `lowongan`
--

CREATE TABLE `lowongan` (
  `id` int(11) NOT NULL,
  `perusahaan` varchar(120) NOT NULL,
  `posisi` varchar(120) NOT NULL,
  `kualifikasi` text DEFAULT NULL,
  `batas` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lowongan`
--

INSERT INTO `lowongan` (`id`, `perusahaan`, `posisi`, `kualifikasi`, `batas`) VALUES
(1, 'PT Nusa Teknologi', 'Teknisi Jaringan', NULL, '2026-10-30'),
(2, 'PT Karya Digital', 'Junior Web Developer', NULL, '2026-11-05'),
(3, 'CV Kreasi Visual', 'Desainer Grafis', NULL, '2026-11-12'),
(4, 'PT Nusa Teknologi', 'Teknisi Jaringan', NULL, '2026-10-30'),
(5, 'PT Karya Digital', 'Junior Web Developer', NULL, '2026-11-05'),
(6, 'CV Kreasi Visual', 'Desainer Grafis', NULL, '2026-11-12');

-- --------------------------------------------------------

--
-- Table structure for table `mitra`
--

CREATE TABLE `mitra` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `bidang_usaha` varchar(160) DEFAULT NULL,
  `jenis_kerjasama` varchar(160) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mitra`
--

INSERT INTO `mitra` (`id`, `nama`, `bidang_usaha`, `jenis_kerjasama`, `deskripsi`) VALUES
(1, 'Bank Bsi', 'Perbankan dan layanan keuangan syariah.', 'Kemitraan layanan perbankan dan edukasi keuangan.', 'Kerja sama dengan BSI untuk mengenalkan layanan perbankan syariah serta meningkatkan literasi keuangan bagi siswa.'),
(2, 'By.U', 'Telekomunikasi dan layanan internet.', 'Kemitraan layanan komunikasi dan promosi.', 'Kemitraan layanan komunikasi dan promosi.'),
(5, 'Universitas Djuanda', 'Pendidikan tinggi.', 'Kemitraan pendidikan dan pengembangan akademik.', 'Kerja sama dengan Universitas Djuanda untuk memberikan informasi tentang perguruan tinggi, melanjutkan pendidikan, serta mendukung pengembangan potensi dan prestasi siswa.'),
(7, 'Bank Bjb', 'Perbankan dan layanan keuangan.', 'Kemitraan layanan perbankan dan edukasi keuangan.', 'Kerja sama dengan Bank BJB untuk mengenalkan layanan perbankan serta meningkatkan literasi keuangan bagi siswa.'),
(8, 'Bonet', 'Telekomunikasi dan layanan internet.', 'Kemitraan layanan internet.', 'Kerja sama dalam menyediakan akses internet untuk mendukung kegiatan pembelajaran dan kebutuhan digital siswa.'),
(11, 'Universitas Ibn Khaldun', 'Pendidikan tinggi.', 'Kemitraan pendidikan dan pengembangan akademik.', 'Kerja sama dengan Universitas Ibn Khaldun untuk memberikan informasi tentang perguruan tinggi, peluang melanjutkan pendidikan, dan pengembangan potensi siswa.'),
(13, 'Bank Mandiri', 'Perbankan dan layanan keuangan.', 'Kemitraan layanan perbankan dan edukasi keuangan.', 'Kerja sama dengan Bank Mandiri untuk mengenalkan layanan perbankan dan meningkatkan pengetahuan siswa tentang pengelolaan keuangan.'),
(14, 'Citraweb Solusi Teknologi', 'Teknologi informasi dan layanan digital.', 'Kemitraan teknologi dan digitalisasi.', 'Kerja sama dalam mendukung pemanfaatan teknologi digital untuk menunjang kegiatan pembelajaran dan pengembangan keterampilan siswa.'),
(15, 'Msv Studio', 'Multimedia, desain, dan produksi konten kreatif.', 'Kemitraan industri kreatif dan pengembangan keterampilan.', 'Kerja sama dalam mendukung pembelajaran multimedia serta meningkatkan kreativitas dan keterampilan siswa di bidang desain dan produksi konten.'),
(16, 'Jawa Pos', 'Media massa dan jurnalistik.', 'Kemitraan media dan publikasi.', 'Kerja sama dengan Jawa Pos untuk mendukung publikasi kegiatan sekolah serta menambah wawasan siswa tentang dunia jurnalistik dan media.'),
(17, 'Universitas Pakuan', 'Pendidikan tinggi.', 'Kemitraan pendidikan dan pengembangan akademik.', 'Kerja sama dengan Universitas Pakuan untuk memberikan informasi tentang perguruan tinggi, peluang melanjutkan pendidikan, dan pengembangan potensi siswa.'),
(18, 'Institut Pertanian Bogor', 'Pendidikan tinggi dan penelitian.', 'Kemitraan pendidikan dan pengembangan akademik.', 'Kerja sama dengan IPB University untuk memberikan informasi tentang perguruan tinggi, peluang melanjutkan pendidikan, serta pengembangan wawasan dan prestasi siswa.'),
(19, 'Politeknik Negeri Jakarta', 'Pendidikan tinggi vokasi dan teknologi.', 'Kemitraan pendidikan dan pengembangan akademik.', 'Kerja sama dengan Politeknik Negeri Jakarta untuk memberikan informasi tentang pendidikan vokasi, peluang melanjutkan studi, dan pengembangan keterampilan siswa sesuai kebutuhan industri.');

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int(11) NOT NULL,
  `isi` varchar(300) NOT NULL,
  `tanggal` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `isi`, `tanggal`) VALUES
(2, 'Jadwal Tes Kemampuan Akademik 26-29 Oktober 2026', '2026-10-18'),
(7, 'Gladi Bersih Tes Kemampuan Akademik 5-8 Oktober 2026', '2026-10-01'),
(8, 'Pemberangkatan PKL Siswa/i Kelas 12 Tgl 9 November 2026', '2026-10-20');

-- --------------------------------------------------------

--
-- Table structure for table `ppdb_dokumen`
--

CREATE TABLE `ppdb_dokumen` (
  `id` int(11) NOT NULL,
  `judul` varchar(180) NOT NULL,
  `deskripsi` text NOT NULL,
  `nama_file` varchar(100) NOT NULL,
  `nama_asli` varchar(255) NOT NULL DEFAULT '',
  `ukuran` int(10) UNSIGNED NOT NULL,
  `diunggah_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ppdb_dokumen`
--

INSERT INTO `ppdb_dokumen` (`id`, `judul`, `deskripsi`, `nama_file`, `nama_asli`, `ukuran`, `diunggah_pada`) VALUES
(1, 'PPDB SMKS INFOKOM BOGOR', '-', 'f20edc45a3f1ed2f1b8e63991f024a7e.pdf', 'Panduan-PPDB-2027-2028.pdf', 62644, '2026-10-21 03:52:34');

-- --------------------------------------------------------

--
-- Table structure for table `ppdb_pendaftar`
--

CREATE TABLE `ppdb_pendaftar` (
  `id` int(11) NOT NULL,
  `nama` varchar(120) NOT NULL,
  `asal_sekolah` varchar(120) DEFAULT NULL,
  `hp` varchar(20) NOT NULL,
  `jurusan` enum('TKJ','RPL','DKV','PSPT') NOT NULL,
  `status` enum('baru','diproses','diterima','ditolak') NOT NULL DEFAULT 'baru',
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prestasi`
--

CREATE TABLE `prestasi` (
  `id` int(11) NOT NULL,
  `tahun` year(4) NOT NULL,
  `prestasi` varchar(200) NOT NULL,
  `tingkat` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prestasi`
--

INSERT INTO `prestasi` (`id`, `tahun`, `prestasi`, `tingkat`) VALUES
(9, '2024', 'Juara 1 Futsal PSSI CUP', 'Kota'),
(10, '2026', 'Juara 3 Futsal Bina Niaga', 'Kota'),
(11, '2026', 'Juara 3 Futsal Sb Futsal Cup', 'Kota'),
(12, '2026', 'Juara 1 E-Sport Ibik', 'Kota'),
(13, '2026', 'Juara 2 E-Sport', 'Kota'),
(14, '2026', 'Juara 3 Poster', 'Kota');

-- --------------------------------------------------------

--
-- Table structure for table `program_keahlian`
--

CREATE TABLE `program_keahlian` (
  `kode` varchar(12) NOT NULL,
  `nama` varchar(180) NOT NULL,
  `deskripsi` text NOT NULL,
  `kompetensi` text NOT NULL,
  `fasilitas` text NOT NULL,
  `kegiatan` text NOT NULL,
  `proyek` text NOT NULL,
  `karier` text NOT NULL,
  `diperbarui_pada` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `program_keahlian`
--

INSERT INTO `program_keahlian` (`kode`, `nama`, `deskripsi`, `kompetensi`, `fasilitas`, `kegiatan`, `proyek`, `karier`, `diperbarui_pada`) VALUES
('DKV', 'Desain Komunikasi Visual', 'Program pembelajaran komunikasi visual, desain grafis, dan produksi konten kreatif. Isi halaman ini dapat disesuaikan dengan kurikulum sekolah.', 'Dasar desain dan ilustrasi|Tipografi dan tata letak|Fotografi dan videografi|Desain media digital|Produksi karya komunikasi visual', 'Komputer desain|Perangkat fotografi dan video|Perangkat lunak desain dan penyuntingan', 'Proyek identitas visual|Produksi foto dan video|Pameran atau presentasi karya', 'Identitas visual|Poster dan materi kampanye|Konten media sosial|Video pendek', 'Desainer grafis pemula|Ilustrator pemula|Fotografer atau editor pemula|Kreator konten', '2026-10-20 06:15:21'),
('PSPT', 'Produksi dan Siaran Program Televisi', 'Program pembelajaran perencanaan, produksi, dan penyiaran program televisi dan konten audiovisual. Isi halaman ini dapat disesuaikan dengan kurikulum serta sarana sekolah.', 'Perencanaan program dan naskah|Teknik kamera dan tata cahaya|Tata suara|Penyuntingan video|Produksi dan penyiaran audiovisual', 'Kamera dan perlengkapan produksi|Perangkat tata cahaya dan audio|Komputer penyuntingan|Ruang produksi atau studio sesuai ketersediaan sekolah', 'Produksi program audiovisual|Latihan pengambilan gambar|Penyuntingan dan publikasi karya', 'Program berita sekolah|Film pendek|Program bincang-bincang|Konten video edukasi', 'Asisten produksi|Juru kamera pemula|Editor video pemula|Asisten penyiaran', '2026-10-20 06:15:21'),
('RPL', 'Rekayasa Perangkat Lunak', 'Program pembelajaran analisis, perancangan, pembuatan, dan pengujian perangkat lunak. Isi halaman ini dapat disesuaikan dengan kurikulum sekolah.', 'Algoritma dan pemrograman|Pengembangan aplikasi web|Pengembangan aplikasi mobile|Basis data dan pengujian perangkat lunak|Kolaborasi dan dokumentasi pengembangan', 'Laboratorium komputer|Perangkat pengembangan perangkat lunak|Lingkungan praktik basis data', 'Proyek aplikasi berbasis kebutuhan|Kolaborasi pengembangan perangkat lunak|Praktik dan pengujian aplikasi', 'Aplikasi web untuk kebutuhan nyata|Aplikasi pengelolaan data|Prototipe aplikasi mobile', 'Web developer pemula|Programmer aplikasi pemula|Software tester pemula|Dukungan teknis aplikasi', '2026-10-20 06:15:21'),
('TKJ', 'Teknik Komputer dan Jaringan', 'Program pembelajaran teknologi komputer dan jaringan. Isi halaman ini dapat disesuaikan dengan kurikulum serta fasilitas sekolah.', 'Perakitan dan pemeliharaan komputer|Konfigurasi jaringan komputer|Administrasi server dan layanan jaringan|Pemecahan masalah dan keamanan jaringan', 'Perangkat komputer dan jaringan|Perangkat router dan switch|Server dan perangkat praktikum', 'Praktik konfigurasi jaringan|Proyek layanan jaringan|Kunjungan atau praktik industri', 'Rancang bangun jaringan lokal|Dokumentasi instalasi dan konfigurasi jaringan|Layanan server untuk kebutuhan praktik', 'Teknisi jaringan|Teknisi komputer|Administrator jaringan pemula|Dukungan teknis', '2026-10-20 06:15:21');

-- --------------------------------------------------------

--
-- Table structure for table `school_profiles`
--

CREATE TABLE `school_profiles` (
  `id` int(11) NOT NULL,
  `category` enum('guru','staff','kepala') NOT NULL,
  `nama` varchar(160) NOT NULL,
  `jabatan` varchar(160) NOT NULL,
  `informasi` text NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `school_profiles`
--

INSERT INTO `school_profiles` (`id`, `category`, `nama`, `jabatan`, `informasi`, `urutan`, `dibuat_pada`) VALUES
(1, 'guru', 'Mila Yaelasari, M.Pd.', 'Wakil Kepala Sekolah Bidang Kurikulum, dan Guru Matematika', '', 1, '2026-10-20 06:26:03'),
(2, 'guru', '-  Dea Rijalul Fikri, S.Kom.,Gr.', 'Wakil Kepala Sekolah Bidang Kesiswaan, dan Guru Produktif DKV', '', 2, '2026-10-20 06:30:51'),
(3, 'guru', 'R. Radiani Srirestuti Dewi, SS.,Gr.', 'Wakil Kepala Sekolah Bidang Hubungan Industri dan Masyarakat, dan Guru Bahasa Jepang', '', 3, '2026-10-20 06:32:58'),
(4, 'guru', 'Heny Handayani, S.Pd.', '⁠Wakil Kepala Sekolah Bidang Pengembangan Sumber Daya Manusia, dan Guru Seni Budaya', '', 4, '2026-10-20 06:35:42'),
(5, 'guru', '- Agus Tiyono, M.Pd.', 'Wakil Kepala Sekolah Bidang Sarana dan Prasarana, dan Guru Ppkn & Sejarah', '', 5, '2026-10-20 06:37:42'),
(6, 'guru', 'Cahyadi Rozi, ST.', 'Kepala Tata Usaha, dan Guru Informatika', '', 6, '2026-10-20 06:39:44'),
(7, 'guru', 'Drs. Puji Marhaen Waluyo', 'Kepala Perpustakaan, dan Guru Ppkn', '', 7, '2026-10-20 06:43:15'),
(8, 'guru', '-  Putri Riandini, S.Hut., M.Pd.,Gr.', '⁠Kepala Bendahara, dan Guru Matematika', '', 8, '2026-10-20 06:45:16'),
(9, 'guru', 'Aditya Nugraha, ST.', 'Kepala Program Keahlian Jurusan TKJ, Guru Produktif TKJ', '', 9, '2026-10-20 06:47:35'),
(10, 'guru', 'Ridwan Hala, S.Kom.,Gr.', 'Kepala Program Keahlian RPL, dan Guru Produktif RPL & TKJ', '', 10, '2026-10-20 06:50:25'),
(11, 'guru', 'Muhammad Ibnu Arif, S.IKom.', '⁠Kepala Program Keahlian DKV, dan Guru Produktif DKV', '', 11, '2026-10-20 06:53:26'),
(12, 'guru', '⁠Erli Suherli, A.Md.Kom.', '-  - ⁠Kepala Program Keahlian PSPT, dan Guru Produktif PSPT & DKV', '', 12, '2026-10-20 06:56:07'),
(13, 'guru', 'Ahmad Hotib, S.Pd.I', 'Pembina OSIS, dan Guru Agama', '', 13, '2026-10-20 06:58:16'),
(14, 'guru', 'Abdul Azis, M.Pd.', 'Pembina OSIS, dan Guru Agama', '', 14, '2026-10-20 07:00:05'),
(15, 'guru', 'Vera Yuni Astuti, SP., M.IKom.,Gr.', '⁠Staf Kurikulum, dan Guru Pkwu', '', 15, '2026-10-20 07:02:27'),
(16, 'guru', 'M. Fauzan Arifin, S.IKom.,Gr.', 'Staf Kurikulum, dan Guru Produktif DKV', '', 16, '2026-10-20 07:08:32'),
(17, 'guru', 'Dra. Popon Puspitasari', 'Guru Bimbingan & Konseling, dan Guru Agama', '', 17, '2026-10-20 07:10:51'),
(18, 'guru', 'Richo Santana, S.Kom.', 'Guru Bimbingan & Konseling, dan Guru Produktif RPL & TKJ', '', 18, '2026-10-20 07:14:14'),
(20, 'guru', 'Dian Hardianti, S.Kom.,Gr.', 'Guru Bimbingan & Konseling, dan Guru Produktif RPL', '', 20, '2026-10-20 07:16:25'),
(21, 'guru', 'Yeni Yuliawati, S.Pd.,Gr.', 'Staf Teller (Bendahara), dan Guru Bahasa Indonesia', '', 21, '2026-10-20 07:18:34'),
(22, 'guru', 'Eva Farida Rahayu, S.Pd.,Gr.', '⁠Staf Teller (Bendahara), dan Guru Bahsa Indonesia', '', 22, '2026-10-20 07:22:47'),
(23, 'guru', 'Jayadi, S.Pd.,Gr.', 'Staf Tata Usaha, dan Guru Pjok', '', 23, '2026-10-20 07:24:11'),
(24, 'guru', 'Reza Prafitriansyah, S.Kom.,Gr.', '⁠Staf Perpustakaan, Guru Produktif DKV', '', 24, '2026-10-20 07:26:35'),
(25, 'guru', 'Dra. Diana Octaria', 'Guru Pipas (Ipa & Ips)', '', 25, '2026-10-20 07:28:09'),
(26, 'guru', 'Nabila Ahmadia Pratama, S.Pd.', 'Guru Bahasa Indonesia', '', 26, '2026-10-20 07:30:14'),
(27, 'guru', 'Erwin Hasiholan Gultom, S.Kom.', 'Guru Produktif RPL', '', 27, '2026-10-20 07:32:10'),
(28, 'guru', 'Dara Purwanita, S.Pd.,Gr.', 'Guru Bahasa Inggris', '', 28, '2026-10-20 07:33:42'),
(29, 'guru', 'Widyatama Nuranisa, SM.', 'Guru Pkwu', '', 29, '2026-10-20 07:35:15'),
(30, 'staff', 'Vera Yuni Astuti, SP., M.IKom.,Gr.', 'Staf Kurikulum', '', 1, '2026-10-20 07:37:50'),
(31, 'staff', 'M. Fauzan Arifin, S.IKom.,Gr.', 'Staff Kurikulum', '', 2, '2026-10-20 07:40:03'),
(34, 'staff', 'Yeni Yuliawati, S.Pd.,Gr.', 'Staf Teller (Bendahara)', '', 3, '2026-10-20 07:41:59'),
(35, 'staff', 'Eva Farida Rahayu, S.Pd.,Gr.', 'Staff Teller (Bendahara)', '', 4, '2026-10-20 07:43:50'),
(37, 'staff', 'Rita Widyastuti', 'Staff Teller (Bendahara)', '', 5, '2026-10-20 07:45:32'),
(38, 'staff', 'Jayadi, S.Pd.,Gr.', 'Staf Tata Usaha', '', 6, '2026-10-20 07:46:39'),
(39, 'staff', 'Yadi Setiadi, S.Sos.,Gr.', 'Staf Tata Usaha', '', 7, '2026-10-20 07:48:38'),
(40, 'staff', 'Siti Nuraeni', 'Staf Tata Usaha', '', 8, '2026-10-20 07:49:41'),
(41, 'staff', 'Reza Prafitriansyah, S.Kom.,Gr.', '⁠Staf Perpustakaan', '', 9, '2026-10-20 07:51:25'),
(42, 'kepala', 'Ir. Hj. Liliek Rahmaningsih, M.M.Pd.', 'Kepala Sekolah', '', 1, '2026-10-20 07:53:44');

-- --------------------------------------------------------

--
-- Table structure for table `school_settings`
--

CREATE TABLE `school_settings` (
  `setting_key` varchar(80) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `school_settings`
--

INSERT INTO `school_settings` (`setting_key`, `setting_value`) VALUES
('alamat', 'Jl. Letdjen Ibrahim Adjie No. 178 Sindang Barang, Kec. Bogor Barat, Kota Bogor'),
('email', 'smk.infokom@gmail.com'),
('kepala_jabatan', 'Kepala Sekolah'),
('kepala_nama', 'Ir. Hj. Liliek Rahmaningsih, M.M.Pd.'),
('kepala_sambutan', 'Assalamu’alaikum warahmatullahi wabarakatuh.\r\nSelamat datang di website sekolah kami. Semoga website ini menjadi sarana informasi, komunikasi, dan inspirasi bagi seluruh warga sekolah serta masyarakat. Mari bersama-sama mewujudkan pendidikan yang berkualitas, berkarakter, dan berprestasi.\r\nWassalamu’alaikum warahmatullahi wabarakatuh.'),
('nama', 'SMKS INFOKOM KOTA BOGOR'),
('wa', '(0251) 8348108'),
('wa_tampil', '(0251) 8348108');

-- --------------------------------------------------------

--
-- Table structure for table `site_images`
--

CREATE TABLE `site_images` (
  `id` int(11) NOT NULL,
  `page_key` varchar(40) NOT NULL,
  `slot_key` varchar(40) NOT NULL,
  `judul` varchar(160) NOT NULL,
  `alt_text` varchar(200) NOT NULL,
  `nama_file` varchar(100) NOT NULL,
  `mime_type` varchar(30) NOT NULL,
  `ukuran` int(10) UNSIGNED NOT NULL,
  `diunggah_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_images`
--

INSERT INTO `site_images` (`id`, `page_key`, `slot_key`, `judul`, `alt_text`, `nama_file`, `mime_type`, `ukuran`, `diunggah_pada`) VALUES
(1, 'guru', 'person-1', 'Guru Matematika', 'Guru matematika adalah pendidik yang mengajarkan konsep dan keterampilan matematika kepada siswa.', 'ebf3265ec41c0a20bec0033055057e6f.png', 'image/png', 46973, '2026-10-20 06:29:17'),
(2, 'guru', 'person-2', 'Guru Produktif DKV', 'Guru produktif DKV adalah pendidik yang mengajarkan keterampilan desain komunikasi visual, seperti desain grafis, ilustrasi, dan multimedia.', 'c7968dfd033b032ea55bea00ab72be25.png', 'image/png', 58938, '2026-10-20 06:31:57'),
(3, 'guru', 'person-3', 'Guru Bahasa Jepang', 'Guru Bahasa Jepang adalah pendidik yang mengajarkan keterampilan berbahasa Jepang, meliputi berbicara, membaca, menulis, dan mendengarkan.', '79dff09776f2e9665b49522e37137280.png', 'image/png', 48215, '2026-10-20 06:34:12'),
(4, 'guru', 'person-4', 'Guru Seni Budaya', 'Guru Seni Budaya adalah pendidik yang mengajarkan seni, kreativitas, dan apresiasi budaya kepada siswa.', 'c4c1d0dc331a81de388626ba3149065a.png', 'image/png', 72958, '2026-10-20 06:36:41'),
(5, 'guru', 'person-5', 'Guru Ppkn & Sejarah', 'Guru PPKn dan Sejarah adalah pendidik yang mengajarkan nilai-nilai Pancasila, kewarganegaraan, serta peristiwa sejarah untuk membentuk karakter dan wawasan kebangsaan siswa.', '9f7047cb710148fea87a989c0a2bf3d5.png', 'image/png', 55871, '2026-10-20 06:38:44'),
(6, 'guru', 'person-6', 'Guru Informatika', 'Guru Informatika adalah pendidik yang mengajarkan teknologi informasi, pemrograman, dan keterampilan digital kepada siswa.', '7daad9f7d15b514125c254df18484d77.png', 'image/png', 94815, '2026-10-20 06:40:43'),
(7, 'guru', 'person-7', 'Guru Ppkn', 'Guru PPKn adalah pendidik yang mengajarkan nilai-nilai Pancasila, hak dan kewajiban warga negara, serta sikap cinta tanah air.', '13624b49066412292600d5afdfdecf44.png', 'image/png', 90512, '2026-10-20 06:44:24'),
(9, 'guru', 'person-8', 'Guru Matematika', 'Guru matematika adalah pendidik yang mengajarkan konsep dan keterampilan matematika kepada siswa.', '5974110cb473c831fbc8bd128af5ed35.png', 'image/png', 67957, '2026-10-20 06:46:27'),
(10, 'guru', 'person-9', 'Guru Produktif TKJ', 'Guru produktif TKJ adalah pendidik yang mengajarkan keterampilan jaringan komputer, perakitan komputer, dan administrasi sistem jaringan.', 'b398cf5972a38882676cfb493dc72a7d.png', 'image/png', 78185, '2026-10-20 06:48:46'),
(11, 'guru', 'person-10', 'Guru Produktif RPL & TKJ', 'Guru produktif RPL dan TKJ adalah pendidik yang mengajarkan pemrograman, pengembangan perangkat lunak, perakitan komputer, dan jaringan komputer.', 'c0d47026a57337bb023a253400df5d1e.png', 'image/png', 82148, '2026-10-20 06:51:12'),
(12, 'guru', 'person-11', 'Guru Produktif DKV', 'Guru produktif DKV adalah pendidik yang mengajarkan keterampilan desain komunikasi visual, seperti desain grafis, ilustrasi, dan multimedia.', '7e196fae237436eae8e52483529fb784.png', 'image/png', 83351, '2026-10-20 06:54:28'),
(13, 'guru', 'person-12', 'Guru Produktif PSPT & DKV', 'Guru produktif PSPT dan DKV adalah pendidik yang mengajarkan teknik produksi siaran televisi serta keterampilan desain komunikasi visual dan desain grafis.', 'c5b7913b0e07ca1506763dcd0703435b.png', 'image/png', 77218, '2026-10-20 06:57:03'),
(14, 'guru', 'person-13', 'Guru Agama', 'Guru Agama adalah pendidik yang mengajarkan nilai-nilai keagamaan, moral, dan budi pekerti untuk membentuk karakter siswa yang berakhlak mulia.', 'ae022133382d420e5db44dffeb9eabba.png', 'image/png', 44878, '2026-10-20 06:59:15'),
(15, 'guru', 'person-14', 'Guru Agama', 'Guru Agama adalah pendidik yang mengajarkan nilai-nilai keagamaan, moral, dan budi pekerti untuk membentuk karakter siswa yang berakhlak mulia.', '8c2b6953590e06f5aa1047290e7ec38a.png', 'image/png', 39500, '2026-10-20 07:01:00'),
(16, 'guru', 'person-15', 'Guru Pkwu', 'Guru PKWU adalah pendidik yang mengajarkan keterampilan prakarya, kewirausahaan, dan kreativitas dalam menciptakan produk bernilai jual.', 'ff1ae760bc92eb63639909cb167524a0.png', 'image/png', 47650, '2026-10-20 07:05:24'),
(17, 'guru', 'person-16', 'Guru Produktif DKV', 'Guru produktif DKV adalah pendidik yang mengajarkan keterampilan desain komunikasi visual, seperti desain grafis, ilustrasi, dan multimedia.', '6977b85117965fad94d941bef7db67e4.png', 'image/png', 33535, '2026-10-20 07:09:12'),
(18, 'guru', 'person-17', 'Guru Agama', 'Guru Agama adalah pendidik yang mengajarkan nilai-nilai keagamaan, moral, dan budi pekerti untuk membentuk karakter siswa yang berakhlak mulia.', '1afcc7fc364d6ef9d0ba7f0ecd9fa05b.png', 'image/png', 43334, '2026-10-20 07:11:29'),
(19, 'guru', 'person-18', 'Guru Produktif RPL & TKJ', 'Guru produktif RPL dan TKJ adalah pendidik yang mengajarkan pemrograman, pengembangan perangkat lunak, perakitan komputer, dan jaringan komputer.', '9eb4577129393fb005353748ff2948bb.png', 'image/png', 39474, '2026-10-20 07:15:36'),
(21, 'guru', 'person-20', 'Guru Produktif RPL', 'Guru produktif RPL adalah pendidik yang mengajarkan pemrograman, pengembangan aplikasi, dan pembuatan perangkat lunak.', 'ad69a93a65da8af651c48708b9ff0e06.png', 'image/png', 55991, '2026-10-20 07:19:38'),
(22, 'guru', 'person-21', 'Guru Bahasa Indonesia', 'Guru Bahasa Indonesia adalah pendidik yang mengajarkan keterampilan membaca, menulis, berbicara, dan memahami bahasa Indonesia dengan baik dan benar.', '4facdc88ec358a337e655db3bee917e3.png', 'image/png', 50828, '2026-10-20 07:21:22'),
(23, 'guru', 'person-22', 'Guru Bahasa Indonesia', 'Guru Bahasa Indonesia adalah pendidik yang mengajarkan keterampilan membaca, menulis, berbicara, dan memahami bahasa Indonesia dengan baik dan benar.', 'd4ba5bdc020eb96823bf91c5fedab7e8.png', 'image/png', 58824, '2026-10-20 07:23:13'),
(24, 'guru', 'person-23', 'Guru Pjok', 'Guru PJOK adalah pendidik yang mengajarkan olahraga, kebugaran jasmani, dan pola hidup sehat kepada siswa.', '766e38f4eb18b756a477f4755041ef8a.png', 'image/png', 66386, '2026-10-20 07:25:41'),
(25, 'guru', 'person-24', 'Guru Produktif DKV', 'Guru produktif DKV adalah pendidik yang mengajarkan keterampilan desain komunikasi visual, seperti desain grafis, ilustrasi, dan multimedia.', '6aa6ddfd7743779dfa28b59fc02223c8.png', 'image/png', 73346, '2026-10-20 07:27:14'),
(26, 'guru', 'person-25', 'Guru Pipas (Ipa & Ips)', 'Guru PIPAS adalah pendidik yang mengajarkan ilmu pengetahuan alam dan sosial serta penerapannya dalam kehidupan sehari-hari.', '365f2ef547745402e4ab232dea5eed05.png', 'image/png', 86277, '2026-10-20 07:29:35'),
(27, 'guru', 'person-26', 'Guru Bahasa Indonesia', 'Guru Bahasa Indonesia adalah pendidik yang mengajarkan keterampilan membaca, menulis, berbicara, dan memahami bahasa Indonesia dengan baik dan benar.', '3d5019fe92a2d58698e38f4c46fc77ee.png', 'image/png', 78176, '2026-10-20 07:30:59'),
(28, 'guru', 'person-27', 'Guru Produktif RPL', 'Guru produktif RPL adalah pendidik yang mengajarkan pemrograman, pengembangan aplikasi, dan pembuatan perangkat lunak.', 'bd094ea19ca22bd6a88334d2b8299ad7.png', 'image/png', 87176, '2026-10-20 07:33:10'),
(29, 'guru', 'person-28', 'Guru Bahasa Inggris', 'Guru Bahasa Inggris adalah pendidik yang mengajarkan keterampilan berbicara, membaca, menulis, dan mendengarkan dalam bahasa Inggris.', '209babe7b5a95a220dddef55b4d21503.png', 'image/png', 110564, '2026-10-20 07:34:50'),
(30, 'guru', 'person-29', 'Guru Pkwu', 'Guru PKWU adalah pendidik yang mengajarkan keterampilan prakarya, kewirausahaan, dan kreativitas dalam menciptakan produk bernilai jual.', '33196a15942c4031f0fa704064799952.png', 'image/png', 112615, '2026-10-20 07:35:55'),
(31, 'staff', 'person-30', 'Staff Kurikulum', 'Staf kurikulum adalah tenaga kependidikan yang membantu mengelola jadwal pelajaran, administrasi pembelajaran, dan pelaksanaan kurikulum sekolah.', '5d86009f0735ba04e3cece45afe4cf4a.png', 'image/png', 47650, '2026-10-20 07:38:58'),
(32, 'staff', 'person-31', 'Staff Kurikulum', 'Staf kurikulum adalah tenaga kependidikan yang membantu mengelola jadwal pelajaran, administrasi pembelajaran, dan pelaksanaan kurikulum sekolah.', '5ab7a5b8a6472f4f0bf949e0011ac075.png', 'image/png', 33535, '2026-10-20 07:41:02'),
(33, 'staff', 'person-34', 'Staff Teller', 'Staf teller adalah petugas yang melayani transaksi keuangan, seperti penyetoran, penarikan, dan pembayaran dengan teliti dan ramah.', '6d7d6fbaba945f8328477fd0dd85b1d8.png', 'image/png', 50828, '2026-10-20 07:43:03'),
(34, 'staff', 'person-35', 'Staff Teller', 'Staf teller adalah petugas yang melayani transaksi keuangan, seperti penyetoran, penarikan, dan pembayaran dengan teliti dan ramah.', 'fc1844f66f285399d6314b1b7cb27b36.png', 'image/png', 58824, '2026-10-20 07:44:10'),
(35, 'staff', 'person-37', 'Staff Teller', 'Staf teller adalah petugas yang melayani transaksi keuangan, seperti penyetoran, penarikan, dan pembayaran dengan teliti dan ramah.', '318582c0cbab73a2b212b20e5fde52d1.png', 'image/png', 62905, '2026-10-20 07:46:00'),
(36, 'staff', 'person-38', 'Staff Tata Usaha', 'Staf Tata Usaha adalah tenaga kependidikan yang mengelola administrasi, surat-menyurat, data siswa, dan dokumen sekolah.', '792c76589e409f85d891dc4286efef71.png', 'image/png', 66386, '2026-10-20 07:47:47'),
(37, 'staff', 'person-39', 'Staff Tata Usaha', 'Staf Tata Usaha adalah tenaga kependidikan yang mengelola administrasi, surat-menyurat, data siswa, dan dokumen sekolah.', 'a83c6a22bdb254efd791518c37f47fe6.png', 'image/png', 73094, '2026-10-20 07:49:05'),
(38, 'staff', 'person-40', 'Staff Tata Usaha', 'Staf Tata Usaha adalah tenaga kependidikan yang mengelola administrasi, surat-menyurat, data siswa, dan dokumen sekolah.', 'b1c018c0560d25b71d0790dd0a346f02.png', 'image/png', 60155, '2026-10-20 07:50:49'),
(39, 'staff', 'person-41', 'Staff Perpustakaan', 'Staf perpustakaan adalah petugas yang mengelola buku, melayani peminjaman, dan menjaga kerapian serta ketertiban perpustakaan sekolah.', 'f7d6b83d7fc7ccc3602539ac744b480a.png', 'image/png', 73622, '2026-10-20 07:52:44'),
(40, 'kepala-sekolah', 'person-42', 'Kepala Sekolah', 'Kepala sekolah adalah pemimpin yang mengelola, mengawasi, dan mengarahkan seluruh kegiatan sekolah agar berjalan dengan baik.', '5146760cee9a6a6a3686ba2b09fca43e.png', 'image/png', 341644, '2026-10-20 07:54:39'),
(41, 'kepala-sekolah', 'sambutan', 'Kepala Sekolah', 'Kepala sekolah adalah pemimpin yang mengelola, mengawasi, dan mengarahkan seluruh kegiatan sekolah agar berjalan dengan baik.', '1297bb8a6de2c1555d7514538a450e99.png', 'image/png', 341644, '2026-10-20 07:57:48'),
(42, 'branding', 'school-logo', 'Logo Smk Infokom Kota Bogor', 'SMK Infokom adalah sekolah menengah kejuruan yang berfokus pada bidang teknologi informasi dan komputer untuk membekali siswa dengan keterampilan digital agar siap kerja maupun melanjutkan pendidikan.', 'd0a9b278029de04e627e261854f84e26.png', 'image/png', 1118457, '2026-10-20 08:10:49'),
(43, 'jurusan-tkj', 'banner', 'Teknik Komputer dan Jaringan', 'TKJ (Teknik Komputer dan Jaringan) adalah jurusan yang mempelajari perakitan komputer, instalasi jaringan, konfigurasi server, serta pemeliharaan perangkat komputer dan internet.', '7cc7ac4df5346169872ac4074c289aa8.jpg', 'image/jpeg', 32574, '2026-10-20 08:21:04'),
(44, 'jurusan-rpl', 'banner', 'Rekayasa Perangkat Lunak', 'RPL (Rekayasa Perangkat Lunak) adalah jurusan yang mempelajari pemrograman, pembuatan aplikasi, pengembangan website, dan pengelolaan perangkat lunak.', '0969b46d5597938045a73277c134d678.jpg', 'image/jpeg', 76328, '2026-10-20 08:22:28'),
(45, 'jurusan-dkv', 'banner', 'Desain Komunikasi Visual', 'DKV (Desain Komunikasi Visual) adalah jurusan yang mempelajari desain grafis, ilustrasi, fotografi, animasi, dan pembuatan konten visual.', '338b7691ee2a00702dd22a9496519576.jpg', 'image/jpeg', 36277, '2026-10-20 08:23:27'),
(46, 'jurusan-pspt', 'banner', 'Produksi Siaran dan Program Televisi', 'PSPT (Produksi Siaran dan Program Televisi) adalah jurusan yang mempelajari proses produksi acara televisi, mulai dari pengambilan gambar, penyuntingan video, penulisan naskah, hingga penyiaran.', 'e9a95793fd24c285334dc9fc4bb2d71b.jpg', 'image/jpeg', 55120, '2026-10-20 08:25:26'),
(47, 'informasi', 'news-1', 'Tim Futsal Smk Infokom Juara 1 di event PSSI CUP', 'Juara Futsal adalah prestasi yang diraih oleh tim futsal setelah berhasil memenangkan pertandingan atau turnamen.', '6f2c20a0f006dadee89dee6f3b2c5379.jpg', 'image/jpeg', 131883, '2026-10-20 08:43:24'),
(49, 'informasi', 'news-5', 'Tim Futsal Smk Infokom Juara 1 tingkat Kota Bogor di event PSSI CUP', 'Juara Futsal adalah prestasi yang diraih oleh tim futsal setelah berhasil memenangkan pertandingan atau turnamen.', 'dcc6a61156ed2ef205aab812df86b624.jpg', 'image/jpeg', 131883, '2026-10-20 08:47:53'),
(50, 'kesiswaan', 'lead', 'Siswa Intra Sekolah (OSIS)', 'OSIS adalah organisasi siswa di sekolah yang bertujuan mengembangkan kepemimpinan, kreativitas, dan tanggung jawab siswa.', 'b74a35c67c301bcbbc82f4521b7b5b00.jpg', 'image/jpeg', 184828, '2026-10-21 01:00:14'),
(51, 'kesiswaan', 'activity-3', 'Pramuka', 'Pramuka adalah organisasi pendidikan nonformal yang membentuk karakter, kedisiplinan, kemandirian, dan jiwa kerja sama.', '4f7850b04fe5b4cfe637d45a7649db8f.jpg', 'image/jpeg', 214727, '2026-10-21 01:03:54'),
(52, 'kesiswaan', 'activity-4', 'Paskibra', 'Paskibra adalah organisasi yang melatih kedisiplinan, tanggung jawab, jiwa nasionalisme, dan keterampilan baris-berbaris.', '414214a4e987183159289f69c8fbf0c0.jpg', 'image/jpeg', 225940, '2026-10-21 01:05:13'),
(53, 'kesiswaan', 'activity-6', 'Futsal', 'Futsal adalah kegiatan olahraga yang melatih keterampilan bermain bola, kerja sama tim, kedisiplinan, dan sportivitas.', '633ca8ce9842afb5e9a25ebd6e0f5783.jpg', 'image/jpeg', 175640, '2026-10-21 01:06:22'),
(54, 'kesiswaan', 'activity-2', 'E-Sport', 'Esport adalah kegiatan olahraga elektronik yang mengembangkan keterampilan bermain game, strategi, kerja sama tim, dan sportivitas.', '4eaeab332b09edbeb2ddf7bc360dd315.jpg', 'image/jpeg', 140738, '2026-10-21 01:09:54'),
(55, 'kesiswaan', 'achievement', 'Juara 2 E-Sport Antar Kota', 'Meraih Juara 2 dalam kompetisi esport antarkota.', '8fd8c8948921171e88b9c143914f5501.jpg', 'image/jpeg', 97717, '2026-10-21 01:11:04'),
(56, 'informasi', 'news-7', 'Juara 2 E-Sport', 'Meraih Juara 2 dalam kompetisi esport antarkota.', 'd1e31c0ae1612357358dd3994fcb69c3.jpg', 'image/jpeg', 97717, '2026-10-21 01:13:33'),
(57, 'galeri', 'gallery-1', 'Peraktik Pembelajaran', 'Praktik pembelajaran di sekolah adalah kegiatan belajar secara langsung untuk meningkatkan pengetahuan, keterampilan, dan pengalaman siswa.', 'e4ee666ccc8b6cbf7901054e7343a7a5.jpg', 'image/jpeg', 115479, '2026-10-21 01:29:16'),
(58, 'galeri', 'gallery-2', 'Kegiatan ekstralkulikuler', 'Kegiatan ekstrakurikuler adalah kegiatan di luar jam pelajaran untuk mengembangkan bakat, minat, kreativitas, dan keterampilan siswa.', '1d42d6107a5cbfd912df9a32fcc969b6.jpg', 'image/jpeg', 124779, '2026-10-21 01:30:41'),
(59, 'galeri', 'gallery-3', 'Karya dan prestasi siswa', 'Karya dan prestasi siswa adalah hasil kreativitas, usaha, dan pencapaian siswa dalam bidang akademik maupun nonakademik.', '32cb6f644220de259bb858f50b43f691.jpg', 'image/jpeg', 140738, '2026-10-21 01:31:57'),
(60, 'galeri', 'gallery-4', 'Belajar bersama', 'Belajar bersama adalah kegiatan belajar secara berkelompok untuk saling berbagi pengetahuan, berdiskusi, dan memahami materi pelajaran.', '085d0204d33e70ce0b38bad186c46529.jpg', 'image/jpeg', 123911, '2026-10-21 01:35:24'),
(61, 'galeri', 'gallery-5', 'Kegiatan industri', 'Kegiatan industri adalah kegiatan pembelajaran atau kunjungan ke dunia industri untuk menambah wawasan dan pengalaman kerja siswa.', '8b9b2e80410f8dee80f44d390511d3d7.jpg', 'image/jpeg', 117775, '2026-10-21 01:37:25'),
(62, 'galeri', 'gallery-6', 'Lingkungan belajar', 'Lingkungan belajar adalah suasana dan tempat yang mendukung kegiatan belajar agar siswa merasa nyaman, aman, dan fokus.', '047a2dcb0e5e99eb9fabc56fa057bdfb.jpg', 'image/jpeg', 83388, '2026-10-21 01:40:14'),
(63, 'bkk', 'lead', 'Bursa Kerja Khusus (BKK)', 'BKK (Bursa Kerja Khusus) adalah layanan sekolah yang membantu siswa dan lulusan mendapatkan informasi lowongan kerja serta penyaluran kerja.', '0860a46be40768b42c4e2470b6dec273.jpg', 'image/jpeg', 183181, '2026-10-21 01:43:08'),
(65, 'ppdb', 'lead', 'PPDB SMKS INFOKOM BOGOR', 'PPDB (Penerimaan Peserta Didik Baru) adalah proses penerimaan siswa baru di sekolah melalui pendaftaran dan seleksi sesuai ketentuan yang berlaku.', 'e25e22c49e4496fea6df532980d4ad57.jpg', 'image/jpeg', 212292, '2026-10-21 01:53:43'),
(66, 'kerjasama', 'partner-7', 'Bank Bjb', 'Kerja sama dengan pihak bank untuk mendukung layanan keuangan dan meningkatkan literasi keuangan siswa.', 'b8d75a17e0995656868bb3b4b62449fe.jpg', 'image/jpeg', 3300, '2026-10-21 02:44:10'),
(67, 'kerjasama', 'partner-8', 'Bonet', 'Kerja sama dengan Bonet dalam menyediakan layanan internet untuk mendukung kegiatan pembelajaran dan kebutuhan digital siswa.', '8d4890510b195e2f50183199b17534b7.jpg', 'image/jpeg', 43367, '2026-10-21 02:45:42'),
(68, 'kerjasama', 'partner-1', 'Bank Bsi', 'Kerja sama dengan pihak bank untuk mendukung layanan keuangan dan meningkatkan literasi keuangan siswa.', '2fbcf9bb2212c6444c3e2046894f13f2.jpg', 'image/jpeg', 65984, '2026-10-21 02:46:32'),
(69, 'kerjasama', 'partner-14', 'Citraweb', 'Kerja sama dengan Citraweb dalam mendukung layanan teknologi informasi, jaringan internet, dan pengembangan keterampilan digital siswa.', 'f4dc22121177102d4b6a1cd72df3cdd7.jpg', 'image/jpeg', 6634, '2026-10-21 02:48:50'),
(70, 'kerjasama', 'partner-16', 'Jawa Pos', 'Kerja sama dengan Jawa Pos untuk mendukung publikasi kegiatan sekolah dan menambah wawasan siswa tentang dunia jurnalistik dan media.', '6419a3ebb85da8057d4c8a6b4b0326aa.jpg', 'image/jpeg', 3524, '2026-10-21 02:50:02'),
(71, 'kerjasama', 'partner-13', 'Bank Mandiri', 'Kerja sama dengan pihak bank untuk mendukung layanan keuangan dan meningkatkan literasi keuangan siswa.', 'a4fbc84cd700d323a3a2fd83be235bd3.jpg', 'image/jpeg', 53180, '2026-10-21 02:50:32'),
(72, 'kerjasama', 'partner-15', 'Msv Studio', 'Kerja sama dengan MSV Studio untuk mengembangkan kreativitas dan keterampilan siswa di bidang multimedia, desain, dan produksi konten digital.', '2c028860e2574f924114c4b043671b4d.jpg', 'image/jpeg', 100345, '2026-10-21 02:51:52'),
(73, 'kerjasama', 'partner-18', 'Institut Pertanian Bogor', 'Kerja sama dengan perguruan tinggi untuk memberikan informasi pendidikan lanjutan dan mendukung pengembangan potensi serta prestasi siswa.', 'f47b2875838866ca06ab355a1a052385.jpg', 'image/jpeg', 12400, '2026-10-21 02:53:15'),
(74, 'kerjasama', 'partner-19', 'Politeknik Negeri Jakarta', 'Kerja sama dengan perguruan tinggi untuk memberikan informasi pendidikan lanjutan dan mendukung pengembangan potensi serta prestasi siswa.', '55695d579a59a54cdb648fa3c263d8f9.png', 'image/png', 138614, '2026-10-21 02:53:52'),
(75, 'kerjasama', 'partner-5', 'Universitas Djuanda', 'Kerja sama dengan perguruan tinggi untuk memberikan informasi pendidikan lanjutan dan mendukung pengembangan potensi serta prestasi siswa.', 'f6cc5436ebf250d75fec829de9115c84.jpg', 'image/jpeg', 13951, '2026-10-21 02:54:22'),
(76, 'kerjasama', 'partner-11', 'Universitas Ibnu Khaldun', 'Kerja sama dengan perguruan tinggi untuk memberikan informasi pendidikan lanjutan dan mendukung pengembangan potensi serta prestasi siswa.', 'fa67f04dcf0223bac2f7c5a193a66037.jpg', 'image/jpeg', 12162, '2026-10-21 02:55:02'),
(77, 'kerjasama', 'partner-17', 'Universitas Pakuan', 'Kerja sama dengan perguruan tinggi untuk memberikan informasi pendidikan lanjutan dan mendukung pengembangan potensi serta prestasi siswa.', '3e2c270ff30c1c86e3b3c4660b31cef2.jpg', 'image/jpeg', 13688, '2026-10-21 02:55:40'),
(78, 'kerjasama', 'partner-2', 'By.U', 'Kerja sama by.U adalah kolaborasi dengan penyedia layanan telekomunikasi untuk mendukung kebutuhan komunikasi dan kegiatan siswa di sekolah.', '2cfe6d8ebc8248a31c5ace21ec763823.jpg', 'image/jpeg', 22718, '2026-10-21 02:56:40'),
(79, 'fasilitas', 'facility-1', 'Lab RPL', 'Fasilitas laboratorium yang mendukung praktik pembelajaran dan meningkatkan keterampilan siswa sesuai bidang keahlian.', '990166c6e2e08229e15ddc9c03dff5d5.jpg', 'image/jpeg', 69811, '2026-10-21 03:03:11'),
(82, 'fasilitas', 'facility-3', 'Studio PSPT', 'Studio adalah fasilitas praktik untuk mengembangkan keterampilan siswa dalam bidang produksi siaran, dan pembuatan konten kreatif.', '05872dc13867215513fd32433d4c2d99.jpg', 'image/jpeg', 112578, '2026-10-21 03:05:50'),
(83, 'fasilitas', 'facility-4', 'Perpustakaan', 'Perpustakaan adalah fasilitas belajar yang menyediakan buku dan sumber informasi untuk menambah pengetahuan serta mendukung pembelajaran siswa.', '3cc3ee373d809fcca462a94e29be1904.jpg', 'image/jpeg', 157274, '2026-10-21 03:09:34'),
(85, 'fasilitas', 'facility-5', 'Aula', 'Aula adalah fasilitas sekolah untuk mendukung kegiatan pertemuan, acara, seminar, dan berbagai kegiatan siswa.', 'd34aa14e993223869673696fa7cebd2d.jpg', 'image/jpeg', 96243, '2026-10-21 03:10:47'),
(87, 'fasilitas', 'facility-6', 'Area Wi-Fi', 'Area Wi-Fi adalah fasilitas internet yang mendukung akses informasi, pembelajaran digital, dan kegiatan belajar siswa.', 'af482f8fcc3e2e433627b48a993a91c9.jpg', 'image/jpeg', 208793, '2026-10-21 03:12:47'),
(88, 'fasilitas', 'lead', 'Ruang Kepala Sekolah', 'Ruang kepala sekolah adalah tempat untuk mengelola administrasi, memimpin kegiatan sekolah, dan menerima tamu.', '1772ddaaa0f7a736a73bcf93a70689bb.jpg', 'image/jpeg', 441504, '2026-10-21 03:15:06'),
(89, 'kerjasama', 'lead', 'Daftar Mitra', 'Daftar mitra adalah kumpulan pihak yang bekerja sama dengan sekolah untuk mendukung pembelajaran, pengembangan keterampilan, dan peluang kerja siswa.', '16369c6a2e799c11459fbe457ba70e5d.jpg', 'image/jpeg', 131455, '2026-10-21 03:16:36'),
(90, 'home', 'hero', 'Infokom', '-', 'ce9aed556125e270dd7acb88f3564122.jpg', 'image/jpeg', 25120, '2026-10-21 03:18:45'),
(95, 'home', 'program-tkj', 'Tkj', 'Teknik Komputer dan Jaringan', '27e0b9c979abd170bf91209c8fb83224.jpg', 'image/jpeg', 32574, '2026-10-21 03:25:39'),
(96, 'home', 'program-rpl', 'Rpl', 'Rekayasa Perangkat Lunak', '7bcb987dc9f8996f050e055310d1a615.jpg', 'image/jpeg', 76328, '2026-10-21 03:26:02'),
(98, 'home', 'program-dkv', 'Pspt', 'Produksi Siaran dan Program Televisi', 'd2e50ca10cbd4c53437df6063c3290e7.jpg', 'image/jpeg', 55120, '2026-10-21 03:27:59'),
(100, 'home', 'program-pspt', 'Pspt', 'Produksi Siaran dan Program Televisi', '0b2dc668219cd4f1c2980fabb9df03ae.jpg', 'image/jpeg', 55120, '2026-10-21 03:28:41'),
(102, 'akademik', 'lead', 'Kurikulum', '-', '5f31a9a85c9d10db38c49ba3edb9240a.jpg', 'image/jpeg', 115479, '2026-10-21 03:31:26'),
(103, 'kesiswaan', 'activity-1', 'Robotik', '-', '2e36fe3be85f8264da8094d4ede68cf5.jpg', 'image/jpeg', 174391, '2026-10-21 03:33:10'),
(105, 'informasi', 'lead', 'berita', '-', 'bb5a36a4c27b7b9d4718dfeb9fe5a59a.jpg', 'image/jpeg', 85549, '2026-10-21 03:34:49'),
(106, 'kesiswaan', 'activity-5', 'Fotografi', '-', 'ac1cbcbd11276b8810bac15208b6f244.jpg', 'image/jpeg', 94817, '2026-10-21 03:35:58'),
(108, 'fasilitas', 'facility-2', 'Tkj', '-', '28abbbdc0f78a891615ffdcc64b33dbe.jpg', 'image/jpeg', 89898, '2026-10-21 03:37:49'),
(110, 'home', 'hero-card', 'gedung', '-', 'b0b8df3f666ae0fb8deed2ba6077a93c.jpg', 'image/jpeg', 441504, '2026-10-21 03:56:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `agenda`
--
ALTER TABLE `agenda`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jadwal_tes`
--
ALTER TABLE `jadwal_tes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lowongan`
--
ALTER TABLE `lowongan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mitra`
--
ALTER TABLE `mitra`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ppdb_dokumen`
--
ALTER TABLE `ppdb_dokumen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ppdb_pendaftar`
--
ALTER TABLE `ppdb_pendaftar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prestasi`
--
ALTER TABLE `prestasi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_keahlian`
--
ALTER TABLE `program_keahlian`
  ADD PRIMARY KEY (`kode`);

--
-- Indexes for table `school_profiles`
--
ALTER TABLE `school_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_order` (`category`,`urutan`);

--
-- Indexes for table `school_settings`
--
ALTER TABLE `school_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `site_images`
--
ALTER TABLE `site_images`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `page_slot` (`page_key`,`slot_key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `agenda`
--
ALTER TABLE `agenda`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `jadwal_tes`
--
ALTER TABLE `jadwal_tes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lowongan`
--
ALTER TABLE `lowongan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `mitra`
--
ALTER TABLE `mitra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `ppdb_dokumen`
--
ALTER TABLE `ppdb_dokumen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ppdb_pendaftar`
--
ALTER TABLE `ppdb_pendaftar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prestasi`
--
ALTER TABLE `prestasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `school_profiles`
--
ALTER TABLE `school_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `site_images`
--
ALTER TABLE `site_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
