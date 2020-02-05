@extends('layouts.app')

@section('title', 'View Product')

@section('content')
    <script>
      window.formRenderOptions = { formData: '{!! json_encode($formData) !!}', dataType: 'json' }
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
            @can(\App\Admin\Permissions\UserPermissions::EDIT_SAVED_PRODUCT)
                <div class="card-footer">
                    <div class="dropdown">
                        <button
                                aria-expanded="false"
                                aria-haspopup="true"
                                class="btn btn-outline-primary dropdown-toggle"
                                data-toggle="dropdown"
                                id="addToCardButton"
                                type="button"
                        >Add To Cart &hellip;
                        </button>
                        <div class="dropdown-menu" aria-labelledby="addToCartButton">
                            @foreach($carts as $cart)
                                <button
                                        class="dropdown-item"
                                        id="cart_{{ $cart->id }}"
                                        type="button"
                                        onclick="addToCart({{ $cart->id }}, {{ $product->id }})"
                                >{{$cart->client->company_name}}
                                </button>
                            @endforeach
                            @if (count($carts)) > 0)
                            <div class="dropdown-divider" id="dropdownDivider"></div>
                            @endif
                            <button
                                    class="dropdown-item"
                                    id="newCart"
                                    type="button"
                                    onclick="createNewCart({{ $product->id }})"
                            >New Cart
                            </button>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
@endsection
