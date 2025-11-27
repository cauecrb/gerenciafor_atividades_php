<script setup>
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()
const appName = 'Gerenciador de Atividades'
const userName = page?.props?.auth?.user?.name || page?.props?.user?.name || ''

function logout() {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''
  router.post(route('logout'), { _token: csrf }, {
    onSuccess: () => {
      window.location.href = route('login')
    },
  })
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
      <header style="display:flex; align-items:center; justify-content:space-between; padding:.75rem 1.25rem; border-bottom:1px solid #e5e7eb; background:#f9fafb;">
        <div style="font-weight:700;">{{ appName }}</div>
        <div style="display:flex; align-items:center; gap:.5rem;">
          <span style="color:#374151;">{{ userName || 'Usuário' }}</span>
          <button type="button" @click="logout" class="btn btn-outline">Sair</button>
        </div>
      </header>
      <div style="padding: 1.25rem 1.5rem;">
        <slot />
      </div>
    </main>
  </div>
</template>
