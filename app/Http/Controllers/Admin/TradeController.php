<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Trade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TradeController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Trade::with('member:id,name,slug')->orderByDesc('closed_at');

        if ($request->filled('member')) {
            $query->where('member_id', $request->integer('member'));
        }
        if ($request->filled('search')) {
            $query->where('symbol', 'like', '%'.$request->string('search').'%');
        }

        $trades = $query->paginate(20)->withQueryString();

        return Inertia::render('admin/trades/Index', [
            'trades' => $trades->through(fn ($t) => [
                'id' => $t->id,
                'member' => $t->member?->name,
                'member_id' => $t->member_id,
                'symbol' => $t->symbol,
                'side' => $t->side,
                'market' => $t->market,
                'leverage' => $t->leverage,
                'pnl' => (float) $t->pnl,
                'roi' => $t->roi !== null ? (float) $t->roi : null,
                'status' => $t->status,
                'source' => $t->source,
                'closed_at' => $t->closed_at?->format('d M Y'),
            ]),
            'members' => Member::orderBy('name')->get(['id', 'name']),
            'filters' => [
                'member' => $request->integer('member') ?: null,
                'search' => $request->string('search')->toString() ?: null,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/trades/Form', [
            'trade' => null,
            'members' => Member::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Trade::create($this->validateData($request));

        $this->toast('Trade / PnL berhasil ditambahkan.');

        return redirect()->route('admin.trades.index');
    }

    public function edit(Trade $trade): Response
    {
        return Inertia::render('admin/trades/Form', [
            'trade' => [
                ...$trade->only([
                    'id', 'member_id', 'symbol', 'market', 'side', 'leverage',
                    'entry_price', 'exit_price', 'quantity', 'pnl', 'roi', 'fee',
                    'status', 'source', 'notes',
                ]),
                'opened_at' => $trade->opened_at?->format('Y-m-d\TH:i'),
                'closed_at' => $trade->closed_at?->format('Y-m-d\TH:i'),
            ],
            'members' => Member::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Trade $trade): RedirectResponse
    {
        $trade->update($this->validateData($request));

        $this->toast('Trade / PnL berhasil diperbarui.');

        return redirect()->route('admin.trades.index');
    }

    public function destroy(Trade $trade): RedirectResponse
    {
        $trade->delete();

        $this->toast('Trade dihapus.');

        return back();
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'symbol' => ['required', 'string', 'max:40'],
            'market' => ['required', 'in:futures,spot'],
            'side' => ['required', 'in:long,short'],
            'leverage' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'entry_price' => ['nullable', 'numeric', 'min:0'],
            'exit_price' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'pnl' => ['required', 'numeric'],
            'roi' => ['nullable', 'numeric'],
            'fee' => ['nullable', 'numeric'],
            'status' => ['required', 'in:open,closed'],
            'source' => ['required', 'in:manual,binance'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'opened_at' => ['nullable', 'date'],
            'closed_at' => ['nullable', 'date'],
        ]);
    }
}
