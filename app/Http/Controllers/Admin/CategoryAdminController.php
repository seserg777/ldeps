<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class CategoryAdminController extends Controller
{
    public function index()
    {
        return view('admin.categories.index');
    }
}


