<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import AlertBanner from '../../Components/AlertBanner.vue'
import AppLayout from '../../Layouts/AppLayout.vue'
import FilePicker from '../../Components/FilePicker.vue'
import DatePicker from '../../Components/DatePicker.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
  allUsers: { type: Array, default: () => [] },
  ownerId: { type: Number, default: null },
})

const form = useForm({
  title: '',
  description: '',
  start_date: '',
  due_date: '',
  attachment: null,
  members: props.ownerId ? [props.ownerId] : [],
})

const showUsersMenu = ref(false)
function addMember(id) {
  const intId = Number(id)
  if (!form.members.includes(intId)) {
    form.members = [...form.members, intId]
  }
  showUsersMenu.value = false
}
function removeMember(id) {
  const intId = Number(id)
  form.members = form.members.filter(m => m !== intId)
}

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

      <div class="field">
        <label class="label">Membros (opcional)</label>
        <div style="position:relative;">
          <button type="button" class="btn btn-secondary" @click="showUsersMenu = !showUsersMenu">Selecionar usuários</button>
          <div v-if="showUsersMenu" style="position:absolute; z-index:50; background:#ffffff; border:1px solid #e5e7eb; border-radius:.375rem; box-shadow:0 2px 8px rgba(0,0,0,.08); margin-top:.25rem; width:100%; max-height:220px; overflow:auto;">
            <div v-for="u in props.allUsers" :key="u.id" @click.prevent.stop="addMember(u.id)" style="padding:.5rem .75rem; cursor:pointer;">
              {{ u.name }} <span style="color:#6b7280; font-size:12px;">({{ u.email }})</span>
            </div>
          </div>
        </div>
        <div style="display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.5rem;">
          <span v-for="id in form.members" :key="id" class="pill">
            {{ (props.allUsers.find(u => u.id === id) || {}).name || 'Você' }}
            <button type="button" class="btn btn-outline" @click="removeMember(id)" style="padding:.125rem .375rem; font-size:12px;">Remover</button>
          </span>
          <span v-if="!form.members.length" style="color:#6b7280;">Nenhum membro selecionado</span>
        </div>
      </div>

      <div class="actions">
        <a :href="route('projects.index')"><button type="button" class="btn btn-secondary">Cancelar</button></a>
        <button type="submit" class="btn btn-primary" :disabled="form.processing">Salvar</button>
      </div>
    </form>
  </div>
</template>
