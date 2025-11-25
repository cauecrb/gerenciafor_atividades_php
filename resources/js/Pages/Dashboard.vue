<script setup>
</script>

<template>
  <div style="max-width: 800px; margin: 40px auto; font-family: sans-serif;">
    <h1>Dashboard</h1>
    <p>Você está autenticado.</p>
    <form method="post" :action="route('logout')">
      <input type="hidden" name="_token" :value="(document.querySelector('meta[name=csrf-token]')?.content)" />
      <button type="submit">Sair</button>
    </form>
  </div>
</template>

