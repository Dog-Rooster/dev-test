<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    // Redirect the user to the Google OAuth page
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
        ->scopes(['openid', 'email', 'profile', 'https://www.googleapis.com/auth/calendar'])
        ->with(['access_type' => 'offline', 'prompt' => 'consent']) // Request offline access with consent prompt
        ->redirect();
    }

    // Handle the callback from Google OAuth
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Save user data in the session or in the database
            session([
                'google_user_name' => $googleUser->name,
                'google_user_email' => $googleUser->email,
                'google_user_avatar' => $googleUser->avatar
            ]);

            // Here, you can fetch the Google token and store it with the user
            $user = Auth::user(); // Or locate the user based on Google email, etc.
            $user->google_token = $googleUser->token;
            $user->google_refresh_token = $googleUser->refreshToken;
            $user->google_token_expiry = $googleUser->expiresIn;
            $user->save();

            // Redirect the user to the homepage or any other page
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect()->route('auth.google')->with('error', 'Something went wrong with Google OAuth');
        }
    }
}
