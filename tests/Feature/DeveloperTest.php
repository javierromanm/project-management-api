<?php

use App\Models\Developer;
use App\Models\User;

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

it('does not save the developer if user creation fails', function(){
    $postData = getDeveloperPostAndPatchData([
        'email' => 'invalid-email'
    ]);

    loginAdmin()->postJson('/api/developers', $postData)
        ->assertStatus(422);

    expect(User::count())->toBe(1);
    expect(Developer::count())->toBe(0);
});

it('does not save the user if developer creation fails', function(){
    $postData = getDeveloperPostAndPatchData([
        'last_name' => str_repeat('a', 256)
    ]);

    loginAdmin()->postJson('/api/developers', $postData)
        ->assertStatus(500);

    expect(User::count())->toBe(1);
    expect(Developer::count())->toBe(0);
});

it('can update a developer if admin', function(){
    $this->withoutExceptionHandling();

    $developer = Developer::factory()->create();

    $patchData = getDeveloperPostAndPatchData();

    loginAdmin()->patchJson('/api/developers/' . $developer->id, $patchData)
        ->assertStatus(200);
    
    $developerUpdated = Developer::latest()->first();

    expectDeveloperPostAndPatchData($developerUpdated, $patchData);
});

it('cannot update a developer if not authenticated', function(){
    $developer = Developer::factory()->create();

    $patchData = getDeveloperPostAndPatchData();

    $this->patchJson('/api/developers/' . $developer->id, $patchData)
        ->assertStatus(401);
});

it('cannot update a developer if not admin', function(){
    $developer = Developer::factory()->create();

    $patchData = getDeveloperPostAndPatchData();

    loginClient()->patchJson('/api/developers/' . $developer->id, $patchData)
        ->assertStatus(403);

    loginDeveloper()->patchJson('/api/developers/' . $developer->id, $patchData)
        ->assertStatus(403);
});

it('requires email, name when updating a developer', function(){
    $developer = Developer::factory()->create();

    $patchData = getDeveloperPostAndPatchData([
        'email' => null,
        'name' => null
    ]);

    loginAdmin()->patchJson('/api/developers/' . $developer->id, $patchData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'email',
            'name'
        ]);
});

it('validates email format when updating a developer', function(){
    $developer = Developer::factory()->create();

    $patchData = getDeveloperPostAndPatchData([
        'email' => 'invalid-email'
    ]);

    loginAdmin()->patchJson('/api/developers/' . $developer->id, $patchData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'email'
        ]);
});

it('ensures the name field does not exceed 255 characters when updating a developer', function(){
    $developer = Developer::factory()->create();

    $patchData = getDeveloperPostAndPatchData([
        'name' => str_repeat('a', 256)
    ]);

    loginAdmin()->patchJson('/api/developers/' . $developer->id, $patchData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'name'
        ]);
});
