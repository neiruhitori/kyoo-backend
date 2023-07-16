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
                <span>Appointment Aktif</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ route('cs.appointment.future.index') }}" class="nav-link">
                <i class="fas fa-list-ul"></i>
                <span>Daftar Appointment</span>
            </a>
        </li>
    @endif

    @if (Auth::user()->Branch->BranchType->is_exhibition)
        <li class="nav-item">
            <a class="nav-link" href="{{route('dashboard')}}">
                <i class="fas fa-list-ul"></i>
                <span>{{ __('Exhibition Queue') }}</span></a>
        </li>
    @endif

    @if (
      Auth::user()->Branch->BranchType->is_premium &&
      Auth::user()->Branch->hasAccess('Voice Recording')
    )
        <li class="nav-item">
            <a class="nav-link" href="{{ route('cs.voiceRecorder.index') }}">
                <i class="fas fa-microphone"></i>
                <span>Rekam Suara</span></a>
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
    @if (
        Auth::user()->Branch->BranchType->is_premium &&
        Auth::user()->Branch->BranchType->is_direct_queue
    )
        @foreach (Auth::user()->Branch->getCsActiveMenus(Auth::user()->WorkstationVct->workstation_id) as $key => $menu)
            @if (is_object($menu))
                <li class="nav-item {{ !request()->is($menu->route) ?: 'active'  }}">
                    <a class="nav-link" href="{{ url($menu->route) }}">
                        <i class="fas fa-cog"></i>
                        <span>{{ __($menu->name_label) }}</span></a>
                </li>
            @elseif(is_array($menu))
                <li class="nav-item">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#branch-information"
                       aria-expanded="true" aria-controls="branch-information">
                        <i class="fas fa-fw fa-building"></i>
                        <span>{{ __($key) }}</span>
                    </a>

                    <div class="collapse {{ !request()->is('admin-branch/branch-information/*') ?: 'show' }}"
                         id="branch-information" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                            @foreach ($menu as $submenuKey => $submenu)
                                <a
                                    class="collapse-item kyoo-sublink {{ !request()->is($submenu->route) ?: 'active'  }}"
                                    href="{{ url($submenu->route) }}"
                                >
                                    {{ __($submenu->name_label) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </li>
            @endif
        @endforeach
    @endif

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
