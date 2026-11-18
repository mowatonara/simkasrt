<?php
$monthNames = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember');
?>
<div class="mb-6 flex flex-wrap items-end justify-between gap-4">
  <div>
    <p class="text-sm text-slate-500">Ringkasan keuangan lingkungan</p>
    <h2 class="mt-1 text-2xl font-bold text-slate-900">Halo, <?= html_escape($current_user['nama']); ?></h2>
  </div>
  <form method="get" action="<?= site_url('dashboard'); ?>" class="flex gap-2">
    <label class="sr-only" for="tahun">Tahun grafik</label>
    <select id="tahun" name="tahun" class="rounded-xl border border-slate-300 bg-white px-3 py-2">
      <?php foreach ($daftar_tahun as $option): ?>
        <option value="<?= (int) $option; ?>" <?= (int) $option === (int) $tahun ? 'selected' : ''; ?>><?= (int) $option; ?></option>
      <?php endforeach; ?>
    </select>
    <label class="sr-only" for="bulan">Bulan ringkasan</label>
    <select id="bulan" name="bulan" class="rounded-xl border border-slate-300 bg-white px-3 py-2">
      <?php foreach ($monthNames as $idx => $name): ?>
        <option value="<?= $idx + 1; ?>" <?= ($idx + 1) === (int) $bulan ? 'selected' : ''; ?>><?= html_escape($name); ?></option>
      <?php endforeach; ?>
    </select>
    <button class="rounded-xl bg-sky-700 px-4 py-2 font-medium text-white hover:bg-sky-800">Terapkan</button>
  </form>
</div>

<section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
  <?php
    $cards = array(
      array('Saldo Kas', rupiah($saldo), 'bg-white', 'text-sky-900'),
      array('Pemasukan ' . $monthNames[$bulan - 1], rupiah($masuk_bulan), 'bg-emerald-50', 'text-emerald-900'),
      array('Pengeluaran ' . $monthNames[$bulan - 1], rupiah($keluar_bulan), 'bg-rose-50', 'text-rose-900'),
      array('Warga Terdaftar', number_format($jumlah_warga), 'bg-indigo-50', 'text-indigo-900')
    );
  ?>
  <?php foreach ($cards as $card): ?>
    <article class="rounded-2xl border border-slate-100 <?= $card[2]; ?> p-5 shadow-sm">
      <p class="text-sm text-slate-500"><?= html_escape($card[0]); ?></p>
      <p class="mt-2 text-2xl font-bold <?= $card[3]; ?>"><?= html_escape($card[1]); ?></p>
    </article>
  <?php endforeach; ?>
</section>

<section class="mt-6 grid gap-6 xl:grid-cols-[1.5fr_1fr]">
  <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
    <div class="mb-4">
      <h3 class="font-semibold text-slate-900">Pemasukan dan Pengeluaran</h3>
      <p class="text-sm text-slate-500">Ringkasan bulanan tahun <?= (int) $tahun; ?></p>
    </div>
    <div class="h-72"><canvas id="cash-chart"></canvas></div>
  </article>
  <article class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
    <div class="mb-4 flex items-center justify-between">
      <div>
        <h3 class="font-semibold text-slate-900">Transaksi Terakhir</h3>
        <p class="text-sm text-slate-500">Pemasukan dan pengeluaran kas</p>
      </div>
      <a href="<?= site_url($role === 'admin' ? 'admin/buku-kas' : 'warga/buku-kas'); ?>" class="text-sm font-semibold text-sky-700">Buku Kas</a>
    </div>
    <?php if (empty($transaksi_terakhir)): ?>
      <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-500">Belum ada transaksi yang tercatat.</p>
    <?php else: ?>
      <div class="space-y-3">
        <?php foreach ($transaksi_terakhir as $row): ?>
          <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3">
            <div class="min-w-0">
              <p class="truncate text-sm font-medium"><?= html_escape($row->kategori); ?></p>
              <p class="text-xs text-slate-500"><?= html_escape($row->tanggal); ?> · <?= html_escape($row->deskripsi); ?></p>
            </div>
            <p class="whitespace-nowrap text-sm font-semibold <?= $row->arah === 'masuk' ? 'text-emerald-700' : 'text-rose-700'; ?>">
              <?= $row->arah === 'masuk' ? '+' : '-'; ?><?= rupiah($row->jumlah); ?>
            </p>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <?php if ($role === 'admin'): ?>
      <a href="<?= site_url('admin/pembayaran'); ?>" class="mt-4 block rounded-xl bg-sky-50 p-3 text-sm font-medium text-sky-900 hover:bg-sky-100">
        <?= (int) $pembayaran_menunggu; ?> pembayaran menunggu verifikasi
      </a>
    <?php endif; ?>
  </article>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('cash-chart'), {
  type: 'bar',
  data: {
    labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
    datasets: [
      {label: 'Pemasukan', data: <?= json_encode($grafik_masuk, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>, backgroundColor: '#85d19a', borderRadius: 5},
      {label: 'Pengeluaran', data: <?= json_encode($grafik_keluar, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>, backgroundColor: '#e88a91', borderRadius: 5}
    ]
  },
  options: {responsive: true, maintainAspectRatio: false, scales: {y: {beginAtZero: true}}, plugins: {legend: {position: 'bottom'}}}
});
</script>
