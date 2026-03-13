<?php

namespace App\Http\Controllers;

use App\Models\AuthModel;
use App\Models\AuthPermissionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    // AUTH USERS
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

    // AUTH PERMISSION
    // LOAD PAGE VIEW
    public function loadAuthPermissionView()
    {
        $authUser = Auth::user();
        return view('templates.admin.auth-permission', [
            'authData' => $authUser
        ]);
    }

    public function fetchAllAuthPermission()
    {
        $authUsers = AuthModel::from('auth_tbl AS a')
            ->select(
                'a.auth_user_id',
                'a.auth_user_name',
                'a.auth_user_image',
                'p.add_permission',
                'p.view_all_permission',
                'p.view_permission',
                'p.edit_permission',
                'p.delete_permission',
                'p.auth_permission_id',
                'ab.auth_user_name AS actionByName'
            )
            ->leftJoin('auth_permission_tbl AS p', 'p.auth_user_id', '=', 'a.auth_user_id')
            ->leftJoin('auth_tbl AS ab', 'ab.auth_user_id', '=', 'p.action_by_user_id')
            ->where('a.auth_user_status', 'YES')
            ->get();

        return response()->json([
            'authUsersList' => $authUsers
        ]);
    }

    public function storeAuthPermission(Request $request)
    {
        $authUser = Auth::user();
        $request->validate([
            'auth_user_id'          => 'required|exists:auth_tbl,auth_user_id',
            'add_permission'        => 'required|in:YES,NO',
            'view_all_permission'   => 'required|in:YES,NO',
            'view_permission'       => 'required|in:YES,NO',
            'edit_permission'       => 'required|in:YES,NO',
            'delete_permission'     => 'required|in:YES,NO',
        ]);

        $actionBy = $authUser->auth_user_id;
        $auth_permission_id = $request->auth_permission_id;

        $data = [
            'auth_user_id'        => $request->auth_user_id,
            'add_permission'      => $request->add_permission,
            'view_all_permission' => $request->view_all_permission,
            'view_permission'     => $request->view_permission,
            'edit_permission'     => $request->edit_permission,
            'delete_permission'   => $request->delete_permission,
            'action_by_user_id'   => $actionBy,
        ];

        if ($auth_permission_id) {
            AuthPermissionModel::where('auth_permission_id', $auth_permission_id)->update($data);
            $message = 'Permissions modified successfully.';
        } else {
            AuthPermissionModel::create($data);
            $message = 'Permissions modified successfully.';
        }

        return response()->json([
            'message' => $message
        ]);
    }
}
