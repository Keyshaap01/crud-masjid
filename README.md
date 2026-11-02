# Website Masjid — PHP Native + MySQL (CRUD)

Website masjid berbasis PHP native (tanpa framework) dengan database MySQL dan panel admin CRUD.
Siap di-deploy ke **Azure App Service + Azure Database for MySQL**. Lihat **PANDUAN-AZURE.md**.

## Panel Admin (CRUD)
- URL: `/admin`. Login default `admin` / `masjid123`. **Ganti di Azure** lewat App Settings `ADMIN_USER` dan `ADMIN_PASS`.
- CRUD **Kegiatan** (tambah, lihat + cari, edit, hapus)
- CRUD **Jadwal Kajian**
- Tabel database dibuat dan diisi data contoh secara otomatis saat pertama kali diakses.

## Database
- **Di Azure:** MySQL. Kredensial dibaca dari environment variable `AZURE_MYSQL_*` (dibuat otomatis oleh wizard *Web App + Database*) atau `DB_HOST/DB_NAME/DB_USER/DB_PASS`.
- **Di komputer lokal:** kalau variabel di atas tidak ada, aplikasi otomatis memakai SQLite (`data/masjid.sqlite`), jadi bisa langsung dicoba tanpa memasang MySQL.

## Fitur
- **Beranda**: jadwal sholat hari ini, hitung mundur ke waktu sholat berikutnya, kajian hari ini, agenda terdekat, progres wakaf
- **Profil**: sejarah, visi & misi, fasilitas, struktur DKM
- **Jadwal Sholat**: tabel bulanan yang dihitung otomatis (metode Kemenag RI), bisa pindah bulan dan dicetak
- **Kajian**: jadwal kajian pekanan dan jadwal khatib Jumat
- **Kegiatan**: agenda dari database dengan filter kategori; kegiatan yang sudah lewat otomatis ditandai "Selesai"
- **Infaq & Donasi**: rekening dengan tombol salin, laporan kas, progres wakaf, kalkulator zakat maal
- **Kontak**: form yang meneruskan pesan ke WhatsApp DKM (tidak disimpan), dan peta Google Maps
- Tanggal Masehi dan Hijriah, tampilan responsif, tombol WhatsApp melayang

## Cara Menjalankan
**PHP built-in server** (PHP 8.1 atau lebih baru, dengan ekstensi pdo_sqlite untuk mode lokal):
```bash
cd masjid
php -S localhost:8000
```
Lalu buka http://localhost:8000

**XAMPP/Laragon**: salin folder `masjid` ke `htdocs` (atau `www`), lalu buka http://localhost/masjid

## Mengubah Konten
Edit `config.php`:
- `$site`: nama, alamat, kontak, **koordinat lat/lng** (untuk jadwal sholat), dan zona waktu
- `$profil`, `$kajian`, `$khatib_jumat`, `$kegiatan`, `$program_rutin`
- `$rekening`, `$kas`, `$program_wakaf`

Kalau masjid berada di luar WIB, ubah juga `date_default_timezone_set()` dan `'timezone'`.

## Struktur
```
masjid/
├── config.php          ← data masjid, akun admin default, data contoh
├── admin/              ← panel admin CRUD (login, kegiatan, kajian)
├── index.php, profil.php, jadwal.php, kajian.php, kegiatan.php, donasi.php, kontak.php
├── includes/
│   ├── header.php, footer.php
│   ├── db.php          ← koneksi database + pembuatan tabel otomatis
│   └── functions.php   ← jadwal sholat, tanggal Hijriah, helper
└── assets/css/style.css, assets/js/main.js
```

## Catatan
- Jadwal sholat dihitung secara astronomis (Subuh 20°, Isya 18°, Ashar Syafi'i, ihtiyat 2 menit). Selisih 1–2 menit dari jadwal resmi masih wajar.
- Tanggal Hijriah memakai kalender tabular, sehingga bisa berbeda ±1 hari dari penetapan resmi.
- Semua data (nama pengurus, rekening, dll.) hanya **contoh**. Ganti dengan data asli sebelum dipublikasikan.
