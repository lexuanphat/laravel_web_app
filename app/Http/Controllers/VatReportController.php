<?php

namespace App\Http\Controllers;

use App\Http\Requests\VatReportRequest;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;

class VatReportController extends Controller
{
    public function index(){
        $data_vats = DB::table('vats')->get();
        $data_type_report = DB::table('list_type_report')->get();
        return view('admin.vat_report.index', [
            'data_vats' => $data_vats,
            'data_type_report' => $data_type_report,
        ]);
    }

    public function generateCode(){
        // Định dạng: DG + Năm tháng (2603) + Số thứ tự (001)
        $prefix = 'DG' . date('ym'); 
        
        // Lấy bản ghi cuối cùng có prefix tương tự trong tháng này
        $lastRecord = DB::table('vat_quality_reports')->where('code', 'like', $prefix . '%')
                        ->orderBy('code', 'desc')
                        ->first();

        if (!$lastRecord) {
            $number = 1;
        } else {
            $lastNumber = (int) substr($lastRecord->code, strlen($prefix));
            $number = $lastNumber + 1;
        }

        return $prefix . str_pad($number, 3, '0', STR_PAD_LEFT);
    }

    public function getData(Request $request) {
        $query_builder = DB::table('vat_quality_reports')
        ->leftJoin('users', 'vat_quality_reports.staff_id', '=', 'users.id')
        ->leftJoin('vats', 'vat_quality_reports.vat_id', '=', 'vats.id');

        $data_list_type_report = DB::table('list_type_report')->get();
        $query_builder->selectRaw("
            vat_quality_reports.*,
            users.full_name as user_full_name,
            vats.code as vat_code
        ")->orderBy('vat_quality_reports.id', 'desc');

        $datatables = DataTables::of($query_builder)
        ->addIndexColumn()
        ->addColumn('chi_tieu_dg', function($item)use($data_list_type_report){
            return "
                <div class='d-flex flex-column'>
                    <span>Độ đạm: {$data_list_type_report->where('type_report', 'protein_level')->where('id', '=', $item->protein_level)->first()->name}</span>
                    <span>Nồng độ muối: {$data_list_type_report->where('type_report', 'salt_level')->where('id', '=', $item->salt_level)->first()->name}</span>
                    <span>Histamin: {$data_list_type_report->where('type_report', 'histamine_level')->where('id', '=', $item->histamine_level)->first()->name}</span>
                    <span>Admin: {$data_list_type_report->where('type_report', 'acid_level')->where('id', '=', $item->acid_level)->first()->name}</span>
                    <span>Amon: {$data_list_type_report->where('type_report', 'amon_level')->where('id', '=', $item->amon_level)->first()->name}</span>
                    <span>Màu sắc: {$data_list_type_report->where('type_report', 'color')->where('id', '=', $item->color)->first()->name}</span>
                </div>
            ";
        })
        ->editColumn('evaluation_date', function($item){
            return date("d/m/Y H:i", strtotime($item->evaluation_date));
        })
        ->addColumn(
            'action', function($item){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.vat_report.detail', ['id' => $item->id]);
                $action_delete = route('admin.vat_report.delete', ['id' => $item->id]);
                return "
                    <div class='button-list'>
                        <button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$item->id}'><i class='ri-edit-box-fill fs-5'></i>{$view_loading}</button>
                        <button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$item->id}'><i class='ri-delete-bin-fill fs-5'></i></button>
                    </div>
                ";
            }
        )
        ->rawColumns(['action', 'chi_tieu_dg']);
        return $datatables->toJson();
    }

    public function store(VatReportRequest $request) {
        $data = $request->validated();
        $data['evaluation_date'] = date("Y-m-d H:i:s");
        $data['staff_id'] = auth()->id();
        $data['code'] = $this->generateCode();

        DB::table('vat_quality_reports')->insert($data);

        return $this->successResponse([], 'Thêm mới thành công');
    }

    public function detail($id, Request $request){
        $data = DB::table('vat_quality_reports')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        return $this->successResponse($data, 'Thành công');
    }

    public function update($id, VatReportRequest $request) {
        $data = DB::table('vat_quality_reports')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        $data_update = $request->validated();
        $data_update['evaluation_date'] = date("Y-m-d H:i:s");
        $data_update['staff_id'] = auth()->id();

        DB::table('vat_quality_reports')->where('id', $id)->update($data_update);

        return $this->successResponse($data, 'Cập nhật thành công');
    }

    public function delete($id){
        $data = DB::table('vat_quality_reports')->where('id', $id)->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu', 404);
        }

        DB::table('vat_quality_reports')->delete($id);

        return $this->successResponse($data, 'Đã xoá dữ liệu');
    }
}
