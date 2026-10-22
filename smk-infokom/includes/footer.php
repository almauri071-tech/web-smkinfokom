</main>
<footer class="foot">
  <div class="wrap fgrid">
    <div><b><?= e($site['nama']) ?></b><?php if ($site['alamat']): ?><p><?= e($site['alamat']) ?></p><?php endif; ?></div>
    <div><b>Hubungi</b><p><?php if ($site['wa']): ?><a href="https://wa.me/<?= rawurlencode($site['wa']) ?>">WhatsApp <?= e($site['wa_tampil'] ?: $site['wa']) ?></a><br><?php endif; ?><?php if ($site['email']): ?><a href="mailto:<?= e($site['email']) ?>"><?= e($site['email']) ?></a><?php endif; ?><?php if (!$site['wa'] && !$site['email']): ?>Informasi kontak belum tersedia.<?php endif; ?></p></div>
    <div><b>Pintasan</b><p><a href="halaman.php?m=ppdb">PPDB / SPMB</a><br><a href="halaman.php?m=bkk">BKK</a><br><a href="halaman.php?m=informasi">Berita</a></p></div>
  </div>
  <div class="wrap copy">© <?= date('Y') ?> <?= $site['nama'] ?>. Semua hak dilindungi.</div>
</footer>
</body>
</html>
