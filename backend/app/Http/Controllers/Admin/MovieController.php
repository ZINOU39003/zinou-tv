<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class MovieController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $movies = Movie::when($search, function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('title_ar', 'like', "%{$search}%");
            })
            ->when($type, function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->orderBy('sort_order')
            ->paginate(15);

        return view('admin.movies.index', compact('movies', 'search', 'type'));
    }

    public function create(): View
    {
        return view('admin.movies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'title_ar' => 'nullable|max:255',
            'poster_url' => 'nullable|url',
            'type' => 'required|in:movie,series',
            'stream_url' => 'nullable|url',
            'description' => 'nullable',
            'description_ar' => 'nullable',
            'year' => 'nullable|integer|min:1900|max:2099',
            'rating' => 'nullable|numeric|min:0|max:10',
            'is_latest' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['is_latest'] = $request->has('is_latest');
        $validated['is_active'] = $request->has('is_active');

        Movie::create($validated);

        return redirect()->route('admin.movies.index')->with('success', 'Movie created successfully.');
    }

    public function edit(Movie $movie): View
    {
        return view('admin.movies.edit', compact('movie'));
    }

    public function update(Request $request, Movie $movie): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'title_ar' => 'nullable|max:255',
            'poster_url' => 'nullable|url',
            'type' => 'required|in:movie,series',
            'stream_url' => 'nullable|url',
            'description' => 'nullable',
            'description_ar' => 'nullable',
            'year' => 'nullable|integer|min:1900|max:2099',
            'rating' => 'nullable|numeric|min:0|max:10',
            'is_latest' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['is_latest'] = $request->has('is_latest');
        $validated['is_active'] = $request->has('is_active');

        $movie->update($validated);

        return redirect()->route('admin.movies.index')->with('success', 'Movie updated successfully.');
    }

    public function destroy(Movie $movie): RedirectResponse
    {
        $movie->delete();
        return redirect()->route('admin.movies.index')->with('success', 'Movie deleted successfully.');
    }
}
