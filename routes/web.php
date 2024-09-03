<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\BookingController;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
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

Route::get('/events/{event}/calendar', [BookingController::class, 'create'])->name('bookings.create');
Route::post('/events/{event}/book', [BookingController::class, 'store'])->name('bookings.store');




Route::get('/auth/google', function (GoogleClient $client) {
    $authUrl = $client->createAuthUrl();
    return redirect($authUrl);
});

Route::get('/oauth2callback', function (GoogleClient $client) {
    // Authenticate the user with the authorization code
    $client->authenticate(request()->input('code')); // Use the request helper

    // Get the access token
    $token = $client->getAccessToken();

    // Store the token in the session
    session(['google_token' => $token]);

    return redirect('/')->with('success', 'Google Calendar connected!');
});

Route::get('/calendar', function (GoogleClient $client) {
    // Retrieve the token from the session
    $token = session('google_token');

    if ($token) {
        // Set the access token on the client
        $client->setAccessToken($token);

        // Check if the token is expired and refresh if needed
        if ($client->isAccessTokenExpired()) {
            $client->refreshToken($client->getRefreshToken());
            session(['google_token' => $client->getAccessToken()]);
        }

        // Create the Google Calendar service
        $service = new GoogleCalendar($client);

        // Now you can interact with the Calendar API
        $calendarList = $service->calendarList->listCalendarList();

        return view('calendar.index', ['calendarList' => $calendarList]);
    }

    // If no token is found, redirect to OAuth flow
    return redirect('/auth/google');
});

require __DIR__ . '/auth.php';
