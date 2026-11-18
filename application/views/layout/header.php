<?php
$isAdmin = isset($role) && $role === 'admin';
$menu = $isAdmin
    ? array(
        array('Dashboard', 'dashboard', '▦'),
        array('Data Warga', 'admin/warga', '♙'),
        array('Pembayaran', 'admin/pembayaran', '⇄'),
        array('Iuran & Tagihan', 'admin/iuran', '◷'),
        array('Buku Kas', 'admin/buku-kas', '▤'),
        array('Pengguna', 'admin/users', '♧'),
        array('Setting', 'admin/setting', '⚙')
    )
    : array(
        array('Dashboard', 'dashboard', '▦'),
        array('History Pembayaran', 'warga/history', '◷'),
        array('Halaman Pembayaran', 'warga/pembayaran', '⇄'),
        array('Buku Kas', 'warga/buku-kas', '▤'),
        array('Setting', 'warga/setting', '⚙')
    );
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= html_escape(isset($judul) ? $judul : 'SIMKAS-RT'); ?> | SIMKAS-RT</title>
  <link rel="icon" type="image/png" href="<?= base_url('assets/images/simkas-rt-logo.png'); ?>">
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script src="<?= base_url('assets/js/password-toggle.js'); ?>" defer></script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-800">
<div class="min-h-screen md:flex">
  <aside id="sidebar" class="hidden w-64 shrink-0 overflow-hidden bg-sky-100 p-4 transition-all duration-200 md:block">
    <div class="mb-7 flex items-center justify-between gap-2">
      <a class="menu-label flex items-center gap-3 whitespace-nowrap text-xl font-black text-sky-950" href="<?= site_url('dashboard'); ?>">
        <img src="<?= base_url('assets/images/simkas-rt-logo.png'); ?>" alt="Logo SIMKAS-RT" class="h-12 w-12 shrink-0 rounded-xl border border-sky-100 bg-white object-contain p-0.5">
        <span>SIMKAS-RT</span>
      </a>
      <button id="toggle-sidebar" type="button" class="rounded-lg px-3 py-2 text-sky-950 hover:bg-sky-200"
              aria-label="Perkecil menu">☰</button>
    </div>
    <nav class="space-y-1">
      <?php foreach ($menu as $item): ?>
        <a href="<?= site_url($item[1]); ?>"
           class="flex items-center gap-3 rounded-xl px-3 py-2.5 font-medium text-slate-700 hover:bg-white hover:text-sky-900">
          <span class="w-5 shrink-0 text-center" aria-hidden="true"><?= $item[2]; ?></span>
          <span class="menu-label whitespace-nowrap"><?= html_escape($item[0]); ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="menu-label mt-8 rounded-xl bg-white/70 p-3 text-xs leading-5 text-slate-600">
      Sistem Informasi Manajemen Kas Rukun Tetangga
    </div>
  </aside>

  <main class="min-w-0 flex-1">
    <header class="sticky top-0 z-20 flex items-center justify-between border-b border-slate-200 bg-white/95 px-4 py-3 backdrop-blur md:px-8">
      <div class="flex items-center gap-3">
        <button id="mobile-menu" type="button" class="rounded-lg bg-sky-100 px-3 py-2 md:hidden" aria-label="Menu">☰</button>
        <img src="<?= base_url('assets/images/simkas-rt-logo.png'); ?>" alt="Logo SIMKAS-RT" class="h-10 w-10 shrink-0 rounded-lg border border-slate-100 bg-white object-contain p-0.5 md:hidden">
        <div>
          <p class="text-xs text-slate-500"><?= $isAdmin ? 'Panel Pengurus RT' : 'Portal Warga'; ?></p>
          <h1 class="font-semibold text-slate-900"><?= html_escape(isset($judul) ? $judul : 'SIMKAS-RT'); ?></h1>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <?php if ($isAdmin): ?>
          <a href="<?= site_url('admin/pembayaran'); ?>" class="relative inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 hover:text-sky-800" aria-label="Pembayaran menunggu verifikasi" title="Pembayaran menunggu verifikasi">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
            </svg>
            <?php if (!empty($pending_payment_count)): ?>
              <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-rose-600 px-1 text-[10px] font-bold leading-none text-white ring-2 ring-white">
                <?= (int) $pending_payment_count > 99 ? '99+' : (int) $pending_payment_count; ?>
              </span>
            <?php endif; ?>
          </a>
        <?php else: ?>
          <details class="relative">
            <summary class="relative inline-flex h-10 w-10 cursor-pointer list-none items-center justify-center rounded-xl bg-sky-100 text-sky-800" aria-label="Notifikasi">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-5 w-5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" />
              </svg>
              <?php if (!empty($unread_count)): ?>
                <span class="absolute -right-1 -top-1 h-3 w-3 rounded-full bg-rose-500 ring-2 ring-white"></span>
              <?php endif; ?>
            </summary>
            <div class="absolute right-0 mt-3 w-80 max-w-[90vw] rounded-2xl border border-slate-200 bg-white p-3 shadow-xl">
              <p class="px-2 pb-2 font-semibold">Notifikasi</p>
              <?php if (empty($notices)): ?>
                <p class="p-3 text-sm text-slate-500">Belum ada notifikasi.</p>
              <?php else: foreach ($notices as $notice): ?>
                <?= form_open('warga/notifikasi/' . (int) $notice->id, array('class' => 'border-t border-slate-100')); ?>
                  <button class="w-full px-2 py-3 text-left text-sm hover:bg-sky-50" type="submit">
                    <span class="block <?= $notice->dibaca_pada ? 'text-slate-500' : 'font-medium text-sky-950'; ?>">
                      <?= html_escape($notice->pesan); ?>
                    </span>
                    <span class="mt-1 block text-xs text-slate-400"><?= html_escape($notice->dibuat_pada); ?></span>
                  </button>
                <?= form_close(); ?>
              <?php endforeach; endif; ?>
            </div>
          </details>
        <?php endif; ?>
        <div class="hidden text-right sm:block">
          <p class="text-sm font-semibold"><?= html_escape($current_user['nama']); ?></p>
          <p class="text-xs capitalize text-slate-500"><?= html_escape($current_user['role']); ?></p>
        </div>
        <?= form_open('logout', array('class' => 'm-0')); ?>
          <button type="submit" class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-medium hover:bg-slate-50">Keluar</button>
        <?= form_close(); ?>
      </div>
    </header>
    <div class="mx-auto max-w-7xl p-4 md:p-8">
      <?php if ($this->session->flashdata('success')): ?>
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
          <?= html_escape($this->session->flashdata('success')); ?>
        </div>
      <?php endif; ?>
      <?php if ($this->session->flashdata('error')): ?>
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
          <?= html_escape($this->session->flashdata('error')); ?>
        </div>
      <?php endif; ?>
