<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function fetchCategoriesList(): \Illuminate\Http\JsonResponse
    {
        try {
            $categoriesList = CategoryModel::from('category_tbl AS c')
                ->select(
                    'c.category_id',
                    'c.category_name',
                    'c.category_slug',
                    'c.category_image'
                )
                ->where('c.category_status', 'YES')
                ->orderBy('c.created_at', 'DESC')
                ->get();

            if ($categoriesList->isEmpty()) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => 'No categories found.',
                    'data' => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => 'Categories list fetched successfully.',
                'data' => $categoriesList
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

    public function fetchCategoryWiseProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'category_id' => 'required|integer|exists:category_tbl,category_id',
            ]);

            $categoryId = $request->input('category_id');

            $productsList = CategoryModel::from('category_tbl AS c')
                ->join('product_tbl AS p', 'c.category_id', '=', 'p.category_id')
                ->select(
                    'p.product_id',
                    'p.product_name',
                    'p.product_image',
                    'p.product_price',
                    'p.product_code',
                    'p.product_availability',
                    'p.product_details',
                    'p.product_stock'
                )
                ->where('c.category_id', $categoryId)
                ->where('p.status', 'YES')
                ->orderBy('p.created_at', 'DESC')
                ->get();

            if ($productsList->isEmpty()) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => 'No products found for this category.',
                    'data' => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => 'Products list fetched successfully for the category.',
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
