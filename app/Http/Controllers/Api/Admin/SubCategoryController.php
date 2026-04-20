<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategoryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function fetchSubCategoriesList(): \Illuminate\Http\JsonResponse
    {
        try {
            $subCategoriesList = SubCategoryModel::from('sub_category_tbl AS sc')
                ->select(
                    'sc.sub_category_id',
                    'sc.event_id',
                    'sc.sub_category_name',
                    'sc.sub_category_slug',
                    'sc.sub_category_image'
                )
                ->where('sc.sub_category_status', 'YES')
                ->orderBy('sc.created_at', 'DESC')
                ->get();

            if ($subCategoriesList->isEmpty()) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => 'No subcategories found.',
                    'data' => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => 'Subcategories list fetched successfully.',
                'data' => $subCategoriesList
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

    public function fetchSubCategoryWiseProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $subCategoryId = $request->input('sub_category_id');
            $eventId = $request->input('event_id');

            $validator = Validator::make($request->all(), [
                'sub_category_id'   => 'required|integer|exists:sub_category_tbl,sub_category_id',
                'event_id'          => 'required|string|exists:sub_category_tbl,event_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422, ['Content-Type' => 'application/json']);
            }

            $productsList = SubCategoryModel::from('sub_category_tbl AS sc')
                ->join('product_tbl AS p', 'sc.sub_category_id', '=', 'p.sub_category_id')
                ->select(
                    'p.product_id',
                    'p.event_id',
                    'p.product_name',
                    'p.product_slug',
                    'p.product_image',
                    'p.product_price',
                    'p.product_code',
                    'p.product_availability',
                    'p.product_details',
                    'p.product_stock'
                )
                ->where('sc.sub_category_id', $subCategoryId)
                ->where('sc.event_id', $eventId)
                ->where('p.status', 'YES')
                ->orderBy('p.created_at', 'DESC')
                ->get();

            if ($productsList->isEmpty()) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => 'No products found for this subcategory.',
                    'data' => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => 'Products list fetched successfully for the subcategory.',
                'data' => $productsList
            ], 200, ['Content-Type' => 'application/json']);
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database Query Exception: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status'  => 'Error',
                'message' => 'Failed due to a database error.',
            ], 500, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            Log::error('Unexpected Exception: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status'  => 'Error',
                'message' => 'An unexpected error occurred.',
            ], 500, ['Content-Type' => 'application/json']);
        }
    }
}
