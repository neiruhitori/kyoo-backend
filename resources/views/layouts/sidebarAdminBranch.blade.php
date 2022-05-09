<style>
    .kyoo-sublink {
      white-space: normal !important;
    }
</style>

<!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <a class="sidebar-brand d-flex mb-2" href="{{route('home')}}">
        <img src="{{asset('img/logo.svg')}}" alt="" style="height: 40px;">
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="{{route('home')}}">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>{{ __('Dashboard') }}</span></a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{ route('adminBranch.branchQrCode') }}">
          <i class="fas fa-fw fa-qrcode"></i>
          <span>{{ __('Branch QR Code') }}</span></a>
      </li>

      @if (Auth::user()->Branch->BranchType->is_premium && Auth::user()->Branch->BranchType->is_direct_queue)
          <li class="nav-item">
            <a class="nav-link" href="{{route('adminBranch.directQueue.monitor')}}" target="_blank">
              <i class="fas fa-fw fa-tv"></i>
              <span>{{ __('Direct Queue Monitor') }}</span></a>
          </li>
      @endif

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#instruction-of-use" aria-expanded="true" aria-controls="instruction-of-use">
          <i class="fas fa-fw fa-info-circle"></i>
          <span>{{ __('Instruction for Use') }}</span>
        </a>

        <div class="collapse" id="instruction-of-use" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item kyoo-sublink" href="{{ route('adminBranch.branchConfigGuide') }}">
              {{ __('How to Configure Qeueu') }}
            </a>
            <a class="collapse-item kyoo-sublink" href="{{ route('adminBranch.customerGuide') }}">
              {{ __('Customer Guide') }}
            </a>
          </div>
        </div>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#branch-information" aria-expanded="true" aria-controls="branch-information">
          <i class="fas fa-fw fa-building"></i>
          <span>{{ __('Branch Information') }}</span>
        </a>

        <div class="collapse" id="branch-information" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item kyoo-sublink" href="{{ route('adminBranch.branch.profile') }}">
              {{ __('Profile') }}
            </a>
            <a class="collapse-item kyoo-sublink" href="{{ route('adminBranch.branch.location') }}">
              {{ __('Location') }}
            </a>
          </div>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#branch-configuration" aria-expanded="true" aria-controls="branch-configuration">
          <i class="fas fa-fw fa-cog"></i>
          <span>{{ __('Branch Configuration') }}</span>
        </a>

        <div class="collapse" id="branch-configuration" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item kyoo-sublink" href="{{ route('adminBranch.department.index') }}">
              {{ __('Department') }}
            </a>

            <a class="collapse-item kyoo-sublink" href="{{ route('adminBranch.schedule.index') }}">
              {{ __('Schedule') }}
            </a>

            <a class="collapse-item kyoo-sublink" href="{{ route('adminBranch.workstation.index') }}">
              {{ __('Workstation') }}
            </a>

            <a class="collapse-item kyoo-sublink" href="{{ route('adminBranch.user.index') }}">
              {{ __('Virtual Counter') }}
            </a>

            @if (Auth::user()->Branch->BranchType->is_premium)
              <a class="collapse-item kyoo-sublink" href="{{ route('adminBranch.feature') }}">
                {{ __('Features') }}
              </a>
            @endif

            @if (Auth::user()->Branch->BranchType->is_premium && Auth::user()->Branch->BranchType->is_direct_queue)
              <a class="collapse-item kyoo-sublink" href="{{ route('adminBranch.tvDisplayConfiguration.index') }}">
                Monitor Antrian (TV)
              </a>
            @endif
          </div>
        </div>
      </li>

      <hr class="sidebar-divider my-0">

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#report" aria-expanded="true" aria-controls="report">
          <i class="fas fa-fw fa-file-alt"></i>
          <span>{{ __('Report') }}</span>
        </a>

        <div class="collapse" id="report" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a
              class="collapse-item kyoo-sublink"
              href="
                @if (Auth::user()->Branch->BranchType->is_appointment)
                  {{ route('adminBranch.report.daily') }}
                @elseif (Auth::user()->Branch->BranchType->is_direct_queue)
                  {{ route('adminBranch.report.directQueue.daily') }}
                @elseif (Auth::user()->Branch->BranchType->is_exhibition)
                  {{ route('adminBranch.exhibition.report.daily') }}
                @endif
              "
            >
              {{ __('Daily Report') }}
            </a>

            <a
              class="collapse-item kyoo-sublink"
              href="
                @if (Auth::user()->Branch->BranchType->is_appointment)
                  {{ route('adminBranch.report.appointment.monthly') }}
                @elseif (Auth::user()->Branch->BranchType->is_direct_queue)
                  {{ route('adminBranch.report.directQueue.monthly') }}
                @elseif (Auth::user()->Branch->BranchType->is_exhibition)
                  {{ route('adminBranch.exhibition.report.monthly') }}
                @endif
              "
            >
              {{ __('Monthly Report') }}
            </a>

            @if (Auth::user()->Branch->BranchType->is_premium)
              <a
                class="collapse-item kyoo-sublink"
                href="{{ route('adminBranch.report.customerSatisfaction') }}"
              >
                Laporan Kepuasan Pelanggan
              </a>
            @endif
          </div>
        </div>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->