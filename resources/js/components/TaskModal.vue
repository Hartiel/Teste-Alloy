<script setup>
import { computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useTaskStore } from '../stores/taskStore';
import TaskForm from './TaskForm.vue';

const taskStore = useTaskStore();
const { editingTask } = storeToRefs(taskStore);

const modalTitle = computed(() => editingTask.value ? 'Editar Tarefa' : 'Nova Tarefa');
</script>

<template>
  <div class="modal-task" style="display:flex;">
    <div class="container-modal regular">
      <div class="top-modal">
        <h3>{{ modalTitle }}</h3>
        <div @click="taskStore.closeModal()" class="close-modal" style="cursor: pointer;">
          <div class="icon w-embed">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M17 7L7 17M7 7L17 17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </div>
        </div>
      </div>

      <div class="content-modal">
        <TaskForm @close="taskStore.closeModal()" />
      </div>

    </div>
  </div>
</template>