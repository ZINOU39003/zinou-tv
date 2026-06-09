@extends('admin.layouts.app')

@section('title', 'Create Match')
@section('header_title', 'Create Match')
@section('header_subtitle', 'Add a new live match with team and stream details')

@section('content')

    <div class="card" style="max-width: 700px; margin: 0 auto;">
        <form action="{{ route('admin.matches.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="tournament_id">Tournament</label>
                <select id="tournament_id" name="tournament_id" class="form-control" required>
                    <option value="">Select Tournament</option>
                    @foreach($tournaments as $tournament)
                        <option value="{{ $tournament->id }}" {{ old('tournament_id') == $tournament->id ? 'selected' : '' }}>{{ $tournament->name }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label for="team_one_name">Team 1 Name (English)</label>
                    <input type="text" id="team_one_name" name="team_one_name" class="form-control" placeholder="Brazil" required value="{{ old('team_one_name') }}">
                </div>

                <div class="form-group">
                    <label for="team_one_name_ar">Team 1 Name (Arabic)</label>
                    <input type="text" id="team_one_name_ar" name="team_one_name_ar" class="form-control" placeholder="البرازيل" value="{{ old('team_one_name_ar') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="team_one_flag">Team 1 Flag URL</label>
                <input type="url" id="team_one_flag" name="team_one_flag" class="form-control" placeholder="https://flagcdn.com/w80/br.png" value="{{ old('team_one_flag') }}">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label for="team_two_name">Team 2 Name (English)</label>
                    <input type="text" id="team_two_name" name="team_two_name" class="form-control" placeholder="Argentina" required value="{{ old('team_two_name') }}">
                </div>

                <div class="form-group">
                    <label for="team_two_name_ar">Team 2 Name (Arabic)</label>
                    <input type="text" id="team_two_name_ar" name="team_two_name_ar" class="form-control" placeholder="الأرجنتين" value="{{ old('team_two_name_ar') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="team_two_flag">Team 2 Flag URL</label>
                <input type="url" id="team_two_flag" name="team_two_flag" class="form-control" placeholder="https://flagcdn.com/w80/ar.png" value="{{ old('team_two_flag') }}">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label for="team_one_score">Team 1 Score</label>
                    <input type="number" id="team_one_score" name="team_one_score" class="form-control" min="0" value="{{ old('team_one_score', 0) }}">
                </div>

                <div class="form-group">
                    <label for="team_two_score">Team 2 Score</label>
                    <input type="number" id="team_two_score" name="team_two_score" class="form-control" min="0" value="{{ old('team_two_score', 0) }}">
                </div>

                <div class="form-group">
                    <label for="match_time">Match Time</label>
                    <input type="text" id="match_time" name="match_time" class="form-control" placeholder="45:00 or FT or HT" required value="{{ old('match_time', '00:00') }}">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label for="match_date">Match Date</label>
                    <input type="date" id="match_date" name="match_date" class="form-control" value="{{ old('match_date') }}">
                </div>

                <div class="form-group">
                    <label for="sort_order">Display Sort Order</label>
                    <input type="number" id="sort_order" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}">
                </div>
            </div>

            <div class="form-group">
                <label for="stream_url">Stream URL</label>
                <input type="url" id="stream_url" name="stream_url" class="form-control" placeholder="https://stream.example.com/live/match/index.m3u8" value="{{ old('stream_url') }}">
            </div>

            <div style="display:flex; gap:24px; margin-top:24px;">
                <div class="form-group" style="display:flex; align-items:center; gap:10px;">
                    <input type="checkbox" id="is_live" name="is_live" value="1" {{ old('is_live') ? 'checked' : '' }} style="width:20px; height:20px; accent-color:var(--danger);">
                    <label for="is_live" style="margin-bottom:0; cursor:pointer;">🔴 Live Now</label>
                </div>

                <div class="form-group" style="display:flex; align-items:center; gap:10px;">
                    <input type="checkbox" id="is_world_cup" name="is_world_cup" value="1" {{ old('is_world_cup') ? 'checked' : '' }} style="width:20px; height:20px; accent-color:var(--warning);">
                    <label for="is_world_cup" style="margin-bottom:0; cursor:pointer;">🏆 World Cup Match</label>
                </div>

                <div class="form-group" style="display:flex; align-items:center; gap:10px;">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked style="width:20px; height:20px; accent-color:var(--accent-primary);">
                    <label for="is_active" style="margin-bottom:0; cursor:pointer;">Active</label>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px; border-top:1px solid var(--border-glass); padding-top:20px;">
                <a href="{{ route('admin.matches.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Match</button>
            </div>
        </form>
    </div>

@endsection
