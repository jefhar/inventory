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
          Mark Invoiced
        </button>
        <button
          class="btn btn-outline-warning"
          id="destroyButton"
          type="button"
          {{ $cart->status !== \Domain\Carts\Models\Cart::STATUS_OPEN ? 'disabled' : '' }}
        >Destroy Cart
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
              <td>
                <button
                  class="btn btn-outline-info"
                  data-product-id="{{ $product->luhn }}"
                  data-product-manufacturer="{{ $product->manufacturer->name }}"
                  data-product-model="{{ $product->model }}"
                  data-product-price=" {{ $product->price }}"
                  type="button"
                ><span
                    id="price{{ $product->luhn }}"
                    class="price"
                  >${{ sprintf('%03.2F', $product->price) }}</span><i
                    class="far fa-edit text-light float-right"

                    title="Click to change product price"
                  ></i></button>
              </td>
              <td>
                <button
                  type="button"
                  class="btn btn-outline-danger">
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
          <h5>Product <span id="modalProductId"></span></h5>
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
          <button id="costSubmitButton" type="submit" class="btn btn-outline-primary" value="Save"><i
              class="far fa-save pr-1"></i>Save
          </button>
          <button type="reset" class="btn btn-outline-secondary" data-dismiss="modal"><i
              class="far fa-times-circle pr-1"></i>Cancel
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
