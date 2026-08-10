<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Withdrawal extends Model
{
    protected $fillable = [
        'member_id', 'amount', 'withdrawn_at', 'method', 'note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:8',
            'withdrawn_at' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
