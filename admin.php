<?php
// Panel admin sederhana: lihat pendaftar PPDB dan kelola berita, pengumuman, agenda.
session_start();
require_once 'includes/data.php';
if (!$pdo) { exit('Database belum tersambung. Impor database/smk_infokom.sql dan cek includes/db.php.'); }
require_once 'includes/pdf_documents.php';
require_once 'includes/image_library.php';
require_once 'includes/school_content.php';
ensure_pdf_documents_table($pdo);
ensure_site_images_table($pdo);
ensure_school_content_tables($pdo);
$maxPdfUpload = maximum_upload_size(10 * 1024 * 1024);
$maxImageUpload = maximum_upload_size(5 * 1024 * 1024);
if (!$pdo->query("SHOW COLUMNS FROM lowongan LIKE 'kualifikasi'")->fetch()) $pdo->exec('ALTER TABLE lowongan ADD kualifikasi TEXT NULL');
if (!$pdo->query("SHOW COLUMNS FROM mitra LIKE 'bidang_usaha'")->fetch()) $pdo->exec('ALTER TABLE mitra ADD bidang_usaha VARCHAR(160) NULL');
if (!$pdo->query("SHOW COLUMNS FROM mitra LIKE 'jenis_kerjasama'")->fetch()) $pdo->exec('ALTER TABLE mitra ADD jenis_kerjasama VARCHAR(160) NULL');
if (!$pdo->query("SHOW COLUMNS FROM mitra LIKE 'deskripsi'")->fetch()) $pdo->exec('ALTER TABLE mitra ADD deskripsi TEXT NULL');
$imageSlots = [
  'branding|school-logo'=>'Identitas Sekolah — logo resmi (tampil di navbar dan sidebar admin)',
  'home|hero'=>'Beranda — banner utama sekolah','home|hero-card'=>'Beranda — foto belajar siswa','home|activity-1'=>'Beranda — kegiatan belajar','home|activity-2'=>'Beranda — kegiatan sekolah','home|activity-3'=>'Beranda — prestasi',
  'home|program-tkj'=>'Beranda — foto program TKJ','home|program-rpl'=>'Beranda — foto program RPL','home|program-dkv'=>'Beranda — foto program DKV','home|program-pspt'=>'Beranda — foto program PSPT',
  'profil|lead'=>'Profil Sekolah — foto utama','akademik|lead'=>'Akademik — foto utama','kesiswaan|lead'=>'Kesiswaan — foto OSIS','kesiswaan|activity-1'=>'Kesiswaan — Robotik dan IoT','kesiswaan|activity-2'=>'Kesiswaan — Esports','kesiswaan|activity-3'=>'Kesiswaan — Pramuka','kesiswaan|activity-4'=>'Kesiswaan — Paskibra','kesiswaan|activity-5'=>'Kesiswaan — Fotografi dan videografi','kesiswaan|activity-6'=>'Kesiswaan — Futsal dan basket','kesiswaan|achievement'=>'Kesiswaan — prestasi siswa',
  'informasi|lead'=>'Informasi — foto utama','informasi|news-1'=>'Informasi — thumbnail berita 1','informasi|news-2'=>'Informasi — thumbnail berita 2','informasi|news-3'=>'Informasi — thumbnail berita 3','informasi|news-4'=>'Informasi — thumbnail berita 4','informasi|news-5'=>'Informasi — thumbnail berita 5','informasi|news-6'=>'Informasi — thumbnail berita 6',
  'galeri|gallery-1'=>'Galeri — foto 1','galeri|gallery-2'=>'Galeri — foto 2','galeri|gallery-3'=>'Galeri — foto 3','galeri|gallery-4'=>'Galeri — foto 4','galeri|gallery-5'=>'Galeri — foto 5','galeri|gallery-6'=>'Galeri — foto 6',
  'fasilitas|lead'=>'Fasilitas — foto utama','fasilitas|facility-1'=>'Fasilitas — ruang komputer','fasilitas|facility-2'=>'Fasilitas — lab jaringan','fasilitas|facility-3'=>'Fasilitas — studio multimedia','fasilitas|facility-4'=>'Fasilitas — perpustakaan','fasilitas|facility-5'=>'Fasilitas — aula','fasilitas|facility-6'=>'Fasilitas — area Wi-Fi',
  'bkk|lead'=>'BKK — rekrutmen dan karier','kerjasama|lead'=>'Kerja Sama PT — kegiatan industri','ppdb|lead'=>'PPDB — siswa baru','kontak|lead'=>'Kontak — lingkungan sekolah',
  'kepala-sekolah|sambutan'=>'Profil — Sambutan Kepala Sekolah',
];
$courseFallbackLabels = ['tkj'=>'Teknik Komputer dan Jaringan','rpl'=>'Rekayasa Perangkat Lunak','dkv'=>'Desain Komunikasi Visual','pspt'=>'Produksi dan Siaran Program Televisi'];
foreach ($courseFallbackLabels as $code=>$label) {
  $imageSlots['jurusan-'.$code.'|banner'] = 'Jurusan '.$code.' — banner';
  for ($slot=1; $slot<=6; $slot++) $imageSlots['jurusan-'.$code.'|gallery-'.$slot] = 'Jurusan '.$code.' — foto proyek '.$slot;
}
foreach (['guru'=>'guru','staff'=>'staff','kepala'=>'kepala-sekolah'] as $category=>$imagePage) {
  foreach (school_profiles($pdo, $category) as $profile) $imageSlots[$imagePage.'|person-'.$profile['id']] = $category.' — '.$profile['nama'];
}
foreach ($pdo->query('SELECT id,judul FROM berita ORDER BY tanggal DESC,id DESC') as $news) {
  $imageSlots['informasi|news-'.$news['id']] = 'Informasi — '.$news['judul'];
}
foreach ($pdo->query('SELECT id,nama FROM mitra ORDER BY nama') as $partner) {
  $imageSlots['kerjasama|partner-'.$partner['id']] = 'Kerja Sama PT — logo '.$partner['nama'];
}

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['csrf'];
function cek(){ if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) { http_response_code(400); exit('Permintaan tidak valid.'); } }

