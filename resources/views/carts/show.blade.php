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
    <div id="CartShow"
         data-cart-id="{{ $cart->luhn }}"
         data-cart-status="{{ $cart->status }}"
         data-cart-created-at="{{ $cart->created_at->format('j M Y H:i') }}"
         data-client-company-name="{{ $cart->client->company_name }}"
         data-client-first-name="{{ $cart->client->person->first_name }}"
         data-client-last-name="{{ $cart->client->person->last_name }}"
         data-client-phone-number="{{ $cart->client->person->phone_number }}"
         data-products='{!! $products->toJson() !!}'
         data-product-padding="{{ config('app.padding.products') }}"
         class="mb-5"
    >
    </div>

@endsection
