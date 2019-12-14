@extends('layouts.app')

@push('scripts')
    <script src="{{ mix('js/workorders/create.js') }}" defer></script>
@endpush

@section('content')
    <div id="workorders_create">New Product Form</div>
@endsection
