<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request) {
        return view('admin.staff.index');
    }
    public function create(Request $request) {
        return view('admin.staff.create');
    }
}
