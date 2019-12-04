@extends('layouts.app')

@push('scripts')
    <script src="{{ mix('js/workorders/edit.js') }}" defer></script>
@endpush

@section('title', 'Edit Work Order #' . $workOrder->id . ': ' . $workOrder->client->company_name )

@section('content')
    <div id="workorders_edit"></div>
    <div class="container">
        <div id="outline" class="row shadow-sm border border-{{ $workOrder->is_locked ? 'warning' : 'success' }}">
            <div class="card col-md">
                <div class="card-header row">
                    <div class="col">
                        <h1 class="text-center">Work Order</h1>
                    </div>
                    <div class="col-sm-3 col-md-2 shadow-sm">
                        <div class="row justify-content-center">
                            Work Order #
                        </div>
                        <div class="row justify-content-center workorder-id">
                            {{ str_pad($workOrder->id, 6, '0', STR_PAD_LEFT) }}
                        </div>
                        <div class="row justify-content-center">
                            {{ $workOrder->created_at->format('j M Y') }}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group form-row">
                            <label
                                    class="form-group col-form-label-sm"
                                    for="company_name"
                            >Client Company Name:</label>
                            <input
                                    class="form-control form-control-sm"
                                    id="company_name"
                                    name="company_name"
                                    required
                                    type="text"
                                    value="{{ $workOrder->client->company_name }}"
                            />
                        </div>
                        <div class="form-group form-row">
                            <label
                                    class="col-sm-2 d-md-none col-form-label-sm"
                                    for="first_name"
                            >First Name</label>
                            <div class="col">
                                <input
                                        class="form-control form-control-sm"
                                        id="first_name"
                                        name="first_name"
                                        value="{{ $workOrder->client->person->first_name }}"
                                />
                            </div>
                            <label
                                    class="col-sm-2 d-md-none col-form-label-sm"
                                    for="last_name"
                            >Last Name</label>
                            <div class="col">
                                <input
                                        class="form-control form-control-sm"
                                        id="last_name"
                                        name="last_name"
                                        value="{{ $workOrder->client->person->last_name }}"
                                />
                            </div>
                        </div>
                        <div class="form-group form-row">
                            <label
                                    class="col-sm-2 d-md-none col-form-label-sm"
                                    for="phone_number"
                            >Phone</label>
                            <div class="col">
                                <input
                                        class="form-control form-control-sm"
                                        id="phone_number"
                                        name="phone_number"
                                        value="{{ $workOrder->client->person->phone_number }}"
                                />
                            </div>
                            <label
                                    class="col-sm-2 d-md-none col-form-label-sm"
                                    for="email"
                            >email</label>
                            <div class="col">
                                <input
                                        class="form-control form-control-sm"
                                        id="email"
                                        name="email"
                                        type="email"
                                        value="{{ $workOrder->client->person->email }}"
                                />
                            </div>
                        </div>
                        <div class="form-group form-row">
                            <label
                                    class="form-check-label"
                                    for="intake"
                            >Intake Notes:</label><br/>
                            <textarea
                                    class="form-control"
                                    name="intake"
                                    id="intake"
                            >{{ $workOrder->intake }}</textarea>
                        </div>
                        <div class="row">
                            <button class="btn btn-outline-success col-4 offset-1" id="update_button" type="submit">
                                Update
                            </button>
                            <button class="btn btn-warning col-4 offset-2" type="reset">Reset</button>
                        </div>
                        <div id='alert_row' class="row">

                        </div>
                    </form>
                    <h2>Inventory Items:</h2>
                    <table class="table table-dark">
                        <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Make</th>
                            <th scope="col">Model</th>
                            <th scope="col">Type</th>
                            <th scope="col">Something</th>
                        </tr>
                        </thead>
                        <tbody id="products_table">
                        @if(isset($products))
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{$product->id}}</td>
                                    <td>{{$produt->manufacturer}}</td>
                                    <td>{{$product->model}}</td>
                                    <td>{{$produt->type}}</td>
                                    <td>{{$product->created_at}}</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                    <div class="row">
                        <button
                                class="btn btn-outline-primary col-6 offset-1"
                                id="add_inventory_button"
                                type="button"
                        >Add Inventory Item
                        </button>
                        <button
                                class="btn btn-sm col-1 offset-4"
                                data-placement="bottom"
                                data-toggle="tooltip"
                                data-is-locked="{{ ($workOrder->is_locked) ? 'true' : 'false' }}"
                                data-work-order-id="{{ $workOrder->id }}"
                                id="lock_button"
                                title="unlock work order"
                                type="button"
                        ><small><i id="lock-icon" class="fas"></i></small>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row shadow-lg border border-info mt-4">
            {{ $workOrder }}
        </div>
    </div>
@endsection
