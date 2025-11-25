<script setup>
import { router } from '@inertiajs/vue3'
import AlertBanner from '../Components/AlertBanner.vue'
const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''
const props = defineProps({
  pending: { type: Array, default: () => [] },
  overdue: { type: Array, default: () => [] },
  completed: { type: Array, default: () => [] },
})

function formatDate(dt) {
  if (!dt) return '-'
  const d = new Date(dt)
  return d.toLocaleString()
}

function logout() {
  router.post(route('logout'), { _token: csrf })
}
</script>

<template>
  <div style="max-width: 900px; margin: 40px auto; font-family: sans-serif;">
    <div style="display:flex; align-items:center; justify-content:space-between;">
      <h1>Tarefas</h1>
      <button type="button" @click="logout">Sair</button>
    </div>

    <AlertBanner v-if="props.flash?.success" variant="success" :message="props.flash.success" />
    <AlertBanner v-if="props.flash?.error" variant="error" :message="props.flash.error" />

    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-top: 1.5rem;">
      <section>
        <h2>Pendentes</h2>
        <ul>
          <li v-for="t in props.pending" :key="t.id" style="padding:.5rem; border:1px solid #e5e7eb; border-radius:.25rem; margin-bottom:.5rem;">
            <strong>{{ t.title }}</strong>
            <div>{{ t.description }}</div>
            <small>Vence: {{ formatDate(t.due_at) }}</small>
          </li>
          <li v-if="!props.pending.length">Nenhuma tarefa pendente</li>
        </ul>
      </section>

      <section>
        <h2>Atrasadas</h2>
        <ul>
          <li v-for="t in props.overdue" :key="t.id" style="padding:.5rem; border:1px solid #fca5a5; background:#fff1f2; border-radius:.25rem; margin-bottom:.5rem;">
            <strong>{{ t.title }}</strong>
            <div>{{ t.description }}</div>
            <small>Venceu: {{ formatDate(t.due_at) }}</small>
          </li>
          <li v-if="!props.overdue.length">Nenhuma tarefa atrasada</li>
        </ul>
      </section>

      <section>
        <h2>Concluídas</h2>
        <ul>
          <li v-for="t in props.completed" :key="t.id" style="padding:.5rem; border:1px solid #d1fae5; background:#ecfdf5; border-radius:.25rem; margin-bottom:.5rem;">
            <strong>{{ t.title }}</strong>
            <div>{{ t.description }}</div>
            <small>Concluída: {{ formatDate(t.completed_at) }}</small>
          </li>
          <li v-if="!props.completed.length">Nenhuma tarefa concluída</li>
        </ul>
      </section>
    </div>
  </div>
</template>
