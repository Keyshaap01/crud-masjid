<?php
$pageTitle = 'Jadwal Sholat';
require __DIR__ . '/includes/header.php';

// Bulan & tahun bisa dipilih lewat query string: jadwal.php?bulan=12&tahun=2026
$bulan = filter_input(INPUT_GET, 'bulan', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 12]]) ?: (int) date('n');
$tahun = filter_input(INPUT_GET, 'tahun', FILTER_VALIDATE_INT, ['options' => ['min_range' => 2000, 'max_range' => 2100]]) ?: (int) date('Y');

$jumlahHari = cal_days_in_month_safe($bulan, $tahun);
$prev = mktime(0, 0, 0, $bulan - 1, 1, $tahun);
$next = mktime(0, 0, 0, $bulan + 1, 1, $tahun);
$todayStr = date('Y-m-d', $now);

$jadwalHariIni = jadwal_sholat($now, $site['lat'], $site['lng'], $site['timezone']);

function cal_days_in_month_safe(int $m, int $y): int
{
    return (int) date('t', mktime(0, 0, 0, $m, 1, $y));
}
?>

<section class="page-hero">
    <div class="hero-pattern"></div>
    <div class="container">
        <h1>Jadwal Sholat</h1>
        <p>Wilayah <?= e($site['kota']) ?> dan sekitarnya</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="today-box">
            <div>
                <span class="eyebrow">Hari ini</span>
                <h2><?= e(tanggal_indo($now)) ?></h2>
                <p class="muted"><?= e(tanggal_hijriah($now)) ?></p>
            </div>
            <div class="prayer-grid compact">
                <?php foreach ($jadwalHariIni as $nama => $jam): ?>
                    <div class="prayer-item"><span><?= e($nama) ?></span><strong><?= e($jam) ?></strong></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="month-nav">
            <a class="btn btn-ghost" href="?bulan=<?= date('n', $prev) ?>&tahun=<?= date('Y', $prev) ?>">← <?= e(BULAN[(int) date('n', $prev)]) ?></a>
            <h2><?= e(BULAN[$bulan] . ' ' . $tahun) ?></h2>
            <a class="btn btn-ghost" href="?bulan=<?= date('n', $next) ?>&tahun=<?= date('Y', $next) ?>"><?= e(BULAN[(int) date('n', $next)]) ?> →</a>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Imsak</th><th>Subuh</th><th>Terbit</th><th>Dzuhur</th><th>Ashar</th><th>Maghrib</th><th>Isya</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($d = 1; $d <= $jumlahHari; $d++):
                        $ts = mktime(12, 0, 0, $bulan, $d, $tahun);
                        $j  = jadwal_sholat($ts, $site['lat'], $site['lng'], $site['timezone']);
                        $isToday = date('Y-m-d', $ts) === $todayStr;
                        $isJumat = date('w', $ts) === '5';
                    ?>
                        <tr class="<?= $isToday ? 'today' : '' ?><?= $isJumat ? ' jumat' : '' ?>">
                            <td><strong><?= $d ?></strong> <span class="muted"><?= e(HARI[(int) date('w', $ts)]) ?></span></td>
                            <?php foreach ($j as $jam): ?><td><?= e($jam) ?></td><?php endforeach; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
        <p class="note">
            Perhitungan: sudut Subuh 20°, Isya 18°, Ashar madzhab Syafi'i, ihtiyat 2 menit (standar Kemenag RI).
            Koordinat <?= e($site['lat']) ?>, <?= e($site['lng']) ?>. Selisih 1–2 menit dari jadwal resmi masih wajar.
        </p>
        <div class="center"><button class="btn btn-ghost" onclick="window.print()">🖨️ Cetak Jadwal</button></div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
