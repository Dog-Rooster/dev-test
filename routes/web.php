<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Auth\GoogleSocialiteController;


//Route::get('/', function () {
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

Route::get('auth/google', [GoogleSocialiteController::class, 'redirectToGoogle']);  // redirect to google login
Route::get('callback/google', [GoogleSocialiteController::class, 'handleCallback']);    // callback route after google account chosen


Route::get('/', [EventController::class, 'index'])->name('events');

Route::get('/bookings', [BookingController::class, 'index'])->name('bookings');

Route::get('/events/{event}/calendar', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store');



require __DIR__ . '/auth.php';
