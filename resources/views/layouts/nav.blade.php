<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="{{ url('/home') }}">
      {{ config('app.name', 'Laravel') }}
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <!-- Left Side Of Navbar -->
      <ul class="navbar-nav mr-auto">
        <!-- Authentication Links -->
        @guest
          <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
          </li>
        @else
          <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              {{ \Auth::user()->name }}
            </a>

            <div class="dropdown-menu dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="{{ route('logout') }}"
                 onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt mr-1"></i>{{ __('Logout') }}
              </a>

              <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
              </form>
            </div>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="workOrderDropdownMenuLink" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Work Orders
            </a>
            <div class="dropdown-menu" aria-labelledby="workOrderDropdownMenuLink">
              <a class="dropdown-item"
                 href="{{ route(\App\WorkOrders\Controllers\WorkOrderController::CREATE_NAME) }}">
                <svg class="bi bi-receipt-cutoff mr-1" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
                     xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd"
                        d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v13h-1V2.118l-.137-.274-.51.51a.5.5 0 0 1-.707 0L12 1.707l-.646.647a.5.5 0 0 1-.708 0L10 1.707l-.646.647a.5.5 0 0 1-.708 0L8 1.707l-.646.647a.5.5 0 0 1-.708 0L6 1.707l-.646.647a.5.5 0 0 1-.708 0L4 1.707l-.646.647a.5.5 0 0 1-.708 0l-.509-.51L2 2.118V15H1V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zM0 15.5a.5.5 0 0 1 .5-.5h15a.5.5 0 0 1 0 1H.5a.5.5 0 0 1-.5-.5z"/>
                  <path fill-rule="evenodd"
                        d="M3 4.5a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 1 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm8-8a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 0 1h-1a.5.5 0 0 1-.5-.5z"/>
                </svg>
                Create</a>
            </div>
          </li>
          @can(\App\Admin\Permissions\UserPermissions::CREATE_OR_EDIT_PRODUCT_TYPE)
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="inventoryDropdownMenuLink" role="button"
                 data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Inventory
              </a>
              <div class="dropdown-menu" aria-labelledby="inventoryDropdownMenuLink">
                <a class="dropdown-item"
                   href="{{ route(\App\Products\Controllers\InventoryController::INDEX_NAME) }}"><i
                    class="fas fa-table mr-1"></i>List Inventory</a>
                <a class="dropdown-item"
                   href="{{ route(\App\Types\Controllers\TypeController::CREATE_NAME) }}"><i
                    class="fab fa-wpforms mr-1"></i>Create and Edit Product
                  Types</a>
              </div>
            </li>
          @endcan
          @cannot(\App\Admin\Permissions\UserPermissions::CREATE_OR_EDIT_PRODUCT_TYPE)
            <li class="nav-item">
            <li class="nav-item">
              <a class="nav-link" href="{{ route(\App\Products\Controllers\InventoryController::INDEX_NAME) }}">
                Inventory
              </a>
            </li>
          @endcannot
          @can(\App\Admin\Permissions\UserPermissions::MUTATE_CART)
            <li class="nav-item">
              <a class="nav-link" href="{{ route(\App\Carts\Controllers\CartController::INDEX_NAME) }}">
                Your Carts
              </a>
            </li>
          @endcan
        @endguest
      </ul>

      <!-- Right Side Of Navbar -->
      <ul class="navbar-nav ml-auto">

      </ul>
    @auth
      <!-- AutoComplete Search Bar -->
        <form class="form-inline">
          <div class="input-group">
            <input id="site-search" class="form-control" type="search" placeholder="Search"
                   aria-label="Search">
            <div class="input-group-append">
              <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
          </div>
        </form>
      @endauth
    </div>
  </div>
</nav>
