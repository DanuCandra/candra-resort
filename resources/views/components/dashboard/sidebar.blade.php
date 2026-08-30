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
                <span class="round-40 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold">
                    {{ str(auth()->user()->name)->substr(0, 1)->upper() }}
                </span>
                <div class="flex-grow-1 overflow-hidden">
                    <h6 class="mb-0 fs-3 fw-semibold text-truncate">{{ auth()->user()->name }}</h6>
                    <span class="fs-2">{{ auth()->user()->role->label() }}</span>
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
