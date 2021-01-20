<nav id="sidebar" aria-label="Main Navigation">
    <div class="content-header bg-white-5">
        <a class="font-w600 text-dual" href="{{ route('admin.dashboard') }}">
            <i class="fa fa-circle-notch text-primary"></i>
            <span class="smini-hide">
                            <span class="font-w700 font-size-h5">WeFullFill</span>
                        </span>
        </a>
        <div>
            <a class="d-lg-none btn btn-sm btn-dual ml-2" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
                <i class="fa fa-fw fa-times"></i>
            </a>
        </div>
    </div>

    <div class="content-side content-side-full">
        <ul class="nav-main">
            <li class="nav-main-item">
                <a class="nav-main-link active" href="{{ route('admin.dashboard') }}">
                    <i class="nav-main-link-icon si si-speedometer"></i>
                    <span class="nav-main-link-name">Dashboard</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                    <i class="nav-main-link-icon si si-layers"></i>
                    <span class="nav-main-link-name">Products</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('product.all')}}">
                            <i class="nav-main-link-icon si si-bag"></i>
                            <span class="nav-main-link-name">All Products</span>
                        </a>
                    </li>

                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('product.create') }}">
                            <i class="nav-main-link-icon si si-bag"></i>
                            <span class="nav-main-link-name">Add New Product</span>
                        </a>
                    </li>

                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('category.create') }}">
                            <i class="nav-main-link-icon si si-bag"></i>
                            <span class="nav-main-link-name">Categories</span>
                        </a>
                    </li>

                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('tags.create') }}">
                            <i class="nav-main-link-icon si si-bag"></i>
                            <span class="nav-main-link-name">Tags</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('admin.orders')}}">
                    <i class="nav-main-link-icon si si-bag"></i>
                    <span class="nav-main-link-name">Orders</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('orders.bulk.tracking')}}">
                    <i class="nav-main-link-icon fa fa-truck"></i>
                    <span class="nav-main-link-name">Bulk Tracking</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('stores.index')}}">
                    <i class="nav-main-link-icon si si-home"></i>
                    <span class="nav-main-link-name">Users </span>
                </a>
            </li>
{{--            <li class="nav-main-item">--}}
{{--                <a class="nav-main-link" href="{{route('users.index')}}">--}}
{{--                    <i class="nav-main-link-icon si si-user"></i>--}}
{{--                    <span class="nav-main-link-name">Non Shopify Users</span>--}}
{{--                </a>--}}
{{--            </li>--}}

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('sales-managers.index')}}">
                    <i class="nav-main-link-icon si si-users"></i>
                    <span class="nav-main-link-name">Sales Managers</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('refunds.index')}}">
                    <i class="nav-main-link-icon fa fa-receipt"></i>
                    <span class="nav-main-link-name">Refunds</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('tickets.index')}}">
                    <i class="nav-main-link-icon fa fa-ticket-alt"></i>
                    <span class="nav-main-link-name">Tickets</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('admin.wallets')}}">
                    <i class="nav-main-link-icon fa fa-wallet"></i>
                    <span class="nav-main-link-name">Wallets</span>
                </a>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('wishlist.index')}}">
                    <i class="nav-main-link-icon fa fa-heart"></i>
                    <span class="nav-main-link-name">Wishlist</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('warehouse.index')}}">
                    <i class="nav-main-link-icon fa fa-warehouse"></i>
                    <span class="nav-main-link-name">Warehouse</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#" >
                    <i class="nav-main-link-icon si si-basket-loaded"></i>
                    <span class="nav-main-link-name">Shipping</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{route('zone.index')}}">
                            <i class="nav-main-link-icon si si-basket-loaded"></i>
                            <span class="nav-main-link-name">Shipping Zones</span>
                        </a>
                    </li>

                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('couriers.index')}}">
                            <i class="nav-main-link-icon si si-basket-loaded"></i>
                            <span class="nav-main-link-name">Courier Service Providers</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('default_info') }}">
                    <i class="nav-main-link-icon si si-support"></i>
                    <span class="nav-main-link-name">Settings</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('admin.emails.index') }}">
                    <i class="nav-main-link-icon fa fa-envelope"></i>
                    <span class="nav-main-link-name">Email Template</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('admin.activity.log.index') }}">
                    <i class="nav-main-link-icon fa fa-check"></i>
                    <span class="nav-main-link-name">Activity Logs</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{ route('email.campaigns.index') }}">
                    <i class="nav-main-link-icon fa fa-flag"></i>
                    <span class="nav-main-link-name">Campaigns</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                    <i class="nav-main-link-icon si si-basket-loaded"></i>
                    <span class="nav-main-link-name">Discounts</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('admin.tiered.pricing.preferences')}}">
                            <i class="nav-main-link-icon si si-basket-loaded"></i>
                            <span class="nav-main-link-name">Tiered Pricing</span>
                        </a>
                    </li>

                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{ route('admin.general.discount.preferences')}}">
                            <i class="nav-main-link-icon si si-basket-loaded"></i>
                            <span class="nav-main-link-name">General Discounts</span>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<header id="page-header">
    <div class="content-header">
        <div class="d-flex align-items-center">
            <button type="button" class="btn btn-sm btn-dual mr-2 d-lg-none" data-toggle="layout" data-action="sidebar_toggle">
                <i class="fa fa-fw fa-bars"></i>
            </button>
            <button type="button" class="btn btn-sm btn-dual mr-2 d-none d-lg-inline-block" data-toggle="layout" data-action="sidebar_mini_toggle">
                <i class="fa fa-fw fa-ellipsis-v"></i>
            </button>

            <button type="button" class="btn btn-sm btn-dual d-sm-none" data-toggle="layout" data-action="header_search_on">
                <i class="si si-magnifier"></i>
            </button>
            <form class="d-none d-sm-inline-block" action="" method="POST">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control form-control-alt" placeholder="Search.." id="page-header-search-input2" name="page-header-search-input2">
                    <div class="input-group-append">
                                    <span class="input-group-text bg-body border-0">
                                        <i class="si si-magnifier"></i>
                                    </span>
                    </div>
                </div>
            </form>
        </div>



        <div class="d-flex align-items-center">
            <!-- User Dropdown -->
            <div class="d-inline-block mr-3 badge badge-primary text-white">
                <a href="/wishlists?status=1" class="text-white">
                    Wishlist Requests
                    <span class="" style="font-size: 13px"> {{$wishlist_request_count}} </span>
                </a>
            </div>

            <div class="d-inline-block mr-3 badge text-white" style="background: Purple;color: white;">
                <a href="/wishlists?status=3" class="text-white">
                    Pending Accepted Wishlists
                    <span class="" style="font-size: 13px"> {{$wishlist_accept_count}} </span>
                </a>
            </div>

            <div class="d-inline-block mr-3 badge badge-success text-white">
                <a href="/wallets-requests" class="text-white">
                    Wallet Requests
                    <span class="" style="font-size: 13px"> {{$wallet_request_count}} </span>
                </a>
            </div>

            <div class="d-inline-block mr-3 badge badge-warning text-white">
                <a href="/refunds?priority=&status=1" class="text-white">
                    Refund Requests
                    <span class="" style="font-size: 13px"> {{$refund_request_count}} </span>
                </a>
            </div>

            <div class="d-inline-block mr-3 badge badge-dark text-white">
                <a href="/tickets?priority=&status=1&more_status=3" class="text-white">
                    Ticket Requests
                    <span class="" style="font-size: 13px"> {{$tickets_request_count}} </span>
                </a>
            </div>

            <div class="dropdown d-inline-block ml-2">
                <button type="button" class="btn btn-sm btn-dual" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded" src="{{ asset('assets/media/avatars/avatar10.jpg') }}" alt="Header Avatar" style="width: 18px;">
                    <span class="d-none d-sm-inline-block ml-1">Admin</span>
                    <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right p-0 border-0 font-size-sm" aria-labelledby="page-header-user-dropdown">
{{--                    <div class="p-3 text-center bg-primary">--}}
{{--                        <img class="img-avatar img-avatar48 img-avatar-thumb" src="{{ asset('assets/media/avatars/avatar10.jpg') }}" alt="">--}}
{{--                    </div>--}}
                    <div class="p-2">
{{--                        <a class="dropdown-item d-flex align-items-center justify-content-between" href="be_pages_generic_profile.html">--}}
{{--                            <span>Profile</span>--}}
{{--                            <span>--}}
{{--                                            <span class="badge badge-pill badge-success">1</span>--}}
{{--                                            <i class="si si-user ml-1"></i>--}}
{{--                                        </span>--}}
{{--                        </a>--}}
{{--                        <a class="dropdown-item d-flex align-items-center justify-content-between" href="javascript:void(0)">--}}
{{--                            <span>Settings</span>--}}
{{--                            <i class="si si-settings"></i>--}}
{{--                        </a>--}}
                        <a class="dropdown-item d-flex align-items-center justify-content-between" href="/logout">
                            <span>Log Out</span>
                            <i class="si si-logout ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

