@extends('layouts.app')



@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header border-primary bg-light">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        You are logged in, {{ Auth::user()->name }}
                        ! <!-- {{ Auth::user()->getRoleNames()->first() }} -->
                        <br/>
                        <br/>
                        <div class="row">
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Create a New WorkOrder</h3>
                                        <p class="card-text">
                                            Create a new WorkOrder to keep track of where equipment comes from. Each
                                            WorkOrder should keep track of one decommission project.
                                        </p>

                                        <a href="/workorders/create" class="btn btn-outline-primary">Create new
                                            WorkOrder</a>
                                    </div>
                                </div>
                            </div>
                            @hasanyrole(
                            \App\Admin\Permissions\UserRoles::TECHNICIAN . '|' .
                            \App\Admin\Permissions\UserRoles::OWNER. '|' .
                            \App\Admin\Permissions\UserRoles::SUPER_ADMIN)
                            <div class="col-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h3 class="card-title">Create a New Product Type</h3>
                                        <p class="card-text">
                                            Create or Edit a new Product Type using the formBuilder.
                                        </p>
                                        <a href="/types/create" class="btn btn-outline-primary">Edit Product Types</a>
                                    </div>
                                </div>
                            </div>
                            @endhasanyrole
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
