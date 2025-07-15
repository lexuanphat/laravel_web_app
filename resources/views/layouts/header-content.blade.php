<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-lg-2 gap-1">

            <!-- Topbar Brand Logo -->
            <div class="logo-topbar">
                <!-- Logo light -->
                <a href="{{route('admin.dashboard')}}" class="logo logo-light">
                    <span class="logo-lg">
                        <img src="{{asset('assets/images/logo-new.png')}}" alt="logo">
                    </span>
                    <span class="logo-sm">
                        <img src="{{asset('assets/images/logo-sm.png')}}" alt="small logo">
                    </span>
                </a>

                <!-- Logo Dark -->
                <a href="{{route('admin.dashboard')}}" class="logo logo-dark">
                    <span class="logo-lg">
                        <img src="{{asset('assets/images/logo-new.png')}}" alt="dark logo">
                    </span>
                    <span class="logo-sm">
                        <img src="{{asset('assets/images/logo-new.png')}}" alt="small logo">
                    </span>
                </a>
            </div>

            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu">
                <i class="mdi mdi-menu"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>

            <!-- Topbar Search Form -->
            <div class="app-search dropdown d-none d-lg-block">
            </div>
        </div>

        <ul class="topbar-menu d-flex align-items-center gap-3">

            @if(auth()->user()->email === 'admin@gmail.com')
            <li class="d-none d-sm-inline-block">
                <a class="nav-link" data-bs-toggle="offcanvas" href="#theme-settings-offcanvas">
                    <i class="ri-settings-3-line font-22"></i>
                </a>
            </li>
            @endif

            {{-- <li class="d-none d-md-inline-block">
                <a class="nav-link" href="#" data-bs-toggle="fullscreen">
                    <i class="ri-fullscreen-line font-22"></i>
                </a>
            </li> --}}

            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user px-2" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="account-user-avatar">
                        <img src="{{asset('storage/' . auth()->user()->profile_default)}}" alt="user-image" width="32" class="rounded-circle">
                    </span>
                    <span class="d-lg-flex flex-column gap-1 d-none">
                        <h5 class="my-0">{{auth()->user()->full_name}}</h5>
                        <h6 class="my-0 fw-normal">{{auth()->user()->role_name}}</h6>
                        <h6 class="my-0 fw-normal">{{auth()->user()->store?->name}}</h6>
                    </span>
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">
                    <!-- item-->
                    <a href="{{ route('admin.logout') }}" class="dropdown-item" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                        <i class="mdi mdi-logout me-1"></i>
                        <span>Thoát</span>
                    </a>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>
    </div>
</div>