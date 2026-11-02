<?php
/**
 * CREATE & UPDATE — form tambah / edit kegiatan.
 * Tanpa ?id  → tambah data baru (INSERT)
 * Dengan ?id → ubah data (UPDATE)
 */
require __DIR__ . '/_init.php';
wajib_login();

$id   = (int) ($_GET['id'] ?? 0);
$edit = $id > 0;
$data = ['judul' => '', 'kategori' => KATEGORI_KEGIATAN[0], 'tanggal' => date('Y-m-d'), 'jam' => '', 'tempat' => '', 'keterangan' => ''];

if ($edit) {
    $row = db_one('SELECT * FROM kegiatan WHERE id = ?', [$id]);
    if (!$row) {
        flash('Data tidak ditemukan.', 'error');
        redirect('kegiatan.php');
    }
    $data = $row;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_csrf();
    foreach (array_keys($data) as $f) {
        if (in_array($f, ['judul', 'kategori', 'tanggal', 'jam', 'tempat', 'keterangan'], true)) {
            $data[$f] = trim($_POST[$f] ?? '');
        }
    }

    // Validasi
    if ($data['judul'] === '' || mb_strlen($data['judul']) > 150) $errors['judul'] = 'Judul wajib diisi (maks. 150 karakter).';
    if (!in_array($data['kategori'], KATEGORI_KEGIATAN, true))     $errors['kategori'] = 'Kategori tidak valid.';
    if (!tanggal_valid($data['tanggal']))                          $errors['tanggal'] = 'Tanggal tidak valid.';
    if ($data['jam'] === '' || mb_strlen($data['jam']) > 50)       $errors['jam'] = 'Jam wajib diisi.';
    if ($data['tempat'] === '' || mb_strlen($data['tempat']) > 100) $errors['tempat'] = 'Tempat wajib diisi.';

    if (!$errors) {
        $params = [$data['judul'], $data['kategori'], $data['tanggal'], $data['jam'], $data['tempat'], $data['keterangan']];
        if ($edit) {
            db_exec('UPDATE kegiatan SET judul = ?, kategori = ?, tanggal = ?, jam = ?, tempat = ?, keterangan = ? WHERE id = ?',
                    [...$params, $id]);
            flash('Kegiatan berhasil diperbarui.');
        } else {
            db_exec('INSERT INTO kegiatan (judul, kategori, tanggal, jam, tempat, keterangan) VALUES (?, ?, ?, ?, ?, ?)', $params);
            flash('Kegiatan baru berhasil ditambahkan.');
        }
        redirect('kegiatan.php');
    }
}

$aktif = 'kegiatan';
$judul = $edit ? 'Edit Kegiatan' : 'Tambah Kegiatan';
require __DIR__ . '/_top.php';

function err(array $errors, string $f): string
{
    return isset($errors[$f]) ? '<small class="error">' . e($errors[$f]) . '</small>' : '';
}
?>

<form method="post" class="card form admin-form" novalidate>
    <?= csrf_field() ?>
    <label>Judul Kegiatan
        <input type="text" name="judul" maxlength="150" required value="<?= e($data['judul']) ?>">
        <?= err($errors, 'judul') ?>
    </label>
    <div class="form-row">
        <label>Kategori
            <select name="kategori">
                <?php foreach (KATEGORI_KEGIATAN as $kat): ?>
                    <option<?= $data['kategori'] === $kat ? ' selected' : '' ?>><?= e($kat) ?></option>
                <?php endforeach; ?>
            </select>
            <?= err($errors, 'kategori') ?>
        </label>
        <label>Tanggal
            <input type="date" name="tanggal" required value="<?= e($data['tanggal']) ?>">
            <?= err($errors, 'tanggal') ?>
        </label>
    </div>
    <div class="form-row">
        <label>Jam
            <input type="text" name="jam" maxlength="50" placeholder="cth. 08.00 WIB / Ba'da Isya" required value="<?= e($data['jam']) ?>">
            <?= err($errors, 'jam') ?>
        </label>
        <label>Tempat
            <input type="text" name="tempat" maxlength="100" placeholder="cth. Aula Masjid" required value="<?= e($data['tempat']) ?>">
            <?= err($errors, 'tempat') ?>
        </label>
    </div>
    <label>Keterangan
        <textarea name="keterangan" rows="4"><?= e($data['keterangan']) ?></textarea>
    </label>
    <div class="form-actions">
        <a href="kegiatan.php" class="btn btn-ghost">Batal</a>
        <button type="submit" class="btn btn-primary-inline"><?= $edit ? 'Simpan Perubahan' : 'Tambah Kegiatan' ?></button>
    </div>
</form>

<?php require __DIR__ . '/_bottom.php'; ?>
