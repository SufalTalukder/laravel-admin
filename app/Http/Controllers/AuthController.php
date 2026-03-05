<?php

namespace App\Http\Controllers;

use App\Models\AuthModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
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

    // LOAD PAGE VIEW
    public function index()
    {
        $authUser = Auth::user();
        return view('templates.admin.auth-user', [
            'authData' => $authUser,
        ]);
    }

    // GET ALL
    public function fetchAllAuthUsers(Request $request)
    {
        $auth_user_type = $request->input('auth_user_type', []);

        if (!is_array($auth_user_type)) {
            $auth_user_type = [$auth_user_type];
        }

        $query = AuthModel::from('auth_tbl as a')
            ->leftJoin('auth_tbl as b', 'a.action_by_user_id', '=', 'b.auth_user_id')
            ->select(
                'a.auth_user_id',
                'a.auth_user_name',
                'a.auth_user_email',
                'a.auth_user_phone_number',
                'a.auth_user_type',
                'a.auth_user_status',
                'a.auth_user_image',
                'a.created_at',
                'a.updated_at',
                'b.auth_user_name as actionByName'
            );

        if (!empty($auth_user_type) && !in_array('all', $auth_user_type)) {
            $query->whereIn('a.auth_user_type', $auth_user_type);
        }

        $authUsers = $query->orderBy('a.created_at', 'desc')->get();

        return response()->json([
            'authUsersList' => $authUsers,
        ]);
    }

    // GET
    public function fetchAuthUserDetails($id)
    {
        $authUser = AuthModel::find($id);

        if (!$authUser) {
            return response()->json(['status' => false, 'message' => 'User not found.'], 404);
        }

        return response()->json([
            'status'   => true,
            'authUser' => $authUser,
        ]);
    }

    // CREATE / UPDATE
    public function createAndUpdateAuthUser(Request $request)
    {
        $authUserId = $request->input('auth_user_id');
        $isUpdate   = !empty($authUserId);

        $emailUnique = 'unique:auth_tbl,auth_user_email';
        if ($isUpdate) {
            $emailUnique .= ',' . $authUserId . ',auth_user_id';
        }

        $rules = [
            'auth_user_name'         => 'required|string|max:100',
            'auth_user_email'        => "required|email|max:50|{$emailUnique}",
            'auth_user_phone_number' => 'required|digits:10',
            'auth_user_type'         => 'required|in:SUPER_ADMIN,ADMIN',
            'auth_user_status'       => 'required|in:YES,NO',
            'auth_user_image'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];

        $rules['auth_user_password'] = $isUpdate
            ? 'nullable|string|min:8|max:50'
            : 'required|string|min:8|max:50';

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $loggedInUser = Auth::user();

        $data = [
            'auth_user_name'         => $request->auth_user_name,
            'auth_user_email'        => $request->auth_user_email,
            'auth_user_phone_number' => $request->auth_user_phone_number,
            'auth_user_type'         => $request->auth_user_type,
            'auth_user_status'       => $request->auth_user_status,
            'action_by_user_id'      => $loggedInUser->auth_user_id,
        ];

        // Hash password
        if ($request->filled('auth_user_password')) {
            $data['auth_user_password'] = Hash::make($request->auth_user_password);
        }

        // Handle image upload
        if ($request->hasFile('auth_user_image')) {
            $image     = $request->file('auth_user_image');
            $imageName = time() . '_' . preg_replace('/\s+/', '_', $image->getClientOriginalName());
            $image->move(public_path('vendor/upload'), $imageName);
            $data['auth_user_image'] = $imageName;
        }

        if ($isUpdate) {
            AuthModel::where('auth_user_id', $authUserId)->update($data);
            $message = 'Auth user updated successfully.';
        } else {
            AuthModel::create($data);
            $message = 'Auth user created successfully.';
        }

        return response()->json([
            'status'  => true,
            'message' => $message,
        ]);
    }

    // DELETE / DELETE ALL
    public function deleteMultipleAuthUser(Request $request)
    {
        $dlt_ids = $request->input('dlt_ids', []);

        if (!is_array($dlt_ids)) {
            $dlt_ids = [$dlt_ids];
        }

        if (empty($dlt_ids)) {
            return response()->json(['message' => 'No records selected.'], 400);
        }

        // Prevent the logged-in user from deleting their own account
        $loggedInUser = Auth::user();
        if (in_array($loggedInUser->auth_user_id, $dlt_ids)) {
            return response()->json([
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        AuthModel::whereIn('auth_user_id', $dlt_ids)->delete();

        return response()->json([
            'message' => 'Selected records deleted successfully.',
        ]);
    }

    // PERMISSION VIEW PAGE
    public function loadAuthPermissionView()
    {
        return view('templates.admin.auth-permission');
    }
}
