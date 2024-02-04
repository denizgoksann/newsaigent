<!DOCTYPE html>
<html class="no-js" lang="tr">
@include('part.header')
<body>	

    @include('part.menu')

   
    @yield('content')

    @include('part.footer')
    @include('part.scripts')
    @yield('scripts')	
</body>
</html>