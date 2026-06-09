<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\StatisticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected StatisticsService $statisticsService;

    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function index(): View
    {
        $stats = $this->statisticsService->getDashboardStats();
        
        // Fetch recent logs
        $recentLogs = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentLogs'));
    }

    public function bulkDelete(Request $request): JsonResponse
    {
        $type = $request->input('type');
        $ids = $request->input('ids', []);

        if (empty($type) || empty($ids) || !is_array($ids)) {
            return response()->json(['success' => false, 'message' => 'لم يتم تحديد أي عناصر للحذف.'], 400);
        }

        $modelClass = match($type) {
            'channels' => \App\Models\Channel::class,
            'categories' => \App\Models\Category::class,
            'users' => \App\Models\User::class,
            'tournaments' => \App\Models\Tournament::class,
            'matches' => \App\Models\Match::class,
            'movies' => \App\Models\Movie::class,
            default => null,
        };

        if (!$modelClass) {
            return response()->json(['success' => false, 'message' => 'نوع المحتوى غير مدعوم.'], 400);
        }

        try {
            if ($type === 'categories') {
                $hasChannels = \App\Models\Channel::whereIn('category_id', $ids)->exists();
                if ($hasChannels) {
                    return response()->json(['success' => false, 'message' => 'لا يمكن حذف بعض التصنيفات المحددة لأنها تحتوي على قنوات نشطة. يرجى إزالة القنوات أولاً.'], 400);
                }
            }

            if ($type === 'users') {
                // Check if deleting admin accounts
                $hasAdmin = \App\Models\User::whereIn('id', $ids)->where('role', \App\Enums\UserRole::ADMIN)->exists();
                if ($hasAdmin) {
                    return response()->json(['success' => false, 'message' => 'لا يمكن حذف حسابات المدراء.'], 400);
                }
            }

            $modelClass::whereIn('id', $ids)->delete();

            return response()->json(['success' => true, 'message' => 'تم حذف العناصر المحددة بنجاح.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()], 500);
        }
    }
}
