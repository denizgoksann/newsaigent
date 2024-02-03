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
                    <label for="uniq_words" class="form-label">Haberde Kullanılmasını ve Kesinlikle Değiştirilmesini İstemediğiniz Kelime veya Cümleleri Giriniz</label>
                    <input type="text" class="form-control" id="uniq_words" name="uniq_words">
                </div>
                <div class="mb-3">
                    <label for="title_draft" class="form-label">Haber Başlığı İçin Haber Metnini Giriniz</label>
                    <textarea id="title_draft" name="title_draft" ></textarea>

                </div>

                <div class="d-flex justify-content-end">
                    <button type="button" id="createNews" class="login_button text-white p-2">Haber Başlığı Oluştur</button>
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
    function historyMessage() {
    var data = {};
    $.ajax({
        url: "{{ route('see-history-title') }}",
        data: data,
        method: 'post',
        success: function(response) {
            $('#historyNews').append(response.data);
        }
    });
}
CKEDITOR.replace('title_draft');

    $('#addFormContentSee').click(() => {
        if($('#homeContent').hasClass('content-block')){
            $('#homeContent').removeClass('content-block').addClass('content-none');
        }
        if($('#seeNews').hasClass('content-block')){
            $('#seeNews').removeClass('content-block').addClass('content-none');
        }
        $('#addFormContent').removeClass('content-none').addClass('content-block');
    })
    
    $('#createNews').on('click', function(e) {
        e.preventDefault(); 
        newsAdd();
    });
    function newsAdd() {
            let title_draft = CKEDITOR.instances['title_draft'].getData();
            let uniq_words = $("#uniq_words").val();
            let formData = new FormData();
            formData.append('title_draft', title_draft);
            formData.append('uniq_words', uniq_words);
            Swal.fire({
                title: 'Yükleniyor...',
                html: 'Lütfen bekleyin, haber oluşturuluyor',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                }
            });
            Swal.fire({
                title: 'Yükleniyor...',
                html: 'Lütfen bekleyin, oluşturuluyor',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                }
            });
            $.ajax({
            url: '{{route("create_title")}}',
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
    $(document).on('click', '.see_message', function() {
        var dataID = $(this).data("id");
        $('#seeNews').empty();
        $('#homeContent').removeClass('content-block').addClass('content-none');
        $('#addFormContent').removeClass('content-block').addClass('content-none');
        $('#seeNews').removeClass('content-none').addClass('content-block');
        $.ajax({
            url: "{{ route('see-title') }}",
            data: {dataID: dataID},
            dataType: 'json',
            method: 'post',
            success: function(data) {
                if (data.success) {
                    var formattedDate = formatDateTime(data.data.created_at);
                    var titleSplit = data.data.title;
                    var titleParse = titleSplit.split('/++');
                    var title = []; 
                    $('#seeNews').empty().append(`
                    <form method="POST" id="newsSave">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center see_message_title mb-2">
                            <span class="last_news_see_text">${formattedDate}</span>
                        </div>
                        <div class="mb-2 p-3">
                            <label class="form-label text-white">Geçmesi İstenilen Kelimeler</label>
                            <input type="text" id="uniq_words_return" class="form-control" disabled value="${data.data.uniq_words}"/>
                        </div>
                        <div class="mb-3 p-3">
                            <label class="form-label text-white">Haber Başlığı İçin Haber Metni</label>
                            <textarea id="title_draft_return" name="title_draft_return">${data.data.title_draft}</textarea>
                        </div>
                        <div class="mb-3 p-3 ">
                            <label class="form-label text-white">Haber Başlıklarınız</label>
                            <div id="title_each">
                        
                            </div>
                        
                        </div>
                        <div class="d-flex justify-content-end align-items-center mt-2 mx-2 last_news_button">
                            <input type="text" class="form-control" hidden value="${data.data.id}" id="titleId"/>
                            <button type="button" id="lastNews" class="login_button text-white p-2 ">Başlığı Tekrar Oluştur</button>
                        </div>
                        </form>
                    `);
                    CKEDITOR.replace('title_draft_return');

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
                        $('#title_each').append('<span class="text-white" style="font-size:20px;">'+ ++index + ' -> ' + value + '</span> <br><br>');
                    });
                    
                   
                } else {
                    console.error(data.message);
                }
            },
        });
    });
    function formatDateTime(dateTimeStr) {
        var date = new Date(dateTimeStr);
        var day = date.getDate().toString().padStart(2, '0');
        var month = (date.getMonth() + 1).toString().padStart(2, '0');
        var year = date.getFullYear();
        var hours = date.getHours().toString().padStart(2, '0');
        var minutes = date.getMinutes().toString().padStart(2, '0');

        return `${day}-${month}-${year} ${hours}:${minutes}`;
    }
    $(document).on('click', '#lastNews', function(e) {
    e.preventDefault(); 
    newSave();
        });

        function newSave() {
            let title_draft_return = CKEDITOR.instances['title_draft_return'].getData();
            let uniq_words_return = $("#uniq_words_return").val();
            let titleId = $("#titleId").val();
            let formData = new FormData();
            formData.append('title_draft_return', title_draft_return);
            formData.append('titleId', titleId);
            formData.append('uniq_words_return', uniq_words_return);
            Swal.fire({
                title: 'Yükleniyor...',
                html: 'Lütfen bekleyin, oluşturuluyor',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                }
            });
            $.ajax({
                url: "{{route('last-title')}}",
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


