<?php
$pageTitle = 'Profil Masjid';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
    <div class="hero-pattern"></div>
    <div class="container">
        <h1>Profil Masjid</h1>
        <p>Mengenal lebih dekat <?= e($site['nama']) ?></p>
    </div>
</section>

<section class="section">
    <div class="container two-col">
        <div>
            <div class="section-head left">
                <span class="eyebrow">Sejarah</span>
                <h2>Perjalanan Kami</h2>
            </div>
            <?php foreach ($profil['sejarah'] as $p): ?>
                <p class="prose"><?= e($p) ?></p>
            <?php endforeach; ?>
        </div>
        <div class="stats">
            <div class="stat"><strong><?= e($site['berdiri']) ?></strong><span>Tahun Berdiri</span></div>
            <div class="stat"><strong><?= number_format($site['kapasitas'], 0, ',', '.') ?>+</strong><span>Kapasitas Jamaah</span></div>
            <div class="stat"><strong><?= count($kajian) ?></strong><span>Kajian Rutin / Pekan</span></div>
            <div class="stat"><strong><?= date('Y') - $site['berdiri'] ?></strong><span>Tahun Melayani Umat</span></div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container two-col">
        <div class="card vm">
            <h3>🌙 Visi</h3>
            <p><?= e($profil['visi']) ?></p>
        </div>
        <div class="card vm">
            <h3>⭐ Misi</h3>
            <ol>
                <?php foreach ($profil['misi'] as $m): ?>
                    <li><?= e($m) ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Fasilitas</span>
            <h2>Fasilitas Masjid</h2>
        </div>
        <div class="grid-3">
            <?php foreach ($profil['fasilitas'] as $f): ?>
                <div class="card feature">
                    <div class="feature-icon"><?= $f['ikon'] ?></div>
                    <h3><?= e($f['nama']) ?></h3>
                    <p><?= e($f['ket']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Kepengurusan</span>
            <h2>Struktur DKM</h2>
        </div>
        <div class="grid-4">
            <?php foreach ($profil['pengurus'] as $p):
                $inisial = implode('', array_map(fn($w) => mb_substr($w, 0, 1),
                    array_slice(array_filter(explode(' ', preg_replace('/^(H\.|Hj\.|Ir\.|Ust\.|Ustzh\.|Dr\.|KH\.)\s*/', '', $p['nama']))), 0, 2)));
            ?>
                <div class="card person">
                    <div class="avatar"><?= e(strtoupper($inisial)) ?></div>
                    <h4><?= e($p['nama']) ?></h4>
                    <p class="muted"><?= e($p['jabatan']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
