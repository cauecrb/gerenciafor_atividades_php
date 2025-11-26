<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = now();
        $projectIds = \App\Models\Project::query()
            ->where('user_id', $user->id)
            ->orWhereHas('members', function ($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->pluck('id');

        $pending = Task::query()
            ->where(function ($q) use ($user, $projectIds) {
                $q->where('user_id', $user->id)
                    ->orWhereIn('project_id', $projectIds);
            })
            ->where('status', 'pending')
            ->where(function ($q) use ($now) {
                $q->whereNull('due_at')->orWhere('due_at', '>=', $now);
            })
            ->orderBy('due_at')
            ->get();
        $overdue = Task::query()
            ->where(function ($q) use ($user, $projectIds) {
                $q->where('user_id', $user->id)
                    ->orWhereIn('project_id', $projectIds);
            })
            ->where('status', 'pending')
            ->whereNotNull('due_at')
            ->where('due_at', '<', $now)
            ->orderBy('due_at')
            ->get();
        $completed = Task::query()
            ->where(function ($q) use ($user, $projectIds) {
                $q->where('user_id', $user->id)
                    ->orWhereIn('project_id', $projectIds);
            })
            ->where('status', 'completed')
            ->orderByDesc('completed_at')
            ->get();

        $inProgress = Task::query()
            ->where(function ($q) use ($user, $projectIds) {
                $q->where('user_id', $user->id)
                    ->orWhereIn('project_id', $projectIds);
            })
            ->where('status', 'in_progress')
            ->orderBy('due_at')
            ->get();

        return Inertia::render('Dashboard', [
            'pending' => $pending,
            'overdue' => $overdue,
            'completed' => $completed,
            'inProgress' => $inProgress,
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }
}
