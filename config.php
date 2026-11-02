<?php
/**
 * KONFIGURASI WEBSITE MASJID
 * Ubah semua data masjid di file ini saja. Tidak perlu database.
 */

date_default_timezone_set('Asia/Jakarta');

$site = [
    'nama'      => 'Masjid Al-Ikhlas',
    'tagline'   => 'Memakmurkan Masjid, Mempererat Ukhuwah',
    'alamat'    => 'Jl. Contoh Raya No. 12, Kel. Sukamaju, Kota Bandung, Jawa Barat 40257',
    'telepon'   => '0812-3456-7890',
    'whatsapp'  => '6281234567890',          // format internasional tanpa +
    'email'     => 'info@masjidalikhlas.id',
    'berdiri'   => 1998,
    'kapasitas' => 1200,

    // Koordinat untuk perhitungan jadwal sholat
    'lat'       => -6.9175,
    'lng'       => 107.6191,
    'timezone'  => 7,                        // WIB = 7, WITA = 8, WIT = 9
    'kota'      => 'Bandung',

    'maps_embed' => 'https://www.google.com/maps?q=-6.9175,107.6191&z=15&output=embed',

    'sosmed' => [
        'Instagram' => 'https://instagram.com/',
        'YouTube'   => 'https://youtube.com/',
        'Facebook'  => 'https://facebook.com/',
    ],
];

$profil = [
    'sejarah' => [
        'Masjid Al-Ikhlas didirikan pada tahun 1998 atas prakarsa warga dan tokoh masyarakat setempat yang merindukan tempat ibadah yang layak di lingkungan mereka. Bermula dari sebuah musholla kecil berukuran 8x10 meter, masjid ini terus berkembang berkat wakaf dan infaq jamaah.',
        'Pada tahun 2012 dilakukan renovasi besar sehingga masjid kini memiliki dua lantai dengan daya tampung lebih dari 1.200 jamaah, dilengkapi ruang serbaguna, perpustakaan, dan Taman Pendidikan Al-Qur\'an (TPA).',
    ],
    'visi' => 'Menjadi masjid yang makmur, menjadi pusat ibadah, pendidikan, dan pemberdayaan umat yang rahmatan lil \'alamin.',
    'misi' => [
        'Menyelenggarakan ibadah sholat berjamaah yang tertib dan khusyuk.',
        'Mengadakan kajian ilmu agama secara rutin untuk semua kalangan.',
        'Membina generasi muda melalui TPA dan Remaja Masjid.',
        'Menyalurkan zakat, infaq, dan sedekah secara amanah dan transparan.',
        'Menjadi pusat kegiatan sosial dan kepedulian bagi masyarakat sekitar.',
    ],
    'pengurus' => [
        ['jabatan' => 'Ketua DKM',       'nama' => 'H. Ahmad Fauzi'],
        ['jabatan' => 'Wakil Ketua',     'nama' => 'Ir. Budi Santoso'],
        ['jabatan' => 'Sekretaris',      'nama' => 'Rahmat Hidayat, S.Pd'],
        ['jabatan' => 'Bendahara',       'nama' => 'Hj. Siti Aminah'],
        ['jabatan' => 'Bidang Ibadah',   'nama' => 'Ust. Abdullah Syafi\'i'],
        ['jabatan' => 'Bidang Pendidikan','nama' => 'Ustzh. Nur Hasanah'],
        ['jabatan' => 'Bidang Sosial',   'nama' => 'Dedi Kurniawan'],
        ['jabatan' => 'Ketua Remaja Masjid','nama' => 'Fikri Ramadhan'],
    ],
    'fasilitas' => [
        ['ikon' => '🕌', 'nama' => 'Ruang Sholat 2 Lantai', 'ket' => 'Ber-AC, karpet tebal, kapasitas 1.200 jamaah'],
        ['ikon' => '🚿', 'nama' => 'Tempat Wudhu', 'ket' => 'Terpisah putra & putri, air bersih 24 jam'],
        ['ikon' => '📚', 'nama' => 'Perpustakaan', 'ket' => 'Lebih dari 1.500 judul buku islami'],
        ['ikon' => '🏫', 'nama' => 'Ruang TPA', 'ket' => '4 ruang kelas untuk belajar Al-Qur\'an'],
        ['ikon' => '🅿️', 'nama' => 'Area Parkir', 'ket' => 'Mobil & motor, dijaga petugas'],
        ['ikon' => '♿', 'nama' => 'Ramah Difabel', 'ket' => 'Jalur kursi roda & toilet khusus'],
    ],
];

/*
 * AKUN ADMIN (panel CRUD di /admin)
 * Di Azure, sebaiknya isi lewat App Settings: ADMIN_USER dan ADMIN_PASS.
 * Nilai di bawah hanya dipakai jika App Settings tidak diisi.
 */
define('ADMIN_USER_DEFAULT', 'admin');
define('ADMIN_PASS_DEFAULT', 'masjid123');

const KATEGORI_KEGIATAN = ['Kajian', 'Sosial', 'Pendidikan', 'Umum'];
const DAFTAR_HARI       = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Ahad'];

