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
        $request->validate(Project::validationRules());
        
        $project = new Project;

        $project->storeOrUpdate($request);
    }

    public function update(Request $request, $id)
    {
        $request->validate(Project::validationRules());
        
        $project = Project::find($id);

        $project->storeOrUpdate($request);
    }
}
