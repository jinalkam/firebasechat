<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="no-scroll">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.png') }}" />

    <!-- Font Awesome Icons -->
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet" />

    <!-- Toastr style -->
    <link href="{{ asset('css/plugins/toastr/toastr.min.css') }}" rel="stylesheet" />

    <!-- Plugins -->
    <link href="{{ asset('js/plugins/gritter/jquery.gritter.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/blueimp/css/blueimp-gallery.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/dropzone/dropzone.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/iCheck/custom.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/chosen/chosen.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/colorpicker/bootstrap-colorpicker.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/switchery/switchery.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/cropper/cropper.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/switchery/switchery.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/jasny/jasny-bootstrap.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/nouslider/jquery.nouislider.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/ionRangeSlider/ion.rangeSlider.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/ionRangeSlider/ion.rangeSlider.skinFlat.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/dataTables/datatables.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/select2/select2.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/summernote/summernote.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/summernote/summernote-bs3.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/datapicker/datepicker3.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/clockpicker/clockpicker.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/plugins/datetimepicker/bootstrap-datetimepicker.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/animate.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/style.css') }}" rel="stylesheet" />

    <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
    

</head>
<body>
    <!-- Wrapper -->
    <div id="wrapper">
        <!-- Sidebar Navigation -->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <span>
                                <img alt="Admin profile pic" class="img-circle" src="{{ asset('img/todd_farrell.gif') }}" width="48"/>
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
<!--                                <li>
                                    <a href="javascript:void(0);">
                                        <i class="fa fa-user" aria-hidden="true"></i>&nbsp;Edit Profile
                                    </a>
                                </li>

                                <li class="divider"></li>-->

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
                    <li class="{{ ($activeMenu == 'dashboard') ? 'active' : '' }}">
                        <a href="{{ url('admin/dashboard') }}"><i class="fa fa-th-large" aria-hidden="true"></i>&nbsp;<span class="nav-label">Dashboard</span></a>
                    </li>
                    <li class="{{ ($activeMenu == 'manage-users') ? 'active' : '' }}">
                        <a href="{{ url('user/userlist') }}"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;<span class="nav-label">Manage Users</span></a>
                    </li>
                    
                    <li class="{{ ($activeMenu == 'manage-advertisements' || $activeMenu == 'manage-advertisements.analytics') ? 'active' : '' }}">
                        <a href="javascript:void(0);"><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;<span class="nav-label">Advertise Manager</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li class="{{ ($activeMenu == 'manage-advertisements') ? 'active' : '' }}">
                                <a href="{{ url('advertisements') }}">Manage Ads</a>
                            </li>
                            <li class="{{ ($activeMenu == 'manage-advertisements.analytics') ? 'active' : '' }}">
                                <a href="{{ url('analytics') }}">Analytics</a>
                            </li>
                        </ul>
                    </li>
                    
<!--                    <li class="{{ ($activeMenu == 'manage-advertisements') ? 'active' : '' }}">
                        <a href="{{ url('advertisements') }}"><i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;<span class="nav-label">Manage Ads</span></a>
                    </li>-->
                    <li class="{{ ($activeMenu == 'manage-cms-pages') ? 'active' : '' }}">
                        <a href="{{ url('cms-page/list') }}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>&nbsp;<span class="nav-label">Manage CMS Pages</span></a>
                    </li>
                    <li class="{{ ($activeMenu == 'manage-supports') ? 'active' : '' }}">
                        <a href="{{ url('support') }}"><i class="fa fa-life-ring" aria-hidden="true"></i>&nbsp;<span class="nav-label">Support Query</span></a>
                    </li>
