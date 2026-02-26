<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function loadCategoryView()
    {
        return view('templates.admin.product-category');
    }

    public function loadSubCategoryView()
    {
        return view('templates.admin.product-sub-category');
    }
}
