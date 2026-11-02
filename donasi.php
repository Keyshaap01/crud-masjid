<?php
$pageTitle = 'Infaq & Donasi';
require __DIR__ . '/includes/header.php';

$saldoAkhir = $kas['saldo_awal'] + $kas['pemasukan'] - $kas['pengeluaran'];
$persen = min(100, round($program_wakaf['terkumpul'] / $program_wakaf['target'] * 100));

// Kalkulator zakat (hanya menghitung, tidak menyimpan apa pun)
$zakat = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $harta = (float) str_replace(['.', ','], ['', '.'], $_POST['harta'] ?? '0');
    $hutang = (float) str_replace(['.', ','], ['', '.'], $_POST['hutang'] ?? '0');
    $hargaEmas = (float) str_replace(['.', ','], ['', '.'], $_POST['emas'] ?? '0');
    $nishab = 85 * $hargaEmas;
    $bersih = max(0, $harta - $hutang);
    $zakat = [
        'bersih' => $bersih,
        'nishab' => $nishab,
        'wajib'  => $hargaEmas > 0 && $bersih >= $nishab,
        'jumlah' => $bersih * 0.025,
    ];
}
?>

<section class="page-hero">
    <div class="hero-pattern"></div>
    <div class="container">
        <h1>Infaq & Donasi</h1>
        <p>"Perumpamaan orang yang menginfakkan hartanya di jalan Allah seperti sebutir biji yang menumbuhkan tujuh tangkai..." (QS. Al-Baqarah: 261)</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Rekening Resmi</span>
            <h2>Salurkan Melalui Transfer</h2>
        </div>
        <div class="grid-3">
            <?php foreach ($rekening as $r): ?>
                <div class="card bank">
                    <span class="badge"><?= e($r['peruntukan']) ?></span>
                    <h3><?= e($r['bank']) ?></h3>
                    <div class="acc">
                        <code data-copy-target><?= e($r['nomor']) ?></code>
                        <button class="btn-copy" data-copy="<?= e(str_replace(' ', '', $r['nomor'])) ?>">Salin</button>
                    </div>
                    <p class="muted">a.n. <?= e($r['atas_nama']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
        <p class="note">Setelah transfer, mohon konfirmasi ke WhatsApp DKM agar tercatat dalam laporan keuangan.
            <a href="https://wa.me/<?= e($site['whatsapp']) ?>?text=<?= rawurlencode('Assalamualaikum, saya ingin konfirmasi transfer infaq/donasi ke ' . $site['nama']) ?>" target="_blank" rel="noopener">Konfirmasi sekarang →</a></p>
    </div>
</section>

<section class="section section-alt">
    <div class="container two-col">
        <div class="card">
            <span class="eyebrow">Program Wakaf</span>
            <h2><?= e($program_wakaf['judul']) ?></h2>
            <div class="progress big"><div style="width: <?= $persen ?>%"></div></div>
            <div class="progress-info">
                <span>Terkumpul<br><strong><?= rupiah($program_wakaf['terkumpul']) ?></strong></span>
                <span class="right">Target<br><strong><?= rupiah($program_wakaf['target']) ?></strong></span>
            </div>
            <p class="muted">Kekurangan: <?= rupiah(max(0, $program_wakaf['target'] - $program_wakaf['terkumpul'])) ?></p>
        </div>

        <div class="card">
            <span class="eyebrow">Transparansi</span>
            <h2>Laporan Kas <?= e($kas['periode']) ?></h2>
            <table class="table plain">
                <tr><td>Saldo awal</td><td class="right"><?= rupiah($kas['saldo_awal']) ?></td></tr>
                <tr><td>Pemasukan</td><td class="right text-green">+ <?= rupiah($kas['pemasukan']) ?></td></tr>
                <tr><td>Pengeluaran</td><td class="right text-red">− <?= rupiah($kas['pengeluaran']) ?></td></tr>
                <tr class="total"><td>Saldo akhir</td><td class="right"><?= rupiah($saldoAkhir) ?></td></tr>
            </table>
        </div>
    </div>
</section>

<section class="section" id="zakat">
    <div class="container two-col">
        <div>
            <div class="section-head left">
                <span class="eyebrow">Kalkulator</span>
                <h2>Hitung Zakat Maal</h2>
            </div>
            <p class="prose">Zakat maal wajib dikeluarkan sebesar 2,5% apabila harta telah mencapai nishab (setara 85 gram emas) dan telah dimiliki selama satu tahun (haul).</p>
            <?php if ($zakat): ?>
                <div class="card result <?= $zakat['wajib'] ? 'ok' : '' ?>">
                    <p>Harta bersih: <strong><?= rupiah($zakat['bersih']) ?></strong></p>
                    <p>Nishab: <strong><?= rupiah($zakat['nishab']) ?></strong></p>
                    <?php if ($zakat['wajib']): ?>
                        <h3>Zakat Anda: <?= rupiah($zakat['jumlah']) ?></h3>
                    <?php else: ?>
                        <h3>Belum mencapai nishab</h3>
                        <p class="muted">Anda belum wajib zakat maal, namun tetap dianjurkan berinfaq dan bersedekah.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <form method="post" action="donasi.php#zakat" class="card form">
            <label>Total harta (tabungan, emas, investasi, dll.)
                <input type="text" name="harta" inputmode="numeric" data-rupiah required placeholder="cth. 150.000.000" value="<?= e($_POST['harta'] ?? '') ?>">
            </label>
            <label>Hutang jatuh tempo
                <input type="text" name="hutang" inputmode="numeric" data-rupiah placeholder="0" value="<?= e($_POST['hutang'] ?? '') ?>">
            </label>
            <label>Harga emas per gram saat ini
                <input type="text" name="emas" inputmode="numeric" data-rupiah required placeholder="cth. 1.500.000" value="<?= e($_POST['emas'] ?? '') ?>">
            </label>
            <button type="submit" class="btn btn-primary">Hitung Zakat</button>
            <small class="muted">Data tidak disimpan. Untuk konsultasi zakat, hubungi Amil DKM.</small>
        </form>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