$jmlAdmin = (int)$pdo->query('SELECT COUNT(*) FROM admin')->fetchColumn();
$msg = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  cek(); $a = $_POST['aksi'] ?? '';
  if ($a === 'setup' && $jmlAdmin === 0 && strlen($_POST['password'] ?? '') >= 8) {
    $pdo->prepare('INSERT INTO admin (username, password) VALUES (?,?)')->execute([trim($_POST['username']), password_hash($_POST['password'], PASSWORD_DEFAULT)]);
    $msg = 'Admin dibuat. Silakan masuk.'; $jmlAdmin = 1;
  } elseif ($a === 'login') {
    $s = $pdo->prepare('SELECT * FROM admin WHERE username = ?'); $s->execute([trim($_POST['username'] ?? '')]); $u = $s->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) { session_regenerate_id(true); $_SESSION['admin'] = $u['username']; $_SESSION['csrf'] = bin2hex(random_bytes(16)); header('Location: admin.php'); exit; }
    $msg = 'Username atau kata sandi salah.';
  } elseif (!empty($_SESSION['admin'])) {
    if ($a === 'logout') { session_destroy(); header('Location: admin.php'); exit; }
    if ($a === 'status') { $pdo->prepare('UPDATE ppdb_pendaftar SET status=? WHERE id=?')->execute([in_array($_POST['status'], ['baru','diproses','diterima','ditolak']) ? $_POST['status'] : 'baru', (int)$_POST['id']]); }
    if (in_array($a,['tambah_berita','simpan_berita'],true)) {
      $recordId = (int)($_POST['id'] ?? 0);
      $values = [trim($_POST['judul'] ?? ''),trim($_POST['ringkasan'] ?? ''),$_POST['tanggal'] ?? ''];
      if ($recordId) $pdo->prepare('UPDATE berita SET judul=?,ringkasan=?,tanggal=? WHERE id=?')->execute([...$values,$recordId]);
      else $pdo->prepare('INSERT INTO berita (judul,ringkasan,tanggal) VALUES (?,?,?)')->execute($values);
      $_SESSION['flash'] = $recordId ? 'Berita berhasil diperbarui.' : 'Berita berhasil ditambahkan.';
    }
    if (in_array($a,['tambah_pengumuman','simpan_pengumuman'],true)) {
      $recordId = (int)($_POST['id'] ?? 0);
      $values = [trim($_POST['isi'] ?? ''),$_POST['tanggal'] ?? ''];
      if ($recordId) $pdo->prepare('UPDATE pengumuman SET isi=?,tanggal=? WHERE id=?')->execute([...$values,$recordId]);
      else $pdo->prepare('INSERT INTO pengumuman (isi,tanggal) VALUES (?,?)')->execute($values);
      $_SESSION['flash'] = $recordId ? 'Pengumuman berhasil diperbarui.' : 'Pengumuman berhasil ditambahkan.';
    }
    if (in_array($a,['tambah_agenda','simpan_agenda'],true)) {
      $recordId = (int)($_POST['id'] ?? 0);
      $values = [$_POST['tanggal'] ?? '',trim($_POST['kegiatan'] ?? ''),trim($_POST['tempat'] ?? '')];
      if ($recordId) $pdo->prepare('UPDATE agenda SET tanggal=?,kegiatan=?,tempat=? WHERE id=?')->execute([...$values,$recordId]);
      else $pdo->prepare('INSERT INTO agenda (tanggal,kegiatan,tempat) VALUES (?,?,?)')->execute($values);
      $_SESSION['flash'] = $recordId ? 'Agenda berhasil diperbarui.' : 'Agenda berhasil ditambahkan.';
    }
    if ($a === 'simpan_prestasi') {
      $recordId = (int)($_POST['id'] ?? 0);
      $values = [(int)($_POST['tahun'] ?? 0),trim($_POST['prestasi'] ?? ''),trim($_POST['tingkat'] ?? '')];
      if ($values[0] < 2000 || $values[0] > 2155 || $values[1] === '' || $values[2] === '') {
        $_SESSION['flash'] = 'ERR: Lengkapi tahun, nama prestasi, dan tingkat yang valid.';
      } elseif ($recordId) {
        $pdo->prepare('UPDATE prestasi SET tahun=?,prestasi=?,tingkat=? WHERE id=?')->execute([...$values,$recordId]);
        $_SESSION['flash'] = 'Prestasi siswa berhasil diperbarui.';
      } else {
        $pdo->prepare('INSERT INTO prestasi (tahun,prestasi,tingkat) VALUES (?,?,?)')->execute($values);
        $_SESSION['flash'] = 'Prestasi siswa berhasil ditambahkan.';
      }
    }
    if ($a === 'simpan_lowongan') {
      $jobId = (int)($_POST['id'] ?? 0);
      $values = [trim($_POST['perusahaan'] ?? ''), trim($_POST['posisi'] ?? ''), trim($_POST['kualifikasi'] ?? ''), $_POST['batas'] ?? ''];
      if ($values[0] === '' || $values[1] === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $values[3])) {
        $_SESSION['flash'] = 'ERR: Lengkapi perusahaan, posisi, dan batas pendaftaran yang valid.';
      } elseif ($jobId) {
        $pdo->prepare('UPDATE lowongan SET perusahaan=?, posisi=?, kualifikasi=?, batas=? WHERE id=?')->execute([...$values, $jobId]);
        $_SESSION['flash'] = 'Informasi lowongan berhasil diperbarui.';
      } else {
        $pdo->prepare('INSERT INTO lowongan (perusahaan, posisi, kualifikasi, batas) VALUES (?,?,?,?)')->execute($values);
        $_SESSION['flash'] = 'Lowongan berhasil ditambahkan.';
      }
      header('Location: admin.php?t=lowongan'); exit;
    }
    if ($a === 'simpan_mitra') {
      $partnerId = (int)($_POST['id'] ?? 0);
      $values = [trim($_POST['nama'] ?? ''), trim($_POST['bidang_usaha'] ?? ''), trim($_POST['jenis_kerjasama'] ?? ''), trim($_POST['deskripsi'] ?? '')];
      if ($values[0] === '') {
        $_SESSION['flash'] = 'ERR: Nama perusahaan mitra wajib diisi.';
      } elseif ($partnerId) {
        $pdo->prepare('UPDATE mitra SET nama=?, bidang_usaha=?, jenis_kerjasama=?, deskripsi=? WHERE id=?')->execute([...$values, $partnerId]);
        $_SESSION['flash'] = 'Informasi perusahaan mitra berhasil diperbarui.';
      } else {
        $pdo->prepare('INSERT INTO mitra (nama, bidang_usaha, jenis_kerjasama, deskripsi) VALUES (?,?,?,?)')->execute($values);
        $_SESSION['flash'] = 'Perusahaan mitra berhasil ditambahkan.';
      }
      header('Location: admin.php?t=mitra'); exit;
    }
    if ($a === 'simpan_profil') {
      $category = $_POST['category'] ?? '';
      if (!in_array($category, ['guru','staff','kepala'], true)) {
        $_SESSION['flash'] = 'ERR: Kategori profil tidak valid.';
        header('Location: admin.php?t=guru'); exit;
      }
      $profileId = (int)($_POST['id'] ?? 0);
      if ($category === 'kepala' && !$profileId) {
        $existingPrincipal = $pdo->query("SELECT id FROM school_profiles WHERE category='kepala' ORDER BY id LIMIT 1")->fetchColumn();
        if ($existingPrincipal) $profileId = (int)$existingPrincipal;
      }
      $name = trim($_POST['nama'] ?? '');
      $role = trim($_POST['jabatan'] ?? '');
      $information = trim($_POST['informasi'] ?? '');
      if ($name === '' || $role === '') {
        $_SESSION['flash'] = 'ERR: Nama dan jabatan wajib diisi.';
      } elseif ($profileId) {
        $pdo->prepare('UPDATE school_profiles SET nama=?, jabatan=?, informasi=? WHERE id=? AND category=?')->execute([$name,$role,$information,$profileId,$category]);
        $_SESSION['flash'] = 'Profil berhasil diperbarui.';
      } else {
        $order = (int)$pdo->query("SELECT COALESCE(MAX(urutan),0)+1 FROM school_profiles WHERE category=".$pdo->quote($category))->fetchColumn();
        $pdo->prepare('INSERT INTO school_profiles (category,nama,jabatan,informasi,urutan) VALUES (?,?,?,?,?)')->execute([$category,$name,$role,$information,$order]);
        $profileId = (int)$pdo->lastInsertId();
        $_SESSION['flash'] = 'Profil berhasil ditambahkan. Unggah fotonya melalui Galeri dan Media.';
      }
      if ($category === 'kepala' && $name !== '' && $role !== '') {
        save_school_setting($pdo, 'kepala_nama', $name);
        save_school_setting($pdo, 'kepala_jabatan', $role);
      }
      header('Location: admin.php?t='.($category === 'kepala' ? 'kepala' : $category)); exit;
    }
    if ($a === 'hapus_profil') {
      $profileId = (int)($_POST['id'] ?? 0);
      $category = $_POST['category'] ?? '';
      if (in_array($category, ['guru','staff','kepala'], true)) {
        $pdo->prepare('DELETE FROM school_profiles WHERE id=? AND category=?')->execute([$profileId,$category]);
        hapus_gambar_lokasi($pdo, $category === 'kepala' ? 'kepala-sekolah' : $category, 'person-'.$profileId);
        if ($category === 'kepala') {
          save_school_setting($pdo, 'kepala_nama', '');
          save_school_setting($pdo, 'kepala_jabatan', '');
        }
        $_SESSION['flash'] = 'Profil berhasil dihapus.';
      } else {
        $_SESSION['flash'] = 'ERR: Kategori profil tidak valid.';
      }
      header('Location: admin.php?t='.($category === 'kepala' ? 'kepala' : $category)); exit;
    }
    if ($a === 'simpan_sambutan') {
      save_school_setting($pdo, 'kepala_sambutan', trim($_POST['sambutan'] ?? ''));
      $_SESSION['flash'] = 'Sambutan kepala sekolah berhasil disimpan.';
      header('Location: admin.php?t=kepala'); exit;
    }
    if ($a === 'simpan_halaman') {
      $pageKey = $_POST['page_key'] ?? '';
      $pageLabels = ['home'=>'Beranda','profil'=>'Profil Sekolah','akademik'=>'Akademik','kesiswaan'=>'Kesiswaan','informasi'=>'Informasi dan Pengumuman','fasilitas'=>'Fasilitas','bkk'=>'BKK','kerjasama'=>'Kerja Sama PT','ppdb'=>'PPDB / SPMB'];
      if (!isset($pageLabels[$pageKey])) {
        $_SESSION['flash'] = 'ERR: Halaman yang dipilih tidak valid.';
        header('Location: admin.php?t=dashboard'); exit;
      }
      $description = trim($_POST['description'] ?? '');
      $summary = trim($_POST['summary'] ?? '');
      $heading = trim($_POST['heading'] ?? '');
      $allowedSections = school_editable_page_sections()[$pageKey] ?? [];
      $sectionInputs = is_array($_POST['sections'] ?? null) ? $_POST['sections'] : [];
      $sectionValues = [];
      foreach ($allowedSections as $sectionTitle=>$sectionKey) {
        $sectionValue = $sectionInputs[$sectionKey] ?? '';
        if (!is_string($sectionValue)) $sectionValue = '';
        $sectionValues[$sectionKey] = trim($sectionValue);
      }
      if (mb_strlen($description) > 500 || mb_strlen($summary) > 4000 || mb_strlen($heading) > 180 || array_filter($sectionValues,fn($value)=>mb_strlen($value)>4000)) {
        $_SESSION['flash'] = 'ERR: Deskripsi maksimal 500 karakter, ringkasan dan setiap bagian maksimal 4.000 karakter, serta judul utama maksimal 180 karakter.';
      } else {
        if ($pageKey === 'home') {
          save_school_setting($pdo, 'home_heading', $heading);
          save_school_setting($pdo, 'home_summary', $summary);
        } else {
          save_school_setting($pdo, 'page_'.$pageKey.'_description', $description);
          save_school_setting($pdo, 'page_'.$pageKey.'_summary', $summary);
          foreach ($sectionValues as $sectionKey=>$sectionValue) {
            save_school_setting($pdo, 'page_'.$pageKey.'_section_'.$sectionKey, $sectionValue);
          }
        }
        $_SESSION['flash'] = 'Konten halaman berhasil disimpan.';
      }
      $redirectPage = $pageKey === 'ppdb' ? 'konten-ppdb' : ($pageKey === 'home' ? 'beranda' : $pageKey);
      header('Location: admin.php?t='.urlencode($redirectPage)); exit;
    }
    if ($a === 'simpan_jurusan') {
      $code = strtoupper(trim($_POST['kode'] ?? ''));
      if (!in_array($code, ['TKJ','RPL','DKV','PSPT'], true)) {
        $_SESSION['flash'] = 'ERR: Kode jurusan tidak valid.';
        header('Location: admin.php?t=jurusan'); exit;
      }
      $fields = [];
      foreach (['kompetensi','fasilitas','kegiatan','proyek','karier'] as $key) {
        $items = preg_split('/\R/u', trim($_POST[$key] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
        $fields[$key] = implode('|', array_map('trim', $items));
      }
      $pdo->prepare('UPDATE program_keahlian SET deskripsi=?,kompetensi=?,fasilitas=?,kegiatan=?,proyek=?,karier=? WHERE kode=?')
        ->execute([trim($_POST['deskripsi'] ?? ''),$fields['kompetensi'],$fields['fasilitas'],$fields['kegiatan'],$fields['proyek'],$fields['karier'],$code]);
      $_SESSION['flash'] = 'Informasi jurusan berhasil diperbarui.';
      header('Location: admin.php?t=jurusan'); exit;
    }
    if ($a === 'simpan_pengaturan') {
      foreach (['nama'=>160,'alamat'=>500,'wa'=>30,'wa_tampil'=>40,'email'=>180] as $key=>$maxLength) {
        $value = trim($_POST[$key] ?? '');
        if (mb_strlen($value) > $maxLength || ($key === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL))) {
          $_SESSION['flash'] = 'ERR: Periksa panjang atau format kolom '.e($key).'.';
          header('Location: admin.php?t=pengaturan'); exit;
        }
        save_school_setting($pdo, $key, $value);
      }
      $_SESSION['flash'] = 'Pengaturan website berhasil disimpan.';
      header('Location: admin.php?t=pengaturan'); exit;
    }
    if ($a === 'simpan_dokumen') {
      try {
        $docId = !empty($_POST['id']) ? (int)$_POST['id'] : null;
        simpan_dokumen_pdf($pdo, $_FILES['pdf'] ?? [], $_POST['judul'] ?? '', $_POST['deskripsi'] ?? '', $docId);
        $_SESSION['flash'] = $docId === null ? 'Dokumen PDF berhasil diunggah.' : 'Dokumen PPDB berhasil diperbarui.';
      } catch (RuntimeException $ex) {
        $_SESSION['flash'] = 'ERR: '.$ex->getMessage();
      } catch (Throwable $ex) {
        error_log('Dokumen PPDB gagal disimpan: '.$ex->getMessage());
        $_SESSION['flash'] = 'ERR: Perubahan dokumen gagal disimpan. Periksa koneksi database dan izin penyimpanan.';
      }
      header('Location: admin.php?t=dokumen'); exit;
    }
    if ($a === 'simpan_gambar') {
      try {
        $target = explode('|', $_POST['target'] ?? '', 2);
        if (count($target) !== 2 || !isset($imageSlots[$_POST['target'] ?? ''])) throw new RuntimeException('Pilih lokasi gambar yang tersedia.');
        simpan_gambar_situs($pdo, $_FILES['gambar'] ?? [], $target[0], $target[1], $_POST['judul'] ?? '', $_POST['alt_text'] ?? '');
        $_SESSION['flash'] = 'Gambar berhasil diunggah dan dipasang.';
      } catch (RuntimeException $ex) {
        $_SESSION['flash'] = 'ERR: '.$ex->getMessage();
      } catch (Throwable $ex) {
        error_log('Gambar situs gagal disimpan: '.$ex->getMessage());
        $_SESSION['flash'] = 'ERR: Gambar gagal disimpan. Periksa database dan izin penyimpanan server.';
      }
      header('Location: admin.php?t=gambar'); exit;
    }
    if ($a === 'hapus_gambar') {
      try {
        hapus_gambar_situs($pdo, (int)($_POST['id'] ?? 0));
        $_SESSION['flash'] = 'Gambar dihapus. Foto ilustrasi bawaan akan ditampilkan kembali.';
      } catch (Throwable $ex) {
        error_log('Gambar situs gagal dihapus: '.$ex->getMessage());
        $_SESSION['flash'] = 'ERR: Gambar gagal dihapus. Silakan coba kembali.';
      }
      header('Location: admin.php?t=gambar'); exit;
    }
    if ($a === 'hapus_dokumen') {
      try {
        hapus_dokumen_pdf($pdo, (int)($_POST['id'] ?? 0));
        $_SESSION['flash'] = 'Dokumen PDF berhasil dihapus.';
      } catch (Throwable $ex) {
        error_log('Dokumen PPDB gagal dihapus: '.$ex->getMessage());
        $_SESSION['flash'] = 'ERR: Dokumen gagal dihapus. Silakan coba kembali.';
      }
      header('Location: admin.php?t=dokumen'); exit;
    }
    if ($a === 'hapus' && in_array($_POST['tabel'], ['berita','pengumuman','agenda','prestasi','ppdb_pendaftar','lowongan','mitra'], true)) {
      $recordId = (int)($_POST['id'] ?? 0);
      $pdo->prepare('DELETE FROM '.$_POST['tabel'].' WHERE id=?')->execute([$recordId]);
      if ($_POST['tabel'] === 'berita') hapus_gambar_lokasi($pdo,'informasi','news-'.$recordId);
      if ($_POST['tabel'] === 'mitra') hapus_gambar_lokasi($pdo,'kerjasama','partner-'.$recordId);
      $_SESSION['flash'] = 'Data berhasil dihapus.';
    }
    header('Location: admin.php?t='.urlencode($_POST['t'] ?? 'ppdb')); exit;
  }
}
$login = !empty($_SESSION['admin']);
$t = $_GET['t'] ?? 'dashboard';
$adminTitles = [
  'dashboard'=>'Dashboard','beranda'=>'Beranda','profil'=>'Profil Sekolah','akademik'=>'Akademik','kesiswaan'=>'Kesiswaan',
  'informasi'=>'Informasi dan Pengumuman','fasilitas'=>'Fasilitas','jurusan'=>'Data Jurusan',
  'guru'=>'Data Guru','staff'=>'Data Staff','kepala'=>'Kepala Sekolah','berita'=>'Berita',
  'pengumuman'=>'Pengumuman','agenda'=>'Agenda','gambar'=>'Galeri','lowongan'=>'BKK dan Lowongan Kerja',
  'mitra'=>'Kerja Sama PT','ppdb'=>'PPDB / SPMB','konten-ppdb'=>'Konten PPDB / SPMB','dokumen'=>'Dokumen PPDB','pengaturan'=>'Pengaturan',
];
$adminTitle = $adminTitles[$t] ?? 'Dashboard';
$selectedCourse = isset($_GET['kode']) && is_string($_GET['kode']) ? strtoupper($_GET['kode']) : '';
if ($t === 'jurusan' && in_array($selectedCourse,['TKJ','RPL','DKV','PSPT'],true)) $adminTitle = 'Jurusan '.$selectedCourse;
$imagePageFilter = isset($_GET['halaman']) && is_string($_GET['halaman']) ? $_GET['halaman'] : '';
$imagePageKeys = ['branding','home','profil','akademik','kesiswaan','informasi','galeri','fasilitas','bkk','kerjasama','ppdb','kontak','guru','staff','kepala-sekolah','jurusan-tkj','jurusan-rpl','jurusan-dkv','jurusan-pspt'];
if (!in_array($imagePageFilter,$imagePageKeys,true)) $imagePageFilter = '';
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin – SMK Infokom</title><link rel="stylesheet" href="assets/style.css">
<style>.adm{min-height:100vh}.inl{display:inline}.inl button{font:inherit;padding:4px 10px}.row{display:grid;gap:12px;max-width:650px;margin:16px 0}textarea{font:inherit;padding:10px;border:1px solid var(--line);border-radius:8px;background:var(--white);color:var(--ink)}</style></head><body><div class="adm <?= $login?'admin-authenticated':'admin-login' ?>">
<?php if (!$login): ?><h1>Admin SMK Infokom</h1><?php endif; ?>
<?php if ($msg): ?><p class="<?= str_starts_with($msg,'ERR:') || str_contains($msg,'salah') ? 'bad':'ok' ?>"><?= e(str_starts_with($msg,'ERR:') ? substr($msg, 5) : $msg) ?></p><?php endif; ?>

