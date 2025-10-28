@props(['title' => null])

@extends('layouts.app')

@section('title', $title ?? config('app.name'))

@section('content')
    @if(isset($header))
        <div class="mb-4">{{ $header }}</div>
    @endif

    {{ $slot }}
@endsection
