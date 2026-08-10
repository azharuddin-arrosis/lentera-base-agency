<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Member;
use App\Models\Trade;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response|RedirectResponse
    {
        if (auth()->user()->isTeam()) {
            return redirect()->route('panel.index');
        }

        $closed = Trade::closed();
        $totalPnl = (float) $closed->sum('pnl');
        $totalTrades = (clone $closed)->count();
        $winning = (clone $closed)->where('pnl', '>', 0)->count();

        return Inertia::render('admin/Dashboard', [
            'kpi' => [
                'total_pnl' => $totalPnl,
                'total_trades' => $totalTrades,
                'win_rate' => $totalTrades > 0 ? round($winning / $totalTrades * 100, 1) : 0,
                'members' => Member::count(),
                'new_leads' => Lead::where('status', 'new')->count(),
            ],
            'recentTrades' => Trade::with('member:id,name,slug')
                ->orderByDesc('closed_at')
                ->limit(8)
                ->get()
                ->map(fn ($t) => [
                    'id' => $t->id,
                    'member' => $t->member?->name,
                    'symbol' => $t->symbol,
                    'side' => $t->side,
                    'pnl' => (float) $t->pnl,
                    'closed_at' => $t->closed_at?->format('d M Y'),
                ]),
            'recentLeads' => Lead::orderByDesc('created_at')->limit(6)->get()
                ->map(fn ($l) => [
                    'id' => $l->id,
                    'name' => $l->name,
                    'subject' => $l->subject,
                    'status' => $l->status,
                    'created_at' => $l->created_at?->diffForHumans(),
                ]),
        ]);
    }
}
