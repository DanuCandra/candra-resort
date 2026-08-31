<div class="offcanvas-menu-overlay"></div>
<div class="canvas-open"><i class="icon_menu"></i></div>
<div class="offcanvas-menu-wrapper">
    <div class="canvas-close"><i class="icon_close"></i></div>
    <div class="header-configure-area">
        @auth
            <a href="{{ route(auth()->user()->dashboardRouteName()) }}" class="bk-btn">Dashboard</a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline-block" data-confirm="Keluar dari akun Candra Resort?" data-confirm-icon="question">
                @csrf
                <button type="submit" class="bk-btn border-0 ml-2"><i class="fa fa-sign-out mr-1"></i>Keluar</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="bk-btn">Masuk</a>
        @endauth
    </div>
    <nav class="mainmenu mobile-menu">
        <ul>
            <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="{{ request()->routeIs('public.rooms.*') ? 'active' : '' }}"><a href="{{ route('public.rooms.index') }}">Kamar</a></li>
            @auth @if(auth()->user()->hasRole('guest'))<li class="{{ request()->routeIs('guest.reservations.*') ? 'active' : '' }}"><a href="{{ route('guest.reservations.index') }}">Reservasi Saya</a></li>@endif @endauth
            <li class="{{ request()->routeIs('public.facilities') ? 'active' : '' }}"><a href="{{ route('public.facilities') }}">Fasilitas</a></li>
            <li class="{{ request()->routeIs('public.promotions.*') ? 'active' : '' }}"><a href="{{ route('public.promotions.index') }}">Promosi</a></li>
            <li class="{{ request()->routeIs('public.gallery') ? 'active' : '' }}"><a href="{{ route('public.gallery') }}">Galeri</a></li>
            <li class="{{ request()->routeIs('public.about') ? 'active' : '' }}"><a href="{{ route('public.about') }}">Tentang</a></li>
            <li class="{{ request()->routeIs('public.contact') ? 'active' : '' }}"><a href="{{ route('public.contact') }}">Kontak</a></li>
        </ul>
    </nav>
    <div id="mobile-menu-wrap"></div>
</div>

<header class="header-section">
    <div class="top-nav">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <ul class="tn-left">
                        <li><i class="fa fa-phone"></i> {{ $siteSettings->get('hotel.phone', '+62 812 3456 7890') }}</li>
                        <li><i class="fa fa-envelope"></i> {{ $siteSettings->get('hotel.email', 'info@candraresort.test') }}</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="tn-right">
                        <div class="top-social">
                            @if ($siteSettings->get('social.instagram'))<a href="{{ $siteSettings->get('social.instagram') }}" target="_blank" rel="noopener" aria-label="Instagram"><i class="fa fa-instagram"></i></a>@endif
                            @if ($siteSettings->get('social.facebook'))<a href="{{ $siteSettings->get('social.facebook') }}" target="_blank" rel="noopener" aria-label="Facebook"><i class="fa fa-facebook"></i></a>@endif
                        </div>
                        @auth
                            <a href="{{ route(auth()->user()->dashboardRouteName()) }}" class="bk-btn">Dashboard Saya</a>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline-block" data-confirm="Keluar dari akun Candra Resort?" data-confirm-icon="question">
                                @csrf
                                <button type="submit" class="bk-btn border-0"><i class="fa fa-sign-out mr-1"></i>Keluar</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="bk-btn">Masuk / Daftar</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="menu-item">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3">
                    <div class="logo">
                        <a href="{{ route('home') }}" class="brand-text">{{ $siteSettings->get('hotel.name', 'Candra Resort') }}<span>{{ $siteSettings->get('hotel.tagline', 'Hotel & Experience') }}</span></a>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="nav-menu">
                        <nav class="mainmenu">
                            <ul>
                                <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a href="{{ route('home') }}">Beranda</a></li>
                                <li class="{{ request()->routeIs('public.rooms.*') ? 'active' : '' }}"><a href="{{ route('public.rooms.index') }}">Kamar</a></li>
                                @auth @if(auth()->user()->hasRole('guest'))<li class="{{ request()->routeIs('guest.reservations.*') ? 'active' : '' }}"><a href="{{ route('guest.reservations.index') }}">Reservasi Saya</a></li>@endif @endauth
                                <li class="{{ request()->routeIs('public.facilities') ? 'active' : '' }}"><a href="{{ route('public.facilities') }}">Fasilitas</a></li>
                                <li class="{{ request()->routeIs('public.promotions.*') ? 'active' : '' }}"><a href="{{ route('public.promotions.index') }}">Promosi</a></li>
                                <li class="{{ request()->routeIs('public.gallery') ? 'active' : '' }}"><a href="{{ route('public.gallery') }}">Galeri</a></li>
                                <li class="{{ request()->routeIs('public.about') ? 'active' : '' }}"><a href="{{ route('public.about') }}">Tentang</a></li>
                                <li class="{{ request()->routeIs('public.contact') ? 'active' : '' }}"><a href="{{ route('public.contact') }}">Kontak</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
