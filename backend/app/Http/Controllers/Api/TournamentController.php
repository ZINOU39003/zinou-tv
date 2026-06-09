<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;

class TournamentController extends Controller
{
    use HasApiResponse;

    public function index(): JsonResponse
    {
        $tournaments = Tournament::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return $this->success($tournaments, 'Tournaments retrieved successfully.');
    }
}
