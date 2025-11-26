<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::resource('projects', \App\Http\Controllers\ProjectController::class)->except(['show']);
    Route::post('projects/{project}/members', [\App\Http\Controllers\ProjectMemberController::class, 'store'])->name('projects.members.store');
    Route::delete('projects/{project}/members/{user}', [\App\Http\Controllers\ProjectMemberController::class, 'destroy'])->name('projects.members.destroy');

    Route::get('projects/{project}/tasks', [\App\Http\Controllers\ProjectTaskController::class, 'index'])->name('projects.tasks.index');
    Route::post('projects/{project}/tasks', [\App\Http\Controllers\ProjectTaskController::class, 'store'])->name('projects.tasks.store');
    Route::get('projects/{project}/tasks/{task}/edit', [\App\Http\Controllers\ProjectTaskController::class, 'edit'])->name('projects.tasks.edit');
    Route::put('projects/{project}/tasks/{task}', [\App\Http\Controllers\ProjectTaskController::class, 'update'])->name('projects.tasks.update');
    Route::delete('projects/{project}/tasks/{task}', [\App\Http\Controllers\ProjectTaskController::class, 'destroy'])->name('projects.tasks.destroy');

    Route::get('tasks', [\App\Http\Controllers\TaskController::class, 'index'])->name('tasks.index');
    Route::get('tasks/create', [\App\Http\Controllers\TaskController::class, 'create'])->name('tasks.create');
    Route::post('tasks', [\App\Http\Controllers\TaskController::class, 'store'])->name('tasks.store');
    Route::get('tasks/{task}/edit', [\App\Http\Controllers\TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('tasks/{task}', [\App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update');
    Route::delete('tasks/{task}', [\App\Http\Controllers\TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::post('tasks/{task}/complete', [\App\Http\Controllers\TaskController::class, 'complete'])->name('tasks.complete');
});
