<?php

use App\Models\Developer;

it('returns all developers paginated by 10 per page if admin', function() {
    $this->withoutExceptionHandling();

    Developer::factory()->count(30)->create();

    loginAdmin()->getJson('/api/developers')
        ->assertStatus(200)
        ->assertJsonCount(10, 'developers.data')
        ->assertJsonStructure([
            'developers' => [
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'last_name',
                        'telephone',
                        'observations',
                        'email'
                    ]
                ]
            ]
        ]);
});

it('does not return all developers if not authenticated', function() {
    Developer::factory()->count(30)->create();

    $this->getJson('/api/developers')
        ->assertStatus(401);
});

it('does not return all developers if not admin', function() {
    Developer::factory()->count(30)->create();

    loginClient()->getJson('/api/developers')
        ->assertStatus(403);

    loginDeveloper()->getJson('/api/developers')
        ->assertStatus(403);
});
