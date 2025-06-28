import { defineStore } from 'pinia';
import taskService from '../taskService';

export const useTaskStore = defineStore('tasks', {
    state: () => ({
        tasks: [],
        loading: false,
        isModalOpen: false,
        editingTask: null,
    }),
    getters: {
        sortedTasks(state) {
            return [...state.tasks].sort((a, b) => {
                if (a.finalizado !== b.finalizado) {
                    return a.finalizado - b.finalizado;
                }
                if (a.data_limite === null) return 1;
                if (b.data_limite === null) return -1;
                return new Date(a.data_limite) - new Date(b.data_limite);
            });
        },
    },
    actions: {
        openModal(task = null) {
            console.log(task);
            this.editingTask = task;
            this.isModalOpen = true;
        },
        closeModal() {
            this.isModalOpen = false;
            this.editingTask = null;
        },
        async fetchTasks() {
                this.loading = true;
            try {
                const response = await taskService.fetchTasks();
                this.tasks = response.data;
            } catch (error) {
                console.error('Erro ao buscar tarefas:', error);
            } finally {
                this.loading = false;
            }
        },
        async createTask(taskData) {
            try {
                await taskService.createTask(taskData);
                await this.fetchTasks();
                this.closeModal();
            } catch (error) {
                console.error("Erro ao criar tarefa:", error);
            }
        },
        async updateTask(taskData) {
            if (!this.editingTask) return;
            try {
                await taskService.updateTask(this.editingTask.id, taskData);
                await this.fetchTasks();
                this.closeModal();
            } catch (error) {
                console.error("Erro ao atualizar tarefa:", error);
            }
        },
        async toggleTask(taskId) {
            console.log(taskId)
            const task = this.tasks.find(t => t.id === taskId);
            if (task) {
                task.finalizado = !task.finalizado;
                try {
                    await taskService.updateTask(taskId, { finalizado: task.finalizado });
                } catch (error) {
                    console.error('Erro ao alternar tarefa:', error);
                    task.finalizado = !task.finalizado;
                }
            }
        },
        async deleteTask(taskId) {
            if (!confirm('Tem certeza que deseja excluir esta tarefa?')) {
                return;
            }
            const taskIndex = this.tasks.findIndex(t => t.id === taskId);
            if (taskIndex !== -1) {
                this.tasks.splice(taskIndex, 1);
                try {
                    await taskService.deleteTask(taskId);
                } catch (error) {
                    console.error('Erro ao deletar tarefa:', error);
                    this.fetchTasks();
                }
            }
        },
        async toggleTask(taskId) {
            const task = this.tasks.find(t => t.id === taskId);
            if (task) {
                task.finalizado = !task.finalizado;
                try {
                    await taskService.toggleTask(taskId);
                } catch (error) {
                    console.error('Erro ao alternar tarefa:', error);
                    task.finalizado = !task.finalizado;
                }
            }
        },
    }
});