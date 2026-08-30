@props(['cancel', 'label' => 'Simpan'])
<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="{{ $cancel }}" class="btn btn-outline-secondary">Batal</a>
    <button type="submit" class="btn btn-primary"><i class="ti ti-device-floppy me-1"></i>{{ $label }}</button>
</div>
