<?php

namespace App\Http\Controllers;

use App\Models\Signal;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SignalController extends Controller
{
    public function index(Request $request): Response
    {
        $this->seo([
            'title' => 'Signal Trading Crypto & Futures — Lentera Project',
            'description' => 'Kumpulan sinyal trading crypto & futures dari tim Lentera Project: entry, stop loss, target, dan analisa. Sebagian premium untuk member.',
        ]);

        $canSeePremium = $this->canSeePremium($request);

        $signals = Signal::published()
            ->with('member') // avatar_url adalah accessor, jadi muat model member utuh
            ->orderByDesc('published_at')
            ->paginate(12);

        return Inertia::render('public/Signals', [
            'signals' => $signals->through(fn (Signal $s) => $this->present($s, $canSeePremium)),
            'canSeePremium' => $canSeePremium,
            'stats' => [
                'total' => Signal::published()->count(),
                'open' => Signal::published()->where('status', 'open')->count(),
                'win' => Signal::published()->where('result', 'tp_hit')->count(),
            ],
        ]);
    }

    public function show(Request $request, Signal $signal): Response
    {
        abort_unless($signal->isPublished(), 404);

        $signal->load('member');
        $canSeePremium = $this->canSeePremium($request);
        $data = $this->present($signal, $canSeePremium);

        $sideLabel = $signal->side === 'long' ? 'LONG' : 'SHORT';
        $this->seo([
            'title' => "Signal {$signal->pair} {$sideLabel} ".($signal->timeframe ? $signal->timeframe.' ' : '').'— Lentera Project',
            'description' => \Illuminate\Support\Str::limit(strip_tags(
                $signal->analysis ?: "Sinyal {$signal->pair} arah {$sideLabel} dari {$signal->author_name} — Lentera Project."
            ), 160),
            'type' => 'article',
            'image' => $signal->member?->avatar_url,
            'schema' => [
                [
                    '@type' => 'Article',
                    'headline' => "Signal {$signal->pair} {$sideLabel}",
                    'datePublished' => $signal->published_at?->toIso8601String(),
                    'dateModified' => $signal->updated_at?->toIso8601String(),
                    'author' => ['@type' => 'Person', 'name' => $signal->author_name],
                    'publisher' => ['@id' => url('/').'#organization'],
                    'mainEntityOfPage' => url('/signals/'.$signal->id),
                ],
                $this->breadcrumb([
                    ['Beranda', url('/')],
                    ['Signal', url('/signals')],
                    [$signal->pair.' '.$sideLabel, url('/signals/'.$signal->id)],
                ]),
            ],
        ]);

        return Inertia::render('public/SignalDetail', [
            'signal' => $data + ['created_at' => $signal->created_at?->translatedFormat('d M Y')],
            'canSeePremium' => $canSeePremium,
        ]);
    }

    /** Konten premium untuk sekarang hanya terbuka bagi admin/konsultan (nanti: member berlangganan). */
    private function canSeePremium(Request $request): bool
    {
        $user = $request->user();

        return $user && ($user->isAdmin() || $user->isTeam());
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Signal $s, bool $canSeePremium): array
    {
        $locked = $s->is_premium && ! $canSeePremium;

        $base = [
            'id' => $s->id,
            'pair' => $s->pair,
            'market' => $s->market,
            'side' => $s->side,
            'timeframe' => $s->timeframe,
            'leverage' => $s->leverage,
            'author' => $s->author_name,
            'author_avatar' => $s->member?->avatar_url,
            'status' => $s->status,
            'result' => $s->result,
            'pnl_percent' => $s->pnl_percent !== null ? (float) $s->pnl_percent : null,
            'is_premium' => $s->is_premium,
            'locked' => $locked,
            'published_at' => $s->published_at?->translatedFormat('d M Y, H:i'),
        ];

        // Detail sensitif hanya untuk sinyal yang tidak terkunci
        if (! $locked) {
            $base += [
                'entry' => $s->entry !== null ? (float) $s->entry : null,
                'entry_max' => $s->entry_max !== null ? (float) $s->entry_max : null,
                'stop_loss' => $s->stop_loss !== null ? (float) $s->stop_loss : null,
                'take_profits' => array_map('floatval', $s->take_profits ?? []),
                'analysis' => $s->analysis,
            ];
        }

        return $base;
    }
}
