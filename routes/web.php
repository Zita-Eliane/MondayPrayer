<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FastController;
use App\Http\Controllers\LeaderController;
use App\Http\Controllers\PrayerSessionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/dashboard', [DashBoardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    // ── Jeûnes ────────────────────────────────────────────────────
    Route::delete('fasts/bulk-destroy', [FastController::class, 'bulkDestroy'])->name('fasts.bulk-destroy');
    Route::resource('fasts', FastController::class)->except(['show']);

    // ── Leaders (personnes pour qui on jeûne) ─────────────────────
    Route::resource('leaders', LeaderController::class)->except(['show']);

    // ── Sessions de prière ────────────────────────────────────────
    Route::get('/prayers', [PrayerSessionController::class, 'index'])->name('prayers.index');
    Route::get('/prayers/create', [PrayerSessionController::class, 'create'])->name('prayers.create');
    Route::post('/prayers', [PrayerSessionController::class, 'store'])->name('prayers.store');
    Route::get('/prayers/{prayer}', [PrayerSessionController::class, 'show'])->name('prayers.show');
    Route::post('/prayers/{prayer}/stop', [PrayerSessionController::class, 'stop'])->name('prayers.stop');
    Route::post('/prayers/{prayer}/counter', [PrayerSessionController::class, 'updateCounter'])->name('prayers.counter');
    Route::post('/prayers/{prayer}/pause', [PrayerSessionController::class, 'pause'])->name('prayers.pause');
    Route::post('/prayers/{prayer}/resume', [PrayerSessionController::class, 'resume'])->name('prayers.resume');
    Route::get('/prayers/{prayer}/edit', [PrayerSessionController::class, 'edit'])->name('prayers.edit');
    Route::put('/prayers/{prayer}', [PrayerSessionController::class, 'update'])->name('prayers.update');
    Route::delete('/prayers/{prayer}', [PrayerSessionController::class, 'destroy'])->name('prayers.destroy');

    // ── Statistiques ──────────────────────────────────────────────
    Route::get('/statistics', [StatisticsController::class, 'index'])->name('statistics.index');

    // ── Profil ────────────────────────────────────────────────────
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ── Préférences jeûne (nouveau) ───────────────────────────────
    Route::patch('/profile/fasting', [ProfileController::class, 'updateFasting'])->name('profile.fasting.update');

    // ── Notifications ─────────────────────────────────────────────
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

    // ── Administration (rôle admin requis) ───────────────────────
    Route::prefix('admin')->name('admin.')->middleware(\App\Http\Middleware\AdminMiddleware::class)->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::patch('/users/{user}/role', [AdminController::class, 'updateRole'])->name('users.role');
        Route::post('/users/{user}/remind', [AdminController::class, 'sendManualReminder'])->name('remind');
        Route::post('/bulk-reminders', [AdminController::class, 'sendBulkReminders'])->name('bulk-reminders');
        Route::get('/statistics', [AdminController::class, 'statistics'])->name('statistics');
        
    });

});

require __DIR__ . '/auth.php';
