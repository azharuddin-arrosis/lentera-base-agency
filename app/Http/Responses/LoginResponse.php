<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|JsonResponse
    {
        $user = $request->user();

        $target = match (true) {
            $user && $user->isAdmin() => route('dashboard'),
            $user && $user->isTeam() => route('panel.index'),
            default => route('home'), // role 'user'/klien → situs publik
        };

        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect()->intended($target);
    }
}
