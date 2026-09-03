<?php

return [

    'backup' => [

        /*
         * Nama backup ini dipakai sebagai nama folder tempat backup
         * disimpan di tiap disk tujuan (mis. folder "KATIBER" di dalam
         * Google Drive).
         */
        'name' => env('APP_NAME', 'KATIBER'),

        'source' => [

            'files' => [

                /*
                 * Kode aplikasi (app/, config/, dst) SENGAJA tidak
                 * disertakan lagi — itu sudah tersimpan dan bisa
                 * dipulihkan lewat Git, tidak perlu ikut di-backup
                 * berulang setiap hari. Yang benar-benar TIDAK BISA
                 * direkonstruksi kalau hilang cuma dua: isi database
                 * (di-dump otomatis lewat 'databases' di bawah) dan file
                 * unggahan pengguna di sini (foto pengurus, logo,
                 * dokumen).
                 */
                'include' => [
                    storage_path('app/public'),
                ],

                'exclude' => [],

                'follow_links' => false,

                'ignore_unreadable_directories' => false,

                /*
                 * Diisi eksplisit ke base_path() — supaya spatie/laravel-backup
                 * tahu persis folder mana yang jadi "akar bersama" dari semua
                 * entri 'include' di atas (app/, config/, storage/app/public/,
                 * dst), lalu memangkas prefix path itu dari setiap nama file
                 * di dalam zip. Tanpa ini, karena include-nya berupa beberapa
                 * folder terpisah, hasilnya jadi nama file super panjang yang
                 * "meratakan" seluruh path absolut Windows (D:\Software\...)
                 * jadi satu baris teks per file, bukan struktur folder wajar
                 * seperti project Laravel biasa.
                 */
                'relative_path' => base_path(),
            ],

            /*
             * Koneksi database yang akan di-dump. Otomatis ikut koneksi
             * default di .env (DB_CONNECTION) — tidak perlu diubah manual
             * kalau kamu ganti dari MySQL ke koneksi lain.
             */
            'databases' => [
                env('DB_CONNECTION', 'mysql'),
            ],
        ],

        'database_dump_compressor' => null,

        'database_dump_file_timestamp_format' => null,

        'database_dump_filename_base' => 'database',

        'database_dump_file_extension' => '',

        'destination' => [

            'compression_method' => \ZipArchive::CM_DEFAULT,

            'compression_level' => 9,

            'filename_prefix' => '',

            /*
             * Cuma "local" di sini — bukan berarti Google Drive tidak
             * dipakai! Karena Flysystem tidak punya adapter Google Drive
             * yang kompatibel dengan versi Guzzle yang dipakai Laravel di
             * project ini, unggah ke Google Drive dilakukan lewat jalur
             * terpisah: App\Listeners\UploadBackupToGoogleDrive (memakai
             * SDK resmi google/apiclient), yang otomatis jalan setiap kali
             * file .zip backup ini selesai dibuat — lihat AppServiceProvider.
             */
            'disks' => [
                'local',
            ],
        ],

        'temporary_directory' => storage_path('app/backup-temp'),

        'password' => env('BACKUP_ARCHIVE_PASSWORD'),

        'encryption' => 'default',

        'tries' => 1,

        'retry_delay' => 0,
    ],

    /*
     * Notifikasi HANYA dikirim untuk kejadian yang perlu ditindaklanjuti
     * (backup gagal, backup tidak sehat, cleanup gagal) — bukan tiap kali
     * backup sukses, supaya tidak membanjiri email dengan notifikasi
     * "semua baik-baik saja" setiap hari.
     */
    'notifications' => [

        'notifications' => [
            \Spatie\Backup\Notifications\Notifications\BackupHasFailedNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\UnhealthyBackupWasFoundNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\CleanupHasFailedNotification::class => ['mail'],
            \Spatie\Backup\Notifications\Notifications\BackupWasSuccessfulNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\HealthyBackupWasFoundNotification::class => [],
            \Spatie\Backup\Notifications\Notifications\CleanupWasSuccessfulNotification::class => [],
        ],

        'notifiable' => \Spatie\Backup\Notifications\Notifiable::class,

        'mail' => [
            'to' => env('BACKUP_ALERT_EMAIL', 'admin@katiber.local'),

            'from' => [
                'address' => env('MAIL_FROM_ADDRESS', 'backup@katiber.local'),
                'name' => env('APP_NAME', 'KATIBER'),
            ],
        ],

        'slack' => [
            'webhook_url' => '',
            'channel' => null,
            'username' => null,
            'icon' => null,
        ],
    ],

    'monitor_backups' => [
        [
            'name' => env('APP_NAME', 'KATIBER'),
            'disks' => [
                'local',
            ],
            'health_checks' => [
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumAgeInDays::class => 1,
                \Spatie\Backup\Tasks\Monitor\HealthChecks\MaximumStorageInMegabytes::class => 5000,
            ],
        ],
    ],

    'cleanup' => [
        'strategy' => \Spatie\Backup\Tasks\Cleanup\Strategies\DefaultStrategy::class,

        'default_strategy' => [

            /*
             * Sesuai permintaan: simpan backup harian selama 7 hari
             * terakhir, dan backup mingguan selama 1 bulan (4 minggu)
             * terakhir. Ditambah sedikit margin aman untuk bulanan/tahunan.
             */
            'keep_all_backups_for_days' => 2,

            'keep_daily_backups_for_days' => 7,

            'keep_weekly_backups_for_weeks' => 4,

            'keep_monthly_backups_for_months' => 3,

            'keep_yearly_backups_for_years' => 1,

            'delete_oldest_backups_when_using_more_megabytes_than' => 5000,
        ],
    ],
];
