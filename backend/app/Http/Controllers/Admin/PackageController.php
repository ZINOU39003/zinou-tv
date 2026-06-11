<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePackageRequest;
use App\Models\Category;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class PackageController extends Controller
{
    public function index(Request $request): View
    {
        $categoryId = $request->input('category_id');

        $packages = Package::when($categoryId, function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->withCount('channels')
            ->with('category')
            ->orderBy('sort_order')
            ->get();

        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.packages.index', compact('packages', 'categories', 'categoryId'));
    }

    public function create(): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.packages.create', compact('categories'));
    }

    public function store(StorePackageRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('packages', 'public');
            $data['logo_url'] = '/storage/'.$path;
        }
        Package::create($data);

        return redirect()->route('admin.packages.index')->with('success', 'تم إنشاء الباقة بنجاح.');
    }

    public function edit(Package $package): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.packages.edit', compact('package', 'categories'));
    }

    public function update(StorePackageRequest $request, Package $package): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image_file')) {
            if ($package->logo_url) {
                $oldPath = str_replace(asset('storage/'), '', $package->logo_url);
                if ($oldPath !== $package->logo_url) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('image_file')->store('packages', 'public');
            $data['logo_url'] = '/storage/'.$path;
        }
        $package->update($data);

        return redirect()->route('admin.packages.index')->with('success', 'تم تحديث الباقة بنجاح.');
    }

    public function destroy(Package $package): RedirectResponse
    {
        if ($package->channels()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف باقة تحتوي على قنوات. قم بنقل أو حذف القنوات أولاً.');
        }

        $package->delete();
        return redirect()->route('admin.packages.index')->with('success', 'تم حذف الباقة بنجاح.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $orders = $request->input('orders', []);

        foreach ($orders as $id => $order) {
            Package::where('id', $id)->update(['sort_order' => (int) $order]);
        }

        return redirect()->back()->with('success', 'تم تحديث ترتيب الباقات.');
    }

    /**
     * AJAX: Return packages for a given category (used in channel create/edit forms)
     */
    public function getByCategory(Request $request)
    {
        $categoryId = $request->input('category_id');
        $packages = Package::where('category_id', $categoryId)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'name_ar']);

        return response()->json($packages);
    }
}
