@extends('layouts.main')

@section('title', $title)

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2 mb-4 report-heading">
        <div>
            <h4 class="fw-semibold mb-1">{{ $title }}</h4>
            <p class="text-muted mb-0">{{ $description }}</p>
        </div>
        <span class="badge bg-light-primary text-primary px-3 py-2">{{ $period->label() }}</span>
    </div>

    @include('owner.reports.partials.filter', ['filterRoute' => url()->current()])
    @include('owner.reports.partials.'.$type)
@endsection

@push('scripts')
    <script src="{{ asset('dashboard/assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const target = document.querySelector('#owner-report-chart');
            if (!target || typeof ApexCharts === 'undefined') return;
            new ApexCharts(target, JSON.parse(target.dataset.chart)).render();
        });
    </script>
    <style>
        @media print {
            .left-sidebar, .topbar, .report-filter-card, .no-print, .dark-transparent { display: none !important; }
            .page-wrapper, .body-wrapper { margin-left: 0 !important; padding-top: 0 !important; }
            .container-fluid { max-width: none !important; padding: 0 !important; }
            .card { box-shadow: none !important; break-inside: avoid; border: 1px solid #ddd; }
            .report-heading { margin-top: 0 !important; }
        }
    </style>
@endpush
