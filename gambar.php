<?php
require 'includes/db.php';
require 'includes/image_library.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || !$pdo) {
  http_response_code($pdo ? 404 : 503);
  exit($pdo ? 'Gambar tidak ditemukan.' : 'Layanan gambar sedang tidak tersedia.');
}
ensure_site_images_table($pdo);
$stmt = $pdo->prepare('SELECT nama_file, mime_type FROM site_images WHERE id=?');
$stmt->execute([$id]);
$image = $stmt->fetch();
$path = $image ? __DIR__.'/uploads/site-images/'.basename($image['nama_file']) : '';
if (!$image || !is_file($path)) {
  http_response_code(404);
  exit('Gambar tidak ditemukan atau file tidak tersedia.');
}
if (!in_array($image['mime_type'], ['image/jpeg','image/png','image/webp'], true)) {
  http_response_code(415);
  exit('Jenis gambar tidak didukung.');
}
header('Content-Type: '.$image['mime_type']);
header('Content-Length: '.filesize($path));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=86400');
readfile($path);
