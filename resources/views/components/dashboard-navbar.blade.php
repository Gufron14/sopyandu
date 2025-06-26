<nav class="navbar navbar-expand-lg main-navbar bg-primary">
    <ul class="navbar-nav mr-auto">
        <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
    </ul>
    <ul class="navbar-nav navbar-right">
        @if (Auth::user() && Auth::user()->role === 'admin')
            <li class="dropdown dropdown-list-toggle">
                <a href="#" data-toggle="dropdown" class="nav-link notification-toggle nav-link-lg">
                    <i class="far fa-bell"></i>
                </a>
                <div class="dropdown-menu dropdown-list dropdown-menu-right">
                    <div class="dropdown-header">Notifikasi
                        <div class="float-right">
                            <a href="#" id="notif-count">0</a>
                        </div>
                    </div>
                    <div class="dropdown-list-content dropdown-list-icons" id="notif-list" style="height: 100%">
                        <!-- AJAX content will be inserted here -->
                    </div>
                    <div class="dropdown-footer text-center">
                        <a href="{{ url('/parent-data?status=not-active') }}">Selengkapnya <i
                                class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </li>
        @endif
        <li class="dropdown">
            @php
                $fullname = '';
                $profile_url = '#';

                if (Auth::user()->officer_id !== null):
                    $fullname = Auth::user()->officers->fullname;
                    $profile_url = url('/officer-profile');
                elseif (Auth::user()->parent_id !== null):
                    $fullname = Auth::user()->familyParents->mother_fullname;
                    $profile_url = url('/parent-profile');
                endif;
            @endphp
            <a href="#" data-toggle="dropdown"
                class="nav-link dropdown-toggle dropdown-toggle-custom nav-link-lg nav-link-user">
                <div class="d-sm-none d-lg-inline-block">{{ $fullname }}</div>
                <img alt="image" src="{{ asset('img/avatar/avatar-1.png') }}" class="rounded-circle ml-1">
            </a>
            <div class="dropdown-menu dropdown-menu-right mr-2">
                <a href="{{ $profile_url }}" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Profil
                </a>

                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item has-icon text-danger" onclick="handleLogout(event)">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
                <form action="{{ route('logout') }}" method="POST" id="logout-form">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>
