# Fitur "Event Unggulan" — Panduan Instalasi & Pemakaian

> **Update:** redirect setelah "Buat Event Baru" sekarang kembali ke
> halaman daftar (bukan langsung ke form edit); skema warna hero/pita
> pengumuman/timeline disesuaikan jadi navy-emas mengikuti identitas
> visual situs; dan pengisian bagan turnamen diubah dari otomatis
> (seeding) menjadi manual — pasangan babak pertama dipilih sendiri oleh
> admin sesuai hasil undian federasi.

Fitur ini menambahkan modul baru untuk menyorot acara besar (mis. KATIBER Cup)
di beranda & pita pengumuman, lengkap dengan halaman detail (microsite) yang
punya info acara, timeline "Info Terkini", dan bagan turnamen interaktif.

## 1. Instalasi

```bash
composer dump-autoload
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan storage:link   # jika belum pernah dijalankan sebelumnya
```

`RolePermissionSeeder` aman dijalankan ulang di database yang sudah ada —
seeder ini hanya melakukan `updateOrCreate` per permission (cek isi file
untuk memastikan, sesuaikan jika di server Anda seeder ditulis dengan cara
lain). Role **Super Admin** otomatis mendapat semua permission baru
(`view_events`, `manage_events`, `delete_events`); untuk role lain, atur
lewat menu **Manajemen Role**.

## 2. Cara pakai (alur singkat)

1. Buka menu **Event Unggulan** (ikon bintang) di sidebar admin.
2. Klik **Buat Event Baru** → isi judul, tanggal, poster, deskripsi.
   - Centang **"Event ini berbentuk turnamen dengan bagan pertandingan"**
     jika event-nya kompetisi (mis. futsal), lalu pilih jumlah tim
     (8/16/32/64). Opsi ini terkunci setelah event disimpan.
   - Set **Status** ke **Aktif** agar event bisa mulai ditampilkan ke publik.
3. Nyalakan switch **Beranda** dan/atau **Pengumuman** di kartu event pada
   halaman daftar — ini yang mengontrol tampil/tidaknya highlight di
   beranda & pita pengumuman. Mati-hidupnya independen satu sama lain.
4. Jika event punya bagan: klik ikon 🏆 **Kelola Bagan** → tambahkan tim
   satu per satu → setelah jumlah tim pas, klik **Buat Bagan**. Sistem
   membuat kerangka bagan kosong (babak 1 sampai final) sesuai jumlah tim;
   pasangan pertandingan babak pertama **diisi manual** oleh Anda lewat
   dropdown di tiap kartu pertandingan (mengikuti hasil undian resmi dari
   federasi/panitia, bukan diacak otomatis oleh sistem).
5. Selama turnamen berjalan, buka halaman Kelola Bagan untuk input skor
   tiap pertandingan. Begitu status diubah ke **Selesai**, pemenang
   otomatis maju ke babak berikutnya — tidak perlu diisi manual satu per
   satu naik ke atas bagan.
6. Gunakan menu 📣 **Info Terkini** untuk memposting pengumuman berjalan
   (technical meeting, perubahan jadwal, dsb.) — ini tampil sebagai
   timeline di halaman publik.
7. Setelah acara selesai, ubah **Status** event ke **Arsip** dari form
   edit. Highlight di beranda otomatis hilang, tapi halaman detail +
   bagan + timeline tetap bisa diakses publik sebagai rekam jejak, dan
   tetap muncul di daftar arsip di `/event`.

## 3. Dipakai berulang untuk event lain

Karena semuanya berbasis satu tabel `featured_events` dengan status
draft/aktif/arsip, Anda bisa membuat event baru kapan pun (webinar,
pelantikan pengurus, dsb.) tanpa perlu ubah kode apa pun — cukup ulangi
langkah 2–3 di atas. Fitur bagan (langkah 4–5) bersifat opsional, jadi
event non-kompetisi cukup lewati saja.

## 4. Berkas yang ditambahkan/diubah

**Migration baru:** `featured_events`, `event_teams`, `event_matches`, `event_updates`

**Model baru:** `FeaturedEvent`, `EventTeam`, `EventMatch`, `EventUpdate`

**Controller baru:** `EventController`, `EventBracketController`,
`EventMatchController`, `EventUpdateController`

**Diubah:** `PublicController` (tambah method `events()` & `eventShow()`),
`routes/web.php`, `RolePermissionSeeder`, `AppServiceProvider` (composer
pita pengumuman), `layouts/app.blade.php` & `layouts/partials/nav-item.blade.php`
(menu sidebar), `layouts/public.blade.php` (pita pengumuman),
`public/home.blade.php` (hero banner)

**View baru:** seluruh isi folder `resources/views/events/` dan
`resources/views/public/events/`

## 5. Yang belum dibuat (opsional, bisa dikembangkan lagi nanti)

- Notifikasi WhatsApp/email otomatis saat ada Info Terkini baru
- Statistik ringkas (top scorer, dsb.) — saat ini fokus hanya skor & pemenang
- Upload banner terpisah untuk mobile vs desktop (saat ini satu poster dipakai untuk semua ukuran layar)
