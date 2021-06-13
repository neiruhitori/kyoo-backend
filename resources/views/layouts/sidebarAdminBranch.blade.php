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
          <span>Dashboard</span></a>
      </li>

      @if (Auth::user()->Branch->BranchType->is_premium && Auth::user()->Branch->BranchType->is_direct_queue)
          <li class="nav-item">
            <a class="nav-link" href="{{route('adminBranch.directQueue.monitor')}}" target="_blank">
              <i class="fas fa-fw fa-tv"></i>
              <span>Direct Queue Monitor</span></a>
          </li>
      @endif

      <li class="nav-item">
        <a class="nav-link" href="{{route('adminBranch.qr')}}" target="_blank">
          <i class="fas fa-fw fa-qrcode"></i>
          <span>Show QR Code</span></a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Heading -->
      <div class="sidebar-heading">
        Menu
      </div>

      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link" href="{{route('adminBranch.branch.edit')}}">
          <i class="fas fa-fw fa-building"></i>
          <span>Edit Branch Profile</span>
        </a>
      </li>

      @if (Auth::user()->Branch->BranchType->is_direct_queue)
          <li class="nav-item">
            <a class="nav-link" href="{{route('adminBranch.branchConfiguration.edit')}}">
              <i class="fas fa-fw fa-building"></i>
              <span>Branch Configuration</span>
            </a>
          </li>
      @endif

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          <i class="fas fa-fw fa-calendar"></i>
          <span>Schedule</span>
        </a>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Submenu:</h6>
            <a class="collapse-item" href="{{route('adminBranch.schedule.index')}}">List Schedule</a>
            <a class="collapse-item" href="{{route('adminBranch.schedule.create')}}">Insert Schedule</a>
          </div>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{route('adminBranch.department.index')}}">
          <i class="fas fa-fw fa-building"></i>
          <span>Department</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
          <i class="fas fa-fw fa-bars"></i>
          <span>Service</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Submenu:</h6>
            <a class="collapse-item" href="{{route('adminBranch.service.index')}}">List Service</a>
            <a class="collapse-item" href="{{route('adminBranch.service.create')}}">Insert Service</a>
          </div>
        </div>
      </li>

      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link" href="{{route('adminBranch.workstation.index')}}">
          <i class="fas fa-fw fa-user"></i>
          <span>Workstation</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{route('adminBranch.user.index')}}">
          <i class="fas fa-fw fa-user"></i>
          <span>Virtual Counter</span>
        </a>
      </li>

      @if (Auth::user()->Branch->BranchType->is_appointment)
          <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
              <i class="fas fa-list-ul"></i>
              <span>Report Appointment</span>
            </a>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionSidebar">
              <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Submenu:</h6>
                <a class="collapse-item" href="{{route('adminBranch.report.daily')}}">Daily Report</a>
              </div>
            </div>
          </li>
      @endif

      @if (Auth::user()->Branch->BranchType->is_direct_queue)
          <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
              <i class="fas fa-list-ul"></i>
              <span>Report Direct Queue</span>
            </a>
            <div id="collapseFour" class="collapse" aria-labelledby="headingThree" data-parent="#accordionSidebar">
              <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Submenu:</h6>
                <a class="collapse-item" href="{{route('adminBranch.report.directQueue.daily')}}">Daily Report</a>
              </div>
            </div>
          </li>
      @endif

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->