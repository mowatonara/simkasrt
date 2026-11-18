# Panduan membangun SIMKAS-RT dengan CodeIgniter 3

Panduan ini mengikuti rancangan SIMKAS-RT: peran admin/pengurus dan warga, dashboard kas, data warga, tagihan, verifikasi pembayaran, buku kas, notifikasi, dan tampilan responsif biru pastel. Skema beserta 10 warga fiktif tersedia di **database/simkas_rt.sql**.

Implementasi aplikasi CI3 yang bisa dijalankan sudah tersedia di root proyek. Untuk langkah pemasangan cepat di Laragon, mulai dari **README.md**; panduan ini menjelaskan susunan fitur dan pola kode.

## 1. Siapkan proyek di Laragon

1. Jalankan Apache dan MySQL dari Laragon.
2. Unduh rilis CI3 dari situs resmi dan ekstrak ke **C:\laragon\www\SIMKAS-RT**. Pastikan **index.php**, **application/**, dan **system/** berada di root proyek.
3. Buka folder tersebut di VS Code.
4. Buka **http://simkas-rt.test** jika virtual host Laragon aktif, atau **http://localhost/SIMKAS-RT**.

CI3 merupakan lini legacy yang berada dalam mode pemeliharaan. Tetap gunakan CI3 sesuai stack yang diminta, tetapi pilih versi PHP Laragon yang kompatibel dengan rilis CI3 dan jangan mengasumsikan PHP terbaru akan cocok tanpa pemeriksaan. Rujuk [repositori resmi CI3](https://github.com/bcit-ci/CodeIgniter) dan [catatan rilis](https://codeigniter.com/userguide3/changelog.html).

## 2. Buat database

Di phpMyAdmin, import **database/simkas_rt.sql**. File ini membuat tabel **users**, **data_warga**, **jenis_iuran**, **tagihan**, **pembayaran**, **transaksi_kas**, dan **notifikasi**. Sepuluh warga yang dimasukkan adalah data dummy.

Buat admin pertama melalui **/setup** setelah mengimpor SQL. Alur ini membuat hash dengan **password_hash($password, PASSWORD_DEFAULT)** dan tertutup otomatis setelah admin tersedia. Setelah masuk, buat akun warga melalui menu **Pengguna**; satu akun warga menunjuk satu baris **data_warga**.

Akun warga dapat ditautkan ke baris warga dengan pola ini; buat hash warga terpisah menggunakan perintah yang sama:

~~~sql
INSERT INTO users (data_warga_id, username, password_hash, nama, role, is_active)
SELECT id, 'warga01', 'TEMPEL_HASH_WARGA', nama, 'warga', 1
FROM data_warga WHERE nik = '9000000000000001';
~~~

## 3. Konfigurasi CI3

Di **application/config/config.php**, sesuaikan alamat aplikasi, buat encryption key acak, dan gunakan zona waktu Indonesia.

~~~php
$config['base_url'] = 'http://simkas-rt.test/';
$config['index_page'] = '';
$config['encryption_key'] = 'GANTI_DENGAN_KUNCI_ACAK_MINIMAL_32_KARAKTER';
$config['csrf_protection'] = TRUE;
$config['csrf_regenerate'] = TRUE;
$config['cookie_secure'] = FALSE; // ubah TRUE setelah memakai HTTPS
date_default_timezone_set('Asia/Jakarta');
~~~

Jika memakai **localhost/SIMKAS-RT**, masukkan segmen itu ke **base_url**. Untuk menghilangkan **index.php** dari URL, aktifkan **mod_rewrite** dan buat **.htaccess** di root. Sesuaikan **RewriteBase** dengan alamat lokal.

~~~apache
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [L]
~~~

Atur koneksi di **application/config/database.php**:

~~~php
$db['default'] = array(
    'dsn'          => '',
    'hostname'     => '127.0.0.1',
    'username'     => 'root',
    'password'     => '', // isi sesuai konfigurasi MySQL Laragon
    'database'     => 'simkas_rt',
    'dbdriver'     => 'mysqli',
    'dbprefix'     => '',
    'pconnect'     => FALSE,
    'db_debug'     => (ENVIRONMENT !== 'production'),
    'cache_on'     => FALSE,
    'cachedir'     => '',
    'char_set'     => 'utf8mb4',
    'dbcollat'     => 'utf8mb4_unicode_ci',
    'swap_pre'     => '',
    'encrypt'      => FALSE,
    'compress'     => FALSE,
    'stricton'     => FALSE,
    'failover'     => array(),
    'save_queries' => TRUE
);
~~~

