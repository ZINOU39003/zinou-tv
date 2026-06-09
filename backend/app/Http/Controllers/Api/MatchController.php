<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SportMatch;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    use HasApiResponse;

    public function index(Request $request): JsonResponse
    {
        $isLive = $request->query('is_live');
        $date = $request->query('date');

        $isWorldCup = $request->query('is_world_cup');

        $matches = SportMatch::where('is_active', true)
            ->when($date, function ($query) use ($date) {
                $query->where('match_date', $date);
            })
            ->when(!$date && !$isLive && !$isWorldCup, function ($query) {
                $query->where('match_date', date('Y-m-d'));
            })
            ->when($isLive, function ($query) {
                $query->where('is_live', true);
            })
            ->when($isWorldCup, function ($query) {
                $query->where('is_world_cup', true);
            })
            ->when($request->query('tournament_id'), function ($query) use ($request) {
                $query->where('tournament_id', $request->query('tournament_id'));
            })
            ->with(['tournament', 'channel'])
            ->orderBy('sort_order')
            ->orderBy('match_time')
            ->get();

        return $this->success($matches, 'Matches retrieved successfully.');
    }
}
