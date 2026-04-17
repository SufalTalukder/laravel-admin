<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategoryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SubCategoryController extends Controller
{
    public function fetchSubCategoriesList(): \Illuminate\Http\JsonResponse
    {
        try {
            $subCategoriesList = SubCategoryModel::from('sub_category_tbl AS sc')
                ->select(
                    'sc.sub_category_id',
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
            $request->validate([
                'sub_category_id' => 'required|integer|exists:sub_category_tbl,sub_category_id',
            ]);

            $subCategoryId = $request->input('sub_category_id');

            $productsList = SubCategoryModel::from('sub_category_tbl AS sc')
                ->join('product_tbl AS p', 'sc.sub_category_id', '=', 'p.sub_category_id')
                ->select(
                    'p.product_id',
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
