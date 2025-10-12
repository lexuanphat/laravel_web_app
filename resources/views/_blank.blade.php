
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name', 'Phần mềm quản lý nước mắm') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Phần mềm quản lý" name="description" />
    <meta content="Le Xuan Phat" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('assets/images/logo-new.png')}}">

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
    <link href="https://fonts.cdnfonts.com/css/helvetica-neue" rel="stylesheet">
    <style>
        html, body {
            /* font-family: 'Helvetica Neue', sans-serif !important; */
        }
    </style>
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

                    @yield('content')

                </div> <!-- container -->

            </div> <!-- content -->

            <!-- Footer Start -->
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

    <script>
        const ASSETS = {
            url_storage:  @json(asset("storage/:image_url")),
            url_no_image: @json(asset("assets/images/no-image.jpg")),
        };
    </script>

    @stack('js')

    <script>
        $(document).ready(function(){
            $(".input_money").on('keyup', function(e){
                let $this = $(this);
                let value = Number($this.val().replace(/\D/g,''));
                $this.val(value.toLocaleString('vi'))
            }).on('focus', function(e){
                if(Number($(this).val()) === 0) {
                    $(this).val("")
                }
            }).on('blur', function(e){
                if($(this).val() === "") {
                    $(this).val(0)
                }
            });
            
            @stack('js_ready')
        })
    </script>


</body>


<!-- Mirrored from coderthemes.com/hyper/saas/pages-starter.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 25 Apr 2025 13:53:38 GMT -->
</html>