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
    <a class="kyoo-logo-container" href="{{ route('dashboard') }}">
        <img
            src="{{ asset('img/logo.svg') }}"
            alt=""
            style="height: 40px;"
        >
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ !request()->is('admin-branch/dashboard') ?: 'active' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>{{ __('Dashboard') }}</span></a>
    </li>

    <li class="nav-item {{ !request()->is('admin-branch/monitoring/*') ?: 'active' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#monitoring" aria-expanded="true"
           aria-controls="monitoring">
            <i class="fas fa-tv"></i>
            <span>Monitoring</span>
        </a>

        <div class="collapse {{ !request()->is('admin-branch/monitoring/*') ?: 'show' }}" id="monitoring"
             data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/monitoring/department') ?: 'active' }}"
                    href="{{ route('admin-branch.monitoring.department') }}"
                >
                    Departemen
                </a>

                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/monitoring/service') ?: 'active' }}"
                    href="{{ route('admin-branch.monitoring.service') }}"
                >
                    Layanan
                </a>
            </div>
        </div>
    </li>

    @if (
            Auth::user()->Branch->BranchType->is_premium &&
            Auth::user()->Branch->BranchType->is_direct_queue
        )
        <li class="nav-item {{ !request()->is('admin-branch/appointment-onsites*') ?: 'active' }}">
            <a class="nav-link" href="{{ route('admin-branch.appointment-onsites') }}">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>{{ __('list.module', ['module' => __('Appointment')]) }}</span></a>
        </li>
    @endif

    <li class="nav-item {{ !request()->is('admin-branch/branch-qr-code') ?: 'active' }}">
        <a class="nav-link" href="{{ route('admin-branch.branch-qr-code') }}">
            <i class="fas fa-fw fa-qrcode"></i>
            <span>{{ __('Branch QR Code') }}</span></a>
    </li>

    <!-- @if (
      Auth::user()->Branch->BranchType->is_premium &&
      Auth::user()->Branch->hasAccess('Web Signage TV')
    )
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin-branch.queue-monitor') }}" target="_blank">
                <i class="fas fa-fw fa-tv"></i>
                <span>Web Monitor Antrian</span>
            </a>
        </li>
    @endif -->

    <li class="nav-item {{ !request()->is('admin-branch/product-guide/*') ?: 'active' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#instruction-of-use"
           aria-expanded="true" aria-controls="instruction-of-use">
            <i class="fas fa-fw fa-info-circle"></i>
            <span>{{ __('Instruction for Use') }}</span>
        </a>

        <div class="collapse {{ !request()->is('admin-branch/product-guide/*') ?: 'show' }}" id="instruction-of-use"
             data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/product-guide/queue-configuration') ?: 'active' }}"
                    href="{{ route('admin-branch.product-guide.queue-configuration') }}"
                >
                    {{ __('How to Configure Qeueu') }}
                </a>

                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/product-guide/customer') ?: 'active' }}"
                    href="{{ route('admin-branch.product-guide.customer') }}"
                >
                    {{ __('Customer Guide') }}
                </a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <li class="nav-item {{ !request()->is('admin-branch/branch-information/*') ?: 'active' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#branch-information"
           aria-expanded="true" aria-controls="branch-information">
            <i class="fas fa-fw fa-building"></i>
            <span>{{ __('Branch Information') }}</span>
        </a>

        <div class="collapse {{ !request()->is('admin-branch/branch-information/*') ?: 'show' }}"
             id="branch-information" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-information/profile') ?: 'active' }}"
                    href="{{ route('admin-branch.branch-information.profile') }}"
                >
                    {{ __('Profile') }}
                </a>

                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-information/location') ?: 'active' }}"
                    href="{{ route('admin-branch.branch-information.location') }}"
                >
                    {{ __('Location') }}
                </a>
            </div>
        </div>
    </li>

    <li class="nav-item {{ !request()->is('admin-branch/branch-configuration/*') ?: 'active' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#branch-configuration"
           aria-expanded="true" aria-controls="branch-configuration">
            <i class="fas fa-fw fa-cog"></i>
            <span>{{ __('Branch Configuration') }}</span>
        </a>

        <div class="collapse {{ !request()->is('admin-branch/branch-configuration/*') ?: 'show' }} {{  !request()->is('admin-branch/cs/*') ?: 'show'}}"
             id="branch-configuration" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-configuration/department*') && !request()->is('admin-branch/branch-configuration/service*') && !request()->is('admin-branch/branch-configuration/slot*') ?: 'active' }}"
                    href="{{ route('admin-branch.branch-configuration.department.index') }}"
                >
                    {{ __('Department') }}
                </a>

                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-configuration/schedule*') ?: 'active' }}"
                    href="{{ route('admin-branch.branch-configuration.schedule.index') }}"
                >
                    {{ __('Schedule') }}
                </a>

                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-configuration/workstation*') ?: 'active' }}"
                    href="{{ route('admin-branch.branch-configuration.workstation.index') }}"
                >
                    {{ __('Workstation') }}
                </a>

                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-configuration/user*') ?: 'active' }}"
                    href="{{ route('admin-branch.branch-configuration.user.index') }}"
                >
                    {{ __('Virtual Counter') }}
                </a>

                @if (
                        Auth::user()->Branch->BranchType->is_premium &&
                        (
                            Auth::user()->Branch->BranchType->is_appointment ||
                            Auth::user()->Branch->BranchType->is_direct_queue
                        )
                    )
                    <a class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-configuration/menu-portal*') ?: 'active' }}"
                    href="{{ route('admin-branch.branch-configuration.menu-portal') }}"
                    >
                        {{ __('Portal Menu') }}
                    </a>
                @endif

                @if (
                  Auth::user()->Branch->BranchType->is_premium &&
                  ( Auth::user()->Branch->hasAccess('Web Signage TV') || Auth::user()->Branch->hasAccess('Webkiosk') )
                )
                    <a
                        class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-configuration/device*') ?: 'active' }}"
                        href="{{ route('admin-branch.branch-configuration.device-account.index') }}"
                    >
                        {{ __('Device Account') }}
                    </a>
                @endif

                @if (Auth::user()->Branch->BranchType->is_premium)
                    <a
                        class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-configuration/feature') ?: 'active' }}"
                        href="{{ route('admin-branch.branch-configuration.feature') }}"
                    >
                        {{ __('Features') }}
                    </a>
                @endif

                @if (
                  Auth::user()->Branch->BranchType->is_premium &&
                  Auth::user()->Branch->hasAccess('Web Signage TV')
                )
                    <a
                        class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-configuration/queue-monitor') ?: 'active' }}"
                        href="{{ route('admin-branch.branch-configuration.queue-monitor') }}"
                    >
                        Monitor Antrian (TV)
                    </a>
                @endif

                @if (
                  Auth::user()->Branch->BranchType->is_premium &&
                  Auth::user()->Branch->hasAccess('Webkiosk')
                )
                    <a
                        class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-configuration/webkiosk') ?: 'active' }}"
                        href="{{ route('admin-branch.branch-configuration.webkiosk') }}"
                    >
                        Kiosk Web
                    </a>
                @endif

                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-configuration/terms-conditions') ?: 'active' }}"
                    href="{{ route('admin-branch.branch-configuration.terms-conditions.index') }}"
                >
                    Syarat & Ketentuan
                </a>

                @if (
                    Auth::user()->Branch->BranchType->is_premium &&
                    Auth::user()->Branch->BranchType->is_direct_queue
                )
                    <a
                        class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/cs/access*') ?: 'active' }}"
                        href="{{ route('admin-branch.cs.access.index') }}"
                    >
                        <span>{{ __('Access') }}</span>
                    </a>
                @endif

                @if (
                  Auth::user()->Branch->BranchType->is_premium &&
                  Auth::user()->Branch->hasAccess('Promosi')
                )
                    <a
                        class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/branch-configuration/promotions*') ?: 'active' }}"
                        href="{{ route('admin-branch.branch-configuration.promotions.index') }}"
                    >
                        Promosi
                    </a>
                @endif
            </div>
        </div>
    </li>

    <hr class="sidebar-divider my-0">

    @if (
      Auth::user()->Branch->BranchType->is_premium &&
      Auth::user()->Branch->hasAccess('Voice Recording')
    )
        <li class="nav-item {{ !request()->is('admin-branch/service-quality/*') ?: 'active' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#service-quality"
               aria-expanded="true" aria-controls="service-quality">
                <i class="fas fa-star"></i>
                <span>Kualitas Pelayanan</span>
            </a>

            <div
                class="collapse {{ !request()->is('admin-branch/service-quality/*') ?: 'show' }}"
                id="service-quality"
                data-parent="#accordionSidebar"
            >
                <div class="bg-white py-2 collapse-inner rounded">
                    @if (Auth::user()->Branch->hasAccess('Voice Recording'))
                        <a
                            class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/service-quality/audio-recording') && !request()->is('admin-branch/service-quality/audio-recording') && !request()->is('admin-branch/service-quality/audio-recording') ?: 'active' }}"
                            href="{{ route('admin-branch.service-quality.audio-recording.index') }}"
                        >
                            Putar Rekaman
                        </a>
                    @endif
                </div>
            </div>
        </li>
    @endif

    <li class="nav-item {{ !request()->is('admin-branch/report/*') ?: 'active' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#report" aria-expanded="true"
           aria-controls="report">
            <i class="fas fa-fw fa-file-alt"></i>
            <span>{{ __('Report') }}</span>
        </a>

        <div class="collapse {{ !request()->is('admin-branch/report/*') ?: 'show' }}" id="report"
             data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/report/daily/*') ?: 'active' }}"
                    href="
                    @if (Auth::user()->Branch->BranchType->is_appointment)
                      {{ route('admin-branch.report.daily.appointment') }}
                    @elseif (Auth::user()->Branch->BranchType->is_direct_queue)
                      {{ route('admin-branch.report.daily.onsite') }}
                    @elseif (Auth::user()->Branch->BranchType->is_exhibition)
                      {{ route('admin-branch.report.daily.exhibition') }}
                    @endif
                  "
                >
                    {{ __('Daily Report') }}
                </a>

                <a
                    class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/report/monthly/*') ?: 'active' }}"
                    href="
                    @if (Auth::user()->Branch->BranchType->is_appointment)
                      {{ route('admin-branch.report.monthly.appointment') }}
                    @elseif (Auth::user()->Branch->BranchType->is_direct_queue)
                      {{ route('admin-branch.report.monthly.onsite') }}
                    @elseif (Auth::user()->Branch->BranchType->is_exhibition)
                      {{ route('admin-branch.report.monthly.exhibition') }}
                    @endif
                  "
                >
                    {{ __('Monthly Report') }}
                </a>

                @if(Auth::user()->Branch->BranchType->is_direct_queue)
                    <a
                        class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/report/appointment-onsites') ?: 'active' }}"
                        href="
                        {{ route('admin-branch.report.appointment-onsites') }}
                    "
                    >
                        {{ __('Report Appointment') }}
                    </a>
                @endif

                @if (Auth::user()->Branch->BranchType->is_premium)
                    <a
                        class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/report/customer-satisfaction*') ?: 'active' }}"
                        href="{{ route('admin-branch.report.customer-satisfaction') }}"
                    >
                        Laporan Kepuasan Pelanggan
                    </a>

                    <a
                        class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/report/department*') ?: 'active' }}"
                        href="{{ route('admin-branch.report.department') }}"
                    >
                        Laporan Departemen
                    </a>

                    <a
                        class="collapse-item kyoo-sublink {{ !request()->routeIs('admin-branch.report.service.*') ?: 'active' }}"
                        href="{{ route('admin-branch.report.service.index') }}"
                    >
                        Laporan Layanan
                    </a>

                    <a
                        class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/report/service-distribution*') ?: 'active' }}"
                        href="{{ route('admin-branch.report.service-distribution') }}"
                    >
                        Laporan Distribusi Tunggu Layanan
                    </a>

                    <a
                        class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/report/workstation*') ?: 'active' }}"
                        href="{{ route('admin-branch.report.workstation') }}"
                    >
                        Laporan Meja
                    </a>

                    <a
                        class="collapse-item kyoo-sublink {{ !request()->is('admin-branch/report/vct*') ?: 'active' }}"
                        href="{{ route('admin-branch.report.vct') }}"
                    >
                        Laporan User
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
