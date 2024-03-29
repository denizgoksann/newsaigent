<section>
    <div class="container">
        <div class="row menu-row">
            <div class="col-6 col-md-2">
                <div class="logo-div">
                    <img src="{{asset('public/web/img/aa_logo.webp')}}" alt="">
                </div>
            </div>
            <div class="col-10 menu">
                <ul class="d-flex gap-5 justify-content-end text-end align-items-center p-4 menu-text w-100">
                    @if (Auth::check() != 1)
                    <li><a href="{{route('index')}}" class="text-decoration-none text-white">ANA SAYFA</a></li>
                    <li><a href="{{route('index')}}/#team" class="text-decoration-none text-white">EKIBIMIZ</a></li>
                    <li><a href="{{route('index')}}/#proje" class="text-decoration-none text-white">PROJE</a></li>
                    <li><a href="{{route('index')}}/#contact" class="text-decoration-none text-white">ILETIŞIM</a></li>                        
                    @endif
                    @auth
                    <div class="dropdown">
                      <a class="text-decoration-none text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">Yabancı Kaynaklar</a>
                      <ul class="dropdown-menu">
                        <li><a href="{{route('nyt')}}" class="text-decoration-none dropdown-item">New York Times</a></li>
                        <li><a href="{{route('twp')}}" class="text-decoration-none dropdown-item">The Washington Post</a></li>
                        <li><a href="{{route('xin')}}" class="text-decoration-none dropdown-item">Xinhua</a></li>
                    </div>
                    <li><a href="{{route('title')}}" class="text-decoration-none text-white">Haber Başlığı Oluştur</a></li>
                    <li><a href="{{route('spot')}}" class="text-decoration-none text-white">Spot Oluştur</a></li>
                    <li><a href="{{route('live')}}" class="text-decoration-none text-white">Yayınlanan Haberler</a></li>
                    <li><a href="{{route('desifre')}}" class="text-decoration-none text-white">Deşifre Haberler</a></li>

                    <div class="dropdown">
                        <a class="text-decoration-none text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Haber Oluştur
                        </a>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="{{route('flash')}}">Flash Haber</a></li>
                          <li><a href="{{route('news')}}" class="text-decoration-none dropdown-item">Haber Oluştur</a></li>


                        </ul>
                      </div>
                    @endauth
                    @if (auth()->check())
                    <div class="dropdown">
                        <button class="text-white login_button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="{{route('profil.pages')}}">Profil</a></li>
                          <li><button class="dropdown-item" id="logoutBtn">Çıkış</button></li>
                        </ul>
                      </div>
                    @else
                    <li><button type="button" class="text-white login_button p-2 " data-bs-toggle="modal" data-bs-target="#staticBackdrop">GİRİŞ / ÜYE OL</button></li>
                    @endif
                </ul>
            </div>
            <div class="col-3 mobile-menu">
                <span class="hmbrgr-menu" id="mobile-menu-button"><i class="bi bi-list"></i></span>
            </div>
            <div class="mobile-menu-container mobile-menu-passive" id="mobile-menu-container" style="z-index:9!important;">
                <ul class="d-flex gap-3 justify-content-start text-start align-items-start p-4 w-100 flex-column mobile-menu-text">
                    <li>
                        <div class="logo-div">
                            <img src="{{ asset('public/web/img/aa_logo.webp') }}" alt="">
                        </div>
                    </li>
                    @if (Auth::check() != 1)
                    <li><a href="{{route('index')}}" class="text-decoration-none text-black">ANA SAYFA</a></li>
                    <li><a href="#team" class="text-decoration-none text-black">EKIBIMIZ</a></li>
                    <li><a href="#proje" class="text-decoration-none text-black">PROJE</a></li>
                    <li><a href="#contact" class="text-decoration-none text-black">ILETIŞIM</a></li>
                    @endif
                    @auth
                    <div class="dropdown">
                      <a class="text-decoration-none text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">Yabancı Kaynaklar</a>
                      <ul class="dropdown-menu">
                        <li><a href="{{route('nyt')}}" class="text-decoration-none dropdown-item">New York Times</a></li>
                        <li><a href="{{route('twp')}}" class="text-decoration-none dropdown-item">The Washington Post</a></li>
                        <li><a href="{{route('xin')}}" class="text-decoration-none dropdown-item">Xinhua</a></li>
                      </div>
                    <li><a href="{{route('title')}}" class="text-decoration-none text-black">Haber Başlığı Oluştur</a></li>
                    <li><a href="{{route('spot')}}" class="text-decoration-none text-black">Spot Oluştur</a></li>
                    <li><a href="{{route('live')}}" class="text-decoration-none text-black">Yayınlanan Haberler</a></li>
                    <li><a href="{{route('desifre')}}" class="text-decoration-none text-black">Deşifre Haberler</a></li>
                    <div class="dropdown">
                        <a class="text-decoration-none text-black" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Haber Oluştur
                        </a>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="{{route('flash')}}">Flash Haber</a></li>
                          <li><a href="{{route('news')}}" class="text-decoration-none dropdown-item">Haber Oluştur</a></li>
                        </ul>
                      </div>
                      <div class="dropdown">
                        <button class="text-white login_button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          {{ Auth::user()->name }}
                        </button>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="{{route('profil.pages')}}">Profil</a></li>
                          <li><button class="dropdown-item" id="logoutBtn">Çıkış</button></li>
                        </ul>
                      </div>
                    @else
                    <li><button type="button" class="text-white login_button_mobile" data-bs-toggle="modal" data-bs-target="#staticBackdrop">GİRİŞ YAP <br> ÜYE OL</button></li>
                    @endauth
                </ul>
                
            </div>
        </div>
    </div>
</section>

@if (auth()->check())

@else
<div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="d-flex justify-content-end w-100 p-2">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color: white"></button></div>
        <div class="modal-body">
            <div class="container">
                <div class="row">
                    @include('part.login')
                    @include('part.register')

                </div>
            </div>
        </div>
      </div>
    </div>
</div>
@endif