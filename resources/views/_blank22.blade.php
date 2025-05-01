<!DOCTYPE html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8" />
        <title>Webapp - Phú Hà</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}">

        <!-- App css -->
        <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/css/app-creative.min.css')}}" rel="stylesheet" type="text/css" id="light-style" />
        <link href="{{asset('assets/css/app-creative-dark.min.css')}}" rel="stylesheet" type="text/css" id="dark-style" />
        <link href="{{asset('assets/css/app-custom.css')}}" rel="stylesheet" type="text/css" id="app-custom" />

        <!-- DataTables CSS -->
        <link href="{{asset('assets/css/vendor/dataTables.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/css/vendor/responsive.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/css/vendor/buttons.bootstrap4.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/css/vendor/select.bootstrap4.css')}}" rel="stylesheet" type="text/css" />

		@stack('style')
    </head>

    <body class="loading" 
	data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true,}'>
        <ul class="notifications"></ul>
        <!-- Begin page -->
        <div class="wrapper">
            <!-- ========== Left Sidebar Start ========== -->
			@include('layouts.left-sidebar')
            <!-- Left Sidebar End -->

            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            <div class="content-page">
                <div class="content">
                    <!-- Topbar Start -->
					@include('layouts.header-content')
                    <!-- end Topbar -->
                    
					@yield('content')

                </div>
                <!-- content -->

                <!-- Footer Start -->
				@include('layouts.footer-content')
                <!-- end Footer -->

            </div>

            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->


        </div>
        <!-- END wrapper -->

        <!-- Right Sidebar -->
		@include('layouts.right-sidebar')
        <!-- /Right-bar -->

        <!-- bundle -->
        <script src="{{asset('assets/js/vendor.min.js')}}"></script>
        <script src="{{asset('assets/js/app.min.js')}}"></script>
        
        <!-- DataTable JS -->
        <script src="{{ asset('assets/js/vendor/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/dataTables.bootstrap4.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/js/vendor/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{asset('assets/js/vendor/dataTables.keyTable.min.js')}}"></script>
        <script src="{{asset('assets/js/vendor/dataTables.select.min.js')}}"></script>
        
        <script>
            const notifications = document.querySelector(".notifications")

            const createToast = (id, message, timer = 5000) => {
                let icon = "uil-info-circle";
                if(id === 'success') {
                    icon = "uil-check-circle";
                } else if(id === 'error') {
                    icon = "uil-times-circle";
                } else if(id === 'warning') {
                    icon = "uil-question-circle";
                }

                const toast = document.createElement("li");
                toast.className = `toast-custom ${id}`;
                toast.innerHTML = `<div class="column">
                                    <i class="${icon}"></i>
                                    <span>${message}</span>
                                </div>
                                <i class="uil-multiply" onclick="removeToast(this.parentElement)"></i>`;
                notifications.appendChild(toast);
                toast.timeoutId = setTimeout(() => removeToast(toast), timer);
            }

            const removeToast = (toast) => {
                toast.classList.add("hide");
                if(toast.timeoutId) clearTimeout(toast.timeoutId); // Clearing the timeout for the toast
                setTimeout(() => toast.remove(), 500); // Removing the toast after 500ms
            }
        </script>
        <!-- demo app -->
        {{-- <script src="{{asset('assets/js/pages/demo.dashboard.js')}}"></script> --}}
        <!-- end demo js-->
		@stack('js')
    </body>
</html>