<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuthModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Auth Login
    public function authUserLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'auth_user_email'    => 'required|email',
            'auth_user_password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'Unprocessable Content',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $authUser = AuthModel::where('auth_user_email', $request->auth_user_email)->first();

        if (!$authUser || !Hash::check($request->auth_user_password, $authUser->auth_user_password)) {
            return response()->json([
                'status'  => 'Unauthorized',
                'message' => 'Invalid email or password.',
            ], 401);
        }

        $token = JWTAuth::fromUser($authUser);

        $cookie = cookie(
            'accessToken',
            $token,
            60 * 24,
            '/',
            null,
            config('app.env') === 'production',
            true,
            false,
            'Strict'
        );

        return response()->json([
            'status'        => 'Success',
            'message'       => 'Login successfully.',
            'accessToken'   => $token,
        ], 200)->withCookie($cookie);
    }

    // Create / Update Auth User
    public function createOrUpdateAuthUser(Request $request)
    {
        $actionByAuthUserId = $request->jwt_auth_user_id;

        $authUserId = $request->input('auth_user_id');
        $isUpdate   = !empty($authUserId);

        $emailUnique = $isUpdate
            ? "unique:auth_tbl,auth_user_email,{$authUserId},auth_user_id"
            : 'unique:auth_tbl,auth_user_email';

        $rules = [
            'auth_user_id'           => 'nullable|integer|exists:auth_tbl,auth_user_id',
            'auth_user_name'         => 'required|string|max:100',
            'auth_user_email'        => "required|email|max:50|{$emailUnique}",
            'auth_user_phone_number' => 'required|digits:10',
            'auth_user_type'         => 'required|in:SUPER_ADMIN,ADMIN',
            'auth_user_status'       => 'required|in:YES,NO',
            'auth_user_image'        => 'nullable|string|max:2048',
            'auth_user_password'     => $isUpdate
                ? 'nullable|string|min:8|max:50'
                : 'required|string|min:8|max:50',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'Unprocessable Content',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data = [
            'auth_user_name'         => $request->auth_user_name,
            'auth_user_email'        => $request->auth_user_email,
            'auth_user_phone_number' => $request->auth_user_phone_number,
            'auth_user_type'         => $request->auth_user_type,
            'auth_user_status'       => $request->auth_user_status,
            'auth_user_image'        => $request->auth_user_image,
            'action_by_user_id'      => $actionByAuthUserId,
        ];

        if ($request->filled('auth_user_password')) {
            $data['auth_user_password'] = Hash::make($request->auth_user_password);
        }

        if ($isUpdate) {
            AuthModel::where('auth_user_id', $authUserId)->update($data);
            $message = 'Auth user updated successfully.';
        } else {
            AuthModel::create($data);
            $message = 'Auth user created successfully.';
        }

        return response()->json([
            'status'  => 'Success',
            'message' => $message,
        ], 200);
    }

    // Fetch Auth Details
    public function fetchAuthUserDetails(Request $request)
    {
        $authUserId   = $request->jwt_auth_user_id;
        $authUserType = $request->jwt_auth_user_type;

        $authUser = AuthModel::where('auth_user_id', $authUserId)->first();

        if (!$authUser) {
            return response()->json([
                'status'  => 'Not Found',
                'message' => 'Auth user not found.',
            ], 404);
        }

        return response()->json([
            'status'  => 'Success',
            'message' => 'Auth user details fetched.',
            'content' => [
                'auth_user_id'           => $authUser->auth_user_id,
                'auth_user_name'         => $authUser->auth_user_name,
                'auth_user_email'        => $authUser->auth_user_email,
                'auth_user_phone_number' => $authUser->auth_user_phone_number,
                'auth_user_type'         => $authUser->auth_user_type,
                'auth_user_status'       => $authUser->auth_user_status,
                'auth_user_image'        => $authUser->auth_user_image,
            ],
        ], 200);
    }

    // Fetch All Auth Users List
    public function fetchAuthUsersList()
    {
        $authUsersList = AuthModel::from('auth_tbl AS a')
            ->leftJoin('auth_tbl AS b', 'a.action_by_user_id', '=', 'b.auth_user_id')
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
                'b.auth_user_name as actionByAuthUser'
            )
            ->orderBy('a.created_at', 'DESC')
            ->get();

        if (!$authUsersList) {
            return response()->json([
                'status'    => 'Not Found',
                'message'   => 'Auth user(s) list not found.',
                'content'   => null
            ], 404);
        }

        return response()->json([
            'status'    => 'Success',
            'message'   => 'Auth user(s) list fetched successfully.',
            'content'   => $authUsersList
        ], 200);
    }

    // Delete Auth User
    public function deleteAuthUser(Request $request)
    {
        $deleteByAuthUserId = $request->input('auth_user_id');
        $findAuthUserIfExists = AuthModel::where('auth_user_id', $deleteByAuthUserId)->first();

        if (!$findAuthUserIfExists) {
            return response()->json([
                'status'    => 'Not Found',
                'message'   => 'Auth user not found.'
            ], 404);
        }

        AuthModel::where('auth_user_id', $deleteByAuthUserId)->delete();

        return response()->json([
            'status'    => 'Success',
            'message'   => 'Auth user deleted successfully.'
        ], 200);
    }

    // Upload Auth User Image
    public function uploadImage(Request $request)
    {
        $authUserId = $request->input('auth_user_id');
        $isUpdate   = !empty($authUserId);

        if ($request->hasFile('auth_user_image')) {
            // Delete old image if updating
            if ($isUpdate) {
                $existingUser = AuthModel::find($authUserId);
                if ($existingUser && $existingUser->auth_user_image) {
                    $oldImagePath = public_path('vendor/upload/' . $existingUser->auth_user_image);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }

            $image     = $request->file('auth_user_image');
            $imageName = time() . '_' . preg_replace('/\s+/', '_', $image->getClientOriginalName());
            $image->move(public_path('vendor/upload/auth'), $imageName);
            $data['auth_user_image'] = $imageName;

            if ($isUpdate) {
                AuthModel::where('auth_user_id', $authUserId)->update($data);
                $message = 'Auth user image updated successfully.';
            } else {
                AuthModel::create($data);
                $message = 'Auth user image created successfully.';
            }

            return response()->json([
                'status'            => 'Success',
                'message'           => $message,
                'auth_user_image'   => $imageName
            ], 200);
        }
    }
}
