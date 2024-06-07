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

it('does not returns all tasks if not authenticated', function(){
    Task::factory()->count(30)->create();

    $this->getJson('/api/tasks')
        ->assertStatus(401);
});

it('does not returns all tasks if not admin', function(){
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