<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Setup Pengurus | SIMKAS-RT</title>
  <link rel="icon" type="image/png" href="<?= base_url('assets/images/simkas-rt-logo.png'); ?>">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script src="<?= base_url('assets/js/password-toggle.js'); ?>" defer></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-sky-100 via-white to-indigo-100">
  <main class="mx-auto flex min-h-screen max-w-xl items-center p-4">
    <section class="w-full rounded-3xl bg-white p-7 shadow-xl sm:p-10">
      <div class="mb-5 flex items-center gap-4">
        <img src="<?= base_url('assets/images/simkas-rt-logo.png'); ?>" alt="Logo SIMKAS-RT" class="h-20 w-20 shrink-0 rounded-2xl border border-slate-100 bg-white object-contain p-0.5">
        <div>
          <p class="text-sm font-semibold uppercase tracking-wider text-sky-700">Setup awal SIMKAS-RT</p>
          <h1 class="mt-2 text-2xl font-bold">Buat akun pengurus pertama</h1>
        </div>
      </div>
      <p class="mt-2 text-sm text-slate-500">Halaman ini otomatis terkunci setelah akun admin dibuat.</p>
      <?php if ($errors): ?><div class="mt-5 rounded-xl bg-rose-50 p-3 text-sm text-rose-700"><?= html_escape($errors); ?></div><?php endif; ?>
      <?= form_open('setup', array('class' => 'mt-6 space-y-4')); ?>
        <label class="block text-sm font-medium">Nama pengurus
          <input name="nama" value="<?= set_value('nama'); ?>" required maxlength="120" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
        </label>
        <label class="block text-sm font-medium">Username
          <input name="username" value="<?= set_value('username'); ?>" required minlength="4" maxlength="60" autocomplete="username" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
        </label>
        <label class="block text-sm font-medium">Password (minimal 10 karakter)
          <div data-password-field class="relative mt-1">
            <input name="password" data-password-input type="password" required minlength="10" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 pr-12">
            <button hidden data-password-toggle type="button" aria-label="Tampilkan password" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center rounded-r-xl px-3 text-slate-500 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-600">
              <svg data-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg data-eye-off hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.25 0 9.75 7 9.75 7a16 16 0 0 1-3.1 3.9M6.2 6.2C3.7 7.9 2.25 12 2.25 12s3.5 7 9.75 7c1 0 1.95-.2 2.8-.5"/></svg>
            </button>
          </div>
        </label>
        <label class="block text-sm font-medium">Konfirmasi password
          <div data-password-field class="relative mt-1">
            <input name="password_confirm" data-password-input type="password" required minlength="10" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 pr-12">
            <button hidden data-password-toggle type="button" aria-label="Tampilkan password" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center rounded-r-xl px-3 text-slate-500 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-600">
              <svg data-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg data-eye-off hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.25 0 9.75 7 9.75 7a16 16 0 0 1-3.1 3.9M6.2 6.2C3.7 7.9 2.25 12 2.25 12s3.5 7 9.75 7c1 0 1.95-.2 2.8-.5"/></svg>
            </button>
          </div>
        </label>
        <button class="w-full rounded-xl bg-sky-700 px-4 py-3 font-semibold text-white hover:bg-sky-800">Buat Akun Admin</button>
      <?= form_close(); ?>
    </section>
  </main>
</body>
</html>
