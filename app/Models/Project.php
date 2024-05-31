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
}
