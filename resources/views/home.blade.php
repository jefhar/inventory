@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-sm-12 col-md-10">
                <div class="card">
                    <div class="card-header border-primary bg-light">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        You are logged in, {{ Auth::user()->name }}!
                    <!-- {{ Auth::user()->getRoleNames()->first() }} -->
                        <br/>
                        <br/>
                        <div class="row hyphenation">
                            <div class="col-6">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h3 class="card-title">Create a New Work&shy;Order</h3>
                                        <p class="card-text">
                                            Create a new Work&shy;Order to keep track of where equipment comes from.
                                            Each
                                            Work&shy;Order should keep track of one decommission project.
                                        </p>

                                        <a href="{{ route(\App\WorkOrders\Controllers\WorkOrdersController::CREATE_NAME) }}"
                                           class="btn btn-outline-primary">Create new
                                            WorkOrder</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h3 class="card-title">View Inventory</h3>
                                        <p class="card-text">
                                            See all items available for sale.
                                        </p>

                                        <a href="{{ route(\App\Products\Controllers\InventoryController::INDEX_NAME) }}" class="btn btn-outline-primary">View Inventory</a>
                                    </div>
                                </div>
                            </div>
                            @can(\App\Admin\Permissions\UserPermissions::CREATE_OR_EDIT_PRODUCT_TYPE)
                                <div class="col-6">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <h3 class="card-title">Create a New Product Type</h3>
                                            <p class="card-text">
                                                Create or Edit a new Product Type using the formBuilder.
                                            </p>
                                            <a href="{{ route(\App\Types\Controllers\TypesController::CREATE_NAME) }}" class="btn btn-outline-primary">Edit Product
                                                Types</a>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                            {{-- This is the log out card. Make sure it is last --}}
                            <div class="w-100"></div>
                            <div class="col-6">
                                <div class="card border-danger">
                                    <div class="card-body text-danger">
                                        <h3 class="card-title">Logout</h3>
                                        <p class="card-text">
                                            Log out of the system.
                                        </p>

                                        <a class="btn btn-outline-danger" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
