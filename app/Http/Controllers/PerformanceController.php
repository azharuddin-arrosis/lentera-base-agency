<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Trade;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PerformanceController extends Controller
{
    public function index(Request $request): Response
    {
        $this->seo([
            'title' => 'Performa & Track Record PnL — Lentera Project',
            'description' => 'Rekam jejak trading transparan: equity curve, win rate, dan PnL bulanan tiap konsultan Lentera Project.',
        ]);

        $memberFilter = $request->query('member');

        $members = Member::where('is_active', true)->orderBy('sort_order')->get();

        $query = Trade::closed()->with('member:id,name,slug');
        if ($memberFilter) {
            $member = $members->firstWhere('slug', $memberFilter);
            if ($member) {
                $query->where('member_id', $member->id);
            }
        }

        $totalPnl = (float) (clone $query)->sum('pnl');
        $totalTrades = (clone $query)->count();
        $winning = (clone $query)->where('pnl', '>', 0)->count();
        $winRate = $totalTrades > 0 ? round($winning / $totalTrades * 100, 1) : 0;
        $bestTrade = (float) (clone $query)->max('pnl');

        // equity curve gabungan (urut waktu)
        $sorted = (clone $query)->orderBy('closed_at')->get();
        $cum = 0;
        $equity = [['label' => 'Start', 'value' => 0]];
        foreach ($sorted as $t) {
            $cum += (float) $t->pnl;
            $equity[] = ['label' => $t->closed_at?->format('d M'), 'value' => round($cum, 2)];
        }

        // pnl bulanan gabungan (6 bulan)
        $monthly = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');
            $sum = $sorted->filter(fn ($t) => $t->closed_at?->format('Y-m') === $key)->sum('pnl');
            $monthly[] = ['label' => $month->translatedFormat('M Y'), 'value' => round((float) $sum, 2)];
        }

        return Inertia::render('public/Performance', [
            'members' => $members->map(fn ($m) => ['name' => $m->name, 'slug' => $m->slug]),
            'activeMember' => $memberFilter,
            'stats' => [
                'total_pnl' => $totalPnl,
                'total_trades' => $totalTrades,
                'win_rate' => $winRate,
                'best_trade' => $bestTrade,
            ],
            'equity_curve' => $equity,
            'monthly_pnl' => $monthly,
            'calendar_trades' => $sorted->map(fn ($t) => [
                'id' => $t->id,
                'member' => $t->member?->name,
                'member_slug' => $t->member?->slug,
                'symbol' => $t->symbol,
                'side' => $t->side,
                'leverage' => $t->leverage !== null ? (float) $t->leverage : null,
                'pnl' => (float) $t->pnl,
                'roi' => $t->roi !== null ? (float) $t->roi : null,
                'date' => $t->closed_at?->format('Y-m-d'),
                'closed_at' => $t->closed_at?->format('d M Y'),
            ])->values(),
        ]);
    }
}
