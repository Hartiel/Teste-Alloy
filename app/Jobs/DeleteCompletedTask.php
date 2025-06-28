<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteCompletedTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $taskId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $taskId)
    {
        $this->taskId = $taskId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Encontra a tarefa pelo ID, incluindo as que sofreram soft-delete
        $task = Task::withTrashed()->find($this->taskId);

        // Se a tarefa for encontrada, exclui permanentemente
        if ($task) {
            $task->forceDelete();
        }
    }
}