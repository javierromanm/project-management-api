<?php

use App\Models\Developer;
use App\Models\Project;
use App\Models\StatusInvoice;
use App\Models\StatusPayment;
use App\Models\StatusTask;
use App\Models\Task;

function getTaskPostAndPatchData($overrides = [])
{
    return array_merge([
        'project_id' => Project::factory()->create()->id,
        'developer_id' => Developer::factory()->create()->id,
        'status_task_id' => StatusTask::factory()->create()->id,
        'status_invoice_id' => StatusInvoice::factory()->create()->id,
        'status_payment_id' => StatusPayment::factory()->create()->id,
        'description' => fake()->sentence(),
        'price_client' => fake()->randomNumber(9, false),
        'price_developer' => fake()->randomNumber(9, false),
        'delivery_date_client' => fake()->date('Y-m-d H:i:s'),
        'delivery_date_developer' => fake()->date('Y-m-d H:i:s'),
        'invoice_number_developer' => fake()->randomNumber(5, false),
        'invoice_date_developer' => fake()->date('Y-m-d H:i:s'),
    ], $overrides);
}

function expectTaskPostAndPatchData($model, $data)
{
    expect($model)
        ->project_id->toBe($data['project_id'])
        ->developer_id->toBe($data['developer_id'])
        ->status_task_id->toBe($data['status_task_id'])
        ->status_invoice_id->toBe($data['status_invoice_id'])
        ->status_payment_id->toBe($data['status_payment_id'])
        ->description->toBe($data['description'])
        ->price_client->toBe($data['price_client'])
        ->price_developer->toBe($data['price_developer'])
        ->delivery_date_client->toBe($data['delivery_date_client'])
        ->delivery_date_developer->toBe($data['delivery_date_developer'])
        ->invoice_number_developer->toBe($data['invoice_number_developer'])
        ->invoice_date_developer->toBe($data['invoice_date_developer']);
}

it('returns all tasks paginated by 10 if admin', function(){
    $this->withoutExceptionHandling();
    
    Task::factory()->count(30)->create();

    loginAdmin()->getJson('/api/tasks')
        ->assertStatus(200)
        ->assertJsonCount(10, 'tasks.data')
        ->assertJsonStructure([
            'tasks' => [
                'data' => [
                    '*' => [
                        'id',
                        'project_id',
                        'developer_id',
                        'developer_name',
                        'description',
                        'price_client',
                        'price_developer',
                        'delivery_date_client',
                        'delivery_date_developer',
                        'status_task',
                        'status_invoice',
                        'status_payment',
                        'invoice_number_developer',
                        'invoice_date_developer'
                    ]
                ]
            ]
        ]);
});

it('does not return all tasks if not authenticated', function(){
    Task::factory()->count(30)->create();

    $this->getJson('/api/tasks')
        ->assertStatus(401);
});

it('does not return all tasks if not admin', function(){
    Task::factory()->count(30)->create();

    loginClient()->getJson('/api/tasks')
        ->assertStatus(403);

    loginDeveloper()->getJson('/api/tasks')
        ->assertStatus(403);
});

it('can store a task if admin', function(){
    $this->withoutExceptionHandling();

    $postData = getTaskPostAndPatchData();

    loginAdmin()->postJson('/api/tasks', $postData)
        ->assertStatus(200);
    
    $task = Task::latest()->first();

    expectTaskPostAndPatchData($task, $postData);
});

it('cannot store a task if not autheticated', function(){
    $postData = getTaskPostAndPatchData();

    $this->postJson('/api/tasks', $postData)
        ->assertStatus(401);
});

it('cannot store a task if not admin', function(){
    $postData = getTaskPostAndPatchData();

    loginClient()->postJson('/api/tasks', $postData)
        ->assertStatus(403);

    loginDeveloper()->postJson('/api/tasks', $postData)
        ->assertStatus(403);
});

it('requires description, project_id, status task, status invoice and status payment when storing a task', function(){
    $postData = getTaskPostAndPatchData([
        'description' => null,
        'project_id' => null,
        'status_task_id' => null,
        'status_invoice_id' => null,
        'status_payment_id' => null,
    ]);

    loginAdmin()->postJson('/api/tasks', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'description',
            'project_id',
            'status_task_id',
            'status_invoice_id',
            'status_payment_id'
        ]);
});

