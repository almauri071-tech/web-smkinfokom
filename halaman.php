<?php
require 'includes/data.php';
$cur = $_GET['m'] ?? '';
if (!isset($menu[$cur])) { http_response_code(404); $title='Halaman tidak ditemukan'; $cur=''; require 'includes/header.php';
  echo '<div class="wrap sec"><h1>Halaman tidak ditemukan</h1><p><a href="index.php">Kembali ke beranda</a></p></div>'; require 'includes/footer.php'; exit; }
[$judul, $desk, $bagian] = $menu[$cur];
$title = $judul;
$profileCategory = in_array($cur, ['guru','staff','kepala-sekolah'], true) ? ($cur === 'kepala-sekolah' ? 'kepala' : $cur) : null;
$profileDetail = null;
if ($profileCategory && isset($_GET['id'])) {
  if ($pdo) {
    $profileStmt = $pdo->prepare('SELECT id, category, nama, jabatan, informasi, urutan FROM school_profiles WHERE id=? AND category=?');
    $profileStmt->execute([(int)$_GET['id'], $profileCategory]);
    $profileDetail = $profileStmt->fetch();
  }
  if (!$profileDetail) {
    http_response_code(404);
    $title = 'Profil tidak ditemukan';
  } else {
    $title = $profileDetail['nama'].' — '.$judul;
  }
}

// Formulir PPDB: simpan ke tabel ppdb_pendaftar
$pesan = ''; $err = '';
if ($cur === 'ppdb' && $_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = trim($_POST['nama'] ?? ''); $asal = trim($_POST['asal'] ?? ''); $hp = trim($_POST['hp'] ?? ''); $jur = $_POST['jurusan'] ?? '';
  if ($nama === '' || !preg_match('/^[0-9+\- ]{8,15}$/', $hp) || !in_array($jur, ['TKJ','RPL','DKV','PSPT'], true)) {
    $err = 'Isi nama lengkap dan nomor HP yang valid.';
  } elseif (!$pdo) {
    $err = 'Database belum tersambung. Hubungi panitia lewat WhatsApp.';
  } else {
    try {
      $pdo->prepare('INSERT INTO ppdb_pendaftar (nama, asal_sekolah, hp, jurusan) VALUES (?,?,?,?)')->execute([$nama, $asal, $hp, $jur]);
      $pesan = 'Terima kasih, '.e($nama).'. Pendaftaran untuk jurusan '.e($jur).' sudah kami terima. Panitia akan menghubungi Anda lewat WhatsApp.';
    } catch (PDOException $ex) { $err = 'Pendaftaran gagal disimpan. Coba lagi beberapa saat.'; }
  }
}
$dokumen = []; $dokumenError = '';
if ($cur === 'ppdb' && $pdo) {
  require_once 'includes/pdf_documents.php';
  try {
    ensure_pdf_documents_table($pdo);
    $dokumen = $pdo->query('SELECT id, judul, deskripsi, nama_asli, ukuran, diunggah_pada FROM ppdb_dokumen ORDER BY diunggah_pada DESC')->fetchAll();
  } catch (PDOException $ex) {
    error_log('Dokumen PPDB gagal dimuat: '.$ex->getMessage());
    $dokumenError = 'Dokumen PPDB belum dapat dimuat. Silakan coba kembali beberapa saat lagi.';
  }
} elseif ($cur === 'ppdb') {
  $dokumenError = 'Layanan dokumen PPDB belum tersedia. Silakan hubungi panitia sekolah.';
}
require 'includes/header.php';
?>
<section class="pagehead"><div class="wrap">
  <h1><?= $judul ?></h1><p class="lead"><?= $desk ?></p>
  <div class="tabs"><?php foreach ($bagian as $s => $_): ?><a href="#<?= slug($s) ?>"><?= $s ?></a><?php endforeach; ?></div>
