<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="#">SIMTO</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#">HSI</a>
        </div>

        <ul class="sidebar-menu">

            {{-- ================= ADMIN & SUPERVISOR ================= --}}
            @if(auth()->user()->role !== 'employee')

            <li class="menu-header">Dashboard</li>

            <li class="nav-item dropdown {{ $type_menu === 'dashboard' ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown">
                    <i class="fas fa-fire"></i><span>Dashboard</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('dashboard-general-dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('dashboard-general-dashboard') }}">
                            General Dashboard
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-item dropdown {{ $type_menu === 'master' ? 'active' : '' }}">
                <a href="#" class="nav-link has-dropdown">
                    <i class="far fa-file-alt"></i> <span>Master</span>
                </a>
                <ul class="dropdown-menu">
                    <li class="{{ Request::is('master-employee') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('master-employee') }}">
                            Employee
                        </a>
                    </li>
                    <li class="{{ Request::is('master-client') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('master-client') }}">
                            Client
                        </a>
                    </li>
                </ul>
            </li>

            <li class="{{ Request::is('career') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('career') }}">
                    <i class="fas fa-briefcase"></i> <span>Job</span>
                </a>
            </li>

            @endif

            {{-- ================= SEMUA ROLE ================= --}}
            <li class="menu-header">Attendance</li>

            <li class="{{ Request::is('attendance') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('attendance') }}">
                    <i class="far fa-clock"></i> <span>Attendance</span>
                </a>
            </li>

            <li class="{{ Request::is('my-attendance') ? 'active' : '' }}">
                <a class="nav-link" href="{{ url('my-attendance') }}">
                    <i class="fas fa-database"></i> <span>My Attendance</span>
                </a>
            </li>

        </ul>
    </aside>
</div>