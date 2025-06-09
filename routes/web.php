<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\TokenTransportController;
use App\Http\Controllers\TransportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'prefix' => 'admin',
    'as' => 'admin.',
    'middleware' => ['localization']
], function($route){

    Auth::routes();

    $route->post('login', [LoginController::class, 'login'])->name('login.post');

    $route->middleware(['auth', 'check_transport_init'])->group(function($route){
        // Thống kê tổng quan
        $route->get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Quản lý đơn hàng
        $route->get('/order', [OrderController::class, 'index'])->name('order');
        $route->get('/order/getData', [OrderController::class, 'getData'])->name('order.get_data');
        $route->get('/order/detail/{id}', [OrderController::class, 'detail'])->name('order.detail');
        $route->get('/order/create', [OrderController::class, 'create'])->name('order.create');
        $route->get('/order/getDataCustomer', [OrderController::class, 'getDataCustomer'])->name('order.get_data_customer');
        $route->get('/order/getDataProduct', [OrderController::class, 'getDataProduct'])->name('order.get_data_product');
        $route->post('/order/apiGetFee', [OrderController::class, 'apiGetFee'])->name('order.api_get_fee');
        $route->post('/order/createOrder', [OrderController::class, 'createOrder'])->name('order.create_order');

        // Quản lý danh mục sản phẩm
        $route->get('/category', [CategoryController::class, 'index'])->name('category');
        $route->get('/category/getData', [CategoryController::class, 'getData'])->name('category.get_data');
        $route->post('/category/store', [CategoryController::class, 'store'])->name('category.store');
        $route->get('/category/detail/{id}', [CategoryController::class, 'detail'])->name('category.detail');
        $route->put('/category/update/{id}', [CategoryController::class, 'update'])->name('category.update');
        $route->delete('/category/delete/{id}', [CategoryController::class, 'delete'])->name('category.delete');

        // Quản lý danh mục vận chuyển
        $route->get('/transport', [TransportController::class, 'index'])->name('transport');
        $route->get('/transport/getData', [TransportController::class, 'getData'])->name('transport.get_data');
        $route->post('/transport/store', [TransportController::class, 'store'])->name('transport.store');
        $route->get('/transport/detail/{id}', [TransportController::class, 'detail'])->name('transport.detail');
        $route->put('/transport/update/{id}', [TransportController::class, 'update'])->name('transport.update');
        $route->delete('/transport/delete/{id}', [TransportController::class, 'delete'])->name('transport.delete');

        // Quản lý sản phẩm
        $route->get('/product', [ProductController::class, 'index'])->name('product');
        $route->get('/product/getData', [ProductController::class, 'getData'])->name('product.get_data');
        $route->post('/product/store', [ProductController::class, 'store'])->name('product.store');
        $route->get('/product/detail/{id}', [ProductController::class, 'detail'])->name('product.detail');
        $route->put('/product/update/{id}', [ProductController::class, 'update'])->name('product.update');
        $route->delete('/product/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');
        $route->get('/product/getDataCategory', [ProductController::class, 'getDataCategory'])->name('product.get_data_category');

        // Quản lý kho
        $route->get('/product-stock', [ProductStockController::class, 'index'])->name('product_stock');
        $route->get('/product-stock/getData', [ProductStockController::class, 'getData'])->name('product_stock.get_data');
        $route->get('/product-stock/getDataProduct', [ProductStockController::class, 'getDataProduct'])->name('product_stock.get_data_product');
        $route->post('/product-stock/store', [ProductStockController::class, 'store'])->name('product_stock.store');
        $route->delete('/product-stock/delete/{id}', [ProductStockController::class, 'delete'])->name('product_stock.delete');

        // Quản lý nhân viến
        $route->get('/staff', [UserController::class, 'index'])->name('staff');
        $route->get('/staff/create', [UserController::class, 'create'])->name('staff.create');

        // Quản lý cửa hàng
        $route->get('/shop', [ShopController::class, 'index'])->name('shop');
        $route->get('/shop/getData', [ShopController::class, 'getData'])->name('shop.get_data');
        $route->post('/shop/async_store_transport', [ShopController::class, 'AsyncStoreTransport'])->name('shop.async_store_transport');
        $route->post('/shop/store', [ShopController::class, 'store'])->name('shop.store');
        $route->get('/shop/detail/{id}', [ShopController::class, 'detail'])->name('shop.detail');
        $route->put('/shop/update/{id}', [ShopController::class, 'update'])->name('shop.update');
        $route->delete('/shop/delete/{id}', [ShopController::class, 'delete'])->name('shop.delete');

        // Quản lý khách hàng
        $route->get('/customer', [CustomerController::class, 'index'])->name('customer');
        $route->get('/customer/getData', [CustomerController::class, 'getData'])->name('customer.get_data');
        $route->post('/customer/store', [CustomerController::class, 'store'])->name('customer.store');
        $route->get('/customer/detail/{id}', [CustomerController::class, 'detail'])->name('customer.detail');
        $route->put('/customer/update/{id}', [CustomerController::class, 'update'])->name('customer.update');
        $route->delete('/customer/delete/{id}', [CustomerController::class, 'delete'])->name('customer.delete');

        // Quản lý token DVVC
        $route->get('/token-transport', [TokenTransportController::class, 'index'])->withoutMiddleware('check_transport_init')->name('token_transport');
        $route->post('/token-transport/store', [TokenTransportController::class, 'store'])->withoutMiddleware('check_transport_init')->name('token_transport.store');

    });
});

Route::fallback(function () {
    if(auth()->check()) {
        return view('admin.page-error.404');
    }
    // comment
    return redirect()->route('admin.login');
});
