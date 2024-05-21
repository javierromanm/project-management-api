<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Client extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function validationRules()
    {
        return [
            'email' => 'required|email',
            'name' => 'required|max:255'
        ];
    }

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

    public function storeUser($request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->role = 'client';
        $user->email = $request->email;
        $user->password = Hash::make(Str::random(15));
        $user->save();
        $this->user_id = $user->id;
    }

    public function storeOrUpdate ($request)
    {
        $this->name = $request->name;
        $this->last_name = $request->last_name;
        $this->telephone = $request->telephone;
        $this->observations = $request->observations;
        $this->save();
    }

    public function updateUser($request)
    {
        $user = $this->user;
        $user->email = $request->email;
        $user->save();
    }
}
