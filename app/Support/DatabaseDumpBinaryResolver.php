<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class DatabaseDumpBinaryResolver
{
    /**
     * Menyuntikkan path folder tempat "mysqldump"/"pg_dump" berada ke
     * config koneksi database yang sedang aktif — dipakai oleh
     * spatie/laravel-backup (lewat spatie/db-dumper) saat mem-backup
     * database. Ini mengatasi kasus umum di Windows/Laragon: binary-nya
     * memang ada, tapi tidak "terlihat" lewat PATH oleh proses PHP.
     *
     * PENTING: dipanggil di SETIAP request (lihat AppServiceProvider::boot()),
     * bukan cuma saat CLI — karena tombol "Buat Cadangan Sekarang" di
     * dashboard men-trigger backup:run dari REQUEST WEB biasa (lewat
     * Artisan::call() di dalam controller), bukan dari terminal. Supaya
     * tetap murah dijalankan tiap request, hasil pencariannya di-cache 1
     * hari (lihat resolve()) — jadi exec()/glob() cuma benar-benar
     * dijalankan sesekali, bukan di setiap page load.
     */
    public static function applyToConfig(): void
    {
        $connectionName = config('database.default');
        $driver = config("database.connections.{$connectionName}.driver");

        $binaryName = match ($driver) {
            'mysql', 'mariadb' => 'mysqldump',
            'pgsql' => 'pg_dump',
            default => null,
        };

        if (! $binaryName) {
            return;
        }

        // Sudah ada override manual (mis. lewat config lain)? Jangan ditimpa.
        if (config("database.connections.{$connectionName}.dump.dump_binary_path")) {
            return;
        }

        $binaryPath = self::resolveCached($binaryName);

        if ($binaryPath) {
            // spatie/db-dumper mengharapkan FOLDER tempat binary berada,
            // bukan path lengkap ke file .exe/.bin-nya.
            config(["database.connections.{$connectionName}.dump.dump_binary_path" => dirname($binaryPath)]);
        }
    }

    /**
     * Membungkus resolve() dengan cache 1 hari, supaya exec()/glob() (yang
     * relatif "berat" kalau dijalankan di tiap request) cuma benar-benar
     * dieksekusi sesekali. Kalau kamu baru saja memperbaiki instalasi
     * mysqldump/pg_dump dan ingin hasil pencariannya langsung di-refresh
     * tanpa menunggu 1 hari, jalankan `php artisan cache:clear`.
     */
    private static function resolveCached(string $binaryName): ?string
    {
        // Cache tidak bisa menyimpan null secara andal di semua driver, jadi
        // "tidak perlu override" direpresentasikan sebagai string kosong.
        $cacheKey = 'db-dump-binary-path:' . $binaryName;

        $cached = Cache::remember($cacheKey, now()->addDay(), function () use ($binaryName) {
            return self::resolve($binaryName) ?? '';
        });

        return $cached !== '' ? $cached : null;
    }

    /**
     * Mengembalikan path lengkap ke binary kalau perlu di-override secara
     * eksplisit, atau null kalau binary sudah bisa ditemukan lewat PATH
     * sistem seperti biasa (artinya tidak perlu override apa pun).
     */
    private static function resolve(string $binaryName): ?string
    {
        $envKey = strtoupper($binaryName) . '_PATH';
        $configured = env($envKey);

        if ($configured && File::exists($configured)) {
            return $configured;
        }

        if (self::existsInSystemPath($binaryName)) {
            return null;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            // .../laragon/www/nama-project (base_path) -> naik 2 folder -> .../laragon
            $laragonRoot = dirname(base_path(), 2);
            $matches = glob("{$laragonRoot}/bin/mysql/mysql-*/bin/{$binaryName}.exe") ?: [];

            if (! empty($matches)) {
                sort($matches);

                return end($matches);
            }
        }

        return null;
    }

    private static function existsInSystemPath(string $binary): bool
    {
        if (! function_exists('exec')) {
            return false;
        }

        $checkCommand = PHP_OS_FAMILY === 'Windows' ? "where {$binary} 2>NUL" : "command -v {$binary} 2>/dev/null";

        exec($checkCommand, $output, $exitCode);

        return $exitCode === 0;
    }
}
