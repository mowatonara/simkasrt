<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Masuk | SIMKAS-RT</title>
  <link rel="icon" type="image/png" href="<?= base_url('assets/images/simkas-rt-logo.png'); ?>">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script src="<?= base_url('assets/js/password-toggle.js'); ?>" defer></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-100 via-white to-indigo-100">
  <main class="mx-auto flex min-h-screen max-w-6xl items-center justify-center p-4">
    <section class="grid w-full overflow-hidden rounded-3xl bg-white shadow-xl md:grid-cols-2">
        <div class="hidden flex-col justify-between bg-sky-800 p-10 text-white md:flex">
          <div class="flex items-center gap-4">
            <img src="<?= base_url('assets/images/simkas-rt-logo.png'); ?>" alt="Logo SIMKAS-RT" class="h-24 w-24 rounded-2xl bg-white object-contain p-1 shadow-sm">
            <div>
              <p class="text-2xl font-black">SIMKAS-RT</p>
              <p class="mt-1 text-xs font-medium text-sky-200">Sistem Manajemen Kas RT</p>
            </div>
          </div>
        <div>
          <p class="mb-3 text-sm font-semibold uppercase tracking-widest text-sky-200">Kas RT lebih tertata</p>
          <h1 class="text-4xl font-bold leading-tight">Transparan untuk pengurus.<br>Jelas untuk warga.</h1>
          <p class="mt-5 max-w-sm text-sky-100">Catat iuran, ajukan pembayaran, dan pantau arus kas lingkungan dalam satu tempat.</p>
        </div>
        <p class="text-sm text-sky-200">Sistem Informasi Manajemen Kas Rukun Tetangga</p>
      </div>
      <div class="p-7 sm:p-10">
        <div class="mb-8 flex items-center gap-3 md:hidden">
          <img src="<?= base_url('assets/images/simkas-rt-logo.png'); ?>" alt="Logo SIMKAS-RT" class="h-16 w-16 shrink-0 rounded-xl border border-slate-100 bg-white object-contain p-0.5">
          <div>
            <p class="text-xl font-black text-sky-900">SIMKAS-RT</p>
            <p class="mt-1 text-sm text-slate-500">Sistem Manajemen Kas Rukun Tetangga</p>
          </div>
        </div>
        <h2 class="text-2xl font-bold text-slate-900">Selamat datang</h2>
        <p class="mt-2 text-sm text-slate-500">Masuk menggunakan akun pengurus atau warga.</p>
        <?php if ($this->session->flashdata('error')): ?>
          <div class="mt-5 rounded-xl bg-rose-50 p-3 text-sm text-rose-700"><?= html_escape($this->session->flashdata('error')); ?></div>
        <?php endif; ?>
        <?= form_open('login', array('class' => 'mt-7 space-y-5')); ?>
          <div>
            <label for="username" class="mb-1.5 block text-sm font-medium">Username admin / nomor HP warga</label>
            <input id="username" name="username" value="<?= set_value('username'); ?>" required autocomplete="username" placeholder="Username atau nomor HP"
                   class="w-full rounded-xl border border-slate-300 px-4 py-3 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
          </div>
          <div>
            <label for="password" class="mb-1.5 block text-sm font-medium">Password</label>
            <div data-password-field class="relative">
              <input id="password" name="password" data-password-input type="password" required autocomplete="current-password"
                     class="w-full rounded-xl border border-slate-300 px-4 py-3 pr-12 outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100">
              <button hidden data-password-toggle type="button" aria-label="Tampilkan password" aria-pressed="false"
                      class="absolute inset-y-0 right-0 flex items-center rounded-r-xl px-3 text-slate-500 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-600">
                <svg data-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg data-eye-off hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.25 0 9.75 7 9.75 7a16 16 0 0 1-3.1 3.9M6.2 6.2C3.7 7.9 2.25 12 2.25 12s3.5 7 9.75 7c1 0 1.95-.2 2.8-.5"/></svg>
              </button>
            </div>
          </div>
          <button class="w-full rounded-xl bg-sky-700 px-4 py-3 font-semibold text-white shadow-sm hover:bg-sky-800">Masuk</button>
        <?= form_close(); ?>
        <?php
          $resetMessage = "Halo, Admin\n" .
            "Saya Warga RT 01 MMTR lupa password Aplikasi SIMKAS-RT,\n" .
            "Atas Nama : (Nama Warga)\n" .
            "Mohon bantu reset password\n\n" .
            "Terima Kasih";
        ?>
        <p class="mt-5 text-center text-sm"><a href="https://wa.me/6281278727025?text=<?= rawurlencode($resetMessage); ?>" target="_blank" rel="noopener noreferrer" class="font-semibold text-sky-700 hover:text-sky-900 hover:underline">Lupa password?</a></p>
        <p class="mt-6 text-center text-xs text-slate-400">Hubungi pengurus RT jika belum memiliki akun.</p>
      </div>
    </section>
  </main>
</body>
</html>
