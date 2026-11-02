<?php
require __DIR__ . '/includes/header.php';

$jadwal   = jadwal_sholat($now, $site['lat'], $site['lng'], $site['timezone']);
$besok    = jadwal_sholat($now + 86400, $site['lat'], $site['lng'], $site['timezone']);
$next     = sholat_berikutnya($jadwal, $now);
$hariIni  = HARI[(int) date('w', $now)];
$kajianHariIni = array_values(array_filter($kajian, fn($k) => $k['hari'] === $hariIni));
$kegiatanTerdekat = array_slice(array_values(array_filter($kegiatan, fn($k) => strtotime($k['tanggal']) >= strtotime('today'))), 0, 3);
?>

<section class="hero">
    <div class="hero-pattern"></div>
    <div class="container hero-inner">
        <div class="hero-text">
            <p class="arabic">بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ</p>
            <h1>Selamat Datang di<br><span><?= e($site['nama']) ?></span></h1>
            <p class="lead"><?= e($site['tagline']) ?>. Pusat ibadah, ilmu, dan kepedulian umat di <?= e($site['kota']) ?>.</p>
            <div class="hero-actions">
                <a href="jadwal.php" class="btn btn-gold">Lihat Jadwal Sholat</a>
                <a href="donasi.php" class="btn btn-outline">Salurkan Infaq</a>
            </div>
        </div>

        <div class="next-prayer card-glass"
             data-countdown
             data-next="<?= e($next['besok'] ? $besok['Subuh'] : $next['jam']) ?>"
             data-besok="<?= $next['besok'] ? '1' : '0' ?>">
            <p class="np-label">Menuju waktu sholat</p>
            <h2 class="np-name"><?= e($next['nama']) ?></h2>
            <p class="np-time"><?= e($next['besok'] ? $besok['Subuh'] : $next['jam']) ?> WIB<?= $next['besok'] ? ' (besok)' : '' ?></p>
            <div class="np-countdown" data-countdown-display>--:--:--</div>
            <p class="np-clock">Sekarang <span data-clock><?= date('H:i:s', $now) ?></span></p>
        </div>
    </div>
</section>

<section class="prayer-strip">
    <div class="container">
        <div class="prayer-grid">
            <?php foreach ($jadwal as $nama => $jam): ?>
                <div class="prayer-item<?= $nama === $next['nama'] && !$next['besok'] ? ' is-next' : '' ?>">
                    <span><?= e($nama) ?></span>
                    <strong><?= e($jam) ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="note">Jadwal untuk wilayah <?= e($site['kota']) ?> dan sekitarnya • Metode Kemenag RI</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="ayat">
            <p class="arabic big">إِنَّمَا يَعْمُرُ مَسٰجِدَ اللّٰهِ مَنْ اٰمَنَ بِاللّٰهِ وَالْيَوْمِ الْاٰخِرِ</p>
            <p>"Sesungguhnya yang memakmurkan masjid-masjid Allah hanyalah orang-orang yang beriman kepada Allah dan hari kemudian..."</p>
            <cite>QS. At-Taubah: 18</cite>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Program</span>
            <h2>Program Rutin Masjid</h2>
        </div>
        <div class="grid-4">
            <?php foreach ($program_rutin as $p): ?>
                <div class="card feature">
                    <div class="feature-icon"><?= $p['ikon'] ?></div>
                    <h3><?= e($p['nama']) ?></h3>
                    <p><?= e($p['ket']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container two-col">
        <div>
            <div class="section-head left">
                <span class="eyebrow">Hari ini, <?= e($hariIni) ?></span>
                <h2>Kajian Hari Ini</h2>
            </div>
            <?php if ($kajianHariIni): ?>
                <?php foreach ($kajianHariIni as $k): ?>
                    <div class="card kajian-today">
                        <span class="badge"><?= e($k['waktu']) ?></span>
                        <h3><?= e($k['judul']) ?></h3>
                        <p>📘 <?= e($k['kitab']) ?></p>
                        <p>🎙️ <?= e($k['pemateri']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card"><p>Tidak ada kajian terjadwal hari ini.</p></div>
            <?php endif; ?>
            <a href="kajian.php" class="link-more">Lihat seluruh jadwal kajian →</a>
        </div>

        <div>
            <div class="section-head left">
                <span class="eyebrow">Agenda</span>
                <h2>Kegiatan Terdekat</h2>
            </div>
            <?php foreach ($kegiatanTerdekat as $k): $t = strtotime($k['tanggal']); ?>
                <div class="event-row">
                    <div class="event-date">
                        <strong><?= date('j', $t) ?></strong>
                        <span><?= e(substr(BULAN[(int) date('n', $t)], 0, 3)) ?></span>
                    </div>
                    <div>
                        <h4><?= e($k['judul']) ?></h4>
                        <p class="muted"><?= e($k['jam']) ?> • <?= e($k['tempat']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <a href="kegiatan.php" class="link-more">Semua kegiatan →</a>
        </div>
    </div>
</section>

<section class="cta">
    <div class="container cta-inner">
        <div>
            <h2><?= e($program_wakaf['judul']) ?></h2>
            <?php $persen = min(100, round($program_wakaf['terkumpul'] / $program_wakaf['target'] * 100)); ?>
            <div class="progress"><div style="width: <?= $persen ?>%"></div></div>
            <p>Terkumpul <strong><?= rupiah($program_wakaf['terkumpul']) ?></strong> dari <?= rupiah($program_wakaf['target']) ?> (<?= $persen ?>%)</p>
        </div>
        <a href="donasi.php" class="btn btn-gold">Ikut Berwakaf</a>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
