<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ============ BACKUP OTOMATIS (spatie/laravel-backup) ============
// PENTING: baris-baris di bawah ini hanya benar-benar jalan kalau server
// punya cron job `* * * * * php artisan schedule:run` — lihat BACA-INI.md
// untuk cara menambahkannya.

// 1. Buat cadangan baru (database + file storage/app/public) jam 02:00,
// dikirim ke semua disk tujuan yang aktif (local, dan Google Drive kalau
// sudah dikonfigurasi) — lihat config/backup.php.
Schedule::command('backup:run')->dailyAt('02:00')
    ->onFailure(fn () => Log::error('Backup harian (backup:run) gagal dijalankan.'));

// 2. Bersihkan cadangan lama sesuai aturan retensi di config/backup.php
// (harian 7 hari, mingguan 4 minggu, dst), jalan tepat sebelum backup baru
// dibuat supaya disk tidak sempat penuh dulu.
Schedule::command('backup:clean')->dailyAt('01:45');

// 3. Periksa kesehatan backup (terlalu lama / terlalu besar) tiap pagi —
// kalau ada masalah, notifikasi email otomatis terkirim sesuai
// config/backup.php > notifications.
Schedule::command('backup:monitor')->dailyAt('07:00');
