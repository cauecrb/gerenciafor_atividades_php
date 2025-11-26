<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import AlertBanner from '../../Components/AlertBanner.vue'

const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''
const form = useForm({
  email: '',
  password: '',
  remember: false,
  _token: csrf,
})

const generalError = ref('')

function submit() {
  generalError.value = ''
  form.post(route('login'), {
    preserveScroll: true,
    preserveState: true,
    onError: (errors) => {
      generalError.value = errors?.email || 'Credenciais incorretas'
    },
  })
}
</script>

<template>
  <div style="min-height:100vh; display:flex; align-items:center; justify-content:center; background:#f9fafb;">
    <div class="form-card" style="width:100%; max-width:420px;">
      <div style="text-align:center; margin-bottom:1rem;">
        <div style="font-size:1.5rem; font-weight:700;">Gerenciador</div>
        <div style="color:#6b7280;">Acesse sua conta</div>
      </div>

      <form @submit.prevent="submit">
        <AlertBanner v-if="generalError" variant="error" :message="generalError" />

        <div class="field">
          <label class="label">Email</label>
          <input class="input" v-model="form.email" type="email" placeholder="seu@email.com" />
          <div v-if="form.errors.email" style="color:#ef4444">{{ form.errors.email }}</div>
        </div>

        <div class="field">
          <label class="label">Senha</label>
          <input class="input" v-model="form.password" type="password" placeholder="••••••••" />
          <div v-if="form.errors.password" style="color:#ef4444">{{ form.errors.password }}</div>
        </div>

        <div class="field" style="margin-top:.25rem;">
          <label style="display:flex; gap:.5rem; align-items:center; color:#374151;">
            <input v-model="form.remember" type="checkbox" /> Lembrar-me
          </label>
        </div>

        <div class="actions" style="justify-content:stretch;">
          <button type="submit" class="btn btn-primary" style="width:100%;" :disabled="form.processing">Entrar</button>
        </div>
      </form>

      <div style="margin-top:1rem; text-align:center;">
        <span style="color:#6b7280;">Não tem conta?</span>
        <a :href="route('register')"><button type="button" class="btn btn-outline" style="margin-left:.5rem;">Cadastre-se</button></a>
      </div>
    </div>
  </div>
  
</template>
