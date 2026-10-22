<?php require_once __DIR__.'/data.php'; $cur = $cur ?? ''; ?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($title ?? $site['nama']) ?> – <?= $site['nama'] ?></title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<a class="skip" href="#isi">Lewati ke konten</a>
<header class="top<?= $cur === 'jurusan' ? ' course-top' : '' ?>">
  <div class="wrap bar">
    <a class="brand" href="index.php"><?php if (site_image_record('branding','school-logo')): ?><img class="logo-image" src="<?= e(site_image_source('branding','school-logo','')) ?>" alt="<?= e(site_image_alt('branding','school-logo','Logo resmi '.$site['nama'])) ?>"><?php else: ?><span class="logo">IK</span><?php endif; ?><span><b><?= $site['nama'] ?></b><small>Teknologi, komunikasi, karier</small></span></a>
    <button class="burger" aria-label="Buka menu" aria-controls="nav" aria-expanded="false" onclick="const n=document.getElementById('nav');const open=n.classList.toggle('open');this.setAttribute('aria-expanded',String(open))">Menu</button>
  </div>
  <nav id="nav" class="nav" aria-label="Menu utama"><div class="wrap">
    <a href="index.php" class="<?= $cur==='' ? 'on':'' ?>">Beranda</a>
    <?php foreach ($menu as $k => $m): ?>
      <div class="dd">
        <a href="<?= $k === 'jurusan' ? 'jurusan.php?kode=TKJ' : 'halaman.php?m='.e($k) ?>" class="<?= $cur===$k ? 'on':'' ?>"><?= e($m[0]) ?></a>
        <button class="dd-toggle" type="button" aria-label="Buka submenu <?= e($m[0]) ?>" aria-expanded="false">⌄</button>
        <div class="sub"><?php foreach ($m[2] as $s => $_): ?><a href="<?= $k === 'jurusan' ? 'jurusan.php?kode='.rawurlencode($s) : 'halaman.php?m='.rawurlencode($k).'#'.slug($s) ?>"><?= e($s) ?></a><?php endforeach; ?></div>
      </div>
    <?php endforeach; ?>
  </div></nav>
</header>
<script>
document.addEventListener('click', function (event) {
  const toggle = event.target.closest('.dd-toggle');
  if (toggle) {
    const item = toggle.closest('.dd');
    const open = item.classList.toggle('expanded');
    toggle.setAttribute('aria-expanded', String(open));
    return;
  }
  if (!event.target.closest('.dd')) {
    document.querySelectorAll('.dd.expanded').forEach(function (item) {
      item.classList.remove('expanded');
      item.querySelector('.dd-toggle').setAttribute('aria-expanded', 'false');
    });
  }
});
</script>
<main id="isi">
