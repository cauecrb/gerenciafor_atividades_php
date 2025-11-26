<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectMemberController extends Controller
{
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

    public function destroy(Project $project, User $user)
    {
        abort_unless($project->user_id === Auth::id(), 403);
        $project->members()->detach($user->id);

        return back()->with('success', __('Membro removido com sucesso.'));
    }
}
