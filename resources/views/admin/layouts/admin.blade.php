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
      
        <!-- /Sidebar Navigation -->
      @include('admin/sidebar/sidebar')
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
                            <span class="m-r-sm text-muted welcome-message">Welcome to   {{ config('app.name') }}  Admin Panel</span>
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
                    Copyright &copy; <?php echo date('Y'); ?> {{ config('app.name') }}.
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

    <!-- Jvectormap -->
    <script src="{{ asset('js/plugins/jvectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>

    <script src="{{ asset('js/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>


    <!-- jqGrid -->
    <script src="{{ asset('js/plugins/jqGrid/i18n/grid.locale-en.js') }}"></script>

    <script src="{{ asset('js/plugins/jqGrid/jquery.jqGrid.min.js') }}"></script>

    <script src="{{ asset('js/plugins/jeditable/jquery.jeditable.js') }}"></script>

    <script src="{{ asset('js/plugins/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('js/admin/custom.js') }}"></script>
    
    
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
