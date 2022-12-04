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

  <li class="nav-item {{ !request()->is('admin-corporate/report/*') ?: 'active' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#report" aria-expanded="true" aria-controls="report">
      <i class="fas fa-fw fa-file-alt"></i>
      <span>Reporting Terpusat</span>
    </a>

    <div class="collapse {{ !request()->is('admin-corporate/report/*') ?: 'show' }}" id="report" data-parent="#accordionSidebar">
      <div class="bg-white py-2 collapse-inner rounded">
        <a
          class="collapse-item kyoo-sublink {{ !request()->is('admin-corporate/report/customer-satisfaction') ?: 'active' }}"
          href="{{ route('adminCorporate.report.customerSatisfaction.index') }}"
        >
          Laporan Kepuasan Pelanggan
        </a>

        <a
          class="collapse-item kyoo-sublink {{ !request()->is('admin-corporate/report/branch') ?: 'active' }}"
          href="{{ route('adminCorporate.report.branch.index') }}"
        >
          Laporan Cabang
        </a>

        <a
          class="collapse-item kyoo-sublink {{ !request()->is('admin-corporate/report/service') ?: 'active' }}"
          href="{{ route('adminCorporate.report.service.index') }}"
        >
          Laporan Layanan
        </a>

        <a
          class="collapse-item kyoo-sublink {{ !request()->is('admin-corporate/report/workstation') ?: 'active' }}"
          href="{{ route('adminCorporate.report.workstation.index') }}"
        >
          Laporan Meja
        </a>
      </div>
    </div>
  </li>

  <hr class="sidebar-divider">

  <div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
  </div>
</ul>