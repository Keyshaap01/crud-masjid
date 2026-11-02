<?php
require __DIR__ . '/_init.php';

if (is_login()) redirect('index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    cek_csrf();
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if (hash_equals(admin_user(), $user) && hash_equals(admin_pass(), $pass)) {
        session_regenerate_id(true);
        $_SESSION['admin'] = $user;
        flash('Selamat datang, ' . $user . '!');
        redirect('index.php');
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — <?= e($site['nama']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="login-page">
    <div class="hero-pattern"></div>
    <form method="post" class="card form login-card">
        <div class="center-text">
            <div class="brand-logo big">🕌</div>
            <h2>Login Admin</h2>
            <p class="muted"><?= e($site['nama']) ?></p>
        </div>
        <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
        <?= csrf_field() ?>
        <label>Username
            <input type="text" name="username" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
        </label>
        <label>Password
            <input type="password" name="password" required>
        </label>
        <button type="submit" class="btn btn-primary">Masuk</button>
        <a href="../index.php" class="center-text">← Kembali ke website</a>
    </form>
</body>
</html>
