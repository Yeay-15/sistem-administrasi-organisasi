<div align="center">

# 🎓 Sistem Administrasi & Absensi Terpadu KATIBER

**Aplikasi web terpadu untuk pengelolaan SDM, absensi kegiatan, dan digitalisasi administrasi surat-menyurat**
dikembangkan untuk penggunaan pribadi di lingkungan organisasi **Keluarga Mahasiswa Tebing Tinggi Bersatu (KATIBER)** — Lhokseumawe, Aceh Utara.

<br/>

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=black)
![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white)

![License](https://img.shields.io/badge/License-Internal%20Use-lightgrey?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-Active%20Development-brightgreen?style=for-the-badge)
![Platform](https://img.shields.io/badge/Platform-Web-informational?style=for-the-badge)

<p><i>Dibangun untuk penggunaan lokal/internal organisasi — fokus pada kecepatan, keamanan data,<br/>dan kemudahan pembuatan Lembar Pertanggungjawaban (LPJ).</i></p>

</div>

---

## 📑 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#️-tech-stack--library)
- [Struktur Direktori](#-struktur-direktori-penting)
- [Panduan Instalasi](#-panduan-instalasi-local-development)
- [Kredensial Default](#-kredensial-default-login)

---

## ✨ Fitur Utama

Sistem ini terbagi menjadi dua pilar fungsional utama:

<table>
<tr>
<td width="50%" valign="top">

### 👥 Pilar SDM & Absensi

- 🗂️ **Manajemen Divisi** — Pengelolaan struktur divisi organisasi
- 📋 **Manajemen Pengurus** — Pendataan pengurus lengkap dengan riwayat jabatan & status aktif
- 🗓️ **Manajemen Agenda** — Pencatatan agenda rapat, pleno, dan kegiatan rutin
- ✅ **Absensi Masal (Upsert)** — Pencatatan kehadiran (Hadir, Izin, Sakit, Alpha) dalam satu halaman
- ⚠️ **Modul Pembinaan** — Rekam jejak pendisiplinan pengurus (Teguran, SP)
- 📊 **Rekap Absensi (LPJ)** — Matriks kehadiran otomatis, ekspor ke PDF & Excel

</td>
<td width="50%" valign="top">

### ✉️ Pilar Administrasi Surat

- 📥 **Surat Masuk** — Pencatatan surat eksternal + unggah arsip (PDF)
- 📤 **Surat Keluar** — Penomoran & pencatatan surat internal/eksternal
- 🔍 **Register Surat (LPJ)** — Filter pencarian cerdas, ekspor ke PDF & Excel
- 🛡️ **Audit Log** — Pencatatan otomatis riwayat aktivitas sistem untuk keamanan data

</td>
</tr>
</table>

---

## 🛠️ Tech Stack & Library

| Layer | Teknologi |
|---|---|
| **Backend** | PHP 8.3 & [Laravel 13](https://laravel.com/) |
| **Frontend** | Blade Templating, [Tailwind CSS v4](https://tailwindcss.com/) (via Vite), [Alpine.js](https://alpinejs.dev/) |
| **Database** | MySQL (Relational Database) |
| **Export Excel** | `maatwebsite/excel` |
| **Export PDF** | `barryvdh/laravel-dompdf` |

---

## 📂 Struktur Direktori Penting

```text
katiber-admin/
├── app/
│   ├── Exports/            # Class untuk format file Excel/PDF (mis. AttendanceReportExport.php)
│   ├── Http/
│   │   ├── Controllers/    # Logika CRUD, filter, dan pemrosesan data sebelum dikirim ke View
│   │   └── Middleware/     # Filter request masuk (mis. autentikasi login)
│   └── Models/             # Representasi tabel database & relasi (Eloquent ORM)
│
├── database/
│   ├── migrations/         # Arsitektur tabel database (skema, tipe data, foreign key)
│   └── seeders/             # Script pengisi data awal (akun Admin)
│
├── public/
│   └── storage/            # Symlink ke folder storage untuk arsip surat (PDF)
│
├── resources/
│   ├── css/app.css         # Entry point Tailwind CSS v4
│   └── views/               # Seluruh tampilan antarmuka (UI)
│       ├── agendas/         # View CRUD Agenda & Input Absensi
│       ├── audit_logs/      # View Riwayat Aktivitas
│       ├── exports/         # Template khusus konversi PDF & Excel
│       ├── layouts/         # Kerangka utama UI (Navbar, interaksi Alpine.js)
│       └── ...               # Modul lainnya
│
├── routes/
│   └── web.php              # Pemetaan URL ke Controller yang sesuai
│
└── .env                      # Konfigurasi environment (database, nama aplikasi)
```

---

## 🚀 Panduan Instalasi (Local Development)

> **Persyaratan Sistem:** PHP ≥ 8.3 · Composer · Node.js & NPM · MySQL (XAMPP/Laragon/sejenisnya)

<details open>
<summary><b>1️⃣ Clone atau Ekstrak Repository</b></summary>

```bash
git clone <url-repository-anda> katiber-admin
cd katiber-admin
```
</details>

<details open>
<summary><b>2️⃣ Install Dependensi PHP & Node.js</b></summary>

```bash
composer install
npm install
```
</details>

<details open>
<summary><b>3️⃣ Konfigurasi Environment</b></summary>

Salin file `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Buka file `.env` dan sesuaikan koneksi database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=katiber_admin
DB_USERNAME=root
DB_PASSWORD=
```
</details>

<details open>
<summary><b>4️⃣ Generate Application Key</b></summary>

```bash
php artisan key:generate
```
</details>

<details open>
<summary><b>5️⃣ Migrasi Database & Seeding</b></summary>

Buat database kosong bernama `katiber_admin` di MySQL Anda, lalu jalankan:

```bash
php artisan migrate:fresh --seed
```
</details>

<details open>
<summary><b>6️⃣ Tautkan Folder Storage (Untuk File Arsip)</b></summary>

```bash
php artisan storage:link
```
</details>

<details open>
<summary><b>7️⃣ Jalankan Aplikasi</b></summary>

Buka dua tab terminal:

**Terminal 1** — Menjalankan server backend:
```bash
php artisan serve
```

**Terminal 2** — Men-compile asset Tailwind CSS secara realtime:
```bash
npm run dev
```
</details>

<details open>
<summary><b>8️⃣ Akses Aplikasi</b></summary>

Buka browser dan akses:

```
http://localhost:8000
```
</details>

---

## 🔑 Kredensial Default (Login)

Gunakan akun berikut untuk masuk ke dalam sistem pertama kali:

| Field | Value |
|---|---|
| **Email** | `admin@katiber.local` |
| **Password** | `rahasia123` |

> ⚠️ **Catatan Keamanan:** Segera ganti password ini jika aplikasi di-deploy ke server publik.

---

<div align="center">

Dikembangkan untuk kebutuhan internal **KATIBER** — Keluarga Mahasiswa Tebing Tinggi Bersatu

</div>
