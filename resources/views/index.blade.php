@extends('layouts.app')

@section('content')
    <div>Блок новостей</div>
    <div class="news">
        @foreach($newsList as $news)
            <div class="news-item" onclick="return window.location.href = '{{ $news->source_link }}'">
                <div class="news-item__content">
                    {{ $news->content }}
                </div>
                <div class="news-item__origin">
                    @if ($news->topic)
                        <div class="news-item__origin topic">{{ $news->topic }}</div>
                    @endif
                    <div class="news-item__origin time">
                        {{ $news->external_datetime }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
