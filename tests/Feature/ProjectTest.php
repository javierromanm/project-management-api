<?php

use App\Models\Project;

it('returns all projects paginated by 10 per page if admin', function(){
    $this->withoutExceptionHandling();
    
    Project::factory()->count(30)->create();

    loginAdmin()->getJson('/api/projects')
        ->assertStatus(200)
        ->assertJsonCount(10, 'projects.data')
        ->assertJsonStructure([
            'projects' => [
                'data' => [
                    '*' => [
                        'id',
                        'client_name',
                        'company-name',
                        'price',
                        'delivery_date',
                        'status_project',
                        'status_invoice',
                        'status_payment',
                        'invoice_number',
                        'invoice_date'
                    ]
                ]
            ]
        ]);
});
