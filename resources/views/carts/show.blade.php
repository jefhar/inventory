@extends('layouts.app')

@section('title', 'Cart for ' . $cart->client->company_name)

@section('content')
    <div class="container">
        {{ $cart }}
    </div>
@endsection
