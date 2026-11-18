<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
  <div>
    <h2 class="text-2xl font-bold">Buku Kas</h2>
    <p class="mt-1 text-sm text-slate-500"><?= !empty($read_only) ? 'Tampilan baca saja untuk warga.' : 'Pemasukan terverifikasi dan pengeluaran kas RT.'; ?></p>
  </div>
  <?php if (empty($read_only)): ?>
    <a href="<?= site_url('admin/buku-kas/cetak') . '?' . http_build_query(array('tahun' => $tahun, 'bulan' => $bulan)); ?>"
       target="_blank" class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold hover:bg-slate-50">Cetak Buku Kas</a>
  <?php endif; ?>
</div>
<section class="mb-5 grid gap-4 sm:grid-cols-3">
  <article class="rounded-2xl bg-emerald-50 p-4"><p class="text-sm text-emerald-800">Pemasukan</p><p class="mt-1 text-xl font-bold text-emerald-900"><?= rupiah($total_masuk); ?></p></article>
  <article class="rounded-2xl bg-rose-50 p-4"><p class="text-sm text-rose-800">Pengeluaran</p><p class="mt-1 text-xl font-bold text-rose-900"><?= rupiah($total_keluar); ?></p></article>
  <article class="rounded-2xl bg-sky-50 p-4"><p class="text-sm text-sky-800">Saldo Kas</p><p class="mt-1 text-xl font-bold text-sky-900"><?= rupiah($total_masuk - $total_keluar); ?></p></article>
</section>
<section class="mb-5 flex flex-wrap items-end justify-between gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
  <form method="get" class="flex flex-wrap items-end gap-2">
    <label class="text-xs text-slate-500">Tahun
      <input name="tahun" type="number" min="2000" max="<?= date('Y'); ?>" value="<?= (int) $tahun; ?>" class="mt-1 block w-28 rounded-lg border border-slate-300 px-3 py-2">
    </label>
    <label class="text-xs text-slate-500">Bulan
      <select name="bulan" class="mt-1 block rounded-lg border border-slate-300 px-3 py-2">
        <option value="0">Semua bulan</option>
        <?php for ($i=1; $i<=12; $i++): ?>
          <option value="<?= $i; ?>" <?= (int) $bulan === $i ? 'selected' : ''; ?>><?= date('F', mktime(0,0,0,$i,1)); ?></option>
        <?php endfor; ?>
      </select>
    </label>
    <button class="rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white">Tampilkan</button>
  </form>
  <?php if (empty($read_only)): ?>
    <a href="#form-pengeluaran" class="rounded-lg bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700">+ Input Pengeluaran</a>
  <?php endif; ?>
</section>
<?php if (empty($read_only)): ?>
<section id="form-pengeluaran" class="mb-5 rounded-2xl border border-rose-100 bg-rose-50/60 p-5">
  <h3 class="mb-3 font-semibold">Catat Pengeluaran</h3>
  <?= form_open('admin/buku-kas', array('class' => 'grid gap-3 sm:grid-cols-2 lg:grid-cols-4')); ?>
    <label class="text-sm">Tanggal<input type="date" name="tanggal" required value="<?= date('Y-m-d'); ?>" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2"></label>
    <label class="text-sm">Kategori<input name="kategori" required maxlength="100" placeholder="Operasional, kebersihan..." class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2"></label>
    <label class="text-sm">Deskripsi<input name="deskripsi" required maxlength="500" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2"></label>
    <div class="flex items-end gap-2"><label class="flex-1 text-sm">Nominal<input type="number" name="jumlah" required min="1" step="1" inputmode="numeric" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2"></label><button class="rounded-xl bg-rose-700 px-4 py-2.5 font-semibold text-white">Simpan</button></div>
  <?= form_close(); ?>
</section>
<?php endif; ?>
<section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
  <div class="overflow-x-auto">
    <table class="w-full min-w-[760px] text-left text-sm">
      <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Arus</th><th class="px-4 py-3">Kategori</th><th class="px-4 py-3">Keterangan</th><th class="px-4 py-3 text-right">Jumlah</th></tr></thead>
      <tbody class="divide-y divide-slate-100">
        <?php if (empty($transaksi)): ?><tr><td colspan="5" class="p-8 text-center text-slate-500">Tidak ada transaksi pada periode ini.</td></tr>
        <?php else: foreach ($transaksi as $row): ?>
          <tr>
            <td class="px-4 py-3"><?= html_escape($row->tanggal); ?></td>
            <td class="px-4 py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold <?= $row->arah === 'masuk' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'; ?>"><?= ucfirst($row->arah); ?></span></td>
            <td class="px-4 py-3 font-medium"><?= html_escape($row->kategori); ?></td>
            <td class="px-4 py-3 text-slate-600"><?= html_escape($row->deskripsi_tampil); ?></td>
            <td class="px-4 py-3 text-right font-semibold <?= $row->arah === 'masuk' ? 'text-emerald-700' : 'text-rose-700'; ?>"><?= $row->arah === 'masuk' ? '+' : '-'; ?><?= rupiah($row->jumlah); ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</section>
