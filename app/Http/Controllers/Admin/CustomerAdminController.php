<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class CustomerAdminController extends Controller
{
    public function index()
    {
        return view('admin.customers.index');
    }
}


