@extends('layouts.app')

@section('title', 'Complete Inventory')

@section('content')
  <div class="container">
    <h1 class="text-center">Complete Inventory</h1>
    <p class="text-muted text-center">Generated at {{ date('j F, Y g:i:s a e') }}</p>
    <div class="row">
      @if ($products->isEmpty())
        No Products Available For Sale.
      @else
        <table class="table table-dark table-hover col-12">
          <thead class="thead-light">
          <tr>
            <th scope="col">Product ID</th>
            <th scope="col">Make</th>
            <th scope="col">Model</th>
            <th scope="col">Type</th>
            <th scope="col">Serial</th>
            <th scope="col">Created At</th>
          </tr>
          </thead>
          <tbody>
          @foreach ($products as $product)
            <tr>
              <th scope="row">
                <a class="btn btn-info"
                   href="/inventory/{{ $product->luhn }}">
                  {{ str_pad($product->luhn, config('app.padding.products'), '0', STR_PAD_LEFT) }}
                </a>
              </th>

              <td>{{$product->manufacturer->name}}</td>
              <td>{{$product->model}}</td>
              <td>{{$product->type->name}}</td>
              <td>{{$product->serial }}</td>
              <td>{{$product->created_at}}</td>
            </tr>
          @endforeach
          </tbody>
        </table>

        {{ $products->links() }}
      @endif
    </div>
  </div>
@endsection
