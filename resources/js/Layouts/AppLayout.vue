<script setup>
import { router } from '@inertiajs/vue3'

const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''

function logout() {
  router.post(route('logout'), { _token: csrf })
}
</script>

<template>
  <div style="display:flex; min-height:100vh; font-family: sans-serif;">
    <aside style="width: 220px; background:#111827; color:#f3f4f6; padding:1rem 0;">
      <div style="padding: 0 1rem; font-weight:700;">Gerenciador</div>
      <nav style="margin-top:1rem; display:flex; flex-direction:column; gap:.25rem;">
        <a :href="route('dashboard')" style="color:#f3f4f6; text-decoration:none; padding:.5rem 1rem; display:block;">Dashboard</a>
        <a :href="route('projects.index')" style="color:#f3f4f6; text-decoration:none; padding:.5rem 1rem; display:block;">Gerenciar Projetos</a>
        <a :href="route('projects.create')" style="color:#f3f4f6; text-decoration:none; padding:.5rem 1rem; display:block;">Criar Projeto</a>
        <a :href="route('tasks.index')" style="color:#f3f4f6; text-decoration:none; padding:.5rem 1rem; display:block;">Minhas Tarefas</a>
        <button type="button" @click="logout" style="margin-top:1rem; margin-left:1rem; width: calc(100% - 2rem); background:#ef4444; color:#fff; border:none; border-radius:.25rem; padding:.5rem; cursor:pointer;">Sair</button>
      </nav>
    </aside>
    <main style="flex:1; background:#ffffff; color:#111827;">
      <div style="padding: 1.25rem 1.5rem;">
        <slot />
      </div>
    </main>
  </div>
</template>
