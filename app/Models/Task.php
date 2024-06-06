<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    public function status_task()
    {
        return $this->belongsTo(StatusTask::class);
    }

    public function status_invoice()
    {
        return $this->belongsTo(StatusInvoice::class);
    }

    public function status_payment()
    {
        return $this->belongsTo(StatusPayment::class);
    }

    public static function getDataForIndex($request)
    {
        $tasks = Task::orderBy('id', 'desc')
            ->paginate(10)
            ->through(function($task){
                return [
                    'id' => $task->id,
                    'project_id' => $task->project_id,
                    'developer_id' => $task->developer_id,
                    'developer_name' => $task->developer->name,
                    'description' => $task->description,
                    'price_client' => $task->price_client,
                    'price_developer' => $task->price_developer,
                    'delivery_date_client' => $task->delivery_date_client,
                    'delivery_date_developer' => $task->delivery_date_developer,
                    'status_task' => $task->status_task->name,
                    'status_invoice' => $task->status_invoice->name,
                    'status_payment' => $task->status_payment->name,
                    'invoice_number_developer' => $task->invoice_number_developer,
                    'invoice_date_developer' => $task->invoice_date_developer
                ];
            });
        
        return ['tasks' => $tasks];
    }
}
