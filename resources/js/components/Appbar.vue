<script setup>
import { useTaskStore } from '../stores/taskStore';

const taskStore = useTaskStore();
</script>

<template>
  <div class="header">
    <h2>Tarefas</h2>
    <div @click="taskStore.openModal()" class="button rounded">
      <div class="icon w-embed"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg></div>
    </div>
  </div>
</template>
