<ul id="sidebarnav">
    <li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">Ringkasan Bisnis</span></li>
    <li class="sidebar-item">
        <a class="sidebar-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}" href="{{ route('owner.dashboard') }}">
            <span><i class="ti ti-layout-dashboard"></i></span><span class="hide-menu">Dashboard</span>
        </a>
    </li>
    @foreach ([
        ['Laporan Reservasi', 'ti-calendar-stats', 'owner.reports.reservations'],
        ['Laporan Okupansi', 'ti-chart-pie', 'owner.reports.occupancy'],
        ['Laporan Pendapatan', 'ti-chart-line', 'owner.reports.revenue'],
        ['Laporan Pembayaran', 'ti-credit-card', 'owner.reports.payments'],
        ['Laporan Layanan', 'ti-report-analytics', 'owner.reports.services'],
        ['Laporan Bulanan', 'ti-calendar-month', 'owner.reports.monthly'],
    ] as [$label, $icon, $route])
        <li class="sidebar-item">
            <a class="sidebar-link {{ request()->routeIs($route) ? 'active' : '' }}" href="{{ route($route) }}">
                <span><i class="ti {{ $icon }}"></i></span><span class="hide-menu">{{ $label }}</span>
            </a>
        </li>
    @endforeach
    <li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">Manajemen</span></li>
    <li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('owner.receptionists.*')?'active':'' }}" href="{{ route('owner.receptionists.index') }}"><span><i class="ti ti-user-cog"></i></span><span class="hide-menu">Receptionist</span></a></li>
</ul>
