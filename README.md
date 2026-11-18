# SIMKAS-RT — Aplikasi CI3

Aplikasi web manajemen kas RT berbasis CodeIgniter 3, MySQL, dan Tailwind CSS CDN. Kode framework CI3 3.1.13 sudah disertakan di folder `system/`, sehingga tidak perlu Composer.

## Menjalankan di Laragon

1. Letakkan folder proyek di `C:\laragon\www\SIMKAS-RT`.
2. Jalankan **Apache** dan **MySQL** dari Laragon. Untuk CI3 3.1.13, PHP 8.1 direkomendasikan; PHP 8.2 dapat menampilkan peringatan kompatibilitas dari framework lama, yang disaring pada konfigurasi development.
3. Buat database `simkasrt` di phpMyAdmin atau HeidiSQL, lalu pilih database tersebut dan impor `database/db_kasrt.sql`. File SQL hanya membuat struktur tabel; tidak ada data warga atau akun yang disertakan.
4. Untuk Laragon lokal, ganti isi `application/config/database.php` ke host `127.0.0.1`, user `root`, password kosong, dan database `simkas_rt`. File proyek saat ini sudah memakai konfigurasi database cPanel.
5. Untuk menjalankan lokal, ubah `base_url` di `application/config/config.php` ke alamat Laragon Anda, set `cookie_secure` ke `FALSE`, dan ubah `RewriteBase` di `.htaccess` menjadi `/` (virtual host) atau `/SIMKAS-RT/` (localhost subfolder). Konfigurasi proyek saat ini ditujukan untuk alamat cPanel pada bagian deployment.
6. Akses `http://simkas-rt.test/setup`, lalu buat akun pengurus pertama. Halaman setup tertutup otomatis setelah admin dibuat.
7. Masuk melalui `http://simkas-rt.test/login`. Buka menu **Pengguna** untuk menautkan akun warga ke sepuluh data contoh atau data warga yang Anda tambahkan.

### Menggunakan localhost/SIMKAS-RT

Jika virtual host Laragon tidak dipakai:

- Ubah `base_url` menjadi `http://localhost/SIMKAS-RT/` di `application/config/config.php` dan set `cookie_secure` ke `FALSE`.
- Ubah `RewriteBase /` menjadi `RewriteBase /SIMKAS-RT/` di `.htaccess`.
- Pastikan Apache mengizinkan `.htaccess` dan modul `mod_rewrite` aktif. URL `index.php` dapat digunakan bila rewrite belum diaktifkan.

## Fitur yang tersedia

- Login, logout POST, setup admin pertama, pembatasan peran admin dan warga.
- Akun warga dibuat dari formulir data warga: username login mengikuti nomor HP dan admin menetapkan password awal. Password disimpan sebagai hash.
- Nomor HP pada tabel warga membuka percakapan WhatsApp; nomor lokal berawalan `0` otomatis diarahkan dengan kode negara `62`.
- Dashboard ringkasan saldo dan transaksi.
- CRUD data warga, NIK disamarkan di tabel, status iuran, serta pembuatan tagihan bulanan.
- Pengajuan pembayaran Transfer/QRIS/Tunai dengan bukti JPG, PNG, atau PDF; verifikasi admin menyetujui atau menolak dengan alasan.
- Pemasukan tercatat ke buku kas setelah pembayaran disetujui; admin dapat mencatat pengeluaran, memfilter, dan mencetak buku kas.
- Pengguna admin/warga, notifikasi warga, riwayat pembayaran, buku kas baca-saja, dan pengaturan profil.
- Token CSRF pada formulir, hash password, validasi input server-side, dan penyimpanan bukti pada folder privat.

## Catatan konfigurasi

- Tailwind CSS dimuat dari CDN, sehingga browser perlu koneksi internet.
- Pastikan folder `application/cache/sessions`, `uploads/profil`, dan `private_uploads/bukti` dapat ditulis oleh Apache. Bukti pembayaran dikirim melalui controller terproteksi; jangan memindahkan folder privat ke dalam folder publik.
- SQL hanya menyediakan struktur tabel. Buat akun admin melalui halaman setup setelah impor.
- CI3 merupakan framework legacy. Sebelum dipasang ke server publik, atur environment production, HTTPS, kredensial database non-root, backup database, dan kebijakan akses sesuai lingkungan RT.

## Berkas panduan

- Panduan langkah demi langkah dan potongan kode: `docs/PANDUAN-SIMKAS-RT.md`.
- Skema basis data: `database/db_kasrt.sql`.
- Framework: [CodeIgniter 3.1.13](https://github.com/bcit-ci/CodeIgniter).

## Import ke cPanel

1. Di **cPanel → MySQL Databases**, buat database dan user MySQL, lalu berikan user akses penuh ke database.
2. Buka phpMyAdmin, pilih database yang baru dibuat dari panel kiri, lalu pilih **Import**.
3. Unggah `database/db_kasrt.sql` dan jalankan import. File ini membuat tabel tanpa memasukkan data warga atau akun.
4. Unggah seluruh isi proyek ke folder `public_html/simkasrt/` agar `index.php`, `application/`, `system/`, dan `.htaccess` berada langsung di dalam folder tersebut.
5. `application/config/database.php` sudah diisi dengan nama database dan username cPanel yang diberikan. Pastikan kredensial tersebut sama dengan database yang dipilih di phpMyAdmin; hostname umumnya `localhost`.
6. Pastikan folder `application/cache/sessions`, `application/logs`, `uploads/profil`, dan `private_uploads/bukti` dapat ditulis oleh PHP. Gunakan permission 755 atau 775 sesuai konfigurasi hosting; hindari 777.
7. Aktifkan sertifikat SSL/AutoSSL di cPanel. Aplikasi menggunakan URL `https://rekap.tasikmalayakota.go.id/simkasrt/`, cookie secure, dan `.htaccess` mengarahkan HTTP ke HTTPS.
8. Buka `https://rekap.tasikmalayakota.go.id/simkasrt/setup` untuk membuat admin pertama. Hapus data warga contoh setelah masuk jika aplikasi akan dipakai dengan data RT sebenarnya.

Konfigurasi ini mengasumsikan folder aplikasi berada di `public_html/simkasrt/`. Jika document root atau lokasi unggah berbeda, sesuaikan `RewriteBase` di `.htaccess` dan `base_url` di `application/config/config.php`.

### Jika halaman setup menampilkan 404

- Coba `https://rekap.tasikmalayakota.go.id/simkasrt/index.php/setup`. Jika URL ini terbuka, unggah ulang `.htaccess` dan pastikan `mod_rewrite` aktif.
- Coba `/simkasrt/login`. Jika halaman login muncul, akun admin mungkin sudah ada sehingga setup ditutup; gunakan akun tersebut. Versi terbaru akan mengarahkan `/setup` ke halaman login.
- Jika kedua URL tetap 404, pastikan berkas ada langsung di `public_html/simkasrt/`: `index.php`, `.htaccess`, `application/config/routes.php`, `application/controllers/Setup.php`, dan `application/views/auth/setup.php`. Perhatikan kapitalisasi `Setup.php` pada Linux.
