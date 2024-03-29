@extends('layouts.app')

@section('title', 'All Carts for ' . $name)

@section('content')
  <div class="container">
    <h2>
      <svg class="bi bi-cart3 mr-1" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
           xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd"
              d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
      </svg>
      All Carts for {{ \Illuminate\Support\Facades\Auth::user()->name }}</h2>
    @if(count($carts) > 0)
      <div
        class="accordion"
        id="cartIndex"
      >
        @foreach($carts as $cart)
          <div
            class="card"
            id="cart_{{ $cart->luhn }}"
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
                        href="/inventory/{{ $product->luhn }}"
                        title="View product #{{ $product->luhn }}"
                      >{{ $product->serial }}</a>.
                    </li>
                  @endforeach
                </ul>
                <br>
                <a
                  class="btn btn-outline-primary"
                  role="button"
                  href="/carts/{{ $cart->luhn }}"
                ><i class="far fa-edit mr-1"></i> Edit cart.</a>
                <div
                  class="drop-button1"
                  data-cart-id="{{ $cart->luhn }}"
                  data-text="Destroy Cart."
                  data-type="cart"
                >
                </div>
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
            <span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i></span
            >Destroy Cart?
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
          <div class="left-danger-border">
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
          ><i class="far fa-times-circle mr-1"></i>Close
          </button>
          <button
            id="destroyCartButton"
            class="btn btn-danger"><i class="fas fa-trash mr-1"></i>
            Destroy Cart
          </button>
        </div>
      </div>
    </div>
  </div>


  <div
    aria-atomic="true"
    aria-live="assertive"
    class="toast"
    data-delay="8000"
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
