<script setup>
import { router, useForm } from '@inertiajs/vue3'
import DatePicker from '../../Components/DatePicker.vue'
import FilePicker from '../../Components/FilePicker.vue'
import AlertBanner from '../../Components/AlertBanner.vue'
import AppLayout from '../../Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
  task: { type: Object, required: true },
})

const form = useForm({
  title: props.task.title || '',
  description: props.task.description || '',
  due_at: props.task.due_at || '',
  priority: props.task.priority || 'medium',
  status: props.task.status || 'pending',
  attachment: null,
})

function submit() {
  form.transform((data) => ({ ...data, _method: 'put' }))
  form.post(`/tasks/${props.task.id}`)
}
</script>

<template>
  <div style="max-width: 800px; margin: 0 auto;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
      <h1 style="font-size:1.5rem; font-weight:700;">Editar Tarefa</h1>
      <a :href="route('tasks.index')"><button type="button" class="btn btn-secondary">Voltar</button></a>
    </div>

    <AlertBanner v-if="Object.keys(form.errors).length" variant="error" message="Corrija os campos destacados" />

    <form class="form-card" @submit.prevent="submit">
      <div class="field">
        <label class="label">Título</label>
        <input class="input" v-model="form.title" type="text" />
      </div>

      <div class="field">
        <label class="label">Descrição</label>
        <input class="input" v-model="form.description" type="text" />
      </div>

      <div class="form-row">
        <div class="field">
          <label class="label">Status</label>
          <select class="input" v-model="form.status">
            <option value="pending">Pendente</option>
            <option value="in_progress">Em andamento</option>
            <option value="completed">Concluída</option>
          </select>
        </div>
        <div class="field">
          <label class="label">Prioridade</label>
          <select class="input" v-model="form.priority">
            <option value="low">Baixa</option>
            <option value="medium">Média</option>
            <option value="high">Alta</option>
          </select>
        </div>
        <div class="field">
          <label class="label">Vencimento</label>
          <DatePicker v-model="form.due_at" />
        </div>
      </div>

      <div class="field">
        <label class="label">Substituir Anexo (PDF)</label>
        <FilePicker v-model="form.attachment" accept="application/pdf" />
      </div>

      <div class="actions">
        <a :href="route('tasks.index')"><button type="button" class="btn btn-secondary">Cancelar</button></a>
        <button type="submit" class="btn btn-primary" :disabled="form.processing">Salvar</button>
      </div>
    </form>
  </div>
  </template>
