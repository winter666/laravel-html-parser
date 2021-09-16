@extends('layouts.app')

@section('css')
    <style>
        .news-container {
            width: 90%;
            margin: 0 auto;
        }
        .prev-page a {
            font-size: 0.9rem;
            color: #808080;
        }
        .news {
            margin: 30px 0;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: flex-start;
            padding: 0 50px;
        }
        .news-item {
            width: 550px;
            min-height: 200px;
            border: 2px solid #808080;
            border-radius: 15px;
            margin: 15px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .news-item__content {
            text-align: justify;
            margin-bottom: 10px;
        }
        .news-item__origin {
            display: flex;
            justify-content: space-between;
            color: #808080;
        }
        .news-item__footer > .actions {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
        }
        .news-item__footer > .actions > a {
            border: 1px solid #808080;
            border-radius: 5px;
            display: block;
            padding: 3px 5px;
        }
        .news-item__footer > .actions > a:hover {
            background-color: #808080;
            color: #ffffff;
        }
    </style>
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
                            <a href="{{ $news->source_link }}" target="_blank">Читать на этом сервисе...</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
