<?php

namespace App\Services;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Collection;

class ChannelService
{
    protected EncryptionService $encryptionService;

    public function __construct(EncryptionService $encryptionService)
    {
        $this->encryptionService = $encryptionService;
    }

    public function getAllActive(): Collection
    {
        return Channel::where('is_active', true)
            ->with(['servers' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get();
    }

    public function getByCategory(int $categoryId): Collection
    {
        return Channel::where('category_id', $categoryId)
            ->where('is_active', true)
            ->with(['servers' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get();
    }

    public function getByPackage(int $packageId, ?int $categoryId = null): Collection
    {
        return Channel::where('is_active', true)
            ->where(function ($query) use ($packageId, $categoryId) {
                $query->where('package_id', $packageId);
                if ($categoryId) {
                    $query->orWhere(function ($q) use ($categoryId) {
                        $q->where('category_id', $categoryId)->whereNull('package_id');
                    });
                }
            })
            ->with(['servers' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get();
    }

    public function search(string $query): Collection
    {
        return Channel::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('name_ar', 'like', "%{$query}%");
            })
            ->with(['servers' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get();
    }

    public function createChannel(array $data): Channel
    {
        $servers = $data['servers'] ?? null;
        unset($data['servers']);

        // Encrypt the URLs
        if (isset($data['stream_url'])) {
            $data['stream_url'] = $this->encryptionService->encrypt($data['stream_url']);
        }
        if (isset($data['backup_url']) && !empty($data['backup_url'])) {
            $data['backup_url'] = $this->encryptionService->encrypt($data['backup_url']);
        }

        $channel = Channel::create($data);

        if ($servers !== null) {
            $this->syncServers($channel, $servers);
        }

        return $channel;
    }

    public function updateChannel(Channel $channel, array $data): Channel
    {
        $servers = $data['servers'] ?? null;
        unset($data['servers']);

        if (isset($data['stream_url'])) {
            $data['stream_url'] = $this->encryptionService->encrypt($data['stream_url']);
        }
        if (isset($data['backup_url'])) {
            $data['backup_url'] = !empty($data['backup_url']) ? $this->encryptionService->encrypt($data['backup_url']) : null;
        }

        $channel->update($data);

        if ($servers !== null) {
            $this->syncServers($channel, $servers);
        }

        return $channel;
    }

    public function syncServers(Channel $channel, ?array $servers): void
    {
        $channel->servers()->delete();

        if (empty($servers)) {
            return;
        }

        foreach ($servers as $index => $serverData) {
            if (empty($serverData['name']) || empty($serverData['stream_url'])) {
                continue;
            }

            $channel->servers()->create([
                'name' => $serverData['name'],
                'stream_url' => $this->encryptionService->encrypt($serverData['stream_url']),
                'stream_type' => $serverData['stream_type'] ?? 'm3u8',
                'quality' => $serverData['quality'] ?? 'HD',
                'sort_order' => isset($serverData['sort_order']) ? (int) $serverData['sort_order'] : $index,
                'is_active' => true,
            ]);
        }
    }

    public function getDecryptedStreamUrl(Channel $channel): string
    {
        return $this->encryptionService->decrypt($channel->stream_url);
    }

    public function getDecryptedBackupUrl(Channel $channel): ?string
    {
        return $channel->backup_url ? $this->encryptionService->decrypt($channel->backup_url) : null;
    }
}
