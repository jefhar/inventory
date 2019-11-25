@extends('layouts.app')

@push('scripts')
    <script src="{{ mix('js/workorders/index.js') }}" defer></script>
@endpush

@section('content')
    {{ time() }}
    <h1>Work Orders</h1>
    {{ $workOrders->links() }}
    @foreach($workOrders as $workOrder)
        {{ $workOrder }}
    @endforeach

@endsection

@section('Othercontent')
    {{--
    {{ $workOrders }}
    {{ $showlocked = $showlocked?? '' }}
    <script type="text/javascript">
      handleChange = () => {
        console.log('Change event fired.')
        const urlParams = (new URL(location)).searchParams
        const showLocked = urlParams.get('showlocked')
        const toggle = document.getElementById('filter-locked')
        const checked = toggle.checked
        console.log('showLocked: `' + showLocked + '` checked: `' + checked + '`')

        if (checked && showLocked === null) {
          urlParams.set('showlocked', 'yes')
          window.location.search = urlParams
        }
        console.log(checked)
      }

    </script>
    <div class="container">
        <div class="card shadow">
            <h2 class="card-header">WorkOrders `{{ $showlocked }}`</h2>
            <div class="card-body">
                <div class="card-title"
                     text-center>{{ $workOrders->links('workorders.index')->with('showlocked', $showlocked?? 'no') }}
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="filter-locked"
                               {{ $showlocked ? 'checked': '' }}
                               onchange="handleChange()">
                        <label class="custom-control-label" for="filter-locked"
                        >
                            showLocked `{{ $showlocked }}`?
                        </label>
                    </div>
                </div>
                <table class="table table-dark table-striped">
                    <thead class="thead-light">
                    <tr>
                        <th scope="col">&nbsp;</th>
                        <th scope="col">ID</th>
                        <th scope="col">Client</th>
                        <th scope="col">Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($workorders as $workOrder)
                        <tr>
                            <th scope="row">
                                <i class="fas fa-lock{{ $workOrder->is_locked ? ' text-warning' : '-open text-success' }}">
                                </i>
                            </th>
                            <th scope="row">
                                <a class="btn btn-{{ $workOrder->is_locked ? 'warning' : 'success' }}"
                                   href="/workorders/{{ $workOrder->id }}">
                                    {{ $workOrder->id}}
                                </a>
                            </th>
                            <td>{{ $workOrder->client->company_name }}</td>
                            <td>{{ $workOrder->created_at->format('j F Y, h:i a') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    --}}
@endsection
