@php
    $count = 1;
    $list_sidebar = [
        [
            'name' => 'Thống kê tổng quan',
            'link' => route('admin.dashboard'),
            'icon' => '<i class="dripicons-article"></i>'
        ],
        [
            'name' => 'Quản lý Sản phẩm',
            'link' => route('admin.product'),
            'icon' => '<i class="uil-cart"></i>',
            'sub_menu' => [
                [
                    'name' => 'Thêm mới sản phẩm',
                    'link' => route('admin.product.create'),
                ],
                [
                    'name' => 'Danh sách sản phẩm',
                    'link' => route('admin.product'),
                ],
            ],
        ],
        [
            'name' => 'Quản Lý Kho',
            'link' => route('admin.dashboard').$count++,
            'icon' => '<i class="uil-dropbox"></i>',
            'sub_menu' => [
                [
                    'name' => 'Nhập kho',
                    'link' => route('admin.dashboard').$count++,
                ],
                [
                    'name' => 'Danh sách quản lý kho',
                    'link' => route('admin.dashboard').$count++,
                ],
            ],
        ],
        [
            'name' => 'Quản lý Cửa hàng',
            'link' => route('admin.dashboard').$count++,
            'icon' => '<i class="uil-store-alt"></i>',
            'sub_menu' => [
                [
                    'name' => 'Danh sách cửa hàng',
                    'link' => route('admin.shop'),
                ],
            ],
        ],
        [
            'name' => 'Quản lý Khách hàng',
            'link' => route('admin.dashboard').$count++,
            'icon' => '<i class="uil-users-alt"></i>',
            'sub_menu' => [
                [
                    'name' => 'Danh sách khách hàng',
                    'link' => route('admin.customer'),
                ],
            ],
        ],
        [
            'name' => 'Quản lý nhân viên',
            'link' => '',
            'icon' => '<i class="uil-user-square"></i>',
            'sub_menu' => [
                [
                    'name' => 'Thêm mới nhân viên',
                    'link' => route('admin.staff.create'),
                ],
                [
                    'name' => 'Danh sách nhân viên',
                    'link' => route('admin.staff'),
                ],
            ],
        ],
        [
            'name' => 'Cấu hình hệ thống',
            'link' => route('admin.dashboard').$count++,
            'icon' => '<i class="dripicons-gear"></i>',
        ],
        [
            'name' => 'Báo cáo',
            'link' => 'bao-cao-doanh-thu',
            'icon' => '<i class="uil-chart-growth"></i>',
        ],
    ];
@endphp
<div class="left-side-menu">

    <!-- LOGO -->
    <a href="{{route('admin.dashboard')}}" class="logo text-center logo-light">
        <span class="logo-lg">
            <img src="{{asset('assets/images/logo.png')}}" alt="" height="16">
        </span>
        <span class="logo-sm">
            <img src="{{asset('assets/images/logo_sm.png')}}" alt="" height="16">
        </span>
    </a>

    <!-- LOGO -->
    <a href="{{route('admin.dashboard')}}" class="logo text-center logo-dark">
        <span class="logo-lg">
            <img src="{{asset('assets/images/logo-dark.png')}}" alt="" height="16">
        </span>
        <span class="logo-sm">
            <img src="{{asset('assets/images/logo_sm_dark.png')}}" alt="" height="16">
        </span>
    </a>

    <div class="h-100" id="left-side-menu-container" data-simplebar>

        <!--- Sidemenu -->
        <ul class="metismenu side-nav mm-show">
            @foreach($list_sidebar as $sidebar)
            @if(isset($sidebar['sub_menu']))
            <li class="side-nav-item">
                <a href="javascript: void(0);" class="side-nav-link">
                    {!!$sidebar['icon']!!}
                    <span> {{$sidebar['name']}} </span>
                </a>
                <ul class="side-nav-second-level mm-collapse" aria-expanded="false">
                    @foreach($sidebar['sub_menu'] as $sub_menu)
                    <li>
                        <a href="{{ $sub_menu['link']}}" class="{{url()->current() === $sub_menu['link'] ? 'active' : ''}}">{{$sub_menu['name']}}</a>
                    </li>
                    @endforeach
                </ul>
            </li>
            @else 
            <li class="side-nav-item">
                <a href="{{$sidebar['link']}}" class="side-nav-link{{url()->current() === $sidebar['link'] ? ' active' : ''}}">
                    {!!$sidebar['icon']!!}
                    <span> {{$sidebar['name']}} </span>
                </a>
            </li>
            @endif
            @endforeach
        </ul>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>