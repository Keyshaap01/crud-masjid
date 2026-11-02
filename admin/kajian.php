<?php
/**
 * READ & DELETE — daftar jadwal kajian dan aksi hapus.
 */
require __DIR__ . '/_init.php';
wajib_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'hapus') {
    cek_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $row = db_one('SELECT judul FROM kajian WHERE id = ?', [$id]);
    if ($row) {
        db_exec('DELETE FROM kajian WHERE id = ?', [$id]);
        flash('Kajian "' . $row['judul'] . '" berhasil dihapus.');
    } else {
        flash('Data tidak ditemukan.', 'error');
    }
    redirect('kajian.php');
}

$rows = ambil_kajian();

$judul = 'Kelola Jadwal Kajian';
require __DIR__ . '/_top.php';
?>

<div class="card">
    <div class="card-head">
        <p class="muted"><?= count($rows) ?> jadwal kajian rutin</p>
        <a href="kajian-form.php" class="btn btn-sm btn-primary-inline">+ Tambah Kajian</a>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Hari</th><th>Waktu</th><th>Judul</th><th>Kitab</th><th>Pemateri</th><th class="right">Aksi</th></tr></thead>
            <tbody>
            <?php if (!$rows): ?>
                <tr><td colspan="6" class="muted center-text">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php foreach ($rows as $k): ?>
                <tr>
                    <td><strong><?= e($k['hari']) ?></strong></td>
                    <td><?= e($k['waktu']) ?></td>
                    <td><?= e($k['judul']) ?></td>
                    <td><?= e($k['kitab']) ?></td>
                    <td><?= e($k['pemateri']) ?></td>
                    <td class="right nowrap">
                        <a href="kajian-form.php?id=<?= (int) $k['id'] ?>" class="btn btn-sm btn-ghost">Edit</a>
                        <form method="post" class="inline" data-confirm="Hapus kajian &quot;<?= e($k['judul']) ?>&quot;?">
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
