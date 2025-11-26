<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TaskController extends Controller
{
    /**
     * Lista tarefas do usuário com filtros de status e prioridade.
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $status = $request->string('status')->toString();
        $priority = $request->string('priority')->toString();

        $q = Task::query()->where('user_id', $userId);
        if ($status) {
            $q->where('status', $status);
        }
        if ($priority) {
            $q->where('priority', $priority);
        }

        $tasks = $q->orderByDesc('created_at')->get();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'filters' => [
                'status' => $status,
                'priority' => $priority,
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    /**
     * Renderiza o formulário de criação de tarefa.
     */
    public function create()
    {
        return Inertia::render('Tasks/Create');
    }

    /**
     * Valida e cria uma tarefa, opcionalmente salva anexo em PDF.
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'attachment' => ['nullable', 'file', 'extensions:pdf', 'max:5120'],
        ]);

        $path = null;
        if ($request->file('attachment')) {
            $file = $request->file('attachment');
            $filename = Str::uuid().'.pdf';
            $destination = public_path('storage/task_attachments');
            File::ensureDirectoryExists($destination);
            $file->move($destination, $filename);
            $path = 'task_attachments/'.$filename;
        }

        Task::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_at' => $validated['due_at'] ?? null,
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
            'attachment_path' => $path,
        ]);

        return redirect()->route('tasks.index')->with('success', __('Tarefa criada.'));
    }

    /**
     * Exibe o formulário de edição da tarefa.
     *
     * @param Task $task
     */
    public function edit(Task $task)
    {
        abort_unless($task->user_id === Auth::id(), 403);

        return Inertia::render('Tasks/Edit', [
            'task' => $task,
        ]);
    }

    /**
     * Atualiza campos da tarefa e o anexo, com autorização.
     *
     * @param Request $request
     * @param Task $task
     */
    public function update(Request $request, Task $task)
    {
        abort_unless($task->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_at' => ['nullable', 'date'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['required', 'in:pending,in_progress,completed'],
            'attachment' => ['nullable', 'file', 'extensions:pdf', 'max:5120'],
        ]);

        $path = $task->attachment_path;
        if ($request->file('attachment')) {
            if ($path) {
                $existing = public_path('storage/'.$path);
                if (is_file($existing)) {
                    @unlink($existing);
                }
            }
            $file = $request->file('attachment');
            $filename = Str::uuid().'.pdf';
            $destination = public_path('storage/task_attachments');
            File::ensureDirectoryExists($destination);
            $file->move($destination, $filename);
            $path = 'task_attachments/'.$filename;
        }

        $task->title = $validated['title'];
        $task->description = $validated['description'] ?? null;
        $task->due_at = $validated['due_at'] ?? null;
        $task->priority = $validated['priority'];
        $task->status = $validated['status'];
        $task->completed_at = $task->status === 'completed' ? now() : null;
        $task->attachment_path = $path;
        $task->save();

        return redirect()->route('tasks.index')->with('success', __('Tarefa atualizada.'));
    }

    /**
     * Remove a tarefa e seu anexo, com autorização.
     *
     * @param Task $task
     */
    public function destroy(Task $task)
    {
        abort_unless($task->user_id === Auth::id(), 403);
        if ($task->attachment_path) {
            $existing = public_path('storage/'.$task->attachment_path);
            if (is_file($existing)) {
                @unlink($existing);
            }
        }
        $task->delete();

        return back()->with('success', __('Tarefa excluída.'));
    }

    /**
     * Marca a tarefa como concluída e define data de conclusão.
     *
     * @param Task $task
     */
    public function complete(Task $task)
    {
        abort_unless($task->user_id === Auth::id(), 403);
        $task->status = 'completed';
        $task->completed_at = now();
        $task->save();

        return back()->with('success', __('Tarefa marcada como concluída.'));
    }

    /**
     * Atualiza o status da tarefa e ajusta conclusão quando aplicável.
     *
     * @param Request $request
     * @param Task $task
     */
    public function status(Request $request, Task $task)
    {
        abort_unless($task->user_id === Auth::id(), 403);
        $validated = $request->validate([
            'status' => ['required', 'in:pending,in_progress,completed'],
        ]);
        $task->status = $validated['status'];
        $task->completed_at = $task->status === 'completed' ? now() : null;
        $task->save();

        return back()->with('success', __('Status da tarefa atualizado.'));
    }
}
