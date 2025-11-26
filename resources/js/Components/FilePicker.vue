<script setup>
import { ref } from 'vue'

const props = defineProps({
  modelValue: { type: [File, null], default: null },
  accept: { type: String, default: '' },
  label: { type: String, default: 'Selecionar arquivo' },
})
const emit = defineEmits(['update:modelValue'])

const inputRef = ref(null)
const fileName = ref('')

function openPicker() {
  inputRef.value?.click()
}

function onChange(e) {
  const file = e.target.files?.[0] || null
  fileName.value = file ? file.name : ''
  emit('update:modelValue', file)
}
</script>

<template>
  <div>
    <button class="btn btn-outline" type="button" @click="openPicker">{{ props.label }}</button>
    <span class="file-name">{{ fileName || 'Nenhum arquivo selecionado' }}</span>
    <input ref="inputRef" type="file" :accept="props.accept" @change="onChange" style="display:none" />
  </div>
</template>
