@extends('layouts.app')

@section('title', 'All Carts for ' . $name)

@section('content')
  <div class="container">
    <h2>All Carts for {{ \Illuminate\Support\Facades\Auth::user()->name }}</h2>
    @if(count($carts) > 0)
      <div
        class="accordion"
        id="cartIndex"
      >
        @foreach($carts as $cart)
          <div
            class="card"
            id="cart{{ $cart->luhn }}"
          >
            <div
              class="card-header"
              id="cartHeader_{{ $cart->luhn }}"
            >
              <h2 class="mb-0">
                <button
                  aria-controls="cartBody_{{ $cart->luhn }}"
                  aria-expanded="false"
                  class="btn btn-link collapsed"
                  data-target="#cartBody_{{ $cart->luhn }}"
                  data-toggle="collapse"
                  type="button"
                ><span class="text-left">
                  Cart {{ $cart->luhn }} &ndash; {{ $cart->client->company_name }}
                    &ndash; Created {{ $cart->created_at->toDayDateTimeString() }}
                  </span>
                </button>
              </h2>
            </div>
            <div
              aria-labelledby="cartHeader_{{ $cart->luhn }}"
              class="collapse"
              data-parent="#cartIndex"
              id="cartBody_{{ $cart->luhn }}"
            >
              <div class="card-body">
                <ul>
                  @foreach( $cart->products as $product)
                    <li>#{{ $product->luhn }}
                      <span class="manufacturer">{{ $product->manufacturer->name }}</span>
                      <span class="model">{{ $product->model }}</span>
                      Serial&nbsp;<a
                        class="serial"
                        href="/carts/{{ $cart->luhn }}"
                        title="View product #{{ $cart->luhn }}"
                      >{{ $product->serial }}</a>.
                    </li>
                  @endforeach
                </ul>
                <br>
                <a
                  class="btn btn-outline-primary card-link"
                  role="button"
                  href="/carts/{{ $cart->luhn }}"
                ><i class="far fa-edit"></i> Edit cart.</a>
                <button
                  class="btn btn-outline-warning card-link"
                  data-toggle="modal"
                  data-target="#destroyCartModal"
                  data-cart="{{ $cart->luhn }}"
                ><i class="far fa-trash-alt"></i>&emsp;Destroy Cart.
                </button>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @else
      You have no carts.
    @endif
  </div>


  <div
    aria-hidden="true"
    aria-labelledby="destroyCartTitle"
    class="modal fade"
    id="destroyCartModal"
    role="dialog"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5
            class="modal-title"
            id="destroyCartTitle"
          >
            <span class="text-warning"><i class="fas fa-exclamation-triangle"></i></span
            >&emsp;Destroy Cart?
          </h5>
          <button
            aria-label="Close"
            class="close"
            data-dimiss="modal"
            type="button"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="left-warning-border">
            <p>This will permanently destroy the cart and return all items to available inventory. Are you sure sure you
              want to do this?</p>
            <p><em>This cannot be undone.</em></p>
          </div>
        </div>
        <div class="modal-footer">
          <button
            class="btn btn-outline-secondary"
            data-dismiss="modal"
            type="button"
          >Close
          </button>
          <button
            id="destroyCartButton"
            class="btn btn-outline-warning"><i class="far fa-trash-alt"></i>&emsp;Destroy Cart
          </button>
        </div>
      </div>
    </div>
  </div>


  <div
    aria-atomic="true"
    aria-live="assertive"
    class="toast"
    data-delay="4000"
    id="destroyedToast"
    role="alert"
    style="position:absolute; top: 6rem; right: 0;"
  >
    <div class="toast-header">
      <strong class="text-warning mr-auto"><i class="fas fa-exclamation-triangle"></i
        >Cart Destroyed</strong>
      <button
        type="button"
        class="ml-2 mb-1 close"
        data-dismiss="toast"
        aria-label="Close"
      ><span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div
      class="toast-body"
      id="toastBody"
    ></div>
  </div>

@endsection
