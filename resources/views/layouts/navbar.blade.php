<!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

        <!-- Sidebar Toggle (Topbar) -->
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
            <i class="fa fa-bars"></i>
        </button>

        <!-- Topbar Navbar -->
        <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Alerts -->
        @switch(Auth::user()->role)
                @case('admin_kyoo')
                  
                    @break
                @case('admin_branch')
                        @if(!Auth::user()->Branch->is_premium && !Auth::user()->Branch->BranchType->is_exhibition)
                        <li class="nav-item no-arrow mx-1">
                            <div class="mt-3 input-group rounded">
                                <div class="btn-group btn-group-toggle textpromotion" >
                                    <label class="btn btn-primary" style="pointer-events: none;">
                                      <small class="text-white">You're using Trial License, upgrade now to access other features!</small>
                                    </label>
                                    <label class="btn btn-warning">
                                        <a class="text-decoration-none text-white" href="{{ route('admin-branch.subscription') }}"><small>Upgrade NOW!</small></a>
                                    </label>
                                  </div>
                                {{-- <p class="form-control text-white bg-primary rounded-start">You're using Trial License, upgrade now to access other features!</p>--}}
                            
                                <a class="btn btn-warning rounded-end textupgrade" href="{{ route('admin-branch.subscription') }}">Upgrade NOW!</a> 
                            
                            </div>
                        </li>
                        @endif
                    @break
                @default
                    
            @endswitch

            @if (
                Auth::user()->role != 'admin_kyoo' &&
                !Auth::user()->Branch->BranchType->is_exhibition
            )      
            <!-- Nav Item - User Information -->
            <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-toggle="dropdown" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small"> <span class="fi fi-{{ app()->getLocale() == 'en' ? 'gb' : 'id'}} fib border"></span> {{ strtoupper(app()->getLocale()) }}</span>
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in">
                    <a class="dropdown-item" href="{{ route('change.locale', 'en') }}">
                        <span class="fi fi-gb fib border"></span> English
                    </a>
                    <a class="dropdown-item" href="{{ route('change.locale', 'id') }}">
                        <span class="fi fi-id fib border"></span> Indonesia
                    </a>
                </div>
            </li>
            @endif
        
        <div class="topbar-divider d-none d-sm-block"></div>
    @if(in_array(Auth::user()->role, ['spv', 'cs']))
      <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="menuDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell fa-sm fa-fw mr-2"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="menuDropdown">
        @if (Auth::user()->Branch->BranchType->is_direct_queue)
                <a class="dropdown-item py-2 d-flex align-items-center" href="{{route('cs.directQueue.monitor')}}">
                    <i class="fas fa-user-friends text-primary mr-3"></i>
                    {{ __('Direct Queue') }}
                </a>
        @endif

        @if (Auth::user()->Branch->BranchType->is_appointment)
                <a class="dropdown-item py-2" href="{{route('dashboard')}}">
                    <i class="fas fa-user text-primary mr-3"></i>
                    {{ __('Active Appointment') }}
                </a>
                <a class="dropdown-item py-2" href="{{ route('cs.appointment.future.index') }}">
                    <i class="fas fa-user-friends text-primary mr-3"></i>
                    {{ __('Appointment List') }}
                </a>
        @endif
    @if (
            Auth::user()->Branch->BranchType->is_premium &&
            Auth::user()->Branch->BranchType->is_direct_queue
        )
         @foreach (Auth::user()->Branch->getCsActiveMenus(Auth::user()->WorkstationVct->workstation_id) as $key => $menu)
            @if(is_array($menu))
            <a href="#" class="dropdown-item py-2 toggle-submenu" data-target="#monitoring">
                <i class="fas fa-chart-bar text-primary mr-3"></i>
                {{ __($key) }}
                <i class="fas fa-caret-down float-right"></i>
            </a>
            <div id="monitoring" class="submenu-content" style="display: none;">
                @foreach ($menu as $submenuKey => $submenu)
                    <a class="dropdown-item py-2" href="{{ url($submenu->route) }}">
                        {{ __($submenu->name_label) }}
                    </a>
                @endforeach
            </div>
             @endif
        @endforeach
        @endif

        @if (
                Auth::user()->Branch->BranchType->is_premium &&
                Auth::user()->Branch->BranchType->is_direct_queue
            )
            @foreach (Auth::user()->Branch->getCsActiveMenus(Auth::user()->WorkstationVct->workstation_id) as $key => $menu)
            @if (is_object($menu) && !isset($menu->route) && $menu->code == Auth::user()->role)
                <a href="#" class="dropdown-item py-2 toggle-submenu" data-target="#reportDropdown">
                    <i class="fas fa-file text-primary" style="margin-right: 1.3rem"></i>
                    {{ __('Report') }} <i class="fas fa-caret-down float-right"></i>
                </a>
            <div id="reportDropdown" class="submenu-content" style="display: none;">
                <a class="dropdown-item py-2" href="{{ route('cs.report.directQueue.daily') }}">
                    {{ __('Daily Report') }}
                </a>
                <a class="dropdown-item py-2" href="{{ route('cs.report.directQueue.monthly') }}">
                   {{ __('Monthly Report') }}
                </a>
                <a class="dropdown-item py-2" href="{{ route('cs.report.directQueue.appointmentOnsite') }}">
                   {{ __('Appointment Report') }}
                </a>
            </div>
            @endif
            @endforeach
    @endif
        @if (
            Auth::user()->Branch->BranchType->is_premium &&
            !Auth::user()->Branch->BranchType->is_direct_queue
        )
                <a class="dropdown-item py-2" href="
                    @if (Auth::user()->Branch->BranchType->is_appointment)
                    {{route('cs.report.daily')}}
                    @elseif (Auth::user()->Branch->BranchType->is_exhibition)
                    {{route('cs.exhibition.report.daily')}}
                    @endif
                ">
                    <i class="fas fa-file text-primary" style="margin-right: 1.3rem"></i>
                    <span>{{ __('Daily Report') }}</span>
                </a>
        @endif
        @if (
                Auth::user()->Branch->BranchType->is_premium &&
                Auth::user()->Branch->BranchType->is_direct_queue
            )
            @foreach (Auth::user()->Branch->getCsActiveMenus(Auth::user()->WorkstationVct->workstation_id) as $key => $menu)
            @if (is_object($menu) && isset($menu->route))
                <a class="dropdown-item py-2" href="{{ url($menu->route) }}">
                    <i class="fas fa-cog text-primary mr-3"></i>
                <span>{{ __($menu->name) }}</span></a>
            @endif
            @endforeach
        @endif
        @if (
            Auth::user()->Branch->BranchType->is_premium &&
            Auth::user()->Branch->hasAccess('Voice Recording')
            )
            <a class="dropdown-item py-2" href="{{ route('cs.voiceRecorder.index') }}">
                <i class="fas fa-microphone text-primary" style="margin-right: 1.3rem"></i>
                {{ __('Record Audio') }}
            </a>
        @endif
            {{-- <a class="dropdown-item" href="#">
                <i class="fas fa-list text-primary mr-2"></i>
                Konfigurasi Meja
            </a>
            <a class="dropdown-item" href="#">
                <i class="fas fa-headphones text-primary mr-2"></i>
                Konfigurasi Layanan
            </a> --}}
        


             <div class="dropdown-divider"></div>
             <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2"></i>
                    Logout
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </a>
  </div>
</li>
    @endif
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-lg-inline text-gray-600 small">{{Auth::user()->name}}</span>
            <img class="img-profile rounded-circle" src="{{asset('img/logo-icon.svg')}}">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
            @switch(Auth::user()->role)
                @case('admin_kyoo')
                    <a class="dropdown-item" href="{{route('admin.profile.edit')}}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        {{ __('Profile') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    @break
                @case('admin_branch')
                    <a class="dropdown-item" href="{{route('admin-branch.profile')}}">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        {{ __('Profile') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    @break
                @default
                    
            @endswitch
                <a class="dropdown-item" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </a>
            </div>
        </li>

        </ul>

    </nav>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>    
    <script>
        $(document).ready(function () {
            // Toggle specific submenu
            $('.toggle-submenu').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const target = $(this).data('target');

            $('.submenu-content').not($(target)).slideUp(150);

            $(target).slideToggle(150);
            });

            $(document).on('click', function () {
            $('.submenu-content').slideUp(150);
            });
        });
    </script>



    {{-- @push('css') --}}
        <style>
            .textupgrade{
                display: none
            }
            @media screen and (max-width:1054px){
                .textpromotion{
                    display: none;
                }
                .textupgrade{
                display: block
            }
            }
        </style>
    {{-- @endpush --}}
    <!-- End of Topbar -->