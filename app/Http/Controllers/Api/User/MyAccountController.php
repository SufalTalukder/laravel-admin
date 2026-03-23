<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use function Symfony\Component\Clock\now;

class MyAccountController extends Controller
{
    // Get User Details
    public function fetchUserDetails(Request $request)
    {
        $userId = $request->user_id;

        if (!$userId) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'User not found.',
            ], 404);
        }

        try {
            $user = UserModel::from('user_tbl')
                ->where('user_id', $userId)
                ->select(
                    'user_id',
                    'full_name',
                    'email_address',
                    'phone_number',
                    'dob',
                    'user_address',
                    'user_referral_code',
                    'user_image',
                    'active'
                )
                ->first();

            return response()->json([
                'status'  => 'Success',
                'message' => 'User details fetched successfully.',
                'data'    => [
                    'user_id'            => $user->user_id,
                    'full_name'          => $user->full_name,
                    'email_address'      => $user->email_address,
                    'phone_number'       => $user->phone_number,
                    'dob'                => $user->dob,
                    'user_address'       => $user->user_address,
                    'user_referral_code' => $user->user_referral_code,
                    'user_image'         => !empty($user->user_image)
                        ? '/vendor/upload/user/' . $user->user_image
                        : '',
                ],
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

    // Update User Details
    public function updateUserDetails(Request $request)
    {
        $userId = $request->user_id;

        $validator = Validator::make($request->all(), [
            'full_name'     => 'required|string|max:100',
            'email_address' => "required|email|max:100|unique:user_tbl,email_address,{$userId},user_id",
            'dob'           => 'required|date',
            'user_address'  => 'required|string|max:255',
            'user_image'    => 'nullable|string|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'Validation Error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $user = UserModel::where('user_id', $userId)->first();

        if (!$user) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'User not found.',
            ], 404);
        }

        try {
            $data = [
                'full_name'     => $request->full_name,
                'email_address' => $request->email_address,
                'dob'           => $request->dob,
                'user_address'  => $request->user_address,
                'user_image'    => $request->user_image,
                'updated_at'    => now(),
            ];

            UserModel::where('user_id', $userId)->update($data);

            return response()->json([
                'status'  => 'Success',
                'message' => 'User details updated successfully.',
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
}
