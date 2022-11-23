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

<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
  <a class="kyoo-logo-container" href="{{ route('adminCorporate.home') }}">
    <img
      src="{{ asset('img/logo.svg') }}"
      alt=""
      style="height: 40px;"
    >
  </a>

  <hr class="sidebar-divider my-0">

  <li class="nav-item {{ !request()->is('admin-corporate') ?: 'active' }}">
    <a class="nav-link" href="{{ route('adminCorporate.home') }}">
      <i class="fas fa-tachometer-alt"></i>
      <span>Dashboard</span></a>
  </li>

  <li class="nav-item {{ !request()->is('admin-corporate/monitoring') ?: 'active' }}">
    <a class="nav-link" href="{{ route('adminCorporate.monitoring') }}">
      <i class="fas fa-desktop"></i>
      <span>Monitoring Terpusat</span></a>
  </li>

  <hr class="sidebar-divider">

  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>
</ul>