@php
    $count = 1;
    $list_sidebar = [
        // [
        //     'name' => 'Thống kê tổng quan',
        //     'link' => route('admin.dashboard'),
        //     'icon' => '<i class="ri-dashboard-fill"></i>'
        // ],
        [
            'name' => 'Quản lý đơn hàng',
            'link' => '',
            'icon' => '<i class="ri-shopping-bag-fill"></i>',
            'sub_menu' => [
                [
                    'name' => 'Tạo đơn và giao hàng',
                    'link' => route('admin.order.create'),
                ],
                [
                    'name' => 'Danh sách đơn hàng',
                    'link' => route('admin.order'),
                ],
                // [
                //     'name' => 'Khách hàng trả',
                //     'link' => route('admin.category'),
                // ],
            ],
        ],
        [
            'name' => 'Quản lý Sản phẩm',
            'link' => '',
            'icon' => '<i class="ri-product-hunt-fill"></i>',
            'sub_menu' => [
                [
                    'name' => 'Danh sách sản phẩm',
                    'link' => route('admin.product'),
                ],
                [
                    'name' => 'Danh sách danh mục',
                    'link' => route('admin.category'),
                ],
                [
                    'name' => 'Danh sách tag',
                    'link' => route('admin.tag'),
                ],
                [
                    'name' => 'Danh sách phí vận chuyển',
                    'link' => route('admin.shipping_fee'),
                ],
                [
                    'name' => 'Danh sách phiếu giảm giá',
                    'link' => route('admin.coupon'),
                ],
                [
                    'name' => 'Danh sách giá khu vực',
                    'link' => route('admin.fee_product_province'),
                ],
            ],
            
        ],
        [
            'name' => 'Quản lý vận chuyển',
            'link' => '',
            'icon' => '<i class="ri-truck-line"></i>',
            'sub_menu' => [
                [
                    'name' => 'Danh sách vận chuyển',
                    'link' => route('admin.transport'),
                ],
            ],
        ],
        // [
        //     'name' => 'Quản lý Danh mục',
        //     'link' => '',
        //     'icon' => '<i class="ri-clipboard-fill"></i>',
        //     'sub_menu' => [
        //         [
        //             'name' => 'Danh sách danh mục',
        //             'link' => route('admin.category'),
        //         ],
        //     ],
        // ],
        // [
        //     'name' => 'Quản Lý Kho',
        //     'link' => '',
        //     'icon' => '<i class="uil-dropbox"></i>',
        //     'sub_menu' => [
        //         [
        //             'name' => 'Danh sách quản lý kho',
        //             'link' => route('admin.product_stock'),
        //         ],
        //     ],
        // ],
        // [
        //     'name' => 'Quản lý Cửa hàng',
        //     'link' => '',
        //     'icon' => '<i class="uil-store-alt"></i>',
        //     'sub_menu' => [
        //         [
        //             'name' => 'Danh sách cửa hàng',
        //             'link' => route('admin.shop'),
        //         ],
        //     ],
        // ],
        [
            'name' => 'Quản lý Khách hàng',
            'link' => '',
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
                    'name' => 'Danh sách nhân viên',
                    'link' => route('admin.staff'),
                ],
            ],
        ],
        [
            'name' => 'Token API Vận chuyển',
            'link' => route('admin.token_transport'),
            'icon' => '<i class="ri-settings-2-line"></i>',
        ],
        // [
        //     'name' => 'Báo cáo',
        //     'link' => 'bao-cao-doanh-thu',
        //     'icon' => '<i class="uil-chart-growth"></i>',
        // ],
    ];
@endphp
<div class="leftside-menu">
    <a href="javascript:;" class="logo logo-light">
        <span class="logo-lg">
            <img style="height: 50px" src="{{asset('assets/images/logo-full.png')}}" alt="logo">
        </span>
        <span class="logo-sm">
            <img src="{{asset('assets/images/logo-new.png')}}" alt="small logo">
        </span>
    </a>


    <!-- Brand Logo Light -->
    <a href="index.html" class="logo logo-dark">
        <span class="logo-lg">
            <img src="{{asset('assets/images/logo-new.png')}}" alt="dark logo">
        </span>
        <span class="logo-sm">
            <img src="{{asset('assets/images/logo-new.png')}}" alt="small logo">
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar">
        <i class="ri-checkbox-blank-circle-line align-middle"></i>
    </div>

    <!-- Full Sidebar Menu Close Button -->
    <div class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <!-- Sidebar -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!-- Leftbar User -->
        <div class="leftbar-user">
            <a href="pages-profile.html">
                <img src="{{asset('assets/images/users/avatar-1.jpg')}}" alt="user-image" height="42" class="rounded-circle shadow-sm">
                <span class="leftbar-user-name mt-2">{{auth()->user()->full_name}}</span>
            </a>
        </div>

        <!--- Sidemenu -->
        <ul class="side-nav">
            <li class="side-nav-title">Quản lý sản xuất</li>
            <li class="side-nav-title">Quản lý bán hàng</li>

            @foreach($list_sidebar as $key => $sidebar) 
            @if(isset($sidebar['sub_menu']) && $sidebar['sub_menu'])
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarSubMenu{{$key}}" aria-expanded="false" aria-controls="sidebarSubMenu{{$key}}" class="side-nav-link">
                    {!!$sidebar['icon']!!}
                    <span> {{$sidebar['name']}} </span>
                </a>
                <div class="collapse" id="sidebarSubMenu{{$key}}">
                    <ul class="side-nav-second-level">
                        @foreach($sidebar['sub_menu'] as $item)
                        <li>
                            <a href="{{$item['link']}}">{{$item['name']}}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </li>
            @else
            <li class="side-nav-item">
                <a href="{{$sidebar['link']}}" class="side-nav-link">
                    {!!$sidebar['icon']!!}
                    <span> {{$sidebar['name']}} </span>
                </a>
            </li>
            @endif
            @endforeach
        </ul>

        <div class="clearfix"></div>
    </div>
</div>
