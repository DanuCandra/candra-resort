<div class="card report-filter-card">
    <div class="card-body py-3">
        <form action="{{ $filterRoute }}" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label class="form-label">Periode</label>
                <select name="period" id="report-period" class="form-select">
                    @foreach ($periodOptions as $value => $label)
                        <option value="{{ $value }}" @selected($period->preset === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-6 report-custom-date">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $period->start->format('Y-m-d')) }}">
            </div>
            <div class="col-lg-2 col-md-6 report-custom-date">
                <label class="form-label">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $period->end->format('Y-m-d')) }}">
            </div>
            <div class="col-lg-auto col-md-6">
                <button type="submit" class="btn btn-primary w-100"><i class="ti ti-filter me-1"></i>Terapkan</button>
            </div>
            @if ($showActions ?? true)
                <div class="col-lg-auto ms-lg-auto d-flex gap-2 no-print">
                    <button type="button" onclick="window.print()" class="btn btn-outline-secondary"><i class="ti ti-printer me-1"></i>Cetak</button>
                    <a href="{{ route('owner.reports.export', array_merge(['report' => $type], request()->only('period', 'start_date', 'end_date'))) }}" class="btn btn-success">
                        <i class="ti ti-file-spreadsheet me-1"></i>CSV
                    </a>
                </div>
            @endif
        </form>
        @error('end_date')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const period = document.querySelector('#report-period');
            const customFields = document.querySelectorAll('.report-custom-date');
            const syncCustomDates = () => customFields.forEach(field => {
                field.style.display = period && period.value === 'custom' ? '' : 'none';
            });
            if (period) period.addEventListener('change', syncCustomDates);
            syncCustomDates();
        });
    </script>
@endpush
