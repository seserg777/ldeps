<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class OrderAdminController extends Controller
{
    public function index()
    {
        return view('admin.orders.index');
    }
}


