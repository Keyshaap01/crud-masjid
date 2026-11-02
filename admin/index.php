<?php
require __DIR__ . '/_init.php';
wajib_login();

$today = date('Y-m-d');
$stat = [
    'kegiatan'   => (int) db()->query('SELECT COUNT(*) FROM kegiatan')->fetchColumn(),
    'mendatang'  => (int) db_one('SELECT COUNT(*) AS n FROM kegiatan WHERE tanggal >= ?', [$today])['n'],
    'kajian'     => (int) db()->query('SELECT COUNT(*) FROM kajian')->fetchColumn(),
];
$terbaru = db_all('SELECT * FROM kegiatan WHERE tanggal >= ? ORDER BY tanggal ASC LIMIT 5', [$today]);
$driver  = is_mysql(db()) ? 'MySQL (Azure Database for MySQL)' : 'SQLite (mode lokal)';

$judul = 'Dashboard';
require __DIR__ . '/_top.php';
?>

<div class="admin-stats">
    <div class="card stat-card"><span>Total Kegiatan</span><strong><?= $stat['kegiatan'] ?></strong></div>
    <div class="card stat-card"><span>Kegiatan Mendatang</span><strong><?= $stat['mendatang'] ?></strong></div>
    <div class="card stat-card"><span>Jadwal Kajian</span><strong><?= $stat['kajian'] ?></strong></div>
    <div class="card stat-card"><span>Database</span><strong class="small"><?= e($driver) ?></strong></div>
</div>

<div class="card">
    <div class="card-head">
        <h3>Kegiatan Mendatang</h3>
        <a href="kegiatan-form.php" class="btn btn-sm btn-primary-inline">+ Tambah Kegiatan</a>
    </div>
    <?php if ($terbaru): ?>
        <div class="table-wrap">
            <table class="table">
                <thead><tr><th>Tanggal</th><th>Judul</th><th>Kategori</th><th>Tempat</th></tr></thead>
                <tbody>
                <?php foreach ($terbaru as $k): ?>
                    <tr>
                        <td><?= e(tanggal_indo(strtotime($k['tanggal']), false)) ?></td>
                        <td><a href="kegiatan-form.php?id=<?= (int) $k['id'] ?>"><?= e($k['judul']) ?></a></td>
                        <td><span class="badge"><?= e($k['kategori']) ?></span></td>
                        <td><?= e($k['tempat']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="muted">Belum ada kegiatan mendatang.</p>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/_bottom.php'; ?>
