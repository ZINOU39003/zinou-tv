<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    use HasApiResponse;

    public function index(Request $request): JsonResponse
    {
        $movies = Movie::where('is_active', true)
            ->when($request->query('type'), function ($query) use ($request) {
                $query->where('type', $request->query('type'));
            })
            ->when($request->query('is_latest'), function ($query) {
                $query->where('is_latest', true);
            })
            ->orderBy('sort_order')
            ->get();

        return $this->success($movies, 'Movies retrieved successfully.');
    }
}
