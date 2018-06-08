<header class="header">

    <div class="main-menu desktop">
        <div class="top-menu">
            <a href="{{ route('admin.home') }}" class="superadmin__logo">
                <img src="/images/6d-admin.png" alt="home"/>
            </a>

            <ul class="nav justify-content-end">
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/logout') }}" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fa fa-sign-out"></i> {{ __('admin/header.logout') }}</a>
                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
            </ul>
        </div>
    </div><!-- .main-menu -->

</header>