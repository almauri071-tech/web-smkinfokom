<?php $title = 'Beranda'; $cur = ''; require 'includes/header.php'; ?>
<?php
$homeHeading = school_setting($pdo, 'home_heading', 'Belajar teknologi lewat proyek nyata, lulus siap kerja.');
$homeSummary = school_setting($pdo, 'home_summary');
?>
<section class="hero">
  <img class="hero-bg" src="<?= e(site_image_source('home','hero','uploads/site-images/3f70ce408ce5af45e1dae76c1171969c.jpg')) ?>" alt="<?= e(site_image_alt('home','hero','Gedung SMK Infokom tampak dari depan')) ?>" fetchpriority="high">
  <div class="wrap hero-in">
    <div>
      <h1><?= e($homeHeading) ?></h1>
      <p class="lead">Empat program keahlian, pembelajaran praktik, dan jejaring industri yang membuka jalan menuju magang dan karier.</p>
      <?php if ($homeSummary !== ''): ?><p class="lead"><?= nl2br(e($homeSummary)) ?></p><?php endif; ?>
      <p class="cta"><a class="btn" href="halaman.php?m=ppdb">Daftar PPDB 2027/2028</a> <a class="btn ghost" href="halaman.php?m=profil">Kenali sekolah kami</a></p>
    </div>
    <img class="hero-photo" src="<?= e(site_image_source('home','hero-card',photo_url('photo-1522202176988-66273c2fd55f',900))) ?>" alt="<?= e(site_image_alt('home','hero-card','Siswa belajar dan berdiskusi bersama (foto ilustrasi)')) ?>" fetchpriority="high">
  </div>
</section>

<section class="wrap sec">
  <h2>Program keahlian</h2>
  <div class="grid g3">
    <?php foreach ([
      ['tkj','Teknik Komputer dan Jaringan','Merakit komputer, membangun jaringan, mengelola server, dan menjaga keamanan sistem.','photo-1518770660439-4636190af475'],
      ['rpl','Rekayasa Perangkat Lunak','Membuat aplikasi web dan mobile, mengelola basis data, dan bekerja dalam tim pengembang.','photo-1516321318423-f06f85e504b3'],
      ['dkv','Desain Komunikasi Visual','Merancang identitas visual, antarmuka aplikasi, foto, dan video.','photo-1504274066651-8d31a536b11a'],
      ['pspt','Produksi dan Siaran Program Televisi','Mempelajari perencanaan, produksi, dan penyuntingan konten audiovisual.','photo-1492619375914-88005aa9e8fb'],
    ] as [$code,$name,$description,$photo]): ?>
      <article class="card">
        <img class="program-photo" src="<?= e(site_image_source('home','program-'.$code,photo_url($photo,700))) ?>" alt="<?= e(site_image_alt('home','program-'.$code,'Foto ilustrasi program '.$name)) ?>" loading="lazy">
        <h3><?= e($name) ?></h3><p><?= e($description) ?></p>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="wrap sec">
  <h2>Kegiatan dan prestasi siswa</h2>
  <div class="grid g3">
    <article class="card"><div class="ph"><img src="<?= e(site_image_source('home','activity-1',photo_url('photo-1516321318423-f06f85e504b3',700))) ?>" alt="<?= e(site_image_alt('home','activity-1','Siswa praktik teknologi di kelas (foto ilustrasi)')) ?>" loading="lazy"><span>Belajar praktik</span></div><h3>Belajar dengan praktik</h3><p>Siswa mengembangkan keterampilan melalui proyek dan praktik sesuai bidang keahlian.</p></article>
    <article class="card"><div class="ph"><img src="<?= e(site_image_source('home','activity-2',photo_url('photo-1523580494863-6f3031224c94',700))) ?>" alt="<?= e(site_image_alt('home','activity-2','Kegiatan bersama warga sekolah (foto ilustrasi)')) ?>" loading="lazy"><span>Kegiatan sekolah</span></div><h3>Aktif berkegiatan</h3><p>Ruang untuk berkolaborasi, berorganisasi, dan mengembangkan karakter.</p></article>
    <article class="card"><div class="ph"><img src="<?= e(site_image_source('home','activity-3',photo_url('photo-1523580494863-6f3031224c94',700))) ?>" alt="<?= e(site_image_alt('home','activity-3','Kegiatan dan pencapaian siswa (foto ilustrasi)')) ?>" loading="lazy"><span>Prestasi siswa</span></div><h3>Bangga atas prestasi</h3><p>Apresiasi untuk karya, kompetisi, dan pencapaian siswa di berbagai bidang.</p></article>
  </div>
</section>

<section class="band"><div class="wrap two">
  <div>
    <h2>Berita terbaru</h2>
    <?php foreach (berita_terbaru(3) as $b): ?>
    <article class="item"><time><?= tgl($b['tanggal']) ?></time><h3><a href="halaman.php?m=informasi#berita"><?= e($b['judul']) ?></a></h3></article>
    <?php endforeach; ?>
  </div>
  <div>
    <h2>Pengumuman dan agenda</h2>
    <?= $menu['informasi'][2]['Pengumuman'] ?>
    <p><a href="halaman.php?m=informasi#agenda">Lihat semua agenda</a></p>
  </div>
</div></section>

<section class="wrap sec">
  <h2>Jelajahi situs</h2>
  <div class="grid g5">
    <?php foreach ($menu as $k => $m): ?>
      <a class="tile" href="halaman.php?m=<?= $k ?>"><b><?= $m[0] ?></b><span><?= implode(', ', array_keys($m[2])) ?></span></a>
    <?php endforeach; ?>
  </div>
</section>

<section class="wrap sec"><div class="ppdb">
  <div><h2>Pendaftaran siswa baru dibuka</h2><p>Siapkan berkas, isi formulir daring, lalu ikuti tes minat dan bakat.</p></div>
  <a class="btn" href="halaman.php?m=ppdb#formulir">Isi formulir</a>
</div></section>
<?php require 'includes/footer.php'; ?>
