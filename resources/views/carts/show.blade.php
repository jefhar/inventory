@extends('layouts.app')

@section('title', 'Cart ' . $cart->luhn . ': ' . $cart->client->company_name)

@section('content')
  @php
    $border = [
      \Domain\Carts\Models\Cart::STATUS_INVOICED => 'success',
      \Domain\Carts\Models\Cart::STATUS_OPEN => 'secondary',
      \Domain\Carts\Models\Cart::STATUS_VOID => 'danger',
    ];
  @endphp
  <div id="cartShow"
       class="container">
    <div id="card-border" class="card border-{{ $border[$cart->status] }}">
      <div class="card-header">
        <h1
          id="cartId"
          data-cart-id="{{ $cart->luhn }}">
          <svg class="bi bi-cart3 mr-1" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
               xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd"
                  d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
          </svg>
          Cart # {{ $cart->luhn }}: <span id="cartStatus" class="capitalize">{{ $cart->status }}</span>
        </h1>
        <p class="lead">{{ $cart->client->company_name }}</p>
        <p>{{ $cart->client->person->first_name }} {{ $cart->client->person->last_name }}
          <i class="pl-4 fas fa-phone-alt"></i>&nbsp;{{ $cart->client->person->phone_number }}
          <br>Created at {{ $cart->created_at->format('j M Y H:i') }}</p>
      </div>
      <div class="card-body">
        <button
          class="btn btn-outline-primary mr-sm-4"
          id="invoiceButton"
          type="button"
          {{ $cart->status !== \Domain\Carts\Models\Cart::STATUS_OPEN ? 'disabled' : '' }}>
          <i class="far fa-check-circle mr-1"></i>Mark Invoiced
        </button>
        <button
          class="btn btn-danger drop-button"
          id="destroyCartButton"
          type="button"
          {{ $cart->status !== \Domain\Carts\Models\Cart::STATUS_OPEN ? 'disabled' : '' }}
        ><i class="far fa-trash-alt mr-1"></i>Destroy Cart
        </button>
        <span id="totalPrice" class="float-right">Cart Total:&nbsp;
        <span id="cartTotalPrice" class="float-right"></span>
      </span>
      </div>

      <div class="card-body">
        <table
          id="cartTable"
          class="table table-dark table-hover col-12">
          <thead class="thead-light">
          <tr>
            <th scope="col">Product ID</th>
            <th scope="col">Make</th>
            <th scope="col">Model</th>
            <th scope="col">Type</th>
            <th scope="col">Serial</th>
            <th scope="col">Price</th>
            <th scope="col">Remove</th>
          </tr>
          </thead>
          <tbody id="cartTableBody">
          @foreach ($cart->products as $product)
            <tr id="productRow_{{ $product->luhn }}">
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
              <td>
                <button
                  id="productPriceButton_{{ $product->luhn }}"
                  class="btn btn-outline-secondary price-button"
                  data-product-id="{{ $product->luhn }}"
                  data-product-manufacturer="{{ $product->manufacturer->name }}"
                  data-product-model="{{ $product->model }}"
                  data-product-price="{{ $product->price }}"
                  type="button"
                  title="Click to change product price"
                ><i class="fas fa-dollar-sign mr-1"></i><span
                    id="productPriceButtonText_{{ $product->luhn }}"
                    class="price text-light"
                  >{{ sprintf('%03.2F', $product->price) }}</span></button>
              </td>
              <td>
                <button
                  type="button"
                  data-product-id="{{ $product->luhn }}"
                  class="btn btn-danger drop-product-button">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="modal" tabindex="-1" role="dialog" id="productCostModal">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5>Product <span id="productModalHeader"></span></h5>
          <button type="button" class="close" data-dimiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="form-inline" id="form">
            <div class="form-group">
              <label class="mr-2" for="productPriceInput">Unit price:</label> $
              <input
                aria-describedby="originalPriceHelp"
                class="form-control ml-1"
                id="productPriceInput"
                min="0"
                pattern="[\d+]\.?[\d\d]?"
                placeholder="0.00"
                required
                step="0.01"
                type="number"
              >
            </div>
            <div class="invalid-feedback">Please enter a positive dollar value.</div>
          </form>
          <small id="originalPriceHelp" class="form-text text-muted">Changing from <span
              id="originalPrice"></span>.</small>
        </div>
        <div class="modal-footer">

          <br>
          <button id="costSubmitButton" type="submit" class="btn btn-outline-primary" value="Save"><i
              class="far fa-save mr-1"></i>Save
          </button>
          <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal"><i
              class="far fa-times-circle mr-1"></i>Cancel
          </button>
        </div>
      </div>
    </div>
  </div>


  <div
    aria-hidden="true"
    aria-labelledby="destroyTitle"
    class="modal fade"
    id="destroyModal"
    role="dialog"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5
            class="modal-title"
            id="destroyModalTitle"
          >
            <span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i></span
            >Destroy <span class="delete-type"></span>?
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
            <p class="cart-visible">This will permanently destroy the cart and return all items to available inventory.
              Are you sure sure you want to do this?</p>
            <p class="product-visible">This will remove the product from the cart. Are you sure sure you want to do
              this?</p>
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
            id="destroyModalButton"
            class="btn btn-danger"><i class="fas fa-trash mr-1"></i>
            Destroy <span class="delete-type"></span>
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
    id="productPriceToast"
    role="alert"
    style="position:absolute; top: 6rem; right: 0;"
  >
    <div class="toast-header">
      <strong class="text-success mr-auto"><i class="far fa-check-circle"></i
        >&nbsp;Product Updated</strong>
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
