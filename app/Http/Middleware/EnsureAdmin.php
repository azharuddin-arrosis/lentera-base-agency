<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        // Konsultan diarahkan ke panelnya sendiri
        if ($user && $user->isTeam()) {
            return redirect()->route('panel.index');
        }

        // Role 'user' (mis. member/klien) belum punya backoffice → arahkan ke situs publik
        return redirect()->route('home');
    }
}
