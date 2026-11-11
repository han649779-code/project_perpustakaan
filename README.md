# Selfwise Library

Aplikasi web **Mini Digital Library** untuk mengelola koleksi buku, dibangun dengan **PHP Native (tanpa framework)** dan **MySQL (mysqli)**. Dibuat sebagai tugas terstruktur mata kuliah **Pemrograman Web**, Program Studi Sistem Informasi.

| | |
|---|---|
| **Nama** | Rehan Ramadhani |
| **NPM** | 2513020049 |

### Link Demo YouTube

<!-- Tempel link video demo di bawah ini -->


---

## Fitur

**Autentikasi & hak akses**
- Login dengan form POST (username dan password), password diverifikasi memakai `password_verify()`.
- Session: dashboard dan semua halaman kelola buku hanya bisa dibuka setelah login.
- Cookie "Ingat username saya": menyimpan username di browser selama 30 hari (password tidak disimpan).
- Logout menghapus session dan cookie, lalu kembali ke halaman login.

**CRUD data buku**
- Create: judul, kategori, penulis, tahun terbit, stok, dan sampul.
- Read: tabel HTML dengan pencarian judul/penulis dan filter kategori.
- Update: ubah data, sampul lama diganti hanya jika file baru diunggah.
- Delete: hapus baris database sekaligus file sampul di folder `uploads/`.

**Logika bisnis & validasi**
- Status ketersediaan memakai percabangan if-else: stok lebih dari 0 tampil **Tersedia**, stok 0 tampil **Dipinjam**.
- Upload sampul: hanya `.jpg` / `.png`, maksimal **2 MB**, isi file dicek sebagai gambar asli, nama file diacak.
- Validasi input di sisi server (judul, kategori, penulis, tahun, stok).

**Keamanan tambahan**
- Prepared statement di semua query, output di-escape dengan `htmlspecialchars`.
- Token CSRF di semua form POST (termasuk hapus).
- `session_regenerate_id()` saat login.

## Teknologi

PHP 7.4+ (disarankan PHP 8.x), MySQL/MariaDB, Bootstrap 5.3, Bootstrap Icons, JavaScript (preview dan validasi file di sisi klien).

## Struktur Project

```
project_perpustakaan/
├── config/
│   ├── koneksi.php        # Koneksi database MySQL + konstanta aplikasi
│   └── helper.php         # Session, CSRF, flash, validasi, upload file
├── includes/
│   ├── header.php         # Head HTML + navbar + pesan flash
│   ├── footer.php         # Script Bootstrap
│   └── form_buku.php      # Form bersama tambah/edit
├── assets/
│   ├── css/style.css
│   └── form_buku.js       # Preview & validasi file sampul
├── uploads/               # Folder penampung file sampul
├── docs/                  # Screenshot untuk README
├── index.php              # Halaman Login
├── proses_login.php       # Verifikasi autentikasi, Session & Cookie
├── logout.php             # Hapus Session & Cookie
├── dashboard.php          # Tampil data (Read) & akses menu
├── tambah_buku.php        # Form tambah data
├── proses_tambah.php      # Query INSERT & upload file
├── edit_buku.php          # Form edit data
├── proses_edit.php        # Query UPDATE & penggantian file
├── hapus_buku.php         # Query DELETE & unlink file gambar
└── db_perpustakaan.sql    # Ekspor database
```

## Cara Menjalankan

1. Salin folder project ke `htdocs` (XAMPP) atau `www` (Laragon), misalnya `htdocs/project_perpustakaan`.
2. Nyalakan Apache dan MySQL.
3. Buka phpMyAdmin, lalu **Import** file `db_perpustakaan.sql` (database `db_perpustakaan` dibuat otomatis).
4. Jika perlu, ubah `DB_USER` dan `DB_PASS` di `config/koneksi.php`.
5. Pastikan folder `uploads/` bisa ditulis oleh server.
6. Buka `http://localhost/project_perpustakaan/`.

**Akun awal:** username `admin`, password `admin123`

## Struktur Tabel Database

### Tabel `users`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT UNSIGNED, AUTO_INCREMENT | Primary key |
| username | VARCHAR(50) | Unik |
| password | VARCHAR(255) | Hash bcrypt |
| nama_lengkap | VARCHAR(100) | Nama yang tampil di navbar |
| created_at | TIMESTAMP | Default waktu saat ini |

### Tabel `buku`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | INT UNSIGNED, AUTO_INCREMENT | Primary key |
| judul | VARCHAR(200) | Judul buku |
| kategori | VARCHAR(50) | Diindeks untuk filter |
| penulis | VARCHAR(120) | Nama penulis |
| tahun_terbit | SMALLINT UNSIGNED | Tahun terbit |
| stok | INT UNSIGNED | Menentukan label Tersedia / Dipinjam |
| sampul | VARCHAR(255), NULL | Nama file di folder `uploads/` |
| created_at | TIMESTAMP | Waktu dibuat |
| updated_at | TIMESTAMP | Diperbarui otomatis |

## Screenshot

> Ambil screenshot setiap halaman setelah aplikasi berjalan, simpan di folder `docs/` dengan nama berikut, lalu gambar akan tampil otomatis.

### Halaman Login
![Login](docs/01-login.png)

### Dashboard (Read)
![Dashboard](docs/02-dashboard.png)

### Tambah Buku (Create + Upload Sampul)
![Tambah Buku](docs/03-tambah-buku.png)

### Edit Buku (Update)
![Edit Buku](docs/04-edit-buku.png)

### Konfirmasi Hapus (Delete)
![Hapus Buku](docs/05-hapus-buku.png)

## Pengumpulan

- Repositori GitHub: _isi link repositori_
- Video demo YouTube: lihat bagian **Link Demo YouTube** di atas
