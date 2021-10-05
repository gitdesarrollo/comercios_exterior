<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sistema de Expedientes -MINECO-</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/all.js') }}"></script>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/all.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:700,900|Open+Sans|Open+Sans+Condensed:700" rel="stylesheet" crossorigin="anonymous">
</head>
<body class="app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show" >
    <main id="app">      
        @include('common.header')
            <div class="app-body"  >
                <div class="sidebar">
                    <sidebar-component />
                    <!-- @include('common.sidebar') -->
                    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
                </div>
                <div class=" pl-4 main" >
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">@yield('breadcrumb')</li>
                    </ol>
                    <div class="container-fluid">
                        <div id="ui-view"></div>
                    </div>
                    @yield('content')
                </div>
            </div>
    </main>
</body>
</html>