<?php if (!$login): ?>
  <form method="post" class="form">
    <input type="hidden" name="csrf" value="<?= $csrf ?>">
    <input type="hidden" name="aksi" value="<?= $jmlAdmin ? 'login' : 'setup' ?>">
    <?php if (!$jmlAdmin): ?><p>Belum ada admin. Buat akun pertama (kata sandi minimal 8 karakter).</p><?php endif; ?>
    <label>Username<input name="username" required autocomplete="username"></label>
    <label>Kata sandi<input type="password" name="password" required minlength="8" autocomplete="current-password"></label>
    <button class="btn"><?= $jmlAdmin ? 'Masuk' : 'Buat admin' ?></button>
  </form>
<?php else: ?>
  <div class="admin-shell">
    <div class="sidebar-backdrop" data-sidebar-close></div>
    <aside class="admin-sidebar" id="adminSidebar">
      <div class="sidebar-heading"><a href="?t=dashboard" class="sidebar-brand"><?php if (site_image_record('branding','school-logo')): ?><img class="logo-image" src="<?= e(site_image_source('branding','school-logo','')) ?>" alt="<?= e(site_image_alt('branding','school-logo','Logo resmi '.$site['nama'])) ?>"><?php else: ?><span class="logo">IK</span><?php endif; ?><span><b>Admin</b><small><?= e($site['nama']) ?></small></span></a><div class="sidebar-controls"><button class="sidebar-collapse" type="button" aria-label="Ciutkan sidebar" data-sidebar-collapse>‹</button><button class="sidebar-close" type="button" aria-label="Tutup menu" data-sidebar-close>×</button></div></div>
      <p class="sidebar-user">Masuk sebagai <strong><?= e($_SESSION['admin']) ?></strong></p>
      <nav class="sidebar-nav" aria-label="Navigasi admin">
        <a class="sidebar-link <?= $t==='dashboard'?'active':'' ?>" href="?t=dashboard"><span class="side-icon">⌂</span><span>Dashboard</span></a>
        <a class="sidebar-link <?= $t==='beranda'?'active':'' ?>" href="?t=beranda"><span class="side-icon">⌂</span><span>Beranda</span></a>
        <p class="sidebar-section-label">KONTEN SEKOLAH</p>
        <details class="sidebar-group" <?= in_array($t,['profil','akademik','kesiswaan','fasilitas'],true)?'open':'' ?>>
          <summary><span class="side-icon">▤</span><span>Halaman Sekolah</span><span class="side-chevron">⌄</span></summary>
          <a class="<?= $t==='profil'?'active':'' ?>" href="?t=profil">Profil Sekolah</a>
          <a class="<?= $t==='akademik'?'active':'' ?>" href="?t=akademik">Akademik</a>
          <a class="<?= $t==='kesiswaan'?'active':'' ?>" href="?t=kesiswaan">Kesiswaan</a>
          <a class="<?= $t==='fasilitas'?'active':'' ?>" href="?t=fasilitas">Fasilitas</a>
        </details>
        <details class="sidebar-group" <?= $t==='jurusan'?'open':'' ?>>
          <summary><span class="side-icon">▦</span><span>Jurusan</span><span class="side-chevron">⌄</span></summary>
          <?php foreach (['TKJ','RPL','DKV','PSPT'] as $courseCode): ?>
            <a class="<?= $t==='jurusan' && $selectedCourse===$courseCode?'active':'' ?>" href="?t=jurusan&amp;kode=<?= $courseCode ?>"><?= $courseCode ?></a>
          <?php endforeach; ?>
        </details>
        <p class="sidebar-section-label">DATA SEKOLAH</p>
        <details class="sidebar-group" <?= in_array($t,['guru','staff'],true)?'open':'' ?>>
          <summary><span class="side-icon">♙</span><span>Guru dan Staff</span><span class="side-chevron">⌄</span></summary>
          <a class="<?= $t==='guru'?'active':'' ?>" href="?t=guru">Data Guru</a><a class="<?= $t==='staff'?'active':'' ?>" href="?t=staff">Data Staff</a>
        </details>
        <a class="sidebar-link <?= $t==='kepala'?'active':'' ?>" href="?t=kepala"><span class="side-icon">♙</span><span>Kepala Sekolah</span></a>
        <p class="sidebar-section-label">PUBLIKASI & KARIER</p>
        <details class="sidebar-group" <?= in_array($t,['informasi','berita','pengumuman','agenda'],true)?'open':'' ?>>
          <summary><span class="side-icon">▧</span><span>Informasi</span><span class="side-chevron">⌄</span></summary>
          <a class="<?= $t==='informasi'?'active':'' ?>" href="?t=informasi">Konten Informasi</a>
          <a class="<?= $t==='berita'?'active':'' ?>" href="?t=berita">Berita</a>
          <a class="<?= $t==='pengumuman'?'active':'' ?>" href="?t=pengumuman">Pengumuman</a>
          <a class="<?= $t==='agenda'?'active':'' ?>" href="?t=agenda">Agenda</a>
        </details>
        <a class="sidebar-link <?= $t==='gambar'?'active':'' ?>" href="?t=gambar"><span class="side-icon">▧</span><span>Galeri</span></a>
        <details class="sidebar-group" <?= in_array($t,['bkk','lowongan'],true)?'open':'' ?>>
          <summary><span class="side-icon">▣</span><span>BKK dan Lowongan Kerja</span><span class="side-chevron">⌄</span></summary>
          <a class="<?= $t==='bkk'?'active':'' ?>" href="?t=bkk">Konten BKK</a>
          <a class="<?= $t==='lowongan'?'active':'' ?>" href="?t=lowongan">Lowongan Kerja</a>
        </details>
        <details class="sidebar-group" <?= in_array($t,['mitra','kerjasama'],true)?'open':'' ?>>
          <summary><span class="side-icon">⇄</span><span>Kerja Sama PT</span><span class="side-chevron">⌄</span></summary>
          <a class="<?= $t==='kerjasama'?'active':'' ?>" href="?t=kerjasama">Konten Kerja Sama</a>
          <a class="<?= $t==='mitra'?'active':'' ?>" href="?t=mitra">Data Perusahaan Mitra</a>
        </details>
        <p class="sidebar-section-label">PENERIMAAN</p>
        <details class="sidebar-group" <?= in_array($t,['ppdb','konten-ppdb','dokumen'],true)?'open':'' ?>>
          <summary><span class="side-icon">▣</span><span>PPDB / SPMB</span><span class="side-chevron">⌄</span></summary>
          <a class="<?= $t==='ppdb'?'active':'' ?>" href="?t=ppdb">Pendaftar</a>
          <a class="<?= $t==='konten-ppdb'?'active':'' ?>" href="?t=konten-ppdb">Konten PPDB</a>
          <a class="<?= $t==='dokumen'?'active':'' ?>" href="?t=dokumen">Dokumen PDF</a>
        </details>
        <a class="sidebar-link <?= $t==='pengaturan'?'active':'' ?>" href="?t=pengaturan"><span class="side-icon">⚙</span><span>Pengaturan</span></a>
      </nav>
      <form method="post" class="sidebar-logout"><input type="hidden" name="csrf" value="<?= e($csrf) ?>"><input type="hidden" name="aksi" value="logout"><button type="submit"><span class="side-icon">↪</span><span>Logout</span></button></form>
      <a class="sidebar-public" href="index.php">↗ Lihat website publik</a>
    </aside>
    <main class="admin-main">
      <header class="admin-mobile-header"><button type="button" data-sidebar-open aria-controls="adminSidebar" aria-expanded="false">☰ Menu</button><b>Admin <?= e($site['nama']) ?></b></header>
      <div class="admin-content">
      <header class="admin-topbar">
        <div class="admin-topbar-title"><span class="eyebrow">ADMINISTRASI SEKOLAH</span><strong><?= e($adminTitle) ?></strong></div>
        <div class="admin-topbar-actions">
          <label class="admin-search"><span aria-hidden="true">⌕</span><input type="search" data-admin-search placeholder="Cari data pada halaman ini" aria-label="Cari data pada halaman ini"></label>
          <?php if ($t === 'gambar'): ?><select class="admin-filter" data-image-page-filter aria-label="Filter foto berdasarkan halaman"><option value="">Semua halaman</option><?php foreach (['branding'=>'Logo Sekolah','home'=>'Beranda','profil'=>'Profil Sekolah','akademik'=>'Akademik','kesiswaan'=>'Kesiswaan','informasi'=>'Informasi','galeri'=>'Galeri','fasilitas'=>'Fasilitas','bkk'=>'BKK','kerjasama'=>'Kerja Sama PT','ppdb'=>'PPDB','kontak'=>'Kontak','guru'=>'Guru','staff'=>'Staff','kepala-sekolah'=>'Kepala Sekolah','jurusan-tkj'=>'Jurusan TKJ','jurusan-rpl'=>'Jurusan RPL','jurusan-dkv'=>'Jurusan DKV','jurusan-pspt'=>'Jurusan PSPT'] as $pageKey=>$pageName): ?><option value="<?= e($pageKey) ?>" <?= $imagePageFilter===$pageKey?'selected':'' ?>><?= e($pageName) ?></option><?php endforeach; ?></select><?php endif; ?>
          <?php if ($t === 'ppdb'): ?><select class="admin-filter" data-admin-filter aria-label="Filter status pendaftar"><option value="">Semua status</option><?php foreach (['baru','diproses','diterima','ditolak'] as $statusOption): ?><option value="<?= $statusOption ?>"><?= ucfirst($statusOption) ?></option><?php endforeach; ?></select><?php endif; ?>
          <button class="admin-export" type="button" data-admin-export hidden>Ekspor CSV</button>
          <?php if (in_array($t,['guru','staff','kepala','kesiswaan','berita','pengumuman','agenda','lowongan','mitra','dokumen','gambar'],true)): ?>
            <a class="admin-add" href="#adminCreateForm" data-scroll-create>＋ Tambah Data</a>
          <?php endif; ?>
          <a class="admin-notification" href="?t=ppdb" aria-label="Lihat pendaftar PPDB">♧</a>
          <span class="admin-avatar" aria-hidden="true"><?= e(strtoupper(substr($_SESSION['admin'],0,1))) ?></span>
          <span class="admin-account"><b><?= e($_SESSION['admin']) ?></b><small>Administrator</small></span>
        </div>
      </header>
      <?php
        $standardTitles = ['ppdb'=>'Pendaftar PPDB','dokumen'=>'Dokumen PPDB','gambar'=>'Galeri dan Media','berita'=>'Berita','pengumuman'=>'Pengumuman','agenda'=>'Agenda','lowongan'=>'BKK dan Lowongan Kerja','mitra'=>'Kerja Sama PT'];
        if (isset($standardTitles[$t])): ?>
          <div class="admin-page-title"><div><p class="eyebrow">PENGELOLAAN WEBSITE</p><h1><?= e($standardTitles[$t]) ?></h1></div></div>
      <?php endif; ?>
  <?php
  function hapusForm($tabel, $id, $t, $csrf){ return '<form method="post" class="inl" onsubmit="return confirm(\'Hapus data ini?\')"><input type="hidden" name="csrf" value="'.e($csrf).'"><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="tabel" value="'.e($tabel).'"><input type="hidden" name="id" value="'.(int)$id.'"><input type="hidden" name="t" value="'.e($t).'"><button>Hapus</button></form>'; }
  $hid = '<input type="hidden" name="csrf" value="'.$csrf.'"><input type="hidden" name="t" value="'.e($t).'">';
  if ($t === 'dashboard'):
    $countTeachers = (int)$pdo->query("SELECT COUNT(*) FROM school_profiles WHERE category='guru'")->fetchColumn();
    $countStaff = (int)$pdo->query("SELECT COUNT(*) FROM school_profiles WHERE category='staff'")->fetchColumn();
    $countCourses = (int)$pdo->query('SELECT COUNT(*) FROM program_keahlian')->fetchColumn();
    $countAnnouncements = (int)$pdo->query('SELECT COUNT(*) FROM pengumuman')->fetchColumn();
    $countApplicants = (int)$pdo->query('SELECT COUNT(*) FROM ppdb_pendaftar')->fetchColumn(); ?>
    <div class="admin-page-title"><div><p class="eyebrow">RINGKASAN</p><h1>Selamat datang, <?= e($_SESSION['admin']) ?></h1><p>Ikhtisar data website sekolah yang tersimpan saat ini.</p></div></div>
    <div class="grid g3 dashboard-cards">
      <a class="card dashboard-card" href="?t=guru"><span>♙</span><b><?= $countTeachers ?></b><small>Data Guru</small></a>
      <a class="card dashboard-card" href="?t=staff"><span>♙</span><b><?= $countStaff ?></b><small>Data Staff</small></a>
      <a class="card dashboard-card" href="?t=jurusan"><span>▦</span><b><?= $countCourses ?></b><small>Jurusan</small></a>
      <a class="card dashboard-card" href="?t=informasi"><span>▧</span><b><?= $countAnnouncements ?></b><small>Pengumuman</small></a>
      <a class="card dashboard-card" href="?t=ppdb"><span>▣</span><b><?= $countApplicants ?></b><small>Pendaftar PPDB</small></a>
      <div class="card dashboard-card dashboard-card-muted"><span>♧</span><b>—</b><small>Data siswa terpisah belum tersedia</small></div>
    </div>
    <section class="admin-panel"><h2>Akses cepat</h2><div class="quick-links"><a class="btn" href="?t=gambar">Kelola foto situs</a><a class="btn" href="?t=kepala">Sambutan kepala sekolah</a><a class="btn" href="?t=jurusan">Data jurusan</a><a class="btn" href="index.php">Lihat website</a></div></section>
  <?php elseif (in_array($t,['beranda','profil','akademik','kesiswaan','informasi','fasilitas','bkk','kerjasama','konten-ppdb'],true)):
    $pageKey = $t === 'beranda' ? 'home' : ($t === 'konten-ppdb' ? 'ppdb' : $t);
    $pageContent = $pageKey === 'home' ? ['Beranda'] : $menu[$pageKey];
    $pageDescription = $pageKey === 'home' ? '' : school_setting($pdo,'page_'.$pageKey.'_description',$pageContent[1]);
    $pageSummary = school_setting($pdo,$pageKey === 'home' ? 'home_summary' : 'page_'.$pageKey.'_summary');
    $pageHeading = school_setting($pdo,'home_heading','Belajar teknologi lewat proyek nyata, lulus siap kerja.'); ?>
    <div class="admin-page-title"><div><p class="eyebrow">KONTEN WEBSITE</p><h1><?= e($pageContent[0]) ?></h1><p>Edit pengantar publik dan kelola foto halaman ini.</p></div></div>
    <div class="admin-page-layout">
      <section class="admin-panel">
        <h2>Konten halaman</h2>
        <form method="post" class="row wide-form"><?= $hid ?><input type="hidden" name="aksi" value="simpan_halaman"><input type="hidden" name="page_key" value="<?= e($pageKey) ?>">
          <?php if ($pageKey === 'home'): ?><label>Judul utama<input name="heading" maxlength="180" value="<?= e($pageHeading) ?>" required></label>
          <?php else: ?><label>Deskripsi singkat<input name="description" maxlength="500" value="<?= e($pageDescription) ?>" required></label><?php endif; ?>
          <label>Ringkasan tambahan<textarea name="summary" rows="6" maxlength="4000" placeholder="Informasi tambahan yang akan muncul di bagian atas halaman publik"><?= e($pageSummary) ?></textarea></label>
          <?php foreach (school_editable_page_sections()[$pageKey] ?? [] as $sectionTitle=>$sectionKey): ?>
            <label>Konten <?= e($sectionTitle) ?> <span class="muted">(maksimal 4.000 karakter; kosongkan untuk memakai konten bawaan)</span><textarea name="sections[<?= e($sectionKey) ?>]" rows="5" maxlength="4000" placeholder="Isi pengganti untuk bagian <?= e($sectionTitle) ?>"><?= e(school_setting($pdo,'page_'.$pageKey.'_section_'.$sectionKey)) ?></textarea></label>
          <?php endforeach; ?>
          <button class="btn">Simpan konten</button>
        </form>
      </section>
      <section class="admin-panel admin-preview-card">
        <h2>Pratinjau foto halaman</h2>
        <?= $pageKey !== 'home' ? school_photo($pageKey,'Foto halaman '.$pageContent[0],'lead','admin-page-photo') : '<img class="admin-page-photo" src="'.e(site_image_source('home','hero',photo_url('photo-1509062522246-3755977927d7'))).'" alt="Pratinjau banner beranda">' ?>
        <p class="muted">Ganti atau hapus foto melalui Galeri. Foto baru akan langsung ditampilkan di halaman publik.</p>
        <?php $photoSlotByPage=['home'=>'hero','profil'=>'lead','akademik'=>'lead','kesiswaan'=>'lead','informasi'=>'lead','fasilitas'=>'lead','bkk'=>'lead','kerjasama'=>'lead','ppdb'=>'lead']; ?>
        <a href="?t=gambar&amp;halaman=<?= e($pageKey) ?>&amp;lokasi=<?= e($pageKey.'|'.($photoSlotByPage[$pageKey] ?? 'lead')) ?>" class="btn ghost">Kelola foto halaman</a>
        <a href="<?= $pageKey === 'home' ? 'index.php' : 'halaman.php?m='.e($pageKey) ?>" target="_blank" rel="noopener" class="admin-text-link">Lihat halaman publik ↗</a>
      </section>
    </div>
    <?php if ($pageKey === 'kesiswaan'):
      $achievements = $pdo->query('SELECT * FROM prestasi ORDER BY tahun DESC,id DESC')->fetchAll(); ?>
      <section class="admin-panel">
        <h2>Prestasi siswa</h2>
        <form method="post" class="row" id="adminCreateForm"><?= $hid ?><input type="hidden" name="aksi" value="simpan_prestasi">
          <label>Tahun<input type="number" name="tahun" min="2000" max="2155" required></label>
          <label>Prestasi<input name="prestasi" maxlength="200" required></label>
          <label>Tingkat<input name="tingkat" maxlength="40" required></label>
          <button class="btn">Tambah prestasi</button>
        </form>
        <div class="tbl"><table><thead><tr><th>Tahun</th><th>Prestasi</th><th>Tingkat</th><th>Aksi</th></tr></thead><tbody>
          <?php foreach ($achievements as $achievement): ?><tr>
            <td><?= e($achievement['tahun']) ?></td><td><?= e($achievement['prestasi']) ?></td><td><?= e($achievement['tingkat']) ?></td>
            <td>
              <details><summary>Edit</summary>
                <form method="post" class="row"><?= $hid ?><input type="hidden" name="aksi" value="simpan_prestasi"><input type="hidden" name="id" value="<?= (int)$achievement['id'] ?>">
                  <label>Tahun<input type="number" name="tahun" min="2000" max="2155" value="<?= e($achievement['tahun']) ?>" required></label>
                  <label>Prestasi<input name="prestasi" maxlength="200" value="<?= e($achievement['prestasi']) ?>" required></label>
                  <label>Tingkat<input name="tingkat" maxlength="40" value="<?= e($achievement['tingkat']) ?>" required></label>
                  <button class="btn">Simpan perubahan</button>
                </form>
              </details>
              <?= hapusForm('prestasi',$achievement['id'],$t,$csrf) ?>
            </td>
          </tr><?php endforeach; ?>
        </tbody></table></div>
      </section>
    <?php endif; ?>
  <?php elseif (in_array($t,['guru','staff'],true)):
    $category = $t;
    $profiles = school_profiles($pdo,$category); ?>
    <div class="admin-page-title"><div><p class="eyebrow">PROFIL SEKOLAH</p><h1><?= $category==='guru'?'Guru':'Staff' ?></h1><p>Data profil yang ditampilkan di website publik.</p></div></div>
    <section class="admin-panel"><h2>Tambah <?= $category==='guru'?'guru':'staff' ?></h2>
      <p class="muted">Setelah profil disimpan, unggah foto melalui tombol “Unggah foto profil”. Foto yang dipasang akan tampil pada kartu dan halaman detail publik.</p>
      <form method="post" class="row" id="adminCreateForm"><?= $hid ?><input type="hidden" name="aksi" value="simpan_profil"><input type="hidden" name="category" value="<?= e($category) ?>">
        <label>Nama<input name="nama" maxlength="160" required></label><label>Jabatan / bidang<input name="jabatan" maxlength="160" required></label><label>Informasi profil<textarea name="informasi" rows="4"></textarea></label><button class="btn">Tambah profil</button>
      </form>
    </section>
    <h2>Profil tersimpan (<?= count($profiles) ?>)</h2>
    <?php if (!$profiles): ?><p class="muted">Belum ada profil. Tambahkan informasi yang telah diverifikasi sekolah.</p><?php endif; ?>
    <?php foreach ($profiles as $profile): ?>
      <article class="admin-panel profile-admin">
        <?= school_photo($category,'Foto profil '.$profile['nama'],'person-'.$profile['id'],'admin-profile-photo') ?>
        <p><a href="?t=gambar&amp;halaman=<?= e($category) ?>&amp;lokasi=<?= e($category.'|person-'.$profile['id']) ?>"><?= site_image_record($category,'person-'.$profile['id']) ? 'Ganti foto profil' : 'Unggah foto profil' ?></a></p>
        <form method="post" class="row"><?= $hid ?><input type="hidden" name="aksi" value="simpan_profil"><input type="hidden" name="category" value="<?= e($category) ?>"><input type="hidden" name="id" value="<?= (int)$profile['id'] ?>">
          <label>Nama<input name="nama" maxlength="160" value="<?= e($profile['nama']) ?>" required></label><label>Jabatan / bidang<input name="jabatan" maxlength="160" value="<?= e($profile['jabatan']) ?>" required></label><label>Informasi profil<textarea name="informasi" rows="4"><?= e($profile['informasi']) ?></textarea></label><button class="btn">Simpan perubahan</button>
        </form>
        <form method="post" onsubmit="return confirm('Hapus profil ini?')"><?= $hid ?><input type="hidden" name="aksi" value="hapus_profil"><input type="hidden" name="category" value="<?= e($category) ?>"><input type="hidden" name="id" value="<?= (int)$profile['id'] ?>"><button>Hapus profil</button></form>
      </article>
    <?php endforeach; ?>
  <?php elseif ($t === 'kepala'):
    $principal = school_profiles($pdo,'kepala')[0] ?? null;
    $greeting = school_setting($pdo,'kepala_sambutan'); ?>
    <div class="admin-page-title"><div><p class="eyebrow">PROFIL SEKOLAH</p><h1>Kepala Sekolah</h1><p>Profil kepala sekolah dan sambutan yang ditampilkan di halaman profil.</p></div></div>
    <section class="admin-panel"><h2><?= $principal?'Perbarui profil kepala sekolah':'Tambahkan profil kepala sekolah' ?></h2>
      <?php if ($principal): ?><?= school_photo('kepala-sekolah','Foto kepala sekolah','person-'.$principal['id'],'admin-profile-photo') ?><p><a href="?t=gambar&amp;halaman=kepala-sekolah&amp;lokasi=<?= e('kepala-sekolah|person-'.$principal['id']) ?>"><?= site_image_record('kepala-sekolah','person-'.$principal['id']) ? 'Ganti foto kepala sekolah' : 'Unggah foto kepala sekolah' ?></a></p><?php endif; ?>
      <form method="post" class="row" id="adminCreateForm"><?= $hid ?><input type="hidden" name="aksi" value="simpan_profil"><input type="hidden" name="category" value="kepala"><?php if ($principal): ?><input type="hidden" name="id" value="<?= (int)$principal['id'] ?>"><?php endif; ?>
        <label>Nama<input name="nama" maxlength="160" value="<?= e($principal['nama'] ?? '') ?>" required></label><label>Jabatan<input name="jabatan" maxlength="160" value="<?= e($principal['jabatan'] ?? '') ?>" placeholder="Isi jabatan sesuai data sekolah" required></label><label>Informasi profil<textarea name="informasi" rows="4"><?= e($principal['informasi'] ?? '') ?></textarea></label><button class="btn">Simpan profil</button>
      </form>
      <?php if ($principal): ?><form method="post" onsubmit="return confirm('Hapus profil kepala sekolah?')"><?= $hid ?><input type="hidden" name="aksi" value="hapus_profil"><input type="hidden" name="category" value="kepala"><input type="hidden" name="id" value="<?= (int)$principal['id'] ?>"><button>Hapus profil</button></form><?php endif; ?>
    </section>
    <section class="admin-panel"><h2>Sambutan kepala sekolah</h2><p class="muted">Simpan teks sambutan dan kelola foto sambutan tersendiri dari foto profil.</p>
      <form method="post" class="row"><?= $hid ?><input type="hidden" name="aksi" value="simpan_sambutan"><label>Teks sambutan<textarea name="sambutan" rows="10" placeholder="Teks sambutan resmi kepala sekolah"><?= e($greeting) ?></textarea></label><button class="btn">Simpan sambutan</button></form>
      <h3>Pratinjau</h3><div class="principal-message"><?= school_photo('kepala-sekolah','Foto kepala sekolah','sambutan','principal-photo') ?><div><h3><?= e($principal['nama'] ?? 'Nama kepala sekolah') ?></h3><p class="muted"><?= e($principal['jabatan'] ?? 'Jabatan') ?></p><p><?= nl2br(e($greeting ?: 'Teks sambutan akan muncul di sini.')) ?></p><a href="?t=gambar&amp;halaman=kepala-sekolah&amp;lokasi=<?= e('kepala-sekolah|sambutan') ?>"><?= site_image_record('kepala-sekolah','sambutan') ? 'Ganti foto sambutan' : 'Unggah foto sambutan' ?></a></div></div>
    </section>
  <?php elseif ($t === 'jurusan'):
    $courseFilter = $selectedCourse;
    $programSql = 'SELECT * FROM program_keahlian';
    if (in_array($courseFilter,['TKJ','RPL','DKV','PSPT'],true)) {
      $programStmt = $pdo->prepare($programSql.' WHERE kode=? ORDER BY FIELD(kode,"TKJ","RPL","DKV","PSPT")');
      $programStmt->execute([$courseFilter]);
      $programs = $programStmt->fetchAll();
    } else {
      $programs = $pdo->query($programSql.' ORDER BY FIELD(kode,"TKJ","RPL","DKV","PSPT")')->fetchAll();
    } ?>
    <div class="admin-page-title"><div><p class="eyebrow">PROGRAM KEAHLIAN</p><h1><?= $courseFilter ? e($courseFilter) : 'Data Jurusan' ?></h1><p>Informasi jurusan yang ditampilkan di situs publik.</p></div></div>
    <?php foreach ($programs as $program): $courseKey='jurusan-'.strtolower($program['kode']); ?>
      <section class="admin-panel course-admin"><h2><?= e($program['kode'].' — '.$program['nama']) ?></h2>
        <?= school_photo($courseKey,'Banner '.$program['nama'],'banner','admin-course-photo') ?>
        <p><a href="?t=gambar&amp;halaman=<?= e($courseKey) ?>&amp;lokasi=<?= e($courseKey.'|banner') ?>">Ganti banner jurusan</a></p>
        <p><a href="jurusan.php?kode=<?= e($program['kode']) ?>" target="_blank" rel="noopener">Pratinjau halaman publik</a></p>
        <form method="post" class="row wide-form"><?= $hid ?><input type="hidden" name="aksi" value="simpan_jurusan"><input type="hidden" name="kode" value="<?= e($program['kode']) ?>">
          <label>Nama program<input value="<?= e($program['nama']) ?>" disabled></label>
          <label>Deskripsi<textarea name="deskripsi" rows="3" required><?= e($program['deskripsi']) ?></textarea></label>
          <?php foreach (['kompetensi'=>'Kompetensi','fasilitas'=>'Fasilitas pembelajaran','kegiatan'=>'Kegiatan pembelajaran','proyek'=>'Proyek siswa','karier'=>'Peluang karier'] as $field=>$label): ?>
            <label><?= e($label) ?> (satu item per baris)<textarea name="<?= e($field) ?>" rows="4" required><?= e(implode("\n",explode('|',$program[$field]))) ?></textarea></label>
          <?php endforeach; ?>
          <button class="btn">Simpan <?= e($program['kode']) ?></button>
        </form>
      </section>
    <?php endforeach; ?>
  <?php elseif ($t === 'pengaturan'):
    $settings = ['nama'=>school_setting($pdo,'nama',$site['nama']),'alamat'=>school_setting($pdo,'alamat',$site['alamat']),'wa'=>school_setting($pdo,'wa',$site['wa']),'wa_tampil'=>school_setting($pdo,'wa_tampil',$site['wa_tampil']),'email'=>school_setting($pdo,'email',$site['email'])]; ?>
    <div class="admin-page-title"><div><p class="eyebrow">KONFIGURASI SITUS</p><h1>Pengaturan</h1><p>Informasi kontak dan identitas yang digunakan di situs publik.</p></div></div>
    <section class="admin-panel">
      <h2>Logo sekolah</h2>
      <p class="muted">Logo ini akan digunakan pada navbar website publik dan sidebar admin. Gunakan logo resmi sekolah, sebaiknya berkas PNG atau WebP berlatar transparan.</p>
      <?php if (site_image_record('branding','school-logo')): ?>
        <img class="settings-logo-preview" src="<?= e(site_image_source('branding','school-logo','')) ?>" alt="<?= e(site_image_alt('branding','school-logo','Logo resmi '.$site['nama'])) ?>">
      <?php else: ?>
        <p class="muted">Logo resmi belum diunggah; monogram sementara masih digunakan.</p>
      <?php endif; ?>
      <a class="btn" href="?t=gambar&amp;halaman=branding&amp;lokasi=branding%7Cschool-logo">Unggah atau ganti logo</a>
    </section>
    <section class="admin-panel"><form method="post" class="row"><?= $hid ?><input type="hidden" name="aksi" value="simpan_pengaturan">
      <label>Nama sekolah<input name="nama" maxlength="160" value="<?= e($settings['nama']) ?>" required></label><label>Alamat<input name="alamat" maxlength="500" value="<?= e($settings['alamat']) ?>"></label><label>Nomor WhatsApp (format internasional)<input name="wa" maxlength="30" value="<?= e($settings['wa']) ?>"></label><label>Nomor WhatsApp untuk tampilan<input name="wa_tampil" maxlength="40" value="<?= e($settings['wa_tampil']) ?>"></label><label>Email<input type="email" name="email" maxlength="180" value="<?= e($settings['email']) ?>"></label><button class="btn">Simpan pengaturan</button>
    </form></section>
  <?php elseif ($t === 'ppdb'):
    $rows = $pdo->query('SELECT * FROM ppdb_pendaftar ORDER BY id DESC')->fetchAll(); ?>
    <h2>Pendaftar PPDB (<?= count($rows) ?>)</h2>
    <div class="tbl"><table><tr><th>Waktu</th><th>Nama</th><th>Asal</th><th>HP</th><th>Jurusan</th><th>Status</th><th></th></tr>
    <?php foreach ($rows as $r): ?><tr data-status="<?= e($r['status']) ?>"><td><?= e($r['dibuat_pada']) ?></td><td><?= e($r['nama']) ?></td><td><?= e($r['asal_sekolah']) ?></td><td><?= e($r['hp']) ?></td><td><?= e($r['jurusan']) ?></td>
      <td><form method="post" class="inl"><?= $hid ?><input type="hidden" name="aksi" value="status"><input type="hidden" name="id" value="<?= $r['id'] ?>"><select name="status" onchange="this.form.submit()"><?php foreach (['baru','diproses','diterima','ditolak'] as $s): ?><option <?= $r['status']===$s?'selected':'' ?>><?= $s ?></option><?php endforeach; ?></select></form></td>
      <td><?= hapusForm('ppdb_pendaftar', $r['id'], $t, $csrf) ?></td></tr><?php endforeach; ?></table></div>
  <?php elseif ($t === 'dokumen'): ?>
    <h2>Unggah dokumen PPDB</h2>
    <p class="muted">Format PDF saja, maksimal <?= number_format($maxPdfUpload / 1048576, 2) ?> MB sesuai batas server (batas aplikasi 10 MB). Dokumen yang disimpan langsung tampil di halaman PPDB.</p>
    <form method="post" enctype="multipart/form-data" class="row pdf-form" id="adminCreateForm" data-pdf-form data-max-bytes="<?= $maxPdfUpload ?>">
      <?= $hid ?><input type="hidden" name="aksi" value="simpan_dokumen">
      <label>Judul dokumen<input name="judul" maxlength="180" required placeholder="Contoh: Panduan pendaftaran PPDB"></label>
      <label>Deskripsi<textarea name="deskripsi" rows="3" placeholder="Keterangan singkat dokumen"></textarea></label>
      <label>File PDF<input type="file" name="pdf" accept="application/pdf,.pdf" required data-pdf-input></label>
      <iframe class="admin-preview" title="Pratinjau PDF baru" hidden></iframe>
      <button class="btn">Unggah dokumen</button>
    </form>
    <h2>Dokumen tersimpan</h2>
    <?php $documents = $pdo->query('SELECT * FROM ppdb_dokumen ORDER BY diunggah_pada DESC')->fetchAll(); ?>
    <?php if (!$documents): ?><p class="muted">Belum ada dokumen.</p><?php else: ?>
      <?php foreach ($documents as $doc): ?>
        <article class="document-card">
          <h3><?= e($doc['judul']) ?></h3>
          <p class="document-meta"><?= e($doc['nama_asli']) ?> · <?= number_format($doc['ukuran'] / 1048576, 2) ?> MB · <?= e(tgl($doc['diunggah_pada'])) ?></p>
          <p><a href="dokumen.php?id=<?= (int)$doc['id'] ?>" target="_blank" rel="noopener">Lihat PDF tersimpan</a></p>
          <form method="post" enctype="multipart/form-data" class="row pdf-form" data-pdf-form data-max-bytes="<?= $maxPdfUpload ?>">
            <?= $hid ?><input type="hidden" name="aksi" value="simpan_dokumen"><input type="hidden" name="id" value="<?= (int)$doc['id'] ?>">
            <label>Judul<input name="judul" maxlength="180" value="<?= e($doc['judul']) ?>" required></label>
            <label>Deskripsi<textarea name="deskripsi" rows="2"><?= e($doc['deskripsi']) ?></textarea></label>
            <label>Ganti file PDF (kosongkan jika hanya mengubah keterangan)<input type="file" name="pdf" accept="application/pdf,.pdf" data-pdf-input></label>
            <iframe class="admin-preview" title="Pratinjau PDF pengganti" hidden></iframe>
            <button class="btn">Simpan perubahan</button>
          </form>
          <form method="post" class="inl" onsubmit="return confirm('Hapus dokumen ini?')"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="aksi" value="hapus_dokumen"><input type="hidden" name="id" value="<?= (int)$doc['id'] ?>"><button>Hapus dokumen</button></form>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  <?php elseif ($t === 'gambar'): ?>
    <h2>Kelola foto situs</h2>
    <p class="muted">Unggah JPEG, PNG, atau WebP maksimal <?= number_format($maxImageUpload / 1048576, 2) ?> MB sesuai batas server (batas aplikasi 5 MB). Foto ilustrasi bawaan akan diganti otomatis.</p>
    <?php if ($imagePageFilter !== ''): ?><p class="image-page-filter">Menampilkan lokasi foto untuk <strong><?= e($imagePageFilter) ?></strong> · <a href="?t=gambar">Tampilkan semua halaman</a></p><?php endif; ?>
    <form method="post" enctype="multipart/form-data" class="row image-form" id="adminCreateForm" data-image-form data-max-bytes="<?= $maxImageUpload ?>">
      <?= $hid ?><input type="hidden" name="aksi" value="simpan_gambar">
      <label>Lokasi foto<select name="target" required><?php $selectedImageSlot=isset($_GET['lokasi']) && is_string($_GET['lokasi']) ? $_GET['lokasi'] : ''; foreach ($imageSlots as $key=>$label): $pageGroup=explode('|',$key,2)[0]; if ($imagePageFilter !== '' && $pageGroup !== $imagePageFilter) continue; ?><option value="<?= e($key) ?>" <?= $selectedImageSlot===$key?'selected':'' ?>><?= e($label) ?></option><?php endforeach; ?></select></label>
      <label>Judul foto<input name="judul" maxlength="160" required></label>
      <label>Teks alternatif<input name="alt_text" maxlength="200" required placeholder="Deskripsi singkat untuk pembaca layar"></label>
      <label>File gambar<input type="file" name="gambar" accept="image/jpeg,image/png,image/webp" required data-image-input></label>
      <img class="admin-image-preview" alt="Pratinjau foto pilihan" hidden>
      <button class="btn">Unggah dan pasang</button>
    </form>
    <h2>Foto terpasang</h2>
    <?php $images = $pdo->query('SELECT * FROM site_images ORDER BY page_key, slot_key')->fetchAll(); ?>
    <?php if (!$images): ?><p class="muted">Belum ada foto yang diunggah. Situs menampilkan foto ilustrasi sementara.</p><?php else: ?>
      <div class="grid g2">
      <?php foreach ($images as $image): ?>
        <article class="document-card" data-image-page="<?= e(str_starts_with($image['page_key'],'jurusan-')?$image['page_key']:$image['page_key']) ?>">
          <img class="managed-image" src="gambar.php?id=<?= (int)$image['id'] ?>" alt="<?= e($image['alt_text']) ?>" loading="lazy">
          <h3><?= e($imageSlots[$image['page_key'].'|'.$image['slot_key']] ?? $image['judul']) ?></h3>
          <p class="document-meta"><?= e($image['judul']) ?> · <?= e($image['nama_file']) ?> · <?= number_format($image['ukuran'] / 1048576, 2) ?> MB · <?= e(tgl($image['diunggah_pada'])) ?></p>
          <form method="post" enctype="multipart/form-data" class="row image-form" data-image-form data-max-bytes="<?= $maxImageUpload ?>">
            <?= $hid ?><input type="hidden" name="aksi" value="simpan_gambar"><input type="hidden" name="target" value="<?= e($image['page_key'].'|'.$image['slot_key']) ?>">
            <label>Judul<input name="judul" maxlength="160" value="<?= e($image['judul']) ?>" required></label>
            <label>Teks alternatif<input name="alt_text" maxlength="200" value="<?= e($image['alt_text']) ?>" required></label>
            <label>Ganti gambar<input type="file" name="gambar" accept="image/jpeg,image/png,image/webp" required data-image-input></label>
            <img class="admin-image-preview" alt="Pratinjau gambar pengganti" hidden>
            <button class="btn">Ganti gambar</button>
          </form>
          <form method="post" class="inl" onsubmit="return confirm('Hapus foto ini dan gunakan kembali foto ilustrasi bawaan?')"><input type="hidden" name="csrf" value="<?= $csrf ?>"><input type="hidden" name="aksi" value="hapus_gambar"><input type="hidden" name="id" value="<?= (int)$image['id'] ?>"><button>Hapus foto</button></form>
        </article>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>
  <?php elseif ($t === 'lowongan'): ?>
    <h2>Tambah lowongan BKK</h2>
    <form method="post" class="row" id="adminCreateForm"><?= $hid ?><input type="hidden" name="aksi" value="simpan_lowongan">
      <label>Perusahaan<input name="perusahaan" maxlength="120" required></label>
      <label>Posisi<input name="posisi" maxlength="120" required></label>
      <label>Kualifikasi<textarea name="kualifikasi" rows="3" placeholder="Persyaratan atau kualifikasi kandidat"></textarea></label>
      <label>Batas pendaftaran<input type="date" name="batas" required></label>
      <button class="btn">Simpan lowongan</button>
    </form>
    <h2>Kelola lowongan</h2>
    <?php foreach ($pdo->query('SELECT * FROM lowongan ORDER BY batas DESC') as $job): ?>
      <article class="document-card">
        <p><span class="status-pill <?= $job['batas'] >= date('Y-m-d') ? 'active':'closed' ?>"><?= $job['batas'] >= date('Y-m-d') ? 'Aktif':'Ditutup' ?></span></p>
        <form method="post" class="row"><?= $hid ?><input type="hidden" name="aksi" value="simpan_lowongan"><input type="hidden" name="id" value="<?= (int)$job['id'] ?>">
          <label>Perusahaan<input name="perusahaan" maxlength="120" value="<?= e($job['perusahaan']) ?>" required></label>
          <label>Posisi<input name="posisi" maxlength="120" value="<?= e($job['posisi']) ?>" required></label>
          <label>Kualifikasi<textarea name="kualifikasi" rows="3"><?= e($job['kualifikasi'] ?? '') ?></textarea></label>
          <label>Batas pendaftaran<input type="date" name="batas" value="<?= e($job['batas']) ?>" required></label>
          <button class="btn">Simpan perubahan</button>
        </form>
        <?= hapusForm('lowongan', $job['id'], $t, $csrf) ?>
      </article>
    <?php endforeach; ?>
  <?php elseif ($t === 'mitra'): ?>
    <h2>Tambah perusahaan mitra</h2>
    <form method="post" class="row" id="adminCreateForm"><?= $hid ?><input type="hidden" name="aksi" value="simpan_mitra">
      <label>Nama PT / perusahaan<input name="nama" maxlength="150" required></label>
      <label>Bidang usaha<input name="bidang_usaha" maxlength="160"></label>
      <label>Jenis kerja sama<input name="jenis_kerjasama" maxlength="160" placeholder="Contoh: PKL, rekrutmen, kelas industri"></label>
      <label>Deskripsi<textarea name="deskripsi" rows="3"></textarea></label>
      <button class="btn">Simpan mitra</button>
    </form>
    <h2>Kelola perusahaan mitra</h2>
    <?php foreach ($pdo->query('SELECT * FROM mitra ORDER BY nama') as $partner): ?>
      <article class="document-card">
        <?php if ($partner['id']): $partnerLogo=site_image_record('kerjasama','partner-'.$partner['id']); ?>
          <?= $partnerLogo ? '<img class="partner-logo admin-partner-logo" src="'.e(site_image_source('kerjasama','partner-'.$partner['id'],'')).'" alt="'.e(site_image_alt('kerjasama','partner-'.$partner['id'],'Logo '.$partner['nama'])).'">' : '<span class="partner-mark" aria-hidden="true">'.e(mb_strtoupper(mb_substr($partner['nama'],0,2))).'</span>' ?>
          <p><a href="?t=gambar&amp;halaman=kerjasama&amp;lokasi=<?= e('kerjasama|partner-'.$partner['id']) ?>">Unggah atau ganti logo resmi</a></p>
        <?php endif; ?>
        <form method="post" class="row"><?= $hid ?><input type="hidden" name="aksi" value="simpan_mitra"><input type="hidden" name="id" value="<?= (int)$partner['id'] ?>">
          <label>Nama PT / perusahaan<input name="nama" maxlength="150" value="<?= e($partner['nama']) ?>" required></label>
          <label>Bidang usaha<input name="bidang_usaha" maxlength="160" value="<?= e($partner['bidang_usaha'] ?? '') ?>"></label>
          <label>Jenis kerja sama<input name="jenis_kerjasama" maxlength="160" value="<?= e($partner['jenis_kerjasama'] ?? '') ?>"></label>
          <label>Deskripsi<textarea name="deskripsi" rows="3"><?= e($partner['deskripsi'] ?? '') ?></textarea></label>
          <button class="btn">Simpan perubahan</button>
        </form>
        <?= hapusForm('mitra', $partner['id'], $t, $csrf) ?>
      </article>
    <?php endforeach; ?>
  <?php elseif ($t === 'berita'): ?>
    <h2>Tambah berita</h2>
    <form method="post" class="row" id="adminCreateForm"><?= $hid ?><input type="hidden" name="aksi" value="tambah_berita"><input name="judul" placeholder="Judul" required><textarea name="ringkasan" rows="3" placeholder="Ringkasan" required></textarea><input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required><button class="btn">Simpan berita</button></form>
    <div class="tbl"><table><thead><tr><th>Tanggal</th><th>Judul</th><th>Aksi</th></tr></thead><tbody><?php foreach ($pdo->query('SELECT * FROM berita ORDER BY tanggal DESC') as $r): ?><tr><td><?= tgl($r['tanggal']) ?></td><td><?= e($r['judul']) ?></td><td><details><summary>Edit</summary><form method="post" class="row"><?= $hid ?><input type="hidden" name="aksi" value="simpan_berita"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><label>Judul<input name="judul" value="<?= e($r['judul']) ?>" required></label><label>Ringkasan<textarea name="ringkasan" rows="3" required><?= e($r['ringkasan']) ?></textarea></label><label>Tanggal<input type="date" name="tanggal" value="<?= e($r['tanggal']) ?>" required></label><button class="btn">Simpan perubahan</button></form></details><a href="?t=gambar&amp;halaman=informasi&amp;lokasi=<?= e('informasi|news-'.$r['id']) ?>">Ganti foto berita</a><?= hapusForm('berita', $r['id'], $t, $csrf) ?></td></tr><?php endforeach; ?></tbody></table></div>
  <?php elseif ($t === 'pengumuman'): ?>
    <h2>Tambah pengumuman</h2>
    <form method="post" class="row" id="adminCreateForm"><?= $hid ?><input type="hidden" name="aksi" value="tambah_pengumuman"><input name="isi" placeholder="Isi pengumuman" required maxlength="300"><input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required><button class="btn">Simpan pengumuman</button></form>
    <div class="tbl"><table><thead><tr><th>Tanggal</th><th>Isi</th><th>Aksi</th></tr></thead><tbody><?php foreach ($pdo->query('SELECT * FROM pengumuman ORDER BY tanggal DESC') as $r): ?><tr><td><?= tgl($r['tanggal']) ?></td><td><?= e($r['isi']) ?></td><td><details><summary>Edit</summary><form method="post" class="row"><?= $hid ?><input type="hidden" name="aksi" value="simpan_pengumuman"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><label>Isi<input name="isi" maxlength="300" value="<?= e($r['isi']) ?>" required></label><label>Tanggal<input type="date" name="tanggal" value="<?= e($r['tanggal']) ?>" required></label><button class="btn">Simpan perubahan</button></form></details><?= hapusForm('pengumuman', $r['id'], $t, $csrf) ?></td></tr><?php endforeach; ?></tbody></table></div>
  <?php else: ?>
    <h2>Tambah agenda</h2>
    <form method="post" class="row" id="adminCreateForm"><?= $hid ?><input type="hidden" name="aksi" value="tambah_agenda"><input type="date" name="tanggal" required><input name="kegiatan" placeholder="Kegiatan" required><input name="tempat" placeholder="Tempat" required><button class="btn">Simpan agenda</button></form>
    <div class="tbl"><table><thead><tr><th>Tanggal</th><th>Kegiatan</th><th>Tempat</th><th>Aksi</th></tr></thead><tbody><?php foreach ($pdo->query('SELECT * FROM agenda ORDER BY tanggal DESC') as $r): ?><tr><td><?= tgl($r['tanggal']) ?></td><td><?= e($r['kegiatan']) ?></td><td><?= e($r['tempat']) ?></td><td><details><summary>Edit</summary><form method="post" class="row"><?= $hid ?><input type="hidden" name="aksi" value="simpan_agenda"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><label>Tanggal<input type="date" name="tanggal" value="<?= e($r['tanggal']) ?>" required></label><label>Kegiatan<input name="kegiatan" value="<?= e($r['kegiatan']) ?>" required></label><label>Tempat<input name="tempat" value="<?= e($r['tempat']) ?>" required></label><button class="btn">Simpan perubahan</button></form></details><?= hapusForm('agenda', $r['id'], $t, $csrf) ?></td></tr><?php endforeach; ?></tbody></table></div>
  <?php endif; ?>
      </div>
    </main>
  </div>
