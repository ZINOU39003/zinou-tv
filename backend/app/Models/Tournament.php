<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'logo_url',
        'is_active',
        'sort_order',
        'standings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'standings' => 'array',
    ];

    public function matches(): HasMany
    {
        return $this->hasMany(SportMatch::class, 'tournament_id');
    }
}
