<div class="mx-auto max-w-4xl">
  <a href="<?= site_url('admin/warga'); ?>" class="text-sm font-semibold text-sky-700">← Kembali ke Data Warga</a>
  <section class="mt-4 rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
    <div class="flex flex-wrap items-start justify-between gap-4">
      <div>
        <p class="text-sm text-slate-500">Status pembayaran iuran</p>
        <h2 class="mt-1 text-2xl font-bold"><?= html_escape($person->nama); ?></h2>
        <p class="mt-1 font-mono text-sm text-slate-500">NIK <?= html_escape(mask_nik($person->nik)); ?></p>
      </div>
      <span class="rounded-full bg-sky-100 px-3 py-1 text-sm"><?= html_escape($person->blok); ?> / <?= html_escape($person->no_rumah); ?></span>
    </div>
    <div class="my-6 flex flex-wrap items-end justify-between gap-4 rounded-2xl bg-indigo-50 p-4">
      <div>
        <h3 class="font-semibold text-indigo-950">Buat tagihan bulanan</h3>
        <p class="text-sm text-indigo-800">Tagihan yang sudah ada tidak akan dibuat ulang.</p>
      </div>
      <?= form_open('admin/warga', array('class' => 'flex flex-wrap items-end gap-2')); ?>
        <input type="hidden" name="_action" value="buat_tagihan">
        <input type="hidden" name="data_warga_id" value="<?= (int) $person->id; ?>">
        <label class="text-xs">Periode
          <input type="month" name="periode" value="<?= date('Y-m'); ?>" required class="mt-1 block rounded-lg border border-indigo-200 bg-white px-3 py-2">
        </label>
        <button class="rounded-lg bg-indigo-700 px-4 py-2 text-sm font-semibold text-white">Buat dan Kirim</button>
      <?= form_close(); ?>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full min-w-[600px] text-left text-sm">
        <thead><tr class="border-b text-slate-500"><th class="py-3">Periode</th><th class="py-3">Jenis Iuran</th><th class="py-3">Nominal</th><th class="py-3">Jatuh Tempo</th><th class="py-3">Status</th></tr></thead>
        <tbody class="divide-y">
          <?php if (empty($tagihan)): ?>
            <tr><td colspan="5" class="py-8 text-center text-slate-500">Belum ada tagihan untuk warga ini.</td></tr>
          <?php else: foreach ($tagihan as $bill): ?>
            <tr>
              <td class="py-3"><?= date('m/Y', strtotime($bill->periode)); ?></td>
              <td class="py-3"><?= html_escape($bill->nama_iuran); ?></td>
              <td class="py-3"><?= rupiah($bill->nominal); ?></td>
              <td class="py-3"><?= html_escape($bill->jatuh_tempo); ?></td>
              <td class="py-3">
                <?php
                  $label = $bill->status === 'lunas' ? 'Lunas' : ($bill->status === 'menunggu_verifikasi' ? 'Menunggu Verifikasi' : 'Menunggak');
                  $badge = $bill->status === 'lunas' ? 'bg-emerald-50 text-emerald-700' : ($bill->status === 'menunggu_verifikasi' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700');
                ?>
                <span class="rounded-full px-2.5 py-1 text-xs font-semibold <?= $badge; ?>"><?= $label; ?></span>
              </td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