</div></section>
<div class="wrap">
<?php $pageSummary = school_setting($pdo, 'page_'.$cur.'_summary'); ?>
<?php if ($pageSummary !== ''): ?><section class="page-summary"><?= nl2br(e($pageSummary)) ?></section><?php endif; ?>
<?php foreach ($bagian as $s => $isi): ?>
  <section class="blok" id="<?= slug($s) ?>">
    <h2><?= $s ?></h2>
    <?php
      $photoSections = ['profil'=>'Sejarah','akademik'=>'Kurikulum','kesiswaan'=>'OSIS','informasi'=>'Berita','fasilitas'=>'Sarana dan Prasarana','bkk'=>'Lowongan Kerja','kerjasama'=>'Daftar Mitra','ppdb'=>'Persyaratan','kontak'=>'Alamat'];
      if (($photoSections[$cur] ?? '') === $s) echo school_photo($cur, $s);
    ?>
    <?php if ($isi === 'SAMBUTAN'): ?>
      <?php
        $headName = school_setting($pdo, 'kepala_nama');
        $headRole = school_setting($pdo, 'kepala_jabatan');
        $speech = school_setting($pdo, 'kepala_sambutan');
        $principal = school_profiles($pdo, 'kepala')[0] ?? null;
        if ($principal) { $headName = $principal['nama']; $headRole = $principal['jabatan']; }
        $headName = $headName ?: 'Informasi kepala sekolah belum tersedia.';
      ?>
      <article class="principal-message">
        <?= school_photo('kepala-sekolah', 'Foto kepala sekolah', 'sambutan', 'principal-photo') ?>
        <div><h3><?= e($headName) ?></h3><?php if ($headRole): ?><p class="muted"><?= e($headRole) ?></p><?php endif; ?>
          <?php if ($speech): ?><div class="speech-text"><?= nl2br(e($speech)) ?></div><?php else: ?><p class="muted">Sambutan kepala sekolah akan ditampilkan setelah diperbarui oleh administrator.</p><?php endif; ?>
          <?php if ($principal): ?><p><a href="halaman.php?m=kepala-sekolah&amp;id=<?= (int)$principal['id'] ?>">Lihat profil kepala sekolah</a></p><?php endif; ?>
        </div>
      </article>
    <?php elseif ($profileCategory && str_starts_with($isi, 'PROFIL:')): ?>
      <?php if ($profileDetail): ?>
        <article class="profile-detail">
          <?= school_photo($cur, 'Foto '.strtolower($judul), 'person-'.$profileDetail['id'], 'profile-photo') ?>
          <div><h3><?= e($profileDetail['nama']) ?></h3><p class="muted"><?= e($profileDetail['jabatan']) ?></p><?php if ($profileDetail['informasi']): ?><p><?= nl2br(e($profileDetail['informasi'])) ?></p><?php endif; ?><a href="halaman.php?m=<?= e($cur) ?>">Kembali ke <?= e($judul) ?></a></div>
        </article>
      <?php else: ?>
        <?php $profiles = school_profiles($pdo, $profileCategory); ?>
        <?php if ($profiles): ?>
          <label class="profile-search-label">Cari profil<input type="search" class="profile-search" placeholder="Cari nama atau jabatan" aria-label="Cari profil"></label>
          <div class="grid g3 profile-grid">
            <?php foreach ($profiles as $profile): ?>
              <article class="card profile-card" data-profile-search="<?= e(strtolower($profile['nama'].' '.$profile['jabatan'])) ?>">
                <?= school_photo($cur, 'Foto '.strtolower($judul).': '.$profile['nama'], 'person-'.$profile['id'], 'profile-photo') ?>
                <h3><?= e($profile['nama']) ?></h3><p class="muted"><?= e($profile['jabatan']) ?></p>
                <?php if ($profile['informasi']): ?><p><?= e(mb_strimwidth($profile['informasi'], 0, 180, '…')) ?></p><?php endif; ?>
                <a href="halaman.php?m=<?= e($cur) ?>&amp;id=<?= (int)$profile['id'] ?>">Lihat profil</a>
              </article>
            <?php endforeach; ?>
          </div>
        <?php else: ?><p class="muted">Profil akan ditampilkan setelah datanya ditambahkan melalui dashboard admin.</p><?php endif; ?>
      <?php endif; ?>
    <?php elseif ($isi === 'FORM'): ?>
      <?php if ($pesan): ?><p class="ok"><?= $pesan ?></p><?php endif; ?>
      <?php if ($err): ?><p class="bad"><?= $err ?></p><?php endif; ?>
      <form method="post" class="form" action="halaman.php?m=ppdb#formulir">
        <label>Nama lengkap<input name="nama" required></label>
        <label>Asal SMP/MTs<input name="asal"></label>
        <label>Nomor HP / WhatsApp<input name="hp" inputmode="tel" required></label>
        <label>Program keahlian<select name="jurusan"><option>TKJ</option><option>RPL</option><option>DKV</option><option>PSPT</option></select></label>
        <button class="btn" type="submit">Kirim pendaftaran</button>
      </form>
    <?php elseif ($isi === 'DOKUMEN'): ?>
      <?php if ($dokumenError): ?><p class="bad"><?= e($dokumenError) ?></p>
      <?php elseif (!$dokumen): ?><p class="muted">Belum ada dokumen PPDB yang dipublikasikan. Silakan periksa kembali nanti.</p>
      <?php else: ?>
        <div class="document-grid">
          <?php foreach ($dokumen as $d): ?>
            <article class="document-card">
              <h3><?= e($d['judul']) ?></h3>
              <?php if ($d['deskripsi'] !== ''): ?><p><?= nl2br(e($d['deskripsi'])) ?></p><?php endif; ?>
              <p class="document-meta"><?= e($d['nama_asli']) ?> · <?= number_format($d['ukuran'] / 1048576, 2) ?> MB · Diunggah <?= e(tgl($d['diunggah_pada'])) ?></p>
              <div class="document-actions">
                <a class="btn" href="dokumen.php?id=<?= (int)$d['id'] ?>" target="_blank" rel="noopener">Lihat PDF</a>
                <a class="btn ghost" href="dokumen.php?id=<?= (int)$d['id'] ?>&amp;download=1">Download PDF</a>
              </div>
              <details><summary>Pratinjau dokumen</summary><iframe class="document-preview" src="dokumen.php?id=<?= (int)$d['id'] ?>" title="Pratinjau <?= e($d['judul']) ?>" loading="lazy"></iframe></details>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php else: echo $isi; endif; ?>
  </section>
<?php endforeach; ?>
</div>
<?php if ($cur === 'galeri' || $profileCategory): ?>
<script>
document.querySelectorAll('[data-gallery-filter]').forEach(function (button) {
  button.addEventListener('click', function () {
    const category = button.dataset.galleryFilter;
    document.querySelectorAll('[data-gallery-filter]').forEach(function (item) {
      item.setAttribute('aria-pressed', String(item === button));
    });
    document.querySelectorAll('[data-gallery-category]').forEach(function (photo) {
      photo.hidden = category !== 'all' && photo.dataset.galleryCategory !== category;
    });
  });
});
const profileSearch = document.querySelector('.profile-search');
if (profileSearch) profileSearch.addEventListener('input', function () {
  const query = profileSearch.value.trim().toLocaleLowerCase();
  document.querySelectorAll('.profile-card').forEach(function (card) {
    card.hidden = !card.dataset.profileSearch.includes(query);
  });
});
</script>
<?php endif; ?>
<?php require 'includes/footer.php'; ?>
