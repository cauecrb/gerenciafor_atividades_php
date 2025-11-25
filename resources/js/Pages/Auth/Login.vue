<script setup>
import { useForm } from '@inertiajs/vue3'

const csrf = document.querySelector('meta[name="csrf-token"])?.content || ''
const form = useForm({
  email: '',
  password: '',
  remember: false,
  _token: csrf,
})

function submit() {
  form.post(route('login'))
}
</script>

<template>
  <div style="max-width: 400px; margin: 40px auto; font-family: sans-serif;">
    <h1>Entrar</h1>

    <form @submit.prevent="submit">
      <label>Email</label>
      <input v-model="form.email" type="email" />
      <div v-if="form.errors.email" style="color:red">{{ form.errors.email }}</div>

      <label>Senha</label>
      <input v-model="form.password" type="password" />
      <div v-if="form.errors.password" style="color:red">{{ form.errors.password }}</div>

      <label style="display:flex; gap:.5rem; align-items:center;">
        <input v-model="form.remember" type="checkbox" /> Lembrar-me
      </label>

      <button type="submit" :disabled="form.processing">Entrar</button>
    </form>

    <p style="margin-top:1rem">
      Não tem conta? <a :href="route('register')">Cadastre-se</a>
    </p>
  </div>
  
</template>
