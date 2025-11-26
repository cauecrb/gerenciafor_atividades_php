<script setup>
import AlertBanner from '../Components/AlertBanner.vue'
import AppLayout from '../Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })
import { router } from '@inertiajs/vue3'
const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''
const props = defineProps({
  pending: { type: Array, default: () => [] },
  overdue: { type: Array, default: () => [] },
  completed: { type: Array, default: () => [] },
  inProgress: { type: Array, default: () => [] },
  flash: { type: Object, default: () => ({}) },
})

function formatDate(dt) {
  if (!dt) return '-'
  const d = new Date(dt)
  const dd = String(d.getDate()).padStart(2, '0')
  const mm = String(d.getMonth() + 1).padStart(2, '0')
  const yyyy = d.getFullYear()
  return `${dd}/${mm}/${yyyy}`
}

function onDragStart(e, task) {
  e.dataTransfer.setData('text/plain', JSON.stringify({ id: task.id, project_id: task.project_id }))
}
function onDropStatus(e, targetStatus) {
  e.preventDefault()
  const data = e.dataTransfer.getData('text/plain')
  if (!data) return
  const obj = JSON.parse(data)
  if (!obj.id) return
  if (obj.project_id) {
    const url = `/projects/${obj.project_id}/tasks/${obj.id}/status`
    router.post(url, { status: targetStatus, _method: 'put', _token: csrf }, {
      preserveScroll: true,
      onSuccess: () => router.reload({ only: ['pending','inProgress','overdue','completed','flash'] })
    })
    return
  }
  const url = `/tasks/${obj.id}/status`
  router.post(url, { status: targetStatus, _method: 'put', _token: csrf }, {
    preserveScroll: true,
    onSuccess: () => router.reload({ only: ['pending','inProgress','overdue','completed','flash'] })
  })
}
function allowDrop(e) { e.preventDefault() }
function editTask(t) {
  if (t.project_id) router.visit(route('projects.tasks.edit', { project: t.project_id, task: t.id }))
  else router.visit(route('tasks.edit', { task: t.id }))
}
function completeTask(t) {
  if (t.project_id) {
    const url = `/projects/${t.project_id}/tasks/${t.id}/status`
    router.post(url, { status: 'completed', _method: 'put', _token: csrf }, {
      preserveScroll: true,
      onSuccess: () => router.reload({ only: ['pending','inProgress','overdue','completed','flash'] })
    })
  } else {
    const url = `/tasks/${t.id}/complete`
    router.post(url, { _token: csrf }, {
      preserveScroll: true,
      onSuccess: () => router.reload({ only: ['pending','inProgress','overdue','completed','flash'] })
    })
  }
}
function deleteTask(t) { if (!t.project_id) router.delete(route('tasks.destroy', { task: t.id })) }

</script>