/* ---- Data contoh: hanya dipakai untuk mengisi database saat pertama kali dijalankan ---- */
$seed_kajian = [
    ['hari' => 'Senin',  'waktu' => 'Ba\'da Maghrib', 'judul' => 'Tafsir Al-Qur\'an',       'kitab' => 'Tafsir Ibnu Katsir',   'pemateri' => 'Ust. Abdullah Syafi\'i'],
    ['hari' => 'Selasa', 'waktu' => 'Ba\'da Subuh',   'judul' => 'Kajian Hadits',            'kitab' => 'Riyadhus Shalihin',    'pemateri' => 'Ust. Hasan Basri, Lc'],
    ['hari' => 'Rabu',   'waktu' => 'Ba\'da Maghrib', 'judul' => 'Fiqih Ibadah',             'kitab' => 'Fiqih Sunnah',         'pemateri' => 'Ust. Muhammad Ridwan'],
    ['hari' => 'Kamis',  'waktu' => 'Ba\'da Isya',    'judul' => 'Yasinan & Tahlil',          'kitab' => '-',                    'pemateri' => 'Jamaah Bapak-bapak'],
    ['hari' => 'Jumat',  'waktu' => '09.00 WIB',      'judul' => 'Majelis Taklim Ibu-ibu',   'kitab' => 'Kitab Akhlak',         'pemateri' => 'Ustzh. Nur Hasanah'],
    ['hari' => 'Sabtu',  'waktu' => 'Ba\'da Maghrib', 'judul' => 'Kajian Remaja',            'kitab' => 'Sirah Nabawiyah',      'pemateri' => 'Ust. Fajar Shiddiq'],
    ['hari' => 'Ahad',   'waktu' => 'Ba\'da Subuh',   'judul' => 'Kuliah Subuh Umum',        'kitab' => 'Tematik',              'pemateri' => 'Pemateri bergilir'],
];

$khatib_jumat = [
    ['tanggal' => '6 November 2026',  'khatib' => 'Ust. Hasan Basri, Lc',     'imam' => 'Ust. Abdullah Syafi\'i'],
    ['tanggal' => '13 November 2026', 'khatib' => 'Dr. H. Mahmud Yunus, M.Ag', 'imam' => 'Ust. Muhammad Ridwan'],
    ['tanggal' => '20 November 2026', 'khatib' => 'Ust. Fajar Shiddiq',       'imam' => 'Ust. Hasan Basri, Lc'],
    ['tanggal' => '27 November 2026', 'khatib' => 'KH. Abdul Karim',          'imam' => 'Ust. Abdullah Syafi\'i'],
];

$seed_kegiatan = [
    ['tanggal' => '2026-11-08', 'judul' => 'Santunan Anak Yatim & Dhuafa', 'tempat' => 'Aula Masjid', 'jam' => '08.00 – 11.00 WIB', 'ket' => 'Penyerahan santunan kepada 100 anak yatim dan dhuafa di sekitar masjid.', 'kategori' => 'Sosial'],
    ['tanggal' => '2026-11-15', 'judul' => 'Tabligh Akbar: Menjaga Iman di Era Digital', 'tempat' => 'Ruang Utama', 'jam' => 'Ba\'da Isya', 'ket' => 'Bersama Dr. H. Mahmud Yunus, M.Ag. Terbuka untuk umum.', 'kategori' => 'Kajian'],
    ['tanggal' => '2026-11-22', 'judul' => 'Kerja Bakti Bersih Masjid', 'tempat' => 'Lingkungan Masjid', 'jam' => '07.00 WIB', 'ket' => 'Mari bersama membersihkan rumah Allah. Sarapan disediakan.', 'kategori' => 'Umum'],
    ['tanggal' => '2026-11-29', 'judul' => 'Wisuda Santri TPA Angkatan ke-15', 'tempat' => 'Aula Masjid', 'jam' => '09.00 WIB', 'ket' => 'Wisuda 35 santri yang telah khatam Al-Qur\'an.', 'kategori' => 'Pendidikan'],
    ['tanggal' => '2026-12-06', 'judul' => 'Donor Darah Bersama PMI', 'tempat' => 'Halaman Masjid', 'jam' => '08.00 – 12.00 WIB', 'ket' => 'Bekerja sama dengan PMI Kota. Bawa KTP.', 'kategori' => 'Sosial'],
];

$program_rutin = [
    ['ikon' => '📖', 'nama' => 'TPA Al-Ikhlas', 'ket' => 'Senin – Jumat, 15.30 – 17.30 WIB. Untuk anak usia 5–12 tahun.'],
    ['ikon' => '🤝', 'nama' => 'Jumat Berkah', 'ket' => 'Pembagian nasi kotak gratis setiap Jumat setelah sholat Jumat.'],
    ['ikon' => '🎓', 'nama' => 'Tahsin Dewasa', 'ket' => 'Belajar memperbaiki bacaan Al-Qur\'an, Sabtu pagi 08.00 WIB.'],
    ['ikon' => '🧑‍🤝‍🧑', 'nama' => 'Remaja Masjid', 'ket' => 'Kegiatan pemuda: mentoring, olahraga, dan bakti sosial.'],
];

$rekening = [
    ['bank' => 'Bank Syariah Indonesia (BSI)', 'nomor' => '7123 4567 89', 'atas_nama' => 'DKM Masjid Al-Ikhlas', 'peruntukan' => 'Infaq & Operasional'],
    ['bank' => 'Bank Muamalat',                'nomor' => '1234 5678 90', 'atas_nama' => 'DKM Masjid Al-Ikhlas', 'peruntukan' => 'Zakat'],
    ['bank' => 'Bank BJB Syariah',             'nomor' => '0011 2233 44', 'atas_nama' => 'DKM Masjid Al-Ikhlas', 'peruntukan' => 'Wakaf Pembangunan'],
];

// Laporan keuangan ringkas (diisi manual oleh bendahara)
$kas = [
    'periode'     => 'Oktober 2026',
    'saldo_awal'  => 48250000,
    'pemasukan'   => 23750000,
    'pengeluaran' => 18400000,
];

$program_wakaf = [
    'judul'    => 'Wakaf Renovasi Tempat Wudhu & Toilet',
    'target'   => 150000000,
    'terkumpul'=> 97500000,
];
