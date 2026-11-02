# Panduan Deploy Web App + Database ke Azure App Service

Panduan ini bisa langsung dipakai sebagai bagian **"Langkah Kerja"** pada laporan.
Tanda **📸 SS-x** menandai bagian yang perlu diambil screenshot-nya untuk laporan.

**Arsitektur:**

```
Pengguna ──HTTPS──► Azure App Service (PHP 8.x, Linux)
                          │  koneksi privat (VNet) + SSL
                          ▼
              Azure Database for MySQL – Flexible Server
                    tabel: kegiatan, kajian

GitHub (repo masjid-app) ──GitHub Actions──► App Service (deploy otomatis setiap push)
```

---

## Bagian A — Gambaran Aplikasi

**Website Masjid Al-Ikhlas** adalah aplikasi PHP native yang terdiri atas:

| Bagian | Fungsi |
|---|---|
| Halaman publik | Beranda, Profil, Jadwal Sholat, Kajian, Kegiatan, Donasi, Kontak |
| Panel admin (`/admin`) | Login, lalu CRUD **Kegiatan** dan **Jadwal Kajian** |
| Database | MySQL: tabel `kegiatan` dan `kajian` |

**Pemetaan operasi CRUD:**

| Operasi | File | Query SQL |
|---|---|---|
| **Create** | `admin/kegiatan-form.php`, `admin/kajian-form.php` | `INSERT INTO ...` |
| **Read** | `admin/kegiatan.php`, `admin/kajian.php`, `kegiatan.php`, `kajian.php` | `SELECT ...` |
| **Update** | `admin/kegiatan-form.php?id=..`, `admin/kajian-form.php?id=..` | `UPDATE ... WHERE id = ?` |
| **Delete** | tombol **Hapus** di `admin/kegiatan.php`, `admin/kajian.php` | `DELETE FROM ... WHERE id = ?` |

Semua query memakai **PDO prepared statement**, sehingga aman dari SQL injection. Form admin juga dilindungi **token CSRF**.

**Struktur tabel:**

```sql
CREATE TABLE kegiatan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(150) NOT NULL,
  kategori VARCHAR(50) NOT NULL,
  tanggal DATE NOT NULL,
  jam VARCHAR(50) NOT NULL,
  tempat VARCHAR(100) NOT NULL,
  keterangan TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE kajian (
  id INT AUTO_INCREMENT PRIMARY KEY,
  hari VARCHAR(10) NOT NULL,
  waktu VARCHAR(50) NOT NULL,
  judul VARCHAR(150) NOT NULL,
  kitab VARCHAR(100),
  pemateri VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Tabel **dibuat otomatis** oleh `includes/db.php` saat aplikasi pertama kali diakses, lalu diisi data contoh. Ini diperlukan karena database Azure berada di jaringan privat (VNet), sehingga tidak bisa diakses langsung dari laptop untuk import SQL.

---

## Bagian B — Membuat Web App + Database di Azure Portal

1. Login ke **https://portal.azure.com** dengan akun Azure for Students.
2. Klik **Create a resource**, cari **"Web App + Database"**, lalu klik **Create**. 📸 **SS-1**
3. Isi tab **Basics**:

   | Field | Isi |
   |---|---|
   | Subscription | Azure for Students |
   | Resource Group | **Create new**, misalnya `rg-masjid-praktikum` |
   | Region | Region yang diizinkan untuk akunmu (misalnya *Southeast Asia* / *Indonesia Central*) |
   | Name | nama unik, misalnya `masjid-crud-namakamu`. Nama ini menjadi URL `https://<nama>.azurewebsites.net` |
   | Runtime stack | **PHP 8.4** (atau 8.3) |
   | Engine | **MySQL – Flexible Server** |
   | Server name | otomatis (boleh diubah) |
   | Database name | otomatis (boleh diubah) |
   | Hosting plan | **Basic** (paling hemat) |

   📸 **SS-2** (tab Basics yang sudah diisi)

4. Klik **Review + create**. Pada halaman ringkasan, **catat username & password database** yang ditampilkan, karena hanya muncul sekali. Lalu klik **Create**. 📸 **SS-3**
5. Tunggu proses deployment selesai (±5–10 menit), lalu klik **Go to resource**. 📸 **SS-4** (pesan *Your deployment is complete*)

> Wizard ini otomatis membuat: App Service Plan, Web App, MySQL Flexible Server, Virtual Network, Private DNS Zone, dan menghubungkan Web App ke database.

📸 **SS-5**: buka Resource Group `rg-masjid-praktikum`, lalu screenshot daftar semua resource yang terbentuk.

---

## Bagian C — Mengecek & Mengatur Environment Variables

1. Buka **Web App**, lalu menu **Settings → Environment variables** → tab **App settings**.
2. Pastikan variabel database dari wizard sudah ada:
   `AZURE_MYSQL_HOST`, `AZURE_MYSQL_DBNAME`, `AZURE_MYSQL_USERNAME`, `AZURE_MYSQL_PASSWORD` (dan `AZURE_MYSQL_PORT` jika ada).

   > Jika nama variabelnya berbeda atau tidak ada, tambahkan manual dengan **+ Add**:
   > `DB_HOST` (= nama server, misalnya `xxxx-server.mysql.database.azure.com`), `DB_NAME`, `DB_USER`, `DB_PASS`.
   > Nilainya bisa dilihat di resource **MySQL Flexible Server → Overview** dan dari catatan di langkah B-4.

