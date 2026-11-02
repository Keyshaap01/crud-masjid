<?php
/**
 * CREATE & UPDATE — form tambah / edit jadwal kajian.
 */
require __DIR__ . '/_init.php';
wajib_login();

$id   = (int) ($_GET['id'] ?? 0);
$edit = $id > 0;
$data = ['hari' => 'Senin', 'waktu' => '', 'judul' => '', 'kitab' => '', 'pemateri' => ''];

if ($edit) {
    $row = db_one('SELECT * FROM kajian WHERE id = ?', [$id]);
    if (!$row) {
        flash('Data tidak ditemukan.', 'error');
        redirect('kajian.php');
    }
    $data = $row;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_csrf();
    foreach (['hari', 'waktu', 'judul', 'kitab', 'pemateri'] as $f) {
        $data[$f] = trim($_POST[$f] ?? '');
    }

    if (!in_array($data['hari'], DAFTAR_HARI, true))                      $errors['hari'] = 'Hari tidak valid.';
    if ($data['waktu'] === '' || mb_strlen($data['waktu']) > 50)          $errors['waktu'] = 'Waktu wajib diisi.';
    if ($data['judul'] === '' || mb_strlen($data['judul']) > 150)         $errors['judul'] = 'Judul wajib diisi (maks. 150 karakter).';
    if (mb_strlen($data['kitab']) > 100)                                  $errors['kitab'] = 'Maksimal 100 karakter.';
    if ($data['pemateri'] === '' || mb_strlen($data['pemateri']) > 100)   $errors['pemateri'] = 'Pemateri wajib diisi.';

    if (!$errors) {
        $params = [$data['hari'], $data['waktu'], $data['judul'], $data['kitab'] ?: '-', $data['pemateri']];
        if ($edit) {
            db_exec('UPDATE kajian SET hari = ?, waktu = ?, judul = ?, kitab = ?, pemateri = ? WHERE id = ?', [...$params, $id]);
            flash('Jadwal kajian berhasil diperbarui.');
        } else {
            db_exec('INSERT INTO kajian (hari, waktu, judul, kitab, pemateri) VALUES (?, ?, ?, ?, ?)', $params);
            flash('Jadwal kajian baru berhasil ditambahkan.');
        }
        redirect('kajian.php');
    }
}

$aktif = 'kajian';
$judul = $edit ? 'Edit Jadwal Kajian' : 'Tambah Jadwal Kajian';
require __DIR__ . '/_top.php';

function err(array $errors, string $f): string
{
    return isset($errors[$f]) ? '<small class="error">' . e($errors[$f]) . '</small>' : '';
}
?>

<form method="post" class="card form admin-form" novalidate>
    <?= csrf_field() ?>
    <div class="form-row">
        <label>Hari
            <select name="hari">
                <?php foreach (DAFTAR_HARI as $h): ?>
                    <option<?= $data['hari'] === $h ? ' selected' : '' ?>><?= e($h) ?></option>
                <?php endforeach; ?>
            </select>
            <?= err($errors, 'hari') ?>
        </label>
        <label>Waktu
            <input type="text" name="waktu" maxlength="50" placeholder="cth. Ba'da Maghrib" required value="<?= e($data['waktu']) ?>">
            <?= err($errors, 'waktu') ?>
        </label>
    </div>
    <label>Judul / Tema Kajian
        <input type="text" name="judul" maxlength="150" required value="<?= e($data['judul']) ?>">
        <?= err($errors, 'judul') ?>
    </label>
    <div class="form-row">
        <label>Kitab (opsional)
            <input type="text" name="kitab" maxlength="100" value="<?= e($data['kitab'] === '-' ? '' : $data['kitab']) ?>">
            <?= err($errors, 'kitab') ?>
        </label>
        <label>Pemateri
            <input type="text" name="pemateri" maxlength="100" required value="<?= e($data['pemateri']) ?>">
            <?= err($errors, 'pemateri') ?>
        </label>
    </div>
    <div class="form-actions">
        <a href="kajian.php" class="btn btn-ghost">Batal</a>
        <button type="submit" class="btn btn-primary-inline"><?= $edit ? 'Simpan Perubahan' : 'Tambah Kajian' ?></button>
    </div>
</form>

<?php require __DIR__ . '/_bottom.php'; ?>
