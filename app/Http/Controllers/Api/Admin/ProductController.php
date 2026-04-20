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
            $eventId = $request->input('event_id');

            $validator = Validator::make($request->all(), [
                'product_slug' => 'required|string|exists:product_tbl,product_slug',
                'event_id'     => 'required|string|exists:product_tbl,event_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422, ['Content-Type' => 'application/json']);
            }

            $productDetails = ProductModel::from('product_tbl AS p')
                ->leftJoin('category_tbl AS c', 'p.category_id', '=', 'c.category_id')
                ->leftJoin('sub_category_tbl AS sc', 'p.sub_category_id', '=', 'sc.sub_category_id')
                ->leftJoin('language_tbl AS l', 'p.language_id', '=', 'l.language_id')
                ->select(
                    'p.product_id',
                    'p.event_id AS p_event_id',
                    'p.product_name',
                    'p.category_id',
                    'c.category_name',
                    'c.category_slug',
                    'c.event_id AS c_event_id',
                    'p.sub_category_id',
                    'sc.sub_category_name',
                    'sc.sub_category_slug',
                    'sc.event_id AS sc_event_id',
                    'p.language_id',
                    'l.language_name',
                    'l.language_slug',
                    'l.event_id AS l_event_id',
                    'p.product_slug',
                    'p.product_brand',
                    'p.product_code',
                    'p.bound_type',
                    'p.book_size',
                    'p.best_seller',
                    'p.new_arrival',
                    'p.product_availability',
                    'p.product_price',
                    'p.product_details',
                    'p.product_image',
                    'p.product_stock'
                )
                ->where('p.product_slug', $productSlug)
                ->where('p.event_id', $eventId)
                ->where('p.status', 'YES')
                ->first();

            if (!$productDetails) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => 'Product not found.',
                    'data' => null
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => 'Product details fetched successfully.',
                'data' => $productDetails
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

    public function fetchFeaturedProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $bestSeller = $request->input('best_seller');
            $newArrival = $request->input('new_arrival');
            $deal = $request->input('deal');

            $validator = Validator::make($request->all(), [
                'best_seller' => 'required|in:YES,NO',
                'new_arrival' => 'required|in:YES,NO',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422, ['Content-Type' => 'application/json']);
            }

            $featuredProducts = ProductModel::from('product_tbl AS p')
                ->leftJoin('category_tbl AS c', 'p.category_id', '=', 'c.category_id')
                ->leftJoin('sub_category_tbl AS sc', 'p.sub_category_id', '=', 'sc.sub_category_id')
                ->leftJoin('language_tbl AS l', 'p.language_id', '=', 'l.language_id')
                ->select(
                    'p.product_id',
                    'p.event_id AS p_event_id',
                    'p.product_name',
                    'p.category_id',
                    'c.category_name',
                    'c.category_slug',
                    'c.event_id AS c_event_id',
                    'p.sub_category_id',
                    'sc.sub_category_name',
                    'sc.sub_category_slug',
                    'sc.event_id AS sc_event_id',
                    'p.language_id',
                    'l.language_name',
                    'l.language_slug',
                    'l.event_id AS l_event_id',
                    'p.product_slug',
                    'p.product_brand',
                    'p.product_code',
                    'p.bound_type',
                    'p.book_size',
                    'p.best_seller',
                    'p.new_arrival',
                    'p.product_availability',
                    'p.product_price',
                    'p.product_details',
                    'p.product_image',
                    'p.product_stock'
                )
                ->where('p.best_seller', $bestSeller)
                ->where('p.new_arrival', $newArrival)
                ->where('p.status', 'YES')
                ->orderBy('p.created_at', 'DESC')
                ->get();

            if ($featuredProducts->isEmpty()) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => "No featured products found matching the criteria.",
                    'data' => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => "Featured products fetched successfully.",
                'data' => $featuredProducts
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
