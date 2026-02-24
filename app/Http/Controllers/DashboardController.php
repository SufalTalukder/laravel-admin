<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function loadDashboardView()
    {
        return view('templates.admin.dashboard');
    }
}
