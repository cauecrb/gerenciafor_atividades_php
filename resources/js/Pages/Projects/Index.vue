<script setup>
import { router } from '@inertiajs/vue3'
import AlertBanner from '../../Components/AlertBanner.vue'
import AppLayout from '../../Layouts/AppLayout.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
  projects: { type: Array, default: () => [] },
})

function remove(id) {
  router.delete(route('projects.destroy', id))
}
</script>

<template>
  <div style="max-width: 900px; margin: 40px auto; font-family: sans-serif;">
    <div style="display:flex; align-items:center; justify-content:space-between;">
      <h1>Projetos</h1>
      <a :href="route('projects.create')">
        <button type="button">Novo Projeto</button>
      </a>
    </div>

    <AlertBanner v-if="$page.props.flash?.success" variant="success" :message="$page.props.flash.success" />
    <AlertBanner v-if="$page.props.flash?.error" variant="error" :message="$page.props.flash.error" />

    <table style="width:100%; border-collapse: collapse; margin-top:1rem;">
      <thead>
        <tr>
          <th style="text-align:left; border-bottom:1px solid #e5e7eb; padding:.5rem;">Título</th>
          <th style="text-align:left; border-bottom:1px solid #e5e7eb; padding:.5rem;">Início</th>
          <th style="text-align:left; border-bottom:1px solid #e5e7eb; padding:.5rem;">Previsão</th>
          <th style="text-align:left; border-bottom:1px solid #e5e7eb; padding:.5rem;">Anexo</th>
          <th style="text-align:left; border-bottom:1px solid #e5e7eb; padding:.5rem;">Ações</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in props.projects" :key="p.id">
          <td style="padding:.5rem;">{{ p.title }}</td>
          <td style="padding:.5rem;">{{ p.start_date || '-' }}</td>
          <td style="padding:.5rem;">{{ p.due_date || '-' }}</td>
          <td style="padding:.5rem;">
            <a v-if="p.attachment_path" :href="'/storage/'+p.attachment_path" target="_blank">Ver anexo</a>
            <span v-else>-</span>
          </td>
          <td style="padding:.5rem; display:flex; gap:.5rem;">
            <a :href="route('projects.edit', p.id)"><button type="button">Editar</button></a>
            <button type="button" @click="remove(p.id)">Excluir</button>
          </td>
        </tr>
        <tr v-if="!props.projects.length">
          <td colspan="5" style="padding:.75rem; text-align:center; color:#6b7280;">Nenhum projeto cadastrado</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