{{--            <div class="dropdown d-inline-block ml-2">--}}
{{--                <button type="button" class="btn btn-sm btn-dual" id="page-header-notifications-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                    <i class="si si-bell"></i>--}}
{{--                    <span class="badge badge-primary badge-pill">6</span>--}}
{{--                </button>--}}
{{--                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0 border-0 font-size-sm" aria-labelledby="page-header-notifications-dropdown">--}}
{{--                    <div class="p-2 bg-primary text-center">--}}
{{--                        <h5 class="dropdown-header text-uppercase text-white">Notifications</h5>--}}
{{--                    </div>--}}
{{--                    <ul class="nav-items mb-0">--}}
{{--                        <li>--}}
{{--                            <a class="text-dark media py-2" href="javascript:void(0)">--}}
{{--                                <div class="mr-2 ml-3">--}}
{{--                                    <i class="fa fa-fw fa-check-circle text-success"></i>--}}
{{--                                </div>--}}
{{--                                <div class="media-body pr-2">--}}
{{--                                    <div class="font-w600">You have a new follower</div>--}}
{{--                                    <small class="text-muted">15 min ago</small>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                    <div class="p-2 border-top">--}}
{{--                        <a class="btn btn-sm btn-light btn-block text-center" href="javascript:void(0)">--}}
{{--                            <i class="fa fa-fw fa-arrow-down mr-1"></i> Load More..--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

        </div>
        <!-- END Right Section -->
    </div>
    <!-- END Header Content -->

    <!-- Header Search -->
    <div id="page-header-search" class="overlay-header bg-white">
        <div class="content-header">
            <form class="w-100" action="be_pages_generic_search.html" method="POST">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <button type="button" class="btn btn-danger" data-toggle="layout" data-action="header_search_off">
                            <i class="fa fa-fw fa-times-circle"></i>
                        </button>
                    </div>
                    <input type="text" class="form-control" placeholder="Search or hit ESC.." id="page-header-search-input" name="page-header-search-input">
                </div>
            </form>
        </div>
    </div>
    <!-- END Header Search -->

    <!-- Header Loader -->
    <!-- Please check out the Loaders page under Components category to see examples of showing/hiding it -->
    <div id="page-header-loader" class="overlay-header bg-white">
        <div class="content-header">
            <div class="w-100 text-center">
                <i class="fa fa-fw fa-circle-notch fa-spin"></i>
            </div>
        </div>
    </div>
</header>



{{--<nav id="sidebar">--}}
{{--    <!-- Sidebar Scroll Container -->--}}
{{--    <div id="sidebar-scroll">--}}
{{--        <!-- Sidebar Content -->--}}
{{--        <!-- Adding .sidebar-mini-hide to an element will hide it when the sidebar is in mini mode -->--}}
{{--        <div class="sidebar-content">--}}
{{--            <!-- Side Header -->--}}
{{--            <div class="side-header side-content bg-white-op">--}}
{{--                <!-- Layout API, functionality initialized in App() -> uiLayoutApi() -->--}}
{{--                <button class="btn btn-link text-gray pull-right hidden-md hidden-lg" type="button" data-toggle="layout" data-action="sidebar_close">--}}
{{--                    <i class="fa fa-times"></i>--}}
{{--                </button>--}}
{{--                <!-- Themes functionality initialized in App() -> uiHandleTheme() -->--}}
{{--                <div class="btn-group pull-right">--}}
{{--                    <button class="btn btn-link text-gray dropdown-toggle" data-toggle="dropdown" type="button">--}}
{{--                        <i class="si si-drop"></i>--}}
{{--                    </button>--}}
{{--                    <ul class="dropdown-menu dropdown-menu-right font-s13 sidebar-mini-hide">--}}
{{--                        <li>--}}
{{--                            <a data-toggle="theme" data-theme="default" tabindex="-1" href="javascript:void(0)">--}}
{{--                                <i class="fa fa-circle text-default pull-right"></i> <span class="font-w600">Default</span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a data-toggle="theme" data-theme="assets/css/themes/amethyst.min.css" tabindex="-1" href="javascript:void(0)">--}}
{{--                                <i class="fa fa-circle text-amethyst pull-right"></i> <span class="font-w600">Amethyst</span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a data-toggle="theme" data-theme="assets/css/themes/city.min.css" tabindex="-1" href="javascript:void(0)">--}}
{{--                                <i class="fa fa-circle text-city pull-right"></i> <span class="font-w600">City</span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a data-toggle="theme" data-theme="assets/css/themes/flat.min.css" tabindex="-1" href="javascript:void(0)">--}}
{{--                                <i class="fa fa-circle text-flat pull-right"></i> <span class="font-w600">Flat</span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a data-toggle="theme" data-theme="assets/css/themes/modern.min.css" tabindex="-1" href="javascript:void(0)">--}}
{{--                                <i class="fa fa-circle text-modern pull-right"></i> <span class="font-w600">Modern</span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a data-toggle="theme" data-theme="assets/css/themes/smooth.min.css" tabindex="-1" href="javascript:void(0)">--}}
{{--                                <i class="fa fa-circle text-smooth pull-right"></i> <span class="font-w600">Smooth</span>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
{{--                <a class="h5 text-white" href="{{route('admin.dashboard')}}">--}}
{{--                    <span class="h4 font-w600 sidebar-mini-hide">WeFullFill</span>--}}
{{--                </a>--}}
{{--            </div>--}}
{{--            <!-- END Side Header -->--}}

{{--            <!-- Side Content -->--}}
{{--            <div class="side-content side-content-full">--}}
{{--                <ul class="nav-main">--}}
{{--                    <li>--}}
{{--                        <a class="active" href="/"><i class="si si-speedometer"></i><span class="sidebar-mini-hide">Dashboard</span></a>--}}
{{--                    </li>--}}

{{--                    <li class="nav-main-heading"><span class="sidebar-mini-hide">User Interface</span></li>--}}
{{--                    <li>--}}
{{--                        <a class="nav-submenu active" data-toggle="nav-submenu" href="#"><i class="si si-bar-chart"></i><span class="sidebar-mini-hide">Products</span></a>--}}
{{--                        <ul>--}}
{{--                            <li>--}}
{{--                                <a class="active" href="{{ route('product.create') }}">Add New Product</a>--}}
{{--                            </li>--}}
{{--                            <li>--}}
{{--                                <a  class="nav-item active" href="{{ route('product.all')}}">View All Products</a>--}}
{{--                            </li>--}}
{{--                            <li>--}}
{{--                                <a class="nav-item active"  href="{{ route('category.create') }}">Manage Categories</a>--}}
{{--                            </li>--}}
{{--                        </ul>--}}
{{--                    </li>--}}

{{--                    <li class="nav-main-heading"><span class="sidebar-mini-hide">Settings</span></li>--}}
{{--                    <li>--}}
{{--                        <a class="nav-item active" href="{{route('sales-managers.index')}}"><i class="si si-user"></i><span class="sidebar-mini-hide">Sales Managers</span></a>--}}
{{--                    </li>--}}

{{--                    <li>--}}
{{--                        <a class="nav-item active"  href="{{route('zone.index')}}"><i class="si si-map"></i><span class="sidebar-mini-hide">Shipping Zones</span></a>--}}
{{--                    </li>--}}
{{--                    <li>--}}
{{--                        <a class="nav-item active"  href="{{ route('default_info') }}"><i class="si si-info"></i><span class="sidebar-mini-hide">Warned Platforms</span></a>--}}
{{--                    </li>--}}

{{--                </ul>--}}
{{--            </div>--}}
{{--            <!-- END Side Content -->--}}
{{--        </div>--}}
{{--        <!-- Sidebar Content -->--}}
{{--    </div>--}}
{{--    <!-- END Sidebar Scroll Container -->--}}
{{--</nav>--}}

