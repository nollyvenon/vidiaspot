<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the provider authentication page.
     *
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider($provider)
    {
        try {
            return Socialite::driver($provider)->redirect();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Provider not configured'], 400);
        }
    }

    /**
     * Obtain the user information from provider.
     *
     * @param string $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleProviderCallback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication failed'], 400);
        }

        // Check if user already exists
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            // Get the default language code from the database
            $defaultLanguage = \App\Models\Language::where('is_default', true)->first();
            $defaultLanguageCode = $defaultLanguage ? $defaultLanguage->code : 'en';

            // Create a new user if not exists
            $user = User::create([
                'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'password' => Hash::make(Str::random(16)), // Generate random password
                'avatar' => $socialUser->getAvatar(),
                'is_verified' => true,
                'language_code' => $defaultLanguageCode, // Use default language from DB
            ]);
        } else {
            // Update user's avatar if it has changed
            if ($socialUser->getAvatar() && $user->avatar !== $socialUser->getAvatar()) {
                $user->update(['avatar' => $socialUser->getAvatar()]);
            }
        }

        // Generate Sanctum token
        $token = $user->createToken('SocialAuth')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * Get available social providers
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProviders()
    {
        return response()->json([
            'providers' => ['google', 'facebook', 'twitter']
        ]);
    }
}
