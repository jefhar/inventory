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
                                <div class="card mb-4">
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
                                <div class="card mb-1">
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
                            {{-- This is the logout card. Make sure it is last --}}
                            <div class="w-100"></div>
                            <div class="col-6">
                                <div class="card border-danger">
                                    <div class="card-body text-danger">
                                        <h3 class="card-title">Logout</h3>
                                        <p class="card-text">
                                            Log out of the system
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
