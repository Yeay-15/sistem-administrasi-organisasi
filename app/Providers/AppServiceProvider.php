<?php

namespace App\Providers;

use App\Listeners\UploadBackupToGoogleDrive;
use App\Models\Aspiration;
use App\Models\HomeSetting;
use App\Models\User;
use App\Support\DatabaseDumpBinaryResolver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Spatie\Backup\Events\BackupZipWasCreated;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Menyuntikkan path binary mysqldump/pg_dump untuk spatie/laravel-backup
        // (lihat DatabaseDumpBinaryResolver). Dipanggil di SETIAP request
        // (bukan cuma CLI) karena tombol "Buat Cadangan Sekarang" di
        // dashboard memicu backup:run lewat request web biasa, bukan
        // terminal — resolver-nya sendiri sudah pakai cache 1 hari supaya
        // tetap murah dijalankan berulang-ulang.
        DatabaseDumpBinaryResolver::applyToConfig();

        // Didaftarkan eksplisit di sini (bukan mengandalkan auto-discovery
        // Laravel untuk listener) supaya kepastiannya lebih terjamin: begitu
        // spatie/laravel-backup selesai membungkus database + storage/app/public
        // jadi satu file .zip lokal, langsung dicoba diunggah ke Google Drive.
        Event::listen(BackupZipWasCreated::class, UploadBackupToGoogleDrive::class);

        // Sistem Role & Permission dinamis (data-driven, bukan hard-coded).
        // Setiap kali ada pengecekan Gate::authorize('slug_permission') atau
        // @can('slug_permission') di Blade/route, keputusannya diambil dari sini:
        // - Super Admin selalu diloloskan.
        // - Ability lainnya dicek lewat User::hasPermission(), yang sudah
        //   menggabungkan hak akses dari Role MAUPUN dari Division secara
        //   otomatis (lihat App\Models\User::hasPermission()). Ini sengaja
        //   tidak lagi hardcode nama divisi tertentu (mis. "Infokom") — hak
        //   akses per-divisi sepenuhnya diatur lewat tabel division_permission
        //   via halaman Manajemen Peran & Akses.
        Gate::before(function (?User $user, string $ability) {
            if (! $user) {
                return null; // Tidak ada keputusan — biarkan guard 'auth' yang menangani.
            }

            if ($user->isSuperAdmin()) {
                return true;
            }

            return $user->hasPermission($ability);
        });

        // Logo, WhatsApp, Instagram, dan email resmi dipakai bersama oleh
        // navbar & footer di semua halaman publik, dan juga oleh sidebar admin
        // (logo + tombol "Lihat Situs Publik") — dibagikan lewat View Composer.
        //
        // Sengaja pakai wildcard '*' (bukan cuma 'layouts.public'/'layouts.app'):
        // saat sebuah view child (mis. public.contact) di-@extends ke sebuah
        // layout, isi @section-nya dirender memakai data milik view child itu
        // sendiri — BUKAN data milik view layout — jadi composer yang cuma
        // ditarget ke nama layout tidak akan sampai ke variabel di dalam
        // @section milik child-nya. Wildcard memastikan $homeSettings selalu
        // tersedia di mana pun ia dipakai. Query DB cukup sekali per request
        // berkat cache statis di bawah, dan closure ini hanya benar-benar
        // jalan saat sebuah view dirender (bukan saat artisan/migrate), jadi
        // aman dipanggil sebelum tabel home_settings ada.
        //
        // Dibungkus try/catch dengan sengaja: kalau database sedang tidak
        // bisa diakses (mis. server down), $homeSettings jatuh ke null
        // alih-alih ikut melemparkan error — supaya halaman error custom
        // (resources/views/errors/500.blade.php dkk) tetap bisa tampil
        // dengan baik alih-alih malah gagal render karena composer ini.
        View::composer('*', function ($view) {
            static $homeSettings = null;
            static $attempted = false;

            if ($homeSettings === null && ! $attempted) {
                $attempted = true;
                try {
                    $homeSettings = HomeSetting::current();
                } catch (\Throwable $e) {
                    $homeSettings = null;
                }
            }

            $view->with('homeSettings', $homeSettings);
        });

        // Badge "Aspirasi Mahasiswa" di sidebar + dropdown notifikasi di
        // header admin — jumlah aspirasi yang belum dibaca pengurus.
        // Ditarget ke 'layouts.app' saja (bukan wildcard '*') karena kedua
        // variabel ini cuma dipakai di markup milik layout itu sendiri
        // (sidebar & header), bukan di dalam @section milik view child.
        View::composer('layouts.app', function ($view) {
            $user = Auth::user();

            if ($user && ($user->isSuperAdmin() || $user->hasPermission('view_aspirations'))) {
                try {
                    $view->with('unreadAspirationsCount', Aspiration::where('is_read', false)->count());
                    $view->with('latestUnreadAspirations', Aspiration::where('is_read', false)->latest()->take(5)->get());
                } catch (\Throwable $e) {
                    $view->with('unreadAspirationsCount', 0);
                    $view->with('latestUnreadAspirations', collect());
                }
            }
        });
    }
}
