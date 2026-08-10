<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ScheduleController;
use App\Models\Booking;
use App\Models\Member;
use App\Models\ScheduleBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(): Response
    {
        $bookings = Booking::orderByDesc('date')->orderByDesc('created_at')->get();

        return Inertia::render('admin/Bookings', [
            'bookings' => $bookings->map(fn ($b) => [
                'id' => $b->id,
                'member_id' => $b->member_id,
                'name' => $b->name,
                'email' => $b->email,
                'phone' => $b->phone,
                'consultant' => $b->consultant_name,
                'date' => $b->date->translatedFormat('l, d M Y'),
                'date_raw' => $b->date->format('Y-m-d'),
                'times' => $b->times,
                'hours' => $b->hours,
                'rate' => (float) $b->rate,
                'total_price' => (float) $b->total_price,
                'notes' => $b->notes,
                'status' => $b->status,
                'payment_status' => $b->payment_status,
                'is_past' => $b->date->isPast() && ! $b->date->isToday(),
            ]),
            'members' => Member::orderBy('name')->get(['id', 'name']),
            'counts' => [
                'all' => $bookings->count(),
                'pending' => $bookings->where('status', 'pending')->count(),
                'confirmed' => $bookings->where('status', 'confirmed')->count(),
                'done' => $bookings->where('status', 'done')->count(),
                'cancelled' => $bookings->where('status', 'cancelled')->count(),
                'unpaid' => $bookings->whereIn('payment_status', ['unpaid', 'awaiting'])->whereIn('status', ['pending', 'confirmed', 'done'])->count(),
                'awaiting' => $bookings->where('payment_status', 'awaiting')->count(),
            ],
            'revenue' => (float) $bookings->where('payment_status', 'paid')->sum('total_price'),
            'blocks' => ScheduleBlock::whereDate('date', '>=', today())->orderBy('date')->get()->map(fn ($blk) => [
                'id' => $blk->id,
                'member_id' => $blk->member_id,
                'consultant' => $blk->member_id ? optional($blk->member)->name : 'Tim Lentera (Admin)',
                'date' => $blk->date->translatedFormat('l, d M Y'),
                'date_raw' => $blk->date->format('Y-m-d'),
                'times' => $blk->times,
                'reason' => $blk->reason,
            ]),
            'consultants' => collect([['id' => 'admin', 'name' => 'Tim Lentera (Admin)']])
                ->concat(Member::orderBy('name')->get(['id', 'name'])->map(fn ($m) => ['id' => (string) $m->id, 'name' => $m->name]))
                ->values(),
        ]);
    }

    public function show(Booking $booking): Response
    {
        return Inertia::render('admin/bookings/Show', [
            'booking' => [
                'id' => $booking->id,
                'consultant' => $booking->consultant_name,
                'name' => $booking->name,
                'email' => $booking->email,
                'phone' => $booking->phone,
                'date' => $booking->date->translatedFormat('l, d F Y'),
                'date_raw' => $booking->date->format('Y-m-d'),
                'times' => $booking->times,
                'hours' => $booking->hours,
                'rate' => (float) $booking->rate,
                'total_price' => (float) $booking->total_price,
                'notes' => $booking->notes,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'paid_at' => $booking->paid_at?->translatedFormat('d M Y H:i'),
                'payment_proof_url' => $booking->payment_proof ? '/storage/'.$booking->payment_proof : null,
                'proof_uploaded_at' => $booking->proof_uploaded_at?->translatedFormat('d M Y H:i'),
                'rating' => $booking->rating,
                'review' => $booking->review,
                'created_at' => $booking->created_at?->translatedFormat('d M Y H:i'),
                'status_url' => route('booking.status', $booking->token),
            ],
        ]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'in:pending,confirmed,done,cancelled']]);
        $booking->update($data);
        $this->toast('Status jadwal diperbarui.');

        return back();
    }

    public function togglePaid(Booking $booking): RedirectResponse
    {
        $paid = $booking->payment_status !== 'paid';
        $booking->update([
            'payment_status' => $paid ? 'paid' : 'unpaid',
            'paid_at' => $paid ? now() : null,
        ]);
        $this->toast($paid ? 'Ditandai LUNAS.' : 'Ditandai belum bayar.');

        return back();
    }

    public function reschedule(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'times' => ['required', 'array', 'min:1'],
            'times.*' => ['string', 'regex:/^\d{2}:\d{2}$/'],
        ]);

        $date = Carbon::parse($data['date']);
        $available = ScheduleController::slotsForWeekday($date->dayOfWeek);
        $times = array_values(array_unique($data['times']));
        sort($times);

        foreach ($times as $t) {
            if (! in_array($t, $available, true)) {
                throw ValidationException::withMessages(['times' => "Slot {$t} tidak tersedia untuk hari itu."]);
            }
        }

        // cek bentrok dengan booking lain (konsultan sama)
        $clashTaken = Booking::where('id', '!=', $booking->id)
            ->whereDate('date', $date->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($booking->member_id, fn ($q) => $q->where('member_id', $booking->member_id), fn ($q) => $q->whereNull('member_id'))
            ->get(['times'])->flatMap(fn ($b) => $b->times)->unique();
        $clash = array_values(array_intersect($times, $clashTaken->all()));
        if (! empty($clash)) {
            throw ValidationException::withMessages(['times' => 'Bentrok dengan jadwal lain: '.implode(', ', $clash)]);
        }

        $booking->update([
            'date' => $date->format('Y-m-d'),
            'times' => $times,
            'hours' => count($times),
            'total_price' => (float) $booking->rate * count($times),
        ]);
        $this->toast('Jadwal berhasil di-reschedule ke '.$date->translatedFormat('l, d F Y').' ('.implode(', ', $times).').');

        return back();
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();
        $this->toast('Jadwal dihapus.');

        return back();
    }

    // ---- Kosongkan jadwal (block) ----

    public function storeBlock(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'consultant' => ['required', 'string'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'full_day' => ['boolean'],
            'times' => ['nullable', 'array'],
            'times.*' => ['string', 'regex:/^\d{2}:\d{2}$/'],
            'reason' => ['nullable', 'string', 'max:180'],
        ]);

        $memberId = $data['consultant'] === 'admin' ? null : (int) $data['consultant'];

        ScheduleBlock::create([
            'member_id' => $memberId,
            'date' => $data['date'],
            'times' => ($data['full_day'] ?? false) ? null : ($data['times'] ?? []),
            'reason' => $data['reason'] ?? null,
        ]);
        $this->toast('Jadwal berhasil dikosongkan.');

        return back();
    }

    public function destroyBlock(ScheduleBlock $block): RedirectResponse
    {
        $block->delete();
        $this->toast('Blokir jadwal dihapus.');

        return back();
    }
}