it('ensures the description field does not exceed 255 characters when storing a task', function(){
    $postData = getTaskPostAndPatchData([
        'description' => str_repeat('a', 256)
    ]);

    loginAdmin()->postJson('/api/tasks', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'description'
        ]);
});

it('ensures price_client, price_developer and invoice_number_developer is an integer when storing a task', function(){
    $postData = getTaskPostAndPatchData([
        'price_client' => 13.45,
        'price_developer' => 15.45,
        'invoice_number_developer' => 23.45
    ]);

    loginAdmin()->postJson('/api/tasks', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'price_client',
            'price_developer',
            'invoice_number_developer'
        ]);
});

it('ensures delivery_date_client, delivery_date_developer and invoice_date_developer has a date time format when storing a task', function(){
    $postData = getTaskPostAndPatchData([
        'delivery_date_client' => '2005-24-07 10:23:18',
        'delivery_date_developer' => '2005-24-07 10:23:18',
        'invoice_date_developer' => '2005-24-07 10:23:18'
    ]);

    loginAdmin()->postJson('/api/tasks', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'delivery_date_client',
            'delivery_date_developer',
            'invoice_date_developer'
        ]);
});

it('can update a task if admin', function(){
    $this->withoutExceptionHandling();

    $task = Task::factory()->create();

    $patchData = getTaskPostAndPatchData();

    loginAdmin()->patchJson('/api/tasks/' . $task->id, $patchData)
        ->assertStatus(200);
    
    $taskUpdated = Task::latest()->first();

    expectTaskPostAndPatchData($taskUpdated, $patchData);
});

it('cannot update a task if not autheticated', function(){
    $task = Task::factory()->create();

    $patchData = getTaskPostAndPatchData();

    $this->patchJson('/api/tasks/' . $task->id, $patchData)
        ->assertStatus(401);
});

it('cannot update a task if not admin', function(){
    $task = Task::factory()->create();

    $patchData = getTaskPostAndPatchData();

    loginClient()->patchJson('/api/tasks/' . $task->id, $patchData)
        ->assertStatus(403);

    loginDeveloper()->patchJson('/api/tasks/' . $task->id, $patchData)
        ->assertStatus(403);
});

it('requires description, project_id, status task, status invoice and status payment when updating a task', function(){
    $task = Task::factory()->create();

    $patchData = getTaskPostAndPatchData([
        'description' => null,
        'project_id' => null,
        'status_task_id' => null,
        'status_invoice_id' => null,
        'status_payment_id' => null,
    ]);

    loginAdmin()->patchJson('/api/tasks/' . $task->id, $patchData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'description',
            'project_id',
            'status_task_id',
            'status_invoice_id',
            'status_payment_id'
        ]);
});

it('ensures the description field does not exceed 255 characters when updating a task', function(){
    $task = Task::factory()->create();

    $patchData = getTaskPostAndPatchData([
        'description' => str_repeat('a', 256)
    ]);

    loginAdmin()->patchJson('/api/tasks/' . $task->id, $patchData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'description'
        ]);
});

it('ensures price_client, price_developer and invoice_number_developer is an integer when updating a task', function(){
    $task = Task::factory()->create();

    $patchData = getTaskPostAndPatchData([
        'price_client' => 13.45,
        'price_developer' => 15.45,
        'invoice_number_developer' => 23.45
    ]);

    loginAdmin()->patchJson('/api/tasks/' . $task->id, $patchData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'price_client',
            'price_developer',
            'invoice_number_developer'
        ]);
});

it('ensures delivery_date_client, delivery_date_developer and invoice_date_developer has a date time format when updating a task', function(){
    $task = Task::factory()->create();

    $patchData = getTaskPostAndPatchData([
        'delivery_date_client' => '2005-24-07 10:23:18',
        'delivery_date_developer' => '2005-24-07 10:23:18',
        'invoice_date_developer' => '2005-24-07 10:23:18'
    ]);

    loginAdmin()->patchJson('/api/tasks/' . $task->id, $patchData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'delivery_date_client',
            'delivery_date_developer',
            'invoice_date_developer'
        ]);
});