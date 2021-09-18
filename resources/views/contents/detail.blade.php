@extends('layouts.app')

@section('title', $news->content)

@section('css')
    <link rel="stylesheet" href="{{ asset('css/contents.css') }}"/>
@endsection

@section('content')
    <div class="news-container">
        <h3>{{ $news->content }}</h3>
        <div class="prev-page">
            <a href="{{ route('contents', $news->load_service) }}">
                Вернуться к списку новостей
            </a>
        </div>
        <div class="news__detail">
            @if (count(json_decode($news->detail->attachments)))
                <div class="image-wrapper">
                    @foreach(json_decode($news->detail->attachments) as $attachment)
                        <img src="{{ $attachment }}" alt="" />
                    @endforeach
                </div>
            @endif
            <div class="text-content">
                <p>{!! $news->detail->content !!}</p>
            </div>
        </div>
    </div>
@endsection
