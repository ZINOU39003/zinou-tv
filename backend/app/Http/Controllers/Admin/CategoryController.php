<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('channels')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $data['icon'] = asset('storage/' . $path);
        }
        Category::create($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image_file')) {
            if ($category->icon) {
                $oldPath = str_replace(asset('storage/'), '', $category->icon);
                if ($oldPath !== $category->icon) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }
            $path = $request->file('image_file')->store('categories', 'public');
            $data['icon'] = asset('storage/' . $path);
        }
        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->channels()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category that contains channels. Reassign or delete channels first.');
        }

        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }

    public function reorder(Request $request): RedirectResponse
    {
        $orders = $request->input('orders', []);
        
        foreach ($orders as $id => $order) {
            Category::where('id', $id)->update(['sort_order' => (int) $order]);
        }

        return redirect()->back()->with('success', 'Category order updated.');
    }
}
