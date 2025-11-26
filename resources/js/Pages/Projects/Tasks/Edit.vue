<script setup>
import { useForm } from '@inertiajs/vue3'
import AlertBanner from '../../../Components/AlertBanner.vue'
import DatePicker from '../../../Components/DatePicker.vue'
import AppLayout from '../../../Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
  project: { type: Object, required: true },
  task: { type: Object, required: true },
  assignees: { type: Array, default: () => [] },
  currentAssignees: { type: Array, default: () => [] },
})

const form = useForm({
  title: props.task.title || '',
  description: props.task.description || '',
  status: props.task.status || 'pending',
  due_at: props.task.due_at || '',
  assignees: [...props.currentAssignees],
})

function submit() {
  form.post(route('projects.tasks.update', { project: props.project.id, task: props.task.id }), {
    method: 'put',
  })
}
</script>

<template>
  <div style="max-width: 800px; margin: 0 auto;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
      <h1 style="font-size:1.5rem; font-weight:700;">Editar Tarefa · {{ props.project.title }}</h1>
      <a :href="route('projects.tasks.index', props.project.id)"><button type="button" class="btn btn-secondary">Voltar</button></a>
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
            <option value="completed">Concluída</option>
          </select>
        </div>
        <div class="field">
          <label class="label">Vencimento</label>
          <DatePicker v-model="form.due_at" />
        </div>
      </div>

      <div class="field">
        <label class="label">Atribuir</label>
        <div style="display:flex; flex-wrap:wrap; gap:.5rem;">
          <label v-for="u in props.assignees" :key="u.id" style="display:flex; align-items:center; gap:.25rem;">
            <input type="checkbox" :value="u.id" v-model="form.assignees" />
            <span>{{ u.name }}</span>
          </label>
        </div>
      </div>

      <div class="actions">
        <a :href="route('projects.tasks.index', props.project.id)"><button type="button" class="btn btn-secondary">Cancelar</button></a>
        <button type="submit" class="btn btn-primary" :disabled="form.processing">Salvar</button>
      </div>
    </form>
  </div>
</template>

