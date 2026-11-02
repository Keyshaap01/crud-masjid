<?php
$pageTitle = 'Kegiatan';
require __DIR__ . '/includes/header.php';

$kategoriList = KATEGORI_KEGIATAN;
$filter = $_GET['kategori'] ?? '';
if (!in_array($filter, $kategoriList, true)) {
    $filter = '';
}
$tampil = $filter ? array_filter($kegiatan, fn($k) => $k['kategori'] === $filter) : $kegiatan;
$today  = strtotime('today');

// Kegiatan mendatang (terdekat dulu), lalu yang sudah lewat (terbaru dulu)
$akanDatang = array_filter($tampil, fn($k) => strtotime($k['tanggal']) >= $today);
$selesaiList = array_reverse(array_filter($tampil, fn($k) => strtotime($k['tanggal']) < $today));
$tampil = array_merge($akanDatang, $selesaiList);
?>

<section class="page-hero">
    <div class="hero-pattern"></div>
    <div class="container">
        <h1>Agenda Kegiatan</h1>
        <p>Mari hadir dan meramaikan kegiatan masjid</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="filters">
            <a href="kegiatan.php" class="chip<?= $filter === '' ? ' active' : '' ?>">Semua</a>
            <?php foreach ($kategoriList as $kat): ?>
                <a href="?kategori=<?= urlencode($kat) ?>" class="chip<?= $filter === $kat ? ' active' : '' ?>"><?= e($kat) ?></a>
            <?php endforeach; ?>
        </div>

        <?php if (!$tampil): ?>
            <div class="card"><p>Belum ada kegiatan pada kategori ini.</p></div>
        <?php endif; ?>
        <div class="grid-2">
            <?php foreach ($tampil as $k):
                $t = strtotime($k['tanggal']);
                $selesai = $t < $today;
            ?>
                <article class="card event<?= $selesai ? ' done' : '' ?>">
                    <div class="event-date big">
                        <strong><?= date('j', $t) ?></strong>
                        <span><?= e(BULAN[(int) date('n', $t)]) ?></span>
                        <small><?= date('Y', $t) ?></small>
                    </div>
                    <div class="event-body">
                        <span class="badge"><?= e($k['kategori']) ?></span>
                        <?php if ($selesai): ?><span class="badge badge-grey">Selesai</span><?php endif; ?>
                        <h3><?= e($k['judul']) ?></h3>
                        <p><?= e($k['keterangan']) ?></p>
                        <ul class="meta">
                            <li>📅 <?= e(tanggal_indo($t)) ?></li>
                            <li>🕐 <?= e($k['jam']) ?></li>
                            <li>📍 <?= e($k['tempat']) ?></li>
                        </ul>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
