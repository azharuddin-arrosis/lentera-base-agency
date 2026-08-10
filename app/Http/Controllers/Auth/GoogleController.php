<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        if (! config('services.google.client_id')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Login Google belum dikonfigurasi. Isi GOOGLE_CLIENT_ID & GOOGLE_CLIENT_SECRET di .env.',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal login dengan Google. Silakan coba lagi.',
            ]);
        }

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Pengguna Google',
                'email_verified_at' => now(),
            ]
        );

        // Beri password acak untuk akun baru (login via Google saja)
        if (! $user->password) {
            $user->forceFill(['password' => bcrypt(Str::random(32))])->save();
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
