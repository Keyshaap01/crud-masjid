<?php
/**
 * Inisialisasi panel admin: session, autentikasi, CSRF, dan pesan flash.
 */
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'cookie_secure'   => !empty($_SERVER['HTTPS']) || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https',
]);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';

function admin_user(): string { return env('ADMIN_USER') ?? ADMIN_USER_DEFAULT; }
function admin_pass(): string { return env('ADMIN_PASS') ?? ADMIN_PASS_DEFAULT; }

function is_login(): bool
{
    return !empty($_SESSION['admin']);
}

function wajib_login(): void
{
    if (!is_login()) {
        header('Location: login.php');
        exit;
    }
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . csrf_token() . '">';
}

function cek_csrf(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419);
        exit('Sesi kedaluwarsa. Silakan muat ulang halaman.');
    }
}

function flash(?string $pesan = null, string $tipe = 'success'): ?array
{
    if ($pesan !== null) {
        $_SESSION['flash'] = ['pesan' => $pesan, 'tipe' => $tipe];
        return null;
    }
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/** Validasi format tanggal Y-m-d. */
function tanggal_valid(string $t): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $t);
    return $d && $d->format('Y-m-d') === $t;
}
