@extends('layouts.app')

@section('title', 'All Cart for ' . $name)

@section('content')
    <div class="container">
        {{ $carts }}
    </div>
@endsection
