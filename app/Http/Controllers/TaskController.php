<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function index() {
        $projects = Project::all();
        $tasks = Task::orderBy('priority')->get();
        return view('tasks.index', compact('tasks', 'projects'));
    }


    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'project_id' => 'required|exists:projects,id',
        ]);

        $maxPriority = Task::where('project_id', $request->project_id)->max('priority') ?? 0;

        Task::create([
            'name' => $request->name,
            'priority' => $maxPriority + 1,
            'project_id' => $request->project_id
        ]);

        return back();
    }

    public function update(Request $request, Task $task) {
        $request->validate(['name' => 'required']);
        $task->update(['name' => $request->name]);
        return back();
    }

    public function destroy(Task $task) {
        $task->delete();
        return back();
    }

    public function reorder(Request $request) {
        foreach ($request->tasks as $index => $id) {
            Task::where('id', $id)->update(['priority' => $index + 1]);
        }
        return response()->json(['status' => 'ok']);
    }
}
