<nav id="sidebar" aria-label="Main Navigation">
    <div class="content-header bg-white-5">
        <a class="font-w600 text-dual" href="index.html">
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
                <a class="nav-main-link active" href="{{route('store.dashboard')}}">
                    <i class="nav-main-link-icon si si-speedometer"></i>
                    <span class="nav-main-link-name">Dashboard</span>
                </a>
            </li>
            <li class="nav-main-item open">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                    <i class="nav-main-link-icon si si-layers"></i>
                    <span class="nav-main-link-name">Products</span>
                </a>
                <ul class="nav-main-submenu">
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{route('store.product.wefulfill')}}">
                            <i class="nav-main-link-icon si si-bag"></i>
                            <span class="nav-main-link-name">By WeFullfill</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{route('store.import_list')}}">
                            <i class="nav-main-link-icon si si-bag"></i>
                            <span class="nav-main-link-name">Import List</span>
                        </a>
                    </li>

                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{route('store.my_products')}}">
                            <i class="nav-main-link-icon si si-bag"></i>
                            <span class="nav-main-link-name">My Products</span>
                        </a>
                    </li>

                </ul>
            </li>
            <li class="nav-main-item">
                <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
                    <i class="nav-main-link-icon si si-bag"></i>
                    <span class="nav-main-link-name">Orders</span>
                </a>
                <ul class="nav-main-submenu open">
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{route('store.orders')}}">
                            <i class="nav-main-link-icon si si-bag"></i>
                            <span class="nav-main-link-name">My Orders</span>
                        </a>
                    </li>


                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{route('store.payments')}}">
                            <i class="nav-main-link-icon si si-bag"></i>
                            <span class="nav-main-link-name">Payment History</span>
                        </a>
                    </li>

                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{route('store.tracking')}}">
                            <i class="nav-main-link-icon si si-bag"></i>
                            <span class="nav-main-link-name">Tracking Info</span>
                        </a>
                    </li>
                    <li class="nav-main-item">
                        <a class="nav-main-link" href="{{route('store.refunds')}}">
                            <i class="nav-main-link-icon si si-bag"></i>
                            <span class="nav-main-link-name">Refunds</span>
                        </a>
                    </li>

                </ul>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('store.customers')}}">
                    <i class="nav-main-link-icon si si-user"></i>
                    <span class="nav-main-link-name">Customer</span>
                </a>
            </li>



            <li class="nav-main-item">
                <a class="nav-main-link"  href="{{route('store.user.wallet.show')}}">
                    <i class="nav-main-link-icon si si-wallet"></i>
                    <span class="nav-main-link-name">Wallet</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link"  href="{{route('store.wishlist')}}">
                    <i class="nav-main-link-icon fa fa-heart"></i>
                    <span class="nav-main-link-name">Wishlist</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('store.notifications')}}">
                    <i class="nav-main-link-icon fa fa-sticky-note"></i>
                    <span class="nav-main-link-name">Notifications</span>
                </a>
            </li>


            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('store.help-center')}}">
                    <i class="nav-main-link-icon fa fa-hands-helping"></i>
                    <span class="nav-main-link-name">Help Center</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('store.index')}}">
                    <i class="nav-main-link-icon si si-wrench"></i>
                    <span class="nav-main-link-name">Settings</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('store.reports')}}">
                    <i class="nav-main-link-icon si si-chart"></i>
                    <span class="nav-main-link-name">Reports</span>
                </a>
            </li>

            <li class="nav-main-item">
                <a class="nav-main-link" href="{{route('store.university.index')}}">
                    <i class="nav-main-link-icon si si-info"></i>
                    <span class="nav-main-link-name">Wefulfill University</span>
                </a>
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
        @php
            $shop =  \OhMyBrew\ShopifyApp\Facades\ShopifyApp::shop();
       /*Local Shop Model!*/
       $shop= \App\Shop::find($shop->id);
           if($shop->has_manager != null){
           $manager = $shop->has_manager;
           }
           else{
               $manager = null;
           }

         if(count($shop->has_user) > 0){
            $associated_user =   $shop->has_user[0];
        }
        else{
            $associated_user = null;
        }

        @endphp
        <!-- User Dropdown -->
            @if($manager != null)
                <div  class="dropdown d-inline-block ml-2">
                    <button  type="button" class="btn btn-sm btn-dual" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img class="rounded" @if($manager->profile == null) src="{{ asset('assets/media/avatars/avatar10.jpg') }}" @else  src="{{asset('managers-profiles')}}/{{$manager->profile}}" @endif alt="Header Avatar" style="width: 18px;">
                        <span class="d-none d-sm-inline-block ml-1">Sales Manager {{$manager->name}} {{$manager->last_name}}</span>
                        <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-left p-0 border-0 font-size-sm" style="width: 460px" aria-labelledby="page-header-user-dropdown">
                        <div class="p-3 text-center">
                            <img class="img-avatar-thumb" style="display: inline-block !important;
     width: 70px !important;
     height: 70px !important;
    border-radius: 0;" @if($manager->profile == null) src="{{ asset('assets/media/avatars/avatar10.jpg') }}" @else  src="{{asset('managers-profiles')}}/{{$manager->profile}}" @endif alt="">
                            <h6 style="margin-top: 10px">I am {{$manager->name}} {{$manager->last_name}}, Your Dedicated Sales Manager</h6>
                        </div>
                        <div class="p-2">
                            <p class="p-2" style="margin-bottom: 0"> I am here to help you with any kind of queries you might have. Feel free to contact me using tickets so that i can help you with understanding how can you get most of WeFullFill. Thanks </p>
                            <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{route('store.help-center')}}">
                                <span>Contact Me</span>
                                <i class="fa fa-ticket-alt"></i>
                            </a>

                        </div>
                    </div>
                </div>
            @endif
        </div>



        <div class="d-flex align-items-center">
            <!-- User Dropdown -->
            <div class="d-inline-block mr-3">
                <span class="badge badge-primary" style="font-size: 13px"> Wallet Balance  : {{number_format($balance,2)}} USD </span>
            </div>
            @if(\Illuminate\Support\Facades\Auth::check())
            <div class="d-inline-block mr-3">
                <a class="nav-main-link" href="{{route('users.index')}}">
                    <i class="nav-main-link-icon fa fa-sync"></i>
                    <span class="nav-main-link-name">Switch To User View</span>
                </a>

            </div>
            @endif

            <div class="dropdown d-inline-block ml-2">
                <button type="button" class="btn btn-sm btn-dual" id="page-header-notifications-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="si si-bell"></i>
                    <span class="badge badge-primary badge-pill">{{$notifications_count}}</span>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0 border-0 font-size-sm" aria-labelledby="page-header-notifications-dropdown">
                    <div class="p-2 bg-primary text-center">
                        <h5 class="dropdown-header text-uppercase text-white">Notifications</h5>
                    </div>
                    <ul class="nav-items mb-0">
                        @if(count($notifications) > 0)
                            @foreach($notifications as $notification)
                                <li>
                                    <a class="text-dark media py-2" href="{{route('store.notification',$notification->id)}}">
                                        <div class="mr-2 ml-3">
                                            <i class="fa fa-fw fa-check-circle text-success"></i>
                                        </div>
                                        <div class="media-body pr-2">
                                            <div class="font-w600">{{$notification->message}}</div>
                                            <small class="text-muted">{{$notification->created_at->diffForHumans()}}</small>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li>
                                <a class="text-dark media py-2" href="javascript:void(0)">
                                    <div class="mr-2 ml-3">
                                        <i class="fa fa-fw fa-check-circle text-success"></i>
                                    </div>
                                    <div class="media-body pr-2">
                                        <div class="font-w600">No Notification</div>
                                    </div>
                                </a>
                            </li>
                        @endif

                    </ul>
                    <div class="p-2 border-top">
                        <a class="btn btn-sm btn-light btn-block text-center" href="{{route('store.notifications')}}">
                            <i class="fa fa-fw fa-arrow-down mr-1"></i> See All
                        </a>
                    </div>
                </div>
            </div>


            <div class="dropdown d-inline-block ml-2">
                <button type="button" class="btn btn-sm btn-dual" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    @if($associated_user != null)
                        <img class="rounded" @if($associated_user->profile == null) src="{{ asset('assets/media/avatars/avatar10.jpg') }}" @else  src="{{asset('managers-profiles')}}/{{$associated_user->profile}}" @endif alt="Header Avatar" style="width: 18px;">
                    @else
                        <img class="rounded" src="{{ asset('assets/media/avatars/avatar10.jpg') }}" alt="Header Avatar" style="width: 18px;">
                    @endif
                    <span class="d-none d-sm-inline-block ml-1">{{explode('.',$shop->shopify_domain)[0]}}</span>
                    <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right p-0 border-0 font-size-sm" aria-labelledby="page-header-user-dropdown">
                    <div class="p-3 text-center bg-primary">
                        @if($associated_user != null)
                            <img class="img-avatar img-avatar48 img-avatar-thumb" @if($associated_user->profile == null) src="{{ asset('assets/media/avatars/avatar10.jpg') }}" @else  src="{{asset('managers-profiles')}}/{{$associated_user->profile}}" @endif  alt="">

                        @else
                        <img class="img-avatar img-avatar48 img-avatar-thumb" src="{{ asset('assets/media/avatars/avatar10.jpg') }}" alt="">
                        @endif
                    </div>
                    <div class="p-2">

                        <a class="dropdown-item d-flex align-items-center justify-content-between" href="{{route('store.index')}}">
                            <span>Settings</span>
                            <i class="si si-settings"></i>
                        </a>
                        <a class="dropdown-item d-flex align-items-center justify-content-between" href="/logout">
                            <span>Log Out</span>
                            <i class="si si-logout ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>


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
    <!-- END Header Loader -->
</header>

