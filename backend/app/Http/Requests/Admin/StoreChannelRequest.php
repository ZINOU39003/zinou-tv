<?php

namespace App\Http\Requests\Admin;

use App\Enums\ChannelQuality;
use App\Enums\StreamType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreChannelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'package_id' => 'nullable|exists:packages,id',
            'logo_url' => 'nullable|url|max:2048',
            'stream_url' => 'required|string|max:2048',
            'stream_type' => ['required', new Enum(StreamType::class)],
            'quality' => ['required', new Enum(ChannelQuality::class)],
            'backup_url' => 'nullable|string|max:2048',
            'country' => 'nullable|string|max:255',
            'language' => 'nullable|string|max:255',
            'continent' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'drm_license_url' => 'nullable|string|max:2048',
            'drm_headers' => 'nullable|string|max:65535',
            'servers' => 'nullable|array',
            'servers.*.name' => 'required|string|max:255',
            'servers.*.stream_url' => 'required|string|max:2048',
            'servers.*.stream_type' => 'required|string|in:m3u8,mpd,ts',
            'servers.*.quality' => 'required|string|in:FHD,HD,SD,4K',
            'servers.*.sort_order' => 'nullable|integer|min:0',
        ];
    }
}
