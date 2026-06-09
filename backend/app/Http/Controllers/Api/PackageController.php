<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PackageResource;
use App\Models\Package;
use App\Traits\HasApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use HasApiResponse;

    public function index(Request $request): JsonResponse
    {
        $categoryId = $request->query('category_id');

        $packages = Package::where('is_active', true)
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->withCount(['channels' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get();

        return $this->success(PackageResource::collection($packages), 'Packages retrieved successfully.');
    }
}
