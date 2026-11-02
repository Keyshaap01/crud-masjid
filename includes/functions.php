<?php
/**
 * Fungsi bantu: escape HTML, format rupiah, tanggal Indonesia,
 * perhitungan jadwal sholat (metode Kemenag RI), dan tanggal Hijriah.
 */

/** Julian Day Number (pengganti gregoriantojd agar tidak butuh ekstensi calendar). */
function jdn(int $m, int $d, int $y): int
{
    $a = intdiv(14 - $m, 12);
    $y = $y + 4800 - $a;
    $m = $m + 12 * $a - 3;
    return $d + intdiv(153 * $m + 2, 5) + 365 * $y + intdiv($y, 4) - intdiv($y, 100) + intdiv($y, 400) - 32045;
}

function e($s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function rupiah(int|float $n): string
{
    return 'Rp ' . number_format($n, 0, ',', '.');
}

const HARI  = ['Ahad', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
const BULAN = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli',
               'Agustus', 'September', 'Oktober', 'November', 'Desember'];
const BULAN_HIJRI = [1 => 'Muharram', 'Safar', 'Rabiul Awal', 'Rabiul Akhir', 'Jumadil Awal',
               'Jumadil Akhir', 'Rajab', "Sya'ban", 'Ramadhan', 'Syawal', "Dzulqa'dah", 'Dzulhijjah'];

function tanggal_indo(int $ts, bool $denganHari = true): string
{
    $str = date('j', $ts) . ' ' . BULAN[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    return $denganHari ? HARI[(int) date('w', $ts)] . ', ' . $str : $str;
}

/* ------------------------------------------------------------------
 *  TANGGAL HIJRIAH (algoritma aritmatika / tabular)
 *  Bisa berbeda ±1 hari dengan penetapan resmi pemerintah.
 * ------------------------------------------------------------------ */
function tanggal_hijriah(int $ts): string
{
    $d = (int) date('j', $ts);
    $m = (int) date('n', $ts);
    $y = (int) date('Y', $ts);

    $jd = jdn($m, $d, $y);
    $l  = $jd - 1948440 + 10632;
    $n  = intdiv($l - 1, 10631);
    $l  = $l - 10631 * $n + 354;
    $j  = intdiv(10985 - $l, 5316) * intdiv(50 * $l, 17719)
        + intdiv($l, 5670) * intdiv(43 * $l, 15238);
    $l  = $l - intdiv(30 - $j, 15) * intdiv(17719 * $j, 50)
        - intdiv($j, 16) * intdiv(15238 * $j, 43) + 29;
    $hm = intdiv(24 * $l, 709);
    $hd = $l - intdiv(709 * $hm, 24);
    $hy = 30 * $n + $j - 30;

    return $hd . ' ' . BULAN_HIJRI[$hm] . ' ' . $hy . ' H';
}

/* ------------------------------------------------------------------
 *  JADWAL SHOLAT
 *  Parameter Kemenag RI: Subuh 20°, Isya 18°, Ashar madzhab Syafi'i,
 *  ihtiyat (pengaman) 2 menit, Imsak = Subuh - 10 menit.
 * ------------------------------------------------------------------ */
function jadwal_sholat(int $ts, float $lat, float $lng, float $tz): array
{
    $dsin  = fn($x) => sin(deg2rad($x));
    $dcos  = fn($x) => cos(deg2rad($x));
    $dtan  = fn($x) => tan(deg2rad($x));
    $dasin = fn($x) => rad2deg(asin($x));
    $dacos = fn($x) => rad2deg(acos(max(-1, min(1, $x))));
    $fix   = fn($a, $b) => $a - $b * floor($a / $b);

    // Julian Day pada tengah hari lokal
    $jd = jdn((int) date('n', $ts), (int) date('j', $ts), (int) date('Y', $ts)) - 0.5
        + (12 - $tz) / 24;
    $D = $jd - 2451545.0;

    // Posisi matahari
    $g    = $fix(357.529 + 0.98560028 * $D, 360);
    $q    = $fix(280.459 + 0.98564736 * $D, 360);
    $L    = $fix($q + 1.915 * $dsin($g) + 0.020 * $dsin(2 * $g), 360);
    $eps  = 23.439 - 0.00000036 * $D;
    $RA   = $fix(rad2deg(atan2($dcos($eps) * $dsin($L), $dcos($L))) / 15, 24);
    $decl = $dasin($dsin($eps) * $dsin($L));
    $eqt  = $q / 15 - $RA;
    if ($eqt > 12)  $eqt -= 24;
    if ($eqt < -12) $eqt += 24;

    $dzuhur = 12 + $tz - $lng / 15 - $eqt;

    // Selisih waktu dari dzuhur saat matahari berada pada sudut tertentu di bawah ufuk
    $T = fn($angle) => $dacos(
        (-$dsin($angle) - $dsin($decl) * $dsin($lat)) / ($dcos($decl) * $dcos($lat))
    ) / 15;

    // Ashar (Syafi'i: panjang bayangan = 1x tinggi benda)
    $asrAlt = rad2deg(atan(1 / (1 + $dtan(abs($lat - $decl)))));
    $asrT   = $dacos(($dsin($asrAlt) - $dsin($decl) * $dsin($lat)) / ($dcos($decl) * $dcos($lat))) / 15;

    $ihtiyat = 2 / 60;
    $subuh   = $dzuhur - $T(20) + $ihtiyat;

    $raw = [
        'Imsak'   => $subuh - 10 / 60,
        'Subuh'   => $subuh,
        'Terbit'  => $dzuhur - $T(0.833) - $ihtiyat,
        'Dzuhur'  => $dzuhur + $ihtiyat,
        'Ashar'   => $dzuhur + $asrT + $ihtiyat,
        'Maghrib' => $dzuhur + $T(0.833) + $ihtiyat,
        'Isya'    => $dzuhur + $T(18) + $ihtiyat,
    ];

    $hasil = [];
    foreach ($raw as $nama => $jam) {
        $menit = (int) round($jam * 60);
        $hasil[$nama] = sprintf('%02d:%02d', intdiv($menit, 60) % 24, $menit % 60);
    }
    return $hasil;
}

/** Waktu sholat (wajib) berikutnya dari sekarang. */
function sholat_berikutnya(array $jadwal, int $now): array
{
    $wajib = ['Subuh', 'Dzuhur', 'Ashar', 'Maghrib', 'Isya'];
    $jamSekarang = date('H:i', $now);
    foreach ($wajib as $w) {
        if ($jadwal[$w] > $jamSekarang) {
            return ['nama' => $w, 'jam' => $jadwal[$w], 'besok' => false];
        }
    }
    return ['nama' => 'Subuh', 'jam' => null, 'besok' => true];
}

/** Menandai link navigasi yang sedang aktif. */
function nav_active(string $page, string $current): string
{
    return $page === $current ? ' class="active"' : '';
}
