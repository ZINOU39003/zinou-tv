<?php

namespace App\Models;

use App\Enums\ChannelQuality;
use App\Enums\StreamType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChannelServer extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_id',
        'name',
        'stream_url',
        'stream_type',
        'quality',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'stream_type' => StreamType::class,
        'quality' => ChannelQuality::class,
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
