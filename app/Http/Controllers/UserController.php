<?php

namespace App\Http\Controllers;

use A6digital\Image\Facades\DefaultProfileImage;
use App\Http\Requests\UserValidateRequest;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request) {
        $list_role = User::ROLE_TEXT;
        $current_user = auth()->user();

        $render_store = DB::table("stores");

        if($current_user->role === User::ROLE_ACCESS_PAGE['manage_producttion']) {
            $list_role = [];
            $list_role[User::ROLE_VALUE['manage_producttion']] = User::ROLE_TEXT[User::ROLE_VALUE['manage_producttion']];
            $list_role[User::ROLE_VALUE['staff_producttion']] = User::ROLE_TEXT[User::ROLE_VALUE['staff_producttion']];
            $render_store = $render_store->where("id", $current_user->store_id);
        } else if($current_user->role === User::ROLE_ACCESS_PAGE['manage_sale']){
            $list_role = [];
            $list_role[User::ROLE_VALUE['manage_sale']] = User::ROLE_TEXT[User::ROLE_VALUE['manage_sale']];
            $list_role[User::ROLE_VALUE['staff_sale']] = User::ROLE_TEXT[User::ROLE_VALUE['staff_sale']];
            $render_store = $render_store->where("id", $current_user->store_id);
        }

        $render_store = $render_store->selectRaw("id, name")->get();

        return view('admin.staff.index', [
            'list_role' => $list_role,
            'render_store' => $render_store,
        ]);
    }
    public function create(Request $request) {
        return view('admin.staff.create');
    }

    public function getData(Request $request) {
        $search = isset($request->search) && !empty($request->search) ? $request->search : "";
        $search = ltrim($search, '?');
        parse_str($search, $parsed);

        $current_user = auth()->user();

        $model = DB::table("users as u")
        ->leftJoin('users as u2', 'u.create_user_id', '=', 'u2.id')
        ->leftJoin('stores as s', 'u.store_id', '=', 's.id')
        ->selectRaw("u.full_name, u.email, u.phone, u.created_at, u.updated_at, u.id, u.role, u2.full_name as create_user_full_name, s.name as store_name");

        switch ($current_user->role) {
            case User::ROLE_ACCESS_PAGE['manage_producttion']:
                $model = $model->whereIn("u.role", [User::ROLE_VALUE['manage_producttion'], User::ROLE_VALUE['staff_producttion']]);
                break;
            case User::ROLE_ACCESS_PAGE['manage_sale']:
                $model = $model->whereIn("u.role", [User::ROLE_VALUE['manage_sale'], User::ROLE_VALUE['staff_sale']]);
                break;
            
            default:
                # code...
                break;
        }

        if(isset($parsed['search'])) {
            $model->where("u.full_name", "LIKE" , "%".trim($parsed['search'])."%");
        }
        if(isset($parsed['phone'])) {
            $model->where("u.phone", "LIKE" , "%".trim($parsed['phone'])."%");
        }

        $datatables = DataTables::of($model)
        ->addIndexColumn()
        ->addColumn(
            'action', function($user)use($current_user){
                $view_loading = view("admin._partials.loading");
                $action_edit = route('admin.staff.detail', ['id' => $user->id]);
                $action_delete = route('admin.staff.delete', ['id' => $user->id]);
                $edit = "<button class='btn btn-warning edit-record' data-action='{$action_edit}' data-record='{$user->id}'><i class='ri-edit-box-fill'></i>{$view_loading}</button>";
                $delete = "<button class='btn btn-danger remove-record' data-action='{$action_delete}' data-record='{$user->id}'><i class='ri-delete-bin-fill'></i></button>";

                if($current_user->role === User::ROLE_ACCESS_PAGE['admin'] &&$current_user->role === $user->role && $current_user->id === $user->id){
                    $delete = "";
                } else if(in_array($current_user->role, [User::ROLE_ACCESS_PAGE['manage_producttion'], User::ROLE_ACCESS_PAGE['manage_sale']]) && $current_user->role === $user->role){
                    $delete = "";
                }

                
                return "
                    <div class='button-list d-flex flex-wrap gap-1'>{$edit}{$delete}</div>
                ";
            }
        )
        ->editColumn('created_at', function($user){
            $created_at = $user->created_at ? date("d/m/Y", strtotime($user->created_at)) : '';
            
            return "
                <div>{$created_at}</div>
            ";
        })
        ->editColumn('updated_at', function($user){
            $updated_at = $user->updated_at ? date("d/m/Y", strtotime($user->updated_at)) : '';
            
            return "
               
                 <div>{$updated_at}</div>

            ";
        })
        ->addColumn('user_full_name', function($user){
           
            $user_action = is_null($user->create_user_full_name) ? 'Hệ thống tạo' : $user->create_user_full_name;
            return "
            
                 <div>{$user_action}</div>
            ";
        })
        ->editColumn('full_name', function($user){
                return "
                    <div>{$user->full_name}</div>
                ";
            }
        )
        ->editColumn('phone', function($user){
            return " <div><a href='tel:{$user->phone}'>{$user->phone}</a></div>";
        })
        ->editColumn('email', function($user){
            return " <div><a href='mailto:{$user->email}'>{$user->email}</a></div>";
        })
        ->editColumn('role', function($user){
            return " <div> ".User::ROLE_TEXT[$user->role]." </div>";
        })
        ->addColumn('store_name', function($user){
            $store_name = $user->role === User::ROLE_ACCESS_PAGE['admin'] ? 'Tất cả' : $user->store_name;
            return "<div>{$store_name}</div>";
        })
        ->rawColumns(['action', 'full_name', 'date_action', 'phone', 'email', 'role', 'store_name', 'created_at', 'updated_at', 'user_full_name']);

        return $datatables->toJson();
    }

    public function store(UserValidateRequest $request) {
        $validated = $request->validated();
        $validated['password'] = \Hash::make($validated['password']);
        $validated['created_at'] = date("Y-m-d H:i:s");
        $validated['updated_at'] = null;
        $validated['create_user_id'] = auth()->user()->id;
        $validated['store_id'] = 0;
        if($validated['role'] === User::ROLE_ACCESS_PAGE['admin']) {
            $validated['store_id'] = null;
        }

        $link = "profiles/".Str::slug($validated['full_name'])."_".strtotime(date("Ymd")).".png";
        $img = DefaultProfileImage::create($validated['full_name']);
        Storage::disk('public')->put("/$link", $img->encode());
        $validated['profile_default'] = $link;

        User::create($validated);
        
        return $this->successResponse([], 'Thêm mới thành công');
    }

    public function update($id, UserValidateRequest $request){
        $validated = $request->validated();
        if(is_null($validated['password'])) {
            unset($validated['password']);
        }else {
            $validated['password'] = \Hash::make($validated['password']);
        }
        $current_user = auth()->user();
        $data = User::where("id", $id);

        switch ($current_user->role) {
            case User::ROLE_ACCESS_PAGE['manage_producttion']:
               $data->whereIn("role", [User::ROLE_VALUE['manage_producttion'], User::ROLE_VALUE['staff_producttion']]);
                break;
            case User::ROLE_ACCESS_PAGE['manage_sale']:
               $data->whereIn("role", [User::ROLE_VALUE['manage_sale'], User::ROLE_VALUE['staff_sale']]);
                break;
            
            default:
                # code...
                break;
        }

        if($data->first()->email === $validated['email']) {
            unset($validated['email']);
        }
        if($data->first()->phone === $validated['phone']) {
            unset($validated['phone']);
        }

        if($data->first()->full_name !== $validated['full_name']) {
            $link = "profiles/".Str::slug($validated['full_name'])."_".strtotime(date("Ymd")).".png";
            $img = DefaultProfileImage::create($validated['full_name']);
            Storage::disk('public')->put("/$link", $img->encode());
            $validated['profile_default'] = $link;

            if (!is_null($data->first()->profile_default) && Storage::disk('public')->exists($data->first()->profile_default)) {
                Storage::disk('public')->delete($data->first()->profile_default);
            }
        }

        $validated['updated_at'] = date("Y-m-d H:i:s");

        $data->update($validated);

        return $this->successResponse([], 'Cập nhật thành công');
    }

    public function detail($id, Request $request){
        $current_user = auth()->user();
        $data = User::where("id", $id)
        ->selectRaw("id, full_name, email, phone, role, store_id");

        switch ($current_user->role) {
            case User::ROLE_ACCESS_PAGE['manage_producttion']:
               $data->whereIn("role", [User::ROLE_VALUE['manage_producttion'], User::ROLE_VALUE['staff_producttion']]);
                break;
            case User::ROLE_ACCESS_PAGE['manage_sale']:
               $data->whereIn("role", [User::ROLE_VALUE['manage_sale'], User::ROLE_VALUE['staff_sale']]);
                break;
            
            default:
                # code...
                break;
        }

        $data = $data->first();

        if(!$data) {
            return $this->errorResponse('Không tìm thấy dữ liệu');
        }
        
        $data_append['hide_password_field'] = false;

        if(($data->role === User::ROLE_VALUE['manage_producttion'] || $data->role === User::ROLE_VALUE['manage_sale']) && $current_user->id !== $data->id) {
            $data_append['hide_password_field'] = true;
        }

        if($current_user->role === User::ROLE_ACCESS_PAGE['admin']) {
            $data_append['hide_password_field'] = false;
        }

        return $this->successResponse($data, 'Lấy dữ liệu thành công', $data_append);
    }

    public function delete($id, Request $request) {
        $current_user = auth()->user();
        $data = User::where("id", $id);

        switch ($current_user->role) {
            case User::ROLE_ACCESS_PAGE['manage_producttion']:
               $data->whereIn("role", [User::ROLE_VALUE['manage_producttion'], User::ROLE_VALUE['staff_producttion']]);
                break;
            case User::ROLE_ACCESS_PAGE['manage_sale']:
               $data->whereIn("role", [User::ROLE_VALUE['manage_sale'], User::ROLE_VALUE['staff_sale']]);
                break;
            
            default:
                # code...
                break;
        }

        if(!$data->first()) {
            return $this->errorResponse('Không tìm thấy dữ liệu');
        }

        $data->delete();
        return $this->successResponse([], 'Xoá thành công');
    }
}
