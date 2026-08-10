<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Signal;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * robots.txt dinamis (host mengikuti environment).
     * Semua area privat/otentikasi di-disallow.
     */
    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /admin',
            'Disallow: /panel',
            'Disallow: /dashboard',
            'Disallow: /settings',
            'Disallow: /booking',   // halaman status booking privat (token)
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /forgot-password',
            '',
            'Sitemap: '.url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines), 200)
            ->header('Content-Type', 'text/plain');
    }

    /**
     * sitemap.xml — semua halaman publik + data (profil tim & detail signal).
     * Area /admin, /panel, /dashboard, /booking, dll tidak disertakan.
     */
    public function sitemap(): Response
    {
        $urls = [
            ['loc' => url('/'), 'priority' => '1.0', 'freq' => 'weekly'],
            ['loc' => url('/team'), 'priority' => '0.8', 'freq' => 'weekly'],
            ['loc' => url('/signals'), 'priority' => '0.9', 'freq' => 'daily'],
            ['loc' => url('/performance'), 'priority' => '0.9', 'freq' => 'daily'],
            ['loc' => url('/schedule'), 'priority' => '0.7', 'freq' => 'weekly'],
            ['loc' => url('/contact'), 'priority' => '0.6', 'freq' => 'monthly'],
        ];

        // Profil tiap konsultan aktif
        foreach (Member::where('is_active', true)->get() as $member) {
            $urls[] = [
                'loc' => url('/team/'.$member->slug),
                'priority' => '0.7',
                'freq' => 'weekly',
                'lastmod' => $member->updated_at?->toAtomString(),
            ];
        }

        // Detail tiap signal yang sudah dipublikasikan
        foreach (Signal::published()->orderByDesc('published_at')->get() as $signal) {
            $urls[] = [
                'loc' => url('/signals/'.$signal->id),
                'priority' => '0.6',
                'freq' => $signal->status === 'open' ? 'daily' : 'monthly',
                'lastmod' => $signal->updated_at?->toAtomString(),
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= '    <loc>'.htmlspecialchars($u['loc']).'</loc>'."\n";
            if (! empty($u['lastmod'])) {
                $xml .= '    <lastmod>'.$u['lastmod'].'</lastmod>'."\n";
            }
            $xml .= '    <changefreq>'.$u['freq'].'</changefreq>'."\n";
            $xml .= '    <priority>'.$u['priority'].'</priority>'."\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
