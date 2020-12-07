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

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Heading -->
      <div class="sidebar-heading">
        Menu
      </div>

      <!-- Nav Item - Pages Collapse Menu -->
      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          <i class="fas fa-fw fa-list-ul"></i>
          <span>Industry Category</span>
        </a>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Submenu:</h6>
            <a class="collapse-item" href="{{route('admin.industryCategory.index')}}">List Category</a>
            <a class="collapse-item" href="{{route('admin.industryCategory.create')}}">Insert Category</a>
          </div>
        </div>
      </li>
      
      <li class="nav-item">
        <a class="nav-link" href="{{route('admin.scheduleTemplate.index')}}">
          <i class="fas fa-fw fa-calendar"></i>
          <span>Schedule Template</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{route('admin.branchType.index')}}">
          <i class="fas fa-fw fa-building"></i>
          <span>Branch Type</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
          <i class="fas fa-fw fa-building"></i>
          <span>Branch Management</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Submenu:</h6>
            <a class="collapse-item" href="{{route('admin.branch.index')}}">List Branch</a>
            <a class="collapse-item" href="{{route('admin.registrationBranch.index')}}">Verify Branch</a>
            <a class="collapse-item" href="{{route('admin.branch.create')}}">Insert Branch</a>
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