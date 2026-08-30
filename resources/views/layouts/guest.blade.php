<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="@yield('meta_description', 'Candra Resort - pengalaman menginap yang hangat dan berkesan.')">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Candra Resort')</title>

    <link href="https://fonts.googleapis.com/css?family=Lora:400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Cabin:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('landing-lage/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing-lage/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing-lage/css/elegant-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('landing-lage/css/flaticon.css') }}">
    <link rel="stylesheet" href="{{ asset('landing-lage/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing-lage/css/nice-select.css') }}">
    <link rel="stylesheet" href="{{ asset('landing-lage/css/jquery-ui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing-lage/css/magnific-popup.css') }}">
    <link rel="stylesheet" href="{{ asset('landing-lage/css/slicknav.min.css') }}">
    <link rel="stylesheet" href="{{ asset('landing-lage/css/style.css') }}">
    <style>
        .brand-text { color: #19191a; font: 700 24px/1.1 "Lora", serif; white-space: nowrap; }
        .brand-text span { color: #dfa974; display: block; font: 600 10px/1.4 "Cabin", sans-serif; letter-spacing: 3px; text-transform: uppercase; }
        .auth-section { background: #f8f8f8; min-height: 70vh; padding: 90px 0; }
        .auth-card { background: #fff; box-shadow: 0 12px 35px rgba(25, 25, 26, .08); padding: 38px; }
        .sona-form .form-control { border: 1px solid #ebebeb; border-radius: 0; height: 50px; }
        .sona-form textarea.form-control { height: auto; }
        .sona-button { background: #dfa974; border: 1px solid #dfa974; color: #fff; cursor: pointer; font-size: 13px; font-weight: 700; letter-spacing: 2px; padding: 14px 24px; text-transform: uppercase; }
        .sona-button:hover { background: #c99155; border-color: #c99155; color: #fff; }
        .page-hero { background-position: center; background-size: cover; padding: 90px 0; position: relative; }
        .page-hero::before { background: rgba(25, 25, 26, .58); content: ''; inset: 0; position: absolute; }
        .page-hero .container { position: relative; }
        .page-hero h1, .page-hero p { color: #fff; }
        .dashboard-card { border: 0; box-shadow: 0 8px 24px rgba(25, 25, 26, .08); }
        .home-room-showcase { padding-left: 32px; padding-right: 32px; }
        .home-room-showcase .hp-room-items { margin-left: 0; margin-right: 0; overflow: visible; }
        .home-room-showcase .hp-room-items > .row { margin-left: -8px; margin-right: -8px; }
        .home-room-showcase .hp-room-items > .row > [class*="col-"] { padding-left: 8px; padding-right: 8px; }
        .home-room-showcase .hp-room-item { border-radius: 6px; margin-right: 0; }
        .room-details-section.room-details-spaced { padding-top: 80px; }
        .aboutus-page-section.about-content-spaced { padding-top: 80px; }
        .promotion-card-copy { color: #707079; line-height: 26px; margin: -5px 4px 30px; }
        @media only screen and (max-width: 991px) {
            .home-room-showcase { padding-left: 15px; padding-right: 15px; }
            .home-room-showcase .hp-room-items > .row { margin-left: -7px; margin-right: -7px; }
            .home-room-showcase .hp-room-items > .row > [class*="col-"] { padding-left: 7px; padding-right: 7px; }
            .room-details-section.room-details-spaced, .aboutus-page-section.about-content-spaced { padding-top: 60px; }
        }
        @media only screen and (max-width: 767px) {
            .home-room-showcase { padding-left: 10px; padding-right: 10px; }
            .room-details-section.room-details-spaced, .aboutus-page-section.about-content-spaced { padding-top: 45px; }
        }
    </style>
    @stack('styles')
</head>

<body>
    <div id="preloder"><div class="loader"></div></div>
    @include('components.guest.navbar')

    <main>@yield('content')</main>

    @include('components.guest.footer')

    <script src="{{ asset('landing-lage/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('landing-lage/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('landing-lage/js/jquery.magnific-popup.min.js') }}"></script>
    <script src="{{ asset('landing-lage/js/jquery.nice-select.min.js') }}"></script>
    <script src="{{ asset('landing-lage/js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('landing-lage/js/jquery.slicknav.js') }}"></script>
    <script src="{{ asset('landing-lage/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('landing-lage/js/main.js') }}"></script>
    @include('partials.notifications')
    @include('partials.sweetalert')
    @stack('scripts')
</body>
</html>
