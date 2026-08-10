<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Signal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SignalController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Signal::with('member:id,name')->orderByDesc('published_at')->orderByDesc('created_at');

        if ($request->filled('search')) {
            $s = $request->string('search')->toString();
            $query->where(fn ($q) => $q->where('pair', 'like', "%{$s}%")->orWhere('author_name', 'like', "%{$s}%"));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $signals = $query->paginate(20)->withQueryString();

        return Inertia::render('admin/signals/Index', [
            'signals' => $signals->through(fn (Signal $s) => $this->row($s)),
            'filters' => [
                'search' => $request->string('search')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
            ],
            'counts' => [
                'all' => Signal::count(),
                'open' => Signal::where('status', 'open')->count(),
                'closed' => Signal::where('status', 'closed')->count(),
                'premium' => Signal::where('is_premium', true)->count(),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('admin/signals/Form', [
            'signal' => null,
            'members' => Member::orderBy('name')->get(['id', 'name']),
            'presetMember' => $request->integer('member') ?: null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Signal::create($this->validateData($request));

        $this->toast('Signal berhasil dibuat.');

        return redirect()->route('admin.signals.index');
    }

    public function edit(Signal $signal): Response
    {
        return Inertia::render('admin/signals/Form', [
            'signal' => [
                ...$signal->only([
                    'id', 'member_id', 'pair', 'market', 'side', 'timeframe', 'leverage',
                    'entry', 'entry_max', 'stop_loss', 'analysis', 'status', 'result',
                    'pnl_percent', 'is_premium',
                ]),
                'take_profits' => $signal->take_profits ?? [],
                'published_at' => $signal->published_at?->format('Y-m-d\TH:i'),
            ],
            'members' => Member::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(Request $request, Signal $signal): RedirectResponse
    {
        $signal->update($this->validateData($request));

        $this->toast('Signal berhasil diperbarui.');

        return redirect()->route('admin.signals.index');
    }

    public function destroy(Signal $signal): RedirectResponse
    {
        $signal->delete();

        $this->toast('Signal dihapus.');

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateData(Request $request): array
    {
        // buang target kosong sebelum divalidasi
        $request->merge([
            'take_profits' => array_values(array_filter(
                (array) $request->input('take_profits', []),
                fn ($v) => $v !== null && $v !== '',
            )),
        ]);

        $data = $request->validate([
            'member_id' => ['nullable', 'exists:members,id'],
            'pair' => ['required', 'string', 'max:40'],
            'market' => ['required', 'in:futures,spot'],
            'side' => ['required', 'in:long,short'],
            'timeframe' => ['nullable', 'string', 'max:20'],
            'leverage' => ['nullable', 'string', 'max:20'],
            'entry' => ['nullable', 'numeric', 'min:0'],
            'entry_max' => ['nullable', 'numeric', 'min:0'],
            'stop_loss' => ['nullable', 'numeric', 'min:0'],
            'take_profits' => ['nullable', 'array'],
            'take_profits.*' => ['numeric', 'min:0'],
            'analysis' => ['nullable', 'string', 'max:4000'],
            'status' => ['required', 'in:open,closed,cancelled'],
            'result' => ['nullable', 'in:running,tp_hit,sl_hit,breakeven'],
            'pnl_percent' => ['nullable', 'numeric'],
            'is_premium' => ['boolean'],
            'published_at' => ['nullable', 'date'],
        ]);

        // nama penulis mengikuti konsultan; null = Tim Lentera
        $data['member_id'] = $data['member_id'] ?? null;
        $data['author_name'] = $data['member_id']
            ? (Member::find($data['member_id'])?->name ?? 'Tim Lentera')
            : 'Tim Lentera';

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function row(Signal $s): array
    {
        return [
            'id' => $s->id,
            'pair' => $s->pair,
            'market' => $s->market,
            'side' => $s->side,
            'timeframe' => $s->timeframe,
            'author' => $s->author_name,
            'status' => $s->status,
            'result' => $s->result,
            'pnl_percent' => $s->pnl_percent !== null ? (float) $s->pnl_percent : null,
            'is_premium' => $s->is_premium,
            'is_published' => $s->isPublished(),
            'published_at' => $s->published_at?->translatedFormat('d M Y H:i'),
        ];
    }
}
