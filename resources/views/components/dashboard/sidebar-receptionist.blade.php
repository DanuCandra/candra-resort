<ul id="sidebarnav">
    <li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">Operasional</span></li>
    <li class="sidebar-item">
        <a class="sidebar-link {{ request()->routeIs('receptionist.dashboard') ? 'active' : '' }}" href="{{ route('receptionist.dashboard') }}">
            <span><i class="ti ti-layout-dashboard"></i></span><span class="hide-menu">Dashboard</span>
        </a>
    </li>
    @foreach ([
        ['receptionist.reservations.*','receptionist.reservations.index','Reservasi','ti-calendar-event'],
        ['receptionist.checkin.*','receptionist.checkin.index','Check-In','ti-login'],
        ['receptionist.checkout.*','receptionist.checkout.index','Check-Out','ti-logout'],
    ] as [$pattern,$routeName,$label,$icon])
        <li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs($pattern)?'active':'' }}" href="{{ route($routeName) }}"><span><i class="ti {{ $icon }}"></i></span><span class="hide-menu">{{ $label }}</span></a></li>
    @endforeach
    <li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('receptionist.guests.*') ? 'active' : '' }}" href="{{ route('receptionist.guests.index') }}"><span><i class="ti ti-users"></i></span><span class="hide-menu">Tamu</span></a></li>

    <li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">Kamar & Harga</span></li>
    @foreach ([
        ['receptionist.room-types.*', 'receptionist.room-types.index', 'Tipe Kamar', 'ti-bed'],
        ['receptionist.rooms.*', 'receptionist.rooms.index', 'Kamar & QR', 'ti-door'],
        ['receptionist.facilities.*', 'receptionist.facilities.index', 'Fasilitas', 'ti-building-community'],
        ['receptionist.pricing.*', 'receptionist.pricing.index', 'Aturan Harga', 'ti-currency-dollar'],
        ['receptionist.promotions.*', 'receptionist.promotions.index', 'Promosi', 'ti-tags'],
    ] as [$pattern, $routeName, $label, $icon])
        <li class="sidebar-item">
            <a class="sidebar-link {{ request()->routeIs($pattern) ? 'active' : '' }}" href="{{ route($routeName) }}">
                <span><i class="ti {{ $icon }}"></i></span><span class="hide-menu">{{ $label }}</span>
            </a>
        </li>
    @endforeach

    <li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">Layanan Tamu</span></li>
    <li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('receptionist.food-orders.*') ? 'active' : '' }}" href="{{ route('receptionist.food-orders.index') }}"><span><i class="ti ti-tools-kitchen-2"></i></span><span class="hide-menu">Pesanan F&B</span></a></li>
    <li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('receptionist.service-orders.*') ? 'active' : '' }}" href="{{ route('receptionist.service-orders.index') }}"><span><i class="ti ti-bell"></i></span><span class="hide-menu">Pesanan Layanan</span></a></li>
    <li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('receptionist.guest-requests.*') ? 'active' : '' }}" href="{{ route('receptionist.guest-requests.index') }}"><span><i class="ti ti-message-circle"></i></span><span class="hide-menu">Permintaan Tamu</span></a></li>

    <li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">Food & Beverage</span></li>
    @foreach ([
        ['receptionist.food-categories.*', 'receptionist.food-categories.index', 'Kategori Menu', 'ti-category'],
        ['receptionist.menu-items.*', 'receptionist.menu-items.index', 'Daftar Menu', 'ti-soup'],
    ] as [$pattern, $routeName, $label, $icon])
        <li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs($pattern) ? 'active' : '' }}" href="{{ route($routeName) }}"><span><i class="ti {{ $icon }}"></i></span><span class="hide-menu">{{ $label }}</span></a></li>
    @endforeach
    <li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('receptionist.hotel-services.*') ? 'active' : '' }}" href="{{ route('receptionist.hotel-services.index') }}"><span><i class="ti ti-spa"></i></span><span class="hide-menu">Layanan Hotel</span></a></li>

    <li class="nav-small-cap"><i class="ti ti-dots nav-small-cap-icon fs-4"></i><span class="hide-menu">Keuangan & Website</span></li>
    <li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('receptionist.payments.*') ? 'active' : '' }}" href="{{ route('receptionist.payments.index') }}"><span><i class="ti ti-credit-card"></i></span><span class="hide-menu">Pembayaran</span></a></li>
    <li class="sidebar-item">
        <a class="sidebar-link {{ request()->routeIs('receptionist.payment-methods.*') ? 'active' : '' }}" href="{{ route('receptionist.payment-methods.index') }}">
            <span><i class="ti ti-wallet"></i></span><span class="hide-menu">Metode Pembayaran</span>
        </a>
    </li>
    <li class="sidebar-item"><a class="sidebar-link {{ request()->routeIs('receptionist.folios.*') ? 'active' : '' }}" href="{{ route('receptionist.folios.index') }}"><span><i class="ti ti-receipt"></i></span><span class="hide-menu">Folio Tamu</span></a></li>
    <li class="sidebar-item"><a class="sidebar-link" href="javascript:void(0)"><span><i class="ti ti-world"></i></span><span class="hide-menu">Konten Website</span><span class="badge bg-light-warning text-warning ms-auto">Segera</span></a></li>
</ul>
