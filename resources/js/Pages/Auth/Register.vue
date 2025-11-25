<script setup>
import { useForm } from '@inertiajs/vue3'
import AlertBanner from '../../Components/AlertBanner.vue'

const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  _token: csrf,
})

function submit() {
  form.post(route('register'), {
    preserveScroll: true,
  })
}
</script>

<template>
  <div style="max-width: 400px; margin: 40px auto; font-family: sans-serif;">
    <h1>Criar conta</h1>

    <form @submit.prevent="submit">
      <AlertBanner v-if="Object.keys(form.errors).length" variant="error" message="Corrija os campos destacados" />
      <label>Nome</label>
      <input v-model="form.name" type="text" />
      <div v-if="form.errors.name" style="color:red">{{ form.errors.name }}</div>

      <label>Email</label>
      <input v-model="form.email" type="email" />
      <div v-if="form.errors.email" style="color:red">{{ form.errors.email }}</div>

      <label>Senha</label>
      <input v-model="form.password" type="password" />
      <div v-if="form.errors.password" style="color:red">{{ form.errors.password }}</div>

      <label>Confirme a senha</label>
      <input v-model="form.password_confirmation" type="password" />

      <button type="submit" :disabled="form.processing">Cadastrar</button>
    </form>

    <p style="margin-top:1rem">
      Já tem conta? <a :href="route('login')">Entrar</a>
    </p>
  </div>
  
</template>
