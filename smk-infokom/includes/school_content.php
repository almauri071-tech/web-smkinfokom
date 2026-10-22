<?php
function ensure_school_content_tables(PDO $pdo): void {
  $pdo->exec("CREATE TABLE IF NOT EXISTS school_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category ENUM('guru','staff','kepala') NOT NULL,
    nama VARCHAR(160) NOT NULL,
    jabatan VARCHAR(160) NOT NULL,
    informasi TEXT NOT NULL,
    urutan INT NOT NULL DEFAULT 0,
    dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY category_order (category, urutan)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS program_keahlian (
    kode VARCHAR(12) PRIMARY KEY,
    nama VARCHAR(180) NOT NULL,
    deskripsi TEXT NOT NULL,
    kompetensi TEXT NOT NULL,
    fasilitas TEXT NOT NULL,
    kegiatan TEXT NOT NULL,
    proyek TEXT NOT NULL,
    karier TEXT NOT NULL,
    diperbarui_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $pdo->exec("CREATE TABLE IF NOT EXISTS school_settings (
    setting_key VARCHAR(80) PRIMARY KEY,
    setting_value TEXT NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  $columns = $pdo->query("SHOW COLUMNS FROM school_profiles LIKE 'informasi'");
  if (!$columns->fetch()) $pdo->exec('ALTER TABLE school_profiles ADD informasi TEXT NOT NULL AFTER jabatan');
  $enrollmentColumn = $pdo->query("SHOW COLUMNS FROM ppdb_pendaftar LIKE 'jurusan'")->fetch();
  if ($enrollmentColumn && !str_contains($enrollmentColumn['Type'], "'PSPT'")) {
    $pdo->exec("ALTER TABLE ppdb_pendaftar MODIFY jurusan ENUM('TKJ','RPL','DKV','PSPT') NOT NULL");
  }

  $programs = [
    ['TKJ','Teknik Komputer dan Jaringan','Program pembelajaran teknologi komputer dan jaringan. Isi halaman ini dapat disesuaikan dengan kurikulum serta fasilitas sekolah.','Perakitan dan pemeliharaan komputer|Konfigurasi jaringan komputer|Administrasi server dan layanan jaringan|Pemecahan masalah dan keamanan jaringan','Perangkat komputer dan jaringan|Perangkat router dan switch|Server dan perangkat praktikum','Praktik konfigurasi jaringan|Proyek layanan jaringan|Kunjungan atau praktik industri','Rancang bangun jaringan lokal|Dokumentasi instalasi dan konfigurasi jaringan|Layanan server untuk kebutuhan praktik','Teknisi jaringan|Teknisi komputer|Administrator jaringan pemula|Dukungan teknis'],
    ['RPL','Rekayasa Perangkat Lunak','Program pembelajaran analisis, perancangan, pembuatan, dan pengujian perangkat lunak. Isi halaman ini dapat disesuaikan dengan kurikulum sekolah.','Algoritma dan pemrograman|Pengembangan aplikasi web|Pengembangan aplikasi mobile|Basis data dan pengujian perangkat lunak|Kolaborasi dan dokumentasi pengembangan','Laboratorium komputer|Perangkat pengembangan perangkat lunak|Lingkungan praktik basis data','Proyek aplikasi berbasis kebutuhan|Kolaborasi pengembangan perangkat lunak|Praktik dan pengujian aplikasi','Aplikasi web untuk kebutuhan nyata|Aplikasi pengelolaan data|Prototipe aplikasi mobile','Web developer pemula|Programmer aplikasi pemula|Software tester pemula|Dukungan teknis aplikasi'],
    ['DKV','Desain Komunikasi Visual','Program pembelajaran komunikasi visual, desain grafis, dan produksi konten kreatif. Isi halaman ini dapat disesuaikan dengan kurikulum sekolah.','Dasar desain dan ilustrasi|Tipografi dan tata letak|Fotografi dan videografi|Desain media digital|Produksi karya komunikasi visual','Komputer desain|Perangkat fotografi dan video|Perangkat lunak desain dan penyuntingan','Proyek identitas visual|Produksi foto dan video|Pameran atau presentasi karya','Identitas visual|Poster dan materi kampanye|Konten media sosial|Video pendek','Desainer grafis pemula|Ilustrator pemula|Fotografer atau editor pemula|Kreator konten'],
    ['PSPT','Produksi dan Siaran Program Televisi','Program pembelajaran perencanaan, produksi, dan penyiaran program televisi dan konten audiovisual. Isi halaman ini dapat disesuaikan dengan kurikulum serta sarana sekolah.','Perencanaan program dan naskah|Teknik kamera dan tata cahaya|Tata suara|Penyuntingan video|Produksi dan penyiaran audiovisual','Kamera dan perlengkapan produksi|Perangkat tata cahaya dan audio|Komputer penyuntingan|Ruang produksi atau studio sesuai ketersediaan sekolah','Produksi program audiovisual|Latihan pengambilan gambar|Penyuntingan dan publikasi karya','Program berita sekolah|Film pendek|Program bincang-bincang|Konten video edukasi','Asisten produksi|Juru kamera pemula|Editor video pemula|Asisten penyiaran'],
  ];
  $insert = $pdo->prepare('INSERT IGNORE INTO program_keahlian (kode,nama,deskripsi,kompetensi,fasilitas,kegiatan,proyek,karier) VALUES (?,?,?,?,?,?,?,?)');
  foreach ($programs as $program) $insert->execute($program);
}

function school_profiles(?PDO $pdo, string $category): array {
  if (!$pdo) return [];
  $stmt = $pdo->prepare('SELECT id, category, nama, jabatan, informasi, urutan FROM school_profiles WHERE category=? ORDER BY urutan, nama');
  $stmt->execute([$category]);
  return $stmt->fetchAll();
}

function school_setting(?PDO $pdo, string $key, string $default = ''): string {
  if (!$pdo) return $default;
  $stmt = $pdo->prepare('SELECT setting_value FROM school_settings WHERE setting_key=?');
  $stmt->execute([$key]);
  $value = $stmt->fetchColumn();
  return $value === false ? $default : (string)$value;
}

function school_editable_page_sections(): array {
  return [
    'profil'=>['Sejarah'=>'sejarah','Visi dan Misi'=>'visi-misi','Struktur Organisasi'=>'struktur-organisasi'],
    'akademik'=>['Kurikulum'=>'kurikulum','Jadwal Pelajaran'=>'jadwal-pelajaran','Kalender Akademik'=>'kalender-akademik'],
    'kesiswaan'=>['OSIS'=>'osis','Ekstrakurikuler'=>'ekstrakurikuler'],
    'fasilitas'=>['Sarana dan Prasarana'=>'sarana-prasarana'],
    'bkk'=>['Rekrutmen'=>'rekrutmen','Tracer Study'=>'tracer-study'],
    'kerjasama'=>['Program Industri'=>'program-industri','MoU'=>'mou','Magang'=>'magang'],
    'ppdb'=>['Persyaratan'=>'persyaratan','Alur Pendaftaran'=>'alur-pendaftaran','Jadwal Seleksi'=>'jadwal-seleksi','Jalur Seleksi'=>'jalur-seleksi','Pengumuman Seleksi'=>'pengumuman-seleksi'],
  ];
}

function save_school_setting(PDO $pdo, string $key, string $value): void {
  $pdo->prepare('INSERT INTO school_settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)')
    ->execute([$key, $value]);
}
