<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function loadSystemActivityView()
    {
        return view('templates.admin.system-activity');
    }
}
