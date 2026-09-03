# Perbaikan Final: Google Drive Pakai OAuth (bukan Service Account) + Backup Diperkecil

6 file berubah — timpa di path yang sama:
- `composer.json`
- `.env.example`
- `.gitignore`
- `config/backup.php`
- `config/services.php`
- `app/Listeners/UploadBackupToGoogleDrive.php`

## Ringkasan 2 perubahan

1. **Backup sekarang cuma `storage/app/public` + database** — kode
   aplikasi (app/, config/, dst) tidak ikut lagi, karena itu sudah aman
   tersimpan di Git. Ukuran backup jadi lebih kecil, lebih cepat, lebih
   fokus ke data yang benar-benar tidak bisa direkonstruksi.
2. **Google Drive ganti dari Service Account ke OAuth** — Service Account
   ternyata tidak bisa dipakai untuk kasus ini (lihat penjelasan di
   bawah). Ini perubahan terakhir, sudah diverifikasi konsepnya benar.

## Kenapa Service Account gagal (storageQuotaExceeded)

Google Drive membedakan dua jenis "identitas": akun manusia biasa (Gmail,
dapat jatah 15GB gratis) dan Service Account (robot, jatah penyimpanan
**0 byte** kalau bukan di Google Workspace berbayar). Waktu Service
Account mencoba membuat file baru — walau di folder yang sudah kamu
share ke dia — Google tetap menganggap Service Account itu "pemilik" file
barunya, dan langsung ditolak karena dia tidak punya kuota sama sekali.
Ini keterbatasan resmi dari Google, bukan sesuatu yang bisa diakali lewat
kode.

## Solusinya: OAuth (autentikasi sebagai akun Gmail biasa)

Dengan OAuth, file yang diunggah "numpang" di kuota akun Gmail asli yang
kamu pakai login — jadi tidak kena batasan Service Account sama sekali.

### Langkah A — Install ulang dependency

```bash
composer update -W
```
(`firebase/php-jwt` sudah tidak dipakai lagi — tidak perlu JWT untuk
OAuth, cukup pertukaran token biasa lewat form HTTP — jadi ketergantungan
itu saya lepas juga, mengurangi risiko konflik versi lebih lanjut.)

### Langkah B — Ambil kredensial OAuth dari Google Cloud Console

1. Buka [Google Cloud Console](https://console.cloud.google.com/) pakai
   akun Gmail khusus (`backup.katiber@gmail.com`).
2. Kalau project dari percobaan Service Account sebelumnya masih ada,
   bisa dipakai lagi — tidak perlu bikin baru. Pastikan **Google Drive
   API** sudah Enable (menu **APIs & Services → Library**).
3. Menu **APIs & Services → OAuth consent screen**:
   - Kalau belum pernah diisi: pilih **External**, isi nama aplikasi
     bebas, email kontak pakai email Gmail khusus tadi, simpan.
   - **PENTING — beda dari instruksi saya paling awal**: setelah
     tersimpan, cari tombol **"Publish App"** di halaman itu, klik, lalu
     konfirmasi. Ini mengubah status dari "Testing" ke **"In production"**
     — untuk scope Drive (tergolong "sensitive", bukan "restricted") dan
     pemakaian personal/organisasi kecil begini, **tidak perlu proses
     verifikasi resmi Google**. Tanpa langkah ini, token yang didapat di
     Langkah C akan otomatis mati dalam 7 hari.
4. Menu **APIs & Services → Credentials → Create Credentials → OAuth
   client ID**:
   - Application type: **Web application**.
   - **Authorized redirect URIs**, tambahkan persis:
     `https://developers.google.com/oauthplayground`
   - Klik Create — catat **Client ID** dan **Client Secret**.

### Langkah C — Ambil Refresh Token lewat OAuth Playground

1. Buka [Google OAuth Playground](https://developers.google.com/oauthplayground).
2. Klik ikon gerigi (⚙️) kanan atas → centang **"Use your own OAuth
   credentials"** → isi Client ID & Client Secret dari Langkah B.
3. Di panel kiri, cari **"Drive API v3"** → centang
   `https://www.googleapis.com/auth/drive.file`.
4. **Authorize APIs** → login pakai akun Gmail khusus → izinkan akses.
5. **Exchange authorization code for tokens** → catat **Refresh token**
   yang muncul.

### Langkah D — Siapkan folder Drive (kalau belum)

Buka Google Drive (akun Gmail khusus), buat/pakai folder yang sudah ada,
ambil **Folder ID** dari URL-nya (bagian setelah `/folders/`). Folder ini
**tidak perlu di-share ke mana pun** lagi — beda dari Service Account,
karena sekarang yang mengunggah adalah pemilik folder itu sendiri.

### Langkah E — Isi `.env`

```env
GOOGLE_DRIVE_CLIENT_ID="isi dari Langkah B"
GOOGLE_DRIVE_CLIENT_SECRET="isi dari Langkah B"
GOOGLE_DRIVE_REFRESH_TOKEN="isi dari Langkah C"
GOOGLE_DRIVE_FOLDER_ID="isi dari Langkah D"
```

Hapus baris `GOOGLE_DRIVE_SERVICE_ACCOUNT_PATH` yang lama kalau masih ada
di `.env` kamu (sudah tidak dipakai).

### Langkah F — Jalankan

```bash
php artisan optimize:clear
php artisan backup:run
```

Cek folder Google Drive-nya — file `.zip` harus muncul di sana.

## Boleh dihapus (opsional, sudah tidak dipakai)

File kredensial Service Account (kalau masih ada):
```
storage/app/private/google-drive-service-account.json
```
Dan Service Account yang sudah dibuat di Google Cloud Console boleh
dihapus juga (tidak wajib, tidak akan mengganggu apa pun kalau dibiarkan).
