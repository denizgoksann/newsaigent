@extends('layout')
@section('content')

<div class="container">
    <div class="row justify-content-start align-items-center">
@foreach ($news as $item)

        <div class="col-12 mb-5">
            <div class="d-flex  text-white flex-column justify-content-start text-start">
                <div class="d-flex align-items-center justify-content-between">
                    @php
                        $title ="";
                        if ($item->news_title == 'null'){
                            $title = "Başlık Boş";
                        }else{
                            $title = $item->news_title;
                        }

                        $editor ="";
                        if ($item->editor == 'null'){
                            $editor = "Editor Boş";
                        }else{
                            $editor = $item->editor;
                        }
                    @endphp
                   
                    <h4>{{ $title }}</h4>
                    <div class="d-flex align-items-center gap-5">
                        <h4>{{ $item->category_name }}</h4>
                        <h4>{{ $item->new_style }}</h4>
                        <h4>{{ $item->bultein_name }}</h4>
                    </div>
                </div>
                <p class="mt-2">{{ $item->news }}</p>

                <div class="d-flex w-100 justify-content-end">
                    <span>{{$editor}}</span>
                </div>
            </div>
        </div>
        <hr style="height: 5px; font-size:20px;" class="text-light">

@endforeach
@foreach ($desifre as $live)

        <div class="col-12 mb-5">
            <div class="d-flex  text-white flex-column justify-content-start text-start">
                <div class="d-flex align-items-center justify-content-between">
                   
                    <h4>{{ $live->desifre_title }}</h4>
                    <div class="d-flex align-items-center gap-5">
                        <h4>{{ $live->desifre }}</h4>
                    </div>
                </div>
                <p class="mt-2">{{ $live->news }}</p>

                <div class="d-flex w-100 justify-content-end">
                    <span>{{$editor}}</span>
                </div>
            </div>
        </div>
        <hr style="height: 5px; font-size:20px;" class="text-light">

@endforeach
</div>
</div>

@endsection
@section('scripts')
    

@endsection


