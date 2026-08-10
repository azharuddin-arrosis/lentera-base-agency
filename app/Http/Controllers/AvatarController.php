<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Response;

class AvatarController extends Controller
{
    /** Palet warna latar avatar (gradient dua warna). */
    protected array $palette = [
        ['#34d399', '#059669'], ['#22d3ee', '#0891b2'], ['#818cf8', '#4f46e5'],
        ['#f472b6', '#db2777'], ['#fbbf24', '#d97706'], ['#a78bfa', '#7c3aed'],
        ['#2dd4bf', '#0d9488'], ['#60a5fa', '#2563eb'], ['#fb7185', '#e11d48'],
        ['#4ade80', '#16a34a'],
    ];

    public function show(Member $member): Response
    {
        $initials = collect(preg_split('/\s+/', trim($member->name)))
            ->filter()
            ->take(2)
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))
            ->implode('');

        [$c1, $c2] = $this->palette[abs(crc32($member->name)) % count($this->palette)];

        $svg = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128" width="128" height="128">
          <defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0" stop-color="{$c1}"/><stop offset="1" stop-color="{$c2}"/>
          </linearGradient></defs>
          <rect width="128" height="128" rx="28" fill="url(#g)"/>
          <text x="64" y="64" dy="0.35em" text-anchor="middle" fill="#fff"
                font-family="ui-sans-serif,system-ui,Arial,sans-serif" font-size="52" font-weight="700">{$initials}</text>
        </svg>
        SVG;

        return response($svg, 200)->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
