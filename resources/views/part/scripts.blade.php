<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
   $(document).ready(() => {
    $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });
    $('#mobile-menu-button').click(() => {
        if ($('#mobile-menu-container').hasClass('mobile-menu-passive')) {
            $('#mobile-menu-container').removeClass('mobile-menu-passive').addClass('mobile-menu-active').css('transition', '0.3s ease-in');
        } else {
            $('#mobile-menu-container').removeClass('mobile-menu-active').addClass('mobile-menu-passive').css('transition', '0.3s ease-in');
        }
    });

    $(document).on('click', (event) => {
        if ($(event.target).closest('#mobile-menu-container, #mobile-menu-button').length > 0) {
            return;
        }
        if ($('#mobile-menu-container').hasClass('mobile-menu-active')) {
            $('#mobile-menu-container').removeClass('mobile-menu-active').addClass('mobile-menu-passive').css('transition', '0.3s ease-in');
        }
    });
    
    $('#registerFormButton').click(() => {
        var formData = $('#registerForm').serialize();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            type:'POST',
            url:'{{route("registerPost")}}',
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
                        window.location.href = "{{route('index')}}";
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
                    }else if(data.success == "existing") {
                        Swal.fire({
                            title: "Başarısız",
                            text: "Bu Kullanıcı Adı veya Eposta Kayıtlı.",
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
    
    $('#loginFormButton').click(() => {
        var formData = $('#loginForm').serialize();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            type:'POST',
            url:'{{route("loginPost")}}',
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
                            text: "Başarıyla Giriş Yapıldı",
                            icon: "success",
                            timer: 2000, 
                            showConfirmButton: false 
                        });
                        setTimeout(function() {
                        window.location.href = "{{route('index')}}";
                        }, 2000);
                    }else if(data.success == "error") {
                        Swal.fire({
                            title: "Başarısız",
                            text: "Kullanıcı Adı veya Şifre Hatalı.",
                            icon: "error",
                            timer: 2000, 
                            showConfirmButton: false 
                        });
                  
                }
            }

        });
    });

    
    $('#logoutBtn').click(() => {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');


        $.ajax({
            type:'POST',
            url:'{{route("logoutPost")}}',
            dataType: 'json',
            headers: {
                    'X-CSRF-TOKEN': csrfToken
            },
            success: function(data) {
                if (data.success == "success") {
                    Swal.fire({
                        title: "Başarılı",
                        text: "Başarıyla Çıkış Yapıldı",
                        icon: "success",
                        timer: 2000, 
                        showConfirmButton: false 
                    });
                    setTimeout(function() {
                    window.location.href = "{{route('index')}}";
                    }, 2000);
                }else if(data.success == "error") {
                    Swal.fire({
                        title: "Başarısız",
                        text: "Çıkış Yapılırken Hata Oldu. Tekrar Deneyiniz",
                        icon: "error",
                        timer: 2000, 
                        showConfirmButton: false 
                    });
                
                }
            }

        });
    });
    $('#logoutBtnMobile').click(() => {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');


        $.ajax({
            type:'POST',
            url:'{{route("logoutPost")}}',
            dataType: 'json',
            headers: {
                    'X-CSRF-TOKEN': csrfToken
            },
            success: function(data) {
                if (data.success == "success") {
                    Swal.fire({
                        title: "Başarılı",
                        text: "Başarıyla Çıkış Yapıldı",
                        icon: "success",
                        timer: 2000, 
                        showConfirmButton: false 
                    });
                    setTimeout(function() {
                    window.location.href = "{{route('index')}}";
                    }, 2000);
                }else if(data.success == "error") {
                    Swal.fire({
                        title: "Başarısız",
                        text: "Çıkış Yapılırken Hata Oldu. Tekrar Deneyiniz",
                        icon: "error",
                        timer: 2000, 
                        showConfirmButton: false 
                    });
                
                }
            }

        });
    });

    $('#email').keypress(function(e){
            if(e.which == 13){
                e.preventDefault();
                $('#loginFormButton').click(); 
            }
    });
    $('#password').keypress(function(e){
            if(e.which == 13){
                e.preventDefault();
                $('#loginFormButton').click(); 
            }
    });

    $('#name_register').keypress(function(e){
        if(e.which == 13){
            e.preventDefault();
            $('#registerFormButton').click(); 
        }
    });
    $('#email_register').keypress(function(e){
        if(e.which == 13){
            e.preventDefault();
            $('#registerFormButton').click(); 
        }
    });
    $('#password_register').keypress(function(e){
        if(e.which == 13){
            e.preventDefault();
            $('#registerFormButton').click(); 
        }
    });
    $('#password_again').keypress(function(e){
        if(e.which == 13){
            e.preventDefault();
            $('#registerFormButton').click(); 
        }
    });


    

});


</script>