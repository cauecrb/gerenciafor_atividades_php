<script setup>
import { useForm } from '@inertiajs/vue3'
import AlertBanner from '../../Components/AlertBanner.vue'
import AppLayout from '../../Layouts/AppLayout.vue'
import FilePicker from '../../Components/FilePicker.vue'
import DatePicker from '../../Components/DatePicker.vue'
defineOptions({ layout: AppLayout })

const form = useForm({
  title: '',
  description: '',
  start_date: '',
  due_date: '',
  attachment: null,
})

function submit() {
  form.post(route('projects.store'), {
    forceFormData: true,
  })
}
</script>

<template>
  <div style="max-width: 800px; margin: 40px auto;">
    <h1 style="font-size:1.5rem; font-weight:700; margin-bottom:1rem;">Novo Projeto</h1>
    <AlertBanner v-if="Object.keys(form.errors).length" variant="error" message="Corrija os campos destacados" />
    <form class="form-card" @submit.prevent="submit">
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
          <label class="label">Data de início</label>
          <DatePicker v-model="form.start_date" />
        </div>
        <div class="field">
          <label class="label">Previsão de conclusão</label>
          <DatePicker v-model="form.due_date" />
        </div>
      </div>

      <div class="field">
        <label class="label">Anexo (PDF/Imagem)</label>
        <FilePicker v-model="form.attachment" accept="application/pdf,image/*" label="Selecionar arquivo" />
        <div v-if="form.errors.attachment" style="color:#ef4444">{{ form.errors.attachment }}</div>
      </div>

      <div class="actions">
        <a :href="route('projects.index')"><button type="button" class="btn btn-secondary">Cancelar</button></a>
        <button type="submit" class="btn btn-primary" :disabled="form.processing">Salvar</button>
      </div>
    </form>
  </div>
</template>
