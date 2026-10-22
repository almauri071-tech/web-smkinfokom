<?php
require_once __DIR__.'/upload_limits.php';

function ensure_pdf_documents_table(PDO $pdo): void {
  $pdo->exec("CREATE TABLE IF NOT EXISTS ppdb_dokumen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(180) NOT NULL,
    deskripsi TEXT NOT NULL,
    nama_file VARCHAR(100) NOT NULL,
    nama_asli VARCHAR(255) NOT NULL DEFAULT '',
    ukuran INT UNSIGNED NOT NULL,
    diunggah_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
  if (!$pdo->query("SHOW COLUMNS FROM ppdb_dokumen LIKE 'nama_asli'")->fetch()) {
    $pdo->exec("ALTER TABLE ppdb_dokumen ADD nama_asli VARCHAR(255) NOT NULL DEFAULT '' AFTER nama_file");
    $pdo->exec('UPDATE ppdb_dokumen SET nama_asli=nama_file WHERE nama_asli=""');
  }
}

function simpan_dokumen_pdf(PDO $pdo, array $file, string $judul, string $deskripsi, ?int $id = null): void {
  $judul = trim($judul);
  if ($judul === '' || strlen($judul) > 180) {
    throw new RuntimeException('Judul dokumen wajib diisi dan maksimal 180 karakter.');
  }
  if ($id !== null) {
    $stmt = $pdo->prepare('SELECT * FROM ppdb_dokumen WHERE id = ?');
    $stmt->execute([$id]);
    $sebelumnya = $stmt->fetch();
    if (!$sebelumnya) throw new RuntimeException('Dokumen yang akan diganti tidak ditemukan.');
  }
  $uploadError = $file['error'] ?? UPLOAD_ERR_NO_FILE;
  if ($uploadError === UPLOAD_ERR_NO_FILE) {
    if ($id === null) throw new RuntimeException('Pilih file PDF untuk diunggah.');
    $pdo->prepare('UPDATE ppdb_dokumen SET judul=?, deskripsi=? WHERE id=?')->execute([$judul, trim($deskripsi), $id]);
    return;
  }
  if (in_array($uploadError, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
    $maxSize = maximum_upload_size(10 * 1024 * 1024);
    throw new RuntimeException('PDF melebihi batas unggah server saat ini (maksimal '.number_format($maxSize / 1048576, 2).' MB).');
  }
  if ($uploadError !== UPLOAD_ERR_OK) {
    throw new RuntimeException('File gagal diunggah. Periksa koneksi atau batas unggah server.');
  }
  $maxSize = maximum_upload_size(10 * 1024 * 1024);
  if (($file['size'] ?? 0) < 1 || $file['size'] > $maxSize) {
    throw new RuntimeException('Ukuran PDF melebihi batas unggah server saat ini (maksimal '.number_format($maxSize / 1048576, 2).' MB).');
  }
  if (!is_uploaded_file($file['tmp_name'] ?? '')) {
    throw new RuntimeException('Berkas unggahan tidak valid.');
  }
  $finfo = new finfo(FILEINFO_MIME_TYPE);
  if ($finfo->file($file['tmp_name']) !== 'application/pdf' || file_get_contents($file['tmp_name'], false, null, 0, 5) !== '%PDF-') {
    throw new RuntimeException('Jenis file tidak valid. Hanya dokumen PDF yang dapat diunggah.');
  }

  $folder = dirname(__DIR__).'/uploads/ppdb-documents';
  if (!is_dir($folder) && !mkdir($folder, 0755, true) && !is_dir($folder)) {
    throw new RuntimeException('Folder penyimpanan dokumen tidak dapat dibuat. Periksa izin folder server.');
  }
  $namaFile = bin2hex(random_bytes(16)).'.pdf';
  $namaAsli = str_replace('\\', '/', $file['name'] ?? 'dokumen.pdf');
  $namaAsli = basename($namaAsli);
  $namaAsli = trim(mb_substr($namaAsli, 0, 255, 'UTF-8')) ?: 'dokumen.pdf';
  $tujuan = $folder.'/'.$namaFile;
  if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
    throw new RuntimeException('PDF gagal disimpan. Periksa izin folder penyimpanan server.');
  }

  try {
    if ($id !== null) {
      $pdo->prepare('UPDATE ppdb_dokumen SET judul=?, deskripsi=?, nama_file=?, nama_asli=?, ukuran=?, diunggah_pada=CURRENT_TIMESTAMP WHERE id=?')
        ->execute([$judul, trim($deskripsi), $namaFile, $namaAsli, (int)$file['size'], $id]);
    } else {
      $pdo->prepare('INSERT INTO ppdb_dokumen (judul, deskripsi, nama_file, nama_asli, ukuran) VALUES (?,?,?,?,?)')
        ->execute([$judul, trim($deskripsi), $namaFile, $namaAsli, (int)$file['size']]);
    }
  } catch (Throwable $error) {
    if (is_file($tujuan) && !unlink($tujuan)) error_log('Gagal membersihkan unggahan PDF: '.$tujuan);
    throw $error;
  }

  if ($id !== null && !empty($sebelumnya['nama_file'])) {
    $fileLama = $folder.'/'.basename($sebelumnya['nama_file']);
    if (is_file($fileLama) && !unlink($fileLama)) error_log('Gagal menghapus PDF lama: '.$fileLama);
  }
}

function hapus_dokumen_pdf(PDO $pdo, int $id): void {
  $stmt = $pdo->prepare('SELECT nama_file FROM ppdb_dokumen WHERE id=?');
  $stmt->execute([$id]);
  $dokumen = $stmt->fetch();
  if (!$dokumen) throw new RuntimeException('Dokumen tidak ditemukan.');
  $pdo->prepare('DELETE FROM ppdb_dokumen WHERE id=?')->execute([$id]);
  $file = dirname(__DIR__).'/uploads/ppdb-documents/'.basename($dokumen['nama_file']);
  if (is_file($file) && !unlink($file)) error_log('Gagal menghapus file PDF: '.$file);
}
