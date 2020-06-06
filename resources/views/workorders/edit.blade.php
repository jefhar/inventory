@extends('layouts.app')

@section('title', 'Edit Work Order #' . $workOrder->luhn . ': ' . $workOrder->client->company_name )

@section('content')
  <div id="WorkOrdersEdit"></div>
  <div class="container">
    <div class="border card col-md rounded-sm row shadow"
         id="outline">
      <div class="card-header row">
        <div class="col">
          <h1 class="text-center"><span id="lockedHeader">The</span> Work Order
            #{{ str_pad($workOrder->luhn, config('app.padding.workorders'), '0', STR_PAD_LEFT) }}</h1>
          <p class="text-center">
            <span class="h3">{{ $workOrder->client->company_name }}</span>
            <br>
            <span class="h6">Created {{ $workOrder->created_at->format('j M Y') }}</span>
        </div>
      </div>
      <div
        aria-atomic="true"
        aria-live="polite"
        class="card-body"
        data-is-locked="{{ ($workOrder->is_locked) ? 'true' : 'false' }}"
        data-work-order-id="{{ $workOrder->luhn }}"
        id="workOrderBody"
        style="position: relative; min-height: 200px;"
      >
        <form class="container">
          <div class="form-row">
            <label
              class="col-form-label-sm"
              for="companyName"
            >Client Company Name:</label>
            <input
              class="form-control form-control-sm"
              id="companyName"
              name="company_name"
              placeholder="Company Name"
              required
              type="text"
              value="{{ $workOrder->client->company_name }}"
            />
          </div>
          <div class="form-row">
            <div class="col">
              <label
                class="col-form-label-sm"
                for="firstName"
              >First Name</label>
              <input
                class="form-control form-control-sm"
                id="firstName"
                name="first_name"
                placeholder="First Name"
                value="{{ $workOrder->client->person->first_name }}"
              />
            </div>
            <div class="col">
              <label
                class="col-form-label-sm"
                for="lastName"
              >Last Name</label>
              <input
                class="form-control form-control-sm"
                id="lastName"
                name="last_name"
                placeholder="Last Name"
                value="{{ $workOrder->client->person->last_name }}"
              />
            </div>
          </div>
          <div class="form-row">
            <div class="col-12 col-sm-6">
              <label
                class="col-form-label-sm"
                for="phoneNumber"
              >Phone</label>
              <input
                class="form-control form-control-sm"
                id="phoneNumber"
                name="phone_number"
                placeholder="Phone Number"
                value="{{ $workOrder->client->person->phone_number }}"
              />
            </div>
            <div class="col-12 col-sm-6">
              <label
                class="col-form-label-sm"
                for="email"
              >email</label>
              <input
                class="form-control form-control-sm"
                id="email"
                name="email"
                placeholder="Email Address"
                type="email"
                value="{{ $workOrder->client->person->email }}"
              />
            </div>
          </div>
          <div class="form-group form-row">
            <label
              class="col-form-label-sm"
              for="intake"
            >Intake Notes:</label><br/>
            <textarea
              class="form-control"
              name="intake"
              id="intake"
            >{{ $workOrder->intake }}</textarea>
          </div>
          <div class="row">
            <button
              class="btn btn-outline-primary col-4 offset-1"
              id="commitChangesButton"
              type="button">
              Commit Changes
            </button>
            <button
              class="btn btn-outline-secondary col-4 offset-2"
              type="reset">
              Revert Changes
            </button>
          </div>
          <div id='alert_row' class="row">

          </div>
        </form>
        <h2 class="mt-3">Inventory Items:</h2>
        <table class="table table-dark">
          <thead>
          <tr>
            <th scope="col">Product ID</th>
            <th scope="col">Make</th>
            <th scope="col">Model</th>
            <th scope="col">Type</th>
            <th scope="col">Serial</th>
            <th scope="col">Created At</th>
          </tr>
          </thead>
          <tbody id="productsTable">

          @foreach ($workOrder->products as $product)
            <tr>
              <th scope="row" class="col-1">
                <a class="btn btn-info"
                   href="/inventory/{{ $product->luhn }}">
                  {{ str_pad($product->luhn, config('app.padding.products'), '0', STR_PAD_LEFT) }}
                </a>
              </th>
              <td>{{$product->manufacturer->name}}</td>
              <td>{{$product->model}}</td>
              <td>{{$product->type->name}}</td>
              <td>{{$product->serial}}</td>
              <td>{{$product->created_at}}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
        @can(\App\Admin\Permissions\UserPermissions::WORK_ORDER_OPTIONAL_PERSON)
          <div class="row">
            <button
              class="btn btn-outline-primary col-6 offset-1"
              data-target="#addNewProductModal"
              data-toggle="modal"
              id="addInventoryItemButton"
              type="button"
            >Add Inventory Item
            </button>
            <button
              class="btn btn-sm col-1 offset-4"
              data-placement="bottom"
              data-toggle="tooltip"
              data-trigger="hover"
              id="lockButton"
              title="unlock work order"
              type="button"
            ><small><i id="lockIcon" class="fas"></i></small>
            </button>
          </div>
        @endcan
      </div>
    </div>
    <div
      class="modal"
      id="addNewProductModal"
      role="dialog"
      tabindex="-1">
      <div
        class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg"
        role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Add Product to Inventory</h5>
            <button
              aria-label="Close"
              class="close"
              data-dismiss="modal"
              type="button"
            ><span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div
            class="modal-body"
            id="productModalBody"
          >
            <div class="container-fluid">
              <div class="row">
                <label for="productType">Select existing product Type:</label>
                <select
                  class="form-control custom-select-sm"
                  id="productType"
                  name="product_type"
                  required
                >
                  <option disabled selected value> -- select an option --</option>
                  @foreach($types as $type)
                    <option value="{{ $type->slug }}">{{ $type->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="row" id="typeForm">
                <form
                  data-work-order-id="{{ $workOrder->luhn }}"
                  id="productForm"
                >
                </form>
              </div>
            </div>
          </div>
          <div class="modal-footer" id="modalFooter">
            <div class="row">
              <div class="spinner-border text-info mr-auto invisible" id="spinner"></div>
              <button
                class="btn btn-outline-secondary mr-4"
                data-dismiss="modal"
                id="cancelNewProductButton"
                type="button"
              >Cancel
              </button>
              <button
                class="btn btn-outline-primary"
                id="productSubmit"
                type="button"
                disabled
              >Add Product
              </button>
            </div>
            <div class="row" id="productError"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
