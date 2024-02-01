@extends('layout')
@section('content')

<section class="container">
    <div class="row justify-content-center">
        <div class="col-6">
            <form id="userUpdateForm">
                @csrf
                <div class="mb-3">
                  <label for="name" class="form-label text-white">İsim</label>
                  <input type="text" class="form-control" id="name" name="name" value="{{Auth::user()->name}}">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label text-white">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{Auth::user()->email}}">
                  </div>
                <div class="mb-3">
                  <label for="password" class="form-label text-white">Yeni Şifre</label>
                  <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3">
                    <label for="password_again" class="form-label text-white">Yeni Şifre</label>
                    <input type="password" class="form-control" id="password_again" name="password_again">
                  </div>
                
                <button type="button" class="btn text-white login_button" id="updatePost">GÜNCELLE</button>
              </form>
        </div>
    </div>
</section>
<div class="margin-div"></div>
@endsection
@section('scripts')
    <script>
            $('#updatePost').click(() => {
        var formData = $('#userUpdateForm').serialize();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            type:'POST',
            url:'{{route("userPost")}}',
            data: formData,
            dataType: 'json',

                headers: {
                    'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/x-www-form-urlencoded'
                },
                success: function(data) {
                    if (data.success == "success") {
                        Swal.fire({
                            title: "Başarılı",
                            text: "Başarıyla Kayıt Olundu",
                            icon: "success",
                            timer: 2000, 
                            showConfirmButton: false 
                        });
                        setTimeout(function() {
                        window.location.href = "{{route('profil.pages')}}";
                        }, 2000);
                    }else if(data.success == "password") {
                        Swal.fire({
                            title: "Başarısız",
                            text: "İki Şifre Uyuşmamaktadır",
                            icon: "error",
                            timer: 2000, 
                            showConfirmButton: false 
                        });
                    }else if(data.success == "error") {
                        Swal.fire({
                            title: "Başarısız",
                            text: "Kayıt Olunurken Hata Oluştu. Tekrar Deneyiniz.",
                            icon: "error",
                            timer: 2000, 
                            showConfirmButton: false 
                        });
                    }else if(data.success == "short") {
                        Swal.fire({
                            title: "Başarısız",
                            text: "Şifreniz En Az 6 karakterden oluşmalıdır.",
                            icon: "error",
                            timer: 2000, 
                            showConfirmButton: false 
                        });
                    }else if(data.success == "no_uppercase") {
                        Swal.fire({
                            title: "Başarısız",
                            text: "Şifreniz En Az Bir Büyük Harf İçermelidir.",
                            icon: "error",
                            timer: 2000, 
                            showConfirmButton: false 
                        });
                    }else if(data.success == "empty") {
                        Swal.fire({
                            title: "Başarısız",
                            text: "Boş Alan Bırakmayınız.",
                            icon: "error",
                            timer: 2000, 
                            showConfirmButton: false 
                        });
                    }
                }

        });
    });
    </script>
@endsection

