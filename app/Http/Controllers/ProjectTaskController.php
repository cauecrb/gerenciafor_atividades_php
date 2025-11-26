<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectTaskController extends Controller
{
    protected function canAccess(Project $project): bool
    {
        $userId = Auth::id();
        if ($project->user_id === $userId) {
            return true;
        }

        return $project->members()->where('users.id', $userId)->exists();
    }

    public function index(Project $project)
    {
        abort_unless($this->canAccess($project), 403);
        $tasks = Task::query()
            ->where('project_id', $project->id)
            ->orderByDesc('created_at')
            ->get();

        $assignable = $project->members()->select('users.id', 'users.name', 'users.email')->get();
        $assignable->push(User::select('id', 'name', 'email')->find($project->user_id));

        return Inertia::render('Projects/Tasks/Index', [
            'project' => $project->only(['id', 'title']),
            'tasks' => $tasks,
            'assignable' => $assignable,
        ]);
    }

    public function store(Request $request, Project $project)
    {
        abort_unless($this->canAccess($project), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date'],
            'assignees' => ['array'],
            'assignees.*' => ['integer', 'exists:users,id'],
        ]);

        $task = Task::create([
            'project_id' => $project->id,
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
            'due_at' => $validated['due_at'] ?? null,
        ]);

        if (! empty($validated['assignees'])) {
            $task->users()->sync($validated['assignees']);
        }

        return redirect()->route('projects.tasks.index', $project)->with('success', __('Tarefa criada.'));
    }

    public function edit(Project $project, Task $task)
    {
        abort_unless($this->canAccess($project) && $task->project_id === $project->id, 403);
        $assignable = $project->members()->select('users.id', 'users.name', 'users.email')->get();
        $assignable->push(User::select('id', 'name', 'email')->find($project->user_id));

        return Inertia::render('Projects/Tasks/Edit', [
            'project' => $project->only(['id', 'title']),
            'task' => $task,
            'assignees' => $assignable,
            'currentAssignees' => $task->users()->pluck('users.id'),
        ]);
    }

    public function update(Request $request, Project $project, Task $task)
    {
        abort_unless($this->canAccess($project) && $task->project_id === $project->id, 403);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:pending,completed'],
            'due_at' => ['nullable', 'date'],
            'assignees' => ['array'],
            'assignees.*' => ['integer', 'exists:users,id'],
        ]);

        $task->title = $validated['title'];
        $task->description = $validated['description'] ?? null;
        $task->status = $validated['status'];
        $task->due_at = $validated['due_at'] ?? null;
        $task->completed_at = $task->status === 'completed' ? now() : null;
        $task->save();

        $task->users()->sync($validated['assignees'] ?? []);

        return redirect()->route('projects.tasks.index', $project)->with('success', __('Tarefa atualizada.'));
    }

    public function destroy(Project $project, Task $task)
    {
        abort_unless($this->canAccess($project) && $task->project_id === $project->id, 403);
        $task->delete();

        return back()->with('success', __('Tarefa excluída.'));
    }
}
