<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Member extends Model
{
    protected $fillable = [
        'name', 'slug', 'title', 'tagline', 'bio', 'avatar', 'specialization',
        'experience_years', 'location', 'joined_at', 'consultation_rate',
        'telegram', 'instagram', 'twitter', 'tradingview', 'website',
        'binance_api_key', 'binance_api_secret',
        'is_active', 'is_featured', 'sort_order',
    ];

    protected $hidden = ['binance_api_key', 'binance_api_secret'];

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'binance_api_secret' => 'encrypted',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Member $member) {
            if (empty($member->slug) && ! empty($member->name)) {
                $member->slug = static::uniqueSlug($member->name, $member->id);
            }
        });
    }

    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;
        while (static::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    public function closedTrades(): HasMany
    {
        return $this->hasMany(Trade::class)->where('status', 'closed');
    }

    // ---- Statistik terhitung ----

    protected function totalPnl(): Attribute
    {
        return Attribute::get(fn () => (float) $this->trades->where('status', 'closed')->sum('pnl'));
    }

    protected function totalTrades(): Attribute
    {
        return Attribute::get(fn () => $this->trades->where('status', 'closed')->count());
    }

    protected function winningTrades(): Attribute
    {
        return Attribute::get(fn () => $this->trades->where('status', 'closed')->where('pnl', '>', 0)->count());
    }

    protected function winRate(): Attribute
    {
        return Attribute::get(function () {
            $total = $this->total_trades;

            return $total > 0 ? round($this->winning_trades / $total * 100, 1) : 0;
        });
    }

    protected function bestTrade(): Attribute
    {
        return Attribute::get(fn () => (float) $this->trades->where('status', 'closed')->max('pnl'));
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->avatar) {
                // fallback: avatar inisial (SVG) yang dihasilkan otomatis
                return $this->id ? '/avatar/'.$this->id : null;
            }

            return str_starts_with($this->avatar, 'http') || str_starts_with($this->avatar, '/')
                ? $this->avatar
                : '/storage/'.$this->avatar;
        });
    }

    /**
     * Ringkasan statistik untuk kartu & profil.
     */
    public function statsSummary(): array
    {
        $closed = $this->trades->where('status', 'closed');

        return [
            'total_pnl' => (float) $closed->sum('pnl'),
            'total_trades' => $closed->count(),
            'win_rate' => $this->win_rate,
            'winning_trades' => $closed->where('pnl', '>', 0)->count(),
            'best_trade' => (float) ($closed->max('pnl') ?? 0),
            'avg_roi' => round((float) $closed->whereNotNull('roi')->avg('roi'), 2),
        ];
    }

    /**
     * Realized PnL per bulan (untuk chart).
     */
    public function monthlyPnl(int $months = 6): array
    {
        $closed = $this->trades
            ->where('status', 'closed')
            ->whereNotNull('closed_at');

        $out = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $key = $month->format('Y-m');
            $sum = $closed->filter(fn ($t) => $t->closed_at?->format('Y-m') === $key)->sum('pnl');
            $out[] = [
                'label' => $month->translatedFormat('M Y'),
                'value' => round((float) $sum, 2),
            ];
        }

        return $out;
    }

    /**
     * Kurva ekuitas kumulatif dari PnL trade (urut waktu).
     */
    public function equityCurve(): array
    {
        $trades = $this->trades
            ->where('status', 'closed')
            ->whereNotNull('closed_at')
            ->sortBy('closed_at')
            ->values();

        $cum = 0;
        $points = [['label' => 'Start', 'value' => 0]];
        foreach ($trades as $t) {
            $cum += (float) $t->pnl;
            $points[] = [
                'label' => $t->closed_at?->format('d M'),
                'value' => round($cum, 2),
            ];
        }

        return $points;
    }
}
