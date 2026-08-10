<?php

namespace App\Http\Controllers;

use App\Support\Seo;
use Inertia\Inertia;

abstract class Controller
{
    /**
     * Kirim notifikasi toast ke frontend (vue-sonner) untuk request berikutnya.
     */
    protected function toast(string $message, string $type = 'success'): void
    {
        Inertia::flash('toast', ['type' => $type, 'message' => $message]);
    }

    /**
     * Set metadata SEO untuk halaman ini (dibaca app.blade.php).
     *
     * @param  array<string, mixed>  $data
     */
    protected function seo(array $data): void
    {
        app(Seo::class)->set($data);
    }

    /**
     * Bangun schema BreadcrumbList untuk JSON-LD.
     *
     * @param  array<int, array{0: string, 1: string}>  $items  [ [nama, url], ... ]
     * @return array<string, mixed>
     */
    protected function breadcrumb(array $items): array
    {
        return [
            '@type' => 'BreadcrumbList',
            'itemListElement' => collect($items)->values()->map(fn ($it, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $it[0],
                'item' => $it[1],
            ])->all(),
        ];
    }
}
