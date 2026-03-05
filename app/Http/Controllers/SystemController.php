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

    // Load view
    public function index()
    {
        $authUser = Auth::user();
        return view('templates.admin.system-activity', [
            'authData' => $authUser
        ]);
    }

    // Load activites with filterations
    public function fetchSystemActivities(Request $request)
    {
        $login_status = $request->input('login_status', []);

        if (!is_array($login_status)) {
            $login_status = [$login_status];
        }

        $query = SystemActivityModel::query();

        if (!empty($login_status) && !in_array('all', $login_status)) {
            $query->whereIn('login_status', $login_status);
        }

        $systemActivities = $query->latest()->get();

        return response()->json([
            'systemActivitiesList' => $systemActivities
        ]);
    }

    // Delete activites
    public function deleteSystemActivities(Request $request)
    {
        $dlt_ids = $request->input('dlt_ids', []);

        if (!is_array($dlt_ids)) {
            $dlt_ids = [$dlt_ids];
        }

        if (empty($dlt_ids)) {
            return response()->json([
                'message' => 'No records selected.'
            ], 400);
        }

        SystemActivityModel::whereIn('auth_login_audit_id', $dlt_ids)->delete();

        return response()->json([
            'message' => 'Selected records deleted successfully.'
        ]);
    }
}
