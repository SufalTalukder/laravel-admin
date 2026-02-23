<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestModel;

class TestController extends Controller
{
    public function index()
    {
        $testModel = new TestModel();
        $data = $testModel->all();
        return response()->json($data);
    }
}
