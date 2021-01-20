
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <title>WeFullfill</title>

    <meta name="description" content="WeFullfill 2020 created by TetraLogicx Pvt. Limited.">
    <meta name="author" content="pixelcave">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=0">

    <!-- Icons -->
    <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
    <link rel="shortcut icon" href="{{ asset('assets/img/favicons/wefullfill.png') }}">

    <!-- END Icons -->

    <!-- Stylesheets -->
    <!-- Web fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400italic,600,700%7COpen+Sans:300,400,400italic,600,700">

    <!-- Page JS Plugins CSS -->
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/slick/slick.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/slick/slick-theme.min.css') }}">

    <!-- Bootstrap and OneUI CSS framework -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/oneui.css') }}">
    <link rel="stylesheet" id="css-main" href="{{ asset('assets/css/oneui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/summernote/summernote.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/dropzonejs/dropzone.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/select2/select2-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/js/plugins/jquery-tags-input/jquery.tagsinput.min.css') }}">
{{--    <link rel="stylesheet" href="{{ asset('assets/js/plugins/sweetalert2/sweetalert2.css') }}">--}}


<!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
    <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/flat.min.css"> -->
    <!-- END Stylesheets -->
</head>
<body>
<!-- Page Container -->
<div id="page-container" class="sidebar-l sidebar-o side-scroll header-navbar-fixed">


    <!-- Header -->
    <header id="header-navbar" class="content-mini content-mini-full">
        <!-- Header Navigation Right -->
        <nav id="sidebar">
            <!-- Sidebar Scroll Container -->
            <div id="sidebar-scroll">
                <!-- Sidebar Content -->
                <!-- Adding .sidebar-mini-hide to an element will hide it when the sidebar is in mini mode -->
                <div class="sidebar-content">
                    <!-- Side Header -->
                    <div class="side-header side-content bg-white-op">
                        <!-- Layout API, functionality initialized in App() -> uiLayoutApi() -->
                        <a class="h5 text-white">
                            <span class="h4 font-w600 sidebar-mini-hide">WeFullFill</span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        <ul class="nav-header pull-right">
            <li>
                <div class="btn-group">
                    <button class="btn btn-default btn-image dropdown-toggle" data-toggle="dropdown" type="button">
                        <img src="{{asset('assets/img/avatars/avatar10.jpg')}}" alt="Avatar">
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="dropdown-header">Profile</li>
                        <li>
                            <a tabindex="-1" href="{{route('logout')}}">
                                <i class="si si-logout pull-right"></i>Log out
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
            </li>
        </ul>
        <!-- END Header Navigation Right -->
        <!-- END Header Navigation Left -->
    </header>
    <main id="main-container">
        <div class="content">
            <div class="row">
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <a class="block block-rounded block-link-hover2" href="{{route('managers.dashboard')}}">
                        <div class="block-content block-content-full text-center bg-amethyst ribbon ribbon-bookmark ribbon-crystal">
                            <div class="ribbon-box font-w600">Sales</div>
                            <div class="item item-2x item-circle bg-crystal-op push-20-t push-20 animated fadeIn" data-toggle="appear" data-offset="50" data-class="animated fadeIn">
                                <i class="fa fa-dollar text-white-op"></i>
                            </div>
                        </div>
                        <div class="block-content" style="padding: 20px">
                            <h4 class="text-center">Sales Manager Panel</h4>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-6 col-lg-6">
                    <a class="block block-rounded block-link-hover2" href="{{route('users.dashboard')}}">
                        <div class="block-content block-content-full text-center bg-success ribbon ribbon-bookmark ribbon-crystal">
                            <div class="ribbon-box font-w600">Store</div>
                            <div class="item item-2x item-circle bg-crystal-op push-20-t push-20 animated fadeIn" data-toggle="appear" data-offset="50" data-class="animated fadeIn">
                                <i class="fa fa-shopping-cart text-white-op"></i>
                            </div>
                        </div>
                        <div class="block-content" style="padding: 20px">
                            <h4 class="text-center">Drop-shipping Management Panel</h4>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </main>
</div>
<!-- END Page Container -->
<!-- OneUI Core JS: jQuery, Bootstrap, slimScroll, scrollLock, Appear, CountTo, Placeholder, Cookie and App.js -->
<script src="{{ asset('assets/js/core/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/core/jquery.slimscroll.min.js') }}"></script>
<script src="{{ asset('assets/js/core/jquery.scrollLock.min.js') }}"></script>
<script src="{{ asset('assets/js/core/jquery.appear.min.js') }}"></script>
<script src="{{ asset('assets/js/core/jquery.countTo.min.js') }}"></script>
<script src="{{ asset('assets/js/core/jquery.placeholder.min.js') }}"></script>
<script src="{{ asset('assets/js/core/js.cookie.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>

<!-- Page Plugins -->
<script src="{{ asset('assets/js/plugins/slick/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/chartjs/Chart.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/summernote/summernote.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/dropzonejs/dropzone.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/jquery-tags-input/jquery.tagsinput.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

<!-- Page JS Code -->
<script src="{{ asset('assets/js/pages/base_pages_dashboard.js') }}"></script>
<script src="{{ asset('js/admin.js') }}"></script>

<script>
    jQuery(function () {
        App.initHelpers('slick');
    });
    jQuery(function () {
        // Init page helpers (Table Tools helper)
        App.initHelpers('table-tools');
    });
    jQuery(function () {
        // Init page helpers (Summernote + CKEditor + SimpleMDE plugins)
        App.initHelpers(['maxlength', 'select2', 'tags-inputs', 'summernote', 'appear', 'appear-countTo']);
    });
</script>
</body>
</html>
