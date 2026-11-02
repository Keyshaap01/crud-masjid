<?php
/**
 * READ & DELETE — daftar kegiatan dan aksi hapus.
 */
require __DIR__ . '/_init.php';
wajib_login();

// DELETE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    cek_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $row = db_one('SELECT judul FROM kegiatan WHERE id = ?', [$id]);
    if ($row) {
        db_exec('DELETE FROM kegiatan WHERE id = ?', [$id]);
        flash('Kegiatan "' . $row['judul'] . '" berhasil dihapus.');
    } else {
        flash('Data tidak ditemukan.', 'error');
    }
    redirect('kegiatan.php');
}

// READ (dengan pencarian)
$q = trim($_GET['q'] ?? '');
$rows = $q !== ''
    ? db_all('SELECT * FROM kegiatan WHERE judul LIKE ? OR tempat LIKE ? OR kategori LIKE ? ORDER BY tanggal DESC',
             ["%$q%", "%$q%", "%$q%"])
    : db_all('SELECT * FROM kegiatan ORDER BY tanggal DESC');
$today = date('Y-m-d');

$judul = 'Kelola Kegiatan';
require __DIR__ . '/_top.php';
?>

<div class="card">
    <div class="card-head">
        <form method="get" class="search">
            <input type="search" name="q" placeholder="Cari judul, tempat, kategori..." value="<?= e($q) ?>">
            <button class="btn btn-sm btn-ghost">Cari</button>
        </form>
        <a href="kegiatan-form.php" class="btn btn-sm btn-primary-inline">+ Tambah Kegiatan</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>#</th><th>Tanggal</th><th>Judul</th><th>Kategori</th><th>Jam</th><th>Tempat</th><th>Status</th><th class="right">Aksi</th></tr>
            </thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="8" class="muted center-text">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $i => $k): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td class="nowrap"><?= e(tanggal_indo(strtotime($k['tanggal']), false)) ?></td>
                    <td><strong><?= e($k['judul']) ?></strong></td>
                    <td><span class="badge"><?= e($k['kategori']) ?></span></td>
                    <td><?= e($k['jam']) ?></td>
                    <td><?= e($k['tempat']) ?></td>
                    <td><?= $k['tanggal'] >= $today ? '<span class="badge badge-gold">Mendatang</span>' : '<span class="badge badge-grey">Selesai</span>' ?></td>
                    <td class="right nowrap">
                        <a href="kegiatan-form.php?id=<?= (int) $k['id'] ?>" class="btn btn-sm btn-ghost">Edit</a>
                        <form method="post" class="inline" data-confirm="Hapus kegiatan &quot;<?= e($k['judul']) ?>&quot;?">
                            <?= csrf_field() ?>
                            <input type="hidden" name="aksi" value="hapus">
                            <input type="hidden" name="id" value="<?= (int) $k['id'] ?>">
                            <button class="btn btn-sm btn-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/_bottom.php'; ?>
