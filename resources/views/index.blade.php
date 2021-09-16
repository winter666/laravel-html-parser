@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{asset('css/home.css')}}"/>
@endsection

@section('content')
    <div class="container">
        <h1 class="main-title">Источники</h1>
        <div class="service-list">
            @foreach($serviceList as $serviceKey => $serviceName)
                <div class="service-list__item" onclick="window.location.href='{{ route('contents', $serviceKey) }}'">
                    <span class="service-list__item__name">{{ $serviceName }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection
