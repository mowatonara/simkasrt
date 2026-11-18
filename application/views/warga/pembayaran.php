<div class="mb-6">
  <h2 class="text-2xl font-bold">Halaman Pembayaran</h2>
  <p class="mt-1 text-sm text-slate-500">Kirim pembayaran iuran atau sumbangan untuk diverifikasi pengurus.</p>
</div>
<div class="grid gap-6 xl:grid-cols-[1fr_0.8fr]">
  <section class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
    <h3 class="mb-4 font-semibold">Form Pengajuan Pembayaran</h3>
    <?= form_open_multipart('warga/pembayaran', array('id' => 'payment-form', 'class' => 'space-y-4')); ?>
      <label class="block text-sm font-medium">Tagihan bulanan
        <select id="bill-select" name="tagihan_id" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
          <option value="">Sumbangan / pembayaran sekali</option>
          <?php foreach ($tagihan as $bill): ?>
            <option value="<?= (int) $bill->id; ?>" data-amount="<?= (float) $bill->nominal; ?>"
                    <?= $selected_bill === (int) $bill->id ? 'selected' : ''; ?>>
              <?= html_escape($bill->nama_iuran); ?> · <?= date('m/Y', strtotime($bill->periode)); ?> · <?= rupiah($bill->nominal); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </label>
      <div id="donation-fields" class="grid gap-3 sm:grid-cols-2">
        <label class="text-sm font-medium">Jenis pembayaran sekali
          <select name="jenis_iuran_id" id="donation-type" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            <option value="">Pilih jenis</option>
            <?php foreach ($donasi as $item): ?>
              <option value="<?= (int) $item->id; ?>"><?= html_escape($item->nama); ?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <label class="text-sm font-medium">Jumlah (rupiah)
          <input id="amount-input" type="number" name="jumlah" min="1" step="1" inputmode="numeric" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5" placeholder="Contoh: 50000">
        </label>
      </div>
      <div class="grid gap-3 sm:grid-cols-2">
        <label class="text-sm font-medium">Tanggal bayar
          <input type="date" name="tanggal_bayar" required value="<?= date('Y-m-d'); ?>" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
        </label>
        <label class="text-sm font-medium">Metode pembayaran
          <select id="method-select" name="metode" required class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
            <option value="">Pilih metode</option><option value="transfer">Transfer</option><option value="qris">QRIS</option><option value="tunai">Tunai</option>
          </select>
        </label>
      </div>
      <div id="payment-details" class="hidden" aria-live="polite">
        <div id="transfer-details" class="hidden rounded-xl border border-sky-200 bg-sky-50 p-4 text-sm text-sky-950">
          <h4 class="font-semibold">Informasi transfer bank</h4>
          <p class="mt-2">Bank: <strong>BCA</strong></p>
          <p>Nomor rekening: <strong class="select-all text-base tracking-wide">8570015428</strong></p>
          <p>Atas nama: <strong>Muhammad Wahyu Saputro</strong></p>
        </div>
        <div id="qris-details" class="hidden rounded-xl border border-indigo-200 bg-indigo-50 p-4 text-center text-sm text-indigo-950">
          <h4 class="font-semibold">Pembayaran melalui QRIS</h4>
          <p class="mt-1">Pindai kode QRIS berikut menggunakan aplikasi pembayaran Anda.</p>
          <a href="<?= base_url('assets/images/qris-simkas-rt.png'); ?>" target="_blank" rel="noopener" class="mt-3 inline-block rounded-xl border border-slate-200 bg-white p-2">
            <img src="<?= base_url('assets/images/qris-simkas-rt.png'); ?>" alt="Kode QRIS KasRT 01 MMTR" class="mx-auto max-h-[28rem] w-full max-w-sm object-contain">
          </a>
          <p class="mt-2 text-xs text-indigo-800">Klik gambar untuk melihat ukuran penuh.</p>
        </div>
      </div>
      <label class="block text-sm font-medium">Bukti pembayaran
        <input type="file" name="bukti" required accept=".jpg,.jpeg,.png,.pdf" class="mt-1 w-full rounded-xl border border-slate-300 px-3 py-2.5">
        <span class="mt-1 block text-xs font-normal text-slate-500">JPG, PNG, atau PDF; ukuran maksimal 2 MB.</span>
      </label>
      <div class="rounded-xl bg-sky-50 p-3 text-sm text-sky-900">Status pengajuan akan menjadi <strong>Menunggu Verifikasi</strong> hingga diperiksa admin.</div>
      <button class="w-full rounded-xl bg-sky-700 px-4 py-3 font-semibold text-white hover:bg-sky-800">Kirim Pembayaran</button>
    <?= form_close(); ?>
  </section>
  <aside class="space-y-4">
    <div class="rounded-2xl bg-indigo-50 p-5">
      <h3 class="font-semibold text-indigo-950">Informasi pembayaran</h3>
      <ul class="mt-3 list-inside list-disc space-y-2 text-sm text-indigo-900">
        <li>Pilih tagihan agar nominal diambil dari data resmi.</li>
        <li>Untuk sumbangan, pilih jenis dan masukkan nominal.</li>
        <li>Informasi rekening atau QRIS tampil setelah metode dipilih.</li>
        <li>Pastikan bukti dan tanggal bayar terbaca jelas.</li>
      </ul>
    </div>
    <a href="<?= site_url('warga/history'); ?>" class="block rounded-2xl border border-slate-100 bg-white p-5 shadow-sm hover:border-sky-200">
      <p class="text-sm text-slate-500">Sudah mengirim?</p><p class="mt-1 font-semibold text-sky-800">Lihat status di History Pembayaran →</p>
    </a>
  </aside>
</div>
<script>
const billSelect = document.getElementById('bill-select');
const donationFields = document.getElementById('donation-fields');
const donationType = document.getElementById('donation-type');
const amountInput = document.getElementById('amount-input');
const methodSelect = document.getElementById('method-select');
const paymentDetails = document.getElementById('payment-details');
const transferDetails = document.getElementById('transfer-details');
const qrisDetails = document.getElementById('qris-details');
function syncPaymentType() {
  const isBill = billSelect.value !== '';
  donationFields.classList.toggle('hidden', isBill);
  donationType.required = !isBill;
  amountInput.required = !isBill;
  if (isBill) {
    donationType.value = '';
    amountInput.value = '';
  }
}
function syncPaymentDetails() {
  const method = methodSelect.value;
  const showDetails = method === 'transfer' || method === 'qris';
  paymentDetails.classList.toggle('hidden', !showDetails);
  transferDetails.classList.toggle('hidden', method !== 'transfer');
  qrisDetails.classList.toggle('hidden', method !== 'qris');
}
billSelect.addEventListener('change', syncPaymentType);
methodSelect.addEventListener('change', syncPaymentDetails);
syncPaymentType();
syncPaymentDetails();
</script>
