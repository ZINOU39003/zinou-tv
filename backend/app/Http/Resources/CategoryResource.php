<?php

namespace App\Http\Resources;

use App\Support\MediaUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'slug' => $this->slug,
            'icon' => MediaUrl::resolve($this->icon),
            'type' => $this->type,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'channels_count' => $this->channels_count ?? $this->channels()->where('is_active', true)->count(),
            'packages_count' => $this->packages_count ?? $this->packages()->where('is_active', true)->count(),
        ];
    }
}
