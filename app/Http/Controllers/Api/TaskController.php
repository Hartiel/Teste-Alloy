<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Jobs\DeleteCompletedTask;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{

    protected string $cacheTag = 'tasks';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // guarda no cache e retorna
        return Cache::remember('tasks.all', 3600, function () {
            return Task::latest()->get();
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'data_limite' => 'nullable|date',
        ]);

        $task = Task::create($validated);

        Cache::forget('tasks.all'); // Limpa o cache

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $cacheKey = "tasks.{$task->id}";
        // guarda no cache e retorna
        return Cache::remember($cacheKey, 3600, function () use ($task) {
            return $task;
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'finalizado' => 'sometimes|boolean',
            'data_limite' => 'nullable|date',
        ]);

        $task->update($validated);

        Cache::forget('tasks.all');         // Limpa o cache
        Cache::forget("tasks.{$task->id}"); // Limpa a task especifica no cache

        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        Cache::forget('tasks.all');             // Limpa o cache
        Cache::forget("tasks.{$task->id}");     // Limpa a task especifica no cache
        return response()->noContent();
    }

    /**
     * Toggle the 'finalizado' status of the task.
     */
    public function toggle(Task $task)
    {
        $task->finalizado = !$task->finalizado;
        $task->save();

        // Se a tarefa foi finalizada, aciona o delete
        if ($task->finalizado) {
            DeleteCompletedTask::dispatch($task->id)->delay(now()->addMinutes(10));
        }

        Cache::forget('tasks.all');         // Limpa o cache
        Cache::forget("tasks.{$task->id}"); // Limpa a task especifica no cache

        return response()->json($task);
    }
}
