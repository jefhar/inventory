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
        >
          <svg class="bi bi-minecart-loaded mr-1" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
               xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M4 15a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 1a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm8-1a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm0 1a2 2 0 1 0 0-4 2 2 0 0 0 0 4zM.115 3.18A.5.5 0 0 1 .5 3h15a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 14 12H2a.5.5 0 0 1-.491-.408l-1.5-8a.5.5 0 0 1 .106-.411zm.987.82l1.313 7h11.17l1.313-7H1.102z"/>
            <path fill-rule="evenodd"
                  d="M6 1.5a2.498 2.498 0 0 1 4 0c.818 0 1.545.394 2 1 .67 0 1.28.265 1.729.694l-.692.722A1.493 1.493 0 0 0 12 3.5c-.314 0-.611-.15-.8-.4-.274-.365-.71-.6-1.2-.6-.314 0-.611-.15-.8-.4a1.497 1.497 0 0 0-2.4 0c-.189.25-.486.4-.8.4-.507 0-.955.251-1.228.638a2.65 2.65 0 0 1-.634.634 1.511 1.511 0 0 0-.263.236l-.75-.662a2.5 2.5 0 0 1 .437-.391 1.63 1.63 0 0 0 .393-.393A2.498 2.498 0 0 1 6 1.5z"/>
          </svg>
          Product #{{ $product->luhn }}</h1>
        <p class="lead">{{ $product->type->name }}<span
            class="ml-2 badge badge-pill badge-{{ \Illuminate\Support\Str::snake($product->status) }}">{{ $product->status }}</span>
        </p>
      </div>
      <div class="card-body">
        <div id="productView"></div>
      </div>
      @can(\App\Admin\Permissions\UserPermissions::MUTATE_PRODUCT_VALUES)
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
              ><i class="fas fa-cart-plus mr-1"></i>Add To Cart &hellip;
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
                href="{{ route(\App\Carts\Controllers\CartController::SHOW_NAME, $product->cart) }}">{{ $product->cart->client->company_name }}
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
