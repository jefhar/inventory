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
          data-cart-luhn="{{ $cart->luhn }}">
          Cart # {{ $cart->luhn }}: <span id="cartStatus" class="capitalize">{{ $cart->status }}</span>
        </h1>
        <p class="lead">{{ $cart->client->company_name }}</p>
        <p>{{ $cart->client->person->first_name }} {{ $cart->client->person->last_name }}
          <i class="pl-4 fas fa-phone-alt"></i>&nbsp;{{ $cart->client->person->phone_number }}
          <br>Created at {{ $cart->created_at->format('j M Y H:i') }}</p>
      </div>
      <div class="card-body">
        <button id="invoiceButton" type="button"
                class="btn btn-outline-primary" {{ $cart->status === \Domain\Carts\Models\Cart::STATUS_INVOICED ? 'disabled' : '' }}>
          Mark Invoiced
        </button>
        <button id="destroyButton" type="button" class="btn btn-outline-danger">Destroy Cart</button>
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
          </tr>
          </thead>
          <tbody id="cartTableBody">
          @foreach ($cart->products as $product)
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
              <td><span
                  id="price{{ $product->luhn }}"
                  class="price"
                >${{ sprintf('%03.2F', $product->price) }}</span> <i
                  class="far fa-edit text-light float-right"
                  data-product-luhn="{{ $product->luhn }}"
                  data-product-manufacturer="{{ $product->manufacturer->name }}"
                  data-product-model="{{ $product->model }}"
                  data-product-price=" {{ $product->price }}"
                  title="Click to change product price"
                ></i></td>
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
          <h5>Product <span id="modalProductLuhn"></span></h5>
          <button type="button" class="close" data-dimiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form class="form-inline" id="form">
            <div class="form-group">
              <label class="mr-2" for="productPrice">Unit price:</label> $
              <input
                aria-describedby="originalPriceHelp"
                class="form-control ml-1"
                id="productPrice"
                min="0"
                pattern="[\d+]\.?[\d\d]?"
                placeholder="0.00"
                required
                step=0.01
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
          <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button id="costSubmitButton" type="submit" class="btn btn-outline-primary" value="Save">Save</button>
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
