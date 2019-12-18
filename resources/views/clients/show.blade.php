@extends('layouts.app')


@section('title', 'WorkOrders for ' . $client->company_name)

@section('content')
    <div class="container">

        <div class="row">
            <h1>{{ $client->company_name }}</h1>
        </div>
        <div class="row mb-2">
            <small>Contact: {{ $client->person->first_name }}
                {{ $client->person->last_name }},&nbsp;
                <i class="fas fa-phone-alt"></i>&nbsp;{{ $client->person->phone_number }}</small>
        </div>
        <div class="row">
            <table class="table table-dark table-hover col-12">
                <thead class="thead-light">
                <tr class="d-flex">
                    <th scope="col" class="col-1">ID</th>

                    <th scope="col" class="col-2">Created At</th>
                    <th scope="col" class="col-2">Updated At</th>
                    <th scope="col" class="col-7">Intake</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($client->workorders as $workOrder)
                    <tr class="d-flex">
                        <th scope="row" class="col-1">
                            <a class="btn {{ $workOrder->is_locked ? 'btn-warning' : 'btn-success' }}"
                               href="/workorders/{{ $workOrder->luhn }}/edit">
                                {{ str_pad($workOrder->luhn, 6, '0', STR_PAD_LEFT) }}&nbsp;<i
                                        class="fas fa-{{ $workOrder->is_locked ? 'lock' : 'unlock-alt' }}"></i>
                            </a>
                        </th>

                        <td class="col-2">
                            {{ $workOrder->created_at->format('d M Y h:i') }}
                        </td>
                        <td class="col-2">
                            {{ $workOrder->updated_at->format('d M Y h:i') }}
                        </td>
                        <td class="d-inline-block text-truncate co">
                            {{ $workOrder->intake }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
