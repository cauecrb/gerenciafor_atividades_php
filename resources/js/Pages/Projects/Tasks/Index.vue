<script setup>
import { useForm } from '@inertiajs/vue3'
import AlertBanner from '../../../Components/AlertBanner.vue'
import DatePicker from '../../../Components/DatePicker.vue'
import AppLayout from '../../../Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
  project: { type: Object, required: true },
  tasks: { type: Array, default: () => [] },
  assignable: { type: Array, default: () => [] },
})

const form = useForm({
  title: '',
  description: '',
  due_at: '',
  assignees: [],
})

function submit() {
  form.post(route('projects.tasks.store', props.project.id))
}

function remove(taskId) {
  window.Inertia.delete(route('projects.tasks.destroy', { project: props.project.id, task: taskId }))
}

function formatDate(dt) {
  if (!dt) return '-'
  const d = new Date(dt)
  const dd = String(d.getDate()).padStart(2, '0')
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const yyyy = d.getFullYear()
  return `${dd}/${mm}/${yyyy}`
}
</script>

<template>
  <div style="max-width: 1000px; margin: 0 auto;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
      <h1 style="font-size:1.5rem; font-weight:700;">Tarefas · {{ props.project.title }}</h1>
      <a :href="route('projects.index')"><button type="button" class="btn btn-secondary">Voltar</button></a>
    </div>

    <AlertBanner v-if="$page.props.flash?.success" variant="success" :message="$page.props.flash.success" />
    <AlertBanner v-if="$page.props.flash?.error" variant="error" :message="$page.props.flash.error" />

    <div class="form-card" style="margin-bottom:1rem;">
      <h2 style="font-weight:700; margin-bottom:.5rem;">Nova Tarefa</h2>
      <form @submit.prevent="submit">
        <div class="field">
          <label class="label">Título</label>
          <input class="input" v-model="form.title" type="text" />
          <div v-if="form.errors.title" style="color:#ef4444">{{ form.errors.title }}</div>
        </div>
        <div class="field">
          <label class="label">Descrição</label>
          <input class="input" v-model="form.description" type="text" />
        </div>
        <div class="form-row">
          <div class="field">
            <label class="label">Vencimento</label>
            <DatePicker v-model="form.due_at" />
          </div>
          <div class="field">
            <label class="label">Atribuir</label>
            <div style="display:flex; flex-wrap:wrap; gap:.5rem;">
              <label v-for="u in props.assignable" :key="u.id" style="display:flex; align-items:center; gap:.25rem;">
                <input type="checkbox" :value="u.id" v-model="form.assignees" />
                <span>{{ u.name }}</span>
              </label>
            </div>
          </div>
        </div>
        <div class="actions">
          <button type="submit" class="btn btn-primary" :disabled="form.processing">Adicionar</button>
        </div>
      </form>
    </div>

    <div class="form-card">
      <table style="width:100%; border-collapse:collapse;">
        <thead>
          <tr>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Título</th>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Status</th>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Vencimento</th>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="t in props.tasks" :key="t.id" style="border-bottom:1px solid #f3f4f6;">
            <td style="padding:.75rem; font-weight:600; color:#111827;">{{ t.title }}</td>
            <td style="padding:.75rem; color:#374151;">{{ t.status }}</td>
            <td style="padding:.75rem; color:#374151;">{{ formatDate(t.due_at) }}</td>
            <td style="padding:.75rem;">
              <div class="actions" style="justify-content:flex-start; margin-top:0;">
                <a :href="route('projects.tasks.edit', { project: props.project.id, task: t.id })"><button type="button" class="btn btn-secondary">Editar</button></a>
                <button type="button" class="btn btn-outline" @click="remove(t.id)">Excluir</button>
              </div>
            </td>
          </tr>
          <tr v-if="!props.tasks.length">
            <td colspan="4" style="padding:1rem; text-align:center; color:#6b7280;">Nenhuma tarefa</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
