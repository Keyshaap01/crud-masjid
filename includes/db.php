<?php
/**
 * KONEKSI DATABASE
 *
 * - Di Azure: memakai Azure Database for MySQL (Flexible Server).
 *   Kredensial dibaca dari App Settings / environment variable:
 *     AZURE_MYSQL_HOST, AZURE_MYSQL_DBNAME, AZURE_MYSQL_USERNAME, AZURE_MYSQL_PASSWORD, AZURE_MYSQL_PORT
 *   (otomatis dibuat oleh wizard "Web App + Database"), atau
 *     DB_HOST, DB_NAME, DB_USER, DB_PASS, DB_PORT (jika diisi manual).
 *
 * - Di komputer lokal (tanpa env di atas): otomatis memakai SQLite (file data/masjid.sqlite)
 *   sehingga bisa langsung dicoba tanpa memasang MySQL.
 *
 * Tabel dibuat otomatis saat pertama kali dijalankan dan diisi data contoh.
 */

function env(string ...$keys): ?string
{
    foreach ($keys as $k) {
        $v = getenv($k);
        if ($v === false && isset($_SERVER[$k])) $v = $_SERVER[$k];
        if ($v !== false && $v !== '' && $v !== null) return (string) $v;
    }
    return null;
}

function db(): PDO
{
    static $pdo = null;
    if ($pdo) return $pdo;

    $host = env('AZURE_MYSQL_HOST', 'DB_HOST');
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        if ($host) {
            $name = env('AZURE_MYSQL_DBNAME', 'DB_NAME') ?? 'masjid';
            $user = env('AZURE_MYSQL_USERNAME', 'DB_USER') ?? '';
            $pass = env('AZURE_MYSQL_PASSWORD', 'DB_PASS') ?? '';
            $port = env('AZURE_MYSQL_PORT', 'DB_PORT') ?? '3306';

            // Azure MySQL mewajibkan koneksi SSL (require_secure_transport = ON)
            foreach (['/etc/ssl/certs/ca-certificates.crt', '/etc/pki/tls/certs/ca-bundle.crt'] as $ca) {
                if (is_readable($ca)) {
                    $options[PDO::MYSQL_ATTR_SSL_CA] = $ca;
                    break;
                }
            }

            $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, $options);
            $pdo->exec("SET time_zone = '+07:00'");
        } else {
            $dir = __DIR__ . '/../data';
            if (!is_dir($dir)) {
                mkdir($dir, 0775, true);
                file_put_contents($dir . '/.htaccess', "Require all denied\n");
            }
            $pdo = new PDO('sqlite:' . $dir . '/masjid.sqlite', null, null, $options);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        error_log('Koneksi database gagal: ' . $e->getMessage());
        exit('<h1>Koneksi database gagal</h1><p>Periksa konfigurasi database pada App Settings.</p>'
           . (env('APP_DEBUG') ? '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>' : ''));
    }

    migrate($pdo);
    return $pdo;
}

function is_mysql(PDO $pdo): bool
{
    return $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql';
}

/** Membuat tabel jika belum ada, lalu mengisi data contoh bila tabel masih kosong. */
function migrate(PDO $pdo): void
{
    $pk   = is_mysql($pdo) ? 'INT AUTO_INCREMENT PRIMARY KEY' : 'INTEGER PRIMARY KEY AUTOINCREMENT';
    $tail = is_mysql($pdo) ? ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4' : '';

    $pdo->exec("CREATE TABLE IF NOT EXISTS kegiatan (
        id          $pk,
        judul       VARCHAR(150) NOT NULL,
        kategori    VARCHAR(50)  NOT NULL,
        tanggal     DATE         NOT NULL,
        jam         VARCHAR(50)  NOT NULL,
        tempat      VARCHAR(100) NOT NULL,
        keterangan  TEXT,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )$tail");

    $pdo->exec("CREATE TABLE IF NOT EXISTS kajian (
        id          $pk,
        hari        VARCHAR(10)  NOT NULL,
        waktu       VARCHAR(50)  NOT NULL,
        judul       VARCHAR(150) NOT NULL,
        kitab       VARCHAR(100),
        pemateri    VARCHAR(100) NOT NULL,
        created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )$tail");

    // Data contoh (seed) diambil dari config.php
    global $seed_kegiatan, $seed_kajian;

    if ((int) $pdo->query('SELECT COUNT(*) FROM kegiatan')->fetchColumn() === 0 && !empty($seed_kegiatan)) {
        $st = $pdo->prepare('INSERT INTO kegiatan (judul, kategori, tanggal, jam, tempat, keterangan) VALUES (?, ?, ?, ?, ?, ?)');
        foreach ($seed_kegiatan as $k) {
            $st->execute([$k['judul'], $k['kategori'], $k['tanggal'], $k['jam'], $k['tempat'], $k['ket']]);
        }
    }
    if ((int) $pdo->query('SELECT COUNT(*) FROM kajian')->fetchColumn() === 0 && !empty($seed_kajian)) {
        $st = $pdo->prepare('INSERT INTO kajian (hari, waktu, judul, kitab, pemateri) VALUES (?, ?, ?, ?, ?)');
        foreach ($seed_kajian as $k) {
            $st->execute([$k['hari'], $k['waktu'], $k['judul'], $k['kitab'], $k['pemateri']]);
        }
    }
}

/* ---------- Helper query ---------- */
function db_all(string $sql, array $params = []): array
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function db_one(string $sql, array $params = []): ?array
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->fetch() ?: null;
}

function db_exec(string $sql, array $params = []): int
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st->rowCount();
}

/* ---------- Query yang dipakai di halaman publik ---------- */
const URUTAN_HARI = ['Senin' => 1, 'Selasa' => 2, 'Rabu' => 3, 'Kamis' => 4, 'Jumat' => 5, 'Sabtu' => 6, 'Ahad' => 7];

function ambil_kajian(): array
{
    $rows = db_all('SELECT * FROM kajian');
    usort($rows, fn($a, $b) => (URUTAN_HARI[$a['hari']] ?? 9) <=> (URUTAN_HARI[$b['hari']] ?? 9) ?: $a['id'] <=> $b['id']);
    return $rows;
}

function ambil_kegiatan(?string $kategori = null): array
{
    return $kategori
        ? db_all('SELECT * FROM kegiatan WHERE kategori = ? ORDER BY tanggal ASC', [$kategori])
        : db_all('SELECT * FROM kegiatan ORDER BY tanggal ASC');
}
