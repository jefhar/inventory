@extends('layouts.app')

@push('scripts')
    <script src="{{ mix('js/app.js') }}" defer></script>
@endpush


@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        You are logged in!
                        <br/>
                        <br/>
                        <a href="/workorders/create">/workorders/create</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
