@props(['title', 'description' => null, 'back' => null])
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-semibold mb-1">{{ $title }}</h4>
        @if ($description)<p class="text-muted mb-0">{{ $description }}</p>@endif
    </div>
    <div class="d-flex gap-2">
        @if ($back)<a href="{{ $back }}" class="btn btn-outline-secondary"><i class="ti ti-arrow-left me-1"></i>Kembali</a>@endif
        {{ $slot }}
    </div>
</div>
