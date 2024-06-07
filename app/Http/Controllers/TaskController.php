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
        $task = new Task;

        $task->storeOrUpdate($request);
    }
}
