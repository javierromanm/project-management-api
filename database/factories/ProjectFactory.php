<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Company;
use App\Models\StatusInvoice;
use App\Models\StatusPayment;
use App\Models\StatusProject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory()->create(),
            'company_id' => Company::factory()->create(),
            'status_project_id' => StatusProject::factory()->create(),
            'status_invoice_id' => StatusInvoice::factory()->create(),
            'status_payment_id' => StatusPayment::factory()->create()
        ];
    }
}
