<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Trade;
use App\Models\Withdrawal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FinancialPlanController extends Controller
{
    public function index(Request $request): Response
    {
        $this->seo(['title' => 'Financial Plan — Rekap Trading & Withdrawal']);

        $members = Member::orderBy('sort_order')->orderBy('name')->get(['id', 'name']);
        $memberId = $request->integer('member') ?: null;

        $tradeQuery = fn () => Trade::closed()
            ->when($memberId, fn ($q) => $q->where('member_id', $memberId))
            ->whereNotNull('closed_at');

        $wdQuery = fn () => Withdrawal::query()
            ->when($memberId, fn ($q) => $q->where('member_id', $memberId));

        $totalPnl = (float) $tradeQuery()->sum('pnl');
        $totalWithdrawn = (float) $wdQuery()->sum('amount');

        // ── Rekap PnL harian (otomatis dari trade closed) ──
        $daily = $tradeQuery()
            ->get(['pnl', 'closed_at'])
            ->groupBy(fn ($t) => $t->closed_at->format('Y-m-d'))
            ->map(fn ($group, $date) => [
                'date' => $date,
                'label' => \Illuminate\Support\Carbon::parse($date)->translatedFormat('D, d M Y'),
                'pnl' => round((float) $group->sum('pnl'), 2),
                'trades' => $group->count(),
            ])
            ->sortByDesc('date')
            ->values();

        $profitDays = $daily->where('pnl', '>', 0)->count();
        $tradingDays = $daily->count();

        // ── Riwayat withdrawal (paginated) ──
        $withdrawals = $wdQuery()
            ->with('member:id,name')
            ->orderByDesc('withdrawn_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('admin/financial-plan/Index', [
            'members' => $members,
            'filters' => ['member' => $memberId],
            'summary' => [
                'total_pnl' => round($totalPnl, 2),
                'total_withdrawn' => round($totalWithdrawn, 2),
                'remaining' => round($totalPnl - $totalWithdrawn, 2), // profit yang belum ditarik
                'trading_days' => $tradingDays,
                'profit_days' => $profitDays,
                'best_day' => $daily->max('pnl') ?? 0,
                'worst_day' => $daily->min('pnl') ?? 0,
                'avg_daily' => $tradingDays > 0 ? round($totalPnl / $tradingDays, 2) : 0,
                'withdraw_count' => $wdQuery()->count(),
            ],
            'daily' => $daily,
            'withdrawals' => $withdrawals->through(fn (Withdrawal $w) => [
                'id' => $w->id,
                'member_id' => $w->member_id,
                'member' => $w->member?->name,
                'amount' => (float) $w->amount,
                'withdrawn_at' => $w->withdrawn_at?->format('Y-m-d'),
                'withdrawn_label' => $w->withdrawn_at?->translatedFormat('d M Y'),
                'method' => $w->method,
                'note' => $w->note,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Withdrawal::create($this->validateData($request));

        $this->toast('Withdrawal berhasil dicatat.');

        return redirect()->route('admin.financial-plan.index');
    }

    public function update(Request $request, Withdrawal $withdrawal): RedirectResponse
    {
        $withdrawal->update($this->validateData($request));

        $this->toast('Withdrawal berhasil diperbarui.');

        return redirect()->route('admin.financial-plan.index');
    }

    public function destroy(Withdrawal $withdrawal): RedirectResponse
    {
        $withdrawal->delete();

        $this->toast('Withdrawal dihapus.');

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateData(Request $request): array
    {
        return $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'withdrawn_at' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
