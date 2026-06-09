<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'poster_url',
        'type',
        'stream_url',
        'description',
        'description_ar',
        'year',
        'rating',
        'is_latest',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'year' => 'integer',
        'rating' => 'decimal:1',
        'is_latest' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];
}
