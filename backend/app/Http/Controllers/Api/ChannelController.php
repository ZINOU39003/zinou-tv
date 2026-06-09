<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Services\ChannelService;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChannelController extends Controller
{
    use HasApiResponse;

    protected ChannelService $channelService;

    public function __construct(ChannelService $channelService)
    {
        $this->channelService = $channelService;
    }

    public function index(Request $request): JsonResponse
    {
        ini_set('memory_limit', '512M');
        $categoryId = $request->query('category_id');
        $packageId = $request->query('package_id');
        $searchQuery = $request->query('search');

        if ($searchQuery) {
            $channels = $this->channelService->search($searchQuery);
        } elseif ($packageId) {
            $channels = $this->channelService->getByPackage((int) $packageId, $categoryId ? (int) $categoryId : null);
        } elseif ($categoryId) {
            $channels = $this->channelService->getByCategory($categoryId);
        } else {
            $channels = $this->channelService->getAllActive();
        }

        return $this->success(ChannelResource::collection($channels), 'Channels retrieved successfully.');
    }

    public function show(int $id): JsonResponse
    {
        $channel = Channel::where('id', $id)->where('is_active', true)->first();

        if (!$channel) {
            return $this->error('Channel not found.', 404);
        }

        return $this->success(new ChannelResource($channel), 'Channel retrieved successfully.');
    }
}
