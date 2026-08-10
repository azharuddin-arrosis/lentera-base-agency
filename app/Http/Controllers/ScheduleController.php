<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Member;
use App\Models\ScheduleBlock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    /** Tarif konsultasi dengan admin/tim (Rupiah per jam). */
    public const ADMIN_RATE = 150000;

    public const ADMIN_KEY = 'admin';

    public function index(): Response
    {
        $this->seo([
            'title' => 'Jadwalkan Konsultasi Trading — Lentera Project',
            'description' => 'Booking sesi konsultasi trading 1-on-1 dengan konsultan Lentera Project. Pilih konsultan, tanggal, dan jam sesuai kebutuhan.',
        ]);

        $members = Member::where('is_active', true)->orderBy('sort_order')->get();

        $consultants = collect([[
            'key' => self::ADMIN_KEY,
            'name' => 'Tim Lentera (Admin)',
            'title' => 'Konsultasi umum & administrasi',
            'avatar_url' => null,
            'rate' => (float) self::ADMIN_RATE,
        ]])->concat($members->map(fn (Member $m) => [
            'key' => (string) $m->id,
            'name' => $m->name,
            'title' => $m->title,
            'avatar_url' => $m->avatar_url,
            'rate' => (float) $m->consultation_rate,
        ]))->values();

        // slot terisi: "key|Y-m-d|HH:MM"
        $booked = Booking::whereIn('status', ['pending', 'confirmed'])
            ->whereDate('date', '>=', today())
            ->get(['member_id', 'date', 'times'])
            ->flatMap(function ($b) {
                $key = $b->member_id ? (string) $b->member_id : self::ADMIN_KEY;
                $d = $b->date->format('Y-m-d');

                return collect($b->times)->map(fn ($t) => "{$key}|{$d}|{$t}");
            });

        // slot yang dikosongkan/diblok admin (member libur di hari itu)
        $blocked = ScheduleBlock::whereDate('date', '>=', today())->get()
            ->flatMap(function ($blk) {
                $key = $blk->member_id ? (string) $blk->member_id : self::ADMIN_KEY;
                $d = $blk->date->format('Y-m-d');
                $times = $blk->times ?: self::slotsForWeekday($blk->date->dayOfWeek); // null = seharian

                return collect($times)->map(fn ($t) => "{$key}|{$d}|{$t}");
            });

        $booked = $booked->concat($blocked)->unique()->values();

        // jam tersedia per weekday (0=Min ... 6=Sab), sudah dikurangi jam istirahat
        $hours = [];
        for ($w = 0; $w <= 6; $w++) {
            $hours[$w] = self::slotsForWeekday($w);
        }

        return Inertia::render('public/Schedule', [
            'consultants' => $consultants,
            'booked' => $booked,
            'today' => today()->format('Y-m-d'),
            'hours' => $hours,
            'adminKey' => self::ADMIN_KEY,
        ]);
    }

    public function store(): RedirectResponse
    {
        $data = request()->validate([
            'consultant' => ['required', 'string'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'times' => ['required', 'array', 'min:1'],
            'times.*' => ['string', 'regex:/^\d{2}:\d{2}$/'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // resolusi konsultan + tarif
        if ($data['consultant'] === self::ADMIN_KEY) {
            $memberId = null;
            $consultantName = 'Tim Lentera (Admin)';
            $rate = (float) self::ADMIN_RATE;
        } else {
            $member = Member::where('is_active', true)->find($data['consultant']);
            if (! $member) {
                throw ValidationException::withMessages(['consultant' => 'Konsultan tidak ditemukan.']);
            }
            $memberId = $member->id;
            $consultantName = $member->name;
            $rate = (float) $member->consultation_rate;
        }

        $date = Carbon::parse($data['date']);
        $available = self::slotsForWeekday($date->dayOfWeek);
        $times = array_values(array_unique($data['times']));
        sort($times);

        foreach ($times as $t) {
            if (! in_array($t, $available, true)) {
                throw ValidationException::withMessages(['times' => "Slot {$t} tidak tersedia untuk hari itu."]);
            }
            if ($date->isToday() && $t <= now()->format('H:i')) {
                throw ValidationException::withMessages(['times' => "Slot {$t} sudah lewat."]);
            }
        }

        // cek bentrok untuk konsultan yang sama
        $existing = Booking::whereDate('date', $date->format('Y-m-d'))
            ->whereIn('status', ['pending', 'confirmed'])
            ->when($memberId, fn ($q) => $q->where('member_id', $memberId), fn ($q) => $q->whereNull('member_id'))
            ->get(['times']);
        $taken = $existing->flatMap(fn ($b) => $b->times)->unique();
        $clash = array_values(array_intersect($times, $taken->all()));
        if (! empty($clash)) {
            throw ValidationException::withMessages([
                'times' => 'Slot berikut baru saja dipesan: '.implode(', ', $clash).'. Silakan pilih slot lain.',
            ]);
        }

        $hours = count($times);
        $booking = Booking::create([
            'member_id' => $memberId,
            'consultant_name' => $consultantName,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'date' => $date->format('Y-m-d'),
            'times' => $times,
            'hours' => $hours,
            'rate' => $rate,
            'total_price' => $rate * $hours,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
        ]);

        $this->toast('Jadwal konsultasi dengan '.$consultantName.' pada '
            .$date->translatedFormat('l, d F Y').' ('.implode(', ', $times).') berhasil dibuat. '
            .'Silakan lakukan pembayaran & unggah buktinya di halaman ini.');

        // Arahkan klien ke halaman status booking miliknya (link privat via token)
        return redirect()->route('booking.status', $booking->token);
    }

    /**
     * Slot jam tersedia per weekday, sudah dikurangi jam istirahat.
     * - Sabtu & Minggu: libur.
     * - Istirahat harian 12.00–13.00 (slot 12:00 dihapus).
     * - Jumat istirahat 11.00–14.00 (slot 11,12,13 dihapus).
     *
     * @return array<int, string>
     */
    public static function slotsForWeekday(int $weekday): array
    {
        if ($weekday === 0 || $weekday === 6) {
            return []; // Minggu (0) & Sabtu (6) libur
        }

        $slots = [];
        for ($h = 9; $h <= 20; $h++) {
            $slots[] = sprintf('%02d:00', $h);
        }

        $breaks = $weekday === 5
            ? ['11:00', '12:00', '13:00'] // Jumat
            : ['12:00'];                  // Senin–Kamis

        return array_values(array_diff($slots, $breaks));
    }
}
