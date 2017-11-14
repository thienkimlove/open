<header id="topnav">
    <div class="topbar-main">
        <div class="container">

            <!-- Logo container-->
            <div class="logo">
                <a href="/" class="logo"><span>{{ config('app.name') }}</span></a>
            </div>
            <!-- End Logo container-->


            <div class="menu-extras">

                <ul class="nav navbar-nav navbar-right pull-right">
                    <li class="hidden-xs">
                        <a href="#" id="btn-fullscreen" class="waves-effect waves-light"><i class="icon-size-fullscreen"></i></a>
                    </li>

                    <li class="dropdown navbar-c-items">
                        <a href="" class="dropdown-toggle waves-effect waves-light profile" data-toggle="dropdown"
                           aria-expanded="true"><img src="/vendor/ubold/assets/images/user.png" alt="user-img"
                                                     class="img-circle"> </a>
                        <ul class="dropdown-menu">
                            {{--<li><a href="javascript:void(0)"><i class="ti-user text-custom m-r-10"></i> Edit Profile</a></li>--}}
                            {{--</li>--}}
                            {{--<li><a href="javascript:void(0)"><i class="ti-lock text-custom m-r-10"></i> Change Password</a>--}}
                            {{--</li>--}}
                            <li class="divider"></li>
                            <li><a href="{{route('logout')}}"><i class="ti-power-off text-danger m-r-10"></i> Logout</a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <div class="menu-item">
                    <!-- Mobile menu toggle-->
                    <a class="navbar-toggle">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                    <!-- End mobile menu toggle-->
                </div>
            </div>

        </div>
    </div>

    @include('common.navbar')
</header>