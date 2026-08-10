<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleBlock extends Model
{
    protected $fillable = ['member_id', 'date', 'times', 'reason'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'times' => 'array',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
