<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DeviceController extends Controller
{
    public function index()
    {
        return view('admin.devices');
    }
}
