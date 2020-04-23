@extends('layouts.app')

@section('title', 'All Cart for ' . $name)

@section('content')
    <div class="container">
        <div
            class="accordion"
            id="cartIndex"
        >
            @foreach($carts as $cart)
                <div class="card">
                    <div
                        class="card-header"
                        id="cartHeader_{{ $cart->luhn }}"
                    >
                        <h2 class="mb-0">
                            <button
                                aria-controls="cardBody_{{ $cart->luhn }}"
                                aria-expanded="false"
                                class="btn btn-link"
                                data-target="#cartBody_{{ $cart->luhn }}"
                                data-toggle="collapse"
                                type="button"
                            >
                                {{ $cart->client->company_name }}
                            </button>
                        </h2>
                    </div>
                    <div
                        id="cartBody_{{ $cart->luhn }}"
                        class="collapse"
                        aria-labelledby="cartHeader_{{ $cart->luhn }}"
                        data-parent="cartIndex"
                    >
                        <div class="card-body">
                            list of items<br/>
                            in cart.
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        {{ $carts }}
    </div>
@endsection
