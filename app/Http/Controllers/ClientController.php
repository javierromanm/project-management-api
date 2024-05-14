<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Client::getDataForIndex($request));
    }

    public function store(Request $request)
    {
        $client = new Client;

        $client->storeUser($request);

        $client->storeOrUpdate($request);
    }
}
