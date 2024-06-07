<?php

namespace Database\Factories;

use App\Models\Developer;
use App\Models\Project;
use App\Models\StatusInvoice;
use App\Models\StatusPayment;
use App\Models\StatusTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'project_id' => Project::factory()->create(),
            'developer_id' => Developer::factory()->create(),
            'status_task_id' => StatusTask::factory()->create(),
            'status_invoice_id' => StatusInvoice::factory()->create(),
            'status_payment_id' => StatusPayment::factory()->create(),
            'description' => fake()->sentence,
            'price_client' => fake()->randomNumber(9, false),
            'price_developer' => fake()->randomNumber(9, false),
            'delivery_date_client' => fake()->date('Y-m-d H:i:s'),
            'delivery_date_developer' => fake()->date('Y-m-d H:i:s'),
            'invoice_number_developer' => fake()->randomNumber(5, false),
            'invoice_date_developer'  => fake()->date('Y-m-d H:i:s')
        ];
    }
}
