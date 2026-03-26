<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\OtpModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    // User Login
    public function userLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'Validation Error',
                'message' => $validator->errors()->first(),
            ], 422, ['Content-Type' => 'application/json']);
        }

        $user = UserModel::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            $user = UserModel::create([
                'phone_number'       => $request->phone_number,
                'active'             => 'YES',
                'user_type'          => 'USER',
                'user_referral_code' => strtoupper(substr(md5($request->phone_number . time()), 0, 8)),
            ]);
            $isNewUser = true;
        } else {
            if ($user->active !== 'YES') {
                return response()->json([
                    'status'  => 'Error',
                    'message' => 'Your account is inactive. Please contact support.',
                ], 403, ['Content-Type' => 'application/json']);
            }
            $isNewUser = false;
        }

        $generatedOtp  = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        $otpExpiryTime = now()->addMinutes(5);

        OtpModel::updateOrCreate(
            [
                'user_id' => $user->user_id,
            ],
            [
                'phone_number' => $user->phone_number,
                'otp'          => $generatedOtp,
                'otp_verified' => 0,
                'otp_expired'  => $otpExpiryTime,
            ]
        );

        return response()->json([
            'status'  => 'Success',
            'message' => 'OTP sent successfully.',
            'data'    => [
                'user_id'     => $user->user_id,
                'is_new_user' => $isNewUser,
                'otp_expires' => $otpExpiryTime->toDateTimeString(),
                'otp'         => $generatedOtp,
            ],
        ], 200, ['Content-Type' => 'application/json']);
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'otp'     => 'required|string|size:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'Validation Error',
                'message' => $validator->errors(),
            ], 422, ['Content-Type' => 'application/json']);
        }

        $user = UserModel::where('user_id', $request->user_id)
            ->where('active', 'YES')
            ->first();

        if (!$user) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'User not found or inactive.',
            ], 404, ['Content-Type' => 'application/json']);
        }

        $otpRecord = OtpModel::getValidOtp($user->user_id, $request->otp);

        if (!$otpRecord) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'Invalid or expired OTP.',
            ], 401, ['Content-Type' => 'application/json']);
        }

        $otpRecord->update(['otp_verified' => 1]);

        try {
            $accessTokenExpiry  = now()->addMinutes(5);
            $refreshTokenExpiry = now()->addDays(15);

            $accessToken = JWTAuth::customClaims([
                'exp'          => $accessTokenExpiry->timestamp,
                'token_type'   => 'access',
                'user_id'      => $user->user_id,
                'phone_number' => $user->phone_number,
                'user_type'    => $user->user_type,
            ])->fromUser($user);

            $refreshToken = JWTAuth::customClaims([
                'exp'        => $refreshTokenExpiry->timestamp,
                'token_type' => 'refresh',
                'user_id'    => $user->user_id,
                'user_type'  => $user->user_type,
            ])->fromUser($user);
        } catch (JWTException $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'Could not generate tokens.',
            ], 500, ['Content-Type' => 'application/json']);
        }

        return response()->json([
            'status'  => 'Success',
            'message' => 'OTP verified. Login successful.',
            'data'    => [
                'access_token'          => $accessToken,
                'refresh_token'         => $refreshToken,
                'token_type'            => 'Bearer',
                'access_token_expires'  => $accessTokenExpiry->toDateTimeString(),
                'refresh_token_expires' => $refreshTokenExpiry->toDateTimeString(),
                'user' => [
                    'user_id'      => $user->user_id,
                    'full_name'    => $user->full_name,
                    'phone_number' => $user->phone_number,
                    'user_type'    => $user->user_type,
                ],
            ],
        ], 200, ['Content-Type' => 'application/json']);
    }

    // Refresh Token
    public function refreshToken(Request $request)
    {
        $refreshToken = $request->header('refreshToken');

        if (!$refreshToken) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'Refresh token is missing.',
            ], 400, ['Content-Type' => 'application/json']);
        }

        try {
            JWTAuth::setToken($refreshToken);
            $payload = JWTAuth::getPayload();

            if ($payload->get('token_type') !== 'refresh') {
                return response()->json([
                    'status'  => 'Error',
                    'message' => 'Invalid refresh token.',
                ], 401, ['Content-Type' => 'application/json']);
            }

            $user = UserModel::where('user_id', $payload->get('user_id'))
                ->where('active', 'YES')
                ->first();

            if (!$user) {
                return response()->json([
                    'status'  => 'Error',
                    'message' => 'User not found or inactive.',
                ], 404, ['Content-Type' => 'application/json']);
            }

            JWTAuth::setToken($refreshToken)->invalidate();

            $accessTokenExpiry  = now()->addMinutes(5);
            $refreshTokenExpiry = now()->addDays(15);

            $newAccessToken = JWTAuth::customClaims([
                'exp'          => $accessTokenExpiry->timestamp,
                'token_type'   => 'access',
                'user_id'      => $user->user_id,
                'phone_number' => $user->phone_number,
                'user_type'    => $user->user_type,
            ])->fromUser($user);

            $newRefreshToken = JWTAuth::customClaims([
                'exp'        => $refreshTokenExpiry->timestamp,
                'token_type' => 'refresh',
                'user_id'    => $user->user_id,
                'user_type'  => $user->user_type,
            ])->fromUser($user);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'status'  => 'Unauthorized',
                'message' => 'Refresh token expired. Please login again.',
                'code'    => 'REFRESH_TOKEN_EXPIRED',
            ], 401, ['Content-Type' => 'application/json']);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'status'  => 'Unauthorized',
                'message' => 'Refresh token is invalid.',
                'code'    => 'REFRESH_TOKEN_INVALID',
            ], 401, ['Content-Type' => 'application/json']);
        } catch (JWTException $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'Could not process refresh token.',
            ], 500, ['Content-Type' => 'application/json']);
        }

        return response()->json([
            'status'  => 'Success',
            'message' => 'Token refreshed successfully.',
            'data'    => [
                'access_token'          => $newAccessToken,
                'refresh_token'         => $newRefreshToken,
                'token_type'            => 'Bearer',
                'access_token_expires'  => $accessTokenExpiry->toDateTimeString(),
                'refresh_token_expires' => $refreshTokenExpiry->toDateTimeString(),
            ],
        ], 200, ['Content-Type' => 'application/json']);
    }

    // Logout
    public function logout(Request $request)
    {
        $errors = [];

        if ($accessToken = $request->header('accessToken')) {
            try {
                JWTAuth::setToken($accessToken)->invalidate();
            } catch (JWTException $e) {
                $errors[] = 'Access token could not be invalidated.';
            }
        }

        if ($refreshToken = $request->header('refreshToken')) {
            try {
                JWTAuth::setToken($refreshToken)->invalidate();
            } catch (JWTException $e) {
                $errors[] = 'Refresh token could not be invalidated.';
            }
        }

        return response()->json([
            'status'  => 'Success',
            'message' => empty($errors) ? 'Logged out successfully.' : 'Logged out with warnings.',
            'errors'  => $errors,
        ], 200, ['Content-Type' => 'application/json']);
    }
}
