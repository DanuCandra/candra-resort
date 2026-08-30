@php($category = $foodCategory ?? null)
<div class="row g-3">
    <div class="col-md-7"><label class="form-label">Nama kategori <span class="text-danger">*</span></label><input name="name" value="{{ old('name', $category?->name) }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-5"><label class="form-label">Slug</label><input name="slug" value="{{ old('slug', $category?->slug) }}" class="form-control @error('slug') is-invalid @enderror" placeholder="Otomatis dari nama">@error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-9"><label class="form-label">Deskripsi</label><textarea name="description" rows="4" class="form-control">{{ old('description', $category?->description) }}</textarea></div>
    <div class="col-md-3"><label class="form-label">Urutan</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $category?->sort_order ?? 0) }}" class="form-control"><div class="form-check form-switch mt-4"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $category?->is_active ?? true))><label class="form-check-label" for="is_active">Aktif</label></div></div>
</div>
