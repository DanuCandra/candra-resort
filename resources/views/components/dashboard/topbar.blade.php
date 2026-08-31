@php
    $currentStaff = auth()->user();
    $staffAvatarUrl = $currentStaff->avatar_path ? asset('storage/'.$currentStaff->avatar_path) : null;
@endphp

<header class="topbar">
    <div class="with-vertical">
        <nav class="navbar navbar-expand-lg p-0">
            <ul class="navbar-nav">
                <li class="nav-item nav-icon-hover-bg rounded-circle ms-n2">
                    <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)"><i class="ti ti-menu-2"></i></a>
                </li>
            </ul>
            <div class="d-block d-xl-none py-3 ms-2 fw-bolder text-primary">Candra Resort</div>
            <div class="navbar-collapse justify-content-end px-0">
                <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                    <li class="nav-item">
                        <a class="nav-link moon dark-layout nav-icon-hover-bg rounded-circle" href="javascript:void(0)"><i class="ti ti-moon"></i></a>
                        <a class="nav-link sun light-layout nav-icon-hover-bg rounded-circle" href="javascript:void(0)"><i class="ti ti-sun"></i></a>
                    </li>
                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link pe-0" href="javascript:void(0)" data-bs-toggle="dropdown" aria-expanded="false">
                            @if ($staffAvatarUrl)
                                <img src="{{ $staffAvatarUrl }}" width="40" height="40" class="rounded-circle object-fit-cover" alt="Foto profil {{ $currentStaff->name }}">
                            @else
                                <span class="round-40 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold">{{ str($currentStaff->name)->substr(0, 1)->upper() }}</span>
                            @endif
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up">
                            <div class="py-3 px-4 border-bottom">
                                <div class="d-flex align-items-center gap-3">
                                    @if ($staffAvatarUrl)
                                        <img src="{{ $staffAvatarUrl }}" width="48" height="48" class="rounded-circle object-fit-cover" alt="Foto profil {{ $currentStaff->name }}">
                                    @else
                                        <span class="round-48 rounded-circle bg-light-primary text-primary d-flex align-items-center justify-content-center fw-semibold">{{ str($currentStaff->name)->substr(0, 1)->upper() }}</span>
                                    @endif
                                    <div class="min-w-0"><h6 class="mb-1 fw-semibold text-truncate">{{ $currentStaff->name }}</h6><span class="fs-2 text-body-secondary d-block text-truncate">{{ $currentStaff->email }}</span><small class="text-primary">{{ $currentStaff->role->label() }}</small></div>
                                </div>
                            </div>
                            <div class="p-3">
                                <a href="{{ route($currentStaff->profileRouteName()) }}" class="dropdown-item rounded-1"><i class="ti ti-user-circle me-2"></i>Profil Saya</a>
                                <a href="{{ route('home') }}" class="dropdown-item rounded-1"><i class="ti ti-world me-2"></i>Lihat Website</a>
                                <form action="{{ route('logout') }}" method="POST" data-confirm="Keluar dari sistem Candra Resort?" data-confirm-icon="question">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary w-100 mt-3">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>
