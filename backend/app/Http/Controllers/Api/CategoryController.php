<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use HasApiResponse;

    public function index(Request $request): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->withCount(['channels' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get();

        return $this->success(CategoryResource::collection($categories), 'Categories retrieved successfully.');
    }
}
