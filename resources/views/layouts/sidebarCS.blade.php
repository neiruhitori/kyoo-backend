<style>
  .kyoo-sublink {
    white-space: normal !important;
  }

  .kyoo-logo {
    width: 72px;
    height: 72px;
    background-color: #FFFFFF;
    border-radius: 8px;
    margin: 0 auto;
  }

  .kyoo-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
  }

  .kyoo-logo-link {
    height: 100%;
    width: 100%;
    display: block;
    padding: .3rem;
  }
</style>

<!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      @if (Auth::user()->Branch->logo)
        <div class="kyoo-logo mb-3 mt-3">
          <a href="{{ route('dashboard') }}" class="kyoo-logo-link">
            <img
              src="{{ asset(Auth::user()->Branch->logo ? 'storage/' . Auth::user()->Branch->logo : 'img/logo.svg') }}"
              alt=""
            >
          </a>
        </div>
      @else
        <a class="sidebar-brand mb-2 text-center" href="{{ route('dashboard') }}">
          <img
            src="{{ asset('img/logo.svg') }}"
            alt=""
            style="height: 40px;"
          >
        </a>
      @endif

      <!-- Divider -->
      <hr class="sidebar-divider my-0">
      
      <li class="nav-item">
        <a class="nav-link" href="{{ route('cs.branchQrCode') }}">
          <i class="fas fa-fw fa-qrcode"></i>
          <span>{{ __('Show QR Code') }}</span></a>
      </li>

      @if (Auth::user()->Branch->BranchType->is_direct_queue)
        <li class="nav-item">
          <a class="nav-link" href="{{route('cs.directQueue.monitor')}}">
            <i class="fas fa-list-ul"></i>
            <span>{{ __('Direct Queue') }}</span></a>
        </li>
      @endif

      @if (Auth::user()->Branch->BranchType->is_appointment)
        <li class="nav-item">
          <a class="nav-link" href="{{route('dashboard')}}">
            <i class="fas fa-list-ul"></i>
            <span>{{ __('Appointment') }}</span></a>
        </li>
      @endif

      @if (Auth::user()->Branch->BranchType->is_exhibition)
        <li class="nav-item">
          <a class="nav-link" href="{{route('dashboard')}}">
            <i class="fas fa-list-ul"></i>
            <span>{{ __('Exhibition Queue') }}</span></a>
        </li>
      @endif

      <li class="nav-item">
        <a class="nav-link" href="
            @if (Auth::user()->Branch->BranchType->is_appointment)
              {{route('cs.report.daily')}}
            @elseif (Auth::user()->Branch->BranchType->is_direct_queue)
              {{route('cs.report.directQueue.daily')}}
            @elseif (Auth::user()->Branch->BranchType->is_exhibition)
              {{route('cs.exhibition.report.daily')}}
            @endif
        ">
          <i class="fas fa-list-ul"></i>
          <span>{{ __('Daily Report') }}</span>
        </a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <li class="nav-item">
        <a 
          class="nav-link" 
          href="#"
          onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
        >
          <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
          Logout
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
          </form>
        </a>
      </li>

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->