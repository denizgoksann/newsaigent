@extends('layout')
@section('content')
<style>
    .twp_link{
        text-decoration: none;
        color: #ffffff;
    }
    .twp_link:hover{
        text-decoration: none;
        color: #eeee;
    }
</style>
@php
    $results = $twp['items'];
    
    @endphp
    
   <div class="container">
    <div class="row justify-content-start">
        @foreach ($results as $key => $item)
        
        <div class="col-12 mb-5">
            <div class="d-flex align-items-center text-white">
                <a href="{{ $item['canonical_url'] }}"class="twp_link d-flex align-items-center" target="_blank"><h4>{{++$key}} .</h4>
                    @php
                        $deneme = $item['additional_properties'];
                        $title = $deneme['page_title']
                    @endphp
                <h4> {{$title}}</h4>
                </a>
            </div>
                <a href="{{ $item['canonical_url'] }}"class="twp_link" target="_blank">
                    @foreach ($item['description'] as $key => $dsc)
                        {{$dsc}}
                    @endforeach
                </a>
        </div>
        @endforeach
    </div>
   </div>
   @endsection
@section('scripts')
    

@endsection