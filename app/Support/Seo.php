<?php

namespace App\Support;

/**
 * Wadah metadata SEO per-request. Diisi oleh controller (via Controller::seo())
 * dan dibaca oleh app.blade.php untuk menghasilkan tag <head> yang unik tiap halaman.
 */
class Seo
{
    public string $title;

    public string $description;

    public ?string $canonical = null;

    public ?string $image = null;

    public string $type = 'website';

    /** @var array<int, array<string, mixed>> Blok JSON-LD tambahan (breadcrumb, entitas, dll). */
    public array $schemas = [];

    public function __construct()
    {
        $this->title = config('app.name').' — Konsultan Trading Profesional & Sinyal';
        $this->description = 'Lentera Project — konsultan trading profesional. Sinyal crypto & futures, mentoring, '
            .'dan analisis pasar berbasis manajemen risiko dengan rekam jejak PnL yang transparan.';
    }

    /**
     * @param  array<string, mixed>  $data  title, description, canonical, image, type, schema
     */
    public function set(array $data): static
    {
        foreach (['title', 'description', 'canonical', 'image', 'type'] as $key) {
            if (! empty($data[$key])) {
                $this->{$key} = $data[$key];
            }
        }

        if (! empty($data['schema'])) {
            // boleh satu schema atau array of schema
            if (array_is_list($data['schema']) && isset($data['schema'][0]) && is_array($data['schema'][0])) {
                $this->schemas = array_merge($this->schemas, $data['schema']);
            } else {
                $this->schemas[] = $data['schema'];
            }
        }

        return $this;
    }

    /** Judul lengkap untuk tag <title>. */
    public function title(): string
    {
        return $this->title;
    }

    public function image(): string
    {
        return $this->image ?? url('/favicon.svg');
    }

    public function canonical(): string
    {
        return $this->canonical ?? url()->current();
    }

    /**
     * Rakit seluruh JSON-LD (Organization + WebSite + schema per-halaman) menjadi string.
     * Dibangun di PHP agar token seperti @context/@type tidak diproses sebagai Blade directive.
     */
    public function jsonLd(): string
    {
        $graph = array_merge([
            [
                '@type' => 'Organization',
                '@id' => url('/').'#organization',
                'name' => config('app.name'),
                'url' => url('/'),
                'logo' => url('/favicon.svg'),
                'description' => 'Konsultan trading profesional: sinyal crypto & futures, mentoring, dan analisis pasar berbasis manajemen risiko.',
                'sameAs' => array_values(array_filter([
                    config('services.social.telegram'),
                    config('services.social.instagram'),
                    config('services.social.twitter'),
                    config('services.social.youtube'),
                ])),
            ],
            [
                '@type' => 'WebSite',
                '@id' => url('/').'#website',
                'name' => config('app.name'),
                'url' => url('/'),
                'inLanguage' => 'id-ID',
                'publisher' => ['@id' => url('/').'#organization'],
            ],
        ], $this->schemas);

        return (string) json_encode(
            ['@context' => 'https://schema.org', '@graph' => $graph],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        );
    }
}
