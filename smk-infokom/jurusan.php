<?php
require 'includes/data.php';
$kode = strtoupper(trim($_GET['kode'] ?? ''));
$stmt = $pdo ? $pdo->prepare('SELECT * FROM program_keahlian WHERE kode=?') : null;
if ($stmt) {
  $stmt->execute([$kode]);
  $program = $stmt->fetch();
} else {
  $program = null;
}
if (!$program) {
  http_response_code(404);
  $title = 'Jurusan tidak ditemukan';
  $cur = 'jurusan';
  require 'includes/header.php';
  echo '<div class="wrap sec"><h1>Jurusan tidak ditemukan</h1><p><a href="halaman.php?m=jurusan">Lihat daftar jurusan</a></p></div>';
  require 'includes/footer.php';
  exit;
}

$title = $program['kode'].' — '.$program['nama'];
$cur = 'jurusan';
$pageKey = 'jurusan-'.strtolower($program['kode']);
$photoFallbacks = [
  'TKJ'=>'photo-1518770660439-4636190af475',
  'RPL'=>'photo-1516321318423-f06f85e504b3',
  'DKV'=>'photo-1504274066651-8d31a536b11a',
  'PSPT'=>'photo-1492619375914-88005aa9e8fb',
];
$courseBanner = site_image_record($pageKey, 'banner');
$courseBannerDimensions = false;
$courseBannerIsMark = false;
if ($courseBanner && !empty($courseBanner['nama_file'])) {
  $courseBannerPath = __DIR__.'/uploads/site-images/'.basename($courseBanner['nama_file']);
  $courseBannerDimensions = is_file($courseBannerPath) ? getimagesize($courseBannerPath) : false;
  if (!$courseBannerDimensions) error_log('Banner jurusan tidak dapat dibaca: '.$pageKey);
  $courseBannerIsMark = $courseBannerDimensions && $courseBannerDimensions[0] / max(1, $courseBannerDimensions[1]) < 1.5;
}
$courseBannerIsPhoto = $courseBannerDimensions && !$courseBannerIsMark;
$courseBannerSource = $courseBannerIsPhoto
  ? site_image_source($pageKey, 'banner', photo_url($photoFallbacks[$kode]))
  : photo_url($photoFallbacks[$kode]);
require 'includes/header.php';
?>
<section class="pagehead course-head">
  <img class="course-banner" src="<?= e($courseBannerSource) ?>" alt="<?= e($courseBannerIsMark ? 'Foto ilustrasi pembelajaran '.$program['nama'] : site_image_alt($pageKey,'banner',$program['nama'].' (foto ilustrasi)')) ?>" fetchpriority="high">
  <div class="wrap course-heading <?= $courseBannerIsMark ? 'has-mark' : '' ?>">
    <div class="course-copy">
      <p class="eyebrow"><?= e($program['kode']) ?></p>
      <h1><?= e($program['nama']) ?></h1>
      <p class="lead"><?= e($program['deskripsi']) ?></p>
    </div>
    <?php if ($courseBannerIsMark): ?><img class="course-mark" src="<?= e(site_image_source($pageKey,'banner','')) ?>" alt="<?= e(site_image_alt($pageKey,'banner','Identitas visual '.$program['nama'])) ?>"><?php endif; ?>
  </div>
</section>
<div class="wrap course-content">
  <section class="blok">
    <h2>Kompetensi</h2>
    <ul class="list"><?php foreach (explode('|',$program['kompetensi']) as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul>
  </section>
  <section class="blok">
    <h2>Fasilitas pembelajaran</h2>
    <p class="muted">Contoh sarana praktik terkait; ketersediaan aktual dapat diperbarui oleh sekolah.</p>
    <ul class="list"><?php foreach (explode('|',$program['fasilitas']) as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul>
  </section>
  <section class="blok">
    <h2>Kegiatan pembelajaran</h2>
    <ul class="list"><?php foreach (explode('|',$program['kegiatan']) as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul>
  </section>
  <section class="blok">
    <h2>Proyek siswa</h2>
    <div class="grid g3">
      <?php foreach (explode('|',$program['proyek']) as $index=>$project): ?>
        <article class="card"><div class="ph"><img src="<?= e(site_image_source($pageKey,'gallery-'.($index+1),photo_url($photoFallbacks[$kode],600))) ?>" alt="<?= e(site_image_alt($pageKey,'gallery-'.($index+1),$program['kode'].' — '.$project.' (foto ilustrasi)')) ?>" loading="lazy"><span><?= e($project) ?></span></div><h3><?= e($project) ?></h3></article>
      <?php endforeach; ?>
    </div>
  </section>
  <section class="blok">
    <h2>Peluang karier</h2>
    <ul class="list"><?php foreach (explode('|',$program['karier']) as $item): ?><li><?= e($item) ?></li><?php endforeach; ?></ul>
  </section>
  <p><a class="btn" href="halaman.php?m=jurusan">Semua jurusan</a></p>
</div>
<?php require 'includes/footer.php'; ?>
