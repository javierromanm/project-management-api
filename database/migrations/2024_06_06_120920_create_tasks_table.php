<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained();
            $table->foreignId('developer_id')->constrained();
            $table->foreignId('status_task_id')->constrained();
            $table->foreignId('status_invoice_id')->constrained();
            $table->foreignId('status_payment_id')->constrained();
            $table->string('description');
            $table->integer('price_client');
            $table->integer('price_developer');
            $table->dateTime('delivery_date_client');
            $table->dateTime('delivery_date_developer');
            $table->integer('invoice_number_developer');
            $table->dateTime('invoice_date_developer');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
