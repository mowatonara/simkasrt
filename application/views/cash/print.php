<!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>Cetak Buku Kas SIMKAS-RT</title>
<link rel="icon" type="image/png" href="<?= base_url('assets/images/simkas-rt-logo.png'); ?>">
<style>
body{font:14px Arial,sans-serif;color:#1f2937;margin:30px}.brand{display:flex;align-items:center;gap:16px;margin-bottom:18px}.brand img{width:90px;height:90px;object-fit:contain;border:1px solid #e2e8f0;border-radius:14px}.brand h1{margin:0 0 4px}.brand p{margin:0;color:#64748b}.meta{margin:20px 0}table{border-collapse:collapse;width:100%}th,td{border:1px solid #cbd5e1;padding:8px;text-align:left}th{background:#eff6ff}.right{text-align:right}.print{padding:8px 14px;margin-bottom:16px}@media print{.print{display:none}body{margin:0}}
</style></head><body>
<button class="print" onclick="window.print()">Cetak</button>
<div class="brand">
  <img src="<?= base_url('assets/images/simkas-rt-logo.png'); ?>" alt="Logo SIMKAS-RT">
  <div><h1>Buku Kas SIMKAS-RT</h1><p>Ringkasan arus kas warga</p></div>
</div>
<div class="meta">Periode: <?= (int) $bulan ? date('F', mktime(0,0,0,(int) $bulan,1)) . ' ' : ''; ?><?= (int) $tahun; ?> ¡¤ Dicetak <?= date('d-m-Y H:i'); ?></div>
<table><thead><tr><th>Tanggal</th><th>Arus</th><th>Kategori</th><th>Keterangan</th><th class="right">Jumlah</th></tr></thead>
<tbody>
<?php $masuk=0; $keluar=0; foreach ($transaksi as $row): ?>
  <?php if ($row->arah === 'masuk') {$masuk += (float)$row->jumlah;} else {$keluar += (float)$row->jumlah;} ?>
  <tr><td><?= html_escape($row->tanggal); ?></td><td><?= ucfirst($row->arah); ?></td><td><?= html_escape($row->kategori); ?></td><td><?= html_escape($row->deskripsi_tampil); ?></td><td class="right"><?= rupiah($row->jumlah); ?></td></tr>
<?php endforeach; ?>
<tr><th colspan="4">Total Pemasukan</th><th class="right"><?= rupiah($masuk); ?></th></tr>
<tr><th colspan="4">Total Pengeluaran</th><th class="right"><?= rupiah($keluar); ?></th></tr>
</tbody></table></body></html>
