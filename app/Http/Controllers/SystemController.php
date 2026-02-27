<?php

namespace App\Http\Controllers;

use App\Models\SystemActivityModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SystemController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            try {
                $this->authCookieCheck();
            } catch (\Throwable $e) {
                Log::error($e);
            }
            return $next($request);
        });
    }

    public function loadSystemActivityView(Request $request, $id = null)
    {
        $authUser = Auth::user();
        $systemActivities = SystemActivityModel::latest()->get();
        $selectedActivity = null;

        if ($id) {
            $selectedActivity = SystemActivityModel::find($id);
        }

        return view('templates.admin.system-activity', [
            'authData' => $authUser,
            'systemActivitiesList' => $systemActivities,
            'selectedActivity' => $selectedActivity
        ]);
    }
}
