<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'icon', 'price_label',
        'features', 'is_active', 'is_featured', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }
}
