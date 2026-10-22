<?php
// Konfigurasi database. Sesuaikan dengan server Anda.
const DB_HOST = 'localhost';
const DB_NAME = 'smk_infokom';
const DB_USER = 'root';
const DB_PASS = '';

$pdo = null;
try {
  $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);
} catch (PDOException $e) {
  error_log('DB gagal: '.$e->getMessage()); // situs tetap tampil dengan data contoh
}

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function tgl($d, $pendek = true){
  $b = ['','Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
  $t = strtotime($d); return date('d', $t).' '.$b[(int)date('n', $t)].' '.date('Y', $t);
}
function ambil($sql, $p = []){
  global $pdo; if (!$pdo) return null;
  try { $s = $pdo->prepare($sql); $s->execute($p); return $s->fetchAll(); } catch (PDOException $e) { return null; }
}
