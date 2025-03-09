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
                Auth::user()->Branch->BranchType->is_appointment
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