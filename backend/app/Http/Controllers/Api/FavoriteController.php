<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Models\Favorite;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use HasApiResponse;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user() ?: \App\Models\User::first();
        if (!$user) {
            $user = new \App\Models\User();
            $user->id = 1;
        }

        // Get channels favorited by user
        $favoriteChannels = Channel::whereHas('favorites', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('is_active', true)->get();

        return $this->success(ChannelResource::collection($favoriteChannels), 'Favorite channels retrieved successfully.');
    }

    public function store(Request $request, int $channelId): JsonResponse
    {
        $user = $request->user() ?: \App\Models\User::first();
        if (!$user) {
            $user = new \App\Models\User();
            $user->id = 1;
        }

        $channel = Channel::where('id', $channelId)->where('is_active', true)->first();

        if (!$channel) {
            return $this->error('Channel not found or inactive.', 404);
        }

        // Add to favorite if not already favorited
        Favorite::firstOrCreate([
            'user_id' => $user->id,
            'channel_id' => $channelId,
        ]);

        return $this->success(null, 'Channel added to favorites.');
    }

    public function destroy(Request $request, int $channelId): JsonResponse
    {
        $user = $request->user() ?: \App\Models\User::first();
        if (!$user) {
            $user = new \App\Models\User();
            $user->id = 1;
        }

        $favorite = Favorite::where('user_id', $user->id)->where('channel_id', $channelId)->first();

        if (!$favorite) {
            return $this->error('Channel is not in favorites.', 404);
        }

        $favorite->delete();

        return $this->success(null, 'Channel removed from favorites.');
    }
}
