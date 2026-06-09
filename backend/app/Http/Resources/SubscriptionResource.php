<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $expiresAt = $this->expires_at;
        $daysRemaining = $expiresAt ? (int) max(0, round(Carbon::now()->diffInDays($expiresAt, false))) : 0;

        return [
            'id' => $this->id,
            'code' => substr($this->code, 0, 4) . '-****-****-' . substr($this->code, -4),
            'duration' => $this->duration->value ?? $this->duration,
            'status' => $this->status->value ?? $this->status,
            'activated_at' => $this->activated_at ? $this->activated_at->toIso8601String() : null,
            'expires_at' => $expiresAt ? $expiresAt->toIso8601String() : null,
            'days_remaining' => $daysRemaining,
            'device' => $this->device ? [
                'device_id' => $this->device->device_id,
                'device_name' => $this->device->device_name,
                'device_model' => $this->device->device_model,
                'last_active_at' => $this->device->last_active_at ? $this->device->last_active_at->toIso8601String() : null,
            ] : null,
        ];
    }
}
