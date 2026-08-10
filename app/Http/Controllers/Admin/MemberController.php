<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Member;
use App\Models\ScheduleBlock;
use App\Models\Signal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MemberController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Member::with('trades')->orderBy('sort_order');

        if ($request->filled('search')) {
            $s = $request->string('search')->toString();
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$s}%")
                ->orWhere('title', 'like', "%{$s}%")
                ->orWhere('specialization', 'like', "%{$s}%"));
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->toString() === 'active');
        }
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $members = $query->paginate(12)->withQueryString();

        return Inertia::render('admin/members/Index', [
            'members' => $members->through(fn (Member $m) => [
                'id' => $m->id,
                'name' => $m->name,
                'slug' => $m->slug,
                'title' => $m->title,
                'specialization' => $m->specialization,
                'avatar_url' => $m->avatar_url,
                'is_active' => $m->is_active,
                'is_featured' => $m->is_featured,
                'stats' => $m->statsSummary(),
            ]),
            'filters' => [
                'search' => $request->string('search')->toString() ?: null,
                'status' => $request->string('status')->toString() ?: null,
                'featured' => $request->boolean('featured') ?: null,
            ],
        ]);
    }

    public function show(Member $member): Response
    {
        $member->load('trades');

        return Inertia::render('admin/members/Show', [
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'slug' => $member->slug,
                'title' => $member->title,
                'tagline' => $member->tagline,
                'bio' => $member->bio,
                'avatar_url' => $member->avatar_url,
                'specialization' => $member->specialization,
                'experience_years' => $member->experience_years,
                'location' => $member->location,
                'joined_at' => $member->joined_at?->translatedFormat('d F Y'),
                'consultation_rate' => (float) $member->consultation_rate,
                'is_active' => $member->is_active,
                'is_featured' => $member->is_featured,
                'socials' => array_filter([
                    'telegram' => $member->telegram,
                    'instagram' => $member->instagram,
                    'twitter' => $member->twitter,
                    'tradingview' => $member->tradingview,
                    'website' => $member->website,
                ]),
            ],
            'stats' => $member->statsSummary(),
            'blocks' => ScheduleBlock::where('member_id', $member->id)
                ->whereDate('date', '>=', today())
                ->orderBy('date')
                ->get()
                ->map(fn ($blk) => [
                    'id' => $blk->id,
                    'date' => $blk->date->translatedFormat('l, d M Y'),
                    'date_raw' => $blk->date->format('Y-m-d'),
                    'times' => $blk->times,
                    'reason' => $blk->reason,
                ]),
            'consultations' => Booking::where('member_id', $member->id)
                ->orderByDesc('date')->orderByDesc('created_at')->limit(50)->get()
                ->map(fn ($b) => [
                    'id' => $b->id,
                    'name' => $b->name,
                    'email' => $b->email,
                    'phone' => $b->phone,
                    'date' => $b->date->translatedFormat('D, d M Y'),
                    'date_raw' => $b->date->format('Y-m-d'),
                    'times' => $b->times,
                    'hours' => $b->hours,
                    'total_price' => (float) $b->total_price,
                    'status' => $b->status,
                    'payment_status' => $b->payment_status,
                    'is_upcoming' => ! ($b->date->isPast() && ! $b->date->isToday()),
                ]),
            'consultStats' => [
                'total' => Booking::where('member_id', $member->id)->where('status', '!=', 'cancelled')->count(),
                'upcoming' => Booking::where('member_id', $member->id)->whereIn('status', ['pending', 'confirmed'])->whereDate('date', '>=', today())->count(),
                'done' => Booking::where('member_id', $member->id)->where('status', 'done')->count(),
                'hours' => (int) Booking::where('member_id', $member->id)->where('status', '!=', 'cancelled')->sum('hours'),
                'earnings' => (float) Booking::where('member_id', $member->id)->where('payment_status', 'paid')->sum('total_price'),
                'unpaid' => (float) Booking::where('member_id', $member->id)->where('payment_status', 'unpaid')->where('status', '!=', 'cancelled')->sum('total_price'),
            ],
            'signals' => Signal::where('member_id', $member->id)
                ->orderByDesc('published_at')->orderByDesc('created_at')->limit(20)->get()
                ->map(fn (Signal $s) => [
                    'id' => $s->id,
                    'pair' => $s->pair,
                    'market' => $s->market,
                    'side' => $s->side,
                    'timeframe' => $s->timeframe,
                    'status' => $s->status,
                    'result' => $s->result,
                    'pnl_percent' => $s->pnl_percent !== null ? (float) $s->pnl_percent : null,
                    'is_premium' => $s->is_premium,
                    'is_published' => $s->isPublished(),
                    'published_at' => $s->published_at?->translatedFormat('d M Y'),
                ]),
            'signalStats' => [
                'total' => Signal::where('member_id', $member->id)->count(),
                'premium' => Signal::where('member_id', $member->id)->where('is_premium', true)->count(),
                'free' => Signal::where('member_id', $member->id)->where('is_premium', false)->count(),
            ],
        ]);
    }

    public function toggleActive(Member $member): RedirectResponse
    {
        $member->update(['is_active' => ! $member->is_active]);
        $this->toast($member->is_active ? 'Team diaktifkan.' : 'Team dinonaktifkan (tidak tampil di web).');

        return back();
    }

    public function create(): Response
    {
        return Inertia::render('admin/members/Form', ['member' => null]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        Member::create($data);

        $this->toast('Member berhasil ditambahkan.');

        return redirect()->route('admin.members.index');
    }

    public function edit(Member $member): Response
    {
        return Inertia::render('admin/members/Form', [
            'member' => $member->only([
                'id', 'name', 'slug', 'title', 'tagline', 'bio', 'avatar', 'specialization',
                'experience_years', 'location', 'joined_at', 'consultation_rate', 'telegram', 'instagram',
                'twitter', 'tradingview', 'website', 'is_active', 'is_featured', 'sort_order',
            ]),
        ]);
    }

    public function update(Request $request, Member $member): RedirectResponse
    {
        $data = $this->validateData($request, $member);
        $member->update($data);

        $this->toast('Member berhasil diperbarui.');

        return redirect()->route('admin.members.index');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $member->delete();

        $this->toast('Member dihapus.');

        return back();
    }

    protected function validateData(Request $request, ?Member $member = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'title' => ['nullable', 'string', 'max:120'],
            'tagline' => ['nullable', 'string', 'max:180'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:120'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:80'],
            'location' => ['nullable', 'string', 'max:120'],
            'joined_at' => ['nullable', 'date'],
            'consultation_rate' => ['nullable', 'numeric', 'min:0'],
            'telegram' => ['nullable', 'string', 'max:180'],
            'instagram' => ['nullable', 'string', 'max:180'],
            'twitter' => ['nullable', 'string', 'max:180'],
            'tradingview' => ['nullable', 'string', 'max:180'],
            'website' => ['nullable', 'string', 'max:180'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
