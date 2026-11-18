<div class="mb-6">
  <h2 class="text-2xl font-bold">History Pembayaran</h2>
  <p class="mt-1 text-sm text-slate-500">Riwayat setoran dan tagihan pada akun Anda.</p>
</div>
<?php if (!empty($tagihan)): ?>
  <section class="mb-6 rounded-2xl border border-amber-100 bg-amber-50 p-5">
    <div class="mb-3 flex items-start justify-between gap-3">
      <div><h3 class="font-semibold text-amber-950">Tagihan belum lunas</h3><p class="text-sm text-amber-800">Pilih tagihan untuk mengirim pembayaran.</p></div>
      <span class="rounded-full bg-white px-3 py-1 text-sm font-semibold text-amber-900"><?= count($tagihan); ?> tagihan</span>
    </div>
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
      <?php foreach ($tagihan as $bill): ?>
        <article class="rounded-xl border border-amber-100 bg-white p-4">
          <p class="font-semibold"><?= html_escape($bill->nama_iuran); ?></p>
          <p class="mt-1 text-sm text-slate-500">Periode <?= date('m/Y', strtotime($bill->periode)); ?> · jatuh tempo <?= html_escape($bill->jatuh_tempo); ?></p>
          <div class="mt-3 flex items-center justify-between">
            <span class="font-bold text-sky-900"><?= rupiah($bill->nominal); ?></span>
            <a href="<?= site_url('warga/pembayaran?tagihan_id=' . (int) $bill->id); ?>" class="rounded-lg bg-sky-700 px-3 py-2 text-sm font-semibold text-white">Bayar</a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </section>
<?php else: ?>
  <div class="mb-6 rounded-2xl bg-emerald-50 p-4 text-sm text-emerald-800">Tidak ada tagihan yang belum dibayar.</div>
<?php endif; ?>
<section class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
  <div class="border-b border-slate-100 p-4"><h3 class="font-semibold">Riwayat setoran</h3></div>
  <div class="overflow-x-auto">
    <table class="w-full min-w-[850px] text-left text-sm">
      <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="px-4 py-3">Tanggal</th><th class="px-4 py-3">Jenis Pembayaran</th><th class="px-4 py-3">Metode</th><th class="px-4 py-3 text-right">Jumlah</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Bukti / Catatan</th><th class="px-4 py-3">Aksi</th></tr></thead>
      <tbody class="divide-y divide-slate-100">
      <?php if (empty($history)): ?>
        <tr><td colspan="7" class="p-8 text-center text-slate-500">Belum ada pengajuan pembayaran.</td></tr>
      <?php else: foreach ($history as $payment): ?>
        <?php
          $badge = $payment->status === 'terverifikasi' ? 'bg-emerald-50 text-emerald-700' : ($payment->status === 'ditolak' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700');
          $statusLabel = $payment->status === 'terverifikasi' ? 'Lunas / Terverifikasi' : ($payment->status === 'ditolak' ? 'Ditolak' : 'Menunggu');
        ?>
        <tr class="align-top">
          <td class="px-4 py-4"><?= html_escape($payment->tanggal_bayar); ?></td>
          <td class="px-4 py-4"><?= html_escape($payment->nama_iuran); ?><?= $payment->periode ? '<br><span class="text-xs text-slate-500">' . date('m/Y', strtotime($payment->periode)) . '</span>' : ''; ?></td>
          <td class="px-4 py-4 uppercase"><?= html_escape($payment->metode); ?></td>
          <td class="px-4 py-4 text-right font-semibold"><?= rupiah($payment->jumlah); ?></td>
          <td class="px-4 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold <?= $badge; ?>"><?= $statusLabel; ?></span></td>
          <td class="px-4 py-4">
            <a class="font-semibold text-sky-700 hover:underline" target="_blank" rel="noopener" href="<?= site_url('warga/bukti/' . (int) $payment->id); ?>">Lihat Bukti</a>
            <?php if ($payment->alasan_penolakan): ?><p class="mt-1 max-w-xs text-xs text-rose-700"><?= html_escape($payment->alasan_penolakan); ?></p><?php endif; ?>
          </td>
          <td class="px-4 py-4">
            <?php if ($payment->status === 'terverifikasi'): ?>
              <button type="button" onclick="alert('Anda sudah lunas!')" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">Lunas</button>
            <?php elseif ($payment->status === 'ditolak' && $payment->tagihan_id): ?>
              <a href="<?= site_url('warga/pembayaran?tagihan_id=' . (int) $payment->tagihan_id); ?>" class="rounded-lg bg-sky-700 px-3 py-1.5 text-xs font-semibold text-white">Bayar ulang</a>
            <?php else: ?>
              <span class="text-xs text-slate-500">Menunggu verifikasi</span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</section>
