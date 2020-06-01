@extends('layouts.app')

@section('title')
  View Product - {{ $product->model}}
@endsection

@section('content')
  <script>
    const removeFromCart = productLuhn => {
      axios.delete(`/pendingSales/${productLuhn}`).then(() => {
        // Remove alert
        const productAddedAlert = document.getElementById('productAddedAlert')
        productAddedAlert.classList.replace('alert-primary', 'alert-warning')
        productAddedAlert.innerHTML = `Product removed from cart. <a href="${window.location.href}">Reload page</a> to add it to a cart.`
      })
    }

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
            data-product-id="{{ $product->id }}"
            data-product-luhn="{{ $product->luhn }}"
        >Product #{{ $product->luhn }}</h1>
        <p class="lead">{{ $product->type->name }}</p>
      </div>
      <div class="card-body">
        <div id="product_show"></div>
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
                      data-cart-id="{{ $cart->id }}"
                    >{{$cart->client->company_name}}
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
                  onclick="createNewCart()"
                >New Cart
                </button>
              </div>
            </div>
          </div>
        @elseif ($product->status === \Domain\Products\Models\Product::STATUS_IN_CART)
          <div class="card-footer">
            <div id="productAddedAlert" class="alert alert-secondary" role="alert">
              Product is in Cart for <a
                href="{{ route(\App\Carts\Controllers\CartController::SHOW_NAME, $product->cart) }}">{{ $product->cart->client->company_name }}
                .</a>
              <br><br>
              <span class="text-danger"><a class="text-danger"
                                           onclick="removeFromCart({{ $product->luhn }});"><i
                    id="removeProduct" class="fas fa-unlink">&#8203;</i> Remove product from cart.</a>
                                Not available for sale.</span>
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
            <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-outline-primary">Save changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
