<?php

namespace App\Http\Resources;

use App\Services\EncryptionService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $encryptionService = resolve(EncryptionService::class);

        // Helper function to rewrite DASH streams through our reverse proxy
        $getProxiedUrl = function($channelId, $encryptedUrl) use ($encryptionService, $request) {
            $decrypted = $encryptionService->decrypt($encryptedUrl);
            if (empty($decrypted)) {
                return '';
            }
            
            // Check if it is a DASH stream (.mpd)
            if (str_contains(strtolower($decrypted), '.mpd')) {
                $parsed = parse_url($decrypted);
                $path = $parsed['path'] ?? '';
                $filename = basename($path) ?: 'playlist_ha.mpd';
                
                // Use the incoming request's scheme and host so the proxy URL
                // works correctly whether accessed via localtunnel, ngrok, or localhost
                $baseUrl = $request->getSchemeAndHttpHost();
                return $baseUrl . '/stream-proxy/' . $channelId . '/' . $filename;
            }
            
            return $decrypted;
        };

        // Fetch servers or synthesize from primary/backup stream
        $servers = [];
        $dbServers = $this->relationLoaded('servers') ? $this->servers : $this->servers()->where('is_active', true)->get();

        if ($dbServers && count($dbServers) > 0) {
            foreach ($dbServers as $server) {
                $servers[] = [
                    'id' => $server->id,
                    'name' => $server->name,
                    'stream_url' => $getProxiedUrl($this->id, $server->stream_url),
                    'stream_type' => $server->stream_type->value ?? $server->stream_type,
                    'quality' => $server->quality->value ?? $server->quality,
                ];
            }
        } else {
            // Fallback synthesized servers
            if ($this->stream_url) {
                $servers[] = [
                    'id' => 0,
                    'name' => 'Server 1',
                    'stream_url' => $getProxiedUrl($this->id, $this->stream_url),
                    'stream_type' => $this->stream_type->value ?? $this->stream_type,
                    'quality' => $this->quality->value ?? $this->quality,
                ];
            }
            if ($this->backup_url) {
                $servers[] = [
                    'id' => 0,
                    'name' => 'Server 2 (Backup)',
                    'stream_url' => $getProxiedUrl($this->id, $this->backup_url),
                    'stream_type' => $this->stream_type->value ?? $this->stream_type,
                    'quality' => $this->quality->value ?? $this->quality,
                ];
            }
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'name_ar' => $this->name_ar,
            'category_id' => $this->category_id,
            'category_name' => $this->category ? $this->category->name : null,
            'category_name_ar' => $this->category ? $this->category->name_ar : null,
            'package_id' => $this->package_id,
            'package_name' => $this->package ? $this->package->name : null,
            'package_name_ar' => $this->package ? $this->package->name_ar : null,
            'logo_url' => $this->logo_url,
            'stream_url' => $getProxiedUrl($this->id, $this->stream_url),
            'stream_type' => $this->stream_type->value ?? $this->stream_type,
            'quality' => $this->quality->value ?? $this->quality,
            'country' => $this->country,
            'language' => $this->language,
            'continent' => $this->continent,
            'backup_url' => $this->backup_url ? $getProxiedUrl($this->id, $this->backup_url) : null,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
            'drm_license_url' => $this->drm_license_url,
            'drm_headers' => $this->drm_headers,
            'servers' => $servers,
        ];
    }
}
