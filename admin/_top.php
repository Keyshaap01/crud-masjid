<?php
$menuAdmin = [
    'index'    => ['📊', 'Dashboard'],
    'kegiatan' => ['📅', 'Kegiatan'],
    'kajian'   => ['📖', 'Kajian'],
];
$aktif = $aktif ?? basename($_SERVER['SCRIPT_NAME'], '.php');
$f = flash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(($judul ?? 'Admin') . ' — Admin ' . $site['nama']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🕌</text></svg>">
</head>
<body class="admin">
<aside class="admin-side">
    <a href="index.php" class="admin-brand">🕌 <span>Admin<br><small><?= e($site['nama']) ?></small></span></a>
    <nav>
        <?php foreach ($menuAdmin as $file => [$ikon, $label]): ?>
            <a href="<?= $file ?>.php" class="<?= $aktif === $file ? 'active' : '' ?>"><?= $ikon ?> <?= e($label) ?></a>
        <?php endforeach; ?>
    </nav>
    <div class="admin-side-foot">
        <a href="../index.php" target="_blank">🌐 Lihat Website</a>
        <a href="logout.php">🚪 Keluar</a>
    </div>
</aside>
<main class="admin-main">
    <header class="admin-top">
        <h1><?= e($judul ?? 'Dashboard') ?></h1>
        <span class="muted">Masuk sebagai <strong><?= e($_SESSION['admin'] ?? '') ?></strong></span>
    </header>
    <?php if ($f): ?>
        <div class="alert alert-<?= e($f['tipe']) ?>"><?= e($f['pesan']) ?></div>
    <?php endif; ?>
