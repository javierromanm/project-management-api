<?php

namespace Database\Factories;

use App\Models\Developer;
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
            'developer_id' => Developer::factory()->create(),
            'status_task_id' => StatusTask::factory()->create(),
            'status_invoice_id' => StatusInvoice::factory()->create(),
            'status_payment_id' => StatusPayment::factory()->create()
        ];
    }
}
