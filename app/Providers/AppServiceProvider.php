<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
    }
}
