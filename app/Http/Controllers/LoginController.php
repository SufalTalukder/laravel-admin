<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function loadLoginView()
    {
        return view('templates.admin.login');
    }

    public function loadRegisterView()
    {
        return view('templates.admin.register');
    }
}
