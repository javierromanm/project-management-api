<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    public function index (Request $request)
    {
        return response()->json(Developer::getDataForIndex($request));
    }

    public function store(Request $request)
    {
        $developer = new Developer;

        $developer->storeUser($request);

        $developer->storeOrUpdate($request);
    }
}
