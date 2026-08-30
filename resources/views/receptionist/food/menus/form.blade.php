@php($item = $menuItem ?? null)
<div class="row g-3">
    <div class="col-md-6"><label class="form-label">Nama menu <span class="text-danger">*</span></label><input name="name" value="{{ old('name', $item?->name) }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-3"><label class="form-label">Kategori</label><select name="food_category_id" class="form-select"><option value="">Tanpa kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected((string)old('food_category_id', $item?->food_category_id) === (string)$category->id)>{{ $category->name }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">Slug</label><input name="slug" value="{{ old('slug', $item?->slug) }}" class="form-control" placeholder="Otomatis"></div>
    <div class="col-md-4"><label class="form-label">Harga <span class="text-danger">*</span></label><div class="input-group"><span class="input-group-text">Rp</span><input type="number" min="0" step="1" name="price" value="{{ old('price', $item?->price) }}" class="form-control @error('price') is-invalid @enderror" required></div>@error('price')<div class="text-danger small">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label class="form-label">Estimasi persiapan</label><div class="input-group"><input type="number" min="1" name="preparation_minutes" value="{{ old('preparation_minutes', $item?->preparation_minutes) }}" class="form-control"><span class="input-group-text">menit</span></div></div>
    <div class="col-md-4"><label class="form-label">Urutan</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $item?->sort_order ?? 0) }}" class="form-control"></div>
    <div class="col-md-8"><label class="form-label">Deskripsi</label><textarea name="description" rows="4" class="form-control">{{ old('description', $item?->description) }}</textarea></div>
    <div class="col-md-4">
        <label class="form-label">Foto menu</label>
        <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="form-control @error('image') is-invalid @enderror">
        @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if ($item && $item->image_path)
            <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}" class="rounded mt-2" style="width:100%;height:100px;object-fit:cover">
        @endif
    </div>
    <div class="col-12 d-flex gap-4"><div class="form-check form-switch"><input type="hidden" name="is_available" value="0"><input class="form-check-input" type="checkbox" name="is_available" value="1" id="is_available" @checked(old('is_available', $item?->is_available ?? true))><label class="form-check-label" for="is_available">Tersedia dipesan</label></div><div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $item?->is_active ?? true))><label class="form-check-label" for="is_active">Aktif</label></div></div>
</div>
