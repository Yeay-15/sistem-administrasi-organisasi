<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Spatie\Backup\Events\BackupZipWasCreated;
use Throwable;

class UploadBackupToGoogleDrive
{
    /**
     * Mengunggah file .zip backup yang baru saja dibuat ke Google Drive.
     *
     * Memakai OAuth (autentikasi sebagai akun Gmail biasa) — BUKAN Service
     * Account. Sempat dicoba pakai Service Account, tapi Google menolak
     * dengan error "storageQuotaExceeded": Service Account tidak punya
     * kuota penyimpanan sendiri di akun Google/Gmail biasa (cuma tersedia
     * kalau pakai Shared Drive dari Google Workspace berbayar). Dengan
     * OAuth, file yang diunggah numpang di kuota 15GB gratis akun Gmail
     * yang dipakai autentikasi — jadi tidak kena batasan itu.
     *
     * Tetap TIDAK memakai SDK resmi google/apiclient (riwayat masalah
     * bentrok versi Guzzle) — dipanggil langsung lewat REST API Google,
     * pakai HTTP client bawaan Laravel.
     */
    public function handle(BackupZipWasCreated $event): void
    {
        $clientId = config('services.google_drive.client_id');
        $clientSecret = config('services.google_drive.client_secret');
        $refreshToken = config('services.google_drive.refresh_token');
        $folderId = config('services.google_drive.folder_id');

        if (! $clientId || ! $clientSecret || ! $refreshToken || ! $folderId) {
            return;
        }

        if (! property_exists($event, 'pathToZip') || ! file_exists($event->pathToZip)) {
            Log::warning('Google Drive: tidak menemukan path file .zip backup dari event BackupZipWasCreated. Unggah dilewati.');

            return;
        }

        try {
            $accessToken = $this->getAccessToken($clientId, $clientSecret, $refreshToken);
            $this->uploadFile($accessToken, $event->pathToZip, $folderId);

            Log::info('Google Drive: backup "' . basename($event->pathToZip) . '" berhasil diunggah.');
        } catch (Throwable $e) {
            // Sengaja tidak dilempar ulang (rethrow) — kalau upload ke
            // Google Drive gagal, backup LOKAL yang sudah berhasil dibuat
            // tetap dianggap sukses. Kegagalan ini cukup dicatat ke log.
            Log::error('Google Drive: gagal mengunggah backup — ' . $e->getMessage());
        }
    }

    /**
     * Menukar refresh token (yang tidak pernah kedaluwarsa, SELAMA
     * consent screen di Google Cloud Console statusnya "In production" —
     * bukan "Testing") dengan access token yang berlaku sementara,
     * lewat endpoint token resmi Google.
     */
    private function getAccessToken(string $clientId, string $clientSecret, string $refreshToken): string
    {
        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->failed() || ! $response->json('access_token')) {
            throw new RuntimeException('Gagal mengambil access token dari Google: ' . $response->body());
        }

        return $response->json('access_token');
    }

    /**
     * "Multipart upload" ke Google Drive API v3 — satu request berisi 2
     * bagian: metadata (nama file + folder tujuan) dalam JSON, lalu isi
     * file .zip-nya dalam bentuk mentah.
     */
    private function uploadFile(string $accessToken, string $filePath, string $folderId): void
    {
        $boundary = 'katiber-backup-' . bin2hex(random_bytes(16));

        $metadata = json_encode([
            'name' => basename($filePath),
            'parents' => [$folderId],
        ]);

        $body = "--{$boundary}\r\n"
            . "Content-Type: application/json; charset=UTF-8\r\n\r\n"
            . $metadata . "\r\n"
            . "--{$boundary}\r\n"
            . "Content-Type: application/zip\r\n\r\n"
            . file_get_contents($filePath) . "\r\n"
            . "--{$boundary}--";

        $response = Http::withToken($accessToken)
            ->withBody($body, "multipart/related; boundary={$boundary}")
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart');

        if ($response->failed()) {
            throw new RuntimeException('Google Drive menolak unggahan: ' . $response->body());
        }
    }
}
