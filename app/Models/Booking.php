<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Booking extends Model
{
    protected $fillable = [
        'token', 'member_id', 'consultant_name', 'name', 'email', 'phone',
        'date', 'times', 'hours', 'rate', 'total_price', 'notes',
        'status', 'payment_status', 'paid_at', 'payment_proof', 'proof_uploaded_at',
        'rating', 'review',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'times' => 'array',
            'rate' => 'decimal:2',
            'total_price' => 'decimal:2',
            'paid_at' => 'datetime',
            'proof_uploaded_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Setiap booking dapat link privat (token) untuk halaman status milik klien
        static::creating(function (Booking $booking) {
            if (empty($booking->token)) {
                $booking->token = (string) Str::random(40);
            }
        });
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
