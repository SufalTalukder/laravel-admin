<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuthModel;
use App\Models\SystemActivityModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use Stevebauman\Location\Facades\Location;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // Auth Register
    public function authUserRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'auth_user_name'     => 'required|string|max:100',
            'auth_user_email'    => 'required|email|max:50|unique:auth_tbl,auth_user_email',
            'auth_user_password' => 'required|string|min:8|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'Unprocessable Content',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        try {
            $data = [
                'auth_user_name'     => $request->auth_user_name,
                'auth_user_email'    => $request->auth_user_email,
                'auth_user_password' => bcrypt($request->auth_user_password),
                'auth_user_status'   => 'NO',
                'auth_user_type'     => 'ADMIN',
            ];

            $authUser = AuthModel::create($data);

            $agent      = new Agent();
            $ip         = $request->ip();
            $location   = Location::get($ip);

            // Prepare common audit data
            $auditData = [
                'auth_method'        => 'email_password',
                'browser'            => $agent->browser(),
                'browser_version'    => $agent->version($agent->browser()),
                'device_type'        => $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop'),
                'device_model'       => $agent->device(),
                'ip_address'         => $ip,
                'operating_system'   => $agent->platform(),
                'os_version'         => $agent->version($agent->platform()),
                'user_agent'         => $request->userAgent(),
                'possible_incognito' => false,
                'referrer_url'       => $request->headers->get('referer'),
                'session_id'         => session()->getId(),
                'login_time'         => now()
            ];

            $geoData = [
                'country' => optional($location)->countryName,
                'state'   => optional($location)->regionName,
                'city'    => optional($location)->cityName,
                'lat'     => optional($location)->latitude,
                'long'    => optional($location)->longitude,
                'address' => optional($location)->cityName
                    ? optional($location)->cityName . ', ' .
                    optional($location)->regionName . ', ' .
                    optional($location)->countryName
                    : null,
            ];

            SystemActivityModel::create(array_merge($auditData, $geoData, [
                'auth_user_id' => $authUser->auth_user_id,
                'login_status' => 'Success',
                'failure_reason' => null
            ]));

            return response()->json([
                'status'  => 'Success',
                'message' => 'Auth user registered successfully.',
            ], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'Registration failed due to a database error.',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

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

        $agent      = new Agent();
        $ip         = $request->ip();
        $location   = Location::get($ip);

        // Prepare common audit data
        $auditData = [
            'auth_method'        => 'email_password',
            'browser'            => $agent->browser(),
            'browser_version'    => $agent->version($agent->browser()),
            'device_type'        => $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop'),
            'device_model'       => $agent->device(),
            'ip_address'         => $ip,
            'operating_system'   => $agent->platform(),
            'os_version'         => $agent->version($agent->platform()),
            'user_agent'         => $request->userAgent(),
            'possible_incognito' => false,
            'referrer_url'       => $request->headers->get('referer'),
            'session_id'         => session()->getId(),
            'login_time'         => now()
        ];

        $geoData = [
            'country' => optional($location)->countryName,
            'state'   => optional($location)->regionName,
            'city'    => optional($location)->cityName,
            'lat'     => optional($location)->latitude,
            'long'    => optional($location)->longitude,
            'address' => optional($location)->cityName
                ? optional($location)->cityName . ', ' .
                optional($location)->regionName . ', ' .
                optional($location)->countryName
                : null,
        ];

        SystemActivityModel::create(array_merge($auditData, $geoData, [
            'auth_user_id' => $authUser->auth_user_id,
            'login_status' => 'Success',
            'failure_reason' => null
        ]));

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

        try {
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
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'Failed due to a database error.',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    // Fetch Auth Details
    public function fetchAuthUserDetails(Request $request)
    {
        $authUserId = $request->input('auth_user_id');

        $validator = Validator::make($request->all(), [
            'auth_user_id'    => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'Unprocessable Content',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

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
    public function fetchAuthUsersList(Request $request)
    {
        $authUserType = $request->input('auth_user_type', '');

        if (!empty($authUserType) && ($authUserType !== 'SUPER_ADMIN' && $authUserType !== 'ADMIN')) {
            return response()->json([
                'status'  => 'Unprocessable Content',
                'message' => 'Invalid auth type.'
            ], 422);
        }

        $query = AuthModel::from('auth_tbl AS a')
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
            );

        if (!empty($authUserType)) {
            $authUsersList = $query->where('a.auth_user_type', $authUserType)->orderBy('a.created_at', 'DESC')->get();
        } else {
            $authUsersList = $query->orderBy('a.created_at', 'DESC')->get();
        }

        if ($authUsersList->isEmpty()) {
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

        $validator = Validator::make($request->all(), [
            'auth_user_id'    => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'Unprocessable Content',
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

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
                'auth_user_image'   => $imageName,
                'auth_image_path'   => '/vendor/upload/auth/' . $imageName
            ], 200);
        }
    }
}
