<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
        ]);
    }

    public function create()
    {
        return Inertia::render('Projects/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
        ]);

        $path = null;
        if ($request->file('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
        }

        $project = Project::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'attachment_path' => $path,
        ]);

        return redirect()->route('projects.index')->with('success', __('Projeto criado com sucesso.'));
    }

    public function edit(Project $project)
    {
        abort_unless($project->user_id === Auth::id(), 403);

        return Inertia::render('Projects/Edit', [
            'project' => $project,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        abort_unless($project->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'attachment' => ['nullable', 'file', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:5120'],
        ]);

        if ($request->file('attachment')) {
            if ($project->attachment_path) {
                Storage::disk('public')->delete($project->attachment_path);
            }
            $project->attachment_path = $request->file('attachment')->store('attachments', 'public');
        }

        $project->title = $validated['title'];
        $project->description = $validated['description'] ?? null;
        $project->start_date = $validated['start_date'] ?? null;
        $project->due_date = $validated['due_date'] ?? null;
        $project->save();

        return redirect()->route('projects.index')->with('success', __('Projeto atualizado com sucesso.'));
    }

    public function destroy(Project $project)
    {
        abort_unless($project->user_id === Auth::id(), 403);
        if ($project->attachment_path) {
            Storage::disk('public')->delete($project->attachment_path);
        }
        $project->delete();

        return redirect()->route('projects.index')->with('success', __('Projeto excluído com sucesso.'));
    }
}
