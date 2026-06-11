<div class="grid-2">
    <div class="form-group">
        <label for="category_id">الشبكة *</label>
        <select id="category_id" name="category_id" class="form-control" required>
            <option value="">اختر الشبكة...</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ (string) old('category_id', $selectedCategoryId ?? '') === (string) $category->id ? 'selected' : '' }}>
                    {{ $category->name_ar ?: $category->name }}
                    @if($category->name_ar && $category->name != $category->name_ar)
                        ({{ $category->name }})
                    @endif
                </option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="package_id">الباقة</label>
        <select id="package_id" name="package_id" class="form-control">
            <option value="">اختر الباقة...</option>
            @foreach($packages as $package)
                @if(!isset($selectedCategoryId) || (string) $package->category_id === (string) ($selectedCategoryId ?? old('category_id')))
                    <option value="{{ $package->id }}" data-category="{{ $package->category_id }}" {{ (string) old('package_id', $selectedPackageId ?? '') === (string) $package->id ? 'selected' : '' }}>
                        {{ $package->name_ar ?: $package->name }}
                    </option>
                @endif
            @endforeach
        </select>
        <span style="font-size:11px; color:var(--text-muted); display:block; margin-top:4px;">
            اختر الشبكة أولاً ثم الباقة التابعة لها.
        </span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('category_id');
    const packageSelect = document.getElementById('package_id');
    if (!categorySelect || !packageSelect) return;

    const packagesUrl = @json(route('admin.packages.by-category'));

    async function loadPackages(categoryId, selectedPackageId) {
        packageSelect.innerHTML = '<option value="">اختر الباقة...</option>';
        if (!categoryId) return;

        try {
            const res = await fetch(packagesUrl + '?category_id=' + encodeURIComponent(categoryId), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const packages = await res.json();
            packages.forEach(function (pkg) {
                const opt = document.createElement('option');
                opt.value = pkg.id;
                opt.textContent = pkg.name_ar || pkg.name;
                if (String(selectedPackageId) === String(pkg.id)) opt.selected = true;
                packageSelect.appendChild(opt);
            });
        } catch (e) {
            console.error('Failed to load packages', e);
        }
    }

    categorySelect.addEventListener('change', function () {
        loadPackages(this.value, '');
    });

    if (categorySelect.value) {
        loadPackages(categorySelect.value, @json(old('package_id', $selectedPackageId ?? '')));
    }
});
</script>
