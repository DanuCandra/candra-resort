<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('dashboard/assets/images/logos/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('dashboard/assets/css/styles.css') }}">
    <title>@yield('title', 'Dashboard') · Candra Resort</title>
    @stack('styles')
</head>
