<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function loadActivityView()
    {
        return view('templates.admin.your-activity');
    }
}
