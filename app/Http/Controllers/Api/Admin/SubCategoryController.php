<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubCategoryModel;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function fetchSubCategoriesList(): \Illuminate\Http\JsonResponse
    {
        try {
            $subCategoriesList = SubCategoryModel::from('sub_category_tbl AS sc')
                ->select(
                    'sc.sub_category_id',
                    'sc.sub_category_name',
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
}
