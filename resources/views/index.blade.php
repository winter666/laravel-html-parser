@extends('layouts.app')

@section('css')
    <style>
        .container {
            width: 90%;
            margin: 0 auto;
        }

        .service-list {
            display: flex;
            flex-wrap: wrap;
        }

        .service-list__item {
            border: 2px solid #808080;
            border-radius: 15px;
            width: 45%;
            margin: 15px 15px 15px 0;
            text-align: center;
            cursor: pointer;
        }
        .service-list__item__name {
            font-size: 3rem;
            color: #3f3f3f;
        }


        @media (max-width: 500px) {
            .container {
                width: 100%;
                margin: 0;
            }
            .service-list__item {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <h3>Источники</h3>
        <div class="service-list">
            @foreach($serviceList as $serviceKey => $serviceName)
                <div class="service-list__item" onclick="window.location.href='{{ route('contents', $serviceKey) }}'">
                    <span class="service-list__item__name">{{ $serviceName }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection
