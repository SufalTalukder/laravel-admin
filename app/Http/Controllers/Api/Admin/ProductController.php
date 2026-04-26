<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AddToCartModel;
use App\Models\AddToWishlistModel;
use App\Models\ProductModel;
use App\Models\RecentlyViewedProductModel;
use App\Models\ReviewProductModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function fetchAllWishlistsOfUser(Request $request): \Illuminate\Http\JsonResponse
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

    public function manageUserCart(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer|exists:product_tbl,product_id',
                'user_id'    => 'required|integer|exists:user_tbl,user_id',
                'action'     => 'required|in:add,remove,increment,decrement',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $userId    = (int) $request->input('user_id');
            $productId = (int) $request->input('product_id');
            $action    = $request->input('action');

            // Fetch product price
            $product = DB::table('product_tbl')
                ->where('product_id', $productId)
                ->select('product_price')
                ->first();

            if (!$product) {
                return response()->json([
                    'status'  => 'Error',
                    'message' => 'Product not found.',
                ], 404);
            }

            $productPrice = (float) $product->product_price;

            $cartItem = AddToCartModel::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            switch ($action) {

                case 'add':
                    if ($cartItem) {
                        if ($cartItem->add_to_cart_status === 'YES') {
                            return response()->json([
                                'status'  => 'Success',
                                'message' => 'Product is already in the cart.',
                                'data'    => $this->formatCartItem($cartItem),
                            ], 200);
                        }
                        // Re-add previously removed item
                        $cartItem->update([
                            'add_to_cart_status'       => 'YES',
                            'quantity'                 => 1,
                            'each_product_total_price' => $productPrice,
                            'created_at'               => now(),
                        ]);
                    } else {
                        $cartItem = AddToCartModel::create([
                            'event_id'                 => generate_event_id(),
                            'auth_user_id'             => 0,
                            'user_id'                  => $userId,
                            'product_id'               => $productId,
                            'quantity'                 => 1,
                            'each_product_total_price' => $productPrice,
                            'add_to_cart_status'       => 'YES',
                            'created_at'               => now(),
                        ]);
                    }

                    return response()->json([
                        'status'  => 'Success',
                        'message' => 'Product added to cart successfully.',
                        'data'    => $this->formatCartItem($cartItem),
                    ], 200);

                case 'remove':
                    if (!$cartItem || $cartItem->add_to_cart_status === 'NO') {
                        return response()->json([
                            'status'  => 'Error',
                            'message' => 'Product is not in the cart.',
                        ], 404);
                    }

                    $cartItem->update([
                        'add_to_cart_status'       => 'NO',
                        'quantity'                 => 0,
                        'each_product_total_price' => 0,
                        'created_at'               => now(),
                    ]);

                    return response()->json([
                        'status'  => 'Success',
                        'message' => 'Product removed from cart successfully.',
                    ], 200);

                case 'increment':
                    if (!$cartItem || $cartItem->add_to_cart_status === 'NO') {
                        return response()->json([
                            'status'  => 'Error',
                            'message' => 'Product is not in the cart.',
                        ], 404);
                    }

                    $newQty = $cartItem->quantity + 1;
                    $cartItem->update([
                        'quantity'                 => $newQty,
                        'each_product_total_price' => $productPrice * $newQty,
                        'created_at'               => now(),
                    ]);

                    return response()->json([
                        'status'  => 'Success',
                        'message' => 'Cart quantity increased.',
                        'data'    => $this->formatCartItem($cartItem),
                    ], 200);

                case 'decrement':
                    if (!$cartItem || $cartItem->add_to_cart_status === 'NO') {
                        return response()->json([
                            'status'  => 'Error',
                            'message' => 'Product is not in the cart.',
                        ], 404);
                    }

                    $newQty = $cartItem->quantity - 1;

                    if ($newQty <= 0) {
                        // Auto-remove when quantity hits 0
                        $cartItem->update([
                            'add_to_cart_status'       => 'NO',
                            'quantity'                 => 0,
                            'each_product_total_price' => 0,
                            'created_at'               => now(),
                        ]);

                        return response()->json([
                            'status'  => 'Success',
                            'message' => 'Product removed from cart (quantity reached zero).',
                        ], 200);
                    }

                    $cartItem->update([
                        'quantity'                 => $newQty,
                        'each_product_total_price' => $productPrice * $newQty,
                        'created_at'               => now(),
                    ]);

                    return response()->json([
                        'status'  => 'Success',
                        'message' => 'Cart quantity decreased.',
                        'data'    => $this->formatCartItem($cartItem),
                    ], 200);

                default:
                    return response()->json([
                        'status'  => 'Error',
                        'message' => 'Invalid action provided.',
                    ], 422);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Database Query Exception: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status'  => 'Error',
                'message' => 'Failed due to a database error.',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Unexpected Exception: ' . $e->getMessage(), ['exception' => $e]);
            return response()->json([
                'status'  => 'Error',
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    private function formatCartItem(AddToCartModel $item): array
    {
        return [
            'add_to_cart_id'           => $item->add_to_cart_id,
            'product_id'               => $item->product_id,
            'quantity'                 => $item->quantity,
            'each_product_total_price' => $item->each_product_total_price,
            'add_to_cart_status'       => $item->add_to_cart_status,
        ];
    }

    public function fetchUserCart(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id'    => 'required|integer|exists:user_tbl,user_id',
                'product_id' => 'required|integer|exists:product_tbl,product_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors()->first(),
                ], 422, ['Content-Type' => 'application/json']);
            }

            $userId    = (int) $request->input('user_id');
            $productId = (int) $request->input('product_id');

            $cartItem = AddToCartModel::where('user_id', $userId)
                ->where('product_id', $productId)
                ->where('add_to_cart_status', 'YES')
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => "No cart item found for the specified user and product.",
                    'data' => null
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => "Cart item fetched successfully.",
                'data' => $this->formatCartItem($cartItem)
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

    public function fetchAllUserCarts(Request $request)
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

            $cartItems = AddToCartModel::from('add_to_cart_tbl AS atc')
                ->leftJoin('product_tbl AS p', 'atc.product_id', '=', 'p.product_id')
                ->select(
                    'atc.add_to_cart_id',
                    'p.product_id',
                    'p.event_id',
                    'p.product_name',
                    'p.product_slug',
                    'p.product_brand',
                    'p.product_code',
                    'p.product_price',
                    'p.product_image',
                    'atc.quantity',
                    'atc.each_product_total_price'
                )
                ->where('atc.user_id', $userId)
                ->where('atc.add_to_cart_status', 'YES')
                ->orderBy('atc.created_at', 'DESC')
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => "No cart items found for the specified user.",
                    'data' => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => "Cart items fetched successfully.",
                'data' => $cartItems
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

    public function searchProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $query = $request->input('query');

            $validator = Validator::make($request->all(), [
                'query' => 'required|string|min:3',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors()->first(),
                ], 422, ['Content-Type' => 'application/json']);
            }

            $products = ProductModel::from('product_tbl AS p')
                ->leftJoin('category_tbl AS c',      'p.category_id',     '=', 'c.category_id')
                ->leftJoin('sub_category_tbl AS sc', 'p.sub_category_id', '=', 'sc.sub_category_id')
                ->leftJoin('language_tbl AS l',      'p.language_id',     '=', 'l.language_id')
                ->select(
                    'p.product_id',
                    'p.event_id',
                    'p.product_name',
                    'p.product_slug',
                    'p.product_image',
                    'sc.sub_category_name',
                    'l.language_name'
                )
                ->where('p.status', 'YES')
                ->where(function ($q) use ($query) {
                    $q->where('p.product_name',          'LIKE', "%{$query}%")
                        ->orWhere('p.product_code',         'LIKE', "%{$query}%")
                        ->orWhere('sc.sub_category_name',   'LIKE', "%{$query}%")
                        ->orWhere('l.language_name',        'LIKE', "%{$query}%");
                })
                ->orderBy('p.created_at', 'DESC')
                ->limit(10)
                ->get();

            if ($products->isEmpty()) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => "No products found matching the search query.",
                    'data' => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => "Products fetched successfully.",
                'data' => $products
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

    public function manageReviewProduct(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id'   => 'required|integer|exists:product_tbl,product_id',
                'user_id'      => 'required|integer|exists:user_tbl,user_id',
                'user_rating'  => 'required|integer|min:1|max:5',
                'user_comment' => 'nullable|string|max:1000',
                'action'       => 'required|in:add,update,delete',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422);
            }

            $productId   = (int) $request->input('product_id');
            $userId      = (int) $request->input('user_id');
            $userRating  = $request->input('user_rating');
            $userComment = $request->input('user_comment') ?: null;
            $action      = $request->input('action');

            $reviewProduct = ReviewProductModel::where('product_id', $productId)
                ->where('user_id', $userId)
                ->first();

            switch ($action) {

                case 'add':
                    if ($reviewProduct) {
                        return response()->json([
                            'status'  => 'Error',
                            'message' => 'Review already exists. Use update action.',
                        ], 409, ['Content-Type' => 'application/json']);
                    }
                    ReviewProductModel::create([
                        'event_id'     => generate_event_id(),
                        'auth_user_id' => 0,
                        'product_id'   => $productId,
                        'user_id'      => $userId,
                        'user_rating'  => $userRating,
                        'user_comment' => $userComment,
                    ]);
                    return response()->json([
                        'status'  => 'Success',
                        'message' => 'Product review submitted successfully.',
                    ], 200, ['Content-Type' => 'application/json']);

                case 'update':
                    if (!$reviewProduct) {
                        return response()->json([
                            'status'  => 'Error',
                            'message' => 'No review found to update.',
                        ], 404, ['Content-Type' => 'application/json']);
                    }
                    $reviewProduct->update([
                        'user_rating'  => $userRating,
                        'user_comment' => $userComment,
                        'updated_at'   => now(),
                    ]);
                    return response()->json([
                        'status'  => 'Success',
                        'message' => 'Product review updated successfully.',
                    ], 200, ['Content-Type' => 'application/json']);

                case 'delete':
                    if (!$reviewProduct) {
                        return response()->json([
                            'status'  => 'Error',
                            'message' => 'No review found to delete.',
                        ], 404, ['Content-Type' => 'application/json']);
                    }
                    $reviewProduct->delete();
                    return response()->json([
                        'status'  => 'Success',
                        'message' => 'Product review deleted successfully.',
                    ], 200, ['Content-Type' => 'application/json']);

                default:
                    return response()->json([
                        'status'  => 'Error',
                        'message' => 'Invalid action provided.',
                    ], 422, ['Content-Type' => 'application/json']);
            }
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

    public function fetchProductReviews(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|integer|exists:product_tbl,product_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors()->first(),
                ], 422, ['Content-Type' => 'application/json']);
            }

            $productId = (int) $request->input('product_id');

            $reviews = ReviewProductModel::from('user_rating_tbl AS r')
                ->leftJoin('user_tbl AS u', 'r.user_id', '=', 'u.user_id')
                ->select(
                    'r.user_rating_id',
                    'r.product_id',
                    'r.user_id',
                    'u.full_name AS user_name',
                    'r.user_rating',
                    'r.user_comment',
                    'r.created_at'
                )
                ->where('r.product_id', $productId)
                ->orderBy('r.created_at', 'DESC')
                ->get();

            if ($reviews->isEmpty()) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => "No reviews found for the specified product.",
                    'data' => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => "Product reviews fetched successfully.",
                'data' => $reviews
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
