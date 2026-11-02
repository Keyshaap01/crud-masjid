<?php
/**
 * Halaman kontak. Form tidak disimpan ke database:
 * setelah divalidasi, pengunjung diarahkan ke WhatsApp DKM dengan pesan yang sudah terisi.
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$old = ['nama' => '', 'keperluan' => 'Informasi umum', 'pesan' => ''];
$keperluanList = ['Informasi umum', 'Akad nikah', 'Konsultasi zakat', 'Peminjaman aula', 'Pendaftaran TPA', 'Lainnya'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['nama']      = trim($_POST['nama'] ?? '');
    $old['keperluan'] = $_POST['keperluan'] ?? '';
    $old['pesan']     = trim($_POST['pesan'] ?? '');

    if ($old['nama'] === '' || mb_strlen($old['nama']) > 80) $errors['nama'] = 'Nama wajib diisi (maks. 80 karakter).';
    if (!in_array($old['keperluan'], $keperluanList, true))     $errors['keperluan'] = 'Pilih keperluan yang tersedia.';
    if (mb_strlen($old['pesan']) < 10)                         $errors['pesan'] = 'Pesan minimal 10 karakter.';

    if (!$errors) {
        $teks = "Assalamu'alaikum DKM {$site['nama']},\n\n"
              . "Nama: {$old['nama']}\n"
              . "Keperluan: {$old['keperluan']}\n\n"
              . $old['pesan'];
        header('Location: https://wa.me/' . $site['whatsapp'] . '?text=' . rawurlencode($teks));
        exit;
    }
}

$pageTitle = 'Kontak';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="hero-pattern"></div>
    <div class="container">
        <h1>Hubungi Kami</h1>
        <p>Pintu masjid selalu terbuka untuk Anda</p>
    </div>
</section>

<section class="section">
    <div class="container two-col">
        <div>
            <div class="contact-list">
                <div class="card contact"><span>📍</span><div><h4>Alamat</h4><p><?= e($site['alamat']) ?></p></div></div>
                <div class="card contact"><span>📞</span><div><h4>Telepon / WhatsApp</h4><p><?= e($site['telepon']) ?></p></div></div>
                <div class="card contact"><span>✉️</span><div><h4>Email</h4><p><?= e($site['email']) ?></p></div></div>
                <div class="card contact"><span>🕐</span><div><h4>Jam Sekretariat</h4><p>Setiap hari, 08.00 – 21.00 WIB</p></div></div>
            </div>
        </div>

        <form method="post" class="card form" novalidate>
            <h3>Kirim Pesan via WhatsApp</h3>
            <label>Nama lengkap
                <input type="text" name="nama" value="<?= e($old['nama']) ?>" required maxlength="80">
                <?php if (isset($errors['nama'])): ?><small class="error"><?= e($errors['nama']) ?></small><?php endif; ?>
            </label>
            <label>Keperluan
                <select name="keperluan">
                    <?php foreach ($keperluanList as $k): ?>
                        <option<?= $old['keperluan'] === $k ? ' selected' : '' ?>><?= e($k) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['keperluan'])): ?><small class="error"><?= e($errors['keperluan']) ?></small><?php endif; ?>
            </label>
            <label>Pesan
                <textarea name="pesan" rows="5" required><?= e($old['pesan']) ?></textarea>
                <?php if (isset($errors['pesan'])): ?><small class="error"><?= e($errors['pesan']) ?></small><?php endif; ?>
            </label>
            <button type="submit" class="btn btn-primary">Kirim ke WhatsApp 💬</button>
            <small class="muted">Pesan tidak disimpan di website — Anda akan diarahkan ke WhatsApp DKM.</small>
        </form>
    </div>
</section>

<section class="map">
    <iframe src="<?= e($site['maps_embed']) ?>" loading="lazy" title="Lokasi <?= e($site['nama']) ?>" referrerpolicy="no-referrer-when-downgrade"></iframe>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
