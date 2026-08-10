<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ScheduleBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeamPanelController extends Controller
{
    public function index(Request $request): Response
    {
        $member = $request->user()->member;
        abort_unless($member, 403);

        $member->load('trades');

        return Inertia::render('panel/Dashboard', [
            'member' => [
                'id' => $member->id,
                'name' => $member->name,
                'title' => $member->title,
                'avatar_url' => $member->avatar_url,
                'consultation_rate' => (float) $member->consultation_rate,
                'is_active' => $member->is_active,
            ],
            'stats' => $member->statsSummary(),
            'consultStats' => [
                'total' => Booking::where('member_id', $member->id)->where('status', '!=', 'cancelled')->count(),
                'upcoming' => Booking::where('member_id', $member->id)->whereIn('status', ['pending', 'confirmed'])->whereDate('date', '>=', today())->count(),
                'done' => Booking::where('member_id', $member->id)->where('status', 'done')->count(),
                'hours' => (int) Booking::where('member_id', $member->id)->where('status', '!=', 'cancelled')->sum('hours'),
                'earnings' => (float) Booking::where('member_id', $member->id)->where('payment_status', 'paid')->sum('total_price'),
                'unpaid' => (float) Booking::where('member_id', $member->id)->where('payment_status', 'unpaid')->where('status', '!=', 'cancelled')->sum('total_price'),
            ],
            'consultations' => Booking::where('member_id', $member->id)
                ->orderByDesc('date')->orderByDesc('created_at')->limit(50)->get()
                ->map(fn ($b) => [
                    'id' => $b->id,
                    'name' => $b->name,
                    'email' => $b->email,
                    'phone' => $b->phone,
                    'date' => $b->date->translatedFormat('D, d M Y'),
                    'times' => $b->times,
                    'hours' => $b->hours,
                    'total_price' => (float) $b->total_price,
                    'status' => $b->status,
                    'payment_status' => $b->payment_status,
                    'is_upcoming' => ! ($b->date->isPast() && ! $b->date->isToday()),
                ]),
            'blocks' => ScheduleBlock::where('member_id', $member->id)
                ->whereDate('date', '>=', today())->orderBy('date')->get()
                ->map(fn ($blk) => [
                    'id' => $blk->id,
                    'date' => $blk->date->translatedFormat('l, d M Y'),
                    'date_raw' => $blk->date->format('Y-m-d'),
                    'times' => $blk->times,
                    'reason' => $blk->reason,
                ]),
        ]);
    }

    public function storeBlock(Request $request): RedirectResponse
    {
        $member = $request->user()->member;
        abort_unless($member, 403);

        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'full_day' => ['boolean'],
            'times' => ['nullable', 'array'],
            'times.*' => ['string', 'regex:/^\d{2}:\d{2}$/'],
            'reason' => ['nullable', 'string', 'max:180'],
        ]);

        ScheduleBlock::create([
            'member_id' => $member->id,
            'date' => $data['date'],
            'times' => ($data['full_day'] ?? false) ? null : ($data['times'] ?? []),
            'reason' => $data['reason'] ?? null,
        ]);
        $this->toast('Jadwal berhasil dikosongkan.');

        return back();
    }

    public function destroyBlock(Request $request, ScheduleBlock $block): RedirectResponse
    {
        abort_unless($block->member_id === $request->user()->member_id, 403);
        $block->delete();
        $this->toast('Blokir jadwal dihapus.');

        return back();
    }
}
