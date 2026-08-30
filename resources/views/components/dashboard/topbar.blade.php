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
                            <span class="round-40 rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-semibold">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</span>
                        </a>
                        <div class="dropdown-menu content-dd dropdown-menu-end dropdown-menu-animate-up">
                            <div class="py-3 px-4 border-bottom">
                                <h6 class="mb-1 fw-semibold">{{ auth()->user()->name }}</h6>
                                <span class="fs-2 text-body-secondary">{{ auth()->user()->email }}</span>
                            </div>
                            <div class="p-3">
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
