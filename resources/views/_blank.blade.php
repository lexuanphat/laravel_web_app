<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.ico')}}">

    {{-- Datatable CSS --}}
    <link href="{{asset('assets/vendor/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/vendor/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- Theme Config Js -->
    <script src="{{asset('assets/js/hyper-config.js')}}"></script>

    <!-- Vendor css -->
    <link href="{{asset('assets/css/vendor.min.css')}}" rel="stylesheet" type="text/css" />

    <!-- App css -->
    <link href="{{asset('assets/css/app-saas.min.css')}}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('assets/css/app-custom.css')}}" rel="stylesheet" type="text/css" id="app-custom" />
    @stack('style')
</head>

<body>
    <ul class="notifications"></ul>
    <!-- Begin page -->
    <div class="wrapper">

        
        <!-- ========== Topbar Start ========== -->
        @include('layouts.header-content')
        <!-- ========== Topbar End ========== -->

        <!-- ========== Left Sidebar Start ========== -->
        @include('layouts.left-sidebar')
        <!-- ========== Left Sidebar End ========== -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- start page title -->
                    @yield('content')
                    <!-- end page title -->

                </div> <!-- container -->

            </div> <!-- content -->

            <!-- Footer Start -->
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <script>document.write(new Date().getFullYear())</script> © Hyper - Coderthemes.com
                        </div>
                        <div class="col-md-6">
                            <div class="text-md-end footer-links d-none d-md-block">
                                <a href="javascript: void(0);">About</a>
                                <a href="javascript: void(0);">Support</a>
                                <a href="javascript: void(0);">Contact Us</a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- end Footer -->

        </div>

        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <!-- Theme Settings -->
    @include('layouts.theme_setting')

    <!-- Vendor js -->
    <script src="{{asset('assets/js/vendor.min.js')}}"></script>

    {{-- Datatable scripts --}}
    <script src="{{asset('assets/vendor/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-bs5/js/dataTables.bootstrap5.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{asset('assets/vendor/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js')}}"></script>

    <!-- App js -->
    <script src="{{asset('assets/js/app.min.js')}}"></script>

    <script src="{{asset('assets/js/vi.js')}}"></script>

    <script>
        const message_errors = {
            text_not_exists: "Dữ liệu có thể đã bị xoá hoặc dữ liệu không tồn tại, vui lòng F5",
            title_not_exists: "Không thể xoá",
        };

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

        $( document ).on( "ajaxComplete", function( event, xhr, settings ) {
           if(xhr.status === 401) {
                window.location.href = @json(route('admin.login'));
           }
        });

        $('#not_fount_modal').on('hidden.bs.modal', function (e) {
            console.log(e.currentTarget);
            
        });
    </script>

    @stack('js')

</body>


<!-- Mirrored from coderthemes.com/hyper/saas/pages-starter.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 25 Apr 2025 13:53:38 GMT -->
</html>