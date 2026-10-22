<?php
// ====== DATA SITUS (ganti isi sesuai data sekolah sebenarnya) ======
$site = [
  'nama'   => 'SMK Infokom',
  'alamat' => '',
  'wa'     => '',
  'wa_tampil' => '',
  'email'  => '',
];
require_once __DIR__.'/db.php';
require_once __DIR__.'/image_library.php';
require_once __DIR__.'/school_content.php';
if ($pdo) {
  try {
    ensure_school_content_tables($pdo);
    $site['nama'] = school_setting($pdo, 'nama', $site['nama']);
    $site['alamat'] = school_setting($pdo, 'alamat', $site['alamat']);
    $site['wa'] = school_setting($pdo, 'wa', $site['wa']);
    $site['wa_tampil'] = school_setting($pdo, 'wa_tampil', $site['wa_tampil']);
    $site['email'] = school_setting($pdo, 'email', $site['email']);
  } catch (PDOException $error) {
    error_log('Pengaturan situs belum dapat dimuat: '.$error->getMessage());
  }
}

function ul($a){ return '<ul class="list">'.implode('', array_map(fn($i)=>"<li>$i</li>", $a)).'</ul>'; }
function tabel($head, $rows){
  $h = '<div class="tbl"><table><thead><tr>'.implode('', array_map(fn($x)=>"<th>$x</th>", $head)).'</tr></thead><tbody>';
  foreach ($rows as $r) $h .= '<tr>'.implode('', array_map(fn($x)=>"<td>$x</td>", $r)).'</tr>';
  return $h.'</tbody></table></div>';
}
function ph($t){ return '<div class="ph" role="img" aria-label="'.$t.'"><span>'.$t.'</span></div>'; }
function photo_url($id, $width = 1000){ return 'https://images.unsplash.com/'.$id.'?auto=format&fit=crop&w='.$width.'&q=82'; }
function school_photo($page, $alt, $slot = 'lead', $class = 'section-photo'){
  if (in_array($page, ['guru','staff','kepala-sekolah'], true) && !site_image_record($page, $slot)) {
    return '<div class="profile-photo-placeholder '.e($class).'" role="img" aria-label="'.e($alt.' — foto belum diunggah').'"><svg class="profile-placeholder-icon" viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="21" r="12"/><path d="M9 57c1-13 10-21 23-21s22 8 23 21v2H9z"/></svg><span>Foto belum tersedia</span></div>';
  }
  $schoolBuilding = site_image_source('home','hero','uploads/site-images/3f70ce408ce5af45e1dae76c1171969c.jpg');
  $photos = [
    'profil' => $schoolBuilding,
    'akademik' => 'photo-1509062522246-3755977927d7',
    'kesiswaan' => 'photo-1523580494863-6f3031224c94',
    'informasi' => 'photo-1523580494863-6f3031224c94',
    'fasilitas' => 'photo-1580582932707-520aed937b7b',
    'bkk' => 'photo-1521737711867-e3b97375f902',
    'kerjasama' => 'photo-1497366754035-f200968a6e72',
    'ppdb' => $schoolBuilding,
    'kontak' => $schoolBuilding,
  ];
  $fallback = isset($photos[$page]) ? (str_starts_with($photos[$page], 'uploads/') || str_starts_with($photos[$page], 'gambar.php?') ? $photos[$page] : photo_url($photos[$page])) : photo_url('photo-1509062522246-3755977927d7');
  $fallbackAlt = in_array($page, ['profil','ppdb','kontak'], true) ? 'Gedung SMK Infokom tampak dari depan' : $alt.' (foto ilustrasi)';
  return '<img class="'.e($class).'" src="'.e(site_image_source($page, $slot, $fallback)).'" alt="'.e(site_image_alt($page, $slot, $fallbackAlt)).'" loading="lazy">';
}

