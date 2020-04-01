@extends('layouts.app')

@section('title', 'View Product')

@section('content')
    <script>
      window.formRenderOptions = { formData: '{!! json_encode($formData) !!}', dataType: 'json' }
    </script>
    <div class="container" id="inventory_show">
        <div class="card">
            <div class="card-header">
                <h1 id="productId" data-product-id="{{ $product->id }}">Product #{{ $product->luhn }}</h1>
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
                        <div class="dropdown-menu" aria-labelledby="addToCartButton" id="cartsDropDownMenu">
                            @if (count($carts) > 0)
                                @foreach($carts as $cart)
                                    <button
                                            class="dropdown-item"
                                            id="cart_{{ $cart->id }}"
                                            type="button"
                                            onclick="addToCart({{ $cart->id }})"
                                    >{{$cart->client->company_name}}
                                    </button>
                                @endforeach

                                <div class="dropdown-divider" id="dropdownDivider"></div>
                            @endif
                            <button
                                    class="dropdown-item"
                                    id="newCartButton"
                                    type="button"
                                    onclick="createNewCart()"
                            >New Cart
                            </button>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
    <div class="modal fade" id="newCartModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New Client Shopping Cart</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="carts_create"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-outline-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
