<?php

use App\Models\Client;
use App\Models\Company;
use App\Models\Project;
use App\Models\StatusInvoice;
use App\Models\StatusPayment;
use App\Models\StatusProject;

function getProjectPostAndPatchData($overrides = [])
{
    return array_merge([
        'client_id' => Client::factory()->create()->id,
        'company_id' => Company::factory()->create()->id,
        'status_project_id' => StatusProject::factory()->create()->id,
        'status_invoice_id' => StatusInvoice::factory()->create()->id,
        'status_payment_id' => StatusPayment::factory()->create()->id,
        'price' => fake()->randomNumber(9, false),
        'delivery_date' => fake()->date('Y-m-d H:i:s'),
        'invoice_number' => fake()->randomNumber(5, false),
        'invoice_date' => fake()->date('Y-m-d H:i:s')
    ], $overrides);
}

function expectProjectPostAndPatchData($model, $data)
{
    expect($model)
        ->client_id->toBe($data['client_id'])
        ->company_id->toBe($data['company_id'])
        ->status_project_id->toBe($data['status_project_id'])
        ->status_invoice_id->toBe($data['status_invoice_id'])
        ->status_payment_id->toBe($data['status_payment_id'])
        ->price->toBe($data['price'])
        ->delivery_date->toBe($data['delivery_date'])
        ->invoice_number->toBe($data['invoice_number'])
        ->invoice_date->toBe($data['invoice_date']);
}

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

it('does not return all projects if not authenticated', function(){
    Project::factory()->count(30)->create();

    $this->getJson('/api/projects')
        ->assertStatus(401);
});

it('does not return all projects if not admin', function(){
    Project::factory()->count(30)->create();

    loginClient()->getJson('/api/projects')
        ->assertStatus(403);

    loginDeveloper()->getJson('/api/projects')
        ->assertStatus(403);
});

it('can store a project if admin', function(){
    $this->withoutExceptionHandling();

    $postData = getProjectPostAndPatchData();

    loginAdmin()->postJson('/api/projects', $postData)
        ->assertStatus(200);
    
    $project = Project::latest()->first();

    expectProjectPostAndPatchData($project, $postData);
});

it('cannot store a project if not authenticated', function(){
    $postData = getProjectPostAndPatchData();

    $this->postJson('/api/projects', $postData)
        ->assertStatus(401);
});

it('cannot store a project if not admin', function(){
    $postData = getProjectPostAndPatchData();

    loginClient()->postJson('/api/projects', $postData)
        ->assertStatus(403);

    loginDeveloper()->postJson('/api/projects', $postData)
        ->assertStatus(403);
});

it('requires status project, status invoice and status payment when storing a project', function(){
    $postData = getProjectPostAndPatchData([
        'status_project_id' => null,
        'status_invoice_id' => null,
        'status_payment_id' => null
    ]);

    loginAdmin()->postJson('/api/projects', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'status_project_id',
            'status_invoice_id',
            'status_payment_id'
        ]);
});

it('ensures price and invoice number is an integer when storing a project', function(){
    $postData = getProjectPostAndPatchData([
        'price' => 13.20,
        'invoice_number' => 125.40
    ]);

    loginAdmin()->postJson('/api/projects', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'price',
            'invoice_number'
        ]);
});

it('ensures delivery date and invoice date has an specific date time format', function(){
    $postData = getProjectPostAndPatchData([
        'delivery_date' => '2005-20-12 10:03:26',
        'invoice_date' => '2005-20-12 10:03:26'
    ]);

    loginAdmin()->postJson('/api/projects', $postData)
        ->assertStatus(422)
        ->assertJsonValidationErrors([
            'delivery_date',
            'invoice_date'
        ]);
});


