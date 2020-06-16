@php
$totalPrice = 0;
@endphp
@component('mail::message')
# Invoice

An invoice for your products has been generated which contains the following products:

@component('mail::table')
Product ID | Manufacturer | Model | Price
-----------|--------------|-------|-------:
@foreach($cart->products as $product)
{{ $product->luhn }} | {{ $product->manufacturer->name }} | {{ $product->model }} | $ {{ $product->price }}
@php
$totalPrice += $product->price
@endphp
@endforeach
@endcomponent

The total price of the invoice is $ {{ $totalPrice }} not including shipping and taxes.

Please pay your Sales Representative, {{ $cart->user->name }}.

  Thanks,<br>
  {{ config('app.name') }}
@endcomponent


