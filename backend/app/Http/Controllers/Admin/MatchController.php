<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SportMatch;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class MatchController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $tournamentId = $request->input('tournament_id');

        $matches = SportMatch::when($search, function ($query) use ($search) {
                $query->where('team_one_name', 'like', "%{$search}%")
                      ->orWhere('team_two_name', 'like', "%{$search}%")
                      ->orWhere('team_one_name_ar', 'like', "%{$search}%")
                      ->orWhere('team_two_name_ar', 'like', "%{$search}%");
            })
            ->when($tournamentId, function ($query) use ($tournamentId) {
                $query->where('tournament_id', $tournamentId);
            })
            ->with('tournament')
            ->orderBy('sort_order')
            ->paginate(15);

        $tournaments = Tournament::where('is_active', true)->orderBy('sort_order')->get();

        return view('admin.matches.index', compact('matches', 'tournaments', 'search', 'tournamentId'));
    }

    public function create(): View
    {
        $tournaments = Tournament::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.matches.create', compact('tournaments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'team_one_name' => 'required|max:255',
            'team_one_name_ar' => 'nullable|max:255',
            'team_one_flag' => 'nullable|url',
            'team_two_name' => 'required|max:255',
            'team_two_name_ar' => 'nullable|max:255',
            'team_two_flag' => 'nullable|url',
            'team_one_score' => 'integer|min:0',
            'team_two_score' => 'integer|min:0',
            'match_time' => 'required|max:10',
            'match_date' => 'nullable|date',
            'is_live' => 'boolean',
            'is_world_cup' => 'boolean',
            'stream_url' => 'nullable|url',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['is_live'] = $request->has('is_live');
        $validated['is_world_cup'] = $request->has('is_world_cup');
        $validated['is_active'] = $request->has('is_active');

        SportMatch::create($validated);

        return redirect()->route('admin.matches.index')->with('success', 'Match created successfully.');
    }

    public function edit(SportMatch $match): View
    {
        $tournaments = Tournament::where('is_active', true)->orderBy('sort_order')->get();
        return view('admin.matches.edit', compact('match', 'tournaments'));
    }

    public function update(Request $request, SportMatch $match): RedirectResponse
    {
        $validated = $request->validate([
            'tournament_id' => 'required|exists:tournaments,id',
            'team_one_name' => 'required|max:255',
            'team_one_name_ar' => 'nullable|max:255',
            'team_one_flag' => 'nullable|url',
            'team_two_name' => 'required|max:255',
            'team_two_name_ar' => 'nullable|max:255',
            'team_two_flag' => 'nullable|url',
            'team_one_score' => 'integer|min:0',
            'team_two_score' => 'integer|min:0',
            'match_time' => 'required|max:10',
            'match_date' => 'nullable|date',
            'is_live' => 'boolean',
            'is_world_cup' => 'boolean',
            'stream_url' => 'nullable|url',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['is_live'] = $request->has('is_live');
        $validated['is_world_cup'] = $request->has('is_world_cup');
        $validated['is_active'] = $request->has('is_active');

        $match->update($validated);

        return redirect()->route('admin.matches.index')->with('success', 'Match updated successfully.');
    }

    public function destroy(SportMatch $match): RedirectResponse
    {
        $match->delete();
        return redirect()->route('admin.matches.index')->with('success', 'تم حذف المباراة بنجاح.');
    }

    public function destroyAll(): RedirectResponse
    {
        $count = SportMatch::count();
        SportMatch::truncate();
        return redirect()->route('admin.matches.index')->with('success', "تم حذف جميع المباريات ({$count} مباراة) بنجاح.");
    }
}
