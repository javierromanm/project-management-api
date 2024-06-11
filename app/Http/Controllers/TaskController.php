<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Task::getDataForIndex($request));
    }

    public function store(Request $request)
    {
        $request->validate(Task::validationRules());
        
        $task = new Task;

        $task->storeOrUpdate($request);
    }

    public function update(Request $request, $id)
    {
        $request->validate(Task::validationRules());
        
        $task = Task::find($id);

        $task->storeOrUpdate($request);
    }
}
