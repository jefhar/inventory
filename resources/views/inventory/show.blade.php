@extends('layouts.app')

@section('title', 'View Product')

@section('content')
    <script>
      window.formRenderOptions = {
        formData: '{!! json_encode($formData) !!}',
        dataType: 'json'
      }
    </script>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Product #{{ $product->luhn }}</h1>
                <p class="lead">{{ $product->type->name }}</p>
            </div>
            <div class="card-body">
                <div id="product_show"></div>
            </div>
        </div>
    </div>
@endsection
