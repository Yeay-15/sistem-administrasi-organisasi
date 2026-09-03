<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    /**
     * Halaman ini KHUSUS Super Admin — bukan lewat sistem permission
     * biasa (role/divisi), karena isi backup adalah SELURUH data
     * organisasi (database + foto/dokumen) termasuk hash password akun.
     * Sengaja tidak dibuat jadi permission yang bisa didelegasikan ke
     * role lain.
     */
    public function index()
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);

        // spatie/laravel-backup menyimpan tiap cadangan sebagai satu file
        // .zip (database + storage/app/public dibungkus jadi satu) di
        // dalam folder bernama sesuai config('backup.backup.name') pada
        // disk 'local' — daftar di bawah ini murni membaca folder itu.
        $disk = Storage::disk('local');
        $backupName = config('backup.backup.name');

        $backups = collect($disk->exists($backupName) ? $disk->files($backupName) : [])
            ->filter(fn ($path) => str_ends_with($path, '.zip'))
            ->map(fn ($path) => [
                'name' => basename($path),
                'size' => $disk->size($path),
                'modified_at' => Carbon::createFromTimestamp($disk->lastModified($path)),
            ])
            ->sortByDesc('modified_at')
            ->values();

        $googleDriveActive = filled(config('services.google_drive.service_account_path'))
            && filled(config('services.google_drive.folder_id'));

        return view('backups.index', compact('backups', 'googleDriveActive'));
    }

    public function store()
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);

        $exitCode = Artisan::call('backup:run');
        $output = trim(Artisan::output());

        if ($exitCode !== 0) {
            AuditLog::record('Backup Database Gagal', $output ?: 'Backup gagal dibuat.');

            return back()->with('error', 'Gagal membuat cadangan. Buka log server untuk detail lengkap.');
        }

        AuditLog::record('Backup Database', 'Membuat cadangan (database + file) secara manual lewat dashboard.');

        return back()->with('success', 'Cadangan berhasil dibuat.');
    }

    public function download(string $filename)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
        $this->validateFilename($filename);

        $disk = Storage::disk('local');
        $path = config('backup.backup.name') . "/{$filename}";

        abort_unless($disk->exists($path), 404);

        AuditLog::record('Unduh Backup', "Mengunduh cadangan: {$filename}");

        return $disk->download($path);
    }

    public function destroy(string $filename)
    {
        abort_unless(Auth::user()->isSuperAdmin(), 403);
        $this->validateFilename($filename);

        $disk = Storage::disk('local');
        $path = config('backup.backup.name') . "/{$filename}";

        if ($disk->exists($path)) {
            $disk->delete($path);
            AuditLog::record('Hapus Backup', "Menghapus cadangan: {$filename}");
        }

        return back()->with('success', 'Cadangan berhasil dihapus.');
    }

    /**
     * Mencegah path traversal — $filename datang dari parameter URL, jadi
     * harus dipastikan benar-benar cuma nama file polos (tidak mengandung
     * '/' atau '..') sebelum dipakai membentuk path ke disk.
     */
    private function validateFilename(string $filename): void
    {
        abort_if(
            str_contains($filename, '/') || str_contains($filename, '..') || basename($filename) !== $filename,
            422,
            'Nama file tidak valid.'
        );
    }
}
