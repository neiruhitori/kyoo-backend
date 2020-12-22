<!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <a class="sidebar-brand d-flex mb-2" href="{{route('home')}}">
        <img src="{{asset('img/logo.svg')}}" alt="" style="height: 40px;">
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      @if (Auth::user()->Branch->BranchType->is_appointment)
          <li class="nav-item">
            <a class="nav-link" href="{{route('home')}}">
              <i class="fas fa-fw fa-users"></i>
              <span>Virtual Counter</span></a>
          </li>
      @endif

      @if (Auth::user()->Branch->BranchType->is_direct_queue)
          <li class="nav-item">
            <a class="nav-link" href="{{route('cs.directQueue.monitor')}}">
              <i class="fas fa-fw fa-users"></i>
              <span>Virtual Counter</span></a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="{{route('cs.directQueue.create')}}">
              <i class="fas fa-fw fa-edit"></i>
              <span>Add Direct Queue</span></a>
          </li>
      @endif

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
          <i class="fas fa-list-ul"></i>
          <span>Report</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Submenu:</h6>
            <a class="collapse-item" href="{{route('cs.report.daily')}}">Daily Report</a>
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