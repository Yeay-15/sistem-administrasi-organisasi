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

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')->middleware('can:view_dashboard');

    Route::resource('divisions', DivisionController::class)->middleware('can:manage_divisions');
    Route::resource('members', MemberController::class)->middleware('can:manage_members');

    Route::resource('agendas', AgendaController::class)->middleware('can:manage_agendas');
    Route::post('/agendas/{agenda}/attendances', [AttendanceController::class, 'store'])
        ->name('attendances.store')->middleware('can:manage_attendances');

    Route::resource('guidances', GuidanceController::class)->middleware('can:manage_guidances');

    Route::resource('incoming-letters', IncomingLetterController::class)->middleware('can:manage_incoming_letters');
    Route::resource('outgoing-letters', OutgoingLetterController::class)->middleware('can:manage_outgoing_letters');

    Route::get('/attendance-reports', [AttendanceReportController::class, 'index'])
        ->name('attendance-reports.index')->middleware('can:manage_attendances');

    Route::resource('guests', GuestController::class)->except(['show'])->middleware('can:manage_guests');

    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->name('audit-logs.index')->middleware('can:view_audit_logs');

    Route::prefix('roles-management')->name('roles-management.')->middleware('can:manage_roles')->group(function () {
        Route::get('/', [RoleManagementController::class, 'index'])->name('index');
        Route::patch('/permissions/{role}/{permission}/toggle', [RoleManagementController::class, 'togglePermission'])
            ->name('toggle-permission');
        Route::patch('/users/{user}', [RoleManagementController::class, 'updateUser'])->name('update-user');
    });
});
