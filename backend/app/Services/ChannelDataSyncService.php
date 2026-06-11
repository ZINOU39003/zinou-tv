<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Channel;
use App\Models\Package;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChannelDataSyncService
{
    public function __construct(
        protected EncryptionService $encryptionService,
        protected ChannelService $channelService,
    ) {}

    public function export(): array
    {
        $encryption = $this->encryptionService;

        $categories = Category::orderBy('sort_order')->get()->map(fn (Category $cat) => [
            'slug' => $cat->slug,
            'name' => $cat->name,
            'name_ar' => $cat->name_ar,
            'type' => $cat->type,
            'icon' => $this->normalizeMediaForExport($cat->icon),
            'sort_order' => $cat->sort_order,
            'is_active' => $cat->is_active,
        ])->values()->all();

        $packages = Package::orderBy('sort_order')->get()->map(fn (Package $pkg) => [
            'slug' => $pkg->slug,
            'category_slug' => $pkg->category?->slug,
            'name' => $pkg->name,
            'name_ar' => $pkg->name_ar,
            'logo_url' => $this->normalizeMediaForExport($pkg->logo_url),
            'sort_order' => $pkg->sort_order,
            'is_active' => $pkg->is_active,
        ])->values()->all();

        $channels = Channel::with('servers')->orderBy('sort_order')->get()->map(function (Channel $ch) use ($encryption) {
            $servers = $ch->servers->map(fn ($s) => [
                'name' => $s->name,
                'stream_url' => $this->safeDecrypt($encryption, $s->stream_url),
                'stream_type' => $s->stream_type->value,
                'quality' => $s->quality->value,
                'is_active' => $s->is_active,
                'sort_order' => $s->sort_order,
            ])->values()->all();

            return [
                'name' => $ch->name,
                'name_ar' => $ch->name_ar,
                'category_slug' => $ch->category?->slug,
                'package_slug' => $ch->package?->slug,
                'logo_url' => $ch->logo_url,
                'stream_url' => $this->safeDecrypt($encryption, $ch->stream_url),
                'stream_type' => $ch->stream_type->value,
                'quality' => $ch->quality->value,
                'backup_url' => $ch->backup_url ? $this->safeDecrypt($encryption, $ch->backup_url) : null,
                'country' => $ch->country,
                'language' => $ch->language,
                'continent' => $ch->continent,
                'sort_order' => $ch->sort_order,
                'is_active' => $ch->is_active,
                'drm_license_url' => $ch->drm_license_url,
                'drm_headers' => $ch->drm_headers,
                'servers' => $servers,
            ];
        })->values()->all();

        return [
            'version' => 1,
            'exported_at' => now()->toIso8601String(),
            'categories' => $categories,
            'packages' => $packages,
            'channels' => $channels,
        ];
    }

    public function import(array $data, bool $replaceExisting = false): array
    {
        $stats = ['categories' => 0, 'packages' => 0, 'channels' => 0, 'skipped' => 0];

        if ($replaceExisting) {
            \App\Models\ChannelServer::query()->delete();
            Channel::query()->delete();
            Package::query()->delete();
            Category::query()->delete();
        }

        $categoryMap = [];
        foreach ($data['categories'] ?? [] as $row) {
            $icon = $this->restoreMediaFromExport($row['icon'] ?? null, 'categories');
            $cat = Category::updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'name_ar' => $row['name_ar'] ?? $row['name'],
                    'type' => $row['type'] ?? 'network',
                    'icon' => $icon,
                    'sort_order' => $row['sort_order'] ?? 0,
                    'is_active' => $row['is_active'] ?? true,
                ]
            );
            $categoryMap[$row['slug']] = $cat->id;
            $stats['categories']++;
        }

        $packageMap = [];
        foreach ($data['packages'] ?? [] as $row) {
            $categoryId = $categoryMap[$row['category_slug']] ?? null;
            if (! $categoryId) {
                continue;
            }
            $logo = $this->restoreMediaFromExport($row['logo_url'] ?? null, 'packages');
            $pkg = Package::updateOrCreate(
                ['slug' => $row['slug'], 'category_id' => $categoryId],
                [
                    'name' => $row['name'],
                    'name_ar' => $row['name_ar'] ?? $row['name'],
                    'logo_url' => $logo,
                    'sort_order' => $row['sort_order'] ?? 0,
                    'is_active' => $row['is_active'] ?? true,
                ]
            );
            $packageMap[$row['slug']] = $pkg->id;
            $stats['packages']++;
        }

        foreach ($data['channels'] ?? [] as $row) {
            $categoryId = $categoryMap[$row['category_slug'] ?? ''] ?? null;
            if (! $categoryId) {
                $stats['skipped']++;

                continue;
            }

            $packageId = isset($row['package_slug'], $packageMap[$row['package_slug']])
                ? $packageMap[$row['package_slug']]
                : null;

            $existing = Channel::where('name', $row['name'])
                ->where('category_id', $categoryId)
                ->first();

            if (! $existing && ! $replaceExisting) {
                $existing = Channel::where('name', $row['name'])->first();
            }

            if ($existing && ! $replaceExisting) {
                $payload = [
                    'name' => $row['name'],
                    'name_ar' => $row['name_ar'] ?? $row['name'],
                    'category_id' => $categoryId,
                    'package_id' => $packageId,
                    'logo_url' => $row['logo_url'] ?? $existing->logo_url,
                    'stream_url' => $row['stream_url'],
                    'stream_type' => $row['stream_type'] ?? 'm3u8',
                    'quality' => $row['quality'] ?? 'HD',
                    'backup_url' => $row['backup_url'] ?? null,
                    'country' => $row['country'] ?? null,
                    'language' => $row['language'] ?? null,
                    'continent' => $row['continent'] ?? null,
                    'sort_order' => $row['sort_order'] ?? 0,
                    'is_active' => $row['is_active'] ?? true,
                    'drm_license_url' => $row['drm_license_url'] ?? null,
                    'drm_headers' => $row['drm_headers'] ?? null,
                    'servers' => $row['servers'] ?? [],
                ];
                $this->channelService->updateChannel($existing, $payload);
                $stats['channels']++;

                continue;
            }

            $payload = [
                'name' => $row['name'],
                'name_ar' => $row['name_ar'] ?? $row['name'],
                'category_id' => $categoryId,
                'package_id' => $packageId,
                'logo_url' => $row['logo_url'] ?? null,
                'stream_url' => $row['stream_url'],
                'stream_type' => $row['stream_type'] ?? 'm3u8',
                'quality' => $row['quality'] ?? 'HD',
                'backup_url' => $row['backup_url'] ?? null,
                'country' => $row['country'] ?? null,
                'language' => $row['language'] ?? null,
                'continent' => $row['continent'] ?? null,
                'sort_order' => $row['sort_order'] ?? 0,
                'is_active' => $row['is_active'] ?? true,
                'drm_license_url' => $row['drm_license_url'] ?? null,
                'drm_headers' => $row['drm_headers'] ?? null,
                'servers' => $row['servers'] ?? [],
            ];

            if ($existing) {
                $this->channelService->updateChannel($existing, $payload);
            } else {
                $this->channelService->createChannel($payload);
            }
            $stats['channels']++;
        }

        return $stats;
    }

    /**
     * Restore Render's original 2-network layout and re-link World Cup channels.
     */
    public function restoreRenderLegacyStructure(): array
    {
        $legacy = json_decode(
            file_get_contents(base_path('database/data/render-legacy-export.json')),
            true
        ) ?: [];

        $stats = $this->import($legacy, false);

        $worldCup = Category::where('slug', 'world-cup-2026')->first();
        $package = $worldCup
            ? Package::where('slug', 'world-cup-2026-bein')->where('category_id', $worldCup->id)->first()
            : null;

        $relinked = 0;
        if ($worldCup && $package) {
            $legacySlugs = ['bein-sports-max', 'world-cup-2026'];
            $legacyCategoryIds = Category::whereIn('slug', $legacySlugs)->pluck('id');

            $relinked = Channel::query()
                ->where(function ($q) use ($legacyCategoryIds) {
                    $q->where('name', 'like', '%beIN Sports Max%')
                        ->orWhere('name', 'like', '%Sports Max%')
                        ->orWhereIn('category_id', $legacyCategoryIds);
                })
                ->update([
                    'category_id' => $worldCup->id,
                    'package_id' => $package->id,
                ]);
        }

        $stats['relinked_world_cup_channels'] = $relinked;

        return $stats;
    }

    protected function safeDecrypt(EncryptionService $encryption, ?string $value): ?string
    {
        if (empty($value)) {
            return null;
        }
        try {
            return $encryption->decrypt($value);
        } catch (\Throwable) {
            return $value;
        }
    }

    protected function normalizeMediaForExport(?string $url): ?array
    {
        if (empty($url)) {
            return null;
        }

        if (str_starts_with($url, 'data:')) {
            return ['type' => 'url', 'value' => $url];
        }

        $path = $this->extractStoragePath($url);
        if ($path && Storage::disk('public')->exists($path)) {
            $mime = Storage::disk('public')->mimeType($path) ?: 'image/png';

            return [
                'type' => 'file',
                'path' => $path,
                'mime' => $mime,
                'data' => base64_encode(Storage::disk('public')->get($path)),
            ];
        }

        return ['type' => 'url', 'value' => $url];
    }

    protected function restoreMediaFromExport(?array $media, string $folder): ?string
    {
        if (empty($media)) {
            return null;
        }

        if (($media['type'] ?? '') === 'url') {
            $value = $media['value'] ?? null;
            if (empty($value)) {
                return null;
            }
            $path = $this->extractStoragePath($value);
            if ($path) {
                return '/storage/'.$path;
            }

            return $value;
        }

        if (($media['type'] ?? '') === 'file' && ! empty($media['data'])) {
            $ext = pathinfo($media['path'] ?? '', PATHINFO_EXTENSION) ?: 'png';
            $filename = Str::random(40).'.'.$ext;
            $dest = $folder.'/'.$filename;
            Storage::disk('public')->put($dest, base64_decode($media['data']));

            return '/storage/'.$dest;
        }

        return null;
    }

    protected function extractStoragePath(string $url): ?string
    {
        if (preg_match('#/storage/(.+)$#', $url, $m)) {
            return $m[1];
        }

        return null;
    }
}
