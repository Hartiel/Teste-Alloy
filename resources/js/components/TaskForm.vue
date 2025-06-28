<script setup>
import { reactive, watch } from 'vue';
import { storeToRefs } from 'pinia';
import { useTaskStore } from '../stores/taskStore';

const taskStore = useTaskStore();
const { editingTask } = storeToRefs(taskStore);
const emit = defineEmits(['close']);

const form = reactive({
  nome: '',
  descricao: '',
  data_limite: '',
});

watch(editingTask, (novaTarefa) => {
  if (novaTarefa) {
    form.nome = novaTarefa.nome;
    form.descricao = novaTarefa.descricao || '';
    form.data_limite = novaTarefa.data_limite
      ? novaTarefa.data_limite.split('T')[0]
      : '';
  } else {
    form.nome = '';
    form.descricao = '';
    form.data_limite = '';
  }
}, { immediate: true });

function handleSubmit() {
  if (!form.nome) {
    alert('O título é obrigatório.');
    return;
  }
  if (editingTask.value) {
    taskStore.updateTask({ ...form });
  } else {
    taskStore.createTask({ ...form });
  }
}
</script>

<template>
  <div class="form-fields w-form">
    <form @submit.prevent="handleSubmit" id="task-form" class="form">
      <div class="block-fields-form">
        <div class="input-wrap no-margin-bottom">
          <input v-model="form.nome" type="text" class="input w-input" id="task-nome" required>
          <label for="task-nome" class="field-label">Título</label>
        </div>
        <div class="input-wrap no-margin-bottom">
          <input v-model="form.descricao" type="text" class="input w-input" id="task-descricao">
          <label for="task-descricao" class="field-label">Detalhes (Opcional)</label>
        </div>
        <div class="input-wrap no-margin-bottom">
          <input v-model="form.data_limite" type="date" class="input w-input" id="task-data">
          <label for="task-data" class="field-label">Data Limite (Opcional)</label>
        </div>
      </div>
    </form>
  </div>
  <div class="bottom-modal">
      <div class="flex-block-horizontal-right-align">
        <div @click="emit('close')" class="button outlined rounded" style="cursor: pointer;">
          <div>Fechar</div>
        </div>
        <button type="submit" form="task-form" class="button rounded" style="cursor: pointer;">
          <div>Salvar Tarefa</div>
        </button>
      </div>
    </div>
</template>

<style>
/* Estilos para o label do input de data */
input[type="date"]+label {
  z-index: 3;
}
input[type="date"]:not(:placeholder-shown)+label,
input[type="date"]:focus+label {
  top: -10px;
  font-size: 14px;
  color: #000000;
}
.bottom-modal {
  padding-top: 2rem;
}
</style>