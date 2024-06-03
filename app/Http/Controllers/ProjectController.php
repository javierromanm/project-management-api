<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Project::getDataForIndex($request));
    }

    public function store(Request $request)
    {
        $project = new Project;

        $project->storeOrUpdate($request);
    }
}
