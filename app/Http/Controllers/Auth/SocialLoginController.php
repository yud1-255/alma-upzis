<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redirect to OAuth provider consent screen.
     *
     * @param string $provider
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle callback from OAuth provider.
     *
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleProviderCallback(string $provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan ' . ucfirst($provider) . '. Silakan coba lagi.');
        }

        if (empty($socialUser->getEmail())) {
            return redirect()->route('login')->with('error', 'Akun ' . ucfirst($provider) . ' Anda tidak memiliki email. Silakan gunakan metode login lain.');
        }

        $user = $this->findOrCreateUser($socialUser, $provider);

        Auth::login($user, true);

        return redirect('/dashboard');
    }

    /**
     * Find existing user by email and link, or create new user.
     *
     * @param \Laravel\Socialite\Contracts\User $socialUser
     * @param string $provider
     * @return User
     */
    private function findOrCreateUser($socialUser, string $provider): User
    {
        $user = User::where('email', $socialUser->getEmail())->first();

        if ($user) {
            $updateData = [
                'social_id' => $socialUser->getId(),
                'social_type' => $provider,
            ];

            // If existing user is unverified but provider confirms email, verify them
            if (!$user->email_verified_at) {
                $emailVerified = $this->isEmailVerifiedByProvider($socialUser, $provider);
                if ($emailVerified) {
                    $updateData['email_verified_at'] = now();
                }
            }

            $user->update($updateData);

            return $user;
        }

        // Create new user
        $emailVerifiedAt = $this->isEmailVerifiedByProvider($socialUser, $provider) ? now() : null;

        return User::create([
            'name' => $socialUser->getName(),
            'email' => $socialUser->getEmail(),
            'password' => Hash::make(Str::random(32)),
            'social_id' => $socialUser->getId(),
            'social_type' => $provider,
            'email_verified_at' => $emailVerifiedAt,
        ]);
    }

    /**
     * Check if the OAuth provider has verified the user's email.
     *
     * @param \Laravel\Socialite\Contracts\User $socialUser
     * @param string $provider
     * @return bool
     */
    private function isEmailVerifiedByProvider($socialUser, string $provider): bool
    {
        if ($provider === 'google') {
            $rawUser = $socialUser->getRaw();
            return !empty($rawUser['email_verified']);
        }

        // Facebook doesn't always return email_verified; trust if email is provided
        if ($provider === 'facebook') {
            return true;
        }

        return false;
    }
}
