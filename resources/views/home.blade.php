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
                    <h3 class="card-title">
                      <svg class="bi bi-receipt-cutoff mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                           fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                              d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v13h-1V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0l-.509-.51L2 2.118V15H1V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zM0 15.5a.5.5 0 0 1 .5-.5h15a.5.5 0 0 1 0 1H.5a.5.5 0 0 1-.5-.5z"/>
                        <path fill-rule="evenodd"
                              d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm8-8a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>
                      </svg>
                      Create a New Work&shy;Order
                    </h3>
                    <p class="card-text">
                      Create a new Work&shy;Order to keep track of where equipment comes from.
                      Each
                      Work&shy;Order should keep track of one decommission project.
                    </p>

                    <a href="{{ route(\App\WorkOrders\Controllers\WorkOrdersController::CREATE_NAME) }}"
                       class="btn btn-outline-primary">
                      <i class="fas fa-file-invoice-dollar mr-1"></i>Create new WorkOrder</a>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="card mb-4">
                  <div class="card-body">
                    <h3 class="card-title"><i class="fas fa-table mr-1"></i>View Inventory</h3>
                    <p class="card-text">
                      See all items available for sale.
                    </p>

                    <a href="{{ route(\App\Products\Controllers\InventoryController::INDEX_NAME) }}"
                       class="btn btn-outline-primary"><i class="fas fa-table mr-1"></i>View Inventory</a>
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
                      <a href="{{ route(\App\Types\Controllers\TypesController::CREATE_NAME) }}"
                         class="btn btn-outline-primary">Edit Product
                        Types</a>
                    </div>
                  </div>
                </div>
              @endcan
              @can(\App\Admin\Permissions\UserPermissions::MUTATE_CART)
                <div class="col-6">
                  <div class="card mb-4">
                    <div class="card-body">
                      <h3 class="card-title">
                        <svg class="bi bi-cart3 mr-1" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd"
                                d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                        </svg>
                        View Carts
                      </h3>
                      <p class="card-text">
                        View or edit your saved carts.
                      </p>
                      <a href="{{ route(\App\Carts\Controllers\CartsController::INDEX_NAME) }}"
                         class="btn btn-outline-primary">
                        <svg class="bi bi-cart3 mr-1" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                             xmlns="http://www.w3.org/2000/svg">
                          <path fill-rule="evenodd"
                                d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                        </svg>
                        View Your Carts</a>
                    </div>
                  </div>
                </div>
              @endcan
              {{-- This is the log out card. Make sure it is last --}}
              <div class="w-100"></div>
              <div class="col-6">
                <div class="card border-danger">
                  <div class="card-body text-danger">
                    <h3 class="card-title"><i class="fas fa-sign-out-alt mr-1"></i>Logout</h3>
                    <p class="card-text">
                      Log out of the system.
                    </p>

                    <a class="btn btn-outline-danger" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                      <i class="fas fa-sign-out-alt mr-1"></i>{{ __('Logout') }}
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
