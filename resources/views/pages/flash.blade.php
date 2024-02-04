@extends('layout')
@section('content')

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12 col-md-3 news_history_container">
            <div class="d-flex align-items-center text-center justify-content-between news_history_title">
                <a href="{{route('index')}}"><img src="{{asset('public/web/img/aa_logo.webp')}}" alt="" style="width: 50px;"></a>
                <span class="add_text_icon" id="addFormContentSee"><i class="bi bi-pencil-square"></i></span>
            </div>
            <div id="historyNews">

            </div>
        </div>
        <div class="col-12 col-md-9 news_content_default content-block" id="homeContent">
            <div class="d-flex news_content_default align-items-center text-center">
                <img src="{{asset('public/web/img/aa_logo.webp')}}" alt="" class="content_logo">

                <p class="hello_ai">
                    Haber Oluşturmak İçin NewsAIgent'e Hoş Geldiniz.
                </p>
           </div>
        </div>
        <div class="col-12 col-md-9 news_content_see content-none" id="seeNews">

        </div>
        <div class="col-12 col-md-9 news_content p-5 content-none" id="addFormContent">
            <form id="newsAdd" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="uniq_words" class="form-label">Flash Haberde Kullanılmasını ve Kesinlikle Değiştirilmesini İstemediğiniz Kelime veya Cümleleri Giriniz</label>
                    <input type="text" class="form-control" id="uniq_words" name="uniq_words">
                </div>
                <div class="mb-3">
                    <label for="flash_draft" class="form-label">Flash Haber İçin Metninizi Giriniz</label>
                    <textarea id="flash_draft" name="flash_draft" ></textarea>

                </div>
               
                <div class="d-flex justify-content-end">
                    <button type="button" id="createNews" class="login_button text-white p-2">Flash Haber Oluştur</button>
                </div>
            </form>

            
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script>
   $(document).ready(() =>{
        historyMessage()
    // Geçmiş mesajları ajax ile çekip listelediğimiz kısım
    function historyMessage() {
        var data = {};
        $.ajax({
            url: "{{ route('see-history-flash') }}",
            data: data,
            method: 'post',
            success: function(response) {
                $('#historyNews').append(response.data);
            }
        });
    }
    CKEDITOR.replace('flash_draft');
    // Ekleme Formunu ajax ile ekrana basıyoruz
    $('#addFormContentSee').click(() => {
        if($('#homeContent').hasClass('content-block')){
            $('#homeContent').removeClass('content-block').addClass('content-none');
        }
        if($('#seeNews').hasClass('content-block')){
            $('#seeNews').removeClass('content-block').addClass('content-none');
        }
        $('#addFormContent').removeClass('content-none').addClass('content-block');
    })
    // Bu kısımda Yeni taslak için create işlemi formunu ajax ile gönderip api ile dönen sonucu ekrana basıyoruz
    $('#createNews').on('click', function(e) {
        e.preventDefault(); 
        newsAdd();
    });
    function newsAdd() {
            let flash_draft = CKEDITOR.instances['flash_draft'].getData();
            let uniq_words = $("#uniq_words").val();
            let formData = new FormData();
            formData.append('flash_draft', flash_draft);
            formData.append('uniq_words', uniq_words);
            Swal.fire({
                title: 'Yükleniyor...',
                html: 'Lütfen bekleyin, oluşturuluyor',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                }
            });
            $.ajax({
            url: '{{route("create_flash")}}',
            method: 'POST',
            data: formData,
            dataType: 'json',
            contentType: false,
            processData: false,
            beforeSend: function(){
                $('#createNews').attr('disabled', true);
                $('#createNews').html('Haber Oluşturuluyor...');
            },
            success: function(response) {
                console.log('CEVAP=>',response)
                $('#createNews').attr('disabled', false);
                $('#createNews').html('Haber Oluştur');
                Swal.close();
            if (response.success == "success") {
                Swal.fire({
                title: "Başarılı",
                text: "Haber Başarıyla Gönderildi. Birazdan Yönlendirileceksiniz.",
                icon: "success",
                timer: 2000, 
                showConfirmButton: false 
            }).then(() => {
                $('input').val("");
                $('#historyNews').empty();
                historyMessage();
                setTimeout(function() {
                    $('.see_message').first().trigger('click');
                }, 500);
            });

            }else if(response.success == "emptyTitle"){
                Swal.fire({
                    title: "Başarısız",
                    text: "Haber Başlığı Boş Olamaz",
                    icon: "error",
                    timer: 2000, 
                    showConfirmButton: false 
                });
                setTimeout(function() {
                }, 2000);
            }else if(response.success == "uniqWords"){
                Swal.fire({
                    title: "Başarısız",
                    text: "Haberde Kullanılmasını ve Kesinlikle Değiştirilmesini İstemediğiniz Kelime veya Cümleler Boş Olamaz",
                    icon: "error",
                    timer: 2000, 
                    showConfirmButton: false 
                });
                setTimeout(function() {
                }, 2000);
            }else if(response.success == "editor"){
                Swal.fire({
                    title: "Başarısız",
                    text: "Editor Alanını Doldurmanız Gerekmektedir.",
                    icon: "error",
                    timer: 2000, 
                    showConfirmButton: false 
                });
                setTimeout(function() {
                }, 2000);  
            }else if(response.success == "error"){
                Swal.fire({
                    title: "Başarısız",
                    text: "Haber Oluşturulurken Bir Hata Oluştu. Bir Daha Deneyiniz",
                    icon: "error",
                    timer: 2000, 
                    showConfirmButton: false 
                });
                setTimeout(function() {
                }, 2000);
            }else if(response.success == "system"){
                Swal.fire({
                    title: "Başarısız",
                    text: "Yapay Zeka ile Bağlantı Sağlanamadı. Bir Daha Deneyiniz",
                    icon: "error",
                    timer: 2000, 
                    showConfirmButton: false 
                });
                setTimeout(function() {
                }, 2000);
            }
            }
        });
    }
    // Bu kısımda solda dökülen geçmiş dökümanlardan tıklananın orta alana basmayı sağlıyoruz
    $(document).on('click', '.see_message', function() {
        var dataID = $(this).data("id");
        $('#seeNews').empty();
        $('#homeContent').removeClass('content-block').addClass('content-none');
        $('#addFormContent').removeClass('content-block').addClass('content-none');
        $('#seeNews').removeClass('content-none').addClass('content-block');
        $.ajax({
            url: "{{ route('see-flash') }}",
            data: {dataID: dataID},
            dataType: 'json',
            method: 'post',
            success: function(data) {
                console.log(data);
                if (data.success) {
                    var formattedDate = formatDateTime(data.data.created_at);
                    var titleSplit = data.data.news;
                    var titleParse = titleSplit.split('/++');
                    var title = []; 
                    $('#seeNews').empty().append(`
                    <form method="POST" id="newsSave">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center see_message_title mb-2">
                            <span class="last_news_see_text">${formattedDate}</span>
                        </div>
                        <div class="mb-3 p-3">
                            <label class="form-label text-white">Flash Haber İçin Değişmez Kelimeler </label>
                            <input type="text" class="form-control" id="uniq_words_return" value="${data.data.uniq_words}"/>
                        </div>
                        <div class="mb-3 p-3">
                            <label class="form-label text-white">Flsh Haber İçin Haber Metni</label>
                            <textarea id="flash_draft_return" name="flash_draft_return" class="w-100">${data.data.news_draft}</textarea>
                        </div>
                        <div class="mb-3 p-3 ">
                            <label class="form-label text-white">Flash Haberleriniz</label>
                            <div id="title_each">
                        
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center mt-2 mx-2 last_news_button">
                            <input type="text" class="form-control" hidden value="${data.data.id}" id="flashId"/>
                            <button type="button" id="lastNews" class="login_button text-white p-2 ">Haberi Tekrar Oluştur</button>
                        </div>
                        </form>
                    `);
                    CKEDITOR.replace('flash_draft_return');

                    $.each(titleParse, function(index, value){
                        var trimmedValue = $.trim(value);
                        if(trimmedValue.startsWith(',')) { 
                            trimmedValue = trimmedValue.substring(1); 
                        }
                        if(trimmedValue !== "") {
                            title.push(trimmedValue); 
                        }
                    });

                    $.each(titleParse, function(index, value){
                        var trimmedValue = $.trim(value);
                        if(trimmedValue.startsWith(',')) { 
                            trimmedValue = trimmedValue.substring(1); 
                        }
                        if(trimmedValue !== "") {
                            title.push(trimmedValue); 
                        }
                    });
                    $.each(title, function(index, value){
                            var maxLength = 130;
                        var valueS = value.length > maxLength ? value.substring(0, maxLength) + '...' : value;

                        var key = index + 1;
                        var key2 = index + 1;
                        var key3 = index + 1;

                        var modalID = "modal_" + key;
                        var indexID = "index_" + key2;

                        $('#title_each').append('<a type="button" class="text-white" style="font-size:15px; text-decoration:none;" data-bs-toggle="modal" data-bs-target="#' + modalID + '">' + valueS + '</a><br><br>');

                        var modalContent = `
                        <!-- Modal -->
                        <div class="modal fade" id="${modalID}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form>
                                            @csrf
                                            <input type="text" hidden id="newsID" value="${data.data.id}">
                                            <input type="text" hidden id="${indexID}" value="${key3}">
                                            <textarea id="news_last_` + key3 +`"  name="news_last" class="news_last">${value}</textarea>
                                            <input type="text"  hidden  id="news_reply" value="${value}">
                                            
                                            <button type="button" id="lastNews" class="login_button text-white p-2 ">Haberi Yayınla</button>
                                            </form>

                                        </div>
                                        <div class="d-flex justify-content-end align-items-center mt-2 mx-2 last_news_button">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        $('#title_each').append(modalContent);

                        CKEDITOR.replace('news_last_' + key3);
                    });

                } else {
                    console.error(data.message);
                }
                CKEDITOR.replace('news_last');

            },
        });
    });
    // Bu kısımda tarihi GG/AA/YYYY SS/DD şeklinde ayarlıyoruz
    function formatDateTime(dateTimeStr) {
        var date = new Date(dateTimeStr);
        var day = date.getDate().toString().padStart(2, '0');
        var month = (date.getMonth() + 1).toString().padStart(2, '0');
        var year = date.getFullYear();
        var hours = date.getHours().toString().padStart(2, '0');
        var minutes = date.getMinutes().toString().padStart(2, '0');

        return `${day}-${month}-${year} ${hours}:${minutes}`;
    }
    //Bu kısımda flash haberi canlıya taşıyoruz
    $(document).on('click', '#lastNews', function(e) {
        e.preventDefault(); 
        newSave();
            });

        function newSave() {

            let news_reply = $("#news_reply").val();
            let formData = new FormData();
            formData.append('news_reply', news_reply);
            Swal.fire({
                title: 'Yükleniyor...',
                html: 'Lütfen bekleyin, oluşturuluyor',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                }
            });
            $.ajax({
                url: "{{route('last-flash-live')}}",
                data: formData,
                dataType: "json",
                method: "POST",
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                processData: false,
                contentType: false,
                beforeSend: function(){
                $('#lastNews').attr('disabled', true);
                $('#lastNews').html('Haber Oluşturuluyor...');
            },
            success: function(response) {
                console.log('CEVAP=>',response)
                $('#lastNews').attr('disabled', false);
                $('#lastNews').html('Haber Oluştur');
                Swal.close();
            if (response.success == "success") {
                Swal.fire({
                title: "Başarılı",
                text: "Haber Başarıyla Yayınlandı.",
                icon: "success",
                timer: 2000, 
                showConfirmButton: false 
            }).then(() => {
                $('input').val("");
                $('#historyNews').empty();
                historyMessage();
                setTimeout(function() {
                }, 500);
            });

            }else if(response.success == "emptyTitle"){
                Swal.fire({
                    title: "Başarısız",
                    text: "Spot Başlığı Boş Olamaz",
                    icon: "error",
                    timer: 2000, 
                    showConfirmButton: false 
                });
                setTimeout(function() {
                }, 2000);
            }else if(response.success == "uniqWords"){
                Swal.fire({
                    title: "Başarısız",
                    text: "Spotta Kullanılmasını ve Kesinlikle Değiştirilmesini İstemediğiniz Kelimeler Boş Olamaz",
                    icon: "error",
                    timer: 2000, 
                    showConfirmButton: false 
                });
               
            }else if(response.success == "error"){
                Swal.fire({
                    title: "Başarısız",
                    text: "Haber Oluşturulurken Bir Hata Oluştu. Bir Daha Deneyiniz",
                    icon: "error",
                    timer: 2000, 
                    showConfirmButton: false 
                });
                setTimeout(function() {
                }, 2000);
            }else if(response.success == "system"){
                Swal.fire({
                    title: "Başarısız",
                    text: "Yapay Zeka ile Bağlantı Sağlanamadı. Bir Daha Deneyiniz",
                    icon: "error",
                    timer: 2000, 
                    showConfirmButton: false 
                });
                setTimeout(function() {
                }, 2000);
            }
            }
            });
        }
   });
</script>
@endsection


