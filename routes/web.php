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
use App\Http\Controllers\PostController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\PublicController;

Route::get('/', [PublicController::class, 'home'])->name('public.home');
Route::get('/tentang-kami', [PublicController::class, 'about'])->name('public.about');
Route::get('/berita', [PublicController::class, 'news'])->name('public.news.index');
Route::get('/berita/{post:slug}', [PublicController::class, 'newsShow'])->name('public.news.show');
Route::get('/galeri', [PublicController::class, 'gallery'])->name('public.gallery');
Route::get('/kontak', [PublicController::class, 'contact'])->name('public.contact');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')->middleware('can:view_dashboard');

    // Setiap modul di bawah ini memisahkan hak "Lihat" (index/show) dari hak
    // "Kelola" (create/store/edit/update/destroy). Pengguna dengan 'manage_x'
    // otomatis lolos juga di 'view_x' berkat fallback di User::hasPermission(),
    // jadi tidak perlu menyalakan dua-duanya secara manual.
    Route::resource('divisions', DivisionController::class)->only(['index'])->middleware('can:view_divisions');
    Route::resource('divisions', DivisionController::class)->except(['index'])->middleware('can:manage_divisions');

    Route::resource('members', MemberController::class)->only(['index'])->middleware('can:view_members');
    Route::resource('members', MemberController::class)->except(['index'])->middleware('can:manage_members');

    Route::resource('agendas', AgendaController::class)->only(['index', 'show'])->middleware('can:view_agendas');
    Route::resource('agendas', AgendaController::class)->except(['index', 'show'])->middleware('can:manage_agendas');
    Route::post('/agendas/{agenda}/attendances', [AttendanceController::class, 'store'])
        ->name('attendances.store')->middleware('can:manage_attendances');

    Route::resource('guidances', GuidanceController::class)->only(['index'])->middleware('can:view_guidances');
    Route::resource('guidances', GuidanceController::class)->except(['index'])->middleware('can:manage_guidances');

    Route::resource('incoming-letters', IncomingLetterController::class)->only(['index'])->middleware('can:view_incoming_letters');
    Route::resource('incoming-letters', IncomingLetterController::class)->except(['index'])->middleware('can:manage_incoming_letters');

    Route::resource('outgoing-letters', OutgoingLetterController::class)->only(['index'])->middleware('can:view_outgoing_letters');
    Route::resource('outgoing-letters', OutgoingLetterController::class)->except(['index'])->middleware('can:manage_outgoing_letters');

    Route::get('/attendance-reports', [AttendanceReportController::class, 'index'])
        ->name('attendance-reports.index')->middleware('can:manage_attendances');

    Route::resource('guests', GuestController::class)->only(['index'])->middleware('can:view_guests');
    Route::resource('guests', GuestController::class)->except(['index', 'show'])->middleware('can:manage_guests');

    // Modul CMS: hanya Super Admin & pengurus Divisi/Role yang punya izin ini (lihat AppServiceProvider).
    Route::resource('posts', PostController::class)->except(['show'])->middleware('can:manage_news');
    Route::resource('galleries', GalleryController::class)->except(['show'])->middleware('can:manage_gallery');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index')->middleware('can:view_audit_logs');

    Route::prefix('roles-management')->name('roles-management.')->middleware('can:manage_roles')->group(function () {
        Route::get('/', [RoleManagementController::class, 'index'])->name('index');
        Route::patch('/permissions/{role}/{permission}/toggle', [RoleManagementController::class, 'togglePermission'])
            ->name('toggle-permission');
        Route::patch('/division-permissions/{division}/{permission}/toggle', [RoleManagementController::class, 'toggleDivisionPermission'])
            ->name('toggle-division-permission');
        Route::patch('/users/{user}', [RoleManagementController::class, 'updateUser'])->name('update-user');
    });
});
