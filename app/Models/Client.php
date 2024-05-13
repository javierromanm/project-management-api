<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    public static function getDataForIndex($request)
    {
        $clients = Client::orderBy('id', 'desc')
            ->paginate(10)
            ->through(function($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'last_name' => $client->last_name,
                    'telephone' => $client->telephone,
                    'observations' => $client->observations,
                    'email' => $client->user->email
                ];
            });
        
        return ['clients' => $clients];
    }
}
