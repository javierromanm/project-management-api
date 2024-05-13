<?php

use App\Models\Client;

it('returns all clients paginated by 10 per page if admin', function() {

    $this->withoutExceptionHandling();
    
    Client::factory()->count(30)->create();

    loginAdmin()->getJson('/api/clients')
        ->assertStatus(200)
        ->assertJsonCount(10, 'clients.data')
        ->assertJsonStructure([
            'clients' => [
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'last_name',
                        'email',
                        'telephone',
                        'observations'
                    ]
                ]
            ]
        ]);
});
