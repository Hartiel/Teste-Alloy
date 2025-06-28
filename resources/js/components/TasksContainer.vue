<script setup>
import { onMounted } from 'vue';
import { storeToRefs } from 'pinia';
import { useTaskStore } from '../stores/taskStore';
import TaskItem from './TaskItem.vue';

const taskStore = useTaskStore();
const { tasks, loading } = storeToRefs(taskStore);

onMounted(() => {
    taskStore.fetchTasks();
});
</script>

<template>
    <div class="tasks">
        <div class="form-fields no-space-top w-form">
            <form class="form">
                <div class="block-tasks">
                    <!-- Loading -->
                    <div v-if="loading" style="text-align: center; padding: 2rem;">
                        <p>Carregando tarefas...</p>
                    </div>
                
                    <!-- Tarefas vazias -->
                    <div v-else-if="!loading && tasks.length === 0" style="text-align: center; padding: 2rem;">
                        <p>Nenhuma tarefa por aqui.</p>
                    </div>
                
                    <!-- Lista de tarefas -->
                    <TaskItem
                        v-else
                        v-for="task in taskStore.sortedTasks"
                        :key="task.id"
                        :task="task"
                    />
                </div>
            </form>
        </div>
    </div>
</template>