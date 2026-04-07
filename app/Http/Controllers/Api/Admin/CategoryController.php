<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoryModel;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function fetchCategoriesList()
    {
        try {
            $categoriesList = CategoryModel::from('category_tbl as c')
                ->select(
                    'c.category_id',
                    'c.category_name',
                    'c.category_image',
                    'c.category_status'
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
}
