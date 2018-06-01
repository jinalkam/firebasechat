<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <!-- Styles -->

        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
        <link href="{{ asset('css/animate.css') }}" rel="stylesheet">
        <link href="{{ asset('css/style.css') }}" rel="stylesheet">
        <script src="{{ asset('js/jquery-2.1.1.js') }}"></script>
        
        @yield('styles')

    </head>

    <body class="gray-bg">

    <div class="middle-box text-center  animated fadeInDown">
        <div>
        
        @yield('content')
         
        </div>
    </div>

        <!-- Mainly scripts -->
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
        <script src="{{ asset('js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

       <!-- Toastr -->
        <script src="{{ asset('js/plugins/toastr/toastr.min.js') }}"></script>

        <script src="{{ asset('js/jquery.blockUI.js') }}"></script>

        <script src="{{ asset('js/custom.js') }}"></script>

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

