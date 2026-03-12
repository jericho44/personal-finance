<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class GoogleSocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        // Redirect user to "login with Google account" page
        return Socialite::driver('google')->redirect();
    }

    public function handleCallback()
    {
        try {
            // Get user data from Google
            $googleUser = Socialite::driver('google')->user();
            Log::info('Google user data: '.print_r($googleUser, true));

            // Find user in the database where the social id is the same as the id provided by Google
            $findUser = User::where('social_id', $googleUser->id)->first();

            if ($findUser) {
                // Log the user in
                Auth::login($findUser);

                // Redirect user to dashboard page
                return redirect('/dashboard');
            } else {
                // Check if the email already exists in the system
                $existingUser = User::where('email', $googleUser->email)->first();

                if ($existingUser) {
                    // If email exists but no social_id, update social_id and log in
                    $existingUser->update([
                        'social_id' => $googleUser->id,
                        'social_type' => 'google',
                    ]);
                    Auth::login($existingUser);
                } else {
                    // If this is the first time the user logs in with Google
                    // Create new user with Google account data
                    $newUser = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'social_id' => $googleUser->id,
                        'social_type' => 'google',
                        'password' => bcrypt('1234567890'), // Generate random password
                    ]);

                    Auth::login($newUser);
                }

                return redirect('/dashboard');
            }
        } catch (Exception $e) {
            // Handle exceptions and redirect to the login page with error message
            Log::error('Google Login Error: '.$e->getMessage(), ['stack' => $e->getTraceAsString()]);

            return redirect('/login')->with('error', 'Error during Google login: '.$e->getMessage());
        }
    }
}
