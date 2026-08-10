<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeam
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isTeam() && $user->member_id) {
            return $next($request);
        }

        // Admin diarahkan ke dashboard backoffice
        if ($user && $user->isAdmin()) {
            return redirect()->route('dashboard');
        }

        // Role 'user' (mis. member/klien) belum punya panel → arahkan ke situs publik
        return redirect()->route('home');
    }
}
