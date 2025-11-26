<script setup>
import { router } from '@inertiajs/vue3'
import AlertBanner from '../../Components/AlertBanner.vue'
import AppLayout from '../../Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
  projects: { type: Array, default: () => [] },
})

function remove(id) {
  if (confirm('Confirmar exclusão?')) router.delete(route('projects.destroy', id))
}

function formatDate(d) {
  if (!d) return '-'
  let s = String(d)
  if (s.includes('T')) s = s.split('T')[0]
  const parts = s.split('-')
  if (parts.length !== 3) return d
  return `${parts[2].padStart(2,'0')}/${parts[1].padStart(2,'0')}/${parts[0]}`
}
</script>

<template>
  <div style="max-width: 1000px; margin: 0 auto;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
      <h1 style="font-size:1.5rem; font-weight:700;">Projetos</h1>
      <a :href="route('projects.create')"><button type="button" class="btn btn-primary">Novo Projeto</button></a>
    </div>

    <AlertBanner v-if="$page.props.flash?.success" variant="success" :message="$page.props.flash.success" />
    <AlertBanner v-if="$page.props.flash?.error" variant="error" :message="$page.props.flash.error" />

    <div class="form-card">
      <table style="width:100%; border-collapse: collapse;">
        <thead>
          <tr>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Título</th>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Início</th>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Previsão</th>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Anexo</th>
            <th style="text-align:left; background:#f9fafb; border-bottom:1px solid #e5e7eb; padding:.75rem; font-weight:600; color:#374151;">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="p in props.projects" :key="p.id" style="border-bottom:1px solid #f3f4f6;">
            <td style="padding:.75rem; font-weight:600; color:#111827;">{{ p.title }}</td>
            <td style="padding:.75rem; color:#374151;">{{ formatDate(p.start_date) }}</td>
          <td style="padding:.75rem; color:#374151;">{{ formatDate(p.due_date) }}</td>
          <td style="padding:.75rem;">
            <a v-if="p.attachment_path" :href="'/storage/'+p.attachment_path" target="_blank" class="btn btn-outline">Ver anexo</a>
            <span v-else style="color:#6b7280;">-</span>
          </td>
          <td style="padding:.75rem;">
            <div class="actions" style="justify-content:flex-start; margin-top:0;">
              <a :href="route('projects.tasks.index', p.id)"><button type="button" class="btn btn-primary">Tarefas</button></a>
              <a :href="route('projects.edit', p.id)"><button type="button" class="btn btn-secondary">Editar</button></a>
              <button type="button" class="btn btn-outline" @click="remove(p.id)">Excluir</button>
            </div>
          </td>
        </tr>
          <tr v-if="!props.projects.length">
            <td colspan="5" style="padding:1rem; text-align:center; color:#6b7280;">
              Nenhum projeto cadastrado
              <div class="actions" style="justify-content:center;">
                <a :href="route('projects.create')"><button type="button" class="btn btn-primary">Criar Projeto</button></a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
