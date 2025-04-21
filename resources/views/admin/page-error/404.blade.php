<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Error 404 | Hyper - Responsive Bootstrap 4 Admin Dashboard</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <!-- App css -->
        <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('assets/css/app-creative.min.css')}}" rel="stylesheet" type="text/css" id="light-style" />
        <link href="{{asset('assets/css/app-creative-dark.min.css')}}" rel="stylesheet" type="text/css" id="dark-style" />

    </head>

    <body class="authentication-bg" data-layout-config='{"darkMode":false}'>

        <div class="account-pages mt-5 mb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-5">
                        <div class="card">
                            <!-- Logo -->
                            <div class="card-header pt-4 pb-4 text-center bg-primary">
                                <a href="index.html">
                                    <span><img src="{{asset('assets/images/logo.png')}}" alt="" height="75"></span>
                                </a>
                            </div>

                            <div class="card-body p-4">
                                <div class="text-center">
                                    <h1 class="text-error">4<i class="mdi mdi-emoticon-sad"></i>4</h1>
                                    <h4 class="text-uppercase text-danger mt-3">Không tìm thấy trang</h4>
                                    <p class="text-muted mt-3">Trang mà bạn yêu cầu không tồn tại, vui lòng xem lại</p>

                                    <a class="btn btn-info mt-3" href="{{route('admin.login')}}"><i class="mdi mdi-reply"></i> Trở về</a>
                                </div>
                            </div> <!-- end card-body-->
                        </div>
                        <!-- end card -->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end page -->

        <footer class="footer footer-alt">
            2025 Le Xuan Phat
        </footer>

        <!-- bundle -->
        <script src="{{asset('assets/js/vendor.min.js')}}"></script>
        <script src="{{asset('assets/js/app.min.js')}}"></script>
        
    </body>
</html>
