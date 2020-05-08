@extends('layouts.app')

@section('content')
  <div id="types_create" class="container">
    <div class="row">
      <div class="card col-12">
        <h2 class="card-header">Create New Product Type</h2>
        <div class="card-body">
          <div class="row">
            <p>Manufacturer and model fields will automatically be added to the form. Please do
              not add them through this form builder. If serial numbers are needed for this
              product type, you must add one text field and update its <span class="font-weight-bolder">Name</span>
              attribute to <code>serial</code>. Remember to also update the <span
                class="font-weight-bolder">Label</span> attribute</p>
          </div>
          <div class="d-flex justify-content-between mb-3">
            <button type="button" class="btn btn-outline-info" id="loadButton">Load Existing&emsp;&emsp;<i
                class="fas fa-file-download"></i></button>
            <button type="button" class="btn btn-outline-secondary" id="previewButton"
                    data-show-on-click="preview">Preview&emsp;&emsp;<i class="far fa-eye"></i></button>
            <button type="button" class="btn btn-outline-primary" id="saveButton">Save Form&emsp;&emsp;<i
                class="far fa-save"></i></button>
            <button type="button" class="btn btn-outline-warning" id="clearButton">Clear&emsp;&emsp;<i
                class="fas fa-eraser"></i></button>
          </div>
          <div id="spinner" class="spinner-border text-info invisible"></div>
          <div id="alert"></div>
          <div id='formbuilder' class="row bg-light py-2 visible">
            <div id="productType" class="col-12"></div>
            <div id="fb-editor" class="col-12"></div>
            <div id="fb-render" class="col-12"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Load Modal -->
  <div id="loadProductModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="loadModalLabel"
       aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loadModalLabel">Load Existing Product Type Form</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="loadpane">
          <label for="typesList">Select an existing product type:</label>
          <select id="typesList" name="list_of_types" size="8">
            @foreach($types as $type)
              <option value="{{ $type->slug }}">{{ $type->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button id="loadTypeButton" type="button" class="btn btn-outline-primary">Load</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /LoadModal -->
  <!-- Save Modal -->
  <div id="saveProductModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="saveModalLabel"
       aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="saveModalLabel">Save New Type</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="savepane">
          <div class="form-group">
            <label for="saveType">Enter name of product type</label>
            <input
              class="form-control"
              id="saveType"
              name="saveType"
              placeholder="Enter product type"
              type="text"
            >
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
          <button id="saveTypeButton" type="button" class="btn btn-outline-primary">Save</button>
        </div>
      </div>
    </div>
  </div>
  <!-- /SaveModal -->
@endsection
