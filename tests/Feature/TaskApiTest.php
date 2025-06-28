<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Task;
use App\Jobs\DeleteCompletedTask;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Cache;

class TaskApiTest extends TestCase
{
    use RefreshDatabase;

    /** Test for list all tasks */
    public function test_it_can_list_all_tasks(): void
    {
        Task::factory(3)->create();

        $response = $this->getJson('/api/tasks');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /** Test for create a task */
    public function test_it_can_create_a_task(): void
    {
        $taskData = [
            'nome' => 'Nova Tarefa',
            'descricao' => 'Descrição da tarefa.',
        ];

        $response = $this->postJson('/api/tasks', $taskData);

        $response->assertStatus(201)
                 ->assertJsonFragment(['nome' => 'Nova Tarefa']);

        $this->assertDatabaseHas('tasks', ['nome' => 'Nova Tarefa']);
    }

    /** Test for show specific task */
    public function test_it_can_show_a_specific_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->getJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $task->id]);
    }

    /** Test for update a task */
    public function test_it_can_update_a_task(): void
    {
        $task = Task::factory()->create();
        $updateData = ['nome' => 'Tarefa Atualizada'];

        $response = $this->putJson('/api/tasks/' . $task->id, $updateData);

        $response->assertStatus(200)
                 ->assertJsonFragment(['nome' => 'Tarefa Atualizada']);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'nome' => 'Tarefa Atualizada']);
    }

    /** Test for delete a task */
    public function test_it_can_soft_delete_a_task(): void
    {
        $task = Task::factory()->create();

        $response = $this->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(204);

        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }

    /** Test for finish a task */
    public function test_it_can_toggle_a_task_status(): void
    {
        // Para a fila pro job não ser executado de verdade e poder verificar se foi apagado pelo softdelete
        Queue::fake();

        $task = Task::factory()->create(['finalizado' => false]);

        $response = $this->patchJson('/api/tasks/' . $task->id . '/toggle');

        $response->assertStatus(200)
                 ->assertJsonFragment(['finalizado' => true]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'finalizado' => true]);

        // Aqui verifica se o job foi pra fila
        Queue::assertPushed(DeleteCompletedTask::class, function ($job) use ($task) {
            return $job->taskId === $task->id;
        });
    }

    /** Test for cache in task list */
    public function test_it_caches_and_invalidates_the_task_list_correctly()
    {
        Cache::flush();

        // Verifica o cache vazio
        $this->assertFalse(Cache::has('tasks.all'));
        $this->getJson('/api/tasks');

        // Verifica se foi criado chave no cache
        $this->assertTrue(Cache::has('tasks.all'));

        // Cria uma tarefa para limpar o cache
        $this->postJson('/api/tasks', [
            'nome' => 'Tarefa para invalidar cache',
        ]);

        // Verifica se a chave foi removida do cache
        $this->assertFalse(Cache::has('tasks.all'));
    }
}