Autoload kebutuhan umum di **application/config/autoload.php**:

~~~php
$autoload['libraries'] = array('database', 'session', 'form_validation');
$autoload['helper'] = array('url', 'form', 'security', 'simkas');
~~~

Pastikan lokasi simpan session dapat ditulis PHP. Simpan bukti pembayaran pada lokasi privat, batasi tipe dan ukuran file, lalu sajikan file melalui controller yang memeriksa hak akses.

## 4. Susun direktori dan rute

~~~text
application/
  controllers/Auth.php
  controllers/admin/Dashboard.php
  controllers/admin/Data_warga.php
  controllers/admin/Pembayaran.php
  controllers/admin/Iuran.php
  controllers/admin/Buku_kas.php
  controllers/admin/Users.php
  controllers/warga/Dashboard.php
  controllers/warga/History_pembayaran.php
  controllers/warga/Pembayaran.php
  controllers/warga/Buku_kas.php
  core/MY_Controller.php
  helpers/simkas_helper.php
  models/User_model.php
  models/Tagihan_model.php
  models/Pembayaran_model.php
  models/Transaksi_kas_model.php
  views/layouts/
private_uploads/bukti/
database/simkas_rt.sql
~~~

Contoh **application/config/routes.php**:

~~~php
$route['default_controller']       = 'auth/login';
$route['login']                    = 'auth/login';
$route['logout']                   = 'auth/logout';
$route['admin/dashboard']          = 'admin/dashboard/index';
$route['admin/data-warga']         = 'admin/data_warga/index';
$route['admin/pembayaran']         = 'admin/pembayaran/index';
$route['admin/iuran-tagihan']      = 'admin/iuran/index';
$route['admin/buku-kas']           = 'admin/buku_kas/index';
$route['admin/users']              = 'admin/users/index';
$route['warga/dashboard']          = 'warga/dashboard/index';
$route['warga/history-pembayaran'] = 'warga/history_pembayaran/index';
$route['warga/pembayaran']         = 'warga/pembayaran/index';
$route['warga/buku-kas']           = 'warga/buku_kas/index';
~~~

CI3 membaca rute dari **application/config/routes.php**. Rute membantu membuat URL rapi, tetapi pemeriksaan akses tetap harus dilakukan controller. [Dokumentasi routing CI3](https://codeigniter.com/userguide3/general/routing.html).

## 5. Buat autentikasi dan pembatasan role

Simpan data minimum ke session setelah password cocok. Jangan menerima **data_warga_id** dari form warga; ambil pemilik data dari session.

~~~php
// application/models/User_model.php
class User_model extends CI_Model
{
    public function by_username($username)
    {
        return $this->db->where('username', $username)
            ->limit(1)->get('users')->row();
    }
}
~~~

~~~php
// Bagian inti Auth::login()
$username = trim((string) $this->input->post('username', TRUE));
$password = (string) $this->input->post('password'); // jangan XSS-filter password
$user = $this->User_model->by_username($username);

if (!$user || (int) $user->is_active !== 1 ||
    !password_verify($password, $user->password_hash)) {
    $this->session->set_flashdata('error', 'Username atau password salah.');
    redirect('login');
}

$this->session->sess_regenerate(TRUE);
$this->session->set_userdata('user', array(
    'id'            => (int) $user->id,
    'nama'          => $user->nama,
    'role'          => $user->role,
    'data_warga_id' => $user->data_warga_id
));
redirect($user->role === 'admin' ? 'admin/dashboard' : 'warga/dashboard');
~~~

Gunakan controller dasar untuk mengecek session dan role pada setiap halaman privat:

~~~php
// application/core/MY_Controller.php
class MY_Controller extends CI_Controller
{
    protected function wajib_role($roles)
    {
        $user = $this->session->userdata('user');
        if (!$user) {
            redirect('login');
            exit;
        }
        if (!in_array($user['role'], (array) $roles, TRUE)) {
            show_error('Akses ditolak.', 403);
        }
    }
}

class Admin_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->wajib_role('admin');
    }
}

class Warga_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->wajib_role('warga');
    }
}
~~~

