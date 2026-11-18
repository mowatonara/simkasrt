<div class="mb-6">
  <h2 class="text-2xl font-bold">Pengaturan Profil</h2>
  <p class="mt-1 text-sm text-slate-500">Perbarui data akun dan keamanan password Anda.</p>
</div>
<section class="max-w-3xl rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-7">
  <?= form_open_multipart($role === 'admin' ? 'admin/setting' : 'warga/setting', array('class' => 'space-y-5')); ?>
    <div class="flex items-center gap-4">
      <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl bg-sky-100 text-2xl font-bold text-sky-800">
        <?php if ($account->foto_profil): ?><img src="<?= base_url('uploads/profil/' . rawurlencode($account->foto_profil)); ?>" alt="Foto profil" class="h-full w-full object-cover">
        <?php else: ?><?= html_escape(substr($account->nama, 0, 1)); ?><?php endif; ?>
      </div>
      <label class="text-sm font-medium">Foto profil
        <input type="file" name="foto" accept=".jpg,.jpeg,.png" class="mt-1 block max-w-full text-sm">
        <span class="text-xs font-normal text-slate-500">JPG/PNG, maksimal 2 MB.</span>
      </label>
    </div>
    <label class="block text-sm font-medium">Nama
      <input name="nama" required maxlength="120" value="<?= html_escape($person ? $person->nama : $account->nama); ?>" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
    </label>
    <?php if ($person): ?>
      <div class="grid gap-4 sm:grid-cols-2">
        <label class="text-sm font-medium">NIK
          <input name="nik" required pattern="[0-9]{16}" maxlength="16" value="<?= html_escape($person->nik); ?>" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
        </label>
        <label class="text-sm font-medium">Nomor HP
          <input name="no_hp" required maxlength="25" value="<?= html_escape($person->no_hp); ?>" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
          <span class="mt-1 block text-xs font-normal text-slate-500">Jika nomor HP diubah, username login warga ikut berubah.</span>
        </label>
        <label class="text-sm font-medium">Email
          <input name="email" type="email" maxlength="150" value="<?= html_escape($person->email); ?>" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
        </label>
        <label class="text-sm font-medium">Jenis Kelamin
          <select name="jenis_kelamin" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            <option value="L" <?= $person->jenis_kelamin === 'L' ? 'selected' : ''; ?>>Laki-laki</option>
            <option value="P" <?= $person->jenis_kelamin === 'P' ? 'selected' : ''; ?>>Perempuan</option>
          </select>
        </label>
        <label class="text-sm font-medium">Blok<input name="blok" required maxlength="20" value="<?= html_escape($person->blok); ?>" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5"></label>
        <label class="text-sm font-medium">Nomor Rumah<input name="no_rumah" required maxlength="20" value="<?= html_escape($person->no_rumah); ?>" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5"></label>
      </div>
    <?php endif; ?>
    <div class="border-t border-slate-100 pt-5">
      <h3 class="font-semibold">Ubah password <span class="text-sm font-normal text-slate-500">(opsional)</span></h3>
      <div class="mt-3 grid gap-4 sm:grid-cols-2">
        <label class="text-sm font-medium">Password lama
          <div data-password-field class="relative mt-1">
            <input data-password-input type="password" name="password_lama" autocomplete="current-password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 pr-12">
            <button hidden data-password-toggle type="button" aria-label="Tampilkan password" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center rounded-r-xl px-3 text-slate-500 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-600">
              <svg data-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg data-eye-off hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.25 0 9.75 7 9.75 7a16 16 0 0 1-3.1 3.9M6.2 6.2C3.7 7.9 2.25 12 2.25 12s3.5 7 9.75 7c1 0 1.95-.2 2.8-.5"/></svg>
            </button>
          </div>
        </label>
        <label class="text-sm font-medium">Password baru
          <div data-password-field class="relative mt-1">
            <input data-password-input type="password" name="password_baru" minlength="8" autocomplete="new-password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 pr-12">
            <button hidden data-password-toggle type="button" aria-label="Tampilkan password" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center rounded-r-xl px-3 text-slate-500 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-600">
              <svg data-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg data-eye-off hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.25 0 9.75 7 9.75 7a16 16 0 0 1-3.1 3.9M6.2 6.2C3.7 7.9 2.25 12 2.25 12s3.5 7 9.75 7c1 0 1.95-.2 2.8-.5"/></svg>
            </button>
          </div>
        </label>
      </div>
    </div>
    <button class="rounded-xl bg-sky-700 px-5 py-3 font-semibold text-white hover:bg-sky-800">Simpan Pengaturan</button>
  <?= form_close(); ?>
</section>