3. Tambahkan variabel untuk akun admin aplikasi dengan **+ Add**:

   | Name | Value |
   |---|---|
   | `ADMIN_USER` | username admin pilihanmu |
   | `ADMIN_PASS` | password yang kuat |

4. Klik **Apply**, lalu **Confirm**. Aplikasi akan restart otomatis. 📸 **SS-6** (nilai password boleh disamarkan/blur)

> Kode membaca variabel ini dengan `getenv()` di `includes/db.php`. Dengan begitu, password tidak ditulis di kode yang di-push ke GitHub.

---

## Bagian D — Upload Kode ke GitHub

1. Ekstrak `masjid-crud.zip`. Pastikan `index.php` ada di **root** repository (bukan di subfolder).
2. Push ke repository GitHub:

   ```bash
   cd masjid-crud
   git init
   git add .
   git commit -m "Website masjid dengan CRUD dan database"
   git branch -M main
   git remote add origin https://github.com/<username>/masjid-app.git
   git push -u origin main --force
   ```

   📸 **SS-7** (isi repository di GitHub)

---

## Bagian E — Deploy Otomatis dengan GitHub Actions (Publish Profile)

1. Di Web App, buka **Settings → Configuration → General settings**. Set **SCM Basic Auth Publishing Credentials** ke **On**, lalu klik **Save**. 📸 **SS-8**
2. Kembali ke **Overview**, klik **Download publish profile**. 📸 **SS-9**
3. Di GitHub, buka repo → **Settings → Secrets and variables → Actions → New repository secret**:
   - Name: `AZURE_PUBLISH_PROFILE`
   - Secret: tempel **seluruh isi** file `.PublishSettings` (XML `<publishData>...</publishData>`)

   📸 **SS-10**
4. File workflow sudah tersedia di `.github/workflows/deploy-azure.yml`. **Ubah `app-name`** sesuai nama Web App-mu, lalu commit & push.
5. Buka tab **Actions** di GitHub dan tunggu sampai job **build** dan **deploy** berwarna hijau ✅. 📸 **SS-11**

> **Alternatif tanpa GitHub Actions:** Web App → **Deployment Center** → Source **GitHub** → pilih repo & branch → **Save**.

---

## Bagian F — Pengujian

Buka `https://<nama-webapp>.azurewebsites.net`.

| No | Pengujian | Hasil yang diharapkan | Screenshot |
|---|---|---|---|
| 1 | Buka halaman beranda | Website tampil, jadwal sholat muncul | 📸 SS-12 |
| 2 | Buka `/admin`, login | Masuk ke Dashboard. Kartu **Database** menampilkan **"MySQL (Azure Database for MySQL)"** (bukti terhubung ke database Azure) | 📸 SS-13 |
| 3 | **Create**: Kegiatan → + Tambah Kegiatan → isi → simpan | Muncul pesan "Kegiatan baru berhasil ditambahkan", data tampil di tabel | 📸 SS-14 |
| 4 | **Read**: buka halaman publik **Kegiatan** | Data baru tampil di website | 📸 SS-15 |
| 5 | **Update**: klik **Edit** → ubah judul/tanggal → simpan | Muncul pesan "berhasil diperbarui", data berubah | 📸 SS-16 |
| 6 | **Delete**: klik **Hapus** → konfirmasi OK | Muncul pesan "berhasil dihapus", data hilang dari tabel | 📸 SS-17 |
| 7 | Ulangi CRUD pada menu **Kajian** | Sama seperti di atas | 📸 SS-18 |
| 8 | Isi form dengan field kosong | Muncul pesan validasi, data tidak tersimpan | 📸 SS-19 |

---

## Bagian G — Troubleshooting

| Masalah | Penyebab & Solusi |
|---|---|
| Dashboard menampilkan **"SQLite (mode lokal)"** di Azure | Environment variable database tidak terbaca. Cek kembali Bagian C. |
| "Koneksi database gagal" | Kredensial salah, atau Web App belum terhubung ke VNet. Tambahkan App Setting `APP_DEBUG=1` untuk melihat detail error, lalu hapus lagi setelah selesai. |
| GitHub Actions: `No credentials found` | Nama secret tidak sama dengan yang ada di workflow, atau disimpan sebagai *Variable*, bukan *Secret*. |
| GitHub Actions: `401 Unauthorized` | SCM Basic Auth belum diaktifkan. Aktifkan, lalu unduh ulang publish profile. |
| Halaman 404 / "Hey, PHP developers!" | `index.php` tidak berada di root yang di-deploy. Sesuaikan `package:` di workflow. |

---

## Bagian H — Setelah Dinilai

Resource **Basic** memakan kredit Azure for Students setiap jam. Setelah asisten selesai mengecek URL, hapus semua resource sekaligus dengan menghapus **Resource Group** `rg-masjid-praktikum`.
