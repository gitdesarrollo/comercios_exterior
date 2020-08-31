<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Inventarios</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/all.js') }}"></script>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:700,900|Open+Sans|Open+Sans+Condensed:700" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
    @include('common.header')
    <div class="app-body"  >
        
        <div class="sidebar">
            @include('common.sidebar')
            <button class="sidebar-minimizer brand-minimizer" type="button"></button>
        </div>
        <main class="py-4 main" id="app">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">@yield('breadcrumb')</li>
            </ol>
            <div class="container-fluid">
                <div id="ui-view"></div>
            </div>
            @yield('content')
        </main>
    </div>
    {{--  <script src="{{ asset('js/app.js')}}"></script>  --}}
</body>
</html>
