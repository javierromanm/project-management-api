<?php

use App\Models\Task;

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
