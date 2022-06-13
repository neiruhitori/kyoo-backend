<style>
  .kyoo-sublink {
    white-space: normal !important;
  }

  .kyoo-logo-container {
    display: flex;
    justify-content: center;
    padding: 1.5rem;
  }
</style>

<!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
      <a class="kyoo-logo-container" style="display: flex;" href="{{ route('dashboard') }}">
        <img
          src="{{ asset('img/logo.svg') }}"
          alt=""
          style="height: 40px;"
        >
      </a>

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

      <li class="nav-item {{ !request()->is('cs/conversations*') ?: 'active' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#audio" aria-expanded="true" aria-controls="audio">
          <i class="fas fa-microphone"></i>
          <span>Audio</span>
        </a>

        <div class="collapse {{ !request()->is('cs/conversations*') ?: 'show' }}" id="audio" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a
              class="collapse-item kyoo-sublink {{ !request()->is('cs/conversations') ?: 'active' }}"
              href="{{ route('cs.conversations.index') }}"
            >
              Rekam Suara
            </a>

            <a
              class="collapse-item kyoo-sublink {{ !request()->is('cs/conversations/record') ?: 'active' }}"
              href="{{ route('cs.conversations.record') }}"
            >
              Riwayat Perekaman
            </a>
          </div>
        </div>
      </li>

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