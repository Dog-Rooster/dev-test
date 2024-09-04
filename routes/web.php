<?php

use App\Http\Controllers\ProfileController;
use App\Http\Middleware\CheckGoogleAuth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\GoogleAuthController;
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

Route::get('/events/{event}/calendar', [BookingController::class, 'create'])->name('bookings.create')->middleware('checkGoogleAuth');
Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store')->middleware('checkGoogleAuth');


Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');

Route::get('/oauth2callback', [GoogleAuthController::class, 'handleGoogleCallback']);

Route::get('/debug-env', function () {
    return response()->json([
        'GOOGLE_CLIENT_ID' => env('GOOGLE_CLIENT_ID'),
        'GOOGLE_CLIENT_SECRET' => env('GOOGLE_CLIENT_SECRET'),
        'GOOGLE_REDIRECT_URI' => env('GOOGLE_REDIRECT_URI'),
    ]);
});

require __DIR__ . '/auth.php';
