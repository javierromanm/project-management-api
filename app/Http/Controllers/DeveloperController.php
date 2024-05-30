<?php

namespace App\Http\Controllers;

use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeveloperController extends Controller
{
    public function index (Request $request)
    {
        return response()->json(Developer::getDataForIndex($request));
    }

    public function store(Request $request)
    {
        $request->validate(Developer::validationRules());
        
        $developer = new Developer;

        DB::beginTransaction();

        try {
            $developer->storeUser($request);

            $developer->storeOrUpdate($request);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'Failed to save Developer and User'], 500);

        }
    }

    public function update(Request $request, $id)
    {
        $request->validate(Developer::validationRules());
        
        $developer = Developer::find($id);

        $developer->updateUser($request);

        $developer->storeOrUpdate($request);
    }
}
