<?php

namespace App\Http\Controllers;

use App\Services\Shipping\Ghtk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CallBackController extends Controller
{
    public function callbackGhn(Request $request) {
        Log::info('Webhook GHN nhận được:', $request->all());

        return $this->successResponse([], 'Ok');
    }

    public function callbackGhtk(Request $request) {
        Log::info('Webhook GHTK nhận được:', $request->all());
        $data = $request->all();

        $data = (object)[
            'label_id' => "S22945623.SG02-B12.1250028746",
            'partner_id' => "DH-SX2BQB0GPC",
            'status_id' => 2,
            'action_time' => "2025-07-09T15:05:44+07:00",
            'reason_code' => null,
            'reason' => null,
            'weight' => 1,
            'fee' => 12000,
            'pick_money' => 0,
            'return_part_package' => 0,
            'return_fee' => 0
        ];

        $find_order = DB::table("orders")
        ->join('order_shipments', 'orders.id', '=', 'order_shipments.order_id')
        ->where("orders.code", $data->partner_id)
        ->where("order_shipments.tracking_number", $data->label_id)
        ->selectRaw('orders.id as order_id, orders.shipping_fee_payer, order_shipments.shipping_fee, order_shipments.id as order_shipment_id')->first();

        $data_update_order = [
            'updated_at' => date("Y-m-d H:i:s"),
            'status' => $data->status_id,
        ];

        DB::table("orders")->where("id", $find_order->order_id)
        ->update($data_update_order);

        $data_update_order_shipment = [
            'current_status' => $data->status_id,
            'shipping_fee' => $data->fee,
            'weight' => $data->weight,
            'updated_at' => date("Y-m-d H:i:s"),
        ];

        DB::table("order_shipments")
        ->where("shipping_partner_id", -2)
        ->where("tracking_number", $data->label_id)
        ->update($data_update_order_shipment);

        DB::table("order_shipment_status_logs")->insert([
            'order_shipment_id' => $find_order->order_shipment_id,
            'status_code' => $data->status_id,
            'status_text' => Ghtk::STATUS_ORDER[$data->status_id],
            'status_time' => date("Y-m-d H:i:s"),
            'note' => $data->reason,
            'raw_payload' => json_encode($data),
            'created_at' => date("Y-m-d H:i:s"),
        ]);

        return $this->successResponse([], 'Ok');
    }
}
