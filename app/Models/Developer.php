<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Developer extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getDataForIndex($request)
    {
        $developers = Developer::orderBy('id', 'desc')
            ->paginate(10)
            ->through(function($developer){
                return [
                    'id' => $developer->id,
                    'name' => $developer->name,
                    'last_name' => $developer->last_name,
                    'telephone' => $developer->telephone,
                    'observations' => $developer->observations,
                    'email' => $developer->user->email
                ];
            });
        
            return ['developers' => $developers];
    }
}
