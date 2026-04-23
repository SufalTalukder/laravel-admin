<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddToWishlistModel;
use App\Models\ProductModel;
use App\Models\RecentlyViewedProductModel;
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

    public function addRecentlyViewedProduct(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer|exists:product_tbl,product_id',
                'user_id'    => 'required|integer|exists:user_tbl,user_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422, ['Content-Type' => 'application/json']);
            }

            $userId    = (int) $request->input('user_id');
            $productId = (int) $request->input('product_id');

            // Check if the product is already then update
            $existing = RecentlyViewedProductModel::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($existing) {
                $existing->touch();
                return response()->json([
                    'success' => 'Success',
                    'message' => 'Recently viewed product updated successfully.',
                ], 200, ['Content-Type' => 'application/json']);
            }

            // Delete oldest entry if exceeding limit of 10 products
            $viewedCount = RecentlyViewedProductModel::where('user_id', $userId)->count();

            if ($viewedCount >= 10) {
                RecentlyViewedProductModel::where('user_id', $userId)
                    ->orderBy('created_at', 'ASC')
                    ->limit(1)
                    ->delete();
            }

            // Insert new entry
            $data = [
                'event_id'     => generate_event_id(),
                'auth_user_id' => 0,
                'user_id'      => $userId,
                'product_id'   => $productId,
                'view_status'  => 'YES',
            ];

            $created = RecentlyViewedProductModel::create($data);

            if (!$created) {
                Log::error('Failed to add product to recently viewed list.', ['data' => $data]);
                return response()->json([
                    'status'  => 'Error',
                    'message' => 'Failed to add product to recently viewed list.',
                ], 500, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => 'Success',
                'message' => 'Product added to recently viewed list successfully.',
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

    public function fetchRecentlyViewedProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id'    => 'required|integer|exists:user_tbl,user_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors()->first(),
                ], 422, ['Content-Type' => 'application/json']);
            }

            $userId = $request->input('user_id');

            $recentlyViewedProducts = RecentlyViewedProductModel::from('recently_viewed_tbl AS rvp')
                ->leftJoin('product_tbl AS p', 'rvp.product_id', '=', 'p.product_id')
                ->select(
                    'p.product_id',
                    'p.event_id AS p_event_id',
                    'p.product_name',
                    'p.product_slug',
                    'p.product_brand',
                    'p.product_code',
                    'p.product_availability',
                    'p.product_price',
                    'p.product_image'
                )
                ->where('rvp.user_id', $userId)
                ->where('rvp.view_status', 'YES')
                ->where('p.status', 'YES')
                ->orderBy('rvp.created_at', 'DESC')
                ->get();

            return response()->json([
                'success' => "Success",
                'message' => "Recently viewed products fetched successfully.",
                'data' => $recentlyViewedProducts
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

    public function manageWishlist(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer|exists:product_tbl,product_id',
                'user_id'    => 'required|integer|exists:user_tbl,user_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422, ['Content-Type' => 'application/json']);
            }

            $userId     = (int) $request->input('user_id');
            $productId  = (int) $request->input('product_id');

            $wishlist = AddToWishlistModel::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($wishlist) {
                if ($wishlist->add_to_wishlist_status == 'YES') {
                    $wishlist->update(['add_to_wishlist_status' => 'NO']);

                    return response()->json([
                        'status'  => 'Success',
                        'message' => 'Product removed from wishlist successfully.',
                    ], 200, ['Content-Type' => 'application/json']);
                } else {
                    $wishlist->update(['add_to_wishlist_status' => 'YES']);

                    return response()->json([
                        'status'  => 'Success',
                        'message' => 'Product added to wishlist successfully.',
                    ], 200, ['Content-Type' => 'application/json']);
                }
            }

            AddToWishlistModel::create([
                'event_id'               => generate_event_id(),
                'auth_user_id'           => 0,
                'product_id'             => $request->product_id,
                'user_id'                => $request->user_id,
                'add_to_wishlist_status' => 'YES',
            ]);

            return response()->json([
                'status'  => 'Success',
                'message' => 'Product added to wishlist successfully.',
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

    public function fetchWishlistOfUser(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer|exists:product_tbl,product_id',
                'user_id'    => 'required|integer|exists:user_tbl,user_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422, ['Content-Type' => 'application/json']);
            }

            $productId  = (int) $request->input('product_id');
            $userId     = (int) $request->input('user_id');

            $wishlistProducts = AddToWishlistModel::from('add_to_favourite_tbl')
                ->select(
                    'add_to_wishlist_status'
                )
                ->where('product_id', $productId)
                ->where('user_id', $userId)
                ->first();

            if (!$wishlistProducts) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => "No wishlist entry found for the specified product and user.",
                    'data' => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => "Wishlist product fetched successfully.",
                'data' => $wishlistProducts
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

    public function fetchAllWishlistsOfUser(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id'    => 'required|integer|exists:user_tbl,user_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors()->first(),
                ], 422, ['Content-Type' => 'application/json']);
            }

            $userId = (int) $request->input('user_id');

            $wishlistProducts = AddToWishlistModel::from('add_to_favourite_tbl AS atf')
                ->leftJoin('product_tbl AS p', 'atf.product_id', '=', 'p.product_id')
                ->select(
                    'p.product_id',
                    'p.event_id AS p_event_id',
                    'p.product_name',
                    'p.product_slug',
                    'p.product_brand',
                    'p.product_code',
                    'p.product_price',
                    'p.product_image'
                )
                ->where('atf.user_id', $userId)
                ->where('atf.add_to_wishlist_status', 'YES')
                ->orderBy('atf.created_at', 'DESC')
                ->get();

            if ($wishlistProducts->isEmpty()) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => "No wishlist products found for the specified user.",
                    'data' => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => "Wishlist products fetched successfully.",
                'data' => $wishlistProducts
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
