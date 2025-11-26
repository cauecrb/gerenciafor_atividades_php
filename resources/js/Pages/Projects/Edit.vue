<script setup>
import { useForm, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import AlertBanner from '../../Components/AlertBanner.vue'
import AppLayout from '../../Layouts/AppLayout.vue'
import FilePicker from '../../Components/FilePicker.vue'
import DatePicker from '../../Components/DatePicker.vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
  project: { type: Object, required: true },
  members: { type: Array, default: () => [] },
  allUsers: { type: Array, default: () => [] },
})

const form = useForm({
  title: props.project.title || '',
  description: props.project.description || '',
  start_date: props.project.start_date || '',
  due_date: props.project.due_date || '',
  attachment: null,
  members: (props.members || []).map(m => m.id),
})

function submit() {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''
  const url = route('projects.update', { project: props.project.id })
  form.transform((data) => ({ ...data, _method: 'put', _token: csrf }))
  form.post(url, { forceFormData: true })
}

const showUsersMenu = ref(false)
function addMemberId(id) {
  const intId = Number(id)
  if (!form.members.includes(intId)) {
    form.members = [...form.members, intId]
  }
  showUsersMenu.value = false
}

function removeMember(userId) {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''
  const url = route('projects.members.destroy', { project: props.project.id, user: userId })
  router.post(url, { _method: 'delete', _token: csrf }, {
    onSuccess: () => {
      form.members = form.members.filter(id => id !== userId)
      router.reload({ only: ['members'] })
    },
  })
}
</script>

<template>
  <div style="max-width: 800px; margin: 40px auto;">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
      <h1 style="font-size:1.5rem; font-weight:700;">Editar Projeto</h1>
      <!-- Ação para acessar/criar tarefas do projeto -->
      <a :href="route('projects.tasks.index', props.project.id)"><button type="button" class="btn btn-primary">Tarefas</button></a>
    </div>
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
        <label class="label">Atualizar anexo (PDF/Imagem)</label>
        <FilePicker v-model="form.attachment" accept="application/pdf,image/*" label="Selecionar arquivo" />
        <div v-if="form.errors.attachment" style="color:#ef4444">{{ form.errors.attachment }}</div>
      </div>

      <div class="actions">
        <a :href="route('projects.index')"><button type="button" class="btn btn-secondary">Cancelar</button></a>
        <button type="submit" class="btn btn-primary" :disabled="form.processing">Salvar</button>
      </div>
    </form>

    <div class="form-card" style="margin-top:1rem;">
      <h2 style="font-weight:700; margin-bottom:.5rem;">Membros do Projeto</h2>
      <div class="field">
        <label class="label">Adicionar membros</label>
        <div style="position:relative;">
          <button type="button" class="btn btn-secondary" @click="showUsersMenu = !showUsersMenu">Selecionar usuários</button>
          <div v-if="showUsersMenu" style="position:absolute; z-index:50; background:#ffffff; border:1px solid #e5e7eb; border-radius:.375rem; box-shadow:0 2px 8px rgba(0,0,0,.08); margin-top:.25rem; width:100%; max-height:220px; overflow:auto;">
            <div v-for="u in props.allUsers" :key="u.id" @click.prevent.stop="addMemberId(u.id)" style="padding:.5rem .75rem; cursor:pointer;">
              {{ u.name }} <span style="color:#6b7280; font-size:12px;">({{ u.email }})</span>
            </div>
          </div>
        </div>
        <div style="display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.5rem;">
          <span v-for="id in form.members" :key="id" class="pill">{{ (props.allUsers.find(u => u.id === id) || {}).name || id }}</span>
          <span v-if="!form.members.length" style="color:#6b7280;">Nenhum membro selecionado</span>
        </div>
      </div>

      <h3 style="font-weight:700; margin:.75rem 0 .5rem;">Participantes atuais</h3>
      <ul>
        <li v-for="m in props.members" :key="m.id" style="display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid #f3f4f6;">
          <div>
            <div style="font-weight:600;">{{ m.name }}</div>
            <div style="color:#6b7280; font-size:13px;">{{ m.email }}</div>
          </div>
          <button v-if="m.id !== props.project.user_id" type="button" class="btn btn-outline" @click="removeMember(m.id)">Remover</button>
        </li>
        <li v-if="!props.members.length" style="color:#6b7280;">Nenhum membro adicionado</li>
      </ul>
    </div>
  </div>
</template>