$menu = [
 'profil' => ['Profil Sekolah', 'Mengenal SMK Infokom dari sejarah hingga struktur organisasinya.', [
   'Sejarah' => '<p>SMK Infokom berdiri pada tahun 2005 dengan satu tujuan: menyiapkan lulusan yang siap bekerja di bidang teknologi informasi dan komunikasi. Dari dua ruang kelas dan satu laboratorium komputer, kini sekolah tumbuh menjadi tiga program keahlian dengan jaringan mitra industri yang luas.</p><p>Setiap tahun, kurikulum diperbarui bersama mitra industri agar sesuai dengan kebutuhan dunia kerja.</p>',
   'Visi dan Misi' => '<p><strong>Visi:</strong> Menjadi SMK unggul yang menghasilkan lulusan kompeten, berkarakter, dan siap bersaing di dunia digital.</p>'.ul(['Menyelenggarakan pembelajaran berbasis proyek dan praktik industri.','Membangun kerja sama berkelanjutan dengan dunia usaha dan industri.','Menanamkan disiplin, integritas, dan etos kerja.','Menyediakan sarana belajar yang mutakhir.']),
   'Struktur Organisasi' => '<p>Informasi struktur organisasi sekolah akan ditampilkan setelah diperbarui oleh administrator.</p>',
   'Sambutan Kepala Sekolah' => 'SAMBUTAN',
 ]],
 'guru' => ['Guru', 'Profil guru yang dipublikasikan oleh sekolah.', ['Daftar Guru' => 'PROFIL:guru']],
 'staff' => ['Staff', 'Profil staff yang dipublikasikan oleh sekolah.', ['Daftar Staff' => 'PROFIL:staff']],
 'kepala-sekolah' => ['Kepala Sekolah', 'Informasi kepala sekolah yang dipublikasikan oleh sekolah.', ['Profil Kepala Sekolah' => 'PROFIL:kepala']],
 'jurusan' => ['Jurusan', 'Program keahlian SMK Infokom.', [
   'TKJ' => '<p><a href="jurusan.php?kode=TKJ">Teknik Komputer dan Jaringan</a></p>',
   'RPL' => '<p><a href="jurusan.php?kode=RPL">Rekayasa Perangkat Lunak</a></p>',
   'DKV' => '<p><a href="jurusan.php?kode=DKV">Desain Komunikasi Visual</a></p>',
   'PSPT' => '<p><a href="jurusan.php?kode=PSPT">Produksi dan Siaran Program Televisi</a></p>',
 ]],
 'akademik' => ['Akademik', 'Kurikulum, jadwal pelajaran, dan kalender akademik tahun ajaran berjalan.', [
   'Kurikulum' => '<p>Program pembelajaran sekolah memadukan teori, praktik, dan proyek sesuai kompetensi keahlian.</p>'.ul(['Program keahlian: Teknik Komputer dan Jaringan (TKJ)','Program keahlian: Rekayasa Perangkat Lunak (RPL)','Program keahlian: Desain Komunikasi Visual (DKV)','Program keahlian: Produksi dan Siaran Program Televisi (PSPT)','Praktik Kerja Lapangan (PKL)','Uji Kompetensi Keahlian (UKK)']),
   'Jadwal Pelajaran' => tabel(['Jam','Senin','Selasa','Rabu'], [['07.00 – 08.30','Matematika','Pemrograman Dasar','Bahasa Indonesia'],['08.30 – 10.00','Bahasa Inggris','Pemrograman Dasar','Jaringan Dasar'],['10.15 – 11.45','Informatika','Basis Data','Jaringan Dasar'],['12.30 – 14.00','Projek IPAS','Basis Data','PJOK']]),
   'Kalender Akademik' => tabel(['Kegiatan','Tanggal'], [['Awal semester ganjil','Juli 2026'],['Penilaian Tengah Semester','Oktober 2026'],['Penilaian Akhir Semester','Desember 2026'],['Libur semester','Akhir Des 2026 – awal Jan 2027'],['Uji Kompetensi Keahlian','Maret 2027']]),
 ]],
 'kesiswaan' => ['Kesiswaan', 'Wadah siswa mengembangkan minat, bakat, dan kepemimpinan.', [
   'OSIS' => '<p>OSIS SMK Infokom mengelola kegiatan siswa, mulai dari masa orientasi, peringatan hari besar, hingga lomba antarkelas. Pengurus dipilih setiap tahun melalui pemilihan langsung.</p>',
   'Ekstrakurikuler' => '<div class="grid g3">'.implode('', array_map(fn($activity,$i)=>'<article class="card"><div class="ph"><img src="'.e(site_image_source('kesiswaan','activity-'.($i+1),photo_url($activity[1],600))).'" alt="'.e(site_image_alt('kesiswaan','activity-'.($i+1),$activity[0].' (foto ilustrasi)')).'" loading="lazy"><span>'.e($activity[0]).'</span></div><h3>'.e($activity[0]).'</h3></article>', [['Robotik dan IoT','photo-1518770660439-4636190af475'],['Esports','photo-1542751371-adc38448a05e'],['Pramuka','photo-1523580494863-6f3031224c94'],['Paskibra','photo-1523580494863-6f3031224c94'],['Fotografi dan Videografi','photo-1504274066651-8d31a536b11a'],['Futsal dan Basket','photo-1517466787929-bc90951d0974']], range(0,5))).'</div>',
   'Prestasi' => '<img class="section-photo" src="'.e(site_image_source('kesiswaan','achievement',photo_url('photo-1523580494863-6f3031224c94'))).'" alt="'.e(site_image_alt('kesiswaan','achievement','Prestasi siswa (foto ilustrasi)')).'" loading="lazy">'.tabel(['Tahun','Prestasi','Tingkat'], [['2026','Juara 1 LKS Web Technologies','Provinsi'],['2026','Juara 2 Lomba Desain Poster Digital','Kota'],['2025','Juara 1 Kompetisi Robotik Pelajar','Nasional'],['2025','Juara 3 LKS IT Network Systems','Provinsi']]),
 ]],
 'informasi' => ['Informasi', 'Berita terbaru, pengumuman resmi, dan agenda kegiatan sekolah.', [
   'Berita' => '<article class="item news-card"><img src="'.e(site_image_source('informasi','news-1',photo_url('photo-1523580494863-6f3031224c94',500))).'" alt="'.e(site_image_alt('informasi','news-1','Siswa mengikuti perlombaan (foto ilustrasi)')).'" loading="lazy"><div><time>12 Okt 2026</time><h3>Siswa RPL juarai LKS tingkat provinsi</h3><p>Tim Web Technologies SMK Infokom meraih juara pertama setelah menyelesaikan tantangan pembuatan aplikasi dalam waktu dua hari.</p></div></article><article class="item news-card"><img src="'.e(site_image_source('informasi','news-2',photo_url('photo-1516321318423-f06f85e504b3',500))).'" alt="'.e(site_image_alt('informasi','news-2','Kegiatan praktik teknologi (foto ilustrasi)')).'" loading="lazy"><div><time>03 Okt 2026</time><h3>Kunjungan industri ke pusat data</h3><p>Siswa TKJ melihat langsung cara kerja pusat data dan berdiskusi dengan para insinyur jaringan.</p></div></article><article class="item news-card"><img src="'.e(site_image_source('informasi','news-3',photo_url('photo-1522202176988-66273c2fd55f',500))).'" alt="'.e(site_image_alt('informasi','news-3','Siswa mengikuti lokakarya (foto ilustrasi)')).'" loading="lazy"><div><time>25 Sep 2026</time><h3>Workshop UI/UX bersama mitra industri</h3><p>Siswa DKV belajar menyusun alur pengguna dan prototipe aplikasi.</p></div></article>',
   'Pengumuman' => ul(['<strong>20 Okt</strong> – Pengumpulan laporan PKL paling lambat 30 Oktober.','<strong>18 Okt</strong> – Jadwal Penilaian Tengah Semester sudah tersedia di menu Akademik.','<strong>15 Okt</strong> – Pendaftaran beasiswa prestasi dibuka sampai 1 November.']),
   'Agenda' => tabel(['Tanggal','Kegiatan','Tempat'], [['27 Okt 2026','Rapat orang tua kelas XII','Aula'],['02 Nov 2026','Lomba Kebersihan Kelas','Lingkungan sekolah'],['10 Nov 2026','Upacara Hari Pahlawan','Lapangan'],['21 Nov 2026','Job Fair SMK Infokom','Aula dan halaman']]),
 ]],
 'galeri' => ['Galeri', 'Dokumentasi kegiatan belajar dan acara sekolah.', [
   'Foto' => '<div class="gallery-filters" role="group" aria-label="Filter kategori galeri"><button type="button" data-gallery-filter="all" aria-pressed="true">Semua</button><button type="button" data-gallery-filter="akademik" aria-pressed="false">Akademik</button><button type="button" data-gallery-filter="ekstrakurikuler" aria-pressed="false">Ekstrakurikuler</button><button type="button" data-gallery-filter="acara" aria-pressed="false">Acara sekolah</button><button type="button" data-gallery-filter="prestasi" aria-pressed="false">Prestasi</button></div><div class="grid g3 gallery-grid">'.implode('', array_map(fn($f,$i)=>'<a class="ph gallery-photo" data-gallery-category="'.e($f[2]).'" href="'.e(site_image_source('galeri','gallery-'.($i+1),photo_url($f[1],1400))).'" target="_blank" rel="noopener"><img src="'.e(site_image_source('galeri','gallery-'.($i+1),photo_url($f[1],600))).'" alt="'.e(site_image_alt('galeri','gallery-'.($i+1),$f[0].' (foto ilustrasi)')).'" loading="lazy"><span>'.e($f[0]).'</span></a>', [['Praktik pembelajaran','photo-1516321318423-f06f85e504b3','akademik'],['Kegiatan ekstrakurikuler','photo-1523580494863-6f3031224c94','ekstrakurikuler'],['Karya dan prestasi siswa','photo-1523580494863-6f3031224c94','prestasi'],['Belajar bersama','photo-1522202176988-66273c2fd55f','akademik'],['Kegiatan industri','photo-1521737711867-e3b97375f902','acara'],['Lingkungan belajar','photo-1509062522246-3755977927d7','acara']], range(0,5))).'</div>',
 ]],
 'fasilitas' => ['Fasilitas', 'Sarana dan prasarana yang menunjang praktik siswa.', [
   'Sarana dan Prasarana' => '<div class="grid g3">'.implode('', array_map(fn($f,$i)=>'<div class="card"><div class="ph"><img src="'.e(site_image_source('fasilitas','facility-'.($i+1),photo_url($f[2],600))).'" alt="'.e(site_image_alt('fasilitas','facility-'.($i+1),$f[0].' (foto ilustrasi)')).'" loading="lazy"><span>'.e($f[0]).'</span></div><h3>'.$f[0].'</h3><p>'.$f[1].'</p></div>', [['Laboratorium Komputer','3 ruang, total 108 unit komputer.','photo-1516321318423-f06f85e504b3'],['Laboratorium Jaringan','Router, switch, dan server untuk praktik.','photo-1518770660439-4636190af475'],['Studio Multimedia','Kamera, pencahayaan, dan ruang suntingan.','photo-1504274066651-8d31a536b11a'],['Perpustakaan Digital','Buku cetak dan akses jurnal daring.','photo-1544717305-2782549b5136'],['Aula Serbaguna','Kapasitas 400 orang.','photo-1523580494863-6f3031224c94'],['Area Wi-Fi','Jaringan nirkabel di seluruh kampus.','photo-1497366754035-f200968a6e72']], range(0,5))).'</div>',
 ]],
 'bkk' => ['BKK', 'Bursa Kerja Khusus: menghubungkan lulusan dengan dunia kerja.', [
   'Lowongan Kerja' => tabel(['Perusahaan','Posisi','Batas daftar'], [['PT Nusa Teknologi','Teknisi Jaringan','30 Okt 2026'],['PT Karya Digital','Junior Web Developer','05 Nov 2026'],['CV Kreasi Visual','Desainer Grafis','12 Nov 2026']]),
   'Rekrutmen' => '<p>Rekrutmen dilakukan melalui BKK. Alumni dan siswa kelas XII mendaftar dengan membawa CV, ijazah atau surat keterangan lulus, dan portofolio. BKK mendampingi siswa dari penyaringan berkas sampai wawancara.</p>',
   'Jadwal Tes' => tabel(['Tanggal','Perusahaan','Tahap'], [['04 Nov 2026','PT Nusa Teknologi','Tes teknis'],['09 Nov 2026','PT Karya Digital','Wawancara'],['16 Nov 2026','CV Kreasi Visual','Review portofolio']]),
   'Tracer Study' => '<p>Tracer study melacak perkembangan alumni setelah lulus untuk memperbaiki kurikulum dan layanan BKK. Alumni diminta mengisi formulir keterangan pekerjaan, studi lanjut, atau wirausaha.</p>'.ul(['Bekerja: 62%','Melanjutkan kuliah: 21%','Wirausaha: 9%','Belum bekerja: 8%']).'<p class="muted">Angka di atas hanya contoh.</p>',
 ]],
 'kerjasama' => ['Kerja Sama PT', 'Kemitraan dengan perusahaan dan perguruan tinggi.', [
   'Daftar Mitra' => '<div class="grid g3">'.implode('', array_map(fn($m)=>'<div class="card"><h3>'.$m.'</h3></div>', ['PT Nusa Teknologi','PT Karya Digital','CV Kreasi Visual','PT Jaringan Mandiri','Universitas Contoh','Politeknik Contoh'])).'</div>',
   'Program Industri' => ul(['Guru tamu dari industri','Penyelarasan kurikulum','Sertifikasi kompetensi','Kunjungan industri']),
   'MoU' => '<p>Kerja sama dituangkan dalam nota kesepahaman (MoU) yang mengatur PKL, rekrutmen lulusan, pelatihan guru, dan penggunaan fasilitas bersama. Salinan MoU tersedia di ruang Humas.</p>',
   'Magang' => '<p>Siswa kelas XI mengikuti PKL selama 4–6 bulan di mitra industri. Pembagian tempat PKL disesuaikan dengan program keahlian dan minat siswa.</p>',
 ]],
 'ppdb' => ['PPDB / SPMB', 'Penerimaan peserta didik baru tahun ajaran 2027/2028.', [
   'Persyaratan' => ul(['Lulusan SMP/MTs atau sederajat','Fotokopi ijazah atau surat keterangan lulus','Fotokopi kartu keluarga dan akta kelahiran','Pas foto berwarna 3×4 (2 lembar)','Fotokopi rapor semester 1–5']),
   'Alur Pendaftaran' => '<ol class="steps"><li>Isi formulir pendaftaran daring.</li><li>Unggah atau serahkan berkas persyaratan.</li><li>Ikuti tes minat dan bakat.</li><li>Cek pengumuman hasil seleksi.</li><li>Lakukan daftar ulang.</li></ol>',
   'Jadwal Seleksi' => '<p>Jadwal resmi pendaftaran dan seleksi akan diumumkan oleh panitia PPDB. Periksa kembali halaman ini dan dokumen resmi secara berkala.</p>',
   'Jalur Seleksi' => '<p>Pilih program keahlian sesuai minat: TKJ, RPL, DKV, atau PSPT. Ketentuan seleksi lengkap tersedia pada panduan PPDB.</p>',
   'Pengumuman Seleksi' => '<p>Informasi hasil seleksi akan disampaikan melalui kanal resmi sekolah dan dapat diperbarui pada bagian dokumen PPDB.</p>',
   'Dokumen PPDB' => 'DOKUMEN',
   'Formulir' => 'FORM',
 ]],
 'kontak' => ['Kontak', 'Hubungi kami untuk informasi pendaftaran, kerja sama, atau kunjungan.', [
   'Alamat' => $site['alamat'] !== '' ? '<p>'.e($site['alamat']).'</p>' : '<p class="muted">Alamat sekolah belum diatur. Silakan lihat kontak resmi sekolah.</p>',
   'WhatsApp' => $site['wa'] !== '' ? '<p><a class="btn" href="https://wa.me/'.rawurlencode($site['wa']).'">Chat '.e($site['wa_tampil'] ?: $site['wa']).'</a></p>' : '<p class="muted">Kontak WhatsApp belum tersedia di situs.</p>',
   'Email' => $site['email'] !== '' ? '<p><a href="mailto:'.e($site['email']).'">'.e($site['email']).'</a></p>' : '<p class="muted">Alamat email belum diatur.</p>',
   'Google Maps' => $site['alamat'] !== '' ? '<iframe class="map" title="Lokasi sekolah" loading="lazy" src="https://www.google.com/maps?q='.rawurlencode($site['alamat']).'&output=embed"></iframe>' : '<p class="muted">Lokasi akan ditampilkan setelah alamat resmi sekolah diatur.</p>',
 ]],
];
function slug($s){ return strtolower(trim(preg_replace('/[^a-z0-9]+/i','-',$s),'-')); }

