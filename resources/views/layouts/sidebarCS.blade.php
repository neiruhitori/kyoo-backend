<!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <a class="sidebar-brand d-flex mb-2" href="{{route('home')}}">
        <img src="{{asset('img/logo.svg')}}" alt="" style="height: 40px;">
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">
      
      <li class="nav-item">
        <a class="nav-link" href="{{route('cs.qr')}}" target="_blank">
          <i class="fas fa-fw fa-qrcode"></i>
          <span>Show QR Code</span></a>
      </li>

      <!-- Nav Item - Dashboard -->
      @if (Auth::user()->Branch->BranchType->is_direct_queue)
        <li class="nav-item">
          <a class="nav-link" href="{{route('cs.directQueue.create')}}">
            <i class="fas fa-fw fa-edit"></i>
            <span>Add Direct Queue</span></a>
        </li>
      @endif
      
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          <i class="fas fa-list-ul"></i>
          <span>Virtual Counter</span>
        </a>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Submenu:</h6>
            @if (Auth::user()->Branch->BranchType->is_appointment)
                <a class="collapse-item" href="{{route('home')}}">Appointment</a>
            @endif
            @if (Auth::user()->Branch->BranchType->is_direct_queue)
                <a class="collapse-item" href="{{route('cs.directQueue.monitor')}}">Direct Queue</a>
            @endif
          </div>
        </div>
      </li>

      @if (Auth::user()->Branch->BranchType->is_appointment)
          <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
              <i class="fas fa-list-ul"></i>
              <span>Report Appointment</span>
            </a>
            <div id="collapseTwo" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
              <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Submenu:</h6>
                <a class="collapse-item" href="{{route('cs.report.daily')}}">Daily Report</a>
              </div>
            </div>
          </li>
      @endif

      @if (Auth::user()->Branch->BranchType->is_direct_queue)
          <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
              <i class="fas fa-list-ul"></i>
              <span>Report Direct Queue</span>
            </a>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionSidebar">
              <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Submenu:</h6>
                <a class="collapse-item" href="{{route('cs.report.directQueue.daily')}}">Daily Report</a>
              </div>
            </div>
          </li>
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