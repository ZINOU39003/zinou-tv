<?php

namespace App\Models;

use App\Enums\ChannelQuality;
use App\Enums\StreamType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Channel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'category_id',
        'package_id',
        'logo_url',
        'stream_url',
        'stream_type',
        'quality',
        'country',
        'language',
        'continent',
        'backup_url',
        'sort_order',
        'is_active',
        'drm_license_url',
        'drm_headers',
    ];

    protected $casts = [
        'stream_type' => StreamType::class,
        'quality' => ChannelQuality::class,
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Package::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function servers(): HasMany
    {
        return $this->hasMany(ChannelServer::class)->orderBy('sort_order');
    }

    public function isFavoritedBy(User $user): bool
    {
        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}
