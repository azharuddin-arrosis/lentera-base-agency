<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Trade;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $this->seo([
            'title' => 'Lentera Project — Konsultan Trading, Sinyal Crypto & Futures',
            'description' => 'Konsultan trading profesional Indonesia: sinyal crypto & futures, mentoring 1-on-1, '
                .'dan analisis pasar berbasis manajemen risiko dengan rekam jejak PnL transparan.',
        ]);

        $members = Member::query()
            ->where('is_active', true)
            ->withCount(['trades as closed_trades_count' => fn ($q) => $q->where('status', 'closed')])
            ->with('trades')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get();

        // statistik agregat seluruh tim
        $closed = Trade::closed();
        $totalPnl = (float) $closed->sum('pnl');
        $totalTrades = (clone $closed)->count();
        $winning = (clone $closed)->where('pnl', '>', 0)->count();
        $winRate = $totalTrades > 0 ? round($winning / $totalTrades * 100, 1) : 0;

        return Inertia::render('public/Home', [
            'stats' => [
                'total_pnl' => $totalPnl,
                'total_trades' => $totalTrades,
                'win_rate' => $winRate,
                'members' => $members->count(),
            ],
            'members' => $members->map(fn (Member $m) => [
                'name' => $m->name,
                'slug' => $m->slug,
                'title' => $m->title,
                'specialization' => $m->specialization,
                'avatar_url' => $m->avatar_url,
                'stats' => $m->statsSummary(),
            ]),
            'services' => Service::where('is_active', true)->orderBy('sort_order')->get(),
            'testimonials' => Testimonial::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
