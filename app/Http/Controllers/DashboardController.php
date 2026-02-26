<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function loadDashboardView(Request $request)
    {
        $token = $request->cookie('auth_token');
        $authUser = Auth::user();

        return view('templates.admin.dashboard', [
            'authToken' => $token,
            'authData' => $authUser
        ]);
    }
}
