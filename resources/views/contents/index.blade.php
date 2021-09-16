@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/contents.css') }}"/>
@endsection

@section('content')
    <div class="news-container">
        <h3>Блок новостей из источника {{ $serviceName }}</h3>
        <div class="prev-page">
            <a href="{{ route('home') }}">
                Вернуться к списку источников
            </a>
        </div>
        <div class="news">
            @foreach($newsList as $news)
                <div class="news-item">
                    <div class="news-item__content">
                        {{ mb_substr($news->content, 0, 200) }}
                    </div>
                    <div class="news-item__origin">
                        <div class="common">
                            @if ($news->topic)
                                <div class="topic">{{ $news->topic }}</div>
                            @endif
                            <div class="time">
                                {{ $news->external_datetime }}
                            </div>
                        </div>
                    </div>
                    <div class="news-item__footer">
                        <div class="actions">
                            <a href="{{ $news->source_link }}" target="_blank">Читать источник...</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
