<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
  <div>
    <h2 class="text-2xl font-bold">Data Warga</h2>
    <p class="mt-1 text-sm text-slate-500">Kelola data warga, status iuran, dan notifikasi tagihan.</p>
  </div>
  <div class="flex items-center gap-3">
    <span class="rounded-full bg-sky-100 px-3 py-1 text-sm font-semibold text-sky-900"><?= (int) $total; ?> warga</span>
    <button id="warga-add-button" type="button" class="rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-sky-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-600 focus-visible:ring-offset-2">
      + Tambah Warga
    </button>
  </div>
</div>

<div id="warga-modal" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-slate-950/50 p-3 sm:p-6 <?= $open_modal ? '' : 'hidden'; ?>" role="dialog" aria-modal="true" aria-labelledby="warga-modal-title">
  <section class="my-auto w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl">
    <div class="flex items-start justify-between border-b border-slate-100 px-5 py-4 sm:px-6">
      <div>
        <h3 id="warga-modal-title" class="text-lg font-semibold"><?= $edit ? 'Ubah Data Warga' : 'Tambah Data Warga'; ?></h3>
        <p class="mt-1 text-sm text-slate-500">Lengkapi informasi warga dan akun login.</p>
      </div>
      <button type="button" data-warga-close class="rounded-lg px-3 py-1 text-2xl leading-none text-slate-500 hover:bg-slate-100 hover:text-slate-800" aria-label="Tutup form">&times;</button>
    </div>
    <div class="max-h-[calc(92vh-80px)] overflow-y-auto p-5 sm:p-6">
      <?= form_open('admin/warga', array('id' => 'warga-form', 'class' => 'grid gap-4 sm:grid-cols-2 lg:grid-cols-4')); ?>
    <input type="hidden" name="_action" value="save">
    <input id="warga-form-id" type="hidden" name="id" value="<?= $edit ? (int) $edit->id : 0; ?>">
    <label class="text-sm">Nama
      <input name="nama" required maxlength="120" value="<?= html_escape(set_value('nama', $edit ? $edit->nama : '')); ?>"
             class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
    </label>
    <label class="text-sm">NIK (16 digit)
      <input name="nik" required inputmode="numeric" pattern="[0-9]{16}" maxlength="16"
             value="<?= html_escape(set_value('nik', $edit ? $edit->nik : '')); ?>"
             class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
    </label>
    <label class="text-sm">Nomor HP
      <input name="no_hp" type="tel" required maxlength="25" inputmode="tel" placeholder="08xxxxxxxxxx"
             value="<?= html_escape(set_value('no_hp', $edit ? $edit->no_hp : '')); ?>"
             class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
    </label>
    <label class="text-sm"><span id="warga-password-label">Password login warga <?= $edit && $edit_account ? '(opsional, isi untuk mengganti)' : ''; ?></span>
      <div data-password-field class="relative mt-1">
        <input name="password" data-password-input type="password" <?= (!$edit || !$edit_account) ? 'required minlength="8"' : 'minlength="8"'; ?>
               autocomplete="new-password" placeholder="Minimal 8 karakter"
               class="w-full rounded-xl border border-slate-300 px-3 py-2 pr-12">
        <button hidden data-password-toggle type="button" aria-label="Tampilkan password" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center rounded-r-xl px-3 text-slate-500 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-600">
          <svg data-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z"/><circle cx="12" cy="12" r="3"/></svg>
          <svg data-eye-off hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.25 0 9.75 7 9.75 7a16 16 0 0 1-3.1 3.9M6.2 6.2C3.7 7.9 2.25 12 2.25 12s3.5 7 9.75 7c1 0 1.95-.2 2.8-.5"/></svg>
        </button>
      </div>
      <span class="mt-1 block text-xs text-slate-500">Username warga otomatis menggunakan nomor HP yang dinormalisasi menjadi angka.</span>
    </label>
    <label class="text-sm">Email
      <input name="email" type="email" maxlength="150" value="<?= html_escape(set_value('email', $edit ? $edit->email : '')); ?>"
             class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
    </label>
    <label class="text-sm">Jenis Kelamin
      <select name="jenis_kelamin" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
        <option value="">Pilih</option>
        <option value="L" <?= set_select('jenis_kelamin', 'L', $edit && $edit->jenis_kelamin === 'L'); ?>>Laki-laki</option>
        <option value="P" <?= set_select('jenis_kelamin', 'P', $edit && $edit->jenis_kelamin === 'P'); ?>>Perempuan</option>
      </select>
    </label>
    <label class="text-sm">Blok
      <input name="blok" required maxlength="20" value="<?= html_escape(set_value('blok', $edit ? $edit->blok : '')); ?>"
             class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
    </label>
    <label class="text-sm">Nomor Rumah
      <input name="no_rumah" required maxlength="20" value="<?= html_escape(set_value('no_rumah', $edit ? $edit->no_rumah : '')); ?>"
             class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
    </label>
    <div class="flex items-end justify-end gap-2 sm:col-span-2 lg:col-span-4">
      <button type="button" data-warga-close class="rounded-xl border border-slate-300 px-4 py-2.5 font-medium text-slate-700 hover:bg-slate-50">Batal</button>
      <button id="warga-submit-button" class="rounded-xl bg-sky-700 px-4 py-2.5 font-medium text-white hover:bg-sky-800"><?= $edit ? 'Simpan Perubahan' : 'Tambah Warga'; ?></button>
    </div>
      <?= form_close(); ?>
    </div>
  </section>
