<aside class="left-menu--vertical">
    <section class="welcome-user">
        <small class="welcome-text">{{ __('admin/sidebar.welcome') }}</small>
        <h5 class="welcome-username">{{ Auth::user()->name }}</h5>
        <small class="welcome-email">{{ Auth::user()->email }}</small>
    </section>
    <ul class="list-unstyled">
        <li class="{{ (Request::is('admin/machine')||Request::is('admin/machine/*'))?'active':'' }}">
            <a><i class="sprite-superadmin-icon icon-properties"></i>{{ __('admin/sidebar.machines') }}</a>
            <div class="collapse in" id="properties" aria-expanded="true">
                <ul class="list-unstyled">
                    <li><a href="{{ route('admin.machine.index', ['type' => \App\Machine::TYPE_WATER]) }}">{{ __('admin/sidebar.waters') }}</a></li>
                    <li><a href="{{ route('admin.machine.index', ['type' => \App\Machine::TYPE_VENDING]) }}">{{ __('admin/sidebar.vendings') }}</a></li>
                    <li><a href="{{ route('admin.machine.index', ['type' => \App\Machine::TYPE_OXYGEN]) }}">{{ __('admin/sidebar.oxygens') }}</a></li>
                    <li><a href="{{ route('admin.machine.index', ['type' => \App\Machine::TYPE_WASHING]) }}">{{ __('admin/sidebar.washings') }}</a></li>
                    <li><a href="{{ route('admin.machine.index', ['type' => \App\Machine::TYPE_RELENISHMENT]) }}">{{ __('admin/sidebar.replenishments') }}</a></li>
                </ul>
            </div>
        </li>
        <li class="{{ (Request::is('admin/user')||Request::is('admin/user/*'))?'active':'' }}"><a href="{{ route('admin.user.index') }}"><i class="sprite-superadmin-icon icon-users"></i> {{ __('admin/sidebar.users') }}</a></li>
        <li class="{{ (Request::is('admin/version')||Request::is('admin/version/*'))?'active':'' }}"><a href="{{ route('admin.version.index') }}"><i class="sprite-superadmin-icon icon-entries"></i> {{ __('admin/sidebar.versions') }}</a></li>
        {{-- <li><a href="#"><i class="sprite-superadmin-icon icon-analytics"></i> {{ __('admin/sidebar.app_menu_analytics') }}</a></li>
        <li><a href="#"><i class="sprite-superadmin-icon icon-analytics"></i> {{ __('admin/sidebar.api_analytics') }}</a></li> --}}
    </ul>
</aside>
