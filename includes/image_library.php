<?php
require_once __DIR__.'/upload_limits.php';

function ensure_site_images_table(PDO $pdo): void {
  $pdo->exec("CREATE TABLE IF NOT EXISTS site_images (
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
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function site_image_record(string $page, string $slot): ?array {
  global $pdo;
  if (!$pdo) return null;
  static $initialized = false;
  static $available = true;
  static $cache = [];
  if (!$initialized) {
    $initialized = true;
    try {
      ensure_site_images_table($pdo);
    } catch (PDOException $error) {
      $available = false;
      error_log('Tabel gambar situs tidak tersedia: '.$error->getMessage());
      return null;
    }
  }
  if (!$available) return null;
  $key = $page.':'.$slot;
  if (array_key_exists($key, $cache)) return $cache[$key];
  try {
    $stmt = $pdo->prepare('SELECT id, alt_text, nama_file FROM site_images WHERE page_key=? AND slot_key=?');
    $stmt->execute([$page, $slot]);
    $record = $stmt->fetch();
    $cache[$key] = $record ?: null;
    return $cache[$key];
  } catch (PDOException $error) {
    error_log('Gambar situs gagal dimuat: '.$error->getMessage());
    return null;
  }
}

function site_image_source(string $page, string $slot, string $fallback): string {
  $record = site_image_record($page, $slot);
  return $record ? 'gambar.php?id='.(int)$record['id'] : $fallback;
}

function site_image_alt(string $page, string $slot, string $fallback): string {
  $record = site_image_record($page, $slot);
  return $record ? $record['alt_text'] : $fallback;
}

function hapus_gambar_lokasi(PDO $pdo, string $page, string $slot): void {
  $stmt = $pdo->prepare('SELECT id, nama_file FROM site_images WHERE page_key=? AND slot_key=?');
  $stmt->execute([$page, $slot]);
  $image = $stmt->fetch();
  if (!$image) return;
  $pdo->prepare('DELETE FROM site_images WHERE id=?')->execute([(int)$image['id']]);
  $path = dirname(__DIR__).'/uploads/site-images/'.basename($image['nama_file']);
  if (is_file($path) && !unlink($path)) error_log('Gagal menghapus file gambar: '.$path);
}

function simpan_gambar_situs(PDO $pdo, array $file, string $page, string $slot, string $judul, string $alt): void {
  $allowed = [
    'branding' => ['school-logo'],
    'home' => ['hero','hero-card','activity-1','activity-2','activity-3','program-tkj','program-rpl','program-dkv','program-pspt'],
    'profil' => ['lead'],
    'akademik' => ['lead'],
    'kesiswaan' => ['lead','activity-1','activity-2','activity-3','activity-4','activity-5','activity-6','achievement'],
    'informasi' => ['lead','news-1','news-2','news-3','news-4','news-5','news-6'],
    'galeri' => ['gallery-1','gallery-2','gallery-3','gallery-4','gallery-5','gallery-6'],
    'fasilitas' => ['lead','facility-1','facility-2','facility-3','facility-4','facility-5','facility-6'],
    'bkk' => ['lead'],
    'kerjasama' => ['lead'],
    'ppdb' => ['lead'],
    'kontak' => ['lead'],
    'guru' => [],
    'staff' => [],
    'kepala-sekolah' => ['sambutan'],
  ];
  $isProfilePhoto = in_array($page, ['guru','staff','kepala-sekolah'], true) && preg_match('/^person-[1-9][0-9]*$/', $slot);
  $isProgramPhoto = preg_match('/^jurusan-(tkj|rpl|dkv|pspt)$/', $page) && ($slot === 'banner' || preg_match('/^gallery-[1-6]$/', $slot));
  $isNewsPhoto = $page === 'informasi' && preg_match('/^news-([1-9][0-9]*)$/', $slot, $newsMatch);
  $isPartnerLogo = $page === 'kerjasama' && preg_match('/^partner-([1-9][0-9]*)$/', $slot, $partnerMatch);
  if ($isNewsPhoto) {
    $stmt = $pdo->prepare('SELECT id FROM berita WHERE id=?');
    $stmt->execute([(int)$newsMatch[1]]);
    $isNewsPhoto = (bool)$stmt->fetchColumn();
  }
  if ($isPartnerLogo) {
    $stmt = $pdo->prepare('SELECT id FROM mitra WHERE id=?');
    $stmt->execute([(int)$partnerMatch[1]]);
    $isPartnerLogo = (bool)$stmt->fetchColumn();
  }
  if (!$isProfilePhoto && !$isProgramPhoto && !$isNewsPhoto && !$isPartnerLogo && !in_array($slot, $allowed[$page] ?? [], true)) throw new RuntimeException('Pilih lokasi gambar yang tersedia.');
  if (trim($judul) === '' || strlen($judul) > 160 || trim($alt) === '' || strlen($alt) > 200) {
    throw new RuntimeException('Judul dan teks alternatif gambar wajib diisi dengan panjang yang sesuai.');
  }
  $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;
  if (in_array($uploadError, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
    $maxSize = maximum_upload_size(5 * 1024 * 1024);
    throw new RuntimeException('Gambar melebihi batas unggah server saat ini (maksimal '.number_format($maxSize / 1048576, 2).' MB).');
  }
  if ($uploadError !== UPLOAD_ERR_OK) {
    throw new RuntimeException('Pilih gambar yang valid. Format JPEG, PNG, atau WebP; ukuran maksimal 5 MB.');
  }
  $maxSize = maximum_upload_size(5 * 1024 * 1024);
  if (($file['size'] ?? 0) < 1 || $file['size'] > $maxSize || !is_uploaded_file($file['tmp_name'] ?? '')) {
    throw new RuntimeException('Gambar melebihi batas unggah server saat ini (maksimal '.number_format($maxSize / 1048576, 2).' MB) atau bukan unggahan valid.');
  }
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  $mime = $finfo->file($file['tmp_name']);
  $extensions = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
  if (!isset($extensions[$mime])) {
    throw new RuntimeException('Jenis gambar tidak valid. Hanya JPEG, PNG, atau WebP yang diterima.');
  }
  $dimensions = @getimagesize($file['tmp_name']);
  if (!$dimensions || $dimensions['mime'] !== $mime) throw new RuntimeException('File gambar rusak atau tidak dapat dibaca.');

  $stmt = $pdo->prepare('SELECT nama_file FROM site_images WHERE page_key=? AND slot_key=?');
  $stmt->execute([$page, $slot]);
  $previous = $stmt->fetchColumn();
  $folder = dirname(__DIR__).'/uploads/site-images';
  if (!is_dir($folder) && !mkdir($folder, 0755, true) && !is_dir($folder)) {
    throw new RuntimeException('Folder penyimpanan gambar tidak dapat dibuat. Periksa izin server.');
  }
  $filename = bin2hex(random_bytes(16)).'.'.$extensions[$mime];
  $destination = $folder.'/'.$filename;
  if (!move_uploaded_file($file['tmp_name'], $destination)) {
    throw new RuntimeException('Gambar gagal disimpan. Periksa izin folder penyimpanan server.');
  }
  try {
    $pdo->prepare('INSERT INTO site_images (page_key, slot_key, judul, alt_text, nama_file, mime_type, ukuran) VALUES (?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE judul=VALUES(judul), alt_text=VALUES(alt_text), nama_file=VALUES(nama_file), mime_type=VALUES(mime_type), ukuran=VALUES(ukuran), diunggah_pada=CURRENT_TIMESTAMP')
      ->execute([$page, $slot, trim($judul), trim($alt), $filename, $mime, (int)$file['size']]);
  } catch (Throwable $error) {
    if (is_file($destination) && !unlink($destination)) error_log('Gagal membersihkan unggahan gambar: '.$destination);
    throw $error;
  }
  if ($previous) {
    $oldPath = $folder.'/'.basename($previous);
    if (is_file($oldPath) && !unlink($oldPath)) error_log('Gagal menghapus gambar lama: '.$oldPath);
  }
}

function hapus_gambar_situs(PDO $pdo, int $id): void {
  $stmt = $pdo->prepare('SELECT nama_file FROM site_images WHERE id=?');
  $stmt->execute([$id]);
  $image = $stmt->fetch();
  if (!$image) throw new RuntimeException('Gambar tidak ditemukan.');
  $pdo->prepare('DELETE FROM site_images WHERE id=?')->execute([$id]);
  $path = dirname(__DIR__).'/uploads/site-images/'.basename($image['nama_file']);
  if (is_file($path) && !unlink($path)) error_log('Gagal menghapus file gambar: '.$path);
}
