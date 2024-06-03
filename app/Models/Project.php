<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function status_project()
    {
        return $this->belongsTo(StatusProject::class);
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
        $projects = Project::orderBy('id', 'desc')
            ->paginate(10)
            ->through(function($project) {
                return [
                    'id' => $project->id,
                    'client_name' => $project->client->name,
                    'company-name' => $project->company->name,
                    'price' => $project->price,
                    'delivery_date' => $project->delivery_date,
                    'status_project' => $project->status_project->name,
                    'status_invoice' => $project->status_invoice->name,
                    'status_payment' => $project->status_payment->name,
                    'invoice_number' => $project->invoice_number,
                    'invoice_date' => $project->invoice_date
                ];
            });

        return ['projects' => $projects];
    }

    public function storeOrUpdate($request)
    {
        $this->client_id = $request->client_id;
        $this->company_id = $request->company_id;
        $this->status_project_id = $request->status_project_id;
        $this->status_invoice_id = $request->status_invoice_id;
        $this->status_payment_id = $request->status_payment_id;
        $this->price = $request->price;
        $this->delivery_date = $request->delivery_date;
        $this->invoice_number = $request->invoice_number;
        $this->invoice_date = $request->invoice_date;
        $this->save();
    }
}
