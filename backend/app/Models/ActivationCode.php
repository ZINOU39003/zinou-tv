<?php

namespace App\Models;

use App\Enums\ActivationCodeStatus;
use App\Enums\CodeDuration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'duration',
        'status',
        'user_id',
        'device_id',
        'activated_at',
        'expires_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'duration' => CodeDuration::class,
        'status' => ActivationCodeStatus::class,
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isValid(): bool
    {
        if ($this->status !== ActivationCodeStatus::ACTIVE) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }
}