<!--                    <li class="{{ ($activeMenu == 'manage-interests') ? 'active' : '' }}">
                        <a href="javascript:void(0);"><i class="fa fa-globe" aria-hidden="true"></i>&nbsp;<span class="nav-label">Miscellaneous</span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li class="{{ ($activeMenu == 'manage-interests') ? 'active' : '' }}">
                                <a href="{{ url('interests') }}">Manage Interests</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">Support Queries</a>
                            </li>
                        </ul>
                    </li>-->
                </ul>
            </div>
        </nav>
        <!-- /Sidebar Navigation -->

        <!-- Page Wrapper -->
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <!-- Top Navigation -->
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary" href="javascript:void(0);"><i class="fa fa-bars"></i></a>

                    </div>

                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <span class="m-r-sm text-muted welcome-message">Welcome to {{config(app.name) }} Admin Panel</span>
                        </li>

                        <li>
                            <span class="m-r-sm text-muted welcome-message">{{ $today }}</span>
                        </li>

                        <li>
                            <a href="{{ route('admin.logout') }}"
                                onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                <i class="fa fa-sign-out"></i>&nbsp;Logout
                            </a>

                            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                        </li>
                    </ul>
                </nav>
                <!-- /Top Navigation -->
            </div>

            <div class="wrapper wrapper-content">
                @yield('content')

                <!-- Footer -->
                <div class="footer">
                    Copyright &copy; <?php echo date('Y'); ?> Hello Layover.
                </div>
                <!-- /Footer -->
            </div>
        </div>
        <!-- /Page Wrapper -->
    </div>
    <!-- /Wrapper -->

    <!-- Loading scripts at the bottom for faster page loads. -->
    <script src="{{ asset('js/jquery-2.1.1.js') }}"></script>
    <script src="{{ asset('js/jquery-ui-1.10.4.min.js') }}"></script>
    <!-- Mainly scripts -->
<!--    <script src="{{ asset('js/jquery-2.1.1.js') }}"></script>
    <script src="{{ asset('js/jquery-ui-1.10.4.min.js') }}"></script>-->
    <script src="{{ asset('js/html5lightbox.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

    <!-- Flot -->
    <script src="{{ asset('js/plugins/flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.spline.js') }}"></script>
    <script src="{{ asset('js/plugins/flot/jquery.flot.resize.js') }}"></script>

    <!-- Peity -->
    <script src="{{ asset('js/plugins/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ asset('js/demo/peity-demo.js') }}"></script>

    <!-- Custom and plugin javascript -->
    <script src="{{ asset('js/inspinia.js') }}"></script>

    <script src="{{ asset('js/plugins/pace/pace.min.js') }}"></script>

    <!-- GITTER -->
    <script src="{{ asset('js/plugins/gritter/jquery.gritter.min.js') }}"></script>

    <!-- ChartJS-->
    <script src="{{ asset('js/plugins/chartJs/Chart.min.js') }}"></script>

    <!-- Toastr -->
    <script src="{{ asset('js/plugins/toastr/toastr.min.js') }}"></script>


    <!-- iCheck -->
    <script src="{{ asset('js/plugins/iCheck/icheck.min.js') }}"></script>



    <!-- jqGrid -->
    <script src="{{ asset('js/plugins/jqGrid/i18n/grid.locale-en.js') }}"></script>

    <script src="{{ asset('js/plugins/jqGrid/jquery.jqGrid.min.js') }}"></script>

    <script src="{{ asset('js/plugins/jeditable/jquery.jeditable.js') }}"></script>

    <script src="{{ asset('js/plugins/dropzone/dropzone.js') }}"></script>

    <script src="{{ asset('js/plugins/dataTables/datatables.min.js') }}"></script>

    <script src="{{ asset('js/plugins/switchery/switchery.js') }}"></script>
      <script src="{{ asset('js/commonweb.js') }}"></script>
     @yield('scripts')
        
        @if (Session::has('message') && !empty(Session::get('message')))
           
                <script type="text/javascript">
          
                    var toasterMessage = '{{Session::get("message")}}';
                    
                    var objMessageToastNotication = {
                        message: toasterMessage
                    };
                    setToastNotification(objMessageToastNotication);
                </script>
            
        @endif

</body>
</html>
