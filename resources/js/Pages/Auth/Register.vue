<script setup>
import { useForm } from '@inertiajs/vue3'
import AlertBanner from '../../Components/AlertBanner.vue'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

function submit() {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''
  form.transform((data) => ({ ...data, _token: csrf }))
  form.post(route('register'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <div style="min-height:100vh; display:flex; align-items:center; justify-content:center; background:#f9fafb; font-family: sans-serif;">
    <div class="form-card" style="width:100%; max-width:460px;">
      <div style="text-align:center; margin-bottom:1rem;">
        <div style="font-size:1.5rem; font-weight:700;">Gerenciador</div>
        <div style="color:#6b7280;">Crie sua conta</div>
      </div>

      <form @submit.prevent="submit">
        <AlertBanner v-if="Object.keys(form.errors).length" variant="error" message="Corrija os campos destacados" />

        <div class="field">
          <label class="label">Nome</label>
          <input class="input" v-model="form.name" type="text" placeholder="Seu nome" />
          <div v-if="form.errors.name" style="color:#ef4444">{{ form.errors.name }}</div>
        </div>

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

        <div class="field">
          <label class="label">Confirme a senha</label>
          <input class="input" v-model="form.password_confirmation" type="password" placeholder="••••••••" />
        </div>

        <div class="actions" style="justify-content:stretch;">
          <button type="submit" class="btn btn-primary" style="width:100%;" :disabled="form.processing">Cadastrar</button>
        </div>
      </form>

      <div style="margin-top:1rem; text-align:center;">
        <span style="color:#6b7280;">Já tem conta?</span>
        <a :href="route('login')"><button type="button" class="btn btn-outline" style="margin-left:.5rem;">Entrar</button></a>
      </div>
    </div>
  </div>
</template>
