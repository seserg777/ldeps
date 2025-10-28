<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ManufacturerAdminController extends Controller
{
    public function index()
    {
        return view('admin.manufacturers.index');
    }
}


