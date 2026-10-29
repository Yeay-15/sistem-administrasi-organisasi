<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\GuidanceController;
use App\Http\Controllers\IncomingLetterController;
use App\Http\Controllers\OutgoingLetterController;
use App\Http\Controllers\LetterGeneratorController;
use App\Http\Controllers\InventoryItemController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\AspirationController;
use App\Http\Controllers\NonOfficerController;
use App\Http\Controllers\CommitteeController;
use App\Http\Controllers\CommitteeFieldController;

// ============ SITEMAP.XML & ROBOTS.TXT (SEO) ============
// Di luar grup "track.visit" - ini feed untuk mesin pencari, bukan halaman
// yang dikunjungi manusia, jadi tidak perlu ikut tercatat sebagai kunjungan.
// robots.txt sengaja dibuat sebagai ROUTE (bukan file statis di public/)
// supaya baris "Sitemap:" di dalamnya otomatis mengikuti domain yang
// sedang dipakai - tidak perlu diedit manual saat pindah dari domain
// development ke domain production.
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// ============ PORTAL PUBLIK ============
// Dibungkus middleware "track.visit" agar tiap kunjungan halaman publik
// tercatat ringan untuk Statistik Website di Dashboard admin.
Route::middleware('track.visit')->group(function () {
  Route::get('/', [PublicController::class, 'home'])->name('public.home');

  Route::get('/profil', [PublicController::class, 'about'])->name('public.about');
  Route::get('/profil/visi-misi', [PublicController::class, 'vision'])->name('public.about.vision');
  Route::get('/profil/struktur-pengurus', [PublicController::class, 'structure'])->name('public.about.structure');

  Route::get('/agenda-kegiatan', [PublicController::class, 'agenda'])->name('public.agenda.index');

  Route::get('/media/artikel-berita', [PublicController::class, 'news'])->name('public.news.index');
  Route::get('/media/artikel-berita/{post:slug}', [PublicController::class, 'newsShow'])->name('public.news.show');
  Route::get('/media/laporan-kegiatan', [PublicController::class, 'reports'])->name('public.reports.index');
  Route::get('/media/laporan-kegiatan/{post:slug}', [PublicController::class, 'reportShow'])->name('public.reports.show');
  Route::get('/media/galeri', [PublicController::class, 'gallery'])->name('public.gallery');

  Route::get('/kontak-aspirasi', [PublicController::class, 'contact'])->name('public.contact');
  // Formulir ini publik dan sebagian modenya boleh anonim, jadi rawan
  // disasar bot spam - dibatasi maksimal 5 kali kirim per jam per IP.
  Route::post('/kontak-aspirasi', [PublicController::class, 'contactStore'])
    ->middleware('throttle:5,60')->name('public.contact.store');
});

// URL admin berbahasa Indonesia. Nama route (->name / ->names) SENGAJA tidak
// diubah supaya semua route('...') di view & controller tetap berfungsi.
// Verba bawaan resource (create/edit) ikut diindonesiakan di sini.
Route::resourceVerbs(['create' => 'tambah', 'edit' => 'ubah']);

Route::middleware('guest')->group(function () {
  Route::redirect('/login', '/masuk');
  Route::get('/masuk', [AuthController::class, 'showLoginForm'])->name('login');
  // Lapisan pertahanan tambahan di level route (di luar rate limiting
  // per-email di AuthController::login) - membatasi request POST /login
  // secara umum per-IP, supaya request brute force tidak bisa dikirim
  // bertubi-tubi walau mencoba banyak alamat email berbeda.
  Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:20,1');
});

Route::middleware('auth')->group(function () {
  // Pengaturan Akun (ranah privat) - semua pengguna login boleh akses profilnya sendiri,
  // tidak perlu permission khusus.
  Route::get('/pengaturan-akun', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/pengaturan-akun', [ProfileController::class, 'update'])->name('profile.update');
  Route::put('/pengaturan-akun/kata-sandi', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

  Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');

  // URL berbahasa Indonesia (sama seperti halaman publik), tapi nama route
  // tetap 'dashboard' supaya semua route('dashboard') di kode tidak perlu diubah.
  // URL lama /dashboard dialihkan agar bookmark lama tetap jalan.
  Route::redirect('/dashboard', '/dasbor');
  Route::get('/dasbor', [DashboardController::class, 'index'])
    ->name('dashboard')->middleware('can:view_dashboard');

  // Setiap modul di bawah ini memisahkan tiga tingkat hak akses:
  // - "Lihat"  (index/show)                    -> can:view_x
  // - "Kelola" (create/store/edit/update)       -> can:manage_x  (TIDAK termasuk hapus)
  // - "Hapus"  (destroy)                        -> can:delete_x  (toggle terpisah & eksklusif)
  // Ketiganya independen kecuali: manage_x & delete_x sama-sama otomatis
  // meng-imply view_x (lihat fallback di User::hasPermission()).
  Route::resource('divisi', DivisionController::class)->names('divisions')->parameters(['divisi' => 'division'])->only(['index'])->middleware('can:view_divisions');
  Route::resource('divisi', DivisionController::class)->names('divisions')->parameters(['divisi' => 'division'])->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_divisions');
  Route::resource('divisi', DivisionController::class)->names('divisions')->parameters(['divisi' => 'division'])->only(['destroy'])->middleware('can:delete_divisions');

  Route::resource('pengurus', MemberController::class)->names('members')->parameters(['pengurus' => 'member'])->only(['index'])->middleware('can:view_members');
  Route::resource('pengurus', MemberController::class)->names('members')->parameters(['pengurus' => 'member'])->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_members');
  Route::resource('pengurus', MemberController::class)->names('members')->parameters(['pengurus' => 'member'])->only(['destroy'])->middleware('can:delete_members');

  // Statistik individu (histori kepanitiaan + kehadiran) - dipakai bersama
  // oleh menu Pengurus maupun Anggota Non-Pengurus, jadi izinnya cukup
  // salah satu dari kedua hak "Lihat" (dicek lagi di dalam controller).
  Route::get('/pengurus/{member}/statistik', [MemberController::class, 'statistics'])
    ->name('members.statistics');
  Route::patch('/pengurus/{member}/ubah-status-keanggotaan', [MemberController::class, 'toggleMembershipType'])
    ->name('members.toggle-membership')->middleware('can:manage_members');
  Route::get('/pengurus/{member}/angkat-pengurus', [MemberController::class, 'promoteForm'])
    ->name('members.promote-form')->middleware('can:manage_members');

  // Anggota Non-Pengurus: orang yang ikut kegiatan/kepanitiaan tapi bukan
  // bagian struktur Pengurus. Menu & controller sengaja terpisah dari
  // "Pengurus" walau sama-sama tabel members (lihat NonOfficerController).
  Route::resource('anggota-non-pengurus', NonOfficerController::class)->names('non-officers')->parameters(['anggota-non-pengurus' => 'non_officer'])->only(['index'])->middleware('can:view_non_officers');
  Route::resource('anggota-non-pengurus', NonOfficerController::class)->names('non-officers')->parameters(['anggota-non-pengurus' => 'non_officer'])->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_non_officers');
  Route::resource('anggota-non-pengurus', NonOfficerController::class)->names('non-officers')->parameters(['anggota-non-pengurus' => 'non_officer'])->only(['destroy'])->middleware('can:delete_non_officers');

  // Kepanitiaan: histori panitia per event, bisa berisi Pengurus maupun
  // Non-Pengurus, dan bisa dikaitkan ke beberapa agenda (rapat + hari-H).
  // Urutan di bawah ini sengaja: rute statis (/kepanitiaan/tambah) HARUS
  // didaftarkan sebelum rute berwildcard (/kepanitiaan/{committee}) dari
  // 'show' - kalau tidak, Laravel akan mencocokkan "create" sebagai ID
  // kepanitiaan lebih dulu (karena didaftarkan lebih awal) dan berakhir 404.
  Route::resource('kepanitiaan', CommitteeController::class)->names('committees')->parameters(['kepanitiaan' => 'committee'])->only(['index'])->middleware('can:view_committees');
  Route::resource('kepanitiaan', CommitteeController::class)->names('committees')->parameters(['kepanitiaan' => 'committee'])->only(['create', 'store'])->middleware('can:manage_committees');
  Route::resource('kepanitiaan', CommitteeController::class)->names('committees')->parameters(['kepanitiaan' => 'committee'])->only(['show'])->middleware('can:view_committees');
  Route::resource('kepanitiaan', CommitteeController::class)->names('committees')->parameters(['kepanitiaan' => 'committee'])->only(['edit', 'update'])->middleware('can:manage_committees');
  Route::resource('kepanitiaan', CommitteeController::class)->names('committees')->parameters(['kepanitiaan' => 'committee'])->only(['destroy'])->middleware('can:delete_committees');
  Route::post('/kepanitiaan/{committee}/anggota', [CommitteeController::class, 'addMember'])
    ->name('committees.members.store')->middleware('can:manage_committees');
  Route::delete('/kepanitiaan/{committee}/anggota/{committeeMember}', [CommitteeController::class, 'removeMember'])
    ->name('committees.members.destroy')->middleware('can:manage_committees');
  Route::post('/kepanitiaan/{committee}/agenda', [CommitteeController::class, 'attachAgenda'])
    ->name('committees.agenda.store')->middleware('can:manage_committees');
  Route::delete('/kepanitiaan/{committee}/agenda/{agenda}', [CommitteeController::class, 'detachAgenda'])
    ->name('committees.agenda.destroy')->middleware('can:manage_committees');
  Route::post('/kepanitiaan-anggota-baru', [CommitteeController::class, 'quickCreateMember'])
    ->name('committees.members.quick-create')->middleware('can:manage_committees');

  // Master bidang panitia (Sie Acara, Sie Konsumsi, dst) - dikelola dari
  // dalam halaman Kepanitiaan, dipakai berulang lintas kepanitiaan.
  Route::resource('bidang-panitia', CommitteeFieldController::class)->names('committee-fields')->parameters(['bidang-panitia' => 'committee_field'])->only(['index'])->middleware('can:view_committees');
  Route::resource('bidang-panitia', CommitteeFieldController::class)->names('committee-fields')->parameters(['bidang-panitia' => 'committee_field'])->only(['store', 'update'])->middleware('can:manage_committees');
  Route::resource('bidang-panitia', CommitteeFieldController::class)->names('committee-fields')->parameters(['bidang-panitia' => 'committee_field'])->only(['destroy'])->middleware('can:delete_committees');

  // Sama seperti kasus 'kepanitiaan' sebelumnya: rute statis (/agenda/tambah)
  // harus didaftarkan sebelum rute wildcard (/agenda/{agenda}) dari 'show',
  // kalau tidak Laravel mencocokkan "create" sebagai ID agenda lebih dulu
  // (karena show didaftarkan lebih awal) dan berakhir error/404. Ini bug
  // bawaan dari kode aslinya (bukan bagian yang saya tambahkan), tapi
  // sekalian saya perbaiki karena sama-sama di routes/web.php.
  Route::resource('agenda', AgendaController::class)->names('agendas')->parameters(['agenda' => 'agenda'])->only(['index'])->middleware('can:view_agendas');
  Route::resource('agenda', AgendaController::class)->names('agendas')->parameters(['agenda' => 'agenda'])->only(['create', 'store'])->middleware('can:manage_agendas');
  Route::resource('agenda', AgendaController::class)->names('agendas')->parameters(['agenda' => 'agenda'])->only(['show'])->middleware('can:view_agendas');
  Route::resource('agenda', AgendaController::class)->names('agendas')->parameters(['agenda' => 'agenda'])->only(['edit', 'update'])->middleware('can:manage_agendas');
  Route::resource('agenda', AgendaController::class)->names('agendas')->parameters(['agenda' => 'agenda'])->only(['destroy'])->middleware('can:delete_agendas');
  Route::patch('/agenda/{agenda}/ubah-publik', [AgendaController::class, 'togglePublic'])
    ->name('agendas.toggle-public')->middleware('can:manage_agendas');
  Route::post('/agenda/{agenda}/absensi', [AttendanceController::class, 'store'])
    ->name('attendances.store')->middleware('can:manage_attendances');
  Route::get('/agenda-ekspor/pdf', [AgendaController::class, 'exportPdf'])
    ->name('agendas.export-pdf')->middleware('can:view_agendas');

  Route::resource('pembinaan', GuidanceController::class)->names('guidances')->parameters(['pembinaan' => 'guidance'])->only(['index'])->middleware('can:view_guidances');
  Route::resource('pembinaan', GuidanceController::class)->names('guidances')->parameters(['pembinaan' => 'guidance'])->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_guidances');
  Route::resource('pembinaan', GuidanceController::class)->names('guidances')->parameters(['pembinaan' => 'guidance'])->only(['destroy'])->middleware('can:delete_guidances');

  Route::resource('surat-masuk', IncomingLetterController::class)->names('incoming-letters')->parameters(['surat-masuk' => 'incoming_letter'])->only(['index'])->middleware('can:view_incoming_letters');
  Route::resource('surat-masuk', IncomingLetterController::class)->names('incoming-letters')->parameters(['surat-masuk' => 'incoming_letter'])->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_incoming_letters');
  Route::resource('surat-masuk', IncomingLetterController::class)->names('incoming-letters')->parameters(['surat-masuk' => 'incoming_letter'])->only(['destroy'])->middleware('can:delete_incoming_letters');

  // --- Surat Generator (surat dari template .docx + preset kepanitiaan) ---
  // Dibuat sebelum resource surat-keluar. Membuat surat = mengelola surat keluar;
  // mengunduh ulang .docx cukup view_outgoing_letters.
  Route::get('/kepanitiaan/{committee}/surat', [LetterGeneratorController::class, 'pickForCommittee'])
    ->name('committees.letters.index')->middleware('can:manage_outgoing_letters');
  Route::get('/kepanitiaan/{committee}/surat/{letterTemplate}', [LetterGeneratorController::class, 'formForCommittee'])
    ->name('committees.letters.create')->middleware('can:manage_outgoing_letters');
  Route::post('/kepanitiaan/{committee}/surat/{letterTemplate}', [LetterGeneratorController::class, 'storeForCommittee'])
    ->name('committees.letters.store')->middleware('can:manage_outgoing_letters');
  Route::get('/surat-keluar/dari-template', [LetterGeneratorController::class, 'hub'])
    ->name('letter-generator.index')->middleware('can:manage_outgoing_letters');
  Route::get('/surat-keluar/dari-template/{letterTemplate}', [LetterGeneratorController::class, 'formForOrganization'])
    ->name('letter-generator.create')->middleware('can:manage_outgoing_letters');
  Route::post('/surat-keluar/dari-template/{letterTemplate}', [LetterGeneratorController::class, 'storeForOrganization'])
    ->name('letter-generator.store')->middleware('can:manage_outgoing_letters');
  Route::get('/surat-keluar/{outgoing_letter}/unduh-docx', [LetterGeneratorController::class, 'download'])
    ->name('outgoing-letters.download-docx')->middleware('can:view_outgoing_letters');
  Route::get('/surat-keluar/{outgoing_letter}/unduh-pdf', [LetterGeneratorController::class, 'downloadPdf'])
    ->name('outgoing-letters.download-pdf')->middleware('can:view_outgoing_letters');

  Route::resource('surat-keluar', OutgoingLetterController::class)->names('outgoing-letters')->parameters(['surat-keluar' => 'outgoing_letter'])->only(['index'])->middleware('can:view_outgoing_letters');
  Route::resource('surat-keluar', OutgoingLetterController::class)->names('outgoing-letters')->parameters(['surat-keluar' => 'outgoing_letter'])->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_outgoing_letters');
  Route::resource('surat-keluar', OutgoingLetterController::class)->names('outgoing-letters')->parameters(['surat-keluar' => 'outgoing_letter'])->only(['destroy'])->middleware('can:delete_outgoing_letters');

  // Inventaris: data barang organisasi (jumlah, tempat, masa kedaluwarsa).
  // Mengikuti pola 3-tier yang sama; semua permission-nya muncul otomatis di
  // halaman Peran & Akses (grup "Inventaris") dan bisa diberikan per Role
  // maupun per Divisi.
  // Impor/template Excel (butuh hak Kelola Inventaris karena menambah/mengubah data).
  Route::get('inventaris/impor', [InventoryItemController::class, 'importForm'])->name('inventory-items.import')->middleware('can:manage_inventory_items');
  Route::post('inventaris/impor', [InventoryItemController::class, 'import'])->name('inventory-items.import.store')->middleware('can:manage_inventory_items');
  Route::get('inventaris/template', [InventoryItemController::class, 'template'])->name('inventory-items.template')->middleware('can:manage_inventory_items');
  Route::resource('inventaris', InventoryItemController::class)->names('inventory-items')->parameters(['inventaris' => 'inventory_item'])->only(['index'])->middleware('can:view_inventory_items');
  Route::resource('inventaris', InventoryItemController::class)->names('inventory-items')->parameters(['inventaris' => 'inventory_item'])->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_inventory_items');
  Route::resource('inventaris', InventoryItemController::class)->names('inventory-items')->parameters(['inventaris' => 'inventory_item'])->only(['destroy'])->middleware('can:delete_inventory_items');

  Route::get('/rekap-absensi', [AttendanceReportController::class, 'index'])
    ->name('attendance-reports.index')->middleware('can:manage_attendances');

  Route::resource('buku-tamu', GuestController::class)->names('guests')->parameters(['buku-tamu' => 'guest'])->only(['index'])->middleware('can:view_guests');
  Route::resource('buku-tamu', GuestController::class)->names('guests')->parameters(['buku-tamu' => 'guest'])->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_guests');
  Route::resource('buku-tamu', GuestController::class)->names('guests')->parameters(['buku-tamu' => 'guest'])->only(['destroy'])->middleware('can:delete_guests');

  // Modul CMS: mengikuti pola 3-tier yang sama. Siapa yang boleh mengakses
  // ditentukan lewat Role ATAU Division (lihat AppServiceProvider & User::hasPermission()).
  Route::resource('berita', PostController::class)->names('posts')->parameters(['berita' => 'post'])->only(['index'])->middleware('can:view_news');
  Route::resource('berita', PostController::class)->names('posts')->parameters(['berita' => 'post'])->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_news');
  Route::resource('berita', PostController::class)->names('posts')->parameters(['berita' => 'post'])->only(['destroy'])->middleware('can:delete_news');
  // Endpoint upload gambar inline dari editor Quill (lihat PostController::uploadContentImage).
  // Gambar disimpan ke storage & yang masuk ke kolom `content` cuma URL-nya,
  // BUKAN base64 mentah seperti perilaku default Quill.
  Route::post('berita/unggah-gambar-konten', [PostController::class, 'uploadContentImage'])
    ->name('posts.upload-content-image')
    ->middleware('can:manage_news');

  Route::resource('galeri', GalleryController::class)->names('galleries')->parameters(['galeri' => 'gallery'])->only(['index'])->middleware('can:view_gallery');
  Route::resource('galeri', GalleryController::class)->names('galleries')->parameters(['galeri' => 'gallery'])->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_gallery');
  Route::resource('galeri', GalleryController::class)->names('galleries')->parameters(['galeri' => 'gallery'])->only(['destroy'])->middleware('can:delete_gallery');

  // Pengaturan Beranda (Hero & Sambutan Ketua Umum) - singleton, tidak perlu resource penuh.
  Route::get('/pengaturan-beranda', [SettingController::class, 'edit'])
    ->name('settings.edit')->middleware('can:manage_settings');
  Route::put('/pengaturan-beranda', [SettingController::class, 'update'])
    ->name('settings.update')->middleware('can:manage_settings');

  Route::resource('prestasi', AchievementController::class)->names('achievements')->parameters(['prestasi' => 'achievement'])->only(['index'])->middleware('can:view_achievements');
  Route::resource('prestasi', AchievementController::class)->names('achievements')->parameters(['prestasi' => 'achievement'])->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_achievements');
  Route::resource('prestasi', AchievementController::class)->names('achievements')->parameters(['prestasi' => 'achievement'])->only(['destroy'])->middleware('can:delete_achievements');

  // Harus di atas resource 'leaders' supaya 'visibility' tidak dianggap {leader}.
  Route::patch('/estafet-kepemimpinan/tampilan', [LeaderController::class, 'updateVisibility'])
    ->name('leaders.visibility')->middleware('can:manage_leaders');

  Route::resource('estafet-kepemimpinan', LeaderController::class)->names('leaders')->parameters(['estafet-kepemimpinan' => 'leader'])->only(['index'])->middleware('can:view_leaders');
  Route::resource('estafet-kepemimpinan', LeaderController::class)->names('leaders')->parameters(['estafet-kepemimpinan' => 'leader'])->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_leaders');
  Route::resource('estafet-kepemimpinan', LeaderController::class)->names('leaders')->parameters(['estafet-kepemimpinan' => 'leader'])->only(['destroy'])->middleware('can:delete_leaders');

  Route::get('/aspirasi', [AspirationController::class, 'index'])
    ->name('aspirations.index')->middleware('can:view_aspirations');
  Route::patch('/aspirasi/{aspiration}/tandai-dibaca', [AspirationController::class, 'markAsRead'])
    ->name('aspirations.mark-as-read')->middleware('can:view_aspirations');
  Route::delete('/aspirasi/{aspiration}', [AspirationController::class, 'destroy'])
    ->name('aspirations.destroy')->middleware('can:delete_aspirations');

  Route::get('/riwayat-aktivitas', [AuditLogController::class, 'index'])
    ->name('audit-logs.index')->middleware('can:view_audit_logs');

  Route::prefix('peran-akses')->name('roles-management.')->middleware('can:manage_roles')->group(function () {
    Route::get('/', [RoleManagementController::class, 'index'])->name('index');
    Route::put('/peran/{role}/akses/{feature}', [RoleManagementController::class, 'updateRoleAccess'])
      ->name('update-role-access');
    Route::put('/divisi/{division}/akses/{feature}', [RoleManagementController::class, 'updateDivisionAccess'])
      ->name('update-division-access');
    Route::post('/divisi/{division}/salin-akses', [RoleManagementController::class, 'copyDivisionAccess'])
      ->name('copy-division-access');
    Route::post('/pengguna', [RoleManagementController::class, 'storeUser'])->name('users.store');
    Route::patch('/pengguna/{user}', [RoleManagementController::class, 'updateUser'])->name('update-user');
    Route::delete('/pengguna/{user}', [RoleManagementController::class, 'destroyUser'])->name('users.destroy');
    Route::patch('/pengguna/{user}/atur-ulang-sandi', [RoleManagementController::class, 'resetPassword'])->name('reset-password');
  });

  // Khusus Super Admin - dicek eksplisit di dalam controller (bukan lewat
  // sistem permission role/divisi biasa), karena berisi seluruh data
  // organisasi termasuk hash password akun.
  Route::prefix('cadangan-data')->name('backups.')->group(function () {
    Route::get('/', [BackupController::class, 'index'])->name('index');
    Route::post('/', [BackupController::class, 'store'])->name('store');
    Route::get('/{filename}/unduh', [BackupController::class, 'download'])->name('download');
    Route::delete('/{filename}', [BackupController::class, 'destroy'])->name('destroy');
  });
});
