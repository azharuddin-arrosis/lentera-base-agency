<?php

use App\Http\Controllers\Admin\BinanceController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinancialPlanController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BookingStatusController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SignalController as AdminSignalController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TradeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PerformanceController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\SignalController;
use App\Http\Controllers\TeamPanelController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/team', [TeamController::class, 'index'])->name('team.index');
Route::get('/team/{member:slug}', [TeamController::class, 'show'])->name('team.show');
Route::get('/avatar/{member}', [\App\Http\Controllers\AvatarController::class, 'show'])->name('avatar');
Route::get('/performance', [PerformanceController::class, 'index'])->name('performance.index');
Route::get('/signals', [SignalController::class, 'index'])->name('signals.index');
Route::get('/signals/{signal}', [SignalController::class, 'show'])->name('signals.show');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Jadwalkan konsultasi (kalender)
Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule.index');
Route::post('/schedule', [ScheduleController::class, 'store'])->name('schedule.store');

// Halaman status booking milik klien (link privat via token) —
// klien mengunggah bukti bayar & memberi ulasan dari sini
Route::get('/booking/{booking:token}', [BookingStatusController::class, 'show'])->name('booking.status');
Route::post('/booking/{booking:token}/proof', [BookingStatusController::class, 'uploadProof'])->name('booking.proof');
Route::post('/booking/{booking:token}/review', [BookingStatusController::class, 'review'])->name('booking.review');

// SEO
Route::get('/robots.txt', [SeoController::class, 'robots']);
Route::get('/sitemap.xml', [SeoController::class, 'sitemap']);

// Google OAuth login
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

/*
|--------------------------------------------------------------------------
| Area admin (perlu login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard = ringkasan backoffice → khusus admin
    Route::get('dashboard', [DashboardController::class, 'index'])->middleware('admin')->name('dashboard');

    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::post('members/{member}/toggle-active', [MemberController::class, 'toggleActive'])->name('members.toggle-active');
        Route::resource('members', MemberController::class);
        Route::resource('trades', TradeController::class)->except('show');
        Route::resource('signals', AdminSignalController::class)->except('show');
        Route::resource('services', ServiceController::class)->except('show');
        Route::resource('testimonials', TestimonialController::class)->except('show');

        // Financial Plan — rekap trading: total WD (input manual) + PnL harian (otomatis)
        Route::get('financial-plan', [FinancialPlanController::class, 'index'])->name('financial-plan.index');
        Route::post('financial-plan/withdrawals', [FinancialPlanController::class, 'store'])->name('financial-plan.store');
        Route::put('financial-plan/withdrawals/{withdrawal}', [FinancialPlanController::class, 'update'])->name('financial-plan.update');
        Route::delete('financial-plan/withdrawals/{withdrawal}', [FinancialPlanController::class, 'destroy'])->name('financial-plan.destroy');

        Route::get('bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::patch('bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
        Route::post('bookings/{booking}/paid', [BookingController::class, 'togglePaid'])->name('bookings.paid');
        Route::post('bookings/{booking}/reschedule', [BookingController::class, 'reschedule'])->name('bookings.reschedule');
        Route::delete('bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
        Route::post('schedule-blocks', [BookingController::class, 'storeBlock'])->name('blocks.store');
        Route::delete('schedule-blocks/{block}', [BookingController::class, 'destroyBlock'])->name('blocks.destroy');

        Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
        Route::patch('leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
        Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');

        Route::get('binance', [BinanceController::class, 'index'])->name('binance.index');
        Route::post('binance/test', [BinanceController::class, 'test'])->name('binance.test');
        Route::post('binance/sync', [BinanceController::class, 'sync'])->name('binance.sync');
    });
});

/*
|--------------------------------------------------------------------------
| Panel Team (role: team) — atur ketersediaan & lihat konsultasi sendiri
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'team'])->prefix('panel')->name('panel.')->group(function () {
    Route::get('/', [TeamPanelController::class, 'index'])->name('index');
    Route::post('blocks', [TeamPanelController::class, 'storeBlock'])->name('blocks.store');
    Route::delete('blocks/{block}', [TeamPanelController::class, 'destroyBlock'])->name('blocks.destroy');
});

require __DIR__.'/settings.php';
