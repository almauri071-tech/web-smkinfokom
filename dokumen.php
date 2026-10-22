<?php
require 'includes/db.php';
require 'includes/pdf_documents.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || !$pdo) {
  http_response_code($pdo ? 404 : 503);
  exit($pdo ? 'Dokumen tidak ditemukan.' : 'Layanan dokumen sedang tidak tersedia.');
}

ensure_pdf_documents_table($pdo);
$stmt = $pdo->prepare('SELECT judul, nama_file FROM ppdb_dokumen WHERE id=?');
$stmt->execute([$id]);
$document = $stmt->fetch();
$path = $document ? __DIR__.'/uploads/ppdb-documents/'.basename($document['nama_file']) : '';
if (!$document || !is_file($path)) {
  http_response_code(404);
  exit('Dokumen tidak ditemukan atau file tidak tersedia.');
}

$filename = preg_replace('/[^A-Za-z0-9._-]/', '_', $document['judul']).'.pdf';
header('Content-Type: application/pdf');
header('Content-Length: '.filesize($path));
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: '.(isset($_GET['download']) ? 'attachment' : 'inline').'; filename="'.$filename.'"');
readfile($path);
