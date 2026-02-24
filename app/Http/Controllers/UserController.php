<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function loadUserView()
    {
        return view('templates.admin.user');
    }
}
