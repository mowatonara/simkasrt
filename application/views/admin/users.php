<div class="mb-6">
  <h2 class="text-2xl font-bold">Pengguna</h2>
  <p class="mt-1 text-sm text-slate-500">Kelola akun pengurus dan tautkan akun warga ke data rumah tangga.</p>
</div>
<section class="mb-6 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
  <h3 class="mb-4 font-semibold"><?= $edit ? 'Ubah Akun' : 'Tambah Akun'; ?></h3>
  <?= form_open('admin/users', array('class' => 'grid gap-3 sm:grid-cols-2 lg:grid-cols-5')); ?>
    <input type="hidden" name="_action" value="simpan"><input type="hidden" name="id" value="<?= $edit ? (int) $edit->id : 0; ?>">
    <label class="text-sm">Nama<input id="nama-user" name="nama" required maxlength="120" value="<?= html_escape(set_value('nama', $edit ? $edit->nama : '')); ?>" class="mt-1 w-full rounded-xl border px-3 py-2"></label>
    <label class="text-sm">Username<input id="username-user" name="username" required minlength="4" maxlength="60" value="<?= html_escape(set_value('username', $edit ? $edit->username : '')); ?>" class="mt-1 w-full rounded-xl border px-3 py-2"><span id="username-help" class="mt-1 block text-xs text-slate-500">Untuk akun warga, username otomatis mengikuti nomor HP.</span></label>
    <label class="text-sm">Peran
      <select name="role" id="role-user" class="mt-1 w-full rounded-xl border px-3 py-2">
        <option value="admin" <?= set_select('role', 'admin', $edit && $edit->role === 'admin'); ?>>Admin / Pengurus</option>
        <option value="warga" <?= set_select('role', 'warga', $edit && $edit->role === 'warga'); ?>>Warga</option>
      </select>
    </label>
    <label id="warga-user-field" class="text-sm">Tautkan warga
      <select name="data_warga_id" class="mt-1 w-full rounded-xl border px-3 py-2">
        <option value="">Pilih warga</option>
        <?php foreach ($warga_tersedia as $person): ?>
          <option value="<?= (int) $person->id; ?>" data-phone="<?= html_escape(normalize_phone_login($person->no_hp)); ?>" data-name="<?= html_escape($person->nama); ?>" <?= $edit && (int) $edit->data_warga_id === (int) $person->id ? 'selected' : ''; ?>><?= html_escape($person->nama); ?> · <?= html_escape($person->blok . '/' . $person->no_rumah); ?> · <?= html_escape($person->no_hp); ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <label class="text-sm">Password <?= $edit ? '(kosongkan jika tetap)' : ''; ?>
      <div data-password-field class="relative mt-1">
        <input name="password" data-password-input type="password" <?= $edit ? '' : 'required minlength="8"'; ?> autocomplete="new-password" class="w-full rounded-xl border px-3 py-2 pr-12">
        <button hidden data-password-toggle type="button" aria-label="Tampilkan password" aria-pressed="false" class="absolute inset-y-0 right-0 flex items-center rounded-r-xl px-3 text-slate-500 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-600">
          <svg data-eye viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-7 9.75-7 9.75 7 9.75 7-3.5 7-9.75 7-9.75-7-9.75-7Z"/><circle cx="12" cy="12" r="3"/></svg>
          <svg data-eye-off hidden viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8"/><path stroke-linecap="round" stroke-linejoin="round" d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.25 0 9.75 7 9.75 7a16 16 0 0 1-3.1 3.9M6.2 6.2C3.7 7.9 2.25 12 2.25 12s3.5 7 9.75 7c1 0 1.95-.2 2.8-.5"/></svg>
        </button>
      </div>
    </label>
    <div class="flex items-end gap-2"><button class="rounded-xl bg-sky-700 px-4 py-2.5 font-semibold text-white"><?= $edit ? 'Simpan' : 'Tambah'; ?></button><?php if ($edit): ?><a href="<?= site_url('admin/users'); ?>" class="rounded-xl border px-4 py-2.5">Batal</a><?php endif; ?></div>
  <?= form_close(); ?>
</section>
<section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
  <div class="overflow-x-auto">
    <table class="w-full min-w-[700px] text-left text-sm">
      <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Nama</th><th class="px-4 py-3">Username</th><th class="px-4 py-3">Peran</th><th class="px-4 py-3">Data Warga</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr></thead>
      <tbody class="divide-y divide-slate-100">
      <?php foreach ($users as $user): ?>
        <tr>
          <td class="px-4 py-3 font-semibold"><?= html_escape($user->nama); ?></td><td class="px-4 py-3"><?= html_escape($user->username); ?></td>
          <td class="px-4 py-3 capitalize"><?= html_escape($user->role); ?></td><td class="px-4 py-3"><?= html_escape($user->nama_warga ?: '—'); ?></td>
          <td class="px-4 py-3"><?= $user->is_active ? 'Aktif' : 'Nonaktif'; ?></td>
          <td class="px-4 py-3"><a href="<?= site_url('admin/users?edit=' . (int) $user->id); ?>" class="rounded-lg bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-800">Edit</a>
            <?php if ($user->is_active && (int) $user->id !== (int) $current_user['id']): ?>
              <?= form_open('admin/users', array('class' => 'ml-1 inline')); ?><input type="hidden" name="_action" value="hapus"><input type="hidden" name="id" value="<?= (int) $user->id; ?>"><button onclick="return confirm('Nonaktifkan akun ini?')" class="rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700">Nonaktifkan</button><?= form_close(); ?>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
<script>
function toggleWargaField() {
  const isWarga = document.getElementById('role-user').value === 'warga';
  const wargaSelect = document.querySelector('[name="data_warga_id"]');
  const username = document.getElementById('username-user');
  const nama = document.getElementById('nama-user');
  const selected = wargaSelect.options[wargaSelect.selectedIndex];
  document.getElementById('warga-user-field').classList.toggle('hidden', !isWarga);
  username.readOnly = isWarga;
  username.classList.toggle('bg-slate-50', isWarga);
  if (isWarga) {
    username.value = selected && selected.value ? selected.dataset.phone : '';
    if (selected && selected.value) nama.value = selected.dataset.name;
  }
}
document.getElementById('role-user').addEventListener('change', toggleWargaField);
document.querySelector('[name="data_warga_id"]').addEventListener('change', toggleWargaField);
toggleWargaField();
</script>
