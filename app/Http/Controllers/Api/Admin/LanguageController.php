<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\LanguageModel;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function fetchLanguagesList(): \Illuminate\Http\JsonResponse
    {
        try {
            $languagesList = LanguageModel::from('language_tbl AS l')
                ->select(
                    'l.language_id',
                    'l.language_name',
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
}
