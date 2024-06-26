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

    public static function validationRules()
    {
        return [
            'description' => 'required|max:255',
            'project_id' => 'required|exists:projects,id',
            'status_task_id' => 'required|exists:status_tasks,id',
            'status_invoice_id' => 'required|exists:status_invoices,id',
            'status_payment_id' => 'required|exists:status_payments,id',
            'price_client' => 'integer',
            'price_developer' => 'integer',
            'invoice_number_developer' => 'integer',
            'delivery_date_client' => 'date_format:Y-m-d H:i:s',
            'delivery_date_developer' => 'date_format:Y-m-d H:i:s',
            'invoice_date_developer' => 'date_format:Y-m-d H:i:s'
        ];
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

    public function storeOrUpdate($request)
    {
        $this->project_id = $request->project_id;
        $this->developer_id = $request->developer_id;
        $this->status_task_id = $request->status_task_id;
        $this->status_invoice_id = $request->status_invoice_id;
        $this->status_payment_id = $request->status_payment_id;
        $this->description = $request->description;
        $this->price_client = $request->price_client;
        $this->price_developer = $request->price_developer;
        $this->delivery_date_client = $request->delivery_date_client;
        $this->delivery_date_developer = $request->delivery_date_developer;
        $this->invoice_number_developer = $request->invoice_number_developer;
        $this->invoice_date_developer = $request->invoice_date_developer;
        $this->save();
    }
}
