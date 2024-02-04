@extends('layout')
@section('content')
<style>
    .xin_link{
        text-decoration: none;
        color: #ffffff;
    }
    .xin_link:hover{
        text-decoration: none;
        color: #eeee;
    }
</style>
@php
    $results = $xin['datasource'];

    @endphp
   <div class="container">
    <div class="row justify-content-start">
        @foreach ($results as $key => $item)
        <div class="col-12 mb-5">
            <div class="d-flex align-items-center text-white">
                <a href="https://english.news.cn/{{ $item['publishUrl'] }}"class="xin_link d-flex align-items-center" target="_blank"><h4>{{++$key}} .</h4>
                <h4>{{ $item['title'] }}</h4></a>
            </div>
        </div>
        @endforeach
    </div>
   </div>

@endsection
@section('scripts')

@endsection