// ====== DATA DARI DATABASE ======
if ($r = ambil('SELECT * FROM berita ORDER BY tanggal DESC')) {
  $newsPhotos = ['photo-1523580494863-6f3031224c94','photo-1516321318423-f06f85e504b3','photo-1522202176988-66273c2fd55f'];
  $menu['informasi'][2]['Berita'] = implode('', array_map(fn($b,$i)=>'<article class="item news-card"><img src="'.e(site_image_source('informasi','news-'.$b['id'],photo_url($newsPhotos[$i % count($newsPhotos)],500))).'" alt="'.e(site_image_alt('informasi','news-'.$b['id'],'Kegiatan sekolah: '.$b['judul'].' (foto ilustrasi)')).'" loading="lazy"><div><time>'.tgl($b['tanggal']).'</time><h3>'.e($b['judul']).'</h3><p>'.e($b['ringkasan']).'</p><a href="#berita">Baca selengkapnya</a></div></article>', $r, array_keys($r)));
}
if ($r = ambil('SELECT * FROM pengumuman ORDER BY tanggal DESC LIMIT 10')) {
  $menu['informasi'][2]['Pengumuman'] = ul(array_map(fn($x)=>'<strong>'.tgl($x['tanggal']).'</strong> – '.e($x['isi']), $r));
}
if ($r = ambil('SELECT * FROM agenda WHERE tanggal >= CURDATE() ORDER BY tanggal')) {
  $menu['informasi'][2]['Agenda'] = tabel(['Tanggal','Kegiatan','Tempat'], array_map(fn($x)=>[tgl($x['tanggal']),e($x['kegiatan']),e($x['tempat'])], $r));
}
if ($r = ambil('SELECT * FROM prestasi ORDER BY tahun DESC, id DESC')) {
  $menu['kesiswaan'][2]['Prestasi'] = tabel(['Tahun','Prestasi','Tingkat'], array_map(fn($x)=>[e($x['tahun']),e($x['prestasi']),e($x['tingkat'])], $r));
}
if (($r = ambil('SELECT * FROM lowongan ORDER BY batas DESC')) !== null) {
  $menu['bkk'][2]['Lowongan Kerja'] = $r ? '<div class="grid g3">'.implode('', array_map(function($job){
    $active = $job['batas'] >= date('Y-m-d');
    $qualification = trim($job['kualifikasi'] ?? '');
    return '<article class="card job-card"><span class="status-pill '.($active ? 'active' : 'closed').'">'.($active ? 'Lowongan aktif' : 'Pendaftaran ditutup').'</span><h3>'.e($job['posisi']).'</h3><p><strong>'.e($job['perusahaan']).'</strong></p><p>'.($qualification !== '' ? nl2br(e($qualification)) : 'Kualifikasi: hubungi BKK untuk informasi lengkap.').'</p><p class="muted">Batas pendaftaran: '.e(tgl($job['batas'])).'</p>'.($active ? '<a class="btn" href="halaman.php?m=kontak">Detail / hubungi BKK</a>' : '').'</article>';
  }, $r)).'</div>' : '<p class="muted">Belum ada informasi lowongan kerja.</p>';
}
if ($r = ambil('SELECT * FROM jadwal_tes WHERE tanggal >= CURDATE() ORDER BY tanggal')) {
  $menu['bkk'][2]['Jadwal Tes'] = tabel(['Tanggal','Perusahaan','Tahap'], array_map(fn($x)=>[tgl($x['tanggal']),e($x['perusahaan']),e($x['tahap'])], $r));
}
if (($r = ambil('SELECT * FROM mitra ORDER BY nama')) !== null) {
  $menu['kerjasama'][2]['Daftar Mitra'] = $r ? '<div class="grid g3">'.implode('', array_map(function($m){
    $logo = site_image_record('kerjasama','partner-'.$m['id'])
      ? '<img class="partner-logo" src="'.e(site_image_source('kerjasama','partner-'.$m['id'],'')).'" alt="'.e(site_image_alt('kerjasama','partner-'.$m['id'],'Logo '.$m['nama'])).'" loading="lazy">'
      : '<span class="partner-mark" aria-hidden="true">'.e(mb_strtoupper(mb_substr($m['nama'],0,2))).'</span>';
    return '<article class="card partner-card">'.$logo.'<h3>'.e($m['nama']).'</h3>'.(!empty($m['bidang_usaha']) ? '<p><strong>Bidang:</strong> '.e($m['bidang_usaha']).'</p>' : '').(!empty($m['jenis_kerjasama']) ? '<p><strong>Kerja sama:</strong> '.e($m['jenis_kerjasama']).'</p>' : '').(!empty($m['deskripsi']) ? '<p>'.nl2br(e($m['deskripsi'])).'</p>' : '<p class="muted">Informasi kerja sama akan diperbarui sekolah.</p>').'</article>';
  }, $r)).'</div>' : '<p class="muted">Informasi perusahaan mitra belum tersedia.</p>';
}
if ($pdo) {
  foreach (['profil','akademik','kesiswaan','informasi','fasilitas','bkk','kerjasama','ppdb'] as $pageKey) {
    if (!isset($menu[$pageKey])) continue;
    $menu[$pageKey][1] = school_setting($pdo, 'page_'.$pageKey.'_description', $menu[$pageKey][1]);
  }
  foreach (school_editable_page_sections() as $pageKey=>$sections) {
    foreach ($sections as $sectionTitle=>$sectionKey) {
      $sectionText = school_setting($pdo, 'page_'.$pageKey.'_section_'.$sectionKey);
      if ($sectionText !== '') $menu[$pageKey][2][$sectionTitle] = '<div class="managed-page-copy">'.nl2br(e($sectionText)).'</div>';
    }
  }
}
function berita_terbaru($n = 3){
  $r = ambil('SELECT judul, tanggal FROM berita ORDER BY tanggal DESC LIMIT '.(int)$n);
  return $r ?: [['judul'=>'Siswa RPL juarai LKS tingkat provinsi','tanggal'=>'2026-10-12'],['judul'=>'Kunjungan industri ke pusat data','tanggal'=>'2026-10-03'],['judul'=>'Workshop UI/UX bersama mitra industri','tanggal'=>'2026-09-25']];
}
