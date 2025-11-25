<script setup>
const props = defineProps({
  variant: { type: String, default: 'info' },
  message: { type: String, required: true },
})

const styles = {
  success: {
    background: '#dcfce7',
    color: '#14532d',
    border: '1px solid #22c55e',
  },
  error: {
    background: '#fee2e2',
    color: '#7f1d1d',
    border: '1px solid #ef4444',
  },
  warning: {
    background: '#fef3c7',
    color: '#78350f',
    border: '1px solid #f59e0b',
  },
  info: {
    background: '#e0f2fe',
    color: '#0c4a6e',
    border: '1px solid #3b82f6',
  },
}
</script>

<template>
  <div :style="{ padding: '.5rem', borderRadius: '.25rem', marginTop: '.5rem', ...styles[props.variant] }">
    {{ props.message }}
  </div>
</template>

