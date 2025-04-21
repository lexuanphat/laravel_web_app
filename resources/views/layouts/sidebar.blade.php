<!--begin::Sidebar-->
@php
    $list_sidebar = [
        [
            'name' => 'Trang Quản Trị',
            'link' => route('admin.dashboard'),
            'icon' => '<span class="menu-icon"><i class="ki-outline ki-home-2 fs-2"></i></span>'
        ],
        [
            'name' => 'Sản phẩm',
            'link' => '',
            'icon' => '<span class="menu-icon"><i class="ki-outline ki-gift fs-2"></i></span>',
            'sub_menu' => [
                [
                    'name' => 'Thêm mới sản phẩm',
                    'link' => '',
                ],
                [
                    'name' => 'Danh sách sản phẩm',
                    'link' => '',
                ],
            ],
        ],
        [
            'name' => 'Quản Lý Kho',
            'link' => '',
            'icon' => '<span class="menu-icon"><i class="ki-duotone ki-parcel fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></span>',
            'sub_menu' => [
                [
                    'name' => 'Nhập kho',
                    'link' => '',
                ],
                [
                    'name' => 'Danh sách quản lý kho',
                    'link' => '',
                ],
            ],
        ],
        [
            'name' => 'Cửa hàng',
            'link' => '',
            'icon' => '<span class="menu-icon"><i class="ki-duotone ki-shop fs-2"><span class="path1"></span><span class="path2"></span><span class="path3"></span><span class="path4"></span><span class="path5"></span></i></span>',
            'sub_menu' => [
                [
                    'name' => 'Thêm mới chi nhánh',
                    'link' => '',
                ],
                [
                    'name' => 'Danh sách chi nhánh',
                    'link' => '',
                ],
            ],
        ],
        [
            'name' => 'Khách hàng',
            'link' => '',
            'icon' => '<span class="menu-icon"><i class="ki-duotone ki-user fs-2"><span class="path1"></span><span class="path2"></span></i></span>',
            'sub_menu' => [
                [
                    'name' => 'Thêm mới khách hàng',
                    'link' => '',
                ],
                [
                    'name' => 'Danh sách khách hàng',
                    'link' => '',
                ],
            ],
        ],
        [
            'name' => 'Cấu hình hệ thống',
            'link' => '',
            'icon' => '<span class="menu-icon"><i class="ki-duotone ki-setting-2 fs-2"><span class="path1"></span><span class="path2"></span></i></span>'
        ],
    ];
@endphp
<div id="kt_app_sidebar" class="app-sidebar flex-column" data-kt-drawer="true" data-kt-drawer-name="app-sidebar" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_sidebar_mobile_toggle">
    <!--begin::Wrapper-->
    <div id="kt_app_sidebar_wrapper" class="app-sidebar-wrapper hover-scroll-y my-5 my-lg-2" data-kt-scroll="true" data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto" data-kt-scroll-dependencies="#kt_app_header" data-kt-scroll-wrappers="#kt_app_sidebar_wrapper" data-kt-scroll-offset="5px">
        <!--begin::Sidebar menu-->
        <div id="#kt_app_sidebar_menu" data-kt-menu="true" data-kt-menu-expand="false" class="app-sidebar-menu-primary menu menu-column menu-rounded menu-sub-indention menu-state-bullet-primary px-6 mb-5">
            @foreach($list_sidebar as $sidebar)
            <!--begin:Menu item-->
            <div data-kt-menu-trigger="click" class="menu-item here menu-accordion">
                @if(isset($sidebar['sub_menu']) && $sidebar['sub_menu'])
                <!--begin:Menu link-->
                <span class="menu-link">
                    {!!$sidebar['icon']!!}
                    <span class="menu-title">{{$sidebar['name']}}</span>
                    <span class="menu-arrow"></span>
                </span>
                <!--end:Menu link-->
                <!--begin:Menu sub-->
                <div class="menu-sub menu-sub-accordion">
                    @foreach($sidebar['sub_menu'] as $sub_menu)
                    <!--begin:Menu item-->
                    <div class="menu-item">
                        <!--begin:Menu link-->
                        <a class="menu-link{{url()->current() === $sub_menu['link'] ? ' active' : ''}}" href="{{$sub_menu['link']}}">
                            <span class="menu-bullet">
                                <span class="bullet bullet-dot"></span>
                            </span>
                            <span class="menu-title">{{$sub_menu['name']}}</span>
                        </a>
                        <!--end:Menu link-->
                    </div>
                    <!--end:Menu item-->
                    @endforeach
                </div>
                <!--end:Menu sub-->
                @else
                <!--begin:Menu link-->
                <div class="menu-item">
                    <a class="menu-link{{url()->current() === $sidebar['link'] ? ' active' : ''}}" href="{{$sidebar['link']}}">
                        {!!$sidebar['icon']!!}
                        <span class="menu-title">{{$sidebar['name']}}</span>
                    </a>
                </div>
                <!--end:Menu link-->
                @endif
            </div>
            <!--end:Menu item-->
            @endforeach
        </div>
        <!--end::Sidebar menu-->
    </div>
    <!--end::Wrapper-->
</div>
<!--end::Sidebar-->