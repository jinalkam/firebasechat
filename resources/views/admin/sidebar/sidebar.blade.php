<!-- Sidebar Navigation -->
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <span>
                        <img alt="Admin profile pic" class="img-circle" src="{{ asset('profile.png') }}" width="48"/>
                    </span>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:void(0);">
                        <span class="clear">
                            <span class="block m-t-xs">
                                <strong class="font-bold">{{ Auth::guard('admin')->user()->first_name . ' ' . Auth::guard('admin')->user()->last_name }}</strong>
                            </span>
                            <span class="text-muted text-xs block">Administrator <b class="caret"></b></span>
                        </span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li>
                            <a href="{{ route('admin.logout') }}"
                               onclick="event.preventDefault();
                                                document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;Logout
                            </a>
                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </div>

                <div class="logo-element">
                    HL
                </div>
            </li>
            <li class="{{ Request::is('admin/dashboard') ? 'active' : '' }}" >
                <a href="{{ url('admin/dashboard') }}"><i class="fa fa-th-large" aria-hidden="true"></i>&nbsp;<span class="nav-label">Dashboard</span></a>
            </li>

            <li class="{{ Request::is('admin/settings') ? 'active' : '' }}"  >
                <a href="{{ url('admin/settings') }}"><i class="fa fa-th-large" aria-hidden="true"></i>&nbsp;<span class="nav-label">Settings</span></a>
            </li>

            <li class="{{ Request::is('admin/users') ? 'active' : '' }}" >
                <a href="{{ url('admin/users') }}"><i class="fa fa-th-large" aria-hidden="true"></i>&nbsp;<span class="nav-label">Users</span></a>
            </li>
        </ul>
    </div>
</nav>