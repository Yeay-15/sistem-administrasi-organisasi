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

// ============ SITEMAP.XML & ROBOTS.TXT (SEO) ============
// Di luar grup "track.visit" — ini feed untuk mesin pencari, bukan halaman
// yang dikunjungi manusia, jadi tidak perlu ikut tercatat sebagai kunjungan.
// robots.txt sengaja dibuat sebagai ROUTE (bukan file statis di public/)
// supaya baris "Sitemap:" di dalamnya otomatis mengikuti domain yang
// sedang dipakai — tidak perlu diedit manual saat pindah dari domain
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
    // disasar bot spam — dibatasi maksimal 5 kali kirim per jam per IP.
    Route::post('/kontak-aspirasi', [PublicController::class, 'contactStore'])
        ->middleware('throttle:5,60')->name('public.contact.store');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    // Lapisan pertahanan tambahan di level route (di luar rate limiting
    // per-email di AuthController::login) — membatasi request POST /login
    // secara umum per-IP, supaya request brute force tidak bisa dikirim
    // bertubi-tubi walau mencoba banyak alamat email berbeda.
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,1');
});

Route::middleware('auth')->group(function () {
    // Pengaturan Akun (ranah privat) — semua pengguna login boleh akses profilnya sendiri,
    // tidak perlu permission khusus.
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')->middleware('can:view_dashboard');

    // Setiap modul di bawah ini memisahkan tiga tingkat hak akses:
    // - "Lihat"  (index/show)                    -> can:view_x
    // - "Kelola" (create/store/edit/update)       -> can:manage_x  (TIDAK termasuk hapus)
    // - "Hapus"  (destroy)                        -> can:delete_x  (toggle terpisah & eksklusif)
    // Ketiganya independen kecuali: manage_x & delete_x sama-sama otomatis
    // meng-imply view_x (lihat fallback di User::hasPermission()).
    Route::resource('divisions', DivisionController::class)->only(['index'])->middleware('can:view_divisions');
    Route::resource('divisions', DivisionController::class)->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_divisions');
    Route::resource('divisions', DivisionController::class)->only(['destroy'])->middleware('can:delete_divisions');

    Route::resource('members', MemberController::class)->only(['index'])->middleware('can:view_members');
    Route::resource('members', MemberController::class)->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_members');
    Route::resource('members', MemberController::class)->only(['destroy'])->middleware('can:delete_members');

    Route::resource('agendas', AgendaController::class)->only(['index', 'show'])->middleware('can:view_agendas');
    Route::resource('agendas', AgendaController::class)->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_agendas');
    Route::resource('agendas', AgendaController::class)->only(['destroy'])->middleware('can:delete_agendas');
    Route::patch('/agendas/{agenda}/toggle-public', [AgendaController::class, 'togglePublic'])
        ->name('agendas.toggle-public')->middleware('can:manage_agendas');
    Route::post('/agendas/{agenda}/attendances', [AttendanceController::class, 'store'])
        ->name('attendances.store')->middleware('can:manage_attendances');
    Route::get('/agendas-export/pdf', [AgendaController::class, 'exportPdf'])
        ->name('agendas.export-pdf')->middleware('can:view_agendas');

    Route::resource('guidances', GuidanceController::class)->only(['index'])->middleware('can:view_guidances');
    Route::resource('guidances', GuidanceController::class)->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_guidances');
    Route::resource('guidances', GuidanceController::class)->only(['destroy'])->middleware('can:delete_guidances');

    Route::resource('incoming-letters', IncomingLetterController::class)->only(['index'])->middleware('can:view_incoming_letters');
    Route::resource('incoming-letters', IncomingLetterController::class)->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_incoming_letters');
    Route::resource('incoming-letters', IncomingLetterController::class)->only(['destroy'])->middleware('can:delete_incoming_letters');

    Route::resource('outgoing-letters', OutgoingLetterController::class)->only(['index'])->middleware('can:view_outgoing_letters');
    Route::resource('outgoing-letters', OutgoingLetterController::class)->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_outgoing_letters');
    Route::resource('outgoing-letters', OutgoingLetterController::class)->only(['destroy'])->middleware('can:delete_outgoing_letters');

    Route::get('/attendance-reports', [AttendanceReportController::class, 'index'])
        ->name('attendance-reports.index')->middleware('can:manage_attendances');

    Route::resource('guests', GuestController::class)->only(['index'])->middleware('can:view_guests');
    Route::resource('guests', GuestController::class)->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_guests');
    Route::resource('guests', GuestController::class)->only(['destroy'])->middleware('can:delete_guests');

    // Modul CMS: mengikuti pola 3-tier yang sama. Siapa yang boleh mengakses
    // ditentukan lewat Role ATAU Division (lihat AppServiceProvider & User::hasPermission()).
    Route::resource('posts', PostController::class)->only(['index'])->middleware('can:view_news');
    Route::resource('posts', PostController::class)->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_news');
    Route::resource('posts', PostController::class)->only(['destroy'])->middleware('can:delete_news');

    Route::resource('galleries', GalleryController::class)->only(['index'])->middleware('can:view_gallery');
    Route::resource('galleries', GalleryController::class)->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_gallery');
    Route::resource('galleries', GalleryController::class)->only(['destroy'])->middleware('can:delete_gallery');

    // Pengaturan Beranda (Hero & Sambutan Ketua Umum) — singleton, tidak perlu resource penuh.
    Route::get('/pengaturan-beranda', [SettingController::class, 'edit'])
        ->name('settings.edit')->middleware('can:manage_settings');
    Route::put('/pengaturan-beranda', [SettingController::class, 'update'])
        ->name('settings.update')->middleware('can:manage_settings');

    Route::resource('achievements', AchievementController::class)->only(['index'])->middleware('can:view_achievements');
    Route::resource('achievements', AchievementController::class)->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_achievements');
    Route::resource('achievements', AchievementController::class)->only(['destroy'])->middleware('can:delete_achievements');

    Route::resource('leaders', LeaderController::class)->only(['index'])->middleware('can:view_leaders');
    Route::resource('leaders', LeaderController::class)->only(['create', 'store', 'edit', 'update'])->middleware('can:manage_leaders');
    Route::resource('leaders', LeaderController::class)->only(['destroy'])->middleware('can:delete_leaders');

    Route::get('/aspirasi', [AspirationController::class, 'index'])
        ->name('aspirations.index')->middleware('can:view_aspirations');
    Route::patch('/aspirasi/{aspiration}/tandai-dibaca', [AspirationController::class, 'markAsRead'])
        ->name('aspirations.mark-as-read')->middleware('can:view_aspirations');
    Route::delete('/aspirasi/{aspiration}', [AspirationController::class, 'destroy'])
        ->name('aspirations.destroy')->middleware('can:delete_aspirations');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index')->middleware('can:view_audit_logs');

    Route::prefix('roles-management')->name('roles-management.')->middleware('can:manage_roles')->group(function () {
        Route::get('/', [RoleManagementController::class, 'index'])->name('index');
        Route::patch('/permissions/{role}/{permission}/toggle', [RoleManagementController::class, 'togglePermission'])
            ->name('toggle-permission');
        Route::patch('/division-permissions/{division}/{permission}/toggle', [RoleManagementController::class, 'toggleDivisionPermission'])
            ->name('toggle-division-permission');
        Route::post('/users', [RoleManagementController::class, 'storeUser'])->name('users.store');
        Route::patch('/users/{user}', [RoleManagementController::class, 'updateUser'])->name('update-user');
        Route::delete('/users/{user}', [RoleManagementController::class, 'destroyUser'])->name('users.destroy');
        Route::patch('/users/{user}/reset-password', [RoleManagementController::class, 'resetPassword'])->name('reset-password');
    });

    // Khusus Super Admin — dicek eksplisit di dalam controller (bukan lewat
    // sistem permission role/divisi biasa), karena berisi seluruh data
    // organisasi termasuk hash password akun.
    Route::prefix('cadangan-data')->name('backups.')->group(function () {
        Route::get('/', [BackupController::class, 'index'])->name('index');
        Route::post('/', [BackupController::class, 'store'])->name('store');
        Route::get('/{filename}/unduh', [BackupController::class, 'download'])->name('download');
        Route::delete('/{filename}', [BackupController::class, 'destroy'])->name('destroy');
    });
});
