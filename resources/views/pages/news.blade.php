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
                <div class="mb-3 d-flex align-items-center gap-2">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 col-md-3">
                                    <label for="category" class="form-label">Haber Kategorisi Seçiniz</label>
                                <select class="form-select " id="categorySelect" aria-label="Default select example">
                                    <option selected>Kategori Seçiniz</option>
                                    @foreach ($category as $key => $item)
                                    <option class="categoryChange" value="{{$item->id}}">{{$item->category_name}}</option>
                                    @endforeach
                                  </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="category" class="form-label">Haber Bülteni Seçiniz</label>
                                <select class="form-select " id="bultenSelect" aria-label="Default select example">
                                <option selected>Bülten Seçiniz</option>
                                @foreach ($bulten as $key => $item)
                                <option class="bultenChange" value="{{$item->id}}">{{$item->bultein_name}}</option>
                                @endforeach
                            </select>
                            </div>
                            <div class="col-12 col-md-3">
                                <label for="category" class="form-label">Haber Türü Seçiniz</label>
                                <select class="form-select " id="styleSelect" aria-label="Default select example">
                                <option selected>Haber Türü Seçiniz</option>
                                @foreach ($style as $key => $item)
                                <option class="styleChange" value="{{$item->id}}">{{$item->new_style}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                        
                    </div>
                    <div class="cat_list">
                       
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
        var selectedCategory = null; // Global değişken olarak tanımla
        var selectedBulten = null; // Global değişken olarak tanımla
        var selectedStyle = null; // Global değişken olarak tanımla

        $('#categorySelect').on('change', function() {
            selectedCategory = $(this).val(); // Global değişkeni güncelle
        });
        $('#bultenSelect').on('change', function() {
            selectedBulten = $(this).val(); // Global değişkeni güncelle
        });
        $('#styleSelect').on('change', function() {
            selectedStyle = $(this).val(); // Global değişkeni güncelle
        });

        
    function newsAdd() {
            let news_title = $("#news_title").val();
            let uniq_words = $("#uniq_words").val();
            let spot = $("#spot").val();
            let location = $("#location").val();
            let editor = $("#editor").val();
            let category_id = selectedCategory;
            let bultein_id = selectedBulten;
            let new_style_id = selectedStyle;
            let news_text = CKEDITOR.instances['news_text'].getData();
            let formData = new FormData();
            formData.append('news_title', news_title);
            formData.append('uniq_words', uniq_words);
            formData.append('news_text', news_text);
            formData.append('spot', spot);
            formData.append('location', location);
            formData.append('editor', editor);
            formData.append('category_id', category_id);
            formData.append('bultein_id', bultein_id);
            formData.append('new_style_id', new_style_id);
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
                    let dataTitle = "";
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
                    if(data.data.news_title == null){
                        dataTitle = "Doldurulmadı";

                    }else{
                        dataTitle = data.data.news_title;
                    }
                    console.log(dataTitle)

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
                        <div class="mb-3">
                            <label for="editor" class="form-label text-white">Haber Başlığı</label>
                            <input type="text" class="form-control" id="news_title" value="${dataTitle}"/>
                        </div>
                        <div class="mb-3">
                            <label for="editor" class="form-label text-white">Değişmez Kelimeler</label>
                            <input type="text" class="form-control" id="uniq_words" value="${data.data.uniq_words}"/>
                        </div>
                        <div class="mb-3">
                            <label for="editor" class="form-label text-white">Spot Alanınız</label>
                            <input type="text" class="form-control" id="spot" value="${dataSpot}"/>
                        </div>
                        <div class="mb-3">
                            <label for="editor" class="form-label text-white">Mahreç</label>
                            <input type="text" class="form-control" id="location" value="${dataLocation}"/>
                        </div>
                        <div class="container">
                            <div class="row align-items-center align-items-center justify-content-center">
                                <div class="col-12 col-md-3">
                                    <label for="category" class="form-label text-white">Haber Kategorisi</label>
                                    <input type="text" class="form-control" id="category_name" disabled value="${data.data.category_name}"/>
                                    <input type="text" class="form-control" id="category_names" hidden value="${data.data.category_id}"/>
                                </div>
                                <div class="col-12 col-md-3">
                                    <label for="category" class="form-label text-white">Bülten</label>
                                    <input type="text" class="form-control" id="bultein_name" disabled value="${data.data.bultein_name}"/>
                                    <input type="text" class="form-control" id="bultein_names" hidden value="${data.data.bultein_id}"/>

                                </div>
                                <div class="col-12 col-md-3">
                                    <label for="category" class="form-label text-white">Bülten</label>
                                    <input type="text" class="form-control" id="style_name" disabled value="${data.data.new_style}"/>
                                    <input type="text" class="form-control" id="style_names" hidden value="${data.data.new_style_id}"/>

                                </div>    
                            </div>
                        <div>
                        <div class="mb-3">
                            <label for="news_editor" class="form-label text-white">Yayıncı</label>
                            <input type="text" class="form-control" id="news_editor" value="${data.data.editor}"/>

                        </div>
                        <div class="mb-3 ">
                            <input type="text" hidden id="newsID" value="${data.data.id}">
                            <input type="text" style="display:none !important;" id="news_return" class="form-control" value="${data.data.news}"/>
                            <label class="form-label text-white">Haber Metinlerinizin Ön İzlemesi</label>
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
                    var key4 = 0;
                    var key5 = 0;
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
                                            <input type="text" hidden id="category_id" value="${data.data.category_id}">
                                            <input type="text" hidden id="bultein_id" value="${data.data.bultein_id}">
                                            <input type="text" hidden id="new_style_id" value="${data.data.new_style_id}">
                                            <input type="text" hidden id="news_title_reply" value="${data.data.news_title}">
                                            <input type="text" hidden id="spot_reply" value="${data.data.spot}">
                                            <input type="text" hidden id="editor_reply" value="${data.data.editor}">
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
            let news_text = $('#news_return').val();
            let bultein_id = $('#bultein_names').val();
            let new_style_id = $('#style_names').val();
            let category_id = $('#category_names').val();
            let formData = new FormData();
            formData.append('news_title', news_title);
            formData.append('news_text', news_text);
            formData.append('spot', spot);
            formData.append('location', location);
            formData.append('editor', editor);
            formData.append('newsID', newsID);
            formData.append('bultein_id', bultein_id);
            formData.append('new_style_id', new_style_id);
            formData.append('category_id', category_id);
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

        $(document).on('click', '#lastNews', function(e) {
        e.preventDefault(); 
        newSave();
            });

        function newSave() {
            let category_id = $("#category_id").val();
            let bultein_id = $("#bultein_id").val();
            let new_style_id = $("#new_style_id").val();
            let news_title_reply = $("#news_title_reply").val();
            let spot_reply = $("#spot_reply").val();
            let news_reply = $("#news_reply").val();
            let location = $("#location").val();
            let editor_reply = $("#editor_reply").val();
            let formData = new FormData();
            formData.append('category_id', category_id);
            formData.append('bultein_id', bultein_id);
            formData.append('new_style_id', new_style_id);
            formData.append('spot_reply', spot_reply);
            formData.append('news_reply', news_reply);
            formData.append('news_title_reply', news_title_reply);
            formData.append('editor_reply', editor_reply);
            formData.append('location', location);
            Swal.fire({
                title: 'Yükleniyor...',
                html: 'Lütfen bekleyin, oluşturuluyor',
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


