<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'slug' => $this->slug,
            'logo_url' => $this->logo_url,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'channels_count' => $this->channels_count ?? $this->channels()->where('is_active', true)->count(),
        ];
    }
}
