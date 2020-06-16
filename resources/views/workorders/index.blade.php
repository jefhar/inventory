@extends('layouts.app')

@section('content')
  {{ time() }}
  <h1>
    <svg class="bi bi-receipt-cutoff mr-1" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
         xmlns="http://www.w3.org/2000/svg">
      <path fill-rule="evenodd"
            d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v13h-1V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0l-.509-.51L2 2.118V15H1V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zM0 15.5a.5.5 0 0 1 .5-.5h15a.5.5 0 0 1 0 1H.5a.5.5 0 0 1-.5-.5z"/>
      <path fill-rule="evenodd"
            d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm8-8a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>
    </svg>
    Work Orders
  </h1>
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
