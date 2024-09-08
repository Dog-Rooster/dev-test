<?php

use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/', [EventController::class, 'index'])->name('events.index');

Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');

Route::get('/events/{event}/calendar', [BookingController::class, 'create'])->middleware('google.auth')->name('bookings.create');
Route::post('/events/book', [BookingController::class, 'store'])->name('bookings.store');

Route::get('/google/auth', [GoogleAuthController::class, 'createAuthUrl'])->name('google.auth');

Route::get('/google/auth/callback', [GoogleAuthController::class, 'handleAuthCallback']);

require __DIR__ . '/auth.php';
