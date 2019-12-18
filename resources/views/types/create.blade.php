@extends('layouts.app')

@push('scripts')
    <script src="{{ mix('/js/types/create.js') }}" defer></script>
@endpush

@section('content')
    <style type="text/css">
        <!--
        .form-rendered #fb-editor {
            display: none;
        }

        #fb-render {
            display: none;
        }

        .form-rendered #fb-render {
            display: block;
        }

        #edit-form {
            display: none;
            float: right;
        }

        .form-rendered #edit-form {
            display: block;
        }

        -->
    </style>
    <div id="types_create" class="container">
        <div class="row">
            <div class="card col-12">
                <h2 class="card-header">Create new Type form</h2>
                <div class="card-body">
                    <div class="row">
                        <p>Manufacturer and model fields will automatically be added to the form. Please do
                            not add them through this form builder.</p>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <button type="button" class="btn btn-outline-info" id="loadButton">Load Existing&emsp;&emsp;<i class="fas fa-file-download"></i></button>
                        <button type="button" class="btn btn-outline-secondary" id="previewButton" data-show-on-click="preview">Preview&emsp;&emsp;<i class="far fa-eye"></i></button>
                        <button type="button" class="btn btn-outline-primary" id="saveButton">Save Form&emsp;&emsp;<i class="far fa-save"></i></button>
                        <button type="button" class="btn btn-outline-warning" id="clearButton">Clear&emsp;&emsp;<i class="fas fa-eraser"></i></button>
                    </div>
                    <div id='formbuilder' class="row bg-light py-2">
                        <div id="fb-editor" class="col-12"></div>
                        <div id="fb-render" class="col-12"></div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Load Modal -->
    <div id="loadModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="loadModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title" id="loadModalLabel">Load Existing Form</h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                <div class="modal-body" id="loadpane">
                    put list of existing forms here.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-outline-primary">Load</button>
                </div>
            </div>
        </div>
    </div>
    <!-- /LoadModal -->
@endsection
