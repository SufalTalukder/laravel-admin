<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function loadAuthView()
    {
        return view('templates.admin.auth-user');
    }

    public function loadAuthPermissionView()
    {
        return view('templates.admin.auth-permission');
    }
}
