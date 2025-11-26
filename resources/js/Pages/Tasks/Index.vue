<script setup>
import { router, useForm } from '@inertiajs/vue3'
import AlertBanner from '../../Components/AlertBanner.vue'
import AppLayout from '../../Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
  tasks: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
  flash: { type: Object, default: () => ({}) },
})

const filterForm = useForm({
  status: props.filters.status || '',
  priority: props.filters.priority || '',
})

function applyFilters() {
  const q = {}
  if (filterForm.status) q.status = filterForm.status
  if (filterForm.priority) q.priority = filterForm.priority
  router.get(route('tasks.index'), q, { preserveState: true })
}

function remove(taskId) {
  router.delete(`/tasks/${taskId}`)
}

function complete(taskId) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''
  router.post(`/tasks/${taskId}/complete`, { _token: csrf })
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
      <h1 style="font-size:1.5rem; font-weight:700;">Minhas Tarefas</h1>
      <a :href="route('tasks.create')"><button type="button" class="btn btn-primary">Nova Tarefa</button></a>
    </div>

    <AlertBanner v-if="props.flash?.success" variant="success" :message="props.flash.success" />
    <AlertBanner v-if="props.flash?.error" variant="error" :message="props.flash.error" />

    <div class="form-card" style="margin-bottom:1rem;">
      <h2 style="font-weight:700; margin-bottom:.5rem;">Filtros</h2>
      <div class="form-row">
        <div class="field">
          <label class="label">Status</label>
          <select v-model="filterForm.status" class="input">
            <option value="">Todos</option>
            <option value="pending">Pendente</option>
            <option value="in_progress">Em andamento</option>
            <option value="completed">Concluída</option>
          </select>
        </div>
        <div class="field">
          <label class="label">Prioridade</label>
          <select v-model="filterForm.priority" class="input">
            <option value="">Todas</option>
            <option value="low">Baixa</option>
            <option value="medium">Média</option>
            <option value="high">Alta</option>
          </select>
        </div>
      </div>
      <div class="actions">
        <button type="button" class="btn btn-secondary" @click="applyFilters">Aplicar</button>
      </div>
    </div>

    <div class="form-card">
      <table style="width:100%; border-collapse:collapse;">
        <thead>
          <tr>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Título</th>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Status</th>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Prioridade</th>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Vencimento</th>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Anexo</th>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="t in props.tasks" :key="t.id" style="border-bottom:1px solid #f3f4f6;">
            <td style="padding:.75rem; font-weight:600; color:#111827;">{{ t.title }}</td>
            <td style="padding:.75rem; color:#374151;">{{ t.status }}</td>
            <td style="padding:.75rem; color:#374151;">{{ t.priority || '-' }}</td>
            <td style="padding:.75rem; color:#374151;">{{ formatDate(t.due_at) }}</td>
            <td style="padding:.75rem; color:#374151;">
              <a v-if="t.attachment_path" :href="`/storage/${t.attachment_path}`" target="_blank">PDF</a>
              <span v-else>-</span>
            </td>
            <td style="padding:.75rem;">
              <div class="actions" style="justify-content:flex-start; margin-top:0;">
                <a :href="`/tasks/${t.id}/edit`"><button type="button" class="btn btn-secondary">Editar</button></a>
                <button type="button" class="btn btn-outline" @click="remove(t.id)">Excluir</button>
                <button type="button" class="btn btn-primary" @click="complete(t.id)" :disabled="t.status==='completed'">Concluir</button>
              </div>
            </td>
          </tr>
          <tr v-if="!props.tasks.length">
            <td colspan="6" style="padding:1rem; text-align:center; color:#6b7280;">Nenhuma tarefa</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  </template>
