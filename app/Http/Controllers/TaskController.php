<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function store_project(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $project = new Project();
        $project->name = $request['name'];
        $project->save();

        return back()->with('success', 'Added Successfully');
    }

    public function index(Request $request)
    {
        $query = Task::query()->with('project')->orderBy('priority');

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $tasks = $query->paginate();

        $projects = Project::all();
        return view('tasks.index', compact('tasks', 'projects'));
    }


    public function store(Request $request)
    {
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

        return back()->with('success', 'Added Successfully');
    }

    public function update(Request $request, Task $task)
    {
        $request->validate(['name' => 'required'  , 'project_id' => 'required|exists:projects,id',]);
        $task->update([
            'name' => $request->name,
            'project_id' => $request->project_id,
        ]);
        return back();
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return back();
    }

    public function reorder(Request $request)
    {
        foreach ($request->order as $item) {
            \App\Models\Task::query()->where('id', $item['id'])->update(['priority' => $item['priority']]);
        }
        return response()->json(['status' => 'ok']);
    }
}