Controller admin mewarisi **Admin_Controller** dan controller warga mewarisi **Warga_Controller**. Aktifkan CSRF, validasi input di server, dan escape saat output. **form_open()** dapat menyisipkan token CSRF. [Keamanan CI3](https://codeigniter.com/userguide3/general/security.html).

## 6. Bangun layout Tailwind dan dashboard

Buat partial header, sidebar, konten, dan notifikasi. Menu sidebar berbeda berdasarkan role; pada layar kecil jadikan sidebar drawer dan gunakan **overflow-x-auto** untuk tabel.

Sebelum memuat layout, kirimkan role dari controller: **$data['role'] = $this->session->userdata('user')['role'];**

~~~php
<!-- application/views/layouts/header.php -->
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape($judul); ?> - SIMKAS-RT</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
<div class="min-h-screen md:flex">
  <aside id="sidebar" class="w-64 shrink-0 overflow-hidden bg-sky-100 p-5 transition-all">
    <div class="mb-6 flex items-center justify-between">
      <strong class="menu-label text-xl text-sky-950">SIMKAS-RT</strong>
      <button id="toggle-sidebar" class="rounded-lg px-3 py-2 hover:bg-sky-200"
              aria-label="Sembunyikan menu">☰</button>
    </div>
    <nav class="space-y-1">
      <?php if ($role === 'admin'): ?>
        <a class="flex gap-3 rounded-lg px-3 py-2 hover:bg-white" href="<?= site_url('admin/dashboard'); ?>"><span>▦</span><span class="menu-label">Dashboard</span></a>
        <a class="flex gap-3 rounded-lg px-3 py-2 hover:bg-white" href="<?= site_url('admin/data-warga'); ?>"><span>♙</span><span class="menu-label">Data Warga</span></a>
        <a class="flex gap-3 rounded-lg px-3 py-2 hover:bg-white" href="<?= site_url('admin/pembayaran'); ?>"><span>⇄</span><span class="menu-label">Pembayaran</span></a>
        <a class="flex gap-3 rounded-lg px-3 py-2 hover:bg-white" href="<?= site_url('admin/buku-kas'); ?>"><span>▤</span><span class="menu-label">Buku Kas</span></a>
      <?php else: ?>
        <a class="flex gap-3 rounded-lg px-3 py-2 hover:bg-white" href="<?= site_url('warga/dashboard'); ?>"><span>▦</span><span class="menu-label">Dashboard</span></a>
        <a class="flex gap-3 rounded-lg px-3 py-2 hover:bg-white" href="<?= site_url('warga/history-pembayaran'); ?>"><span>◷</span><span class="menu-label">History Pembayaran</span></a>
        <a class="flex gap-3 rounded-lg px-3 py-2 hover:bg-white" href="<?= site_url('warga/pembayaran'); ?>"><span>⇄</span><span class="menu-label">Halaman Pembayaran</span></a>
        <a class="flex gap-3 rounded-lg px-3 py-2 hover:bg-white" href="<?= site_url('warga/buku-kas'); ?>"><span>▤</span><span class="menu-label">Buku Kas</span></a>
      <?php endif; ?>
    </nav>
  </aside>
  <main class="min-w-0 flex-1 p-4 md:p-8">
    <!-- Header: nama pengguna, lonceng, dan area konten. -->
<script>
document.getElementById('toggle-sidebar').addEventListener('click', function () {
  const sidebar = document.getElementById('sidebar');
  sidebar.classList.toggle('w-64');
  sidebar.classList.toggle('w-20');
  sidebar.querySelectorAll('.menu-label').forEach(function (label) {
    label.classList.toggle('hidden');
  });
});
</script>
~~~

~~~php
<!-- Isi dashboard -->
<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
  <article class="rounded-2xl bg-white p-5 shadow-sm">
    <p class="text-sm text-slate-500">Saldo Kas</p>
    <p class="mt-2 text-2xl font-bold text-sky-900">
      Rp <?= number_format($saldo, 0, ',', '.'); ?>
    </p>
  </article>
  <article class="rounded-2xl bg-sky-100 p-5">
    <p class="text-sm text-slate-600">Pemasukan Bulan Ini</p>
    <p class="mt-2 text-2xl font-bold">Rp <?= number_format($pemasukan_bulan_ini, 0, ',', '.'); ?></p>
  </article>
  <article class="rounded-2xl bg-rose-100 p-5">
    <p class="text-sm text-slate-600">Pengeluaran Bulan Ini</p>
    <p class="mt-2 text-2xl font-bold">Rp <?= number_format($pengeluaran_bulan_ini, 0, ',', '.'); ?></p>
  </article>
  <article class="rounded-2xl bg-indigo-100 p-5">
    <p class="text-sm text-slate-600">Warga Terdaftar</p>
    <p class="mt-2 text-2xl font-bold"><?= (int) $jumlah_warga; ?></p>
  </article>
</section>
<section class="mt-6 rounded-2xl bg-white p-5 shadow-sm">
  <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <h2 class="font-semibold">Ringkasan Keuangan</h2>
    <form method="get">
      <label for="tahun" class="mr-2 text-sm">Tahun</label>
      <select id="tahun" name="tahun" onchange="this.form.submit()"
              class="rounded-lg border border-slate-300 p-2">
        <!-- Opsi tahun dari controller -->
      </select>
    </form>
  </div>
  <canvas id="grafik-kas"></canvas>
</section>
~~~

Controller mengirim 12 nilai pemasukan dan pengeluaran, termasuk nol untuk bulan tanpa transaksi. Pakai parameter binding untuk filter tahun:

~~~php
$sql = "SELECT MONTH(tanggal) AS bulan,
        SUM(CASE WHEN arah = 'masuk' THEN jumlah ELSE 0 END) AS pemasukan,
        SUM(CASE WHEN arah = 'keluar' THEN jumlah ELSE 0 END) AS pengeluaran
        FROM transaksi_kas
        WHERE tanggal >= ? AND tanggal < ?
        GROUP BY MONTH(tanggal)";
$rows = $this->db->query(
    $sql,
    array($tahun . '-01-01', ($tahun + 1) . '-01-01')
)->result();
~~~

Untuk prototipe, Tailwind Play CDN adalah opsi paling mudah. Dokumentasi Tailwind menyatakan Play CDN untuk pengembangan, bukan produksi. Grafik dapat menggunakan Chart.js. [Tailwind Play CDN](https://tailwindcss.com/docs/installation/play-cdn) · [Chart.js CDN](https://www.chartjs.org/docs/latest/getting-started/installation.html).

~~~html
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labelBulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const dataMasuk = <?= json_encode($grafik_masuk, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
const dataKeluar = <?= json_encode($grafik_keluar, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;
new Chart(document.getElementById('grafik-kas'), {
  type: 'bar',
  data: {labels: labelBulan, datasets: [
    {label: 'Pemasukan', data: dataMasuk, backgroundColor: '#86d39a'},
    {label: 'Pengeluaran', data: dataKeluar, backgroundColor: '#e27c82'}
  ]},
  options: {responsive: true, scales: {y: {beginAtZero: true}}}
});
</script>
~~~

JavaScript toggle pada contoh layout mengecilkan sidebar dan menyembunyikan label sambil membiarkan tombol serta ikon tetap terlihat.

## 7. Data warga, pencarian, dan tagihan

Bangun CRUD warga dengan validasi NIK 16 digit, email, HP, jenis kelamin, blok, dan nomor rumah. Default pagination 10 data; sediakan pilihan 20, 50, dan 100 serta pencarian nama/NIK. Perubahan dan penghapusan harus memakai POST dengan CSRF, bukan tautan GET.

Masking NIK hanya mengubah tampilan:

~~~php
// application/helpers/simkas_helper.php
function mask_nik($nik)
{
    $nik = (string) $nik;
    if (!preg_match('/^\d{16}$/', $nik)) {
        return '****************';
    }
    return substr($nik, 0, 6) . '******' . substr($nik, -4);
}
~~~

Pada tabel admin tampilkan **mask_nik($warga->nik)**. Jangan masukkan NIK lengkap ke atribut HTML atau payload yang tidak dibutuhkan. Hitung status per warga dan periode dari **tagihan**: lunas, menunggu verifikasi, atau belum bayar/menunggak. Modal status menampilkan bulan yang tertunggak. Tombol buat tagihan membuat tagihan yang belum ada dan notifikasi ke akun warga.

Kombinasi warga + jenis iuran + periode harus unik seperti pada SQL. Ini mencegah tagihan bulanan duplikat saat tombol ditekan berulang.

## 8. Pembayaran dan verifikasi admin

Riwayat pembayaran hanya menampilkan data milik akun warga yang sedang login. Jika tagihan lunas, tampilkan dialog “Anda sudah lunas!”. Jika belum, minta tanggal, metode Transfer/QRIS/Tunai, nominal, jenis, dan bukti. Tentukan pemilik dari session dan nominal/jenis dari database, bukan nilai nominal yang dikirim browser.

Contoh validasi upload CI3:

~~~php
$this->load->library('upload');
$this->upload->initialize(array(
    'upload_path'   => APPPATH . '../private_uploads/bukti/',
    'allowed_types' => 'jpg|jpeg|png|pdf',
    'max_size'      => 2048,
    'encrypt_name'  => TRUE,
    'detect_mime'   => TRUE,
    'mod_mime_fix'  => TRUE
), TRUE);

if (!$this->upload->do_upload('bukti')) {
    $data['error_upload'] = $this->upload->display_errors('', '');
    // Tampilkan ulang form dan escape teks error.
} else {
    $nama_file = $this->upload->data('file_name');
    // Simpan pembayaran dengan status "menunggu".
}
~~~

Form upload harus memakai **multipart/form-data**. Simpan nama file acak, batasi ekstensi dan ukuran, serta lindungi direktori agar file tidak bisa dibuka tanpa otorisasi. Jika folder privat berada di root proyek, buat **private_uploads/.htaccess**:

~~~apache
Require all denied
~~~

Controller yang sudah memeriksa pemilik/role tetap dapat membaca file dari disk dan mengirimkannya ke browser. [Dokumentasi upload CI3](https://codeigniter.com/userguide3/libraries/file_uploading.html).

Admin hanya memproses pembayaran berstatus menunggu. Verifikasi harus dalam satu transaksi database:

1. Kunci baris pembayaran dan pastikan status masih menunggu.
2. Jika disetujui, ubah pembayaran menjadi terverifikasi, tagihan menjadi lunas, lalu buat satu pemasukan di buku kas.
3. Jika ditolak, simpan alasan dan kembalikan tagihan ke belum bayar.
4. Commit semua perubahan bersama-sama; rollback jika satu langkah gagal.

Kolom **transaksi_kas.pembayaran_id** dibuat unik supaya satu pembayaran tidak menjadi pemasukan ganda.

~~~php
$this->db->trans_begin();
$pembayaran = $this->db->query(
    'SELECT * FROM pembayaran WHERE id = ? FOR UPDATE',
    array($id)
)->row();

// Periksa status/otorisasi; update pembayaran dan tagihan.
// Jika disetujui, insert satu transaksi kas pemasukan.
// Jika ditolak, simpan alasan penolakan.

if ($this->db->trans_status() === FALSE) {
    $this->db->trans_rollback();
} else {
    $this->db->trans_commit();
}
~~~

## 9. Buku kas, notifikasi, dan pengaturan profil

- Pemasukan muncul hanya setelah pembayaran disetujui, bukan ketika warga baru mengirim bukti.
- Pengeluaran dibuat admin melalui form tanggal, kategori, deskripsi, dan nominal positif.
- Buku kas difilter tahun/bulan dan bisa dicetak. Warga hanya memperoleh akses baca.
- Notifikasi tagihan ditautkan ke **users.id** pemilik, memiliki waktu baca, dan hanya terlihat oleh warga tersebut. Klik notifikasi menuju halaman pembayaran.
- Pengaturan profil memperbarui foto dan nama; profil warga juga dapat memperbarui NIK, HP, email, jenis kelamin, blok, nomor rumah. Perubahan password meminta password lama dan menyimpan hash baru.

## 10. Pemetaan menu dari rancangan

| Admin/pengurus | Warga |
|---|---|
| Dashboard kas + filter tahun | Dashboard ringkasan |
| Data warga, status, dan tagihan | Riwayat pembayaran pribadi |
| Verifikasi/penolakan pembayaran | Form pembayaran dan bukti |
| Pengelolaan iuran | Buku kas baca saja |
| Buku kas, cetak, input pengeluaran | Notifikasi tagihan |
| Akun pengurus dan pengaturan profil | Pengaturan profil dan password |

Setelah fitur utama berjalan, pindahkan Tailwind dari Play CDN ke build CSS untuk produksi, atur environment produksi, gunakan HTTPS, batasi akses bukti pembayaran, dan buat backup database berkala.

## Referensi resmi

- [Dokumentasi CodeIgniter 3](https://codeigniter.com/userguide3/)
- [Konfigurasi database CI3](https://codeigniter.com/userguide3/database/configuration.html)
- [Keamanan dan CSRF CI3](https://codeigniter.com/userguide3/general/security.html)
- [Tailwind Play CDN](https://tailwindcss.com/docs/installation/play-cdn)
- [Chart.js](https://www.chartjs.org/docs/latest/getting-started/)
