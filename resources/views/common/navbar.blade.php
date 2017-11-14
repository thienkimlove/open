<div class="navbar-custom">
    <?php
        $currentUser = Sentinel::getUser();
    ?>
    <div class="container">
        <div id="navigation">
            <!-- Navigation Menu-->
            <ul class="navigation-menu">
                <li class="has-submenu">
                    <a href="/"><i class="md md-dashboard"></i>Trang chủ</a>
                </li>

                @if ($currentUser->isSuperAdmin())
                <li class="has-submenu">
                    <a href="#"><i class="md md-class"></i>Other</a>
                    <ul class="submenu">
                        <li><a href="{{ url('/users')}}">User</a></li>
                        <li><a href="{{ url('/roles') }}">Role</a></li>
                        <li><a href="{{ url('/permissions') }}">Permission</a></li>
                    </ul>
                </li>
                @endif
            </ul>
            <!-- End navigation menu        -->
        </div>
    </div> <!-- end container -->
</div> <!-- end navbar-custom -->
