<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController; 
use App\Http\Controllers\{
    HomeController,
    EventController,
    ServiceController,
    GalleryController,
    ContactController,
    PaymentController,
};
use App\Filament\Pages\Dashboard;

use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
// Rate limited: max 5 pesan per 10 menit
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:5,10')
    ->name('contact.store');

Route::prefix('events')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('events.index');
    Route::get('/search', [EventController::class, 'search'])->name('events.search');
    Route::get('/{event}', [EventController::class, 'show'])->name('events.show');
});
/*
|--------------------------------------------------------------------------
| Authentication (User & Admin)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
    
    // Tambahkan route untuk registrasi
    Route::get('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'create'])->name('register');    
    // Seharusnya (benar)
    Route::post('/register', [\App\Http\Controllers\Auth\RegisterController::class, 'store'])->name('register.store');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::match(['put', 'patch'], '/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::get('/my-bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/payment', [PaymentController::class, 'index'])->name('payments');

    // Rate limited: max 5 booking per menit per user
    Route::post('/events/{event}/book', [EventController::class, 'book'])
        ->middleware('throttle:5,1')
        ->name('events.book');

    Route::prefix('payment')->group(function () {
        Route::get('/success/{id}', [PaymentController::class, 'success'])->name('payment.success');
        // Rate limited: max 3 konfirmasi pembayaran per menit
        Route::post('/confirm', [PaymentController::class, 'confirm'])
            ->middleware('throttle:3,1')
            ->name('payment.confirm');
        Route::get('/{service}', [PaymentController::class, 'show'])->name('payment.show');
    });
});

Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'email'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'update'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/confirm-password', [\App\Http\Controllers\Auth\PasswordConfirmationController::class, 'create'])->name('password.confirm');
    Route::post('/confirm-password', [\App\Http\Controllers\Auth\PasswordConfirmationController::class, 'store']);

    Route::get('/verify-email', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/verify-email/{id}/{hash}', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('/email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

/*
|--------------------------------------------------------------------------
| Admin (Protected) Routes via Middleware
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    // Route::get('/', fn() => redirect()->route('admin.dashboard'))->name('admin.home');

    // Filament dashboard
    Route::get('/dashboard', [Dashboard::class, 'index'])->name('admin.dashboard');

    Route::get('/payments/{payment}/proof', [PaymentController::class, 'proof'])->name('admin.payments.proof');
});

// add auth.php
