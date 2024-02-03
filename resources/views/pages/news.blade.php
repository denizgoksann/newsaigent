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
                    <label for="news_title" class="form-label">Haber Başlığı</label>
                    <input type="text" class="form-control" id="news_title" name="news_title">
                </div>
                <div class="mb-3">
                    <label for="uniq_words" class="form-label">Haberde Kullanılmasını ve Kesinlikle Değiştirilmesini İstemediğiniz Kelime veya Cümleleri Giriniz</label>
                    <input type="text" class="form-control" id="uniq_words" name="uniq_words">
                </div>
                <div class="mb-3">
                    <label for="spot" class="form-label">Spotu Giriniz</label>
                    <input type="text" class="form-control" id="spot" name="spot">
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Mahreç Giriniz</label>
                    <input type="text" class="form-control" id="location" name="location">
                </div>
                <div class="mb-3">
                    <label for="editor" class="form-label">Yayıncı Giriniz</label>
                    <input type="text" class="form-control" id="editor" name="editor">
                </div>
                <div class="mb-3">
                    <label for="category" class="form-label">Haber Kategorisi Seçiniz</label>
                    <select class="form-select" id="categorySelect" aria-label="Default select example">
                        <option selected>Kategori Seçiniz</option>
                        @foreach ($category as $key => $item)
                        <option id="category_{{++$key}}" value="{{$item->id}}">{{$item->category_name}}</option>
                            
                        @endforeach
                 
                      </select>
                </div>
                <div class="mb-3">
                    <label for="news_text" class="form-label">Haber detaylarını giriniz</label>
                    <textarea id="news_text" name="news_text" ></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" id="createNews" class="login_button text-white p-2">Haber Oluştur</button>
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
        url: "{{ route('see-history') }}",
        data: data,
        method: 'post',
        success: function(response) {
            $('#historyNews').append(response.data);
        }
    });
}
CKEDITOR.replace('news_text');
var editor = CKEDITOR.instances['news_text']; 

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
    function selectCategory(){
        $('#categorySelect').change(function() {
        var selectedValue = $(this).val();
    });
    }
 
    function newsAdd() {
        selectCategory();
            let news_title = $("#news_title").val();
            let uniq_words = $("#uniq_words").val();
            let spot = $("#spot").val();
            let location = $("#location").val();
            let editor = $("#editor").val();
            let category = selectedValue;
            console.log(category);
            let news_text = CKEDITOR.instances['news_text'].getData();
            let formData = new FormData();
            formData.append('news_title', news_title);
            formData.append('uniq_words', uniq_words);
            formData.append('news_text', news_text);
            formData.append('spot', spot);
            formData.append('location', location);
            formData.append('editor', editor);
            Swal.fire({
                title: 'Yükleniyor...',
                html: 'Lütfen bekleyin, haber oluşturuluyor',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                }
            });
            $.ajax({
            url: '{{route("create_news")}}',
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
                console.log(response)
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
            }else if(response.success == "emptyText"){
                Swal.fire({
                    title: "Başarısız",
                    text: "Haber Açıklaması Boş Olamaz",
                    icon: "error",
                    timer: 2000, 
                    showConfirmButton: false 
                });
                setTimeout(function() {
                }, 2000);
            }else if(response.success == "emptyEditor"){
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
            url: "{{ route('see-news') }}",
            data: {dataID: dataID},
            dataType: 'json',
            method: 'post',
            success: function(data) {
                if (data.success) {
                    var formattedDate = formatDateTime(data.data.created_at);
                    let dataSpot ="";
                    let dataLocation = "";
                    if(data.data.spot == null){
                        dataSpot = "Doldurulmadı";
                    }else{
                        dataSpot = data.data.spot;
                    }
                    if(data.data.location == null){
                        dataLocation = "Doldurulmadı";

                    }else{
                        dataLocation = data.data.location;
                    }
                    var titleSplit = data.data.news;
                    var titleParse = titleSplit.split('/++');
                    var title = []; 
                    var key = 0;
                    $('#seeNews').append(`
                       <form method="POST" id="newsSave" clss="p-3">
                        @csrf
                        <div class="d-flex justify-content-between align-items-center see_message_title mb-2">
                            <span class="last_news_see_text">${formattedDate}</span>
                        </div>
                        <div class="mb-3 p-3">
                            <label for="editor" class="form-label text-white">Haber Başlığı</label>
                            <input type="text" class="form-control" id="news_title" disabled value="${data.data.news_title}"/>
                        </div>
                        <div class="mb-3 p-3">
                            <label for="editor" class="form-label text-white">Değişmez Kelimeler</label>
                            <input type="text" class="form-control" id="uniq_words" disabled value="${data.data.uniq_words}"/>
                        </div>
                        <div class="mb-3 p-3">
                            <label for="editor" class="form-label text-white">Spot Alanınız</label>
                            <input type="text" class="form-control" id="spot" disabled value="${dataSpot}"/>
                        </div>
                        <div class="mb-3 p-3">
                            <label for="editor" class="form-label text-white">Haber Lokasyonu</label>
                            <input type="text" class="form-control" id="location" disabled value="${dataLocation}"/>
                        </div>
                        <div class="mb-3 p-3">
                            <label for="news_editor" class="form-label text-white">Haber Editörleri</label>
                            <input type="text" class="form-control" id="news_editor" disabled value="${data.data.editor}"/>
                            <input type="text" hidden id="newsID" value="${data.data.id}">

                        </div>
                        <div class="mb-3 p-3 ">
                            <label class="form-label text-white">Spotlarınız</label>
                            <div id="title_each">
                        
                            </div>
                        </div>
                       
                        <div class="d-flex justify-content-end align-items-center mt-2 mx-2 last_news_button">
                            <button type="button" id="lastNewsCreate" class="login_button text-white p-2 ">Haberi Yeniden Oluştur</button>
                        </div>
                        </form>

     

                        
                    `);






                    $.each(titleParse, function(index, value){
                        var trimmedValue = $.trim(value);
                        if(trimmedValue.startsWith(',')) { 
                            trimmedValue = trimmedValue.substring(1); 
                        }
                        if(trimmedValue !== "") {
                            title.push(trimmedValue); 
                        }
                    });
                    var key = 0;
                    var key2 = 0;
                    var key3 = 0;
                    $.each(title, function(index, value){
                        console.log(title)
                        var maxLength = 130;
                        if (value.length > maxLength) {
                            valueS = value.substring(0, maxLength) + '...';
                        }
                        $('#title_each').append('<a type="button" class="text-white" style="font-size:15px; text-decoration:none;" data-bs-toggle="modal" data-bs-target="#modal_'+ ++key +'">' + ++index + ' -> ' + valueS + '</a><br><br>');
                        $('#title_each').append(`
                        <!-- Modal -->
                        <form method="POST" id="newsSaveEdit" clss="p-3">
                        @csrf
                            <div class="modal fade" id="modal_`+ ++key2 +`" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                    <div class="modal-header">
                                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="text" hidden id="newsID" value="${data.data.id}">
                                        <input type="text" hidden id="indexID" value="`+ ++key3 +`">
                                        <textarea id="news_last" name="news_last" >`+ value +`</textarea>

                                        
                                    </div>
                                    <div class="d-flex justify-content-end align-items-center mt-2 mx-2 last_news_button">
                                        <button type="button" id="lastNews" class="login_button text-white p-2 ">Haberi Kaydet</button>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        `);
                    CKEDITOR.replace('news_last');
                        
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
            let news_text = CKEDITOR.instances['news_last'].getData();
            let dataID = $("#newsID").val();
            let indexID = $('#indexID').val();
            let formData = new FormData();
            formData.append('news_last', news_text);
            formData.append('news_id', dataID);
            formData.append('indexID', indexID);
            Swal.fire({
                title: 'Yükleniyor...',
                html: 'Lütfen bekleyin, haber oluşturuluyor',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                }
            });
            $.ajax({
                url: "{{route('last-news')}}",
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
                success: function (response) {
                $('#lastNews').attr('disabled', false);
                $('#lastNews').html('Haber Oluştur');
                    if(response.success == "success"){
                        Swal.fire({
                            title: "Başarılı",
                            text: "Haber Başarıyla Kayıt Edildi",
                            icon: "success",
                            timer: 2000, 
                            showConfirmButton: false 
                        });
                        setTimeout(function() {
                        }, 2000);

                    }else if(response.success == "error"){
                        Swal.fire({
                            title: "Başarısız",
                            text: "Haber Kayıt Edilirken Hata Oluştu. Bir Daha Deneyiniz.",
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

        $(document).on('click', '#lastNewsCreate', function(e) {
            e.preventDefault(); 
            newCreate();
            });

        function newCreate() {
            let news_title = $("#news_title").val();
            let spot = $("#spot").val();
            let location = $("#location").val();
            let editor = $("#news_editor").val();
            let newsID = $("#newsID").val();
            let uniq_words = $("#uniq_words").val();
            let news_text = CKEDITOR.instances['news_last'].getData();
            let formData = new FormData();
            formData.append('news_title', news_title);
            formData.append('news_text', news_text);
            formData.append('spot', spot);
            formData.append('location', location);
            formData.append('editor', editor);
            formData.append('newsID', newsID);
            formData.append('uniq_words', uniq_words);
            Swal.fire({
                title: 'Yükleniyor...',
                html: 'Lütfen bekleyin, haber oluşturuluyor',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading()
                }
            });
            $.ajax({
                url: "{{route('last-news-return')}}",
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
            }else if(response.success == "emptyText"){
                Swal.fire({
                    title: "Başarısız",
                    text: "Haber Açıklaması Boş Olamaz",
                    icon: "error",
                    timer: 2000, 
                    showConfirmButton: false 
                });
                setTimeout(function() {
                }, 2000);
            }else if(response.success == "emptyEditor"){
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
   });
</script>
@endsection


