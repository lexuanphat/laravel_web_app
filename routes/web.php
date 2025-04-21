<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ShopController;
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

    $route->middleware(['auth'])->group(function($route){
        // Thống kê tổng quan
        $route->get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Quản lý sản phẩm
        $route->get('/product', [AdminDashboardController::class, 'index'])->name('product');
        $route->get('/product/create', [AdminDashboardController::class, 'index'])->name('product.create');

        // Quản lý nhân viến
        $route->get('/staff', [UserController::class, 'index'])->name('staff');
        $route->get('/staff/create', [UserController::class, 'create'])->name('staff.create');

        // Quản lý cửa hàng
        $route->get('/shop', [ShopController::class, 'index'])->name('shop');
        $route->get('/shop/getData', [ShopController::class, 'getData'])->name('shop.get_data');
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
    });
});

Route::fallback(function () {
   return view('admin.page-error.404');
});