</div>

<section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
  <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-4">
    <form method="get" action="<?= site_url('admin/warga'); ?>" class="flex flex-wrap gap-2">
      <label class="sr-only" for="q">Cari warga</label>
      <input id="q" name="q" value="<?= html_escape($q); ?>" placeholder="Cari nama, NIK, HP, blok..."
             class="min-w-56 rounded-xl border border-slate-300 px-3 py-2">
      <label class="sr-only" for="per_page">Jumlah baris</label>
      <select id="per_page" name="per_page" class="rounded-xl border border-slate-300 px-3 py-2">
        <?php foreach (array(10,20,50,100) as $size): ?>
          <option value="<?= $size; ?>" <?= $per_page === $size ? 'selected' : ''; ?>><?= $size; ?> per halaman</option>
        <?php endforeach; ?>
      </select>
      <button class="rounded-xl bg-slate-800 px-4 py-2 text-white">Cari</button>
    </form>
    <p class="text-sm text-slate-500">NIK pada tabel disamarkan.</p>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-[1050px] w-full text-left text-sm">
      <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
        <tr>
          <th class="px-4 py-3">No</th><th class="px-4 py-3">Nama Warga</th><th class="px-4 py-3">NIK</th>
          <th class="px-4 py-3">No HP / Email</th><th class="px-4 py-3">Jenis Kelamin</th><th class="px-4 py-3">Blok / Rumah</th>
          <th class="px-4 py-3">Status Iuran</th><th class="px-4 py-3">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (empty($warga)): ?>
          <tr><td colspan="8" class="p-8 text-center text-slate-500">Data warga belum tersedia.</td></tr>
        <?php else: foreach ($warga as $index => $person): $status = $statuses[$person->id]; ?>
          <tr class="align-top hover:bg-slate-50/70">
            <td class="px-4 py-4"><?= (($page - 1) * $per_page) + $index + 1; ?></td>
            <td class="px-4 py-4">
              <p class="font-semibold"><?= html_escape($person->nama); ?></p>
              <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-xs <?= $person->status_aktif ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500'; ?>">
                <?= $person->status_aktif ? 'Aktif' : 'Nonaktif'; ?>
              </span>
            </td>
            <td class="px-4 py-4 font-mono"><?= html_escape(mask_nik($person->nik)); ?></td>
            <td class="px-4 py-4">
              <?php
                $phoneForMessage = normalize_phone_login($person->no_hp);
                $nameForMessage = trim(preg_replace('/\s+/', ' ', $person->nama));
                $whatsappMessage = "Salam, Bapak/Ibu " . $nameForMessage . "\n" .
                  "Anda sudah didaftarkan di Aplikasi Sistem Manajemen Kas RT (SIMKAS RT)\n" .
                  "Bisa diakses melalui laman:\n" .
                  "https://naracorp.web.id\n\n" .
                  "Username : " . $phoneForMessage . "\n" .
                  "Password :\n\n" .
                  "Untuk keamanan, harap mengubah password setelah berhasil login.\n\n" .
                  "Terima Kasih\n\n" .
                  "_Pesan ini dikirim secara otomatis_\n" .
                  "_Pengurus RT 01 MMTR_";
                $whatsappUrl = 'https://wa.me/' . whatsapp_number($person->no_hp) . '?text=' . rawurlencode($whatsappMessage);
              ?>
              <a href="<?= html_escape($whatsappUrl); ?>" target="_blank" rel="noopener noreferrer" class="font-medium text-emerald-700 hover:underline">
                <?= html_escape($person->no_hp); ?>
              </a><br><span class="text-xs text-slate-500"><?= html_escape($person->email); ?></span>
            </td>
            <td class="px-4 py-4"><?= $person->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
            <td class="px-4 py-4"><?= html_escape($person->blok); ?> / <?= html_escape($person->no_rumah); ?></td>
            <td class="px-4 py-4">
              <a href="<?= site_url('admin/warga/status/' . (int) $person->id); ?>" class="font-semibold text-sky-700 hover:underline">Lihat</a>
              <?php if ($status['menunggak']): ?><p class="mt-1 text-xs text-rose-600">Menunggak <?= (int) $status['menunggak']; ?> tagihan</p><?php endif; ?>
              <?php if ($status['menunggu']): ?><p class="mt-1 text-xs text-amber-700"><?= (int) $status['menunggu']; ?> menunggu verifikasi</p><?php endif; ?>
            </td>
            <td class="px-4 py-4">
              <div class="flex flex-wrap gap-2">
                <a class="rounded-lg bg-sky-50 px-2.5 py-1.5 text-xs font-semibold text-sky-800" href="<?= site_url('admin/warga?edit=' . (int) $person->id); ?>">Edit</a>
                <?php if ($person->status_aktif): ?>
                  <?= form_open('admin/warga/hapus/' . (int) $person->id, array('class' => 'inline')); ?>
                    <button class="rounded-lg bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-700" onclick="return confirm('Nonaktifkan warga ini? Riwayat kas tetap disimpan.')">Hapus</button>
                  <?= form_close(); ?>
                  <?= form_open('admin/warga', array('class' => 'flex items-center gap-1')); ?>
                    <input type="hidden" name="_action" value="buat_tagihan">
                    <input type="hidden" name="data_warga_id" value="<?= (int) $person->id; ?>">
                    <input type="month" name="periode" value="<?= date('Y-m'); ?>" required class="w-32 rounded-lg border border-slate-300 px-2 py-1 text-xs">
                    <button class="rounded-lg bg-indigo-50 px-2 py-1.5 text-xs font-semibold text-indigo-700">Tagihkan</button>
                  <?= form_close(); ?>
                <?php endif; ?>
              </div>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <?php $pages = max(1, (int) ceil($total / $per_page)); ?>
  <div class="flex items-center justify-between border-t border-slate-100 p-4 text-sm">
    <p class="text-slate-500">Halaman <?= (int) $page; ?> dari <?= $pages; ?></p>
    <div class="flex gap-2">
      <?php if ($page > 1): ?><a class="rounded-lg border px-3 py-1.5" href="<?= site_url('admin/warga') . '?' . http_build_query(array('q' => $q, 'per_page' => $per_page, 'page' => $page - 1)); ?>">Sebelumnya</a><?php endif; ?>
      <?php if ($page < $pages): ?><a class="rounded-lg border px-3 py-1.5" href="<?= site_url('admin/warga') . '?' . http_build_query(array('q' => $q, 'per_page' => $per_page, 'page' => $page + 1)); ?>">Berikutnya</a><?php endif; ?>
    </div>
  </div>
