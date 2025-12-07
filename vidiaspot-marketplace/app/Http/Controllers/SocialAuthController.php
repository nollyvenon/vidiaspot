<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the provider's authentication page.
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function redirectToProvider(string $provider): RedirectResponse
    {
        $providers = ['google', 'facebook', 'twitter', 'linkedin', 'github', 'apple'];

        if (!in_array($provider, $providers)) {
            return redirect()->route('login')->with('error', 'Invalid social provider');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from the provider.
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function handleProviderCallback(string $provider): RedirectResponse
    {
        $providers = ['google', 'facebook', 'twitter', 'linkedin', 'github', 'apple'];

        if (!in_array($provider, $providers)) {
            return redirect()->route('login')->with('error', 'Invalid social provider');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            // Check if social account already exists
            $existingAccount = SocialAccount::where([
                'provider' => $provider,
                'provider_user_id' => $socialUser->getId(),
            ])->first();

            if ($existingAccount) {
                // Social account exists, log in the user
                Auth::login($existingAccount->user);
                $existingAccount->update([
                    'last_login_at' => now(),
                    'access_token' => $socialUser->token,
                ]);
            } else {
                // Check if user with email exists
                $existingUser = User::where('email', $socialUser->getEmail())->first();

                if ($existingUser) {
                    // Link social account to existing user
                    $existingUser->socialAccounts()->create([
                        'provider' => $provider,
                        'provider_user_id' => $socialUser->getId(),
                        'provider_username' => $socialUser->getNickname(),
                        'email' => $socialUser->getEmail(),
                        'avatar' => $socialUser->getAvatar(),
                        'access_token' => $socialUser->token,
                        'refresh_token' => $socialUser->refreshToken ?? null,
                        'expires_at' => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
                        'last_login_at' => now(),
                    ]);

                    Auth::login($existingUser);
                } else {
                    // Create new user
                    $newUser = User::create([
                        'name' => $socialUser->getName() ?? $socialUser->getNickname(),
                        'email' => $socialUser->getEmail(),
                        'password' => Hash::make(Str::random(16)), // Generate random password
                        'avatar' => $socialUser->getAvatar(),
                        'email_verified_at' => now(), // Social accounts are considered verified
                    ]);

                    // Create social account record
                    $newUser->socialAccounts()->create([
                        'provider' => $provider,
                        'provider_user_id' => $socialUser->getId(),
                        'provider_username' => $socialUser->getNickname(),
                        'email' => $socialUser->getEmail(),
                        'avatar' => $socialUser->getAvatar(),
                        'access_token' => $socialUser->token,
                        'refresh_token' => $socialUser->refreshToken ?? null,
                        'expires_at' => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
                        'last_login_at' => now(),
                    ]);

                    Auth::login($newUser);
                }
            }

            return redirect()->intended('/dashboard')->with('success', 'Logged in successfully via ' . ucfirst($provider));
        } catch (\Exception $e) {
            \Log::error('Social login error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Social login failed. Please try again.');
        }
    }

    /**
     * Get social login settings
     */
    public function getSocialLoginSettings(): \Illuminate\Http\JsonResponse
    {
        $settings = [
            'google_enabled' => !empty(env('GOOGLE_CLIENT_ID')) && !empty(env('GOOGLE_CLIENT_SECRET')),
            'facebook_enabled' => !empty(env('FACEBOOK_CLIENT_ID')) && !empty(env('FACEBOOK_CLIENT_SECRET')),
            'twitter_enabled' => !empty(env('TWITTER_CLIENT_ID')) && !empty(env('TWITTER_CLIENT_SECRET')),
            'linkedin_enabled' => !empty(env('LINKEDIN_CLIENT_ID')) && !empty(env('LINKEDIN_CLIENT_SECRET')),
            'github_enabled' => !empty(env('GITHUB_CLIENT_ID')) && !empty(env('GITHUB_CLIENT_SECRET')),
            'apple_enabled' => !empty(env('APPLE_CLIENT_ID')) && !empty(env('APPLE_CLIENT_SECRET')),
            'available_providers' => $this->getAvailableProviders(),
        ];

        return response()->json([
            'success' => true,
            'settings' => $settings,
        ]);
    }

    /**
     * Get available social login providers
     */
    protected function getAvailableProviders(): array
    {
        $providers = [];

        if (!empty(env('GOOGLE_CLIENT_ID'))) {
            $providers[] = [
                'name' => 'google',
                'display_name' => 'Google',
                'icon' => 'google',
                'enabled' => true,
            ];
        }

        if (!empty(env('FACEBOOK_CLIENT_ID'))) {
            $providers[] = [
                'name' => 'facebook',
                'display_name' => 'Facebook',
                'icon' => 'facebook',
                'enabled' => true,
            ];
        }

        if (!empty(env('TWITTER_CLIENT_ID'))) {
            $providers[] = [
                'name' => 'twitter',
                'display_name' => 'Twitter',
                'icon' => 'twitter',
                'enabled' => true,
            ];
        }

        if (!empty(env('LINKEDIN_CLIENT_ID'))) {
            $providers[] = [
                'name' => 'linkedin',
                'display_name' => 'LinkedIn',
                'icon' => 'linkedin',
                'enabled' => true,
            ];
        }

        if (!empty(env('GITHUB_CLIENT_ID'))) {
            $providers[] = [
                'name' => 'github',
                'display_name' => 'GitHub',
                'icon' => 'github',
                'enabled' => true,
            ];
        }

        if (!empty(env('APPLE_CLIENT_ID'))) {
            $providers[] = [
                'name' => 'apple',
                'display_name' => 'Apple',
                'icon' => 'apple',
                'enabled' => true,
            ];
        }

        return $providers;
    }

    /**
     * Disconnect a social account
     */
    public function disconnectSocialAccount(Request $request, string $provider): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not authenticated',
            ], 401);
        }

        $socialAccount = $user->socialAccounts()->where('provider', $provider)->first();

        if (!$socialAccount) {
            return response()->json([
                'success' => false,
                'error' => 'Social account not found',
            ], 404);
        }

        $socialAccount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Social account disconnected successfully',
        ]);
    }

    /**
     * Get user's linked social accounts
     */
    public function getUserSocialAccounts(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'User not authenticated',
            ], 401);
        }

        $socialAccounts = $user->socialAccounts()->get();

        return response()->json([
            'success' => true,
            'social_accounts' => $socialAccounts->map(function ($account) {
                return [
                    'provider' => $account->provider,
                    'provider_username' => $account->provider_username,
                    'email' => $account->email,
                    'avatar' => $account->avatar,
                    'connected_at' => $account->created_at->toISOString(),
                    'last_login_at' => $account->last_login_at ? $account->last_login_at->toISOString() : null,
                    'is_connected' => true,
                ];
            }),
        ]);
    }

    /**
     * Link additional social account to existing user
     */
    public function linkSocialAccount(Request $request, string $provider): RedirectResponse
    {
        $providers = ['google', 'facebook', 'twitter', 'linkedin', 'github', 'apple'];

        if (!in_array($provider, $providers)) {
            return redirect()->back()->with('error', 'Invalid social provider');
        }

        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to link social accounts');
        }

        // Check if this provider is already linked
        $existingAccount = $user->socialAccounts()->where('provider', $provider)->first();
        if ($existingAccount) {
            return redirect()->back()->with('error', 'This social account is already linked to your account');
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle callback for linking social account
     */
    public function handleLinkCallback(string $provider): RedirectResponse
    {
        $providers = ['google', 'facebook', 'twitter', 'linkedin', 'github', 'apple'];

        if (!in_array($provider, $providers)) {
            return redirect()->route('login')->with('error', 'Invalid social provider');
        }

        try {
            $socialUser = Socialite::driver($provider)->user();
            $user = auth()->user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to link social account');
            }

            // Check if this provider account is already used by another user
            $existingAccount = SocialAccount::where([
                'provider' => $provider,
                'provider_user_id' => $socialUser->getId(),
            ])->first();

            if ($existingAccount) {
                return redirect()->back()->with('error', 'This social account is already linked to another user');
            }

            // Link the account to the current user
            $user->socialAccounts()->create([
                'provider' => $provider,
                'provider_user_id' => $socialUser->getId(),
                'provider_username' => $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'avatar' => $socialUser->getAvatar(),
                'access_token' => $socialUser->token,
                'refresh_token' => $socialUser->refreshToken ?? null,
                'expires_at' => $socialUser->expiresIn ? now()->addSeconds($socialUser->expiresIn) : null,
                'last_login_at' => now(),
            ]);

            return redirect()->route('profile')->with('success', 'Social account linked successfully');
        } catch (\Exception $e) {
            \Log::error('Social account linking error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to link social account. Please try again.');
        }
    }
}