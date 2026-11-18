<div class="mb-6">
  <h2 class="text-2xl font-bold">Iuran & Tagihan</h2>
  <p class="mt-1 text-sm text-slate-500">Atur jenis dan nominal iuran. Tagihan dibuat per warga dan per periode.</p>
</div>
<section class="mb-6 rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
  <h3 class="mb-4 font-semibold"><?= $edit ? 'Ubah Jenis Iuran' : 'Tambah Jenis Iuran'; ?></h3>
  <?= form_open('admin/iuran', array('class' => 'grid gap-3 sm:grid-cols-2 lg:grid-cols-4')); ?>
    <input type="hidden" name="_action" value="simpan">
    <input type="hidden" name="id" value="<?= $edit ? (int) $edit->id : 0; ?>">
    <label class="text-sm">Nama iuran
      <input name="nama" required maxlength="100" value="<?= html_escape(set_value('nama', $edit ? $edit->nama : '')); ?>"
             class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2">
    </label>
    <label class="text-sm">Nominal (rupiah)
      <input name="nominal" type="number" min="0" step="1" inputmode="numeric" required value="<?= html_escape(set_value('nominal', $edit ? $edit->nominal : '100000')); ?>"
             class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2">
    </label>
    <label class="text-sm">Frekuensi
      <select name="frekuensi" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2">
        <option value="bulanan" <?= set_select('frekuensi', 'bulanan', !$edit || $edit->frekuensi === 'bulanan'); ?>>Bulanan</option>
        <option value="sekali" <?= set_select('frekuensi', 'sekali', $edit && $edit->frekuensi === 'sekali'); ?>>Sekali / sumbangan</option>
      </select>
    </label>
    <div class="flex items-end gap-2">
      <button class="rounded-xl bg-sky-700 px-4 py-2.5 font-medium text-white"><?= $edit ? 'Simpan' : 'Tambah'; ?></button>
      <?php if ($edit): ?><a href="<?= site_url('admin/iuran'); ?>" class="rounded-xl border px-4 py-2.5">Batal</a><?php endif; ?>
    </div>
  <?= form_close(); ?>
</section>
<section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
  <div class="overflow-x-auto">
    <table class="w-full min-w-[700px] text-left text-sm">
      <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">No</th><th class="px-4 py-3">Jenis Iuran</th><th class="px-4 py-3">Nominal</th><th class="px-4 py-3">Frekuensi</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Aksi</th></tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (empty($iuran)): ?><tr><td colspan="6" class="p-8 text-center text-slate-500">Belum ada jenis iuran.</td></tr>
        <?php else: foreach ($iuran as $index => $item): ?>
          <tr>
            <td class="px-4 py-3"><?= $index + 1; ?></td><td class="px-4 py-3 font-semibold"><?= html_escape($item->nama); ?></td>
            <td class="px-4 py-3"><?= rupiah($item->nominal); ?></td>
            <td class="px-4 py-3 capitalize"><?= html_escape($item->frekuensi); ?></td>
            <td class="px-4 py-3"><?= $item->aktif ? 'Aktif' : 'Nonaktif'; ?></td>
            <td class="px-4 py-3">
              <a href="<?= site_url('admin/iuran?edit=' . (int) $item->id); ?>" class="rounded-lg bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-800">Edit</a>
              <?= form_open('admin/iuran', array('class' => 'ml-1 inline')); ?>
                <input type="hidden" name="_action" value="hapus"><input type="hidden" name="id" value="<?= (int) $item->id; ?>">
                <button class="rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700" onclick="return confirm('Hapus atau nonaktifkan jenis iuran ini?')">Hapus</button>
              <?= form_close(); ?>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</section>
