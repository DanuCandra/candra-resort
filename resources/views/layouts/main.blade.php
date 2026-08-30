<!DOCTYPE html>
<html lang="id" dir="ltr" data-bs-theme="light" data-color-theme="Blue_Theme" data-layout="vertical">

@include('components.dashboard.header')

<body>
    <div class="preloader">
        <img src="{{ asset('dashboard/assets/images/logos/favicon.png') }}" alt="Memuat" class="lds-ripple img-fluid">
    </div>

    <div id="main-wrapper">
        @include('components.dashboard.sidebar')

        <div class="page-wrapper">
            @include('components.dashboard.topbar')

            <div class="body-wrapper">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <div class="dark-transparent sidebartoggler"></div>
    @include('components.dashboard.script')
</body>
</html>
