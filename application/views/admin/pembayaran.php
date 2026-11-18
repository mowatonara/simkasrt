<div class="mb-6">
  <h2 class="text-2xl font-bold">Verifikasi Pembayaran</h2>
  <p class="mt-1 text-sm text-slate-500">Setoran warga masuk ke buku kas hanya setelah disetujui.</p>
</div>
<section class="space-y-4">
  <?php if (empty($pembayaran)): ?>
    <div class="rounded-2xl border border-slate-100 bg-white p-10 text-center shadow-sm">
      <span class="text-4xl">✓</span><p class="mt-3 font-semibold">Tidak ada pembayaran menunggu.</p>
      <p class="mt-1 text-sm text-slate-500">Pengajuan baru dari warga akan tampil di halaman ini.</p>
    </div>
  <?php else: foreach ($pembayaran as $payment): ?>
    <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
      <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
          <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Menunggu Verifikasi</p>
          <h3 class="mt-1 text-lg font-bold"><?= html_escape($payment->nama_warga); ?></h3>
          <p class="text-sm text-slate-500"><?= html_escape($payment->nama_iuran); ?>
            <?= $payment->periode ? ' · ' . date('m/Y', strtotime($payment->periode)) : ' · Sumbangan'; ?></p>
        </div>
        <p class="text-xl font-bold text-sky-900"><?= rupiah($payment->jumlah); ?></p>
      </div>
      <div class="mt-4 grid gap-3 text-sm sm:grid-cols-3">
        <p><span class="text-slate-500">Tanggal bayar:</span> <?= html_escape($payment->tanggal_bayar); ?></p>
        <p><span class="text-slate-500">Metode:</span> <?= strtoupper(html_escape($payment->metode)); ?></p>
        <p><span class="text-slate-500">Dikirim:</span> <?= html_escape($payment->dibuat_pada); ?></p>
      </div>
      <div class="mt-4 flex flex-wrap items-end justify-between gap-4 border-t border-slate-100 pt-4">
        <a target="_blank" rel="noopener" href="<?= site_url('admin/pembayaran/bukti/' . (int) $payment->id); ?>"
           class="rounded-xl bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-800 hover:bg-sky-100">Lihat Bukti</a>
        <?= form_open('admin/pembayaran', array('class' => 'flex flex-1 flex-wrap items-end justify-end gap-2')); ?>
          <input type="hidden" name="pembayaran_id" value="<?= (int) $payment->id; ?>">
          <label class="min-w-52 flex-1 text-xs text-slate-500">Alasan bila ditolak
            <input name="alasan" maxlength="500" placeholder="Contoh: nominal tidak sesuai"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2">
          </label>
          <button name="keputusan" value="tolak" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700">Tolak</button>
          <button name="keputusan" value="setujui" class="rounded-xl bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800">Setujui</button>
        <?= form_close(); ?>
      </div>
    </article>
  <?php endforeach; endif; ?>
</section>
