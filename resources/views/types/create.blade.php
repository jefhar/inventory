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
@endsection
