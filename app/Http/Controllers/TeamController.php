<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Member;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function index(): Response
    {
        $this->seo([
            'title' => 'Tim Konsultan & Trader — Lentera Project',
            'description' => 'Kenali tim konsultan dan trader profesional Lentera Project beserta spesialisasi, pengalaman, dan rekam jejaknya.',
        ]);

        $members = Member::query()
            ->where('is_active', true)
            ->with('trades')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('public/Team', [
            'members' => $members->map(fn (Member $m) => [
                'name' => $m->name,
                'slug' => $m->slug,
                'title' => $m->title,
                'tagline' => $m->tagline,
                'specialization' => $m->specialization,
                'experience_years' => $m->experience_years,
                'location' => $m->location,
                'avatar_url' => $m->avatar_url,
                'stats' => $m->statsSummary(),
            ]),
        ]);
    }

    public function show(Member $member): Response
    {
        abort_unless($member->is_active, 404);

        $desc = $member->tagline ?: $member->bio
            ?: ($member->name.' — '.($member->title ?: 'konsultan trading').' di Lentera Project.');

        $this->seo([
            'title' => trim($member->name.' — '.($member->title ?: 'Konsultan Trading')).' | Lentera Project',
            'description' => \Illuminate\Support\Str::limit(strip_tags($desc), 160),
            'type' => 'profile',
            'image' => $member->avatar_url,
            'schema' => [
                [
                    '@type' => 'Person',
                    'name' => $member->name,
                    'jobTitle' => $member->title,
                    'description' => strip_tags((string) $member->bio),
                    'image' => $member->avatar_url,
                    'url' => url('/team/'.$member->slug),
                    'worksFor' => ['@id' => url('/').'#organization'],
                    'knowsAbout' => $member->specialization,
                    'sameAs' => array_values(array_filter([
                        $member->telegram, $member->instagram, $member->twitter, $member->tradingview, $member->website,
                    ])),
                ],
                $this->breadcrumb([
                    ['Beranda', url('/')],
                    ['Tim', url('/team')],
                    [$member->name, url('/team/'.$member->slug)],
                ]),
            ],
        ]);

        $member->load(['trades' => fn ($q) => $q->orderByDesc('closed_at')]);

        $doneBookings = Booking::where('member_id', $member->id)->where('status', 'done');
        $ratedBookings = Booking::where('member_id', $member->id)->where('status', 'done')->whereNotNull('rating');

        return Inertia::render('public/MemberProfile', [
            'member' => [
                'name' => $member->name,
                'slug' => $member->slug,
                'title' => $member->title,
                'tagline' => $member->tagline,
                'bio' => $member->bio,
                'avatar_url' => $member->avatar_url,
                'specialization' => $member->specialization,
                'experience_years' => $member->experience_years,
                'location' => $member->location,
                'joined_at' => $member->joined_at?->translatedFormat('F Y'),
                'socials' => array_filter([
                    'telegram' => $member->telegram,
                    'instagram' => $member->instagram,
                    'twitter' => $member->twitter,
                    'tradingview' => $member->tradingview,
                    'website' => $member->website,
                ]),
            ],
            'stats' => $member->statsSummary(),
            'equity_curve' => $member->equityCurve(),
            'monthly_pnl' => $member->monthlyPnl(6),
            'trades' => $member->trades
                ->where('status', 'closed')
                ->whereNotNull('closed_at')
                ->map(fn ($t) => [
                    'id' => $t->id,
                    'member' => $member->name,
                    'member_slug' => $member->slug,
                    'symbol' => $t->symbol,
                    'side' => $t->side,
                    'market' => $t->market,
                    'leverage' => $t->leverage !== null ? (float) $t->leverage : null,
                    'entry_price' => $t->entry_price,
                    'exit_price' => $t->exit_price,
                    'pnl' => (float) $t->pnl,
                    'roi' => $t->roi !== null ? (float) $t->roi : null,
                    'source' => $t->source,
                    'notes' => $t->notes,
                    'date' => $t->closed_at?->format('Y-m-d'),
                    'closed_at' => $t->closed_at?->format('d M Y'),
                ])->values(),
            'consultation' => [
                'done_count' => (clone $doneBookings)->count(),
                'rating_count' => (clone $ratedBookings)->count(),
                'avg_rating' => round((float) (clone $ratedBookings)->avg('rating'), 1),
            ],
            'reviews' => (clone $ratedBookings)->orderByDesc('date')->limit(12)->get()
                ->map(fn ($b) => [
                    'id' => $b->id,
                    'name' => $b->name,
                    'rating' => (int) $b->rating,
                    'review' => $b->review,
                    'date' => $b->date->translatedFormat('d M Y'),
                ]),
        ]);
    }
}
