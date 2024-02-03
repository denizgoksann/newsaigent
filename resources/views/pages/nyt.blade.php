@extends('layout')
@section('content')
<style>
    .nyt_link{
        text-decoration: none;
        color: #ffffff;
    }
    .nyt_link:hover{
        text-decoration: none;
        color: #eeee;
    }
</style>
@php
    $results = $nyt['results'];

    @endphp
   <div class="container">
    <div class="row justify-content-start">
        @foreach ($results as $key => $item)
        <div class="col-12 mb-5">
            <div class="d-flex align-items-center text-white">
                <a href="{{ $item['url'] }}"class="nyt_link d-flex align-items-center" target="_blank"><h4>{{++$key}} .</h4>
                <h4>{{ $item['title'] }}</h4></a>
            </div>
            <a href="{{ $item['url'] }}"class="nyt_link" target="_blank">{{ $item['abstract'] }}</a>
        </div>
        @endforeach
    </div>
   </div>

@endsection
@section('scripts')
    

@endsection


