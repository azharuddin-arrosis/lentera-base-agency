<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Signal extends Model
{
    protected $fillable = [
        'member_id', 'author_name', 'pair', 'market', 'side', 'timeframe', 'leverage',
        'entry', 'entry_max', 'stop_loss', 'take_profits', 'analysis',
        'status', 'result', 'pnl_percent', 'is_premium', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'take_profits' => 'array',
            'entry' => 'decimal:8',
            'entry_max' => 'decimal:8',
            'stop_loss' => 'decimal:8',
            'pnl_percent' => 'decimal:2',
            'is_premium' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /** Hanya sinyal yang sudah dipublikaslikan (published_at <= now). */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->lte(now());
    }
}
