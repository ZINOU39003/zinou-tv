@extends('admin.layouts.app')

@section('title', 'Edit Movie - ' . $movie->title)
@section('header_title', 'Edit Movie / Series')
@section('header_subtitle', 'Update movie details and streaming source')

@section('content')

    <div class="card" style="max-width: 700px; margin: 0 auto;">
        <form action="{{ route('admin.movies.update', $movie->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label for="title">Title (English)</label>
                    <input type="text" id="title" name="title" class="form-control" required value="{{ old('title', $movie->title) }}">
                </div>

                <div class="form-group">
                    <label for="title_ar">Title (Arabic)</label>
                    <input type="text" id="title_ar" name="title_ar" class="form-control" value="{{ old('title_ar', $movie->title_ar) }}">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                <div class="form-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" class="form-control" required>
                        <option value="movie" {{ old('type', $movie->type) == 'movie' ? 'selected' : '' }}>Movie</option>
                        <option value="series" {{ old('type', $movie->type) == 'series' ? 'selected' : '' }}>Series</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="year">Year</label>
                    <input type="number" id="year" name="year" class="form-control" min="1900" max="2099" value="{{ old('year', $movie->year) }}">
                </div>
            </div>

            <div style="display:grid; grid-template-columns:2fr 1fr; gap:20px;">
                <div class="form-group">
                    <label for="poster_url">Poster URL</label>
                    <input type="url" id="poster_url" name="poster_url" class="form-control" value="{{ old('poster_url', $movie->poster_url) }}">
                </div>

                <div class="form-group">
                    <label for="rating">Rating (0-10)</label>
                    <input type="number" id="rating" name="rating" class="form-control" min="0" max="10" step="0.1" value="{{ old('rating', $movie->rating) }}">
                </div>
            </div>

            <div class="form-group">
                <label for="stream_url">Stream URL</label>
                <input type="url" id="stream_url" name="stream_url" class="form-control" value="{{ old('stream_url', $movie->stream_url) }}">
            </div>

            <div class="form-group">
                <label for="description">Description (English)</label>
                <textarea id="description" name="description" class="form-control" rows="3">{{ old('description', $movie->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="description_ar">Description (Arabic)</label>
                <textarea id="description_ar" name="description_ar" class="form-control" rows="3">{{ old('description_ar', $movie->description_ar) }}</textarea>
            </div>

            <div class="form-group">
                <label for="sort_order">Display Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" class="form-control" value="{{ old('sort_order', $movie->sort_order) }}">
            </div>

            <div style="display:flex; gap:24px; margin-top:24px;">
                <div class="form-group" style="display:flex; align-items:center; gap:10px;">
                    <input type="hidden" name="is_latest" value="0">
                    <input type="checkbox" id="is_latest" name="is_latest" value="1" {{ old('is_latest', $movie->is_latest) ? 'checked' : '' }} style="width:20px; height:20px; accent-color:var(--warning);">
                    <label for="is_latest" style="margin-bottom:0; cursor:pointer;">🆕 Mark as Latest</label>
                </div>

                <div class="form-group" style="display:flex; align-items:center; gap:10px;">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $movie->is_active) ? 'checked' : '' }} style="width:20px; height:20px; accent-color:var(--accent-primary);">
                    <label for="is_active" style="margin-bottom:0; cursor:pointer;">Active and visible</label>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px; border-top:1px solid var(--border-glass); padding-top:20px;">
                <a href="{{ route('admin.movies.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Movie</button>
            </div>
        </form>
    </div>

@endsection
