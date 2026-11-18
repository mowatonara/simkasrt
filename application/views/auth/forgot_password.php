<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Lupa Password | SIMKAS-RT</title>
  <link rel="icon" type="image/png" href="<?= base_url('assets/images/simkas-rt-logo.png'); ?>">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-100 via-white to-indigo-100">
  <main class="mx-auto flex min-h-screen max-w-lg items-center justify-center p-4">
    <section class="w-full rounded-3xl bg-white p-7 shadow-xl sm:p-10">
      <div class="mb-6 flex items-center gap-3">
        <img src="<?= base_url('assets/images/simkas-rt-logo.png'); ?>" alt="Logo SIMKAS-RT" class="h-16 w-16 rounded-xl border border-slate-100 bg-white object-contain p-1">
        <div>
          <p class="text-lg font-black text-sky-900">SIMKAS-RT</p>
          <p class="text-sm text-slate-500">Pemulihan akun warga</p>
        </div>
      </div>
      <h1 class="text-2xl font-bold text-slate-900">Lupa password?</h1>
      <p class="mt-2 text-sm leading-6 text-slate-600">Masukkan email yang terdaftar pada data warga. Password baru akan dikirim ke email tersebut apabila cocok dengan satu akun warga aktif.</p>

      <?php if ($this->session->flashdata('error')): ?>
        <div class="mt-5 rounded-xl bg-rose-50 p-3 text-sm text-rose-700"><?= html_escape($this->session->flashdata('error')); ?></div>
      <?php endif; ?>
      <?php if ($this->session->flashdata('success')): ?>
        <div class="mt-5 rounded-xl bg-emerald-50 p-3 text-sm text-emerald-800"><?= html_escape($this->session->flashdata('success')); ?></div>
      <?php endif; ?>

      <?= form_open('auth/lupa_password', array('class' => 'mt-6 space-y-4')); ?>
        <label for="email" class="block text-sm font-medium text-slate-800">Email warga
          <input id="email" name="email" type="email" required maxlength="150" autocomplete="email" placeholder="nama@email.com"
                 class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
        </label>
        <button class="w-full rounded-xl bg-sky-700 px-4 py-3 font-semibold text-white shadow-sm hover:bg-sky-800">Kirim Password Baru</button>
      <?= form_close(); ?>

      <p class="mt-6 text-center text-sm"><a href="<?= site_url('login'); ?>" class="font-semibold text-sky-700 hover:text-sky-900 hover:underline">Kembali ke halaman masuk</a></p>
    </section>
  </main>
</body>
</html>
