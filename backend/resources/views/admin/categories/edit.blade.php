@extends('admin.layouts.app')

@section('title', 'Edit Category - ' . $category->name)
@section('header_title', 'Edit Category')
@section('header_subtitle', 'Update group classification details')

@section('content')

    <div class="card" style="max-width: 600px; margin: 0 auto;">
        <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Category Name (English)</label>
                <input type="text" id="name" name="name" class="form-control" required value="{{ old('name', $category->name) }}">
            </div>

            <div class="form-group">
                <label for="name_ar">Category Name (Arabic)</label>
                <input type="text" id="name_ar" name="name_ar" class="form-control" required value="{{ old('name_ar', $category->name_ar) }}">
            </div>

            <div class="form-group">
                <label for="slug">URL Slug</label>
                <input type="text" id="slug" name="slug" class="form-control" required value="{{ old('slug', $category->slug) }}">
            </div>

            <div class="form-group">
                <label for="type">Classification Type</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="content_type" {{ old('type', $category->type) == 'content_type' ? 'selected' : '' }}>Content Type (نوع المحتوى)</option>
                    <option value="country" {{ old('type', $category->type) == 'country' ? 'selected' : '' }}>Country (الدولة)</option>
                    <option value="language" {{ old('type', $category->type) == 'language' ? 'selected' : '' }}>Language (اللغة)</option>
                    <option value="continent" {{ old('type', $category->type) == 'continent' ? 'selected' : '' }}>Continent (القارة)</option>
                    <option value="network" {{ old('type', $category->type) == 'network' ? 'selected' : '' }}>Network / Brand (الشبكات والشركات الباثة)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="icon">Icon Name (Material Design icon tag or image URL)</label>
                <input type="text" id="icon" name="icon" class="form-control" value="{{ old('icon', $category->icon) }}">
            </div>

            <div class="form-group">
                <label for="image_file">Upload New Image File (Optional, overrides Icon text)</label>
                <input type="file" id="image_file" name="image_file" class="form-control" accept="image/*">
                @if($category->icon)
                    <div style="margin-top: 10px;">
                        <span style="font-size: 12px; color: var(--text-muted); display: block; margin-bottom: 5px;">Current Image / Icon:</span>
                        <div>
                            @if(filter_var($category->icon, FILTER_VALIDATE_URL) || str_starts_with($category->icon, 'http'))
                                <img src="{{ $category->icon }}" alt="Current Icon" style="max-height: 80px; border-radius: 8px; border: 1px solid var(--border-glass); padding: 5px; background: rgba(255,255,255,0.05);">
                            @else
                                <span style="font-family: monospace; font-size: 14px; color: var(--accent-primary);">{{ $category->icon }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="form-group">
                <label for="sort_order">Display Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" class="form-control" required value="{{ old('sort_order', $category->sort_order) }}">
            </div>

            <div class="form-group" style="display:flex; align-items:center; gap:10px; margin-top: 24px;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }} style="width:20px; height:20px; accent-color:var(--accent-primary);">
                <label for="is_active" style="margin-bottom:0; cursor:pointer;">Active and available on client applications</label>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:32px; border-top:1px solid var(--border-glass); padding-top:20px;">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Category</button>
            </div>
        </form>
    </div>

@endsection
