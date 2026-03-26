<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\UserAddressModel;
use App\Models\UserModel;
use Exception;
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
            ], 404, ['Content-Type' => 'application/json']);
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
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'Failed due to a database error.',
            ], 500, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'An unexpected error occurred.',
            ], 500, ['Content-Type' => 'application/json']);
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
            ], 422, ['Content-Type' => 'application/json']);
        }

        $user = UserModel::where('user_id', $userId)->first();

        if (!$user) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'User not found.',
            ], 404, ['Content-Type' => 'application/json']);
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
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'Failed due to a database error.',
            ], 500, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'An unexpected error occurred.',
            ], 500, ['Content-Type' => 'application/json']);
        }
    }

    // ADD / UPDATE USER ADDRESS
    public function addUpdateUserAddress(Request $request)
    {
        $userId = $request->user_id;

        $validator = Validator::make($request->all(), [
            'user_address_id' => 'nullable|integer|exists:user_address_tbl,user_address_id',
            'address_type'    => 'required|in:Home,Office,Others',
            'user_address'    => 'required|string|min:5|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'Validation Error',
                'message' => $validator->errors(),
            ], 422);
        }

        try {
            $data = [
                'user_id'      => $userId,
                'address_type' => $request->address_type,
                'user_address' => $request->user_address
            ];

            $address = UserAddressModel::updateOrCreate(
                [
                    'user_address_id' => $request->user_address_id,
                    'user_id' => $userId
                ],
                $data
            );

            return response()->json([
                'status'  => 'Success',
                'message' => $request->user_address_id
                    ? 'Address updated successfully.'
                    : 'Address created successfully.'
            ], 200);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error("Database error", ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'Error',
                'message' => 'Database error occurred.',
            ], 500);
        } catch (\Exception $e) {
            Log::error("Unexpected error", ['error' => $e->getMessage()]);
            return response()->json([
                'status'  => 'Error',
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    // FETCH ALL USER ADDRESS LIST
    public function fetchAllUserAddresses(Request $request)
    {
        $userId = $request->user_id;

        try {
            $userAddressesList = UserAddressModel::from('user_address_tbl AS a')
                ->leftJoin('user_tbl AS b', 'b.user_id', '=', 'a.user_id')
                ->select(
                    'b.full_name',
                    'b.user_id',
                    'a.address_type',
                    'a.user_address',
                    'a.created_at'
                )
                ->where('b.user_id', $userId)
                ->orderBy('a.created_at', 'DESC')
                ->get();

            if ($userAddressesList->isEmpty()) {
                return response()->json([
                    'status'  => 'Not found',
                    'message' => 'User address(es) not found.',
                    'data'    => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'status'  => 'Success',
                'message' => 'User address(es) fetched successfully.',
                'data'    => $userAddressesList
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'Failed due to a database error.',
            ], 500, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'An unexpected error occurred.',
            ], 500, ['Content-Type' => 'application/json']);
        }
    }

    // FETCH USER DETAILS
    public function fetchUserAddressDetails(Request $request)
    {
        $userId = $request->user_id;

        $validator = Validator::make($request->all(), [
            'user_address_id' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'Validation Error',
                'message' => $validator->errors()->first(),
            ], 422, ['Content-Type' => 'application/json']);
        }

        try {
            $eachUserAddressDetails = UserAddressModel::from('user_address_tbl AS a')
                ->leftJoin('user_tbl AS b', 'b.user_id', '=', 'a.user_id')
                ->select(
                    'b.full_name',
                    'b.user_id',
                    'a.address_type',
                    'a.user_address',
                    'a.created_at'
                )
                ->where(['b.user_id' => $userId, 'user_address_id' => $request->user_address_id])
                ->get();

            if ($eachUserAddressDetails->isEmpty()) {
                return response()->json([
                    'status'  => 'Not found',
                    'message' => 'Address not found.'
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'status'  => 'Success',
                'message' => 'Address fetched successfully.',
                'data'    => $eachUserAddressDetails
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'Failed due to a database error.',
            ], 500, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'Error',
                'message' => 'An unexpected error occurred.',
            ], 500, ['Content-Type' => 'application/json']);
        }
    }
}
