<?php

use App\Models\Client;

use function Pest\Faker\fake;

function getClientPostAndPatchData($overrides = [])
{
    return array_merge([
        'email' => fake()->email,
        'name' => fake()->firstName,
        'last_name' => fake()->lastName,
        'telephone' => fake()->phoneNumber,
        'observations' => fake()->sentence,
    ], $overrides);
}

function expectClientPostAndPatchData($model, $data)
{
    expect($model)
        ->user->role->toBe('client')
        ->user->email->toBe($data['email'])
        ->name->toBe($data['name'])
        ->last_name->toBe($data['last_name'])
        ->telephone->toBe($data['telephone'])
        ->observations->toBe($data['observations']);
}

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

it('does not return all clients if not authenticated', function(){
    Client::factory()->count(30)->create();

    $this->getJson('/api/clients')
        ->assertStatus(401);
});

it('does not return all clients if not admin', function(){
    Client::factory()->count(30)->create();

    loginClient()->getJson('/api/clients')
        ->assertStatus(403);

    loginDeveloper()->getJson('/api/clients')
        ->assertStatus(403);
});

it('can store a client if admin', function() {
    $this->withoutExceptionHandling();

    $postData = getClientPostAndPatchData();

    loginAdmin()->postJson('/api/clients', $postData)
        ->assertStatus(200);

    $client = Client::latest()->first();

    expectClientPostAndPatchData($client, $postData);
});

it('cannot store a client if not authenticated', function(){
    $postData = getClientPostAndPatchData();

    $this->postJson('/api/clients', $postData)
        ->assertStatus(401);
});

it('cannot store a client if not admin', function(){
    $postData = getClientPostAndPatchData();

    loginClient()->postJson('/api/clients', $postData)
        ->assertStatus(403);

    loginDeveloper()->postJson('/api/clients', $postData)
        ->assertStatus(403);
});

it('requires email, name when storing a client', function(){
    $postData = getClientPostAndPatchData([
        'email' => null,
        'name' => null
    ]);

    loginAdmin()->postJson('/api/clients', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'email',
            'name'
        ]);
});

it('validates email format when storing a client', function(){
    $postData = getClientPostAndPatchData([
        'email' => 'invalid-email'
    ]);

    loginAdmin()->postJson('/api/clients', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'email'
        ]);;
});

it('ensures the name field does not exceed 255 characters when storing a client', function(){
    $postData = getClientPostAndPatchData([
        'name' => str_repeat('a', 256)
    ]);

    loginAdmin()->postJson('/api/clients', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name'
        ]);
});
