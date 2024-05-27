<?php

use App\Models\Developer;

function getDeveloperPostAndPatchData($overrides = [])
{
    return array_merge([
        'email' => fake()->email,
        'name' => fake()->firstName,
        'last_name' => fake()->lastName,
        'telephone' => fake()->phoneNumber,
        'observations' => fake()->sentence,
    ], $overrides);
}

function expectDeveloperPostAndPatchData($model, $data)
{
    expect($model)
        ->user->role->toBe('developer')
        ->user->email->toBe($data['email'])
        ->name->toBe($data['name'])
        ->last_name->toBe($data['last_name'])
        ->telephone->toBe($data['telephone'])
        ->observations->toBe($data['observations']);
}

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

it('can store a developer if admin', function(){
    $this->withoutExceptionHandling();

    $postData = getDeveloperPostAndPatchData();

    loginAdmin()->postJson('/api/developers', $postData)
        ->assertStatus(200);
    
    $developer = Developer::latest()->first();

    expectDeveloperPostAndPatchData($developer, $postData);
});

it('cannot store a developer if not authenticated', function(){
    $postData = getDeveloperPostAndPatchData();

    $this->postJson('/api/developers', $postData)
        ->assertStatus(401);
});

it('cannot store a developer if not admin', function(){
    $postData = getDeveloperPostAndPatchData();

    loginClient()->postJson('/api/developers', $postData)
        ->assertStatus(403);

    loginDeveloper()->postJson('/api/developers', $postData)
        ->assertStatus(403);
});

it('requires email, name when storing a developer', function(){
    $postData = getDeveloperPostAndPatchData([
        'email' => null,
        'name' => null
    ]);

    loginAdmin()->postJson('/api/developers', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'email',
            'name'
        ]);
});

it('validates email format when storing a developer', function(){
    $postData = getDeveloperPostAndPatchData([
        'email' => 'invalid-email'
    ]);

    loginAdmin()->postJson('/api/developers', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'email'
        ]);
});

it('ensures the name field does not exceed 255 characters when storing a developer', function(){
    $postData = getDeveloperPostAndPatchData([
        'name' => str_repeat('a', 256)
    ]);

    loginAdmin()->postJson('/api/developers', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name'
        ]);
});