<?php endif; ?>
</div>
<script>
const sidebar = document.getElementById('adminSidebar');
const adminSearch = document.querySelector('[data-admin-search]');
const adminFilter = document.querySelector('[data-admin-filter]');
const adminCards = Array.from(document.querySelectorAll('.admin-content .document-card, .admin-content .profile-admin, .admin-content .course-admin, .admin-content .dashboard-card'));
const adminTables = Array.from(document.querySelectorAll('.admin-content .tbl table')).map(function (table) {
  const rows = Array.from(table.querySelectorAll('tbody tr'));
  const pager = document.createElement('nav');
  pager.className = 'admin-pagination';
  pager.setAttribute('aria-label','Navigasi tabel');
  const previous = document.createElement('button');
  previous.type = 'button';
  previous.textContent = 'Sebelumnya';
  const summary = document.createElement('span');
  const next = document.createElement('button');
  next.type = 'button';
  next.textContent = 'Berikutnya';
  pager.append(previous,summary,next);
  table.closest('.tbl').after(pager);
  let currentPage = 1;
  previous.addEventListener('click',function(){ currentPage--; applyAdminFilters(false); });
  next.addEventListener('click',function(){ currentPage++; applyAdminFilters(false); });
  return {table:table,rows:rows,pager:pager,previous:previous,next:next,summary:summary,page:()=>currentPage,setPage:value=>{currentPage=value;}};
});
function rowMatches(row,query,status) {
  const matchesText = !query || row.innerText.toLocaleLowerCase('id').includes(query);
  const matchesStatus = !status || row.dataset.status === status;
  return matchesText && matchesStatus;
}
function applyAdminFilters(resetPages = true) {
  const query = (adminSearch?.value || '').trim().toLocaleLowerCase('id');
  const status = adminFilter?.value || '';
  const imagePage = document.querySelector('[data-image-page-filter]')?.value || '';
  adminTables.forEach(function (view) {
    if (resetPages) view.setPage(1);
    const matches = view.rows.filter(function(row){return rowMatches(row,query,status);});
    const pageCount = Math.max(1,Math.ceil(matches.length / 10));
    const currentPage = Math.min(Math.max(1,view.page()),pageCount);
    view.setPage(currentPage);
    const visibleRows = new Set(matches.slice((currentPage-1)*10,currentPage*10));
    view.rows.forEach(function(row){row.hidden = !visibleRows.has(row);});
    view.pager.hidden = view.rows.length <= 10;
    view.previous.disabled = currentPage <= 1;
    view.next.disabled = currentPage >= pageCount;
    const first = matches.length ? (currentPage-1)*10+1 : 0;
    const last = Math.min(currentPage*10,matches.length);
    view.summary.textContent = 'Menampilkan '+first+'-'+last+' dari '+matches.length;
  });
  adminCards.forEach(function (card) {
    const matchesText = !query || card.innerText.toLocaleLowerCase('id').includes(query);
    const matchesPage = !imagePage || card.dataset.imagePage === imagePage;
    card.hidden = !matchesText || !matchesPage;
  });
}
adminSearch?.addEventListener('input',function(){applyAdminFilters(true);});
adminFilter?.addEventListener('change',function(){applyAdminFilters(true);});
document.querySelector('[data-image-page-filter]')?.addEventListener('change',function () {
  const url = new URL(window.location.href);
  if (this.value) url.searchParams.set('halaman',this.value);
  else url.searchParams.delete('halaman');
  url.searchParams.delete('lokasi');
  window.location.assign(url.toString());
});
applyAdminFilters(false);
const exportButton = document.querySelector('[data-admin-export]');
const exportTable = document.querySelector('.admin-content .tbl table');
if (exportButton && exportTable) {
  exportButton.hidden = false;
  exportButton.addEventListener('click', function () {
    const query = (adminSearch?.value || '').trim().toLocaleLowerCase('id');
    const status = adminFilter?.value || '';
    const exportRows = Array.from(exportTable.querySelectorAll('thead tr')).concat(
      Array.from(exportTable.querySelectorAll('tbody tr')).filter(function(row){return rowMatches(row,query,status);})
    );
    const rows = exportRows.map(function (row) {
      return Array.from(row.querySelectorAll('th,td')).map(function (cell) {
        const text = cell.innerText.trim().replace(/\s+/g,' ');
        return '"' + text.replace(/"/g,'""') + '"';
      }).join(',');
    });
    const blob = new Blob(['\uFEFF' + rows.join('\r\n')], {type:'text/csv;charset=utf-8'});
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'ekspor-<?= e(preg_replace('/[^a-z0-9-]/i','-', $t)) ?>.csv';
    link.click();
    URL.revokeObjectURL(link.href);
  });
}
document.querySelector('[data-scroll-create]')?.addEventListener('click', function (event) {
  const form = document.getElementById('adminCreateForm');
  if (!form) return;
  event.preventDefault();
  form.scrollIntoView({behavior:'smooth',block:'center'});
  form.querySelector('input:not([type=hidden]),select,textarea')?.focus({preventScroll:true});
});
document.querySelector('[data-sidebar-open]')?.addEventListener('click', function () {
  sidebar.classList.add('drawer-open');
  document.body.classList.add('drawer-visible');
  this.setAttribute('aria-expanded','true');
});
document.querySelectorAll('[data-sidebar-close]').forEach(function (element) {
  element.addEventListener('click', closeSidebar);
});
function closeSidebar() {
  if (!sidebar) return;
  sidebar.classList.remove('drawer-open');
  document.body.classList.remove('drawer-visible');
  document.querySelector('[data-sidebar-open]')?.setAttribute('aria-expanded','false');
}
document.addEventListener('keydown', function (event) {
  if (event.key === 'Escape') closeSidebar();
});
document.querySelector('[data-sidebar-collapse]')?.addEventListener('click', function () {
  document.body.classList.toggle('sidebar-collapsed');
  const collapsed = document.body.classList.contains('sidebar-collapsed');
  this.setAttribute('aria-label',collapsed?'Perluas sidebar':'Ciutkan sidebar');
  this.textContent = collapsed?'›':'‹';
});
document.querySelectorAll('[data-pdf-form]').forEach(function (form) {
  const input = form.querySelector('[data-pdf-input]');
  const preview = form.querySelector('.admin-preview');
  const maxBytes = Number(form.dataset.maxBytes);
  let previousUrl = '';
  input.addEventListener('change', function () {
    if (previousUrl) URL.revokeObjectURL(previousUrl);
    const file = input.files[0];
    if (!file) { preview.hidden = true; preview.removeAttribute('src'); return; }
    if (file.type !== 'application/pdf' || file.size > maxBytes) {
      preview.hidden = true;
      alert(file.type !== 'application/pdf' ? 'Pilih file PDF.' : 'Ukuran file maksimal '+(maxBytes / 1048576).toFixed(2)+' MB sesuai batas server.');
      input.value = '';
      return;
    }
    previousUrl = URL.createObjectURL(file);
    preview.src = previousUrl;
    preview.hidden = false;
  });
});
document.querySelectorAll('[data-image-form]').forEach(function (form) {
  const input = form.querySelector('[data-image-input]');
  const preview = form.querySelector('.admin-image-preview');
  const maxBytes = Number(form.dataset.maxBytes);
  let previousUrl = '';
  input.addEventListener('change', function () {
    if (previousUrl) URL.revokeObjectURL(previousUrl);
    const file = input.files[0];
    if (!file) { preview.hidden = true; preview.removeAttribute('src'); return; }
    if (!['image/jpeg','image/png','image/webp'].includes(file.type) || file.size > maxBytes) {
      preview.hidden = true;
      alert('Pilih gambar JPEG, PNG, atau WebP dengan ukuran maksimal '+(maxBytes / 1048576).toFixed(2)+' MB sesuai batas server.');
      input.value = '';
      return;
    }
    previousUrl = URL.createObjectURL(file);
    preview.src = previousUrl;
    preview.hidden = false;
  });
});
</script>
</body></html>
