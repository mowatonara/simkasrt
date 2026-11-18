# simkasrt

Aplikasi web manajemen kas RT berbasis CodeIgniter 3, MySQL, dan Tailwind CSS CDN. Kode framework CI3 3.1.13 sudah disertakan di folder `system/`, sehingga tidak perlu Composer.

## Menjalankan di XAMPP

1. Letakkan folder proyek di `C:\xampp\htdocs`.
2. Jalankan **Apache** dan **MySQL** dari XAMPP. Untuk CI3 3.1.13, PHP 8.1 direkomendasikan; PHP 8.2 dapat menampilkan peringatan kompatibilitas dari framework lama, yang disaring pada konfigurasi development.
3. Buka phpMyAdmin atau HeidiSQL, lalu impor `database/db_kasrt.sql`. SQL ini membuat database `db_kasrt`, seluruh tabel aplikasi.
4. Untuk XAMPP lokal, ganti isi `application/config/database.php` ke host `127.0.0.1`, user `root`, password kosong, dan database `db_kasrt`. File proyek saat ini sudah memakai konfigurasi database cPanel.
5. Untuk menjalankan lokal, ubah `base_url` di `application/config/config.php` ke alamat XAMPP Anda, set `cookie_secure` ke `FALSE`, dan ubah `RewriteBase` di `.htaccess` menjadi `/` (virtual host) atau `/simkasrt/` (localhost subfolder). 
Konfigurasi proyek saat ini ditujukan untuk alamat cPanel pada bagian deployment.

### Menggunakan localhost/simkasrt

Jika virtual host XAMPP tidak dipakai:

- Ubah `base_url` menjadi `http://localhost/simkasrt/` di `application/config/config.php` dan set `cookie_secure` ke `FALSE`.
- Ubah `RewriteBase /` menjadi `RewriteBase /simkasrt/` di `.htaccess`.
- Pastikan Apache mengizinkan `.htaccess` dan modul `mod_rewrite` aktif. URL `index.php` dapat digunakan bila rewrite belum diaktifkan.

## Catatan konfigurasi

- Tailwind CSS dimuat dari CDN, sehingga browser perlu koneksi internet.
- Pastikan folder `application/cache/sessions`, `uploads/profil`, dan `private_uploads/bukti` dapat ditulis oleh Apache. Bukti pembayaran dikirim melalui controller terproteksi; jangan memindahkan folder privat ke dalam folder publik.
- CI3 merupakan framework legacy. Sebelum dipasang ke server publik, atur environment production, HTTPS, kredensial database non-root, backup database, dan kebijakan akses.

## Import Database

- Skema dan data awal: `database/db_kasrt.sql`.
- Skema import phpMyAdmin cPanel: `database/db_kasrt.sql` (tanpa perintah `CREATE DATABASE` dan `USE`).


