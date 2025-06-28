<script setup>
import { storeToRefs } from 'pinia';
import { useTaskStore } from './stores/taskStore';
import Appbar from '@/components/Appbar.vue';
import Footer from '@/components/Footer.vue';
import TasksContainer from '@/components/TasksContainer.vue';
import TaskModal from '@/components/TaskModal.vue';

const taskStore = useTaskStore();
const { isModalOpen } = storeToRefs(taskStore);
</script>

<template>
  <div class="content-tasks">
    <Appbar />
    <TasksContainer />
    <Footer />
  </div>

  <transition name="fade">
    <TaskModal v-if="isModalOpen" />
  </transition>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
