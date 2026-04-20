<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\LanguageModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    public function fetchLanguagesList(): \Illuminate\Http\JsonResponse
    {
        try {
            $languagesList = LanguageModel::from('language_tbl AS l')
                ->select(
                    'l.language_id',
                    'l.event_id',
                    'l.language_name',
                    'l.language_slug',
                    'l.language_image'
                )
                ->where('l.language_status', 'YES')
                ->orderBy('l.created_at', 'DESC')
                ->get();

            if ($languagesList->isEmpty()) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => 'No languages found.',
                    'data' => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => 'Languages list fetched successfully.',
                'data' => $languagesList
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

    public function fetchLanguageWiseProducts(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $languageId = $request->input('language_id');
            $eventId = $request->input('event_id');

            $validator = Validator::make($request->all(), [
                'language_id'   => 'required|integer|exists:language_tbl,language_id',
                'event_id'      => 'required|string|exists:language_tbl,event_id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status'  => 'Unprocessable Content',
                    'message' => 'Validation failed.',
                    'errors'  => $validator->errors(),
                ], 422, ['Content-Type' => 'application/json']);
            }

            $productsList = LanguageModel::from('language_tbl AS l')
                ->join('product_tbl AS p', 'l.language_id', '=', 'p.language_id')
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
                ->where('l.language_id', $languageId)
                ->where('l.event_id', $eventId)
                ->where('p.status', 'YES')
                ->orderBy('p.created_at', 'DESC')
                ->get();

            if ($productsList->isEmpty()) {
                return response()->json([
                    'success' => "Not Found",
                    'message' => 'No products found for this language.',
                    'data' => []
                ], 404, ['Content-Type' => 'application/json']);
            }

            return response()->json([
                'success' => "Success",
                'message' => 'Products list fetched successfully for the language.',
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
