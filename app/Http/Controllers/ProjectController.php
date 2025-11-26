<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File as FileFacade;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Lista projetos do usuário autenticado e renderiza a página de índice.
     */
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

    /**
     * Renderiza o formulário de criação de projeto.
     */
    public function create()
    {
        return Inertia::render('Projects/Create', [
            'allUsers' => \App\Models\User::query()->select('id', 'name', 'email')->orderBy('name')->get(),
            'ownerId' => \Illuminate\Support\Facades\Auth::id(),
        ]);
    }

    /**
     * Valida e cria um novo projeto, opcionalmente salva anexo, e redireciona.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'members' => ['array'],
            'members.*' => ['integer', 'exists:users,id'],
        ]);

        $path = null;
        if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
            $file = $request->file('attachment');
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = Str::uuid().'.'.$ext;
            $destination = public_path('storage/attachments');
            FileFacade::ensureDirectoryExists($destination);
            $file->move($destination, $filename);
            $path = 'attachments/'.$filename;
        }

        $project = Project::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'attachment_path' => $path,
        ]);

        $members = collect($validated['members'] ?? [])
            ->push(Auth::id())
            ->unique()
            ->values()
            ->all();
        $project->members()->sync($members);

        return redirect()->route('projects.index')->with('success', __('Projeto criado com sucesso.'));
    }

    /**
     * Exibe o formulário de edição de projeto com lista de membros.
     *
     * @param Project $project
     */
    public function edit(Project $project)
    {
        abort_unless($project->user_id === Auth::id(), 403);

        return Inertia::render('Projects/Edit', [
            'project' => $project,
            'members' => $project->members()->select('users.id', 'users.name', 'users.email')->get(),
            'allUsers' => \App\Models\User::query()->select('id', 'name', 'email')->orderBy('name')->get(),
        ]);
    }

    /**
     * Atualiza dados do projeto e anexo, com validação e autorização.
     *
     * @param Request $request
     * @param Project $project
     */
    public function update(Request $request, Project $project)
    {
        abort_unless($project->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
            'members' => ['array'],
            'members.*' => ['integer', 'exists:users,id'],
        ]);

        if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
            if ($project->attachment_path) {
                FileFacade::delete(public_path('storage/'.$project->attachment_path));
            }
            $file = $request->file('attachment');
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = Str::uuid().'.'.$ext;
            $destination = public_path('storage/attachments');
            FileFacade::ensureDirectoryExists($destination);
            $file->move($destination, $filename);
            $project->attachment_path = 'attachments/'.$filename;
        }

        $project->title = $validated['title'];
        $project->description = $validated['description'] ?? null;
        $project->start_date = $validated['start_date'] ?? null;
        $project->due_date = $validated['due_date'] ?? null;
        $project->save();

        $previous = $project->members()->pluck('users.id')->all();
        $new = $validated['members'] ?? [];
        $project->members()->sync($new);
        $removed = array_diff($previous, $new);
        if (! empty($removed)) {
            \App\Models\Task::query()
                ->where('project_id', $project->id)
                ->each(function ($task) use ($removed) {
                    $task->users()->detach($removed);
                });
        }

        return redirect()->route('projects.index')->with('success', __('Projeto atualizado com sucesso.'));
    }

    /**
     * Remove projeto e anexo, com autorização.
     *
     * @param Project $project
     */
    public function destroy(Project $project)
    {
        abort_unless($project->user_id === Auth::id(), 403);
        if ($project->attachment_path) {
            FileFacade::delete(public_path('storage/'.$project->attachment_path));
        }
        $project->delete();

        return redirect()->route('projects.index')->with('success', __('Projeto excluído com sucesso.'));
    }
}
