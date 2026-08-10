<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class BookingStatusController extends Controller
{
    public function show(Booking $booking): Response
    {
        return Inertia::render('public/BookingStatus', [
            'booking' => $this->present($booking),
        ]);
    }

    public function uploadProof(Request $request, Booking $booking): RedirectResponse
    {
        if (in_array($booking->status, ['cancelled'], true)) {
            throw ValidationException::withMessages(['payment_proof' => 'Booking ini sudah dibatalkan.']);
        }
        if ($booking->payment_status === 'paid') {
            throw ValidationException::withMessages(['payment_proof' => 'Pembayaran sudah dikonfirmasi lunas.']);
        }

        $request->validate([
            'payment_proof' => ['required', 'image', 'max:4096'], // maks 4MB
        ]);

        if ($booking->payment_proof) {
            Storage::disk('public')->delete($booking->payment_proof);
        }

        $path = $request->file('payment_proof')->store('payments', 'public');
        $booking->update([
            'payment_proof' => $path,
            'proof_uploaded_at' => now(),
            'payment_status' => 'awaiting', // menunggu verifikasi admin
        ]);
        $this->toast('Bukti pembayaran terkirim. Tim kami akan memverifikasi pembayaran Anda.');

        return back();
    }

    public function review(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->status !== 'done') {
            throw ValidationException::withMessages(['rating' => 'Ulasan hanya bisa diberikan setelah konsultasi selesai.']);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);
        $booking->update($data);
        $this->toast('Terima kasih! Ulasan Anda sudah tersimpan.');

        return back();
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Booking $booking): array
    {
        return [
            'token' => $booking->token,
            'consultant' => $booking->consultant_name,
            'name' => $booking->name,
            'email' => $booking->email,
            'phone' => $booking->phone,
            'date' => $booking->date->translatedFormat('l, d F Y'),
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
            'rating' => $booking->rating ? (int) $booking->rating : null,
            'review' => $booking->review,
            'can_pay' => ! in_array($booking->status, ['cancelled'], true) && $booking->payment_status !== 'paid',
            'can_review' => $booking->status === 'done',
        ];
    }
}
