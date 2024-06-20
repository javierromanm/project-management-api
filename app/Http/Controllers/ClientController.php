<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Client::getDataForIndex($request));
    }

    public function store(Request $request)
    {
        $request->validate(Client::validationRules());

        $client = new Client;

        DB::beginTransaction();

        try {
            $client->storeUser($request);

            $client->storeOrUpdate($request);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => 'Failed to save client and user'], 422);
        }  
    }

    public function edit(Request $request, $id)
    {
        $client = Client::find($id);
        return response()->json($client->getDataForEdit($request));
    }

    public function update(Request $request, $id)
   {
        $request->validate(Client::validationRules());

        $client = Client::find($id);

        $client->updateUser($request);

        $client->storeOrUpdate($request);
   } 
}
