<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(){
        $current_date = date("Y-m-08");

        $start_month_current = date("Y-m-01");
        $end_month_current = date("Y-m-t");

        $start_month_before = date("Y-m-01", strtotime("-1 months"));
        $end_month_before = date("Y-m-t", strtotime("-1 months"));

        $total_customer = DB::table('customers')
        ->whereBetween('created_at', ["{$start_month_current} 00:00:00", "{$end_month_current} 23:59:59"])
        ->count();

        $total_order = DB::table('orders')
        ->whereBetween('created_at', ["{$start_month_current} 00:00:00", "{$end_month_current} 23:59:59"])
        ->selectRaw("
            COUNT(*) AS cnt_order, 
            SUM(IFNULL(total_amount, 0)) AS total_amount,
            SUM(IFNULL(paid_amount, 0)) AS paid_amount
        ")
        ->first();

        $order_latest = DB::table('orders')
        ->join('customers', 'orders.customer_id', '=', 'customers.id')
        ->whereBetween('orders.created_at', ["{$current_date} 00:00:00", "{$current_date} 23:59:59"])
        ->selectRaw("
            orders.*, customers.full_name as customer_full_name        
        ")
        ->get();

        $customer_top_order = DB::table('orders')
        ->join('customers', 'orders.customer_id', '=', 'customers.id')
        ->selectRaw("
            orders.customer_id,
            customers.full_name as customer_full_name,
            SUM(orders.paid_amount) as sum_paid_amount,
            COUNT(orders.id) as cnt_order
        ")
        ->groupBy([
            'orders.customer_id',
            'customers.full_name'
        ])
        ->havingRaw("
            sum_paid_amount > 0
        ")
        ->orderByRaw('sum_paid_amount DESC')
        ->limit(10)
        ->get();

        return view('admin.dashboard.index', [
            'total_customer' => $total_customer,
            'total_order' => $total_order,
            'order_latest' => $order_latest,
            'customer_top_order' => $customer_top_order,
        ]);
    }
}
