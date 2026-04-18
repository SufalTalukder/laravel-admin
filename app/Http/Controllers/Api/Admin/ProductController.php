<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function fetchProductDetails(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $productSlug = $request->input('product_slug');

            $validator = Validator::make($request->all(), [
                'product_slug' => 'required|string|exists:product_tbl,product_slug',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors()->first(),
                ], 422);
            }

            $productDetails = ProductModel::from('product_tbl AS p')
                ->leftJoin('category_tbl AS c', 'p.category_id', '=', 'c.category_id')
                ->leftJoin('sub_category_tbl AS sc', 'p.sub_category_id', '=', 'sc.sub_category_id')
                ->leftJoin('language_tbl AS l', 'p.language_id', '=', 'l.language_id')
                ->select(
                    'p.product_id',
                    'p.product_name',
                    'p.category_id',
                    'c.category_name',
                    'c.category_slug',
                    'p.sub_category_id',
                    'sc.sub_category_name',
                    'sc.sub_category_slug',
                    'p.language_id',
                    'l.language_name',
                    'l.language_slug',
                    'p.product_slug',
                    'p.product_brand',
                    'p.product_code',
                    'p.bound_type',
                    'p.book_size',
                    'p.product_availability',
                    'p.product_price',
                    'p.product_details',
                    'p.product_image',
                    'p.product_stock'
                )
                ->where('p.product_slug', $productSlug)
                ->where('p.status', 'YES')
                ->first();

            if (!$productDetails) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => 'Product not found.',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'success' => "Success",
                'message' => 'Product details fetched successfully.',
                'data' => $productDetails
            ], 200);
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