<template>
  <div style="max-width: 1200px; margin: 0 auto; font-family: sans-serif;">
    <div style="display:flex; align-items:center; justify-content:space-between;">
      <h1>Tarefas</h1>
    </div>

    <AlertBanner v-if="props.flash?.success" variant="success" :message="props.flash.success" />
    <AlertBanner v-if="props.flash?.error" variant="error" :message="props.flash.error" />

    <div class="kanban" style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1.5rem;">
      <section class="kanban-col">
        <div class="kanban-header">Pendente</div>
        <div class="kanban-drop" @dragover="allowDrop" @drop="(e)=>onDropStatus(e,'pending')">
          <div v-for="t in props.pending" :key="t.id" class="kanban-card" draggable="true" @dragstart="(e)=>onDragStart(e,t)">
          <div class="badges">
            <span class="badge badge-status">{{ t.project_id ? 'Projeto' : 'Pessoal' }}</span>
            <span class="badge" :class="{
              'badge-priority-low': t.priority==='low',
              'badge-priority-medium': t.priority==='medium',
              'badge-priority-high': t.priority==='high'
            }">{{ t.priority || 'medium' }}</span>
          </div>
          <div class="kanban-sub" v-if="t.project && t.project.title"><span class="pill">Projeto: {{ t.project.title }}</span></div>
          <div class="kanban-title">{{ t.title }}</div>
          <div class="kanban-sub">{{ t.description }}</div>
            <div class="kanban-sub" v-if="t.due_at"><span class="pill">Vence: {{ formatDate(t.due_at) }}</span></div>
            <div class="actions" style="justify-content:flex-start; gap:.5rem; margin-top:.5rem;">
              <button class="btn btn-secondary" @click.stop="editTask(t)">Editar</button>
              <button class="btn btn-outline" @click.stop="deleteTask(t)" :disabled="!!t.project_id">Excluir</button>
              <button class="btn btn-primary" @click.stop="completeTask(t)" :disabled="!!t.project_id">Concluir</button>
            </div>
          </div>
          <div v-if="!props.pending.length" style="color:#6b7280;">Nenhuma tarefa pendente</div>
        </div>
      </section>

      <section class="kanban-col">
        <div class="kanban-header">Em andamento</div>
        <div class="kanban-drop" @dragover="allowDrop" @drop="(e)=>onDropStatus(e,'in_progress')">
          <div v-for="t in props.inProgress" :key="t.id" class="kanban-card" draggable="true" @dragstart="(e)=>onDragStart(e,t)">
          <div class="badges">
            <span class="badge badge-status">{{ t.project_id ? 'Projeto' : 'Pessoal' }}</span>
            <span class="badge" :class="{
              'badge-priority-low': t.priority==='low',
              'badge-priority-medium': t.priority==='medium',
              'badge-priority-high': t.priority==='high'
            }">{{ t.priority || 'medium' }}</span>
          </div>
          <div class="kanban-sub" v-if="t.project && t.project.title"><span class="pill">Projeto: {{ t.project.title }}</span></div>
          <div class="kanban-title">{{ t.title }}</div>
          <div class="kanban-sub">{{ t.description }}</div>
            <div class="kanban-sub" v-if="t.due_at"><span class="pill">Vence: {{ formatDate(t.due_at) }}</span></div>
            <div class="actions" style="justify-content:flex-start; gap:.5rem; margin-top:.5rem;">
              <button class="btn btn-secondary" @click.stop="editTask(t)">Editar</button>
              <button class="btn btn-outline" @click.stop="deleteTask(t)" :disabled="!!t.project_id">Excluir</button>
              <button class="btn btn-primary" @click.stop="completeTask(t)" :disabled="!!t.project_id">Concluir</button>
            </div>
          </div>
          <div v-if="!props.inProgress.length" style="color:#6b7280;">Nenhuma tarefa em andamento</div>
        </div>
      </section>

      <section class="kanban-col">
        <div class="kanban-header">Concluída</div>
        <div class="kanban-drop" @dragover="allowDrop" @drop="(e)=>onDropStatus(e,'completed')">
          <div v-for="t in props.completed" :key="t.id" class="kanban-card" draggable="true" @dragstart="(e)=>onDragStart(e,t)">
          <div class="badges">
            <span class="badge badge-status">{{ t.project_id ? 'Projeto' : 'Pessoal' }}</span>
            <span class="badge" :class="{
              'badge-priority-low': t.priority==='low',
              'badge-priority-medium': t.priority==='medium',
              'badge-priority-high': t.priority==='high'
            }">{{ t.priority || 'medium' }}</span>
          </div>
          <div class="kanban-sub" v-if="t.project && t.project.title"><span class="pill">Projeto: {{ t.project.title }}</span></div>
          <div class="kanban-title">{{ t.title }}</div>
          <div class="kanban-sub">{{ t.description }}</div>
            <div class="kanban-sub" v-if="t.completed_at"><span class="pill">Concluída: {{ formatDate(t.completed_at) }}</span></div>
            <div class="actions" style="justify-content:flex-start; gap:.5rem; margin-top:.5rem;">
              <button class="btn btn-secondary" @click.stop="editTask(t)">Ver</button>
            </div>
          </div>
          <div v-if="!props.completed.length" style="color:#6b7280;">Nenhuma tarefa concluída</div>
        </div>
      </section>
    </div>
  </div>
</template>
