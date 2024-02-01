<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('part.styles')
    @yield('styles')
    <title>@yield('title' , '404 Ai')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}" />


</head>