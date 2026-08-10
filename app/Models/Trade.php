<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    protected $fillable = [
        'member_id', 'symbol', 'market', 'side', 'leverage',
        'entry_price', 'exit_price', 'quantity', 'pnl', 'roi', 'fee',
        'status', 'source', 'binance_id', 'notes', 'opened_at', 'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'leverage' => 'decimal:2',
            'entry_price' => 'decimal:8',
            'exit_price' => 'decimal:8',
            'quantity' => 'decimal:8',
            'pnl' => 'decimal:8',
            'roi' => 'decimal:4',
            'fee' => 'decimal:8',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    protected function isProfit(): Attribute
    {
        return Attribute::get(fn () => (float) $this->pnl >= 0);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }
}
