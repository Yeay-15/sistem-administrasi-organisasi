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

// Jika user belum login, halaman utama diarahkan ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected Routes (Hanya bisa diakses jika sudah login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('divisions', DivisionController::class);
    Route::resource('members', MemberController::class);

    Route::resource('agendas', AgendaController::class);
    Route::post('/agendas/{agenda}/attendances', [AttendanceController::class, 'store'])->name('attendances.store');

    Route::resource('guidances', GuidanceController::class);

    Route::resource('incoming-letters', IncomingLetterController::class);
    Route::resource('outgoing-letters', OutgoingLetterController::class);

    Route::get('/attendance-reports', [AttendanceReportController::class, 'index'])->name('attendance-reports.index');

    Route::resource('guests', GuestController::class)->except(['show']);

    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
});
