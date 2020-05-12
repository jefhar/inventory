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
              {{ Auth::user()->name }}
            </a>

            <div class="dropdown-menu dropdown-menu" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="{{ route('logout') }}"
                 onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                {{ __('Logout') }}
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
                 href="{{ route(\App\WorkOrders\Controllers\WorkOrdersController::CREATE_NAME) }}">Create</a>
              <div class="dropdown-divider"></div>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{ route(\App\Products\Controllers\InventoryController::INDEX_NAME) }}">
              Inventory
            </a>
          </li>
          @can(\App\Admin\Permissions\UserPermissions::MUTATE_CART)
            <li class="nav-item">
              <a class="nav-link" href="{{ route(\App\Carts\Controllers\CartsController::INDEX_NAME) }}">
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
