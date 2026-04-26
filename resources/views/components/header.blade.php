<div class="navbar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
        <ul class="navbar-nav mr-3">
            <li>
                <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg">
                    <i class="fas fa-bars"></i>
                </a>
            </li>
        </ul>
    </form>

    @php
        $employee = auth()->user()->employee ?? null;
        $contractEnd = $employee?->contract_end;

        $statusClass = 'badge-secondary';
        $statusText = 'Contract';
        $daysLeft = null;

        if ($contractEnd) {
            $daysLeft = \Carbon\Carbon::now()->diffInDays($contractEnd, false);

            if ($daysLeft <= 30 && $daysLeft >= 0) {
                $statusClass = 'badge-warning';
                $statusText = 'Contract Ends Soon';
            } elseif ($daysLeft < 0) {
                $statusClass = 'badge-danger';
                $statusText = 'The contract has expired';
            }
        }
    @endphp

    <span class="badge {{ $statusClass }}">
        {{ $statusText }}
        @if(!is_null($daysLeft) && $daysLeft >= 0)
            ({{ $daysLeft }} days)
        @endif
    </span>
    <ul class="navbar-nav navbar-right">
        <li class="dropdown">
            <a href="#"
                data-toggle="dropdown"
                class="nav-link dropdown-toggle nav-link-lg nav-link-user">

                <img alt="image"
                    src="{{ asset('img/avatar/avatar-1.png') }}"
                    class="rounded-circle mr-1">

                <div class="d-sm-none d-lg-inline-block">
                    Hi, {{ $employee->full_name ?? 'User' }}
                </div>
            </a>

            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ url('profile') }}" class="dropdown-item has-icon">
                    <i class="far fa-user"></i> Profile
                </a>

                <div class="dropdown-divider"></div>

                <a href="#"
                    class="dropdown-item has-icon text-danger"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>

                <form id="logout-form"
                    action="{{ route('logout') }}"
                    method="POST"
                    style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
    </ul>
</nav>