<aside class="left-menu--vertical">
    <section class="welcome-user">
        <small class="welcome-text">{{ __('admin/sidebar.welcome') }}</small>
        <h5 class="welcome-username">{{ Auth::user()->name }}</h5>
        <small class="welcome-email">{{ Auth::user()->email }}</small>
    </section>
    <ul class="list-unstyled">
        <li class="{{ (Request::is('admin/machine')||Request::is('admin/machine/*'))?'active':'' }}"><a href=""><i class="sprite-superadmin-icon icon-organizations"></i> {{ __('admin/sidebar.machines') }}</a></li>
        <li><a href="#"><i class="sprite-superadmin-icon icon-analytics"></i> {{ __('admin/sidebar.app_menu_analytics') }}</a></li>
        <li><a href="#"><i class="sprite-superadmin-icon icon-analytics"></i> {{ __('admin/sidebar.api_analytics') }}</a></li>
    </ul>
</aside>
