<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class GoogleSocialiteController extends Controller
{
    public function redirectToGoogle()
    {
//        $client = Socialite::driver('google')->stateless()->getOAuthProvider();
//        $client->setScopes(['https://www.googleapis.com/auth/calendar']);
//        $authUrl = $client->createAuthUrl();
//        return redirect()->to($authUrl);
        // redirect user to "login with Google account" page
     //   return Socialite::driver('google')->scopes(['https://www.googleapis.com/auth/calendar.readonly'])->redirect();

        return Socialite::driver('google')->scopes(['https://www.googleapis.com/auth/calendar'])->redirect();
    }

    public function handleCallback()
    {
        try {
            // get user data from Google
            $user = Socialite::driver('google')->user();
            Log::info($user->token);
            // find user in the database where the social id is the same with the id provided by Google
            $finduser = User::where('social_id', $user->id)->first();
            // Store access token in session or database
            Session::put('google_access_token', $user->token);

            if ($finduser)  // if user found then do this
            {
                // Log the user in
                Auth::login($finduser);

                // redirect user to dashboard page
                return redirect('/dashboard');
            }
            else
            {
                // if user not found then this is the first time he/she try to login with Google account
                // create user data with their Google account data
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'social_id' => $user->id,
                    'social_type' => 'google',  // the social login is using google
                    'password' => bcrypt('my-google'),  // fill password by whatever pattern you choose
                ]);
                Log::warning($newUser);
                Auth::login($newUser);

                return redirect('/dashboard');
            }

        }
        catch (Exception $e)
        {
            dd($e->getMessage());
        }
    }
}
