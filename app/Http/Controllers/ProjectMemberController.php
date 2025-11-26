<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectMemberController extends Controller
{
    /**
     * Adiciona um membro ao projeto por email, evitando duplicidade e o próprio proprietário.
     *
     * @param Request $request
     * @param Project $project
     */
    public function store(Request $request, Project $project)
    {
        abort_unless($project->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($user->id === $project->user_id) {
            return back()->with('error', __('Você já é o proprietário do projeto.'));
        }

        $project->members()->syncWithoutDetaching([$user->id]);

        return back()->with('success', __('Membro adicionado com sucesso.'));
    }

    /**
     * Remove um membro do projeto.
     *
     * @param Project $project
     * @param User $user
     */
    public function destroy(Project $project, User $user)
    {
        abort_unless($project->user_id === Auth::id(), 403);
        if ($user->id === $project->user_id) {
            return back()->with('error', __('Não é possível remover o proprietário do projeto.'));
        }
        $project->members()->detach($user->id);
        \App\Models\Task::query()
            ->where('project_id', $project->id)
            ->each(function ($task) use ($user) {
                $task->users()->detach($user->id);
            });

        return back()->with('success', __('Membro removido com sucesso.'));
    }
}
