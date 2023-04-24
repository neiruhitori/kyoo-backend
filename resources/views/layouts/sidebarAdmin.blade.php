<!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <a class="sidebar-brand d-flex mb-2" href="{{route('dashboard')}}">
        <img src="{{asset('img/logo.svg')}}" alt="" style="height: 40px;">
      </a>

      <!-- Divider -->
      <hr class="sidebar-divider my-0">

      <!-- Nav Item - Dashboard -->
      <li class="nav-item">
        <a class="nav-link" href="{{route('dashboard')}}">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>{{ __('Dashboard') }}</span></a>
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
          <span>{{ __('Industry Category') }}</span>
        </a>
        <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Submenu:</h6>
            <a class="collapse-item" href="{{route('admin.industryCategory.index')}}">
              {{ __('list.module', ['module' => __('Category')]) }}
            </a>
            <a class="collapse-item" href="{{route('admin.industryCategory.create')}}">
              {{ __('create.module', ['module' => __('Category')]) }}
            </a>
          </div>
        </div>
      </li>
      
      <li class="nav-item">
        <a class="nav-link" href="{{route('admin.scheduleTemplate.index')}}">
          <i class="fas fa-fw fa-calendar"></i>
          <span>{{ __('Schedule Template') }}</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{route('admin.branchType.index')}}">
          <i class="fas fa-fw fa-building"></i>
          <span>{{ __('Branch License') }}</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
          <i class="fas fa-fw fa-building"></i>
          <span>{{ __('Branch Management') }}</span>
        </a>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <a class="collapse-item" href="{{route('admin.branch.index')}}">{{ __('List Branch') }}</a>

            <a class="collapse-item" href="{{route('admin.registrationBranch.index')}}">{{ __('Verify Branch') }}</a>

            <a class="collapse-item" href="{{route('admin.branch.create')}}">{{ __('create.module', ['module' => __('Branch')]) }}</a>
          </div>
        </div>
      </li>

      <li class="nav-item">
        <a
          class="nav-link collapsed"
          href="#"
          data-toggle="collapse"
          data-target="#collapseThree"
          aria-expanded="true"
          aria-controls="collapseThree"
        >
          <i class="fas fa-fw fa-building"></i>
          <span>Managemen Corporate</span>
        </a>

        <div
          id="collapseThree"
          class="collapse"
          aria-labelledby="headingThree"
          data-parent="#accordionSidebar"
        >
          <div class="bg-white py-2 collapse-inner rounded">
            <a
              class="collapse-item"
              href="{{ route('admin.corporate.index') }}"
            >
              Daftar Corporate
            </a>

            <a
              class="collapse-item"
              href="{{ route('admin.corporate.options') }}"
            >
              Tambah Corporate
            </a>
          </div>
        </div>
      </li>

      <li class="nav-item">
        <a class="nav-link" href="{{route('admin.waSession.index')}}">
          <i class="fab fa-whatsapp"></i>
          <span>WhatsApp Session</span>
        </a>
      </li>

      <!-- Divider -->
      <hr class="sidebar-divider">

      <!-- Sidebar Toggler (Sidebar) -->
      <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
      </div>

    </ul>
    <!-- End of Sidebar -->