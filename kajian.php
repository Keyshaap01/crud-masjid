<?php
$pageTitle = 'Jadwal Kajian';
require __DIR__ . '/includes/header.php';
$hariIni = HARI[(int) date('w', $now)];
?>

<section class="page-hero">
    <div class="hero-pattern"></div>
    <div class="container">
        <h1>Kajian & Khutbah</h1>
        <p>Menuntut ilmu adalah kewajiban setiap muslim</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Rutin Pekanan</span>
            <h2>Jadwal Kajian</h2>
        </div>
        <div class="grid-3">
            <?php foreach ($kajian as $k): ?>
                <div class="card kajian<?= $k['hari'] === $hariIni ? ' is-today' : '' ?>">
                    <div class="kajian-head">
                        <span class="day"><?= e($k['hari']) ?></span>
                        <?php if ($k['hari'] === $hariIni): ?><span class="badge badge-gold">Hari ini</span><?php endif; ?>
                    </div>
                    <h3><?= e($k['judul']) ?></h3>
                    <ul class="meta">
                        <li>🕐 <?= e($k['waktu']) ?></li>
                        <li>📘 <?= e($k['kitab']) ?></li>
                        <li>🎙️ <?= e($k['pemateri']) ?></li>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Sholat Jumat</span>
            <h2>Jadwal Khatib & Imam</h2>
        </div>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Tanggal</th><th>Khatib</th><th>Imam</th></tr></thead>
                <tbody>
                    <?php foreach ($khatib_jumat as $k): ?>
                        <tr>
                            <td><strong><?= e($k['tanggal']) ?></strong></td>
                            <td><?= e($k['khatib']) ?></td>
                            <td><?= e($k['imam']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="note">Adzan Jumat dikumandangkan saat masuk waktu Dzuhur. Jamaah diharapkan hadir 15 menit sebelumnya.</p>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