</section>

<script>
(function () {
  var modal = document.getElementById('warga-modal');
  var form = document.getElementById('warga-form');
  var addButton = document.getElementById('warga-add-button');
  var title = document.getElementById('warga-modal-title');
  var submitButton = document.getElementById('warga-submit-button');
  var previousFocus = null;

  if (!modal || !form || !addButton) {
    return;
  }

  function openModal() {
    previousFocus = document.activeElement;
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    var firstField = modal.querySelector('input:not([type="hidden"]), select');
    if (firstField) {
      firstField.focus();
    }
  }

  function closeModal() {
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');

    var url = new URL(window.location.href);
    if (url.searchParams.has('edit')) {
      url.searchParams.delete('edit');
      window.history.replaceState({}, '', url.pathname + (url.search ? url.search : '') + url.hash);
    }

    var focusTarget = previousFocus && document.contains(previousFocus) ? previousFocus : addButton;
    focusTarget.focus();
  }

  function resetForAdd() {
    ['nama', 'nik', 'no_hp', 'email', 'blok', 'no_rumah'].forEach(function (name) {
      var field = form.querySelector('[name="' + name + '"]');
      if (field) {
        field.value = '';
      }
    });

    var idField = form.querySelector('[name="id"]');
    var genderField = form.querySelector('[name="jenis_kelamin"]');
    var passwordField = form.querySelector('[name="password"]');
    if (idField) {
      idField.value = '0';
    }
    if (genderField) {
      genderField.value = '';
    }
    if (passwordField) {
      passwordField.value = '';
      passwordField.type = 'password';
      passwordField.required = true;
      passwordField.minLength = 8;
      var toggle = passwordField.closest('[data-password-field]').querySelector('[data-password-toggle]');
      var eye = toggle.querySelector('[data-eye]');
      var eyeOff = toggle.querySelector('[data-eye-off]');
      toggle.setAttribute('aria-label', 'Tampilkan password');
      toggle.setAttribute('aria-pressed', 'false');
      toggle.title = 'Tampilkan password';
      eye.hidden = false;
      eyeOff.hidden = true;
    }

    title.textContent = 'Tambah Data Warga';
    document.getElementById('warga-password-label').textContent = 'Password login warga';
    submitButton.textContent = 'Tambah Warga';
  }

  addButton.addEventListener('click', function () {
    resetForAdd();
    openModal();
  });

  modal.querySelectorAll('[data-warga-close]').forEach(function (button) {
    button.addEventListener('click', closeModal);
  });

  modal.addEventListener('click', function (event) {
    if (event.target === modal) {
      closeModal();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (modal.classList.contains('hidden')) {
      return;
    }
    if (event.key === 'Escape') {
      closeModal();
      return;
    }
    if (event.key === 'Tab') {
      var focusable = modal.querySelectorAll('button:not([hidden]), input:not([type="hidden"]), select, a[href]');
      if (!focusable.length) {
        return;
      }
      var first = focusable[0];
      var last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
  });

  if (!modal.classList.contains('hidden')) {
    document.body.classList.add('overflow-hidden');
    var firstField = modal.querySelector('input:not([type="hidden"]), select');
    if (firstField) {
      firstField.focus();
    }
  }
})();
</script>
