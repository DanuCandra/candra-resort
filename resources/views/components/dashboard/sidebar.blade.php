@php
    $sidebarStaff = auth()->user();
    $sidebarAvatarUrl = $sidebarStaff->avatar_path ? asset('storage/'.$sidebarStaff->avatar_path) : null;
@endphp

<aside class="left-sidebar with-vertical">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route(auth()->user()->dashboardRouteName()) }}" class="text-nowrap logo-img text-decoration-none">
                <span class="fs-6 fw-bolder text-primary">Candra</span>
                <span class="fs-6 fw-bolder text-dark">Resort</span>
            </a>
            <a href="javascript:void(0)" class="sidebartoggler ms-auto text-decoration-none fs-5 d-block d-xl-none">
                <i class="ti ti-x"></i>
            </a>
        </div>

        <nav class="sidebar-nav scroll-sidebar" data-simplebar>
            @if (auth()->user()->hasRole(\App\Enums\UserRole::Owner))
                @include('components.dashboard.sidebar-owner')
            @else
                @include('components.dashboard.sidebar-receptionist')
            @endif
        </nav>

        <div class="fixed-profile p-3 mx-4 mb-2 bg-primary-subtle rounded mt-3">
            <div class="hstack gap-3">
                <a href="{{ route($sidebarStaff->profileRouteName()) }}" class="d-flex flex-shrink-0 text-decoration-none" aria-label="Buka profil saya">
                    @if ($sidebarAvatarUrl)
                        <img src="{{ $sidebarAvatarUrl }}" width="40" height="40" class="rounded-circle object-fit-cover" alt="Foto profil {{ $sidebarStaff->name }}">
                    @else
                        <span class="round-40 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold">{{ str($sidebarStaff->name)->substr(0, 1)->upper() }}</span>
                    @endif
                </a>
                <div class="flex-grow-1 overflow-hidden">
                    <a href="{{ route($sidebarStaff->profileRouteName()) }}" class="text-dark text-decoration-none"><h6 class="mb-0 fs-3 fw-semibold text-truncate">{{ $sidebarStaff->name }}</h6></a>
                    <span class="fs-2">{{ $sidebarStaff->role->label() }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" data-confirm="Keluar dari sistem Candra Resort?" data-confirm-icon="question">
                    @csrf
                    <button class="border-0 bg-transparent text-primary" type="submit" aria-label="Keluar">
                        <i class="ti ti-power fs-6"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
