<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/db.php';

// Data dinamis dari database
$kajian   = ambil_kajian();
$kegiatan = ambil_kegiatan();

$current   = basename($_SERVER['SCRIPT_NAME'], '.php');
$pageTitle = isset($pageTitle) ? $pageTitle . ' — ' . $site['nama'] : $site['nama'] . ' | ' . $site['tagline'];
$now       = time();

$menu = [
    'index'    => 'Beranda',
    'profil'   => 'Profil',
    'jadwal'   => 'Jadwal Sholat',
    'kajian'   => 'Kajian',
    'kegiatan' => 'Kegiatan',
    'donasi'   => 'Infaq & Donasi',
    'kontak'   => 'Kontak',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="<?= e($site['nama'] . ' — ' . $site['tagline'] . '. ' . $site['alamat']) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🕌</text></svg>">
</head>
<body>

<div class="topbar">
    <div class="container topbar-inner">
        <span>📅 <?= e(tanggal_indo($now)) ?> &nbsp;•&nbsp; <?= e(tanggal_hijriah($now)) ?></span>
        <span class="topbar-right">📞 <?= e($site['telepon']) ?> &nbsp;•&nbsp; ✉️ <?= e($site['email']) ?></span>
    </div>
</div>

<header class="navbar" id="navbar">
    <div class="container nav-inner">
        <a href="index.php" class="brand">
            <span class="brand-logo">
                <svg viewBox="0 0 64 64" aria-hidden="true"><path fill="currentColor" d="M32 4c-1 6-8 9-8 16a8 8 0 0 0 16 0c0-7-7-10-8-16Zm-18 26c-6 4-10 10-10 18v12h16V48c0-7-2-13-6-18Zm36 0c-4 5-6 11-6 18v12h16V48c0-8-4-14-10-18ZM32 26c-8 3-12 10-12 18v16h24V44c0-8-4-15-12-18Zm-4 22a4 4 0 0 1 8 0v12h-8V48Z"/></svg>
            </span>
            <span>
                <strong><?= e($site['nama']) ?></strong>
                <small><?= e($site['kota']) ?></small>
            </span>
        </a>
        <button class="nav-toggle" id="navToggle" aria-label="Buka menu">
            <span></span><span></span><span></span>
        </button>
        <nav class="nav-menu" id="navMenu">
            <?php foreach ($menu as $file => $label): ?>
                <a href="<?= $file ?>.php"<?= nav_active($file, $current) ?>><?= e($label) ?></a>
            <?php endforeach; ?>
        </nav>
    </div>
</header>

<main>
