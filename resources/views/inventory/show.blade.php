@extends('layouts.app')

@section('title')
  View Product - {{ $product->model}}
@endsection

@section('content')
  <script>
    window.formRenderOptions = {
      formData: '{!! json_encode(
    $formData,
    JSON_THROW_ON_ERROR | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG
    ) !!}', dataType: 'json',
    }
  </script>

  <div class="container" id="inventoryShow">
    <div class="card">
      <div class="card-header">
        <h1 id="productId"
            data-product-id="{{ $product->luhn }}"
        >Product #{{ $product->luhn }}</h1>
        <p class="lead">{{ $product->type->name }}<span
            class="ml-2 badge badge-pill badge-{{ \Illuminate\Support\Str::snake($product->status) }}">{{ $product->status }}</span>
        </p>
      </div>
      <div class="card-body">
        <div id="productView"></div>
      </div>
      @can(\App\Admin\Permissions\UserPermissions::EDIT_SAVED_PRODUCT)
        @if($product->status === \Domain\Products\Models\Product::STATUS_AVAILABLE)
          <div class="card-footer" id="cardFooter">
            <div class="dropdown">
              <button
                aria-expanded="false"
                aria-haspopup="true"
                class="btn btn-outline-primary dropdown-toggle"
                data-toggle="dropdown"
                id="addToCartButton"
                type="button"
              ><i class="fas fa-cart-plus pr-1"></i>Add To Cart &hellip;
              </button>
              <div class="dropdown-menu" aria-labelledby="addToCartButton" id="cartsDropDownMenu">
                @if (count($carts) > 0)
                  @foreach($carts as $cart)

                    <button
                      class="dropdown-item"
                      id="cart_{{ $cart->luhn }}"
                      type="button"
                      data-cart-id="{{ $cart->luhn }}"
                    ><i class="fas fa-cart-plus"></i> {{$cart->client->company_name}}
                      - created
                      {{\Carbon\Carbon::createFromFormat("Y-m-d H:i:s", $cart->created_at)
->diffForHumans(now(), ['syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW]) }}
                    </button>
                  @endforeach

                  <div class="dropdown-divider" id="dropdownDivider"></div>
                @endif
                <button
                  class="dropdown-item"
                  id="newCartButton"
                  type="button"
                ><i class="fas fa-shopping-cart"></i> New Cart
                </button>
              </div>
            </div>
          </div>
        @elseif ($product->status === \Domain\Products\Models\Product::STATUS_IN_CART)
          <div class="card-footer">
            <div id="productAddedAlert" class="alert alert-secondary" role="alert">
              Product is in Cart for <a
                href="{{ route(\App\Carts\Controllers\CartsController::SHOW_NAME, $product->cart) }}">{{ $product->cart->client->company_name }}
                .</a><br>
              <button type="button" class="btn btn-outline-danger" id="productInCartButton">
                <i class="fas fa-trash-alt"></i> Remove product from cart.
              </button>
            </div>
          </div>
        @endif
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
            <button type="button" class="btn btn-outline-danger" data-dismiss="modal"><i
                class="far fa-times-circle"></i>&nbsp;Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